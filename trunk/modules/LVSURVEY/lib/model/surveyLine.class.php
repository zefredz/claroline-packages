<?php
From::module('LVSURVEY')->uses(	'util/surveyConstants.class', 
								'model/questionLine.class', 
								'model/separatorLine.class');
abstract class SurveyLine
{
    public static function cmp_surveyLines($a, $b)
    {
        return $a->rank - $b->rank;
    }



    public $id;

    public $survey;

    public $rank;

    public function __construct($survey)
    {
        $this->id = -1;
        $this->survey = $survey;
        $this->rank = -1;
    }


    public function checkConsistency()
    {
        if($this->survey->id == -1)
            throw new Exception("Survey line not consistent : unsaved survey");
    }
    public function save()
    {
        $this->checkConsistency();

        if($this->id == -1)
        {
            $this->insertInDB();
            return;
        }
        $this->updateInDB();
    }


    private function insertInDB()
    {
        $this->insertAbstractLine();
        $this->insertConcreteLine();
    }
    private function insertAbstractLine()
    {
        $dbCnx = ClaroLine::getDatabase();
        //add a relation survey-question
        $sqlInsertRel = "
        	INSERT INTO 	`".SurveyConstants::$SURVEY_LINE_TBL."`
            SET 			`surveyId` 		= ".(int) $this->survey->id." ; ";
        // execute the creation query and get id of inserted assignment
        $dbCnx->exec($sqlInsertRel);

        $insertedId = $dbCnx->insertId();
        $this->id = $insertedId;
        $this->rank = $insertedId;

        //don't forget rank
        $sqlUpdateRank = "
        	UPDATE 	`".SurveyConstants::$SURVEY_LINE_TBL."`
            SET 	`rank` 	= ".(int) $insertedId."
        	WHERE 	`id` 	= ".(int) $insertedId;

        $dbCnx->exec($sqlUpdateRank);
    }
    protected abstract function insertConcreteLine();


    private function updateInDB()
    {
        $this->updateAbstractLine();
        $this->updateConcreteLine();
    }

    private function updateAbstractLine()
    {
        $dbCnx = ClaroLine::getDatabase();
        //add a relation survey-question
        $sqlUpdate = "
        	UPDATE 			`".SurveyConstants::$SURVEY_LINE_TBL."`
            SET 			`surveyId` 			= ".(int) $this->survey->id.",  
                    		`rank`				= ".(int) $this->rank." 
            WHERE 			`id`                = ".(int) $this->id." ; ";
        // execute the creation query and get id of inserted assignment
        $dbCnx->exec($sqlUpdate);
    }

    abstract protected function updateConcreteLine();


    public function delete()
    {
        $this->checkConsistency();

        $this->deleteConcreteLine();
        $this->deleteAbstractLine();
    }
    private function deleteAbstractLine()
    {
        $dbCnx = ClaroLine::getDatabase();

        $sqlRemoveRel = "
        	DELETE FROM 	`".SurveyConstants::$SURVEY_LINE_TBL."`
            WHERE 			`id` 		= ".(int) $this->id." ; ";
        $dbCnx->exec($sqlRemoveRel);

    }
    abstract protected function deleteConcreteLine();


    public function move($up)
    {
        $dbCnx = Claroline::getDatabase();

        //exchange rank with
        $sqlSubSelect= "
        		SELECT	`rank` 
                FROM 	`".SurveyConstants::$SURVEY_LINE_TBL."`
                WHERE 	`id` 		= ".(int) $this->id." ";
        $sqlSelect = "
        		SELECT		`id`, 
        					`rank` 
                FROM 		`".SurveyConstants::$SURVEY_LINE_TBL."`
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

        //attention c'est parti pour le show
        // l'id�e est de cr�er une vue temporaire en faisant une
        // jointure sur soi-meme.
        // si on essaie de swapper le row [id = 5, rank = 4] avec
        // le row [id = 12, rank = 5]
        // il faut d'abord cr�er un resultset qui ressemble �
        //   id1    |     rank1      | id2    | rank2
        //--------------------------------------------
        //   5              4           12         5
        //
        // et ensuite de pratiquer un update sur ce resultset
        //   rank1 = rank2
        //   rank2 = rank1


        $sqlUpdate = "
        			UPDATE		`" . SurveyConstants::$SURVEY_LINE_TBL."` AS SL1
        			JOIN 		`" . SurveyConstants::$SURVEY_LINE_TBL."` AS SL2 	
        			SET			SL1.`rank` = SL2.`rank`, 
        						SL2.`rank` = SL1.`rank` 
        			WHERE		SL1.`id` = " . (int) $ranks[0]['id'] . "  
        			AND			SL2.`id` = " . (int) $ranks[1]['id'] . " ; ";
        $dbCnx->exec($sqlUpdate);

        $surveyLineList = $this->survey->getSurveyLineList();
        foreach($surveyLineList as $surveyLine)
        {
            if($surveyLine->id == $ranks[0]['id'])
                $surveyLine->rank = $ranks[1]['rank'];
            if($surveyLine->id == $ranks[1]['id'])
                $surveyLine->rank = $ranks[0]['rank'];

        }




    }

