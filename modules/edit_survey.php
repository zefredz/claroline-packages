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

From::module('LVSURVEY')->uses('survey.class');
FromKernel::uses('utils/input.lib', 'utils/validator.lib');
    
// Tool label (must be in database)
$tlabelReq = 'LVSURVEY';
add_module_lang_array($tlabelReq);
    
$processForm = isset($_REQUEST['claroFormId']);
$survey = new Survey(claro_get_current_course_id());

//=================================
// SHOW FORM
//=================================
if(!$processForm)
{
	//prepare survey Object
	$surveyId = isset($_REQUEST['surveyId'])?(int)$_REQUEST['surveyId']:-1;
	$is_updating = ($surveyId != -1);    
    if($is_updating)
    {
        $survey = Survey::load($surveyId);
    }
    renderEditSurvey($survey);
    exit();
}
    

//=================================
// PARSE & PROCESS FORM
//=================================
try
{
	$survey = Survey::loadFromForm(claro_get_current_course_id());
	$survey->save();
}
catch(Exception $e)
{
	$dialogBox = new DialogBox();
	$dialogBox->error( $e->getMessage());
   	renderEditSurvey($survey, $dialogBox);
   	exit();
}
renderSucess($survey);

//=================================
// DISPLAY FUNCTIONS
//=================================
function renderSucess($survey)
{
	$surveySavedBoxTpl = new PhpTemplate(dirname(__FILE__).'/templates/survey_saved_success.tpl.php');
	$surveySavedBoxTpl->assign('surveyId', $survey->id);        
	$boxcontent = $surveySavedBoxTpl->render();
	$dialogBox = new DialogBox();
	$dialogBox->success( get_lang("Survey has been saved")."!".$boxcontent);
	
	renderContents($dialogBox->render(),$survey,get_lang('Success'));
}	
    
    
function renderEditSurvey($survey, $dialogBox = NULL)
{
	
	$is_updating = $survey->id != -1;    
    if($is_updating)
    {
    	$pageTitle = get_lang('Edit this survey');
    }
    else
    {
    	$pageTitle = get_lang('New survey');
    }
	
	$editSurveyTpl = new PhpTemplate(dirname(__FILE__).'/templates/edit_survey.tpl.php');
    $editSurveyTpl->assign('survey', $survey);
	$contenttoshow = '';
    if(!is_null($dialogBox))
    {
    	$contenttoshow .= $dialogBox->render();	
    }
    $contenttoshow .= $editSurveyTpl->render();    
    
    renderContents($contenttoshow,$survey, $pageTitle);	
}

function renderContents($contents, $survey, $pageTitle)
{
	$out = '';
    $out .= claro_html_tool_title($pageTitle);  
    
    

    $out .= $contents;
	//create breadcrumbs
	$claroline = Claroline::getInstance();
    $claroline->display->banner->breadcrumbs->append(get_lang('Surveys'), 'survey_list.php');
    $claroline->display->banner->breadcrumbs->append($survey->title, 'show_survey.php?surveyId='.$survey->id);
    $claroline->display->banner->breadcrumbs->append($pageTitle);
    // append output
    $claroline->display->body->appendContent($out);
   
    // render output
    echo $claroline->display->render();
}
    
?>