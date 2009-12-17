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
    

$tlabelReq = 'LVSURVEY';
add_module_lang_array($tlabelReq);
try
{
	$surveyId = (int)$_REQUEST['surveyId'];
	$survey = Survey::load($surveyId);
	if(isset($_REQUEST['claroFormId']))
	{
		addSeparatorToSurvey($survey);
	}
	else
	{
		displayAddNewSeparatorForm($survey);
	}
}
catch(Exception $e)
{
   	displayError($e);
}


//=================================
// Controller Functions
//=================================


function addSeparatorToSurvey($survey)
{		
		$surveyLine = SurveyLineFactory::createSeparatorFromForm($survey);        
        $surveyLine->save();		
        $survey->addSurveyLine($surveyLine);
        displaySuccess($survey, get_lang("Separator successfully added to survey"));

}

//=================================
// Display section
//=================================

function displayAddNewSeparatorForm($survey)
{
	$separatorTpl = new PhpTemplate(dirname(__FILE__).'/templates/editSeparator.tpl.php');
	$separatorTpl->assign('surveyId', $survey->id);
	$separatorTpl->assign('separator', SurveyLineFactory::createSeparatorLine($survey,''));
	renderContents($separatorTpl->render(), $survey,get_lang('Add Separator'));
}

function displaySuccess($survey, $successMessage)
{
	$surveySavedBoxTpl = new PhpTemplate(dirname(__FILE__).'/templates/survey_saved_success.tpl.php');
	$surveySavedBoxTpl->assign('surveyId', $survey->id);    
	
    $dialogBox = new DialogBox();
    $dialogBox->success($successMessage);    
	
	renderContents($surveySavedBoxTpl->render(), $survey,get_lang('Success'), $dialogBox);
}

function displayError($e)
{
	$dialogBox = new DialogBox();
	$dialogBox->error( get_lang($e->getMessage()));
	renderContents('',NULL,get_lang('Error'),$dialogBox);
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