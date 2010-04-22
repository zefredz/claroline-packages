<?php


class Option {
	protected $id;
	
	protected $choiceId;
	protected $choice;
	
	protected $text;
	
	public function __construct($choiceId)
    {
    	$this->id = -1;
        $this->choiceId = $choiceId;
        $this->choice = null;
        $this->text = '';
    }
    
    static function __set_state($array)
    {
    	static $properties = array();
    	if(empty($properties))
    	{
    		$properties = array_keys(get_object_vars(new Option(-1)));
    	}
    	
    	$res = new Option($array['choiceId']);
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
    
    public static function loadSelectedOptionFromForm($questionLine)
    {
    	$question = $questionLine->question;
    	$choices = $question->getChoiceList();
    	$userInput = Claro_UserInput::getInstance();
    	
    	$res = array();
    	foreach($choices as $aChoice)
    	{
    		$inputName = "choiceId{$questionLine->id}_{$aChoice->id}";
    		$optionId = $userInput->get($inputName, -1);
    		if(-1 == $optionId)
    		{
    			continue;
    		} 
    		$res[] = $aChoice->getOption($optionId);
    	}
    	return $res;    	
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
    	}else{
    		throw new Exception('Cannot update an option');
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
    
    public function getId()
    {
    	return $this->id;
    }
    public function getText()
    {
    	return $this->text;
    }
    public function getChoiceId()
    {
    	return $this->choiceId;
    }

}