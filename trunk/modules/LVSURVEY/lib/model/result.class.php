<?php
class Result
{
	
	public $surveyId;
	
	public $surveyLineId;
	
	public $choiceId;
	
	public $userId;
	public $firstName;
	public $lastName;
	
	public $comment;
	
	static function __set_state($array)
    {
    	if(empty($array)) return false;
    	static $properties = array();
    	if(empty($properties))
    	{
    		$properties = array_keys(get_object_vars(new Result()));
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
}

class ChoiceResults
{
	public $resultList = array();
}
class LineResults
{
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
    		
    		$choiceResultList->resultList[$result->userId] = $result;
    		
    	}
		
    	return $res;
	    
    }
}	
	
