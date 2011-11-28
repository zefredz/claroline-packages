<?php 
global $tlabelReq;
$tlabelReq = 'LVSURVEY';


require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';
From::module('LVSURVEY')->uses(	'controller/managerSurveyLessPage.class', 
								'model/survey.class', 
								'model/question.class');


class ShowQuestionPage extends ManagerSurveyLessPage
{
	
	private $question = null;
	
	public function __construct()
	{
		parent::__construct();
		try{
			$questionId = parent::getUserInt('questionId');
			$this->question = Question::load($questionId);
		} catch (Exception $e){
			parent::errorAndDie($e->getMessage());
		}
	}
	public function render()
	{      
	    $previewQuestionTpl = new PhpTemplate(dirname(__FILE__).'/templates/preview_question.tpl.php');
	    $previewQuestionTpl->assign('question', $this->question);
	    $previewQuestionTpl->assign('editMode', true);
	    try
	    {
	    	$surveyId = parent::getUserInt('surveyId');
	    	$previewQuestionTpl->assign('surveyId', $surveyId);
	    } catch(Exception $e){
			//No survey, to attach this question to
		}
		return $previewQuestionTpl->render();			
	}
	
	public function defineBreadCrumb()
	{
		parent::defineBreadCrumb();
		$questionPoolURL = 'question_pool.php';
		try
	    {
	    	$surveyId = parent::getUserInt('surveyId');
	    	$questionPoolURL .= '?surveyId='.$surveyId;
	    } catch(Exception $e){
			//No survey, to attach this question to
		}
	    parent::appendBreadCrumbElement(get_lang('Question pool'), $questionPoolURL);
	    parent::appendBreadCrumbElement(get_lang('Question preview'));
	}
	
	
	
}
$page = new ShowQuestionPage();
$page->execute();