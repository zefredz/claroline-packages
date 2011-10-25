<?php
class Result
{
	
	public $surveyId;
	
	public $surveyLineId;
	
	public $choiceId;
	
	public $optionId;
	
	public $userId;
	public $firstName;
	public $lastName;
	
	public $comment;
	
	static function __set_state($array)
        {
            if(empty($array)) return false;            

            $res = new Result();
            foreach ($array as $akey => $aval) 
            {                
                $res -> {$akey} = $aval;
            }
            return $res;
        }
}

class OptionResults
{
	public $resultList = array();
}
class ChoiceResults
{
	public $resultList = array();
	public $optionResultList = array();
}
class LineResults
{
	public $resultList = array();
	public $choiceResultList = array();	
}
class SurveyResults
{
	public $lineResultList = array();
	
/* return an array like $result[$questionId][$choiceId][$userId] = aResult */
	static function loadResults($surveyId, $surveyLineId = NULL, $choiceId = NULL)
    {
    	$mainTableList = claro_sql_get_main_tbl();
		$userTable = $mainTableList['user'];
		
    	$sql = "
        	SELECT 		P.`surveyId`		as surveyId, 
        				A.`surveyLineId`	as surveyLineId, 
        				AI.`choiceId`		as choiceId,
        				AI.`optionId`		as optionId,  
        				U.`user_id`			as userId, 
        				U.`nom` 			as firstName,
        				U.`prenom` 			as lastName,
        				A.`comment`			as comment  
        	FROM 		`".SurveyConstants::$CHOICE_TBL."` as C 
        	INNER JOIN `".SurveyConstants::$ANSWER_ITEM_TBL."` as AI
        	ON 			AI.`choiceId` 		= C.`id` 
        	INNER JOIN `".SurveyConstants::$ANSWER_TBL."` as A
        	ON 			AI.`answerId` 		= A.`id` 
        	INNER JOIN `".SurveyConstants::$PARTICIPATION_TBL."` as P
        	ON 			A.`participationId` = P.`id`
        	INNER JOIN  `".$userTable."` as U 
        	ON 			P.`userId` 			= U.`user_id`        	 
        	WHERE 		P.`surveyId`		= ".(int)$surveyId."
     		";
    		if ($surveyLineId != NULL)
    		{
    			$sql .= "AND			A.`surveyLineId` 	= ".(int)$surveyLineId."
    			";
    		}
    		if ($choiceId != NULL)
    		{
    			$sql .= "AND			AI.`choiceId`  		= ".(int)$choiceId."
    			";
    		}
        	$sql .= " 	ORDER BY surveyId, questionId, choiceId, userId ;";
    	
    	$resultSet = Claroline::getDatabase()->query($sql);
    	$res = new SurveyResults();
    	foreach($resultSet as $row)
    	{
    		
    		$result = Result::__set_state($row);
    		if( !isset($res->lineResultList[$result->surveyLineId]))
    		{
    			$res->lineResultList[$result->surveyLineId] = new LineResults();
    		}
    		$questionResultList = $res->lineResultList[$result->surveyLineId];
    		
    		
    		if( !isset($questionResultList->choiceResultList[$result->choiceId]))
    		{
    			$questionResultList->choiceResultList[$result->choiceId] = new ChoiceResults();
    		}
    		$choiceResultList = $questionResultList->choiceResultList[$result->choiceId];
    		
    		
    		
    		if (!is_null($result->optionId))
    		{
	    		if( !isset($choiceResultList->optionResultList[$result->optionId]))
	    		{
	    			$choiceResultList->optionResultList[$result->optionId] = new OptionResults();
	    		}
	    		$optionResultList = $choiceResultList->optionResultList[$result->optionId];
	    		$optionResultList->resultList[$result->userId] = $result;
    		}
    		
    		$choiceResultList->resultList[$result->userId] = $result;
    		$questionResultList->resultList[$result->userId] = $result;
    		
    		
    	}
		
    	return $res;
	    
    }
}	
	
