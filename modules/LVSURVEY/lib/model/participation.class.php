<?php
From::module('LVSURVEY')->uses( 'util/surveyConstants.class', 
                                'model/question.class', 
                                'model/answer.class', 
                                'model/surveyLine.class',
                                'model/guest.class.php');

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
        
        $res = new Participation($array['surveyId'], $array['userId']);
        
        foreach ($array as $akey => $aval) 
        {
            $res -> {$akey} = $aval;
        }
        
        return $res;
    }

    static function loadParticipationOfUserForSurvey($userId, $surveyId)
    {
        $dbCnx = Claroline::getDatabase();
        
        $sql = "
            SELECT
                       `id`                             AS id,
                       `surveyId`                       AS surveyId,
                       `userId`                         AS userId
            FROM        `".SurveyConstants::$PARTICIPATION_TBL."` 
            WHERE       `userId` = ".(int) $userId."
            AND         `surveyId` = ".(int)$surveyId."; "; 
         
        $resultSet = $dbCnx->query($sql);
        if($resultSet->isEmpty())
        {
            return new Participation($surveyId, $userId);
        }
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
                       `id`                             AS id,
                       `surveyId`                       AS surveyId,
                       `userId`                         AS userId
            FROM        `".SurveyConstants::$PARTICIPATION_TBL."` 
            WHERE       `id` = ".(int) $id."; "; 
        
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
        $surveyLineList = $survey->getSurveyLineList();
        
        foreach($surveyLineList as $surveyLine)
        {
            if(!is_a($surveyLine, 'QuestionLine'))continue;
            $answer = Answer::loadAnswerOfQuestionFromForm($participation, $surveyLine);
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
        SET 
            `updated_at`                        = NULL, 
            `surveyId`              = ".(int)$this->surveyId.",
            `userId`                = ".(int) $this->userId."; ";
            
        // execute the creation query and get id of inserted assignment
        $dbCnx->exec($sql);
        $insertedId = $dbCnx->insertId();
        $this->id = $insertedId;
    }
    
    private function updateInDB()
    {
        //automatically update timestamp
        $dbCnx = ClaroLine::getDatabase();
        $sql = "
            UPDATE      `".SurveyConstants::$PARTICIPATION_TBL."`
            SET `updated_at`                = NULL
            WHERE       `id` = ".(int)$this->id ;
            
            $dbCnx->exec($sql);
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
        try
        {
            $this->user->loadFromDatabase();
        }catch(Exception $e)
        {
            $this->setUser(new Guest());
        }
    }
    
    public function setUser($user)
    {
        $this->userId = $user->userId;
        $this->user = $user;
    }
    
    public function getAnswerList()
    {
        if(empty($this->answerList))
        {
            $this->loadAnswerList();
        }
        
        return $this->answerList;
    }
    
    private function loadAnswerList()
    {
        $dbCnx = Claroline::getDatabase();
        $sql = "SELECT  A.`id`                  as id, 
                        A.`participationId`     as participationId, 
                        A.`surveyLineId`        as surveyLineId, 
                        A.`comment`             as comment,
                        A.`predefined`          as predefinedValue
                FROM    `".SurveyConstants::$ANSWER_TBL."` as A
                WHERE   A.`participationId` = ".(int)$this->id."; ";
        
        $resultSet = $dbCnx->query($sql);
        $surveyLineList = $this->getSurvey()->getSurveyLineList();
        
        $this->answerList = array();
        
        foreach( $resultSet as $row )
        {
            $answer = Answer::__set_state($row);
            $answer->setParticipation($this);
            $answer->setQuestionLine($surveyLineList[$row['surveyLineId']]);
            $this->answerList[$row['id']] = $answer;
        }
    }
    
    public function getAnswerForSurveyLine($surveyLine)
    {
        $answerList = $this->getAnswerList();
        foreach($answerList as $answer)
        {
            if($answer->getSurveyLineId() == $surveyLine->id) return $answer;
        }
        $res = new Answer($this->id, $surveyLine->id);
        $res->setParticipation($this);
        $res->setQuestionLine($surveyLine);
        return $res;
    }
    
    public function delete()
    {
        if($this->id == -1)
        {
            return;
        }
        
        $answerList = $this->getAnswerList();
        
        foreach($answerList as $answer)
        {
            $answer->delete();
        }
        $sql = "
            DELETE FROM `".SurveyConstants::$PARTICIPATION_TBL."`
            WHERE       `id` = ".(int) $this->id."; "; 
        Claroline::getDatabase()->exec($sql);
    }
    
    public function isValid()
    {
        //check for required answers
        $surveyLineList = $this->survey->getSurveyLineList();
        
        foreach($surveyLineList as $surveyLine)
        {
            if( $surveyLine instanceof SeparatorLine)
            {
                continue;
            }
            
            $check = $this->checkAnswerForSurveyLine($surveyLine->id);
            
            if(!$check)
            {
                return false;
            }
        }
        
        return true;
    }
    
    private function checkAnswerForSurveyLine($surveyLineId)
    {
        $answerList = $this->getAnswerList();
        
        foreach($answerList as $answer)
        {
            if($answer->getSurveyLineId() == $surveyLineId) return $answer->isValid();
        }
        
        return false;
    }
    
    public function isNew()
    {
        return $this->id <= 0;
    }
}