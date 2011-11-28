<?php 
global $tlabelReq;
$tlabelReq = 'LVSURVEY';
require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php'; 
From::module('LVSURVEY')->uses(	'model/question.class', 
 									'model/survey.class', 
 									'controller/managerSurveyPage.class');
 
 class AddQuestionPage extends ManagerSurveyPage
 {
 	
 	private $question = null;
 	
 	public function __construct()
 	{
 		parent::__construct();
		$questionId = parent::getUserInt('questionId');
		$this->question = Question::load($questionId);
		
 	}
 	public function render()
 	{
 		$survey = parent::getSurvey();
 		$surveySavedBoxTpl = new PhpTemplate(dirname(__FILE__).'/templates/survey_saved_success.tpl.php');
		$surveySavedBoxTpl->assign('surveyId', $survey->id);    
	
		return   $surveySavedBoxTpl->render();
 	}

 	
 	public function performAddQuestionToSurvey()
 	{
 		try
 		{
	 		$survey = parent::getSurvey(); 		
			$surveyLine = SurveyLineFactory::createQuestionLine($survey,$this->question);        
	        $surveyLine->save();		
	        $survey->addSurveyLine($surveyLine);
	        parent::success(get_lang("Question successfully added to survey"));
 		}
 		catch (Exception $e)
 		{
 			parent::error($e->getMessage());
 		} 				
 	}
 	
 	public function execute()
 	{
 		$this->performAddQuestionToSurvey();
 		parent::execute();
 	}
 }
  
 $page = new AddQuestionPage();
 $page->execute();
