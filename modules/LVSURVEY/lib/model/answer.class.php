<?php

From::module('LVSURVEY')->uses( 'util/surveyConstants.class', 
                                'model/question.class', 
                                'model/choice.class');

class Answer
{
    public $id;
    
    protected $participationId;
    protected $participation;
    
    protected $surveyLineId;
    protected $questionLine;
    
    public $comment;
    
    protected $selectedChoiceList;
    protected $selectedOptionList;
    
    public function __construct($participationId, $surveyLineId)
    {
        $this->id = -1;
        $this->participationId = $participationId;
        $this->participation = NULL;
        $this->surveyLineId = $surveyLineId;
        $this->questionLine = NULL;
        $this->comment = '';
        $this->selectedChoiceList = array();
        $this->selectedOptionList = array();
    }
    
    static function __set_state($array)
    {
        if(empty($array)) return false;
        
        $res = new Answer($array['participationId'], $array['surveyLineId']);
        foreach ($array as $akey => $aval)
        {
            $res -> {$akey} = $aval;
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
                       A.`id`                           AS id,
                       A.`participationId`              AS participationId,
                       A.`surveyLineId`                 AS surveyLineId,
                       A.`comment`                      AS comment
            FROM        `".SurveyConstants::$ANSWER_TBL."` A
            WHERE       `id` = ".(int) $id."; "; 
         
         
        $resultSet = $dbCnx->query($sql);
        $data = $resultSet->fetch();
        return self::__set_state($data);
    }
    
    static function loadAnswerOfQuestionFromForm($participation, $questionLine)
    {
        $userInput = Claro_UserInput::getInstance();
        
        try
        {
            $formAnswerId = (int)$userInput->getMandatory('answerId'.$questionLine->id);
        }
        catch(Claro_Validator_Exception $e)
        {
            throw new Claro_Validator_Exception(get_lang('You have forgotten to fill a mandatory field'));
        }
        
        if($formAnswerId == -1 )
        {
            $answer = new Answer($participation->id, $questionLine->id);
            $answer->setQuestionLine($questionLine);
            $answer->setParticipation($participation);
        }
        else 
        {
            $answer = self::load($formAnswerId);
        }
        
        $answer->comment = $userInput->get('answerComment'.$questionLine->id, '');
        
        $answer->selectedChoiceList = Choice::loadSelectedChoicesFromForm($questionLine);
        $answer->selectedOptionList = Option::loadSelectedOptionFromForm($questionLine);
        
        return $answer;
    }
    public function save()
    {
        
        $surveyLineList = $this->getParticipation()->getSurvey()->getSurveyLineList();
        $maxCommentSize = $this->getQuestionLine()->maxCommentSize;
        $this->comment = substr(trim($this->comment),0,$maxCommentSize);
        
        if(-1 == $this->id)
        {
            $this->insertInDB();
        }
        else
        {
            $this->updateInDB();
        }
        
        $this->deleteOldChoices();
        $this->saveNewChoices();
    }
    
    private function deleteOldChoices()
    {
        $dbCnx = Claroline::getDatabase();
        $sqlDelete = "";
        $question = $this->getQuestionLine()->question;
        
        if('OPEN' == $question->type)
        {
            $sqlDelete .= "
                DELETE      AI, 
                            C 
                FROM    `".SurveyConstants::$ANSWER_ITEM_TBL."` AS AI 
                INNER JOIN `".SurveyConstants::$CHOICE_TBL."` AS C 
                ON AI.`choiceId` = C.`id` 
                ";
        }
        else
        {
            $sqlDelete .= "
                DELETE      AI
                FROM    `".SurveyConstants::$ANSWER_ITEM_TBL."` AS AI 
                INNER JOIN `".SurveyConstants::$CHOICE_TBL."` AS C 
                ON AI.`choiceId` = C.`id` 
                ";
        }
        
        $sqlDelete .= "WHERE AI.`answerId` = ". (int) $this->id." ";
        $dbCnx->exec($sqlDelete);
    }
    
    private function saveNewChoices()
    {
        $dbCnx = Claroline::getDatabase(); 
        $question = $this->getQuestionLine()->question;
        
        if('OPEN' == $question->type)
        {
            $this->selectedChoiceList[0]->save();
        }
        
        if('ARRAY' == $question->type )
        {
            foreach($this->selectedOptionList as $option)
            {
                $sql = "
                    INSERT INTO `".SurveyConstants::$ANSWER_ITEM_TBL."` 
                    SET `answerId` = ".(int)$this->id.",  
                        `choiceId` = ".(int)$option->getChoiceId().", 
                        `optionId` = ".(int)$option->getId()."; ";
                $dbCnx->exec($sql);
            }
            
            return;
        }
        
        foreach($this->selectedChoiceList as $choice)
        {
            $sql = "
                INSERT INTO `".SurveyConstants::$ANSWER_ITEM_TBL."` 
                SET `answerId` = ".(int)$this->id.",  
                    `choiceId` = ".(int)$choice->id."; ";
            $dbCnx->exec($sql);
        }
    }
    
    private function insertInDB()
    {
        $dbCnx = ClaroLine::getDatabase();
        $sql = "
                INSERT INTO `".SurveyConstants::$ANSWER_TBL."`
                SET `participationId`       = ".(int)$this->participationId.",
                    `surveyLineId`          = ".(int) $this->surveyLineId.", 
                    `comment`               = ".$dbCnx->quote($this->comment).";  ";
            
        $dbCnx->exec($sql);
        $insertedId = $dbCnx->insertId();
        $this->id = $insertedId;
    }
    
    private function updateInDB()
    {
        $dbCnx = ClaroLine::getDatabase();
        $sql = "
                UPDATE `".SurveyConstants::$ANSWER_TBL."`
                SET    `comment`            = ".$dbCnx->quote($this->comment)."
                WHERE `id`                  = ".(int)$this->id.";  ";
        
        $dbCnx->exec($sql);
    }
    
    public function getParticipation()
    {
        if(empty($this->participation))
        {
            $this->loadParticipation();
        }
        return $this->participation;
    }
    
    private function loadParticipation(){
        $this->participation = Participation::load($this->participationId);
    }
    
    public function setParticipation($participation)
    {
        $this->participation = $participation;
        $this->participationId = $participation->id;
    }
    
    public function getQuestionLine(){
        if(empty($this->questionLine))
        {
            $this->loadQuestionLine();
        }
        return $this->questionLine;
    }
    
    private function loadQuestionLine()
    {
        $survey = $this->getParticipation()->getSurvey();
        $surveyLineList = $survey->getSurveyLineList();
        $this->setQuestionLine($surveyLineList[$this->surveyLineId]);
    }
    
    public function setQuestionLine($questionLine)
    {
        $this->questionLine = $questionLine;
        $this->surveyLineId = $questionLine->id;
    }
    
    public function getSurveyLineId()
    {
        return $this->surveyLineId;
    }
    
    public function getSelectedChoiceList()
    {
        if(empty($this->selectedChoiceList))
        {
            $this->loadSelectedChoiceList();
        }
        return $this->selectedChoiceList;
    }
    
    private function loadSelectedChoiceList()
    {
        $dbCnx = Claroline::getDatabase();
        $sql = "
            SELECT  AI.`id`         AS id, 
                    AI.`choiceId`   AS choiceId, 
                    AI.`answerId`   AS answerId
            FROM    `".SurveyConstants::$ANSWER_ITEM_TBL."` AI 
            WHERE   AI.`answerId` = ".(int)$this->id."; ";
        
        $resultSet = $dbCnx->query($sql);
        
        $question = $this->getQuestionLine()->question;
        
        $this->selectedChoiceList = array();
        foreach( $resultSet as $row )
        {
            $this->selectedChoiceList[] = $question->getChoice($row['choiceId']);
        }
    }
    
    public function getSelectedOptionList()
    {
        if(empty($this->selectedOptionList))
        {
            $this->loadSelectedOptionList();
        }
        return $this->selectedOptionList;
    }
    
    private function loadSelectedOptionList()
    {
        $dbCnx = Claroline::getDatabase();
        $sql = "
            SELECT  AI.`id`         AS id, 
                    AI.`choiceId`   AS choiceId, 
                    AI.`answerId`   AS answerId, 
                    AI.`optionId`  AS optionId 
            FROM    `".SurveyConstants::$ANSWER_ITEM_TBL."` AI 
            WHERE   AI.`answerId` = ".(int)$this->id."; ";
        
        $resultSet = $dbCnx->query($sql);
        $question = $this->getQuestionLine()->question;
        $this->selectedChoiceList = array();
        
        foreach( $resultSet as $row )
        {
            $choice = $question->getChoice($row['choiceId']);
            $this->selectedOptionList[] = $choice->getOption($row['optionId']);
        }
    }
    
    public function delete()
    {
        if($this->id == -1)return;
        $dbCnx = Claroline::getDatabase();
        $this->deleteOldChoices();
        $sql = "
            DELETE FROM `".SurveyConstants::$ANSWER_TBL."`
            WHERE       `id` = ".(int) $this->id."; "; 
        $dbCnx->exec($sql);
    }
    
    public function isValid()
    {
        $questionLine = $this->getQuestionLine();
        
        if(!$questionLine->isRequired())
        {
            return true;
        }
        
        if($questionLine->question->type == 'OPEN' )
        {
            $selectedChoiceList = $this->getSelectedChoiceList();
            $answerText = $selectedChoiceList[0]->text;
            if(trim($answerText) == '') return false;
        }
        
        if($questionLine->question->type == 'ARRAY' )
        {
            $expectedChoiceCount = sizeof($questionLine->question->getChoiceList());
            $selectedOptionList = $this->getSelectedOptionList();
            if(sizeof($selectedOptionList) != $expectedChoiceCount) return false;
        }
        
        if( $questionLine->question->type == 'MCSA' )
        {
            $selectedChoiceList = $this->getSelectedChoiceList();
            if(empty($selectedChoiceList))return false;
        }
        
        //NOTHING TO CHECK FOR MCMA
        return true;
    }
}