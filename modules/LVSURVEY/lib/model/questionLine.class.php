<?php
From::module('LVSURVEY')->uses(	'util/surveyConstants.class', 
								'model/surveyLine.class');

class QuestionLine extends SurveyLine{
	
	public $question;	
	
	public $maxCommentSize;

    private $required;
	
	public function __construct($survey, $question)
	{
		parent::__construct($survey);
		$this->question = $question;
		$this->maxCommentSize = $survey->maxCommentSize;
        $this->required = true;
	}
	
	static function __set_state($array)
    {
    	if(empty($array)) return false;
    	   	
    	$res = new QuestionLine($array['survey'], $array['question']);
        foreach ($array as $akey => $aval) 
        {
            $res -> {$akey} = $aval;
        }
        return $res;
    }
	public function checkConsistency()
	{
		parent::checkConsistency();
		if($this->question->id == -1)
            throw new Exception("Survey line (Question) not consistent : unsaved question");
	}

	protected function insertConcreteLine()
	{
		$dbCnx = ClaroLine::getDatabase();
        //add a relation survey-question
        $sqlInsertRel = "
        	INSERT INTO 	`".SurveyConstants::$SURVEY_LINE_QUESTION_TBL."`
            SET 			`id`	 		= ".(int) $this->id.",
                    		`questionId` 	= ".(int) $this->question->id.",
                            `required` 	= ".(int) $this->required.",
                    		`maxCommentSize` = ".(int) $this->maxCommentSize." ; ";
        // execute the creation query and get id of inserted assignment
        $dbCnx->exec($sqlInsertRel);        
    	
	}
	
	protected function updateConcreteLine()
	{
		$dbCnx = ClaroLine::getDatabase();
        //add a relation survey-question
        $sqlUpdate = "
        	UPDATE 			`".SurveyConstants::$SURVEY_LINE_QUESTION_TBL."`
            SET       		`questionId` 		= ".(int) $this->question->id.",
                            `required`          = ".(int) $this->required.",
                    		`maxCommentSize` 	= ".(int) $this->maxCommentSize." 
            WHERE 			`id`                = ".(int) $this->id." ; ";
        // execute the creation query and get id of inserted assignment
        $dbCnx->exec($sqlUpdate);
	}
	protected function deleteConcreteLine()
	{
		$dbCnx = ClaroLine::getDatabase(); 
        $this->deleteLinkedAnswers($dbCnx);
        $sql = "
	        		DELETE FROM `".SurveyConstants::$SURVEY_LINE_QUESTION_TBL."`
	        		WHERE 			`id`                = ".(int) $this->id." ; ";
	    $dbCnx->exec($sql);
	}
	private function deleteLinkedAnswers($dbCnx)
	{
		$sql = "
	        		DELETE 		A, AI 
	        		FROM 		`".SurveyConstants::$ANSWER_TBL."` AS A 
	        		INNER JOIN 	`".SurveyConstants::$ANSWER_ITEM_TBL."` AS AI 
	        		ON 			A.`id` = AI.`answerId`
	        		WHERE 		A.`surveyLineId` = ".(int) $this->id." ; ";
	    

	    if($this->question->type == 'OPEN')
	    {
	    	$sql = "
	        		DELETE 		A, AI, C 
	        		FROM 		`".SurveyConstants::$ANSWER_TBL."` AS A 
	        		INNER JOIN 	`".SurveyConstants::$ANSWER_ITEM_TBL."` AS AI 
	        		ON 			A.`id` = AI.`answerId`
	        		INNER JOIN 	`".SurveyConstants::$CHOICE_TBL."` AS C 
	        		ON 			AI.`choiceId` = C.`id`
	        		WHERE 		A.`surveyLineId` = ".(int) $this->id." ; ";
	    }
	    
	    $dbCnx->exec($sql);
	}
	public function render($editMode, $participation, $allowChange = true)
	{
		$questionLineTpl = new PhpTemplate(get_module_path('LVSURVEY').'/templates/questionLine.tpl.php');
    	$questionLineTpl->assign('surveyLine', $this);
		$questionLineTpl->assign('participation', $participation);
    	$questionLineTpl->assign('editMode', $editMode);
        $questionLineTpl->assign('allowChange', $allowChange);
    	return $questionLineTpl->render();
	}

    public function isRequired()
    {
        return $this->required;
    }

    public function setRequired($required)
    {
        $this->required = $required;
    }
	
	
}
