<?php
From::module('LVSURVEY')->uses( 'model/guest.class.php');

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
    public $predefinedValue;
    
    
    static function __set_state($array)
        {
            if(empty($array))
            {
                return false;
            }
            
            $res = new Result();
            
            foreach ($array as $akey => $aval) 
            {
                $res -> {$akey} = $aval;
            }
            
            if(is_null($res->userId))
            {
                $guest = new Guest();
                $res->userId = $guest->userId;
                $res->firstName = $guest->firstName;
                $res->lastName = $guest->lastName;
            }
            
            return $res;
        }
}

class OptionResults
{
    public $resultList = array();
}

class PredefinedResults
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
    public $predefinedResultList = array(); 
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
            SELECT      P.`surveyId`        as surveyId, 
                        A.`surveyLineId`    as surveyLineId, 
                        AI.`choiceId`       as choiceId,
                        AI.`optionId`       as optionId,  
                        U.`user_id`         as userId, 
                        U.`nom`             as lastName,
                        U.`prenom`          as firstName,
                        A.`comment`         as comment,
                        A.`predefined`      as predefinedValue
            FROM        `".SurveyConstants::$PARTICIPATION_TBL."` as P
            INNER JOIN `".SurveyConstants::$ANSWER_TBL."` as A
            ON          P.`id`              = A.`participationId` 
            LEFT JOIN  `".SurveyConstants::$ANSWER_ITEM_TBL."` as AI
            ON          A.`id`              = AI.`answerId`
            LEFT JOIN `".SurveyConstants::$CHOICE_TBL."` as C 
            ON          AI.`choiceId`       = C.`id` 
            LEFT JOIN  `".$userTable."` as U 
            ON          P.`userId`          = U.`user_id`            
            WHERE       P.`surveyId`        = ".(int)$surveyId."
            ";
        
        if ($surveyLineId != NULL)
        {
            $sql .= "AND            A.`surveyLineId`    = ".(int)$surveyLineId."
            ";
        }
        
        if ($choiceId != NULL)
        {
            $sql .= "AND            AI.`choiceId`       = ".(int)$choiceId."
            ";
        }
        
        $sql .= "   ORDER BY surveyId, questionId, choiceId, predefined, userId ;";
        
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
            
            if( !empty( $result->predefinedValue ) )
            {
                if( !isset($questionResultList->predefinedResultList[$result->predefinedValue]))
                {
                    $questionResultList->predefinedResultList[$result->predefinedValue] = new PredefinedResults();
                }
                $questionResultList->predefinedResultList[$result->predefinedValue]->resultList[] = $result;
            }
            
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
                $optionResultList->resultList[] = $result;
            }
            
            $choiceResultList->resultList[] = $result;
            $questionResultList->resultList[] = $result;
        }
        
        return $res;
    }
}