 <?php 
  /**
     * This is a tool to create surveys. It's the new version better than older CLSURVEY
     * @copyright (c) Haute Ecole Léonard de Vinci
     * @version     0.1 $Revision$
     * @author      BAUDET Gregory <gregory.baudet@gmail.com>
     * @license     http://www.gnu.org/copyleft/gpl.html
     *              GNU GENERAL PUBLIC LICENSE version 2 or later
     * @package     LVSURVEY
     */
     
class Choices
{
    //unique id of the course
    protected $courseId;
    
    //list of choices
    protected $choiceList;
    
    //questionId
    protected $questionId;
    
    //table containing choices
    protected $tblChoice;
    
    //selected choices
    protected $selectedList;
    
    //if choices must be duplicated
    protected $duplicate;
    
    //constructor
    public function __construct($courseId)
    {
        $this->questionId = -1;
        $this->courseId = mysql_real_escape_string($courseId);
        $tbl = claro_sql_get_tbl(array('survey2_choice'));
        $this->tblChoice = $tbl['survey2_choice']; //TODO : nothardcoded
        $this->selectedList = array();
        $this->duplicate = false;
        $this->choiceList = array();
    }
    
    //set the questionId
    public function setQuestionId($value)
    {
        $this->questionId = (int)$value;
    }
    
    //load choices, questionId must be set
    public function load()
    { 
        if($this->questionId == -1)
            return false;
        $sql = "SELECT
                `id`,
                `text`
        FROM `".$this->tblChoice."`
        WHERE `questionId` = '".$this->questionId."'
        ORDER BY `id` ASC";
        
        $this->choiceList = claro_sql_query_fetch_all($sql);  
                
    }
    
    //save the choices. Used when creating or editing a question
    public function save()
    {
		
        if($this->questionId == -1)
            return false;
        foreach ($this->choiceList as $aChoice )
        {
            if(($aChoice['id']=='-1')||($this->duplicate == true))
            {
                $sql = "INSERT INTO `".$this->tblChoice."`
                        SET `questionId` = '".$this->questionId    ."',
                        	`text` = '".addslashes($aChoice['text'])."'";
                $insertedId = claro_sql_query_insert_id($sql);
            }
            else
            {
                $sql = "UPDATE `".$this->tblChoice."`
                	SET `text` = '".addslashes($aChoice['text'])."'
                	WHERE `id` = ".$aChoice['id'];
                claro_sql_query($sql);
            }
        }
    }
    
    //remove choices of the question
    public function removeChoices()
    {
        $sql = "DELETE FROM `".$this->tblChoice."`
                	WHERE `questionId` = ".(int)$this->questionId;
        claro_sql_query($sql);
    }
    
    //set the list of choices
    public function setChoices($list)
    {
        if(empty($this->choiceList))
        {
            $i = 0;
            foreach ( $list as $aChoice )
            {
                $this->choiceList[$i]['id']='-1';
                $this->choiceList[$i]['text']=$aChoice;
                $i++;
            }
        }
        else
        {
            if(count($this->choiceList)!=count($list))
                return false;
            else
            {
                for ( $i=0;$i<count($this->choiceList);$i++ )
                {
                    $this->choiceList[$i]['text']=$list[$i];
                }   
            }
        }
        return true;
    }
    
    //get the list of choices
    public function GetChoices()
    {
        return $this->choiceList;
    }
    
    //set choices wich are selected
    public function setSelection($list)
    {
        if(is_array($list))
            $this->selectedList = $list;
    }
    
    //ask if a choice is selected
    public function isSelected($id)
    {
                    //echo $this->id;
            //var_dump($this->selectedList);
        foreach($this->selectedList as $item)
        {
            if((int)$item == (int)$id)
            {
                return true;
            }
        }
        
        return false;
    }
    
    //Specified choices must be duplicated on next save
    public function setDuplicate($val)
    {
		if($val == true)
			$this->duplicate = true;
		else
			$this->duplicate = false;
	}
}
?>