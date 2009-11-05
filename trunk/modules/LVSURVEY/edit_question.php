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

From::module('LVSURVEY')->uses('Question.class', 'Survey.class');
FromKernel::uses('utils/input.lib', 'utils/validator.lib');
    
// Tool label (must be in database)
$tlabelReq = 'LVSURVEY';
add_module_lang_array($tlabelReq);
    
$processForm = isset($_REQUEST['claroFormId']);
$question = new Question();

//=================================
// SHOW FORM
//=================================
if(!$processForm)
{
	//prepare survey Object
	$questionId = isset($_REQUEST['questionId'])?(int)$_REQUEST['questionId']:-1;
	$is_updating = ($questionId != -1);    
    if($is_updating)
    {
        $question = Question::load($questionId);
    }
    renderEditQuestion($question);
    exit();
}
    

//=================================
// PARSE & PROCESS FORM
//=================================
try
{
	$question = Question::loadFromForm();
	$question->save();
	if(isset($_REQUEST['surveyId']))
	{
		$survey = Survey::load((int)$_REQUEST['surveyId']);
		$survey->addQuestion($question);
	}
	renderSucess($question);
}
catch(Exception $e)
{
	$dialogBox = new DialogBox();
	$dialogBox->error( $e->getMessage());
   	renderEditQuestion($question, $dialogBox);
}

//=================================
// DISPLAY FUNCTIONS
//=================================
function renderSucess($question)
{
	$surveySavedBoxTpl = new PhpTemplate(dirname(__FILE__).'/templates/survey_saved_success.tpl.php');
	
	if(!isset($_REQUEST['surveyId']))
	{
		claro_redirect('question_pool.php');
		exit();
	}
	$surveySavedBoxTpl->assign('surveyId', $_REQUEST['surveyId']);        
	$boxcontent = $surveySavedBoxTpl->render();
	$dialogBox = new DialogBox();
	$dialogBox->success( get_lang("Question has been saved")."!");
	$dialogBox->form($boxcontent);
	
	renderContents($dialogBox->render(),get_lang('Success'));
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
    
    renderContents($contenttoshow, $pageTitle);
    

	
}

function renderContents($contents, $pageTitle)
{
	$out = '';
    $out .= claro_html_tool_title($pageTitle);  
    
    

    $out .= $contents;
	//create breadcrumbs
	$claroline = Claroline::getInstance();
    $claroline->display->banner->breadcrumbs->append(get_lang('Surveys'), 'survey_list.php');
    $claroline->display->banner->breadcrumbs->append($pageTitle);
    // append output
    $claroline->display->body->appendContent($out);
   
    // render output
    echo $claroline->display->render();
}
 
?>