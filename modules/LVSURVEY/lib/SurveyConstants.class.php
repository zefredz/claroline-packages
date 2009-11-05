<?php



class SurveyConstants{
	//table which contains surveys
    public static $SURVEY_TBL;
    
    //table which contains surveys
    public static $QUESTION_TBL;
    
    //table which contains Answers to Questions
    public static $CHOICE_TBL;
    
    //table which contains relation between questions and surveys
    public static $REL_SURV_QUEST_TBL;
    
    //table for multiple choice answers 
    public static $ANSWER_TBL;
    
    //table for comment 
    public static $ANSWER_ITEM_TBL;
	
    //table which contains relation between users and surveys
	public static $PARTICIPATION_TBL;
	
	
	static function __init()
	{		
		$tbl = claro_sql_get_tbl(
		             array('survey2_survey', 
		                   'survey2_question',
		            	   'survey2_choice', 
		                   'survey2_rel_survey_question', 
		                   'survey2_answer_item', 
		                   'survey2_answer', 
		                   'survey2_participation'
		                 )
		             );
		SurveyConstants::$SURVEY_TBL = $tbl['survey2_survey']; 
		SurveyConstants::$QUESTION_TBL = $tbl['survey2_question']; 
		SurveyConstants::$CHOICE_TBL = $tbl['survey2_choice']; 
		SurveyConstants::$REL_SURV_QUEST_TBL = $tbl['survey2_rel_survey_question'];
		SurveyConstants::$ANSWER_TBL = $tbl['survey2_answer'];
		SurveyConstants::$ANSWER_ITEM_TBL = $tbl['survey2_answer_item'];
		SurveyConstants::$PARTICIPATION_TBL = $tbl['survey2_participation'];
	}
}

SurveyConstants::__init();

?>