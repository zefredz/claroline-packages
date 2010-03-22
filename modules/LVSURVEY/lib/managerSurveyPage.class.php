<?php
From::module('LVSURVEY')->uses('surveyPage.class');


abstract class ManagerSurveyPage extends SurveyPage{
	
	protected function assertSecurityAccess(){
		parent::assertSecurityAccess();
		if(!claro_is_allowed_to_edit())
		{
			//not allowed for normal user
		    parent::errorAndDie('Access denied');
		}
	}
}