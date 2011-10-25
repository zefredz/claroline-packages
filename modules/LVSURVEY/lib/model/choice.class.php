<?php
From::module('LVSURVEY')->uses(	'util/surveyConstants.class', 
								'model/question.class',
 								'model/option.class',
								'util/functions.class');


class Choice
{
	public $id;
	
	public $text;
	
	protected $questionId;
	protected $question;
	
	protected $optionList;
        	
	public function __construct($questionId)
        {
            $this->id = -1;
            $this->text = '';      
            $this->questionId = $questionId;
            $this->question = NULL;
            $this->optionList = array();
        }
	
	static function __set_state($array)
        {
            static $properties = array();
            if(empty($properties))
            {
                    $properties = array_keys(get_object_vars(new Choice(-1)));
            }

            $res = new Choice($array['questionId']);
            foreach ($array as $akey => $aval) {
                if(in_array($akey,$properties))
                {
                    $res -> {$akey} = $aval;
                }
            }
            return $res;
        }	
    
    //load survey from the db
    static function load($id)
    {
    	$dbCnx = Claroline::getDatabase();
        /*
        * get row of table
        */
        $sql = "
        	SELECT
            	       `id` 							AS id,
            	       `questionId`						AS questionId,
            	       `text`							AS text
           	FROM 		`".SurveyConstants::$QUESTION_TBL."` 
           	WHERE 		`id` = ".(int) $id."; "; 
         
         
        $resultSet = $dbCnx->query($sql);
        $data = $resultSet->fetch();
        return self::__set_state($data);
    }
    
    static function loadSelectedChoicesFromForm($questionLine)
    { 
    	$question = $questionLine->question;
    	if('OPEN' == $question->type)
    	{
    		$choiceText = $_REQUEST['choiceText'.$questionLine->id];
    		$choice = new Choice($questionLine->question->id);
    		$choice->text = $choiceText;
    		return array($choice);
    	}
    	if(!isset($_REQUEST['choiceId'.$questionLine->id])) return array();
    	if('MCSA' == $question->type)
    	{
    		$choiceId = $_REQUEST['choiceId'.$questionLine->id];
    		$choice = new Choice($questionLine->question->id);
    		$choice->id = $choiceId;
    		return array($choice);
    	}
    	$choices = $_REQUEST['choiceId'.$questionLine->id];
    	$res = array();
    	foreach($choices as $choiceId)
    	{
    		$choice = new Choice($questionLine->question->id);
    		$choice->id = $choiceId;
    		$res[] = $choice;
    	}
    	return $res;
    	
    }
    
    static function loadFromForm($choiceNumber,$question,$duplicate = false)
    {
        static $choiceValidator;
        if(is_null($choiceValidator))
        {
            $choiceValidator= new Claro_Validator_CustomNotEmpty();
        }
        $userInput = Claro_UserInput :: getInstance();
        $choiceId = $duplicate?-1:(int)$userInput->getMandatory('questionChId'.$choiceNumber);
        $userInput->setValidator('questionCh'.$choiceNumber, $choiceValidator);    	
        $choiceText = $userInput->getMandatory('questionCh'.$choiceNumber);
        $choice = Choice::__set_state(array('id' => $choiceId, 'text' => $choiceText, 'questionId' => $question->id));
        $choice->setQuestion($question);

        if('ARRAY' == $question->type)
        {
                $choice->optionList = self::loadOptionsFromForm($choiceNumber, $choice);
        }
        return $choice;
    }
    
    static function loadOptionsFromForm($choiceNumber,$choice)
    {    	
    	$userInput = Claro_UserInput::getInstance();
    	try
    	{
	    	$formOptions = (string)$userInput->getMandatory('optionsForChoice'.$choiceNumber);
    	}
    	catch(Claro_Validator_Exception $e)
    	{
    		throw new Claro_Validator_Exception(get_lang('You have forgotten to give options for choice '.$choiceNumber));
    	}
    	$optionsText = explode(';', $formOptions);
    	$res = array();
    	foreach($optionsText as $anOptionText)
    	{
    		$data = array(
    			'choiceId'  => $choice->id, 
    			'choice'	=> $choice, 
    			'text'		=> $anOptionText);
    		$option = Option::__set_state($data);
    		$res[] = $option;
    	}
    	return $res;
    }
    
    
    
