<?php
From::module('LVSURVEY')->uses('SurveyConstants.class', 'Question.class', 'Answer.class');

class Participation
{
	public $id;
	
	protected $surveyId;
	protected $survey;
	
	protected $userId;
	protected $user;
	
	protected $answerList;
	
	public function __construct($surveyId, $userId)
	{
		$this->id = -1;
		$this->surveyId = $surveyId;
		$this->survey = NULL;
		
		$this->userId = $userId;
		$this->user = NULL;
		
		$this->answerList = array();
	}
	
	static function __set_state($array)
    {
    	if(empty($array)) return false;
    	static $properties = array();
    	if(empty($properties))
    	{
    		$properties = array_keys(get_object_vars(new Participation(-1,-1)));
    	}
    	
    	$res = new Participation($array['surveyId'], $array['userId']);
        foreach ($array as $akey => $aval) {
            if(in_array($akey,$properties))
            {
            	$res -> {$akey} = $aval;
            }
        }
        return $res;
    }

    static function loadParticipationOfUserForSurvey($userId, $surveyId)
    {
    	$dbCnx = Claroline::getDatabase();
        
        $sql = "
        	SELECT
            	       `id` 							AS id,
            	       `surveyId`						AS surveyId,
            	       `userId`							AS userId
           	FROM 		`".SurveyConstants::$PARTICIPATION_TBL."` 
           	WHERE 		`userId` = ".(int) $userId."
           	AND 		`surveyId` = ".(int)$surveyId."; "; 
        
                
         
        $resultSet = $dbCnx->query($sql);
        if($resultSet->isEmpty()) return new Participation($surveyId, $userId);
        $data = $resultSet->fetch();
        $res =  self::__set_state($data);
        $res->loadAnswerList();
        return $res;
    }
    
    static function load($id)
    {
    	$dbCnx = Claroline::getDatabase();
        
        $sql = "
        	SELECT
            	       `id` 							AS id,
            	       `surveyId`						AS surveyId,
            	       `userId`							AS userId
           	FROM 		`".SurveyConstants::$PARTICIPATION_TBL."` 
           	WHERE 		`id` = ".(int) $id."; "; 
         
         
        $resultSet = $dbCnx->query($sql);
        $data = $resultSet->fetch();
        $res =  self::__set_state($data);
        $res->loadAnswerList();
        return $res;
    }
    
    static function loadFromForm()
    {
    	$userId = claro_get_current_user_id();    	
    	$userInput = Claro_UserInput::getInstance();    	
    	
    	try
    	{
	    	$formId = (int)$userInput->getMandatory('participationId');  
	    	$formSurveyId = (int)$userInput->getMandatory('surveyId');	    	
    	}
    	catch(Claro_Validator_Exception $e)
    	{
    		throw new Claro_Validator_Exception(get_lang('You have forgotten to fill a mandatory field'));
    	}    	
    	
		if($formId == -1 )
		{			
			$participation = new Participation($formSurveyId, $userId);			
		}
		else 
		{
			$participation = self::load($formId);	
		}	
		
		$participation->answerList = array();
		$survey = $participation->getSurvey();
		$questionList = $survey->getQuestionList();
		foreach($questionList as $question)
		{
			$answer = Answer::loadAnswerOfQuestionFromForm($question);
			$answer->setParticipation($participation);
			$participation->answerList[] = $answer;
		}		
		return $participation;		
    }
    
    public function save()
    {
    	if($this->getSurvey()->hasEnded())
    	{
    		throw new Exception("You cannot make or change your answer after the end of the Survey");
    	}
    	if(-1 == $this->id)
    	{
    		$this->insertInDB();
    	}
    	else
    	{
    		$this->updateInDB();
    	}
    	
    	foreach($this->answerList as $answer)
    	{
    		$answer->setParticipation($this);
    		$answer->save();    		
    	}
    }
	private function insertInDB()
    {
    	$dbCnx = ClaroLine::getDatabase();
    	
        $sql = "
        		INSERT INTO `".SurveyConstants::$PARTICIPATION_TBL."`
                SET `surveyId` 				= ".(int)$this->surveyId.",
                	`userId` 				= ".(int) $this->userId."; ";
			
        // execute the creation query and get id of inserted assignment
        $dbCnx->exec($sql);
        $insertedId = $dbCnx->insertId();
        $this->id = $insertedId;      
        

    }
    private function updateInDB()
    {
    	//NOTHING TO DO HERE
    }
    
	public function getSurvey()
    {
    	if(empty($this->survey))
    	{
    		$this->loadSurvey();
    	}
    	return $this->survey;
    }
	private function loadSurvey()
    {
    	$this->survey = Survey::load($this->surveyId);
    }
    public function setSurvey($survey)
    {
    	$this->survey = $survey;
    	$this->surveyId = $survey->id;
    }
    
    public function getUser()
    {
    	if(empty($this->user))
    	{
    		$this->loadUser();
    	}
    	return $this->user;
    }
    private function loadUser()
    {
    	$this->user = new Claro_User($this->userId);
    	$this->user->loadFromDatabase();
    }
    
    public function getAnswerList()
    {
    	return $this->answerList;
    }
	private function loadAnswerList()
    {
    	$dbCnx = Claroline::getDatabase();
    	$sql = "SELECT 	A.`id`					as id, 
    					A.`participationId`		as participationId, 
    					A.`questionId`			as questionId, 
    					A.`comment`				as comment
                FROM 	`".SurveyConstants::$ANSWER_TBL."` as A
                WHERE 	A.`participationId` = ".(int)$this->id."; ";
        
    	$resultSet = $dbCnx->query($sql);
        $questionList = $this->getSurvey()->getQuestionList();
    	
    	
    	$this->answerList = array();
        foreach( $resultSet as $row )
	    {
	    	$answer = Answer::__set_state($row);
	    	$answer->setParticipation($this);
	    	$answer->setQuestion($questionList[$answer->getQuestionId()]);
            $this->answerList[$row['id']] = $answer;
	    }    	
    }
    
    public function getAnswerForQuestion($questionId)
    {
    	$answerList = $this->getAnswerList();
    	foreach($answerList as $answer)
    	{
    		if($answer->getQuestionId() == $questionId) return $answer;
    	}
    	return new Answer($this->id, $questionId);
    }
    public function delete()
    {
    	if($this->id == -1)return;
    	$answerList = $this->getAnswerList();
    	foreach($answerList as $answer)
    	{
    		$answer->delete();
    	}
    	$sql = "
    		DELETE FROM `".SurveyConstants::$PARTICIPATION_TBL."`
    		WHERE 		`id` = ".(int) $this->id."; "; 
    	Claroline::getDatabase()->exec($sql);
    } 
	
}
