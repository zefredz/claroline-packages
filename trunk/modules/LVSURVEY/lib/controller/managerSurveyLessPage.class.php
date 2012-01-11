<?php
From::module('LVSURVEY')->uses('controller/surveyLessPage.class');

abstract class ManagerSurveyLessPage extends SurveyLessPage
{
    protected function checkAccess()
    {
        if ( ! claro_is_user_authenticated() )
        {
            claro_disp_auth_form( true );
        }
        
        if( ! parent::checkAccess() )
        {
            return false;
        }
        
        if( ! claro_is_allowed_to_edit() )
        {
            return false;
        }
        
        return true;
    }
}