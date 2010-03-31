<?php
From::module('LVSURVEY')->uses('surveyLessPage.class');


abstract class ManagerSurveyLessPage extends SurveyLessPage{
	
	protected function assertSecurityAccess(){
		parent::assertSecurityAccess();
		if(!claro_is_allowed_to_edit())
		{
			//not allowed for normal user
		    parent::errorAndDie('Access denied');
		}
	}
}