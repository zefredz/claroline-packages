<?php



class SurveyConstants{
	//table which contains surveys
    public static $SURVEY_TBL;
    
    //table which contains surveys
    public static $QUESTION_TBL;
    
    //table which contains Answers to Questions
    public static $CHOICE_TBL;
    
    //table which contains data about survey lines (like rank position)
    public static $SURVEY_LINE_TBL;
    
    //table which contains data about question survey lines 
    public static $SURVEY_LINE_QUESTION_TBL;
    
    //table which contains data about separator survey lines
    public static $SURVEY_LINE_SEPARATOR_TBL;    
    
    //table for answers from a user to a question 
    public static $ANSWER_TBL;
    
    //table for answer item : the selected choice(s) from a user to a question 
    public static $ANSWER_ITEM_TBL;
	
    //table which contains relation between users and surveys
	public static $PARTICIPATION_TBL;
	
	//table for different option of a choice, used for array questions
	public static $OPTION_TBL;
	
	
	
	
	static function __init()
	{		
		$tbl = claro_sql_get_tbl(
		             array('survey2_survey', 
		                   'survey2_question',
		            	   'survey2_choice', 
		                   'survey2_survey_line',
		             	   'survey2_survey_line_question',
		                   'survey2_survey_line_separator', 
		                   'survey2_answer_item', 
		                   'survey2_answer', 
		                   'survey2_participation', 
		             	   'survey2_option'
		                 )
		             );
		SurveyConstants::$SURVEY_TBL = $tbl['survey2_survey']; 
		SurveyConstants::$QUESTION_TBL = $tbl['survey2_question']; 
		SurveyConstants::$CHOICE_TBL = $tbl['survey2_choice']; 
		SurveyConstants::$SURVEY_LINE_TBL = $tbl['survey2_survey_line'];
		SurveyConstants::$SURVEY_LINE_QUESTION_TBL = $tbl['survey2_survey_line_question'];
		SurveyConstants::$SURVEY_LINE_SEPARATOR_TBL = $tbl['survey2_survey_line_separator'];
		SurveyConstants::$ANSWER_TBL = $tbl['survey2_answer'];
		SurveyConstants::$ANSWER_ITEM_TBL = $tbl['survey2_answer_item'];
		SurveyConstants::$PARTICIPATION_TBL = $tbl['survey2_participation'];
		SurveyConstants::$OPTION_TBL = $tbl['survey2_option'];
	}
}

SurveyConstants::__init();

?>