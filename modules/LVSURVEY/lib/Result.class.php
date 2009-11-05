<?php
class Result
{
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

	static function loadResults($surveyId, $questionId, $choiceId)
    {
    	$mainTableList = claro_sql_get_main_tbl();
		$userTable = $mainTableList['user'];
		
    	$sql = "
        	SELECT 		U.`nom` 			as firstName,
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
        	ON 			P.`userId` = U.`user_id`        	 
        	WHERE 		P.`surveyId`		= ".(int)$surveyId."  
        	AND			A.`questionId` 		= ".(int)$questionId." 
        	AND			AI.`choiceId`  		= ".(int)$choiceId." ;";
    	
    	
    	$resultSet = Claroline::getDatabase()->query($sql);
    	$res = array();
    	foreach($resultSet as $row)
    	{
    		$result = self::__set_state($row);
    		$res[] = $result;
    	}
		
    	return $res;
	    
    }
	
	
}