    public function getQuestion(){
    	if(empty($this->question))
    	{
    		$this->loadQuestion();
    	}
    	return $this->question;
    }
    private function loadQuestion()
    {
        $this->question = Question::load($this->questionId);
    }
	public function setQuestion($question)
    {
    	$this->question = $question;
    	$this->questionId = $question->id;
    }
    
	public function getOptionList()
    {
    	if(empty($this->optionList))
    	{
    		$this->loadOptionList();
    	}
    	return $this->optionList;
    }
    private function loadOptionList()
    {
    	$dbCnx = Claroline::getDatabase();
    	$sql = "SELECT 	O.`id`				as id, 
    					O.`choiceId`		as choiceId, 
    					O.`text`			as text
                FROM 	`".SurveyConstants::$OPTION_TBL."` as O
                WHERE 	O.`choiceId` = ".(int)$this->id." 
                ORDER BY id ASC; ";
        
    	$resultSet = $dbCnx->query($sql);
        $this->questionList = array();	    
	    foreach( $resultSet as $row )
	    {
	    	$option = Option::__set_state($row);
            $this->optionList[] = $option;
	    }    
    }
    
    public function getOption($optionId)
    {
    	$optionList = $this->getOptionList();
    	foreach($optionList as $option)
    	{
    		if($option->getId() == $optionId)
    			return $option;
    	}
    	return null;
    }
    
	private function validate()
    {
    	$validationErrors = array();
    	if(empty($this->text))
    		$this->validationErrors[] = 'Choice body is required';
    	if(-1 == $this->questionId)
    		$this->validationErrors[] = 'Question not defined';    	
    	
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
    	
   	 	foreach($this->optionList as $option)
    	{
    		$option->save();
    	}
    	
    	$this->deleteObsoleteOptions();
    	
    }
    
    
    private function insertInDB()
    {
    	$dbCnx = ClaroLine::getDatabase();
    	
        //Insert new survey in DB
        $sql = "
        		INSERT INTO `".SurveyConstants::$CHOICE_TBL."`
                SET 		`text` 				= ".$dbCnx->quote($this->text).",
                			`questionId`		= ".(int)($this->questionId)."; ";
                    
			
        // execute the creation query and get id of inserted assignment
        $dbCnx->exec($sql);
        $insertedId = $dbCnx->insertId($sql);                   		
        $this->id = (int) $insertedId;
        
    	foreach($this->optionList as $option)
    	{
    		$option->setChoice($this);
    	}
        
    }
    private function updateInDB()
    {
    	$dbCnx = ClaroLine::getDatabase();
        //update current survey in DB (we cannot change id, courseId or anonimity)
        $sql = "
        	UPDATE 		`".SurveyConstants::$CHOICE_TBL."`
            SET 		`text` 				= ".$dbCnx->quote($this->text).",
                		`id` 				= ".(int)$this->id.",
                    	`questionId`		= ".(int)($this->questionId)." 
            WHERE 		`id` = ".(int)$this->id ;
            
            $dbCnx->exec($sql);
    }
    
	private function deleteObsoleteOptions()
    {
    	$optionList = $this->getOptionList();
    	$validOptionIdList = array_map(array('Functions', 'idOf'),$optionList);    	
    	$sql = "
    			DELETE FROM 	`".SurveyConstants::$OPTION_TBL."`  
        		WHERE			`choiceId` = ".(int)$this->id." "; 
    	if(!empty($validOptionIdList)){		
        		$sql .= " 
        		AND 			`id` NOT IN (".implode(', ',$validOptionIdList).") ;";
    	}    	
    	$dbCnx = Claroline::getDatabase();
    	$dbCnx->exec($sql); 
    }
    
    public function getText()
    {
    	return $this->text;
    }
    
	
	
	
}