<?php
From::module('LVSURVEY')->uses('controller/surveyLessPage.class');


abstract class ManagerSurveyLessPage extends SurveyLessPage{
	
	protected function checkAcess(){
		if( !parent::checkAccess())
        {
            return false;
        }
		if(!claro_is_allowed_to_edit())
		{
			return false;
		}
        return true;
	}
}