<?php
From::module('LVSURVEY')->uses('controller/surveyPage.class');


abstract class ManagerSurveyPage extends SurveyPage{
	
	protected function checkAccess()
    {
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