    abstract public function render($editMode, $participation);



}

class SurveyLineFactory
{
    public static function linesOfSurvey($survey)
    {
        return SurveyLineFactory::selectLinesWhere("SL.`surveyId` = ".(int)$survey->id, $survey);
    }
    public static function loadSingleLine($lineId, $survey)
    {
        $list =  SurveyLineFactory::selectLinesWhere("SL.`id` = ".(int)$lineId, $survey);
        return $list[$lineId];
    }


    public static function selectLinesWhere($where, $survey)
    {
        $dbCnx = Claroline::getDatabase();
        $sql = "
    			SELECT          SL.`id` 				as surveyLineId,
		    					SL.`rank`				as rank, 
		    					SLQ.`maxCommentSize` 	as maxCommentSize, 
		    					SLQ.`questionId`		as questionId, 
		    					Q.`text`				as questionText, 
		    					Q.`type`				as questionType,
		    					SLS.`title`				as separatorTitle,
		    					SLS.`description`		as separatorDescription, 
		    					IFNULL(SLS.`id`,'TRUE')	as isQuestionLine     					  
                FROM 	`".SurveyConstants::$SURVEY_LINE_TBL."` as SL
                LEFT JOIN `".SurveyConstants::$SURVEY_LINE_QUESTION_TBL."` as SLQ
                ON 		SL.`id` = SLQ.`id` 
                LEFT JOIN `".SurveyConstants::$SURVEY_LINE_SEPARATOR_TBL."` as SLS
                ON 		SL.`id` = SLS.`id` 
                LEFT JOIN `".SurveyConstants::$QUESTION_TBL."` as Q
                ON 		SLQ.`questionId` = Q.`id`                
                WHERE 	".$where."
                ORDER BY SL.`rank` ASC; ";


        $resultSet = $dbCnx->query($sql);

        $surveyLineList = array();
        foreach( $resultSet as $row )
        {
            if('TRUE' == $row['isQuestionLine'])
                $surveyLineList[$row['surveyLineId']] = self::buildQuestionLine($row,$survey);
            else
                $surveyLineList[$row['surveyLineId']] = self::buildSeparatorLine($row,$survey);
        }
        return $surveyLineList;
    }

    private static function buildQuestionLine($row,$survey)
    {
        $questionData = array(	'id' 		=> $row['questionId'],
                'text' 		=> $row['questionText'],
                'type' 		=> $row['questionType']);

        $question = Question::__set_state($questionData);

        $questionLineData = array(	'id' 				=> $row['surveyLineId'],
                'survey' 			=> $survey,
                'rank'				=> $row['rank'],
                'question' 			=> $question,
                'maxCommentSize' 	=> $row['maxCommentSize']);

        return QuestionLine::__set_state($questionLineData);
    }
    private static function buildSeparatorLine($row,$survey)
    {

        $separatorLineData = array(	'id' 				=> $row['surveyLineId'],
                'survey' 			=> $survey,
                'rank'				=> $row['rank'],
                'title' 			=> $row['separatorTitle'],
                'description' 		=> $row['separatorDescription']);

        return SeparatorLine::__set_state($separatorLineData);
    }


    public static function createQuestionLine($survey,$question)
    {
        return new QuestionLine($survey,$question);
    }
    public static function createSeparatorLine($survey,$title)
    {
        return new SeparatorLine($survey,$title);
    }


    public static function createSeparatorFromForm($survey)
    {
        $userInput = Claro_UserInput::getInstance();
        try
        {
            $formId = (string)$userInput->getMandatory('separatorId');
            $formTitle = (string)$userInput->getMandatory('separatorTitle');
        }
        catch(Claro_Validator_Exception $e)
        {
            throw new Claro_Validator_Exception(get_lang('You have forgotten to fill a mandatory field'));
        }
        if($formId == -1)
        {
            $separator = new SeparatorLine($survey, $formTitle);
        }
        else
        {
            $separator = SurveyLineFactory::loadSingleLine($formId,$survey);
        }
        $separator->setTitle($formTitle);
        $separator->setDescription($userInput->get('separatorDescription', ''));

        return $separator;
    }
}