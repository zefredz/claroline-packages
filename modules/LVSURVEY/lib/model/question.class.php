<?php
FromKernel::uses(	'utils/input.lib', 
					'utils/validator.lib');
From::module('LVSURVEY')->uses(	'util/surveyConstants.class', 
								'model/choice.class',
								'util/functions.class');


class Question
{
	
	public static $VALID_QUESTION_TYPES = array(	'OPEN', 
													'MCSA', 
													'MCMA', 
													'ARRAY'); 
	
	public $id;
	
	public $text;
	
	public $type;
	
	
	protected $used;
	
	protected $choiceList;
	
	
	public function __construct()
    {
        $this->id = -1;
        $this->text = '';
        $this->type = 'MCSA'; 
        $this->choiceList = array();
        $this->used = 0;
    }
	
	static function __set_state($array)
    {
    	if(empty($array)) return false;
    	static $properties = array();
    	if(empty($properties))
    	{
    		$properties = array_keys(get_object_vars(new Question()));
    	}
    	
    	$res = new Question();
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
            	       Q.`id` 							AS id,
            	       Q.`text`							AS text,
            	       Q.`type`							AS type,
            	       COUNT(DISTINCT S.`id`)			AS used
           	FROM 		`".SurveyConstants::$QUESTION_TBL."` Q
           	LEFT JOIN 	`".SurveyConstants::$SURVEY_LINE_QUESTION_TBL."` AS SLQ
            ON 			Q.`id`= SLQ.`questionId`
            LEFT JOIN 	`".SurveyConstants::$SURVEY_LINE_TBL."` AS SL
            ON 			SLQ.`id`= SL.`id`
            LEFT JOIN 	`".SurveyConstants::$SURVEY_TBL."` AS S
            ON 			SL.`surveyId`= S.`id`              
           	WHERE 		Q.`id`  = ".(int) $id."
           	GROUP BY	Q.`id` ; "; 
         
         
        $resultSet = $dbCnx->query($sql);
        $data = $resultSet->fetch();
        if(empty($data))
			throw new Exception("Invalid Question Id");
        return self::__set_state($data);
    }
    
    static function loadQuestionPool($orderBy, $ascDesc )
    {
    	$acceptedOrderBy = array('text', 'id', 'type', 'used');
        if(!in_array($orderBy, $acceptedOrderBy))
            $orderBy = 'text';
            
        $acceptedAscDesc = array('ASC', 'DESC');
        if(!in_array($ascDesc, $acceptedAscDesc))
            $ascDesc = 'ASC';
        
        $dbCnx = Claroline::getDatabase();
        $sql = "
        	SELECT
            	       Q.`id` 							AS id,
            	       Q.`text`							AS text,
            	       Q.`type`							AS type,
            	       COUNT(DISTINCT S.`id`)			AS used
           	FROM 		`".SurveyConstants::$QUESTION_TBL."` Q
           	LEFT JOIN 	`".SurveyConstants::$SURVEY_LINE_QUESTION_TBL."` AS SLQ
            ON 			Q.`id`= SLQ.`questionId`
            LEFT JOIN 	`".SurveyConstants::$SURVEY_LINE_TBL."` AS SL
            ON 			SLQ.`id`= SL.`id`
            LEFT JOIN 	`".SurveyConstants::$SURVEY_TBL."` AS S
            ON 			SL.`surveyId`= S.`id`
            GROUP BY	Q.`id` 
           	ORDER BY 	".$orderBy." ".$ascDesc." ; ";
        
        $resultSet = $dbCnx->query($sql);
        $res = array();	    
	    foreach( $resultSet as $row )
	    {
	    	$question = self::__set_state($row);
            $res[] = $question;
	    }   
        return $res;
    }
    
    static function loadFromForm()
    {
    	//PARSE INPUT
    	$userInput = Claro_UserInput::getInstance();
    	$questionTypeValidator = new Claro_Validator_AllowedList(self::$VALID_QUESTION_TYPES);
    	$userInput->setValidator('questionType',$questionTypeValidator);
    	$formDuplicate = $userInput->get('questionDuplicate','0');
    	
    	try
    	{
	    	$formId = (int)$userInput->getMandatory('questionId');  
	    	$formText = (string)$userInput->getMandatory('questionText');	    	
    	}
    	catch(Claro_Validator_Exception $e)
    	{
    		throw new Claro_Validator_Exception(get_lang('You have forgotten to fill a mandatory field'));
    	}
    	try
    	{
    		$formType = $userInput->getMandatory('questionType');
    	} 
    	catch (Claro_Validator_Exception $e)
    	{
    		throw new Claro_Validator_Exception(get_lang('Unknown Question Type or Alignment'));
    	}

    	$duplicate = ('1' == $formDuplicate);
    	
    	
    	//UPDATE QUESTION   	
		if($formId == -1 || $duplicate)
		{			
			$question = new Question();
			
		}
		else 
		{
			$question = self::load($formId);	
		}
		
		$question->text = $formText;
		$question->type = $formType;
		
		//HANDLE CHOICE LIST if question type is not OPEN
		if('OPEN' != $question->type)
		{
			$question->choiceList = array();
			for($i = 1; isset($_REQUEST['questionCh'.$i]) && !empty($_REQUEST['questionCh'.$i]); ++$i)
			{
				$choice = Choice::loadFromForm($i, $question, $duplicate);
				$question->choiceList[] = $choice;	
			}
		}
		if($duplicate)
		{
			
			$surveyId = (int)$_REQUEST['surveyId'];
			$survey = Survey::load($surveyId);
			$survey->removeQuestion($formId);
		}
		
		return $question;
    }
    
