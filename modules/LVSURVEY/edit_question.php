<?php 
global $tlabelReq;
$tlabelReq = 'LVSURVEY';

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';  
From::module('LVSURVEY')->uses(	'controller/managerSurveyLessPage.class', 
								'model/question.class', 
								'model/survey.class');

class EditQuestionPage extends ManagerSurveyLessPage {

	private $question = null;
	private $questionId = 0;


    private $questionLine = null;
    private $answerRequired = true;

	private $survey = null;

	private $showSuccess = false;

	
 	public function __construct()
	{
		parent::__construct();
		$input = Claro_UserInput::getInstance();
		$idValidator = new Claro_Validator_ValueType('intstr');
		$input->setValidator('questionId', $idValidator);
		$input->setValidator('surveyId', $idValidator);
                $input->setValidator('questionLineId', $idValidator);
       
		$surveyId = (int)$input->get('surveyId', '0');
        $questionLineId = (int)$input->get('questionLineId', '0');
		$this->questionId = (int)$input->get('questionId', '0');
        
        
		if(!empty($surveyId))
		{
			$this->survey = Survey::load($surveyId);
            if($questionLineId > 0)
            {
                $this->questionLine = SurveyLineFactory::loadSingleLine($questionLineId, $this->survey);
                $this->question = $this->questionLine->question;
                $this->questionId = $this->question->id;
            }
            else
            {
                $this->question = new Question();
                $this->questionLine = 
                        SurveyLineFactory::createQuestionLine(
                                    $this->survey,
                                    $this->question, 
                                    true);
            }
		}


        if(!empty($this->questionId) && $this->questionId != -1)
		{
			$this->question = Question::load($this->questionId);
		} 
		if(empty($this->question))
		{
			$this->question = new Question();
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
	    	$editQuestionTpl->assign('survey', $this->survey);
            $editQuestionTpl->assign('answerRequired', $this->questionLine->isRequired());
            $editQuestionTpl->assign('questionLine', $this->questionLine);
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
		
		
		if($this->question->id != 0)
	    {
	    	$editQuestionURL .= '&questionId='.$this->question->id;
	    	parent::appendBreadCrumbElement(get_lang('Edit question'), $editQuestionURL);
	    }
	    else
	    {
	    	parent::appendBreadCrumbElement(get_lang('New question'), $editQuestionURL);
	    }
	    
	}
	
	public function performQuestionSave()
	{
       try
		{
			$this->question = Question::loadFromForm();
            $this->question->setAuthorId(claro_get_current_user_id());
			$this->question->save();
			if(!empty($this->survey))
			{
               
                $input = Claro_UserInput::getInstance();
                $input->setValidator('answerRequired', new Claro_Validator_ValueType('intstr') );
                $this->answerRequired = (bool) ($input->get('answerRequired', '1')) ;
                $this->questionLine->question = $this->question;
                $this->questionLine->setRequired($this->answerRequired);
                $this->questionLine->save();

			}
			parent::success(get_lang("Question was successfully saved"));
			$this->showSuccess = true;
		}
		catch (Exception $e)
		{
			parent::error($e->getMessage());
		}
        
        
	}
    
    protected function checkAccess(){
        if (!parent::checkAccess())
        {
            return false;
        }
        if ( $this->questionId == 0 )
        {
            return true;
        }
        $current_user_id = claro_get_current_user_id(); 
        $author_id = $this->question->getAuthorId();
        if($current_user_id != $author_id)
        {
            return false;
        }
        return true;
	}
}

$page = new EditQuestionPage();
$page->execute();