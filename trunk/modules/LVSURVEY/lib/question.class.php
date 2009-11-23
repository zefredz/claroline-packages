<?php
From::module('LVSURVEY')->uses('surveyConstants.class', 'choice.class');
FromKernel::uses('utils/input.lib', 'utils/validator.lib');

class Question
{
	
	public $id;
	
	public $text;
	
	public $type;
	
	public $choiceAlignment;
	
	protected $used;
	
	protected $choiceList;
	
	
	public function __construct()
    {
        $this->id = -1;
        $this->text = '';
        $this->type = 'MCSA';    
        
        $this->choiceAlignment = 'VERTI';
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
            	       Q.`alignment`					AS choiceAlignment,
            	       COUNT(QR.`surveyId`) 			AS used
           	FROM 		`".SurveyConstants::$QUESTION_TBL."` Q
           	LEFT JOIN 	`".SurveyConstants::$REL_SURV_QUEST_TBL."` AS QR
            ON 			Q.`id`= QR.`questionId`              
           	WHERE 		Q.`id`  = ".(int) $id."
           	GROUP BY	Q.`id` ; "; 
         
         
        $resultSet = $dbCnx->query($sql);
        $data = $resultSet->fetch();
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
            	       Q.`alignment`					AS choiceAlignment,
            	       COUNT(QR.`surveyId`) 			AS used
           	FROM 		`".SurveyConstants::$QUESTION_TBL."` Q
           	LEFT JOIN 	`".SurveyConstants::$REL_SURV_QUEST_TBL."` AS QR
            ON 			Q.`id`= QR.`questionId`
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
    	$questionTypeValidator = new Claro_Validator_AllowedList(array('OPEN','MCSA','MCMA'));
    	$alignValidator = new Claro_Validator_AllowedList(array('VERTI','HORIZ'));
    	$userInput->setValidator('questionType',$questionTypeValidator);
    	$userInput->setValidator('questionAlignment',$alignValidator);
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
	    	$formAlignment = (string)$userInput->getMandatory('questionAlignment');
    	} 
    	catch (Claro_Validator_Exception $e)
    	{
    		throw new Claro_Validator_Exception(get_lang('Unknown Question Type or Alignment'));
    	}

    	$duplicate = ('1' == $formDuplicate);
    	
    	
    	//UPDATE QUESTION   	
    	//if survey already exists we must first load its current state
		if($formId == -1 || $duplicate)
		{			
			$question = new Question();
			
		}
		else 
		{
			$question = self::load($formId);	
		}
		
		$question->text = $formText;
		$question->choiceAlignment = $formAlignment;
		$question->type = $formType;
		
		//HANDLE CHOICE LIST
		if('MCMA' == $question->type || 'MCSA' == $question->type)
		{
			$question->choiceList = array();
			for($i = 1; isset($_REQUEST['questionCh'.$i]) && !empty($_REQUEST['questionCh'.$i]); ++$i)
			{
				$choiceId = $duplicate?-1:(int)$_REQUEST['questionChId'.$i];
				$choiceText = $_REQUEST['questionCh'.$i];
				$choice = Choice::__set_state(array('id' => $choiceId, 'text' => $choiceText, 'questionId' => $question->id));
				$choice->setQuestion($question);
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
            $this->choiceList[$row['id']] = $choice;
	    }    
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
    	
    	if(!in_array($this->type, array('OPEN','MCSA','MCMA')))
    		$this->validationErrors[] = 'Unknown question type';
    		
    	if(!in_array($this->choiceAlignment, array('VERTI','HORIZ')))
    		$this->validationErrors[] = 'Unknown alignment';
    	
    	
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
    	
    	if('MCMA' == $this->type || 'MCSA' == $this->type)
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
                			`type` 				= ".$dbCnx->quote($this->type).",
                    		`alignment`			= ".$dbCnx->quote($this->choiceAlignment)."; ";
                    
			
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
                		`type` 				= ".$dbCnx->quote($this->type).",
                    	`alignment`			= ".$dbCnx->quote($this->choiceAlignment)." 
            WHERE 		`id` = ".(int)$this->id ;
            
            $dbCnx->exec($sql);
    }
	public function isAnswered()
	{
	    $dbCnx = ClaroLine::getDatabase();
        $sql = "
        	SELECT 		COUNT(`questionId`) AS answers
        	FROM 		`".SurveyConstants::$ANSWER_TBL."` 
        	WHERE 		`questionId` = ".(int)$this->id." ; ";
        
        $resultSet = $dbCnx->query($sql);
        $row = $resultSet->fetch();
        return ((int)$row['answers']) > 0;
        
	}
	
	public function delete()
	{
		if($this->id == -1)
			return;
		$dbCnx = Claroline::getDatabase();
		$answerIdList = getLinkedAnswerIdList($dbCnx);
		if(!empty($answerIdList))
		{
			deleteLinkedAnswerItems($answerIdList, $dbCnx);
			deleteLinkedAnswers($answerIdList, $dbCnx);
		}
		deleteRelationsToSurvey($dbCnx);
		deleteLinkedChoices($dbCnx);		
		deleteQuestion($dbCnx);
	}
	private function getLinkedAnswerIdList($dbCnx)
	{
		$sqlLinkedAnswers = "
    		SELECT 			A.`id`			AS answerId
    		FROM			`".SurveyConstants::$ANSWER_TBL."` A
    		WHERE 			A.`questionId` = ".(int)$this->id." ; ";
    	
    	$answerIdRS = $dbCnx->query($sqlLinkedAnswers);
    	$answerIdList = array();
    	foreach($answerIdRS as $row)
    	{
    		$answerIdList[] = $row['answerId'];	
    	}
    	return $answerIdList;
	}
	private function deleteLinkedAnswerItems($answerIdList, $dbCnx)
	{
		$sql = "
	        		DELETE FROM `".SurveyConstants::$ANSWER_ITEM_TBL."`
	        		WHERE 		`answerId` IN (".implode(', ',$answerIdList)."); ";
	    $dbCnx->exec($sql);
	}
	private function deleteLinkedAnswers($answerIdList, $dbCnx)
	{
		$sql = "
	        		DELETE FROM `".SurveyConstants::$ANSWER_TBL."`
	        		WHERE 		`id` IN (".implode(', ',$answerIdList)."); ";
	    $dbCnx->exec($sql);        		
	}
	private function deleteRelationsToSurvey($dbCnx)
	{
		$sql = "
	        		DELETE FROM `".SurveyConstants::$REL_SURV_QUEST_TBL."`
	        		WHERE 		`questionId` = ".(int)$this->id;
	    $dbCnx->exec($sql); 
	}
	private function deleteLinkedChoices($dbCnx)
	{
		$sql = "
        	DELETE FROM `".SurveyConstants::$CHOICE_TBL."`
        	WHERE 		`questionId` = ".(int)$this->id;
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