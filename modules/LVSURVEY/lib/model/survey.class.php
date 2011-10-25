<?php

From::module('LVSURVEY')->uses(	'util/surveyConstants.class', 
								'util/dateValidator.class', 
								'model/participation.class', 
								'model/question.class', 
								'model/surveyLine.class');
FromKernel::uses(	'claroCourse.class', 
					'utils/input.lib', 
					'utils/validator.lib');

class Survey {
	
	const DEFAULT_MAX_COMMENT_SIZE = 200;
	
	//unique id of the survey
    public $id;
	
    //course owning to this survey
    protected $courseId;
    protected $course;    
    
    //title of the survey
    public $title;

    //description of the survey
    public $description;
      
    //if the survey is anonymous
    public $is_anonymous;
    
    //visibility of the survey to users
    public $is_visible;
      
    //visibility of results to users : 'VISIBLE'|'INVISIBLE'|'VISIBLE_AT_END'
    public $resultsVisibility;
    
    //startDate of the survey
    public $startDate;
    
    //endDate of the survey
    public $endDate;    
    
    //max size of comments
    public $maxCommentSize;
	
    //rank of survey
    public $rank;

    protected $surveyLineList;
	
    //questions of the survey
    protected $questionList;	
        
    //map userID to answer
    protected $participationList;

    private $allowChangeAnswers;

    public function __construct($courseId)
    {
        $this->id = -1;
        $this->courseId = mysql_real_escape_string($courseId);        
        $this->title = '';
        $this->description = '';
        $this->is_anonymous = true;
        $this->is_visible = false;
        $this->allowChangeAnswers = true;
        $this->resultsVisibility = 'INVISIBLE';
        $this->startDate = time();
        $nextMonth = strtotime( "+1 month" );
        $this->endDate = $nextMonth;
        $this->maxCommentSize = self::DEFAULT_MAX_COMMENT_SIZE;
        $this->rank = -1;
        
        
        $this->course = NULL;
        $this->questionList = array();		
		$this->participationList = array();
		$this->surveyLineList = array();
		$validationErrors = '';
    }
    
    static function __set_state($array = array())
    {
    	$res = new Survey($array['courseId']);
        foreach ($array as $akey => $aval) 
        {            
            $res -> {$akey} = $aval;
        }
        return $res;
    }	
    
    //load survey from the db
    static function load($id)
    {
    	if($id <= 0)
    		throw new Exception("Invalid Survey Id");
    	$dbCnx = Claroline::getDatabase();
        /*
        * get row of table
        */
        $sql = "SELECT
                   `id` 							AS id,
                   `title`							AS title,
                   `courseId`						AS courseId,
                   `description`					AS description,
                   `is_anonymous` 					AS is_anonymous,
                   `is_visible`						AS is_visible,
                   `allow_change_answers`			AS allowChangeAnswers,
                   `resultsVisibility`				AS resultsVisibility,
                   `maxCommentSize`					AS maxCommentSize,
                   `rank`							AS rank,
                   UNIX_TIMESTAMP(`startDate`) 		AS `startDate`,
                   UNIX_TIMESTAMP(`endDate`) 		AS `endDate`
           FROM `".SurveyConstants::$SURVEY_TBL."`
           WHERE `id` = ".(int) $id; 
         
         
        $resultSet = $dbCnx->query($sql);        
        $data = $resultSet->fetch();
		if(empty($data))
			throw new Exception("Invalid Survey Id");
        return self::__set_state($data);
    }
    
