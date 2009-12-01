<?php
class SurveyLine
{
	public $id;
	
	public $survey;
	
	public $question;
	
	public $rank;
	
	public $maxCommentSize;
	
	public function __construct($survey, $question)
	{
		$this->id = -1;
		$this->survey = $survey;
		$this->question = $question;
		
		$this->rank = -1;
		$this->maxCommentSize = $survey->maxCommentSize;	
	}
	
	static function __set_state($array)
    {
    	if(empty($array)) return false;
    	static $properties = array();
    	if(empty($properties))
    	{
    		$properties = array_keys(get_object_vars(new SurveyLine(new Survey(''),NULL,0)));
    	}    	
    	$res = new SurveyLine($array['survey'], $array['question']);
        foreach ($array as $akey => $aval) {
            if(in_array($akey,$properties))
            {
            	$res -> {$akey} = $aval;
            }
        }
        return $res;
    }
	public function save()
    {
    	if($this->question->id == -1)
            throw new Exception("Cannot add unsaved question to survey");
        if($this->survey->id == -1)
            throw new Exception("Cannot add question to unsaved survey");
            
    	if($this->id == -1){
    		$this->insertInDB();
    		return;
    	}    	
    	$this->updateInDB();
    }
    
    
    private function insertInDB()
    {
    	$dbCnx = ClaroLine::getDatabase();
        //add a relation survey-question
        $sqlInsertRel = "
        	INSERT INTO 	`".SurveyConstants::$REL_SURV_QUEST_TBL."`
            SET 			`surveyId` 		= ".(int) $this->survey->id.",
                    		`questionId` 	= ".(int) $this->question->id.",
                    		`maxCommentSize` = ".(int) $this->maxCommentSize." ; ";
        // execute the creation query and get id of inserted assignment
        $dbCnx->exec($sqlInsertRel);
        
    	$insertedId = $dbCnx->insertId();        
  		$this->id = $insertedId;
  		$this->rank = $insertedId;
    	
    	//don't forget rank
        $sqlUpdateRank = "
        	UPDATE 	`".SurveyConstants::$REL_SURV_QUEST_TBL."`
            SET 	`rank` 	= ".(int) $insertedId."
        	WHERE 	`id` 	= ".(int) $insertedId;
        
    	$dbCnx->exec($sqlUpdateRank);
    }
    private function updateInDB()
    {
    	$dbCnx = ClaroLine::getDatabase();
        //add a relation survey-question
        $sqlUpdate = "
        	UPDATE 			`".SurveyConstants::$REL_SURV_QUEST_TBL."`
            SET 			`surveyId` 			= ".(int) $this->survey->id.",
                    		`questionId` 		= ".(int) $this->question->id.",
                    		`maxCommentSize` 	= ".(int) $this->maxCommentSize.", 
                    		`rank`				= ".(int) $this->rank." 
            WHERE 			`id`                = ".(int) $this->id." ; ";
        // execute the creation query and get id of inserted assignment
        $dbCnx->exec($sqlUpdate);
    }

    
    public function delete()
    {
    	if($this->question->id == -1)
            throw new Exception("Cannot remove unsaved question ");
        if($this->survey->id == -1)
            throw new Exception("Cannot remove question from unsaved survey");
            
        $dbCnx = ClaroLine::getDatabase();
        $answerIdList = $this->getLinkedAnswerIdList($dbCnx);
        
        if(!empty($answerIdList))
        {
        	$this->deleteLinkedAnswerItems($answerIdList,$dbCnx);
        	$this->deleteLinkedAnswers($answerIdList,$dbCnx);
        }        
        
        $sqlRemoveRel = "
        	DELETE FROM 	`".SurveyConstants::$REL_SURV_QUEST_TBL."`
            WHERE 			`surveyId` 		= ".(int) $this->survey->id." 
            AND        		`questionId` 	= ".(int) $this->question->id."; ";
        $dbCnx->exec($sqlRemoveRel);
        
    }
    
    private function getLinkedAnswerIdList($dbCnx)
    {
    	$sqlLinkedAnswers = "
    		SELECT 	DISTINCT	A.`id`			AS answerId
    		FROM				`".SurveyConstants::$ANSWER_TBL."` A 
    		INNER JOIN 			`".SurveyConstants::$PARTICIPATION_TBL."` P 
    		ON					A.`participationId` = P.`id` 
    		WHERE 				P.`surveyId` = ".(int)$this->survey->id." 
    		AND 				A.`questionId` = ".(int)$this->question->id."  ; ";
    	
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
    
	public function move($up)
    {
        $dbCnx = Claroline::getDatabase();

        //exchange rank with 
        $sqlSubSelect= "
        		SELECT	`rank`
                FROM 	`".SurveyConstants::$REL_SURV_QUEST_TBL."`
                WHERE 	`surveyId` 		= ".(int) $this->survey->id."
                AND  	`questionId` 	= ".(int) $this->question->id." ";
        $sqlSelect = "
        		SELECT
                  			`id`,
                  			`rank`
                FROM 		`".SurveyConstants::$REL_SURV_QUEST_TBL."`
                WHERE 		`surveyId` = '".$this->survey->id."'
                AND 		`rank` ".($up?"<=":">=")." (".$sqlSubSelect.")
                ORDER BY	`rank` ".($up?"DESC":"ASC")." LIMIT 2";       
         
        
        $resultSet = $dbCnx->query($sqlSelect);
        if ( $resultSet->count() < 2)
        	throw new Exception ("Cannot move this question in this survey");
        $ranks = array();
        foreach($resultSet as $row)
        {
        	$ranks[] = $row;
        }    
        	
        $sqlUpdateQ1 = "
        			UPDATE `" . SurveyConstants::$REL_SURV_QUEST_TBL."`
                    SET `rank` = " . (int) $ranks[1]['rank'] . "
                    WHERE `id` = " . (int) $ranks[0]['id'] . " ; ";
        $dbCnx->exec($sqlUpdateQ1);
        $sqlUpdateQ2 = "
        			UPDATE `" . SurveyConstants::$REL_SURV_QUEST_TBL . "` 
                    SET `rank` = " . (int) $ranks[0]['rank'] . " 
                    WHERE `id` = " . (int) $ranks[1]['id'] . " ; ";
    	$dbCnx->exec($sqlUpdateQ2);
    }
   
}