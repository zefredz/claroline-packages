<?php 
 
 require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php'; 
 
//=================================
// Security check
//=================================

 if ( 	!claro_is_in_a_course() 
 		|| !claro_is_course_allowed() 
 		|| !claro_is_user_authenticated() 
 	) 
 		claro_disp_auth_form(true);

if(!claro_is_allowed_to_edit())
{
	//not allowed for normal user
    claro_redirect('survey_list.php');
    exit();
}
 
//=================================
// Init section
//=================================

From::module('LVSURVEY')->uses('question.class', 'survey.class');
FromKernel::uses('utils/input.lib', 'utils/validator.lib');
    
// Tool label (must be in database)
$tlabelReq = 'LVSURVEY';
add_module_lang_array($tlabelReq);
    
$processForm = isset($_REQUEST['claroFormId']);
$question = new Question();
$questionId = isset($_REQUEST['questionId'])?(int)$_REQUEST['questionId']:-1;
$is_updating = ($questionId != -1);    
if($is_updating)
{
    $question = Question::load($questionId);
}

//=================================
// SHOW FORM
//=================================
if(!$processForm)
{	
    renderEditQuestion($question);
    exit();
}
    

//=================================
// PARSE & PROCESS FORM
//=================================
try
{
	$question = Question::loadFromForm();
	$shoulAddToSurvey = $question->id == -1;
	$question->save();
	if(!isset($_REQUEST['surveyId']))
	{
		claro_redirect('question_pool.php');
		exit;
	}
	if(isset($_REQUEST['surveyId']))
	{
		$survey = Survey::load((int)$_REQUEST['surveyId']);
		if($shoulAddToSurvey)
		{
			addQuestionToSurvey($question->id, $survey);
		}
		displaySuccess($survey, get_lang("Question was successfully saved"));
	}
	
	
}
catch(Exception $e)
{
	$dialogBox = new DialogBox();
	$dialogBox->error( $e->getMessage());
   	renderEditQuestion($question, $dialogBox);
}

function addQuestionToSurvey($questionId, $survey)
{

		$question = Question::load($questionId);
		$surveyLine = SurveyLineFactory::createQuestionLine($survey,$question);        
        $surveyLine->save();		
        $survey->addSurveyLine($surveyLine);        

}

//=================================
// DISPLAY FUNCTIONS
//=================================

function displaySuccess($survey, $successMessage)
{
	
	$surveySavedBoxTpl = new PhpTemplate(dirname(__FILE__).'/templates/survey_saved_success.tpl.php');
	$surveySavedBoxTpl->assign('surveyId', $survey->id);    
	
    $dialogBox = new DialogBox();
    $dialogBox->success($successMessage);    
	
	renderContents($surveySavedBoxTpl->render(), $survey,get_lang('Success'), $dialogBox);
}
    
    
function renderEditQuestion($question, $dialogBox = NULL)
{
	
	$is_updating = $question->id != -1;    
    if($is_updating)
    {
    	$pageTitle = get_lang('Edit this question');
    }
    else
    {
    	$pageTitle = get_lang('New question');
    }
	
	$editQuestionTpl = new PhpTemplate(dirname(__FILE__).'/templates/edit_question.tpl.php');
    $editQuestionTpl->assign('question', $question);
    if(isset($_REQUEST['surveyId']))
    {
    	$editQuestionTpl->assign('surveyId', $_REQUEST['surveyId']);
    }
	$contenttoshow = '';
    if(!is_null($dialogBox))
    {
    	$contenttoshow .= $dialogBox->render();	
    }
    $contenttoshow .= $editQuestionTpl->render();    
    
    renderContents($contenttoshow,NULL, $pageTitle);
    

	
}

function renderContents($contents,$survey , $pageTitle, $dialogBox = NULL)
{
	$claroline = Claroline::getInstance();
    $claroline->display->banner->breadcrumbs->append(get_lang('Surveys'), 'survey_list.php');
    if (!is_null($survey))
    	$claroline->display->banner->breadcrumbs->append(htmlspecialchars($survey->title), 'show_survey.php?surveyId='.$survey->id);
    $claroline->display->banner->breadcrumbs->append($pageTitle);
    
    $out = claro_html_tool_title($pageTitle);
    if(!is_null($dialogBox))
    {
    	$out .= $dialogBox->render();
    }
    $out .= $contents;
    $claroline->display->body->appendContent($out);
   
    echo $claroline->display->render();
}
 
?>