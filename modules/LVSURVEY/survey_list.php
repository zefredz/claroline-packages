<?php 
global $tlabelReq;
$tlabelReq = 'LVSURVEY';

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';
From::module('LVSURVEY')->uses(	'controller/surveyLessPage.class', 
								'model/survey.class');

class SurveyListPage extends SurveyLessPage 
{
	private $editMode = false;
	private $showConfirmDelete = false;
	private $survey = null;
	
	public function __construct()
	{
		parent::__construct();
		$this->editMode = claro_is_allowed_to_edit();
	}
	
	private function getSurvey()
	{
		if($this->survey != null){
			return $this->survey;
		}
		$surveyId = parent::getUserInt('surveyId');
		$this->survey =  Survey::load($surveyId);
		return $this->survey;	
	}
	
	public function performToggleSurveyVisibility()
	{
		$survey = $this->getSurvey();
		$survey->is_visible = !$survey->is_visible;
		$survey->save();
	}
	
	public function performSurveyStart()
	{
		$survey = $this->getSurvey();
		$survey->startDate = time();
       	if($survey->endDate < time())
       	{
       		$nextMonth = strtotime( "+1 month" );
        	$survey->endDate = $nextMonth;
       	}
       	$survey->save();
	}
	
	public function performSurveyStop()
	{
		$survey = $this->getSurvey();
		$survey->endDate = time();
        if($survey->startDate> time()){
            $survey->startDate = 0;
        }
        $survey->save();
	}
	public function performSurveyMoveUp()
	{
		$survey = $this->getSurvey();
		$survey->moveSurvey(true);		
	}
	public function performSurveyMoveDown()
	{
		$survey = $this->getSurvey();
		$survey->moveSurvey(false);
	}
	public function performSurveyDelete()
	{
		if( ! parent::isConfirmed()){
			$this->showConfirmDelete = true;
			return;
		}
		$survey = $this->getSurvey();
		$survey->delete();		
	}
	
	public function render(){
		if($this->showConfirmDelete)
			return $this->displayDeleteConfirmation();
		
		return $this->displaySurveyList();
	}
	
	private function displayDeleteConfirmation()
	{		
		$survey = $this->getSurvey();
		$delConfTpl = new PhpTemplate(dirname(__FILE__).'/templates/delete_survey_conf.tpl.php');
    	$delConfTpl->assign('survey', $survey);        
    	$form = $delConfTpl->render();
    	
    	$dialogBox = new DialogBox();	
		if($survey->isAnswered())
		{		
			$dialogBox->warning(get_lang('Some users have already answered to this survey.'));
		}
		$dialogBox->question( get_lang('Are you sure you want to delete this survey?'));
    	$dialogBox->form($form);
    	  	
    	return $dialogBox->render();
	}  
    
    
	private function displaySurveyList()
	{   
		
	    $surveyList = Survey::loadSurveyList(claro_get_current_course_id());
	    $surveyListTpl = new PhpTemplate(dirname(__FILE__).'/templates/survey_list.tpl.php');
	    $surveyListTpl->assign('surveyList', $surveyList);
	    $surveyListTpl->assign('editMode', $this->editMode);   
	    
	    return $surveyListTpl->render();
		
	}
	
	protected function defineBreadCrumb(){
		parent::defineBreadCrumb();
		if($this->showConfirmDelete){
    		parent::appendBreadCrumbElement(get_lang('Delete survey')); 
		}
	}
	
	
}

$page = new SurveyListPage();
$page->execute(); 