	//load properties of the survey from form (create or edit survey form)
    static function loadFromForm($courseId)
    {
    	
    	//PARSE INPUT
    	$userInput = Claro_UserInput::getInstance();
    	$userInput->setValidator('surveyStartDate',DateValidator::getInstance());
    	$userInput->setValidator('surveyEndDate',DateValidator::getInstance());
    	
    	$commentValidator = new Claro_Validator_Pcre('/^0*([0-9]{1,2}|1[0-9]{2}|200)$/');
    	$userInput->setValidator('maxCommentSize',$commentValidator);
    	
    	try
    	{
	    	$formId = (string)$userInput->getMandatory('surveyId');  
	    	$formTitle = (string)$userInput->getMandatory('surveyTitle');
	    	$formIsAnonymous = (string)$userInput->getMandatory('surveyIsAnonymous');
	    	$formDescription = (string)$userInput->getMandatory('surveyDescription');
	    	$formResultsVisibility = (string)$userInput->getMandatory('surveyResultsVisibility');
	    	$formMaxCommentSize = (int)$userInput->getMandatory('maxCommentSize');
            $formAllowChangeAnswers = (string)$userInput->getMandatory('surveyAllowChangeAnswers');
    	}
    	catch(Claro_Validator_Exception $e)
    	{
    		throw new Claro_Validator_Exception(get_lang('You have forgotten to fill a mandatory field'));
    	}
    	try
    	{
    		$formStartDate = (string)$userInput->get('surveyStartDate', '');
    		$formEndDate = (string)$userInput->get('surveyEndDate', '');
    	} 
    	catch (Claro_Validator_Exception $e)
    	{
    		throw new Claro_Validator_Exception(get_lang('Date format must be DD/MM/YY'));
    	}	
    	
    	
    	//UPDATE SURVEY
    	$survey = new Survey($courseId);    	
    	//if survey already exists we must first load its current state
		if($formId == -1)
		{			
			// we can never override anonimity
    		$survey->is_anonymous = ('true' == $formIsAnonymous);
			
		}
		else 
		{
			$survey = self::load($formId);	
		}
		
		$survey->title = $formTitle;
		$survey->description = $formDescription;
		$survey->resultsVisibility = $formResultsVisibility;
		$survey->startDate = DateValidator::getInstance()->getTimeStamp($formStartDate);
		$survey->endDate = DateValidator::getInstance()->getTimeStamp($formEndDate);
		$survey->maxCommentSize = $formMaxCommentSize;
        $survey->allowChangeAnswers = ('true' == $formAllowChangeAnswers);
		
		return $survey;
            
    }
    
    static function loadSurveyList($courseId)
    {
		$dbCnx = Claroline::getDatabase();
        $sql = "
        	SELECT
                   		`id` 							AS id,
                   		`title`							AS title,
                   		`courseId`						AS courseId,
                   		`description`					AS description,
                   		`is_anonymous`					AS is_anonymous,
                   		`is_visible`					AS is_visible,
                        `allow_change_answers`			AS allowChangeAnswers,
                   		`resultsVisibility`				AS resultsVisibility,
                   		`maxCommentSize`				AS maxCommentSize,
                   		`rank`							AS rank, 
                   		UNIX_TIMESTAMP(`startDate`) 	AS `startDate`,
                   		UNIX_TIMESTAMP(`endDate`) 		AS `endDate`
           	FROM 		`".SurveyConstants::$SURVEY_TBL."`
           	WHERE 		`courseId` = ".$dbCnx->quote($courseId)."
           	ORDER BY 	`rank` ASC";
    	

        $resultSet = $dbCnx->query($sql);
        $res = array();
        foreach($resultSet as $row)
        {
        	$res[] = Survey::__set_state($row);
        }
		return $res;
    }
    
    public function getCourse()
    {
    	if(empty($this->course))
    	{
    		$this->loadCourse();
    	}
    	return $this->course;
    }
    public function setCourse($course)
    {
    	$this->course = course;
    	$this->courseId = $course->courseId;
    }
    private function loadCourse()
    {
    	$this->course = new ClaroCourse();
    	$this->course->load($this->courseId);
    	
    }
    public function getCourseId()
    {
    	return $this->courseId;
    }
    public function setCourseId($courseId)
    {
    	$this->courseId = $coursId;
    	$this->course = NULL;
    }
    
	public function getSurveyLineList()
    {
    	if(empty($this->surveyLineList))
    	{
    		$this->loadSurveyLineList();
    	}
    	return $this->surveyLineList;
    }
    
    private function loadSurveyLineList()
    {
    	$this->surveyLineList = SurveyLineFactory::linesOfSurvey($this); 
    }
    
    public function getParticipationList()
    {
    	if(empty($this->participationList))
    	{
    		$this->loadParticipationList();
    	}
    	return $this->participationList;
    }
    private function loadParticipationList()
    {
    	$dbCnx = Claroline::getDatabase();
    	$sql = "
    			SELECT 	P.`id`							as id, 
    					P.`userId`						as userId,
    					P.`surveyId`					as surveyId
                FROM 	`".SurveyConstants::$PARTICIPATION_TBL."` as P
                WHERE 	P.`surveyId` = ".(int)$this->id."; ";
        
    	$resultSet = $dbCnx->query($sql);
        $this->participationList = array();	    
	    foreach( $resultSet as $row )
	    {
	    	$participation = Participation::__set_state($row);
	    	$participation->setSurvey($this);
            $this->participationList[$row['id']] = $participation;
	    }
    }
   
