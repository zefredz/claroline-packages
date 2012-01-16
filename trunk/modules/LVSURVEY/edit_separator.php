<?php 
global $tlabelReq;
$tlabelReq = 'LVSURVEY';

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php'; 
From::module('LVSURVEY')->uses( 'controller/managerSurveyPage.class');

class EditSeparatorPage extends ManagerSurveyPage {
    
    private $separator;
    private $surveyLineId = 0;
    private $showSuccess = false;
    
    
    public function __construct()
    {
        parent::__construct();
        $survey = parent::getSurvey();
        $input = Claro_UserInput::getInstance();
        $idValidator = new Claro_Validator_ValueType('intstr');
        $this->surveyLineId = (int)$input->get('surveyLineId', '0');
        if(empty($this->surveyLineId))
        {
            $this->separator =  SurveyLineFactory::createSeparatorLine($survey,'[Blank Separator]');
        } 
        else 
        {
            $this->separator = SurveyLineFactory::loadSingleLine($this->surveyLineId, $survey);
        }       
    }
    
    public function render(){
        if($this->showSuccess){
            return $this->renderSuccess();
        }
        return $this->renderEditSeparator();
    }
    
    private function renderSuccess()
    {
        $survey = parent::getSurvey();
        $surveySavedBoxTpl = new PhpTemplate(dirname(__FILE__).'/templates/survey_saved_success.tpl.php');
        $surveySavedBoxTpl->assign('surveyId', $survey->id);

        return $surveySavedBoxTpl->render();
    }
    
    private function renderEditSeparator()
    {
        $survey = parent::getSurvey();
        $separatorTpl = new PhpTemplate(dirname(__FILE__).'/templates/edit_separator.tpl.php');
        $separatorTpl->assign('surveyId', $survey->id);
        $separatorTpl->assign('separator', $this->separator);
        return $separatorTpl->render();
    }
    
    public function defineBreadCrumb(){
        parent::defineBreadCrumb();
        $survey = parent::getSurvey();
        $editSeparatorURL = 'edit_separator.php?surveyId='.$survey->id;
        
        
        if($this->separator->id != -1)
        {
            $editSeparatorURL .= '&surveyLineId='.$this->separator->id;
            parent::appendBreadCrumbElement(get_lang('Edit separator'), $editSeparatorURL);
        }
        else
        {
	    	parent::appendBreadCrumbElement(get_lang('New separator'), $editSeparatorURL);
        }
        
    }
    
    public function performSeparatorSave()
    {
        try
        {
            $survey = parent::getSurvey();
            $this->separator = SurveyLineFactory::createSeparatorFromForm($survey);
            $this->separator->save();
            parent::success(get_lang("Separator successfully added to survey"));
            $this->showSuccess = true;
        } catch (Exception $e){
            parent::error($e->getMessage());
        }
    }
}

$page = new EditSeparatorPage();
$page->execute();
die();