    public function getChoiceList()
    {
    	if(empty($this->choiceList))
    	{
    		$this->loadChoiceList();
    	}
    	return $this->choiceList;
    }
    private function loadChoiceList()
    {
    	$dbCnx = Claroline::getDatabase();
    	$sql = "SELECT 	C.`id`				as id, 
    					C.`questionId`		as questionId, 
    					C.`text`			as text
                FROM 	`".SurveyConstants::$CHOICE_TBL."` as C
                WHERE 	C.`questionId` = ".(int)$this->id."; ";
        
    	$resultSet = $dbCnx->query($sql);
        $this->questionList = array();	    
	    foreach( $resultSet as $row )
	    {
	    	$choice = Choice::__set_state($row);
            $this->choiceList[] = $choice;
	    }    
    }
    
    public function getChoice($choiceId)
    {
    	$choiceList = $this->getChoiceList();
    	foreach($choiceList as $aChoice)
    	{
    		if($aChoice->id == $choiceId)
    		{
    			return $aChoice;
    		}
    	}
    	return null;
    }
    public function getUsed()
    {
    	return $this->used;
    }
	private function validate()
    {
    	$validationErrors = array();
    	if(empty($this->text))
    		$this->validationErrors[] = 'Question body is required';
    	
    	if(!in_array($this->type, self::$VALID_QUESTION_TYPES))
    		$this->validationErrors[] = 'Unknown question type';    	
    	
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
    	}
    	else
    	{
    		$this->updateInDB();
    	} 

    	$this->deleteObsoleteChoices();
    	
    	if('OPEN' != $this->type)
		{
    		foreach($this->choiceList as $choice)
    		{
    			$choice->save();
    		}
		}
    	
    }
    
    
    private function insertInDB()
    {
    	$dbCnx = ClaroLine::getDatabase();
    	
        //Insert new survey in DB
        $sql = "
        		INSERT INTO `".SurveyConstants::$QUESTION_TBL."`
                SET 		`text` 				= ".$dbCnx->quote($this->text).",
                			`type` 				= ".$dbCnx->quote($this->type)."; ";
                    
			
        // execute the creation query and get id of inserted assignment
        $dbCnx->exec($sql);
        $insertedId = $dbCnx->insertId($sql);
                   		
           
        $this->id = (int) $insertedId;
        foreach($this->choiceList as $choice)
        {
        	$choice->setQuestion($this);
        }
        
        
    }
    private function updateInDB()
    {
    	$dbCnx = ClaroLine::getDatabase();
        //update current survey in DB (we cannot change id, courseId or anonimity)
        $sql = "
        	UPDATE 		`".SurveyConstants::$QUESTION_TBL."`
            SET 		`text` 				= ".$dbCnx->quote($this->text).",
                		`type` 				= ".$dbCnx->quote($this->type)."  
            WHERE 		`id` = ".(int)$this->id ;
            
            $dbCnx->exec($sql);
    }
    
    private function deleteObsoleteChoices()
    {
    	$validChoiceIdList = array_map(array('Functions', 'idOf'),$this->getChoiceList());
    	
    	$sql = "
    			DELETE FROM 	`".SurveyConstants::$CHOICE_TBL."`  
        		WHERE			`questionId` = ".(int)$this->id." "; 
    	if(!empty($validChoiceIdList))  			
        		$sql .= " 
        		AND 			`id` NOT IN (".implode(', ',$validChoiceIdList).") ;";
    	$dbCnx = Claroline::getDatabase();
    	$dbCnx->exec($sql); 
    }
    
	public function isAnswered()
	{
	    $dbCnx = ClaroLine::getDatabase();
        $sql = "
        	SELECT 		COUNT(*) AS answers
        	FROM 		`".SurveyConstants::$ANSWER_TBL."` A
        	INNER JOIN  `".SurveyConstants::$SURVEY_LINE_QUESTION_TBL."` SLQ 
        	ON			 A.`surveyLineId` = SLQ.`id`
        	WHERE 		 SLQ.`questionId` = ".(int)$this->id." ; ";
        
        $resultSet = $dbCnx->query($sql);
        $row = $resultSet->fetch();
        return ((int)$row['answers']) > 0;
        
	}
	
	public function delete()
	{
		if($this->id == -1)
			return;
		$dbCnx = Claroline::getDatabase();
		$this->deleteSurveyLines($dbCnx);
		$this->deleteLinkedChoices($dbCnx);		
		$this->deleteQuestion($dbCnx);
	}
	private function deleteSurveyLines($dbCnx)
	{
		$sql = "
	        		DELETE 		SLQ, A, AI 
	        		FROM 		`".SurveyConstants::$SURVEY_LINE_QUESTION_TBL."` AS SLQ 
	        		INNER JOIN 	`".SurveyConstants::$ANSWER_TBL."` AS A 
	        		ON 			SLQ.`id` = A.`surveyLineId` 
	        		INNER JOIN 	`".SurveyConstants::$ANSWER_ITEM_TBL."` AS AI  
	        		ON 			A.`id` = AI.`answerId` 
	        		WHERE 		SLQ.`questionId` = ".(int) $this->id." ; ";
	    $dbCnx->exec($sql);        		
	}
	private function deleteLinkedChoices($dbCnx)
	{
		$sql = "
        	DELETE 				C, O
        	FROM 				`".SurveyConstants::$CHOICE_TBL."` AS C `
        	INNER JOIN 			`".SurveyConstants::$OPTION_TBL."` AS O 
	        ON 					C.`id` = O.`choiceId` 
        	WHERE 				C.`questionId` = ".(int)$this->id;
        $dbCnx->exec($sql);
	}
	private function deleteQuestion($dbCnx)
	{
		$sql = "
        	DELETE FROM `".SurveyConstants::$QUESTION_TBL."`
        	WHERE 		`id` = ".(int)$this->id;
        $dbCnx->exec($sql);
	}
   
    
}