	//check if all ok to go to db
    private function validate()
    {
    	$validationErrors = array();
    	if(empty($this->title))
    		$this->validationErrors[] = 'Title is required';
    	
    	
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
    		return;
    	}
    	
    	$this->updateInDB();
    }
    
    
    private function insertInDB()
    {
    	$dbCnx = ClaroLine::getDatabase();
    	
        //Insert new survey in DB
        $sql = "
        		INSERT INTO `".SurveyConstants::$SURVEY_TBL."`
                SET `courseId` 				= ".$dbCnx->quote($this->courseId).",
                	`title` 				= ".$dbCnx->quote($this->title).",
                    `description` 			= ".$dbCnx->quote($this->description).",
                    `is_anonymous` 			= ".($this->is_anonymous?1:0).",
                    `is_visible` 			= ".($this->is_visible?1:0).",
                    `allow_change_answers`  = ".($this->allowChangeAnswers?1:0).",
                    `resultsVisibility` 	= ".$dbCnx->quote($this->resultsVisibility).",
                    `maxCommentSize`		= ".(int) $this->maxCommentSize.", 
                    `startDate` = ".(is_null($this->startDate)?"NULL":"FROM_UNIXTIME(".$this->startDate.")").",
                    `endDate` = ".(is_null($this->endDate)?"NULL":"FROM_UNIXTIME(".$this->endDate.")");
			
        // execute the creation query and get id of inserted assignment
        $dbCnx->exec($sql);
        $insertedId = $dbCnx->insertId();      
        

        $sql = "
        	UPDATE `".SurveyConstants::$SURVEY_TBL."`
            SET `rank` = ".(int) $insertedId."
        	WHERE `id` = ".(int) $insertedId;
      	$dbCnx->exec($sql);
           
        $this->id = (int) $insertedId;
        $this->rank = (int) $insertedId;
    }
    private function updateInDB()
    {
    	$dbCnx = ClaroLine::getDatabase();
        //update current survey in DB (we cannot change id, courseId or anonimity)
        $sql = "
        	UPDATE 	`".SurveyConstants::$SURVEY_TBL."`
            SET 	`title` 				= ".$dbCnx->quote($this->title).",
                    `description` 			= ".$dbCnx->quote($this->description).", 
                   	`is_visible` 			= ".($this->is_visible?1:0).",
                    `resultsVisibility` 	= ".$dbCnx->quote($this->resultsVisibility).",
                    `maxCommentSize`		= ".$dbCnx->quote($this->maxCommentSize).",
                    `allow_change_answers`  = ".($this->allowChangeAnswers?1:0).",
                    `startDate` = ".(is_null($this->startDate)?"NULL":"FROM_UNIXTIME(".$this->startDate.")").",
                    `endDate` = ".(is_null($this->endDate)?"NULL":"FROM_UNIXTIME(".$this->endDate.")")." 
            WHERE 	`id` = ".(int)$this->id ;
            
            $dbCnx->exec($sql);
    }
    public function addSurveyLine($surveyLine)
    {       
        $surveyLineList = $this->getSurveyLineList();
        $this->surveyLineList[$surveyLine->id] = $surveyLine;   
           
        
    }
    public function removeLine($surveyLineId)
    {
    	$surveyLineList = $this->getSurveyLineList();
        $surveyLineList[$surveyLineId]->delete();
        unset($this->surveyLineList[$surveyLineId]);
    }
    /** remova all occurences of question with id = $questionId */
	public function removeQuestion($questionId)
    {
    	$surveyLineList = $this->getSurveyLineList();
    	foreach($surveyLineList as $surveyLine){
    		if(isset($surveyLine->question) && $surveyLine->question->id == $questionId)
    		{
    			$surveyLine->delete();
    		}
    	}
    }
    
    public function moveLine($surveyLineId, $up)
    {
        $surveyLineList = $this->getSurveyLineList();
        $surveyLineList[$surveyLineId]->move($up);
    }
	//check if someone has answered survey
	public function isAnswered()
	{
	    return count($this->getParticipationList()) > 0;
	}
	public function delete()
    {
    	if($this->id == -1)
			return;
		$dbCnx = Claroline::getDatabase();
		
		$participationIdList = $this->getParticipationIdList();
		$answerIdList = $this->getLinkedAnswerIdList($dbCnx, $participationIdList);
		if(!empty($answerIdList))
		{
			$this->deleteLinkedAnswerItems($answerIdList, $dbCnx);
			$this->deleteLinkedAnswers($answerIdList, $dbCnx);
		}
        if(!empty($participationIdList))
        {
        	$this->deleteLinkedParticipation($participationIdList, $dbCnx);
        	
        }
    	if($this->id != -1)
		{
			$this->deleteRelationsToQuestions($dbCnx);
        	$this->deleteSurvey($dbCnx);
		}
    }
    
    private function deleteLinkedParticipation($participationIdList, $dbCnx)
    {
    	$sql = "
    		DELETE FROM `".SurveyConstants::$PARTICIPATION_TBL."`
    		WHERE 		`id` IN (".implode(', ',$participationIdList).") ; ";
    	$dbCnx->exec($sql);
    }
	
	private function getParticipationIdList()
	{
		$particpationIdList = array();
		foreach($this->getParticipationList() as $participation)
		{
			$particpationIdList[] = $participation->id;
		}
		return $particpationIdList;
	}
	private function getLinkedAnswerIdList($dbCnx, $participationIdList)
	{
		if(empty($participationIdList))return array();
		$sqlLinkedAnswers = "
    		SELECT 			A.`id`			AS answerId
    		FROM			`".SurveyConstants::$ANSWER_TBL."` A
    		WHERE 			A.`participationId` IN (".implode(', ',$participationIdList).") ; ";
    	
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
    
	private function deleteRelationsToQuestions($dbCnx)
	{
		$sql = "
	        		DELETE FROM `".SurveyConstants::$SURVEY_LINE_TBL."`
	        		WHERE 		`surveyId` = ".(int)$this->id;
	    $dbCnx->exec($sql); 
	}
	private function deleteSurvey($dbCnx)
	{
		$sql = "
        	DELETE FROM `".SurveyConstants::$SURVEY_TBL."`
        	WHERE 		`id` = ".(int)$this->id;
        $dbCnx->exec($sql);
	}
    
	public function moveSurvey($up)
    {
        $dbCnx = Claroline::getDatabase();

        //exchange rank with 
        $sqlSubSelect= "
        		SELECT	`rank`
                FROM 	`".SurveyConstants::$SURVEY_TBL."`
                WHERE 	`id` 		= ".(int) $this->id."  ";
        $sqlSelect = "
        		SELECT
                  			`id`,
                  			`rank`
                FROM 		`".SurveyConstants::$SURVEY_TBL."`
                WHERE 		`rank` ".($up?"<=":">=")." (".$sqlSubSelect.")
                ORDER BY	`rank` ".($up?"DESC":"ASC")." LIMIT 2";       
         
        
        $resultSet = $dbCnx->query($sqlSelect);
        if ( $resultSet->count() < 2)
        	throw new Exception ("Cannot move this survey");
        $ranks = array();
        foreach($resultSet as $row)
        {
        	$ranks[] = $row;
        }    
        	
    	//exchange ranks
    	//TODO transaction
        $sqlUpdateQ1 = "
        			UPDATE `" . SurveyConstants::$SURVEY_TBL."`
                    SET `rank` = " . (int) $ranks[1]['rank'] . "
                    WHERE `id` = " . (int) $ranks[0]['id'] . " ; ";
        $dbCnx->exec($sqlUpdateQ1);
        $sqlUpdateQ2 = "
        			UPDATE `" . SurveyConstants::$SURVEY_TBL . "` 
                    SET `rank` = " . (int) $ranks[0]['rank'] . " 
                    WHERE `id` = " . (int) $ranks[1]['id'] . " ; ";
    	$dbCnx->exec($sqlUpdateQ2);
    }
    
	// results visibility = 'VISIBLE'|'INVISIBLE'|'VISIBLE_AT_END'
    public function areResultsVisibleNow()
    {
    	if('VISIBLE' == $this->resultsVisibility) 
    		return true;
    	if('INVISIBLE' == $this->resultsVisibility)
    		return false;
    	return $this->hasEnded();
    	
    }
    public function isAccessible()
    {
    	if(!$this->is_visible) return false;
    	if(empty($this->startDate)) return false;
    	if($this->startDate > time()) return false;    	
    	return true;
    }
    public function reset()
    {
    	$participationList = $this->getParticipationList();
    	foreach($participationList as $participation)
    	{
    		$participation->delete();
    	}    	
    }
    public function hasEnded()
    {
    	if(0 == $this->endDate) return false;
    	return $this->endDate < time();    	
    }
	public function isStarted()
    {
    	if(0 == $this->startDate) return false;
    	return $this->startDate < time();    	
    }

    public function isAllowedToChangeAnswers()
    {
        return $this->allowChangeAnswers;
    }

    public function setAllowToChangeAnswers($allowed)
    {
        $this->allowChangeAnswers = $allowed;
    }

    
}