<?php
From::module('LVSURVEY')->uses('surveyLessPage.class', 'survey.class');

abstract class SurveyPage extends SurveyLessPage{
	
	protected $survey;
	
	
	public function __construct(){
		parent::__construct();
		try{			
			$survey = $this->loadSurvey();
		}catch(Exception $e){
			$this->errorAndDie($e->getMessage());
		} 
	}
	
	protected function setSurvey($survey)
	{
		$this->survey = $survey;
	}
	protected function getSurvey(){
		return $this->survey;
	}
	

	protected function defineBreadCrumb(){
		parent::defineBreadCrumb();
		$surveyTitle = htmlspecialchars($this->survey->title);
		$url = "show_survey.php?surveyId={$this->survey->id}";
		$this->appendBreadCrumbElement($surveyTitle, $url); 
	}


	protected function loadSurvey(){
			$input = Claro_UserInput::getInstance();
			$idValidator = new Claro_Validator_ValueType('intstr');
			$input->setValidator('surveyId', $idValidator);
			$surveyId = $input->getMandatory('surveyId');
			$this->survey = Survey::load($surveyId);
	}	

}