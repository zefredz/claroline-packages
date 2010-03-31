<?php 
 
require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';  
From::module('LVSURVEY')->uses('managerSurveyLessPage.class', 'question.class', 'survey.class');

class EditQuestionPage extends ManagerSurveyLessPage {

	private $question = null;
	private $questionId = 0;

	private $survey = null;

	private $showSuccess = false;
	
 	public function __construct()
	{
		parent::__construct();
		$input = Claro_UserInput::getInstance();
		$idValidator = new Claro_Validator_ValueType('intstr');
		$input->setValidator('questionId', $idValidator);
		$input->setValidator('surveyId', $idValidator);		
		$surveyId = (int)$input->get('surveyId', '0');
		$this->questionId = (int)$input->get('questionId', '0');
		
		if(!empty($this->questionId) && $this->questionId != -1)
		{
			$this->question = Question::load($this->questionId);
		} 
		else 
		{
			$this->question = new Question();
		}
		
		if(!empty($surveyId))
		{
			$this->survey = Survey::load($surveyId);
		}		
	}
	
	
	public function render(){
		if($this->showSuccess){
			return $this->renderSuccess();
		}
		return $this->renderEditQuestion();
	}
	
	private function renderSuccess()
	{
		if(empty($this->survey))
		{
			claro_redirect('question_pool.php');
			die();
		}
		
		$surveySavedBoxTpl = new PhpTemplate(dirname(__FILE__).'/templates/survey_saved_success.tpl.php');
		$surveySavedBoxTpl->assign('surveyId', $this->survey->id);
		
		return $surveySavedBoxTpl->render(); 
	}
	
	private function renderEditQuestion()
	{

	
		$editQuestionTpl = new PhpTemplate(dirname(__FILE__).'/templates/edit_question.tpl.php');
    	$editQuestionTpl->assign('question', $this->question);
	    if(!empty($this->survey))
	    {
	    	$editQuestionTpl->assign('surveyId', $this->survey->id);
	    }
	
    	return $editQuestionTpl->render();    
    
	}
	
	public function defineBreadCrumb(){
		parent::defineBreadCrumb();
		$editQuestionURL = 'edit_question.php?';
		
		if(!empty($this->survey))
	    {
	    	$editQuestionURL .= '&surveyId='.$this->survey->id;
	    	parent::appendBreadCrumbElement($this->survey->title, 'show_survey.php?surveyId=' . (int)$this->survey->id);
	    }
		
		
		if($this->question->id != -1)
	    {
	    	$editQuestionURL .= '&questionId='.$this->question->id;
	    	parent::appendBreadCrumbElement(get_lang('Edit question'), $editQuestionURL);
	    }
	    else
	    {
	    	parent::appendBreadCrumbElement('New question', $editQuestionURL);
	    }
	    
	}
	
	public function performQuestionSave()
	{
		
		try
		{
			$this->question = Question::loadFromForm();
			$this->question->save();
			if(!empty($this->survey) && $this->question->id != $this->questionId)
			{
				$this->addQuestionToSurvey();
			}
			parent::success(get_lang("Question was successfully saved"));
			$this->showSuccess = true;
		}
		catch (Exception $e)
		{
			parent::error($e->getMessage());
		}
	}
	private function addQuestionToSurvey()
	{
			$surveyLine = SurveyLineFactory::createQuestionLine($this->survey,$this->question);        
	        $surveyLine->save();		
	        $this->survey->addSurveyLine($surveyLine);	
	}
}

$page = new EditQuestionPage();
$page->execute();