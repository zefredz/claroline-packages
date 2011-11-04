<?php
From::module('LVSURVEY')->uses('controller/surveyPage.class');


abstract class ManagerSurveyPage extends SurveyPage{
	
    protected function checkAccess()
    {
        if ( !claro_is_user_authenticated() )
        {
                claro_disp_auth_form(true);
        }
        
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