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

require_once __DIR__ . '/SurveyConstants.php';
     
//class to manage list of questions
class QuestionList {
    
    //unique id of the course
    protected $courseId;
    
    //list of surveys
    protected $questionList;
    
    //list order
    protected $orderby;
    
    //reverse order
    protected $reverseOrder;
    
    //constructor
    public function __construct($courseId)
    {
        $this->courseId = mysql_real_escape_string($courseId);        
        //default values
        $this->orderby = 'title';
        $this->reverseOrder = false;
        
    }
    
    //load the question list in specified order
    public function load($orderby, $reverse)
    {
        $acceptedValues = array('title', 'id', 'type', 'used');

        if(in_array($orderby, $acceptedValues))
            $this->orderby = $orderby;
            
        $ascdesc = "DESC";
        if(($this->orderby == 'title') || ($this->orderby == 'type'))  
        {
            $ascdesc = "ASC";  
        }
        
        $this->reverseOrder = false;
        if($reverse == true)
        {
            $this->reverseOrder = true;
            if($ascdesc == "DESC")
                $ascdesc = "ASC";
            else
                $ascdesc = "DESC";
        }
        
        /*
                     * get rows of table
                     */
         $sql = "SELECT q.`id`, q.`title`, q.`type`, COUNT(rel.`surveyId`) AS used
                    FROM `".SurveyConstants::$QUESTION_TBL."` AS q 
                    LEFT JOIN `".SurveyConstants::$REL_SURV_QUEST_TBL."` AS rel
                    ON q.`id`= rel.`questionId` 
                    WHERE q.`courseId` = '".$this->courseId."'
                    GROUP BY q.`id`
                    ORDER BY ".$this->orderby." ".$ascdesc;

        $this->questionList = claro_sql_query_fetch_all($sql);
    }
    
    //generate html for the list of questions
    public function render()
    {
        $out = '';
        $out .= '<table class="claroTable emphaseLine" border="0" align="center" cellpadding="2" cellspacing="2" width="100%">' . "\n\n"
        .     '<thead>' . "\n"
        .     '<tr class="headerX">' . "\n"
        .     '<th><a href="question_pool.php?orderby=title'.(($this->orderby=='title') && ($this->reverseOrder==false)?'&amp;reverse=1':'').'" >' 
        .     get_lang('Question title') 
        .	  '</a></th>' . "\n"
        .     '<th><a href="question_pool.php?orderby=type'.(($this->orderby=='type') && ($this->reverseOrder==false)?'&amp;reverse=1':'').'" >' 
        .     get_lang('Type of question')
        . 	  '</a></th>' . "\n"
        .     '<th><a href="question_pool.php?orderby=used'.(($this->orderby=='used') && ($this->reverseOrder==false)?'&amp;reverse=1':'').'" >' 
        .     get_lang('Number of surveys using the question') 
        . 	  '</a></th>' . "\n"
        .	  '<th>' . get_lang('Modify') . '</th>' . "\n"
        .     '<th>' . get_lang('Delete') . '</th>' . "\n";
        
        $colspan = 4;
        
        $out .= '</tr>' . "\n"
        .     '</thead>' . "\n\n"
        .     '<tbody>' . "\n\n";      
        
        if( !empty($this->questionList) )
        {
            $counter = 0;
            //add each line
            foreach( $this->questionList as $aQuestion )
            {
                $counter++;
        
                $out .= '<tr>' . "\n"
                .     '<td>'
                .     '<a href="show_question.php?questionId='.$aQuestion['id'].'" class="item">'
                .     ''//TODO : insert an question icon here
                .     $aQuestion['title']
                .     '</a>'
                .     '</td>' . "\n";
        		
                $out .= '<td>'
                .     $aQuestion['type']
                .     '</td>' . "\n";
                
                $out .= '<td>'
                .     $aQuestion['used']
                .     '</td>' . "\n";
                
                $out .=  '<td align="center">'
                .     '<a href="edit_question.php?questionId='.$aQuestion['id'].'">'
                .     '<img src="' .get_icon_url('edit').'" border="0" alt="'.get_lang('Modify').'" />'
                .     '</a>'
                .     '</td>' . "\n";
    
                $confirmString = get_lang('Are you sure you want to delete this question?');
    
                $out .=  '<td align="center">'
                .     '<a href="question_pool.php?questionId='.$aQuestion['id'].'&amp;cmd=questionDel" onclick="javascript:if(!confirm(\''.clean_str_for_javascript($confirmString).'\')) return false;">'
                .     '<img src="' .get_icon_url('delete').'"" border="0" alt="'.get_lang('Delete').'" />'
                .     '</a>'
                .     '</td>' . "\n";
               
                $out .=  '</tr>' . "\n\n";
            }
        }
        else
        {
            //there is no question to list
            $out .= '<tr>' . "\n"
            .     '<td colspan="'.$colspan.'">' . get_lang('Empty') . '</td>' . "\n"
            .     '</tr>' . "\n\n";
        }
        
        $out .= '</tbody>' . "\n\n"
            .'</table>' . "\n\n";
        
        return $out;
    }

