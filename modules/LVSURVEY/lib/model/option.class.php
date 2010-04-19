<?php


class Option {
	protected $id;
	
	protected $choiceId;
	protected $choice;
	
	protected $text;
	
	public function __construct($choiceId)
    {
        $this->choiceId = $choiceId;
        $this->choice = $choice;
        $this->text = $text;
    }
    
    static function __set_state($array)
    {
    	static $properties = array();
    	if(empty($properties))
    	{
    		$properties = array_keys(get_object_vars(new Option(-1)));
    	}
    	
    	$res = new Choice($array['choiceId']);
        foreach ($array as $akey => $aval) {
            if(in_array($akey,$properties))
            {
            	$res -> {$akey} = $aval;
            }
        }
        return $res;
    }
    
    static function load($id)
    {
    	$dbCnx = Claroline::getDatabase();
        $sql = "
        	SELECT
            	       `id` 							AS id,
            	       `choiceId`						AS choiceId,
            	       `text`							AS text
           	FROM 		`".SurveyConstants::$OPTION_TBL."` 
           	WHERE 		`id` = ".(int) $id."; "; 
         
         
        $resultSet = $dbCnx->query($sql);
        $data = $resultSet->fetch();
        return self::__set_state($data);
    }
    
	public function getChoice(){
    	if(empty($this->choice))
    	{
    		$this->loadChoice();
    	}
    	return $this->question;
    }
    private function loadChoice()
    {
        $this->choice = Choice::load($this->choiceId);
    }
	public function setChoice($choice)
    {
    	$this->choice = $choice;
    	$this->choiceId = $choice->id;
    }
	private function validate()
    {
    	$validationErrors = array();
    	if(empty($this->text))
    		$this->validationErrors[] = 'Option text is required';
    	if($this->choiceId <= 0)
    		$this->validationErrors[] = 'Choice not defined';    	
    	
    	return $validationErrors;    	
    }
    public function save()
    {
    	$validationErrors = $this->validate();
    	if(!empty($validationErrors)){
    		throw new Exception(implode('<br/>', $validationErrors));
    	}
    	
    	if($this->id == -1){
    		$this->insertInDB();
    	} else {
    		$this->updateInDB();
    	}   	
    }
    
    
    private function insertInDB()
    {
    	$dbCnx = ClaroLine::getDatabase();
    	
        //Insert new survey in DB
        $sql = "
        		INSERT INTO `".SurveyConstants::$OPTION_TBL."`
                SET 		`text` 				= ".$dbCnx->quote($this->text).",
                			`choiceId`			= ".(int)($this->choiceId)."; ";
                    
			
        // execute the creation query and get id of inserted assignment
        $dbCnx->exec($sql);
        $insertedId = $dbCnx->insertId($sql);                   		
        $this->id = (int) $insertedId;
        
    }
    private function updateInDB()
    {
    	$dbCnx = ClaroLine::getDatabase();
        //update current survey in DB (we cannot change id, courseId or anonimity)
        $sql = "
        	UPDATE 		`".SurveyConstants::$OPTION_TBL."`
            SET 		`text` 				= ".$dbCnx->quote($this->text).",
                		`id` 				= ".(int)$this->id.",
                    	`choiceId`			= ".(int)($this->choiceId)." 
            WHERE 		`id` = ".(int)$this->id ;
            
            $dbCnx->exec($sql);
    }
}