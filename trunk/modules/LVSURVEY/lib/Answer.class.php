<?php

From::module('LVSURVEY')->uses('SurveyConstants.class', 'Question.class', 'Choice.class');

class Answer
{
	public $id;
	
	protected $participationId;
	protected $participation;
	
	protected $questionId;
	protected $question;
	
	public $comment;
	
	protected $selectedChoiceList;
	
	public function __construct($participationId, $questionId)
	{
		$this->id = -1;
		$this->participationId = $participationId;
		$this->participation = NULL;
		$this->questionId = $questionId;
		$this->question = NULL;
		$this->comment = '';
		$this->selectedChoiceList = array();	
	}
	
	static function __set_state($array)
    {
    	if(empty($array)) return false;
    	static $properties = array();
    	if(empty($properties))
    	{
    		$properties = array_keys(get_object_vars(new Answer(-1,-1)));
    	}
    	
    	$res = new Answer($array['participationId'], $array['questionId']);
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
            	       A.`id` 							AS id,
            	       A.`participationId`				AS participationId,
            	       A.`questionId`					AS questionId,
            	       A.`comment`						AS comment
           	FROM 		`".SurveyConstants::$ANSWER_TBL."` A
           	WHERE 		`id` = ".(int) $id."; "; 
         
         
        $resultSet = $dbCnx->query($sql);
        $data = $resultSet->fetch();
        return self::__set_state($data);
    }
    
    static function loadAnswerOfQuestionFromForm($question)
    {
    	$questionId = $question->id;
    	$userInput = Claro_UserInput::getInstance();    	
    	
    	try
    	{
	    	$participationId = (int)$userInput->getMandatory('participationId');
    		$formAnswerId = (int)$userInput->getMandatory('answerId'.$questionId);	    	
    	}
    	catch(Claro_Validator_Exception $e)
    	{
    		throw new Claro_Validator_Exception(get_lang('You have forgotten to fill a mandatory field'));
    	}    	
    	
		if($formAnswerId == -1 )
		{			
			$answer = new Answer($participationId, $questionId);			
		}
		else 
		{
			$answer = self::load($formAnswerId);	
		}	
		
		$answer->comment = $userInput->get('answerComment'.$questionId, '');
		$answer->comment = substr(trim($answer->comment),0,$question->maxCommentSize);
		
		$answer->selectedChoiceList = Choice::loadSelectedChoicesFromForm($question);
		
		
		return $answer;
    }
	public function save()
    {
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
        $question = $this->getQuestion();
        if('OPEN' == $question->type)
        {
        	$sqlSubselect = "
        		SELECT `choiceId` 
        		FROM `".SurveyConstants::$ANSWER_ITEM_TBL."` 
        		 WHERE `answerId` = ". (int) $this->id." ";
        	$sql = "
        		DELETE FROM `".SurveyConstants::$CHOICE_TBL."` 
        		WHERE `id` IN ( ".$sqlSubselect."); ";
        	$dbCnx->exec($sql);
        }
        
        $sql = "
        		DELETE FROM `".SurveyConstants::$ANSWER_ITEM_TBL."`
                WHERE `answerId` = ". (int) $this->id . "; ";			
        $dbCnx->exec($sql);
    }
    private function saveNewChoices()
    {
    	$dbCnx = Claroline::getDatabase(); 
    	$question = $this->getQuestion();
    	if('OPEN' == $question->type)
        {
        	$this->selectedChoiceList[0]->save();
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
                SET `participationId`		= ".(int)$this->participationId.",
                	`questionId` 			= ".(int) $this->questionId.", 
                	`comment`				= ".$dbCnx->quote($this->comment).";  ";
			
        $dbCnx->exec($sql);
        $insertedId = $dbCnx->insertId();
        $this->id = $insertedId;      
        

    }
    private function updateInDB()
    {
    	$dbCnx = ClaroLine::getDatabase();    	
        $sql = "
        		UPDATE `".SurveyConstants::$ANSWER_TBL."`
                SET    `comment`			= ".$dbCnx->quote($this->comment)."
                WHERE `id`					= ".(int)$this->id.";  ";
			
        $dbCnx->exec($sql);
    }
    
    public function getParticipation()
    {
    	if(empty($this->participation))
    	{
    		$this->loadParticipation;
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
    public function getQuestionId()
    {
    	return $this->questionId;
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
    		SELECT	AI.`id`			AS id, 
    				AI.`choiceId`	AS choiceId, 
    				AI.`answerId`	AS answerId
    		FROM	`".SurveyConstants::$ANSWER_ITEM_TBL."` AI 
    		WHERE	AI.`answerId` = ".(int)$this->id."; ";
    	
    	$resultSet = $dbCnx->query($sql);
    	
    	$choiceList = $this->getQuestion()->getChoiceList();

    	
        $this->selectedChoiceList = array();	    
	    foreach( $resultSet as $row )
	    {
            $this->selectedChoiceList[$row['choiceId']] = $choiceList[$row['choiceId']];
	    } 
    }
    public function delete()
    {
    	if($this->id == -1)return;
    	$dbCnx = Claroline::getDatabase();
    	$this->deleteOldChoices();
    	$sql = "
    		DELETE FROM `".SurveyConstants::$ANSWER_TBL."`
    		WHERE 		`id` = ".(int) $this->id."; "; 
    	$dbCnx->exec($sql);
    } 
}
