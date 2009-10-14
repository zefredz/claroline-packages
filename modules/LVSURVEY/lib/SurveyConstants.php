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
    public static $ANSWER_CHOICE_TBL;
    
    //table for text answers 
    //public static $ANSWER_TEXT_TBL;
	
    //table which contains relation between users and surveys
	public static $REL_SURV_USER_TBL;
	
	
	static function __init()
	{
		$tbl = claro_sql_get_tbl(
		             array('survey2_survey', 
		                   'survey2_question',
		            	   'survey2_choice', 
		                   'survey2_rel_survey_question', 
		                   'survey2_answer_choice', 
		                   //'survey2_answer_text', 
		                   'survey2_rel_survey_user'
		                 )
		             );
		SurveyConstants::$SURVEY_TBL = $tbl['survey2_survey']; 
		SurveyConstants::$QUESTION_TBL = $tbl['survey2_question']; 
		SurveyConstants::$CHOICE_TBL = $tbl['survey2_choice']; 
		SurveyConstants::$REL_SURV_QUEST_TBL = $tbl['survey2_rel_survey_question'];
		SurveyConstants::$ANSWER_CHOICE_TBL = $tbl['survey2_answer_choice'];
		//SurveyConstants::$ANSWER_TEXT_TBL = $tbl['survey2_answer_text'];
		SurveyConstants::$REL_SURV_USER_TBL = $tbl['survey2_rel_survey_user'];
	}
}

SurveyConstants::__init();

?>