<?php 
global $tlabelReq;
$tlabelReq = 'LVSURVEY';

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';
From::module('LVSURVEY')->uses( 'controller/surveyPage.class');


class EditSurveyPage extends SurveyPage
{
    const DEFAULT_TITLE = '[Blank Survey]';
    
    private $surveyId = 0;
    private $showSuccessBox = false;
    
    public function __construct(){
        parent::__construct();
    }
    
    //override
    protected function loadSurvey()
    {
        
        $input = Claro_UserInput::getInstance();
        $this->surveyId = (int)$input->get('surveyId', '-1');
        if($this->surveyId > 0)
        {
            parent::loadSurvey();
            return;
        }
        $survey = new Survey(claro_get_current_course_id());
        $survey->title = self::DEFAULT_TITLE;
        parent::setSurvey($survey);
    }
    
    protected function defineBreadCrumb(){
        parent::defineBreadCrumb();
        
        $survey = parent::getSurvey();
        $editSurveyURL = 'edit_survey.php';
        
        if($survey->id != -1)
        {
            $editSurveyURL .= '?surveyId='.$this->survey->id;
            parent::appendBreadCrumbElement(get_lang('Edit survey'), $editSurveyURL);
        }
        else
        {
            parent::appendBreadCrumbElement('New survey', $editSurveyURL);
        }
        
        if($this->showSuccessBox)
        {
            parent::appendBreadCrumbElement(get_lang("Survey has been saved")."!");
        }
    }
    
    
    public function performSurveySave(){
        if(isset($_REQUEST['claroFormId']))
        {
            $this->processForm();
        }
    }
    
    private function processForm(){
        try
        {
            $survey = Survey::loadFromForm(claro_get_current_course_id());
            $survey->save();
            parent::setSurvey($survey);
            parent::success(get_lang("Survey has been saved")."!");
            $this->showSuccessBox = true;
        }
        catch(Exception $e)
        {
            parent::error($e->getMessage());
        }
    }
    
    public function render()
    {
        if($this->showSuccessBox)
        {
            return $this->renderSucessBox();
        }
        return $this->renderEditSurvey();
    }
    
    private function renderSucessBox()
    {
        $survey = parent::getSurvey();
        $surveySavedBoxTpl = new PhpTemplate(dirname(__FILE__).'/templates/survey_saved_success.tpl.php');
        $surveySavedBoxTpl->assign('surveyId', $survey->id);
        
        return $surveySavedBoxTpl->render();
    }
    
    private function renderEditSurvey(){   
        
        $survey = parent::getSurvey();
        $editSurveyTpl = new PhpTemplate(dirname(__FILE__).'/templates/edit_survey.tpl.php');
        $editSurveyTpl->assign('survey', $survey);
        return $editSurveyTpl->render();
    }
}

$page = new EditSurveyPage();
$page->execute();
