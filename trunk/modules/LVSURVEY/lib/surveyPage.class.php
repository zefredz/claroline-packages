<?php
From::module('LVSURVEY')->uses('survey.class');

abstract class SurveyPage{
	
	private $survey;
	
	private $flash;
	
	private static $DEFAULT_ERROR_MESSAGE;
	private static $DEFAULT_SUCCES_MESSAGE;
	
	
	public function __construct(){
		$this->assertSecurityAccess();
		$this->flash = new DialogBox();
		SurveyPage::$DEFAULT_ERROR_MESSAGE = get_lang('Error');
		SurveyPage::$DEFAULT_SUCCES_MESSAGE = get_lang('Success');	
		
		$this->init();
		try{			
			$survey = $this->loadSurvey();
		}catch(Exception $e){
			$this->errorAndDie($e->getMessage());
		} 
	}
	
	public function execute(){
		
		$this->performCommandIfNeeded();
		
		$contents = $this->render();
		$this->adddSurveyListToBreadCrumb();
		$this->adddSurveyToBreadCrumb();
		$this->addSpecificBreadCrumb();
		

		$this->display($contents);
	}
	protected function getSurvey(){
		return $this->survey;
	}
	
	protected abstract function render();
	protected abstract function addSpecificBreadCrumb();
	
	protected function errorAndDie($message){
			$dialogBox = new DialogBox();
			$dialogBox->error( $message);
			$contents = $dialogBox->render();
			$pageTtitle = get_lang('Error');
			$this->adddSurveyListToBreadCrumb();
			$this->appendBreadCrumbElement(get_lang("Error"));
			$this->display($contents);
			die();
	}
	private function adddSurveyListToBreadCrumb(){
		$this->appendBreadCrumbElement(get_lang('Surveys'), 'survey_list.php');	
	}
	
	private function adddSurveyToBreadCrumb(){
		$surveyTitle = htmlspecialchars($this->survey->title);
		$url = "show_survey.php?surveyId={$this->survey->id}";
		$this->appendBreadCrumbElement($surveyTitle, $url); 
	
	}
	protected function appendBreadCrumbElement($name,$url = null, $icon = null){
		$claroline = Claroline::getInstance();	
    	$claroline->display->banner->breadcrumbs->append($name,$url,$icon);
	}
	protected function performCommandIfNeeded(){
		if(!(isset($_REQUEST['cmd'])))
			return;
		$methodName = 'perform' .ucwords(trim($_REQUEST['cmd']));		
		if(method_exists($this,$methodName))
			$this->{$methodName}();
		
	}
	
	protected function display($contents){
		$claroline = Claroline::getInstance();
		$claroline->display->body->appendContent($this->flash->render());
		$claroline->display->body->appendContent($contents);
		echo $claroline->display->render();
	}
	
	protected function assertSecurityAccess(){
	if ( 	!claro_is_in_a_course() 
		|| !claro_is_course_allowed() 
		|| !claro_is_user_authenticated() )
		{
			claro_disp_auth_form(true);
		}
	}
	protected function init(){
		$tlabelReq = 'LVSURVEY';
		add_module_lang_array($tlabelReq);
		claro_set_display_mode_available(true);
	}
	protected function loadSurvey(){
			$surveyId = (int)$_REQUEST['surveyId'];
			$this->survey = Survey::load($surveyId);
	}
	
	protected function error($message = 'Error', $var_to_replace=null ){
		$this->flash->error(get_lang($message, $var_to_replace));
	}
	protected function success($message =  'Success', $var_to_replace=null ){
		$this->flash->success(get_lang($message, $var_to_replace));
	}
	protected function info($message, $var_to_replace=null){
		$this->flash->info(get_lang($message, $var_to_replace));
	}
}