    //show the list of choices when adding an existing question to a survey
    public function renderChooseList($surveyId)
    {
        $out = '';
        
        $out .= '<table class="claroTable emphaseLine" border="0" align="center" cellpadding="2" cellspacing="2" width="100%">' . "\n\n"
        .     '<thead>' . "\n"
        .     '<tr class="headerX">' . "\n"
        .     '<th><a href="add_question.php?fromPool=1&amp;surveyId='.$surveyId.'&amp;orderby=title'.(($this->orderby=='title') && ($this->reverseOrder==false)?'&amp;reverse=1':'').'" >' 
        .     get_lang('Question title') 
        .	  '</a></th>' . "\n"
        .     '<th><a href="add_question.php?fromPool=1&amp;surveyId='.$surveyId.'&amp;orderby=type'.(($this->orderby=='type') && ($this->reverseOrder==false)?'&amp;reverse=1':'').'" >' 
        .     get_lang('Type of question')
        . 	  '</a></th>' . "\n"
        .     '<th><a href="add_question.php?fromPool=1&amp;surveyId='.$surveyId.'&amp;orderby=used'.(($this->orderby=='used') && ($this->reverseOrder==false)?'&amp;reverse=1':'').'" >' 
        .     get_lang('Number of surveys using the question') 
        . 	  '</a></th>' . "\n"
        .     '<th>&nbsp;' . get_lang('') . '</th>' . "\n";
        
        $colspan = 4;
        
        $out .= '</tr>' . "\n"
        .     '</thead>' . "\n\n"
        .     '<tbody>' . "\n\n";      
        
        $counter = 0;
        if( !empty($this->questionList) )
        {
            $sql = "SELECT `questionId` FROM `".SurveyConstants::$REL_SURV_QUEST_TBL."`
                    WHERE `surveyId`='".(int)$surveyId."'";
                    
            $questOfSurv = claro_sql_query_fetch_all($sql);
            
            $counter = 0;
            foreach( $this->questionList as $aQuestion )
            {
                $found = false;
                foreach($questOfSurv as $q)
                {
                    if((int)$aQuestion['id'] == (int)$q['questionId'])
                        $found = true;
                }
                
                if($found == false)
                {
                    $counter++;
                    $out .= '<tr>' . "\n"
                    .     '<td>'
                   
                    .     ''//TODO : insert an question icon here
                    .     $aQuestion['title']
                    .     '</td>' . "\n";
            		
                    $out .= '<td>'
                    .     $aQuestion['type']
                    .     '</td>' . "\n";
                    
                    $out .= '<td>'
                    .     $aQuestion['used']
                    .     '</td>' . "\n";
                    
                    $out .=  '<td align="center">'
                    .     '<a href="add_question.php?surveyId='.$surveyId.'&amp;questionId='.$aQuestion['id'].'">'
                    . get_lang('Choose') //TODO : an icon or leave a text?
                    .     '</a>'
                    .     '</td>' . "\n";
                }
   
               
                $out .=  '</tr>' . "\n\n";
            }
        }
        
        if($counter == 0)
        {
            $out .= '<tr>' . "\n"
            .     '<td colspan="'.$colspan.'">' . get_lang('Empty') . '</td>' . "\n"
            .     '</tr>' . "\n\n";
        }
        
        $out .= '</tbody>' . "\n\n"
            .'</table>' . "\n\n";
        
        
        return $out;
    }
    
}


?>