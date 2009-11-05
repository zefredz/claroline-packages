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
//FromKernel::uses('utils/input.lib', 'utils/validator.lib');
    
// Tool label (must be in database)
$tlabelReq = 'LVSURVEY';
add_module_lang_array($tlabelReq);

    
//prepare survey Object
try
{
	$surveyId = (int)$_REQUEST['surveyId'];
	$survey = Survey::load($surveyId);	
}
catch(Exception $e)
{
	$dialogBox = new DialogBox();
	$dialogBox->error( $e->getMessage());
   	displayContents($dialogBox->render(),get_lang("Error"));
}

if(isset($_REQUEST['questionId']))
{
	addQuestionToSurvey((int)$_REQUEST['questionId'], $survey);
	exit;
}

if((isset($_REQUEST['fromPool'])) && ((int)$_REQUEST['fromPool']==1))
{
	addQuestionFromPoolToSurvey($survey);
	exit;
}

displayAddingMethods($survey);


function addQuestionToSurvey($questionId, $survey)
{
	try
	{
		$question = Question::load($questionId);
        $survey->addQuestion($question);
        $dialogBox = new DialogBox();
        $dialogBox->success("Question successfully added to survey");
        displaySuccess($survey, $dialogBox);
	}
	catch(Exception $e)
	{
		$dialogBox = new DialogBox();
		$dialogBox->error($e->getMessage());
		displayAddingMethods($survey, $dialogBox);
	}	
}

function addQuestionFromPoolToSurvey($survey)
{
	claro_redirect("question_pool.php?surveyId=".$survey->id);
}


function displaySuccess($survey, $dialogBox)
{
	$surveySavedBoxTpl = new PhpTemplate(dirname(__FILE__).'/templates/survey_saved_success.tpl.php');
	$surveySavedBoxTpl->assign('surveyId', $survey->id);        
	
	renderContents($surveySavedBoxTpl->render(), $survey,get_lang('Success'), $dialogBox);
}
function displayAddingMethods($survey, $dialogBox = NULL)
{
	$addQuestionTpl = new PhpTemplate(dirname(__FILE__).'/templates/add_question.tpl.php');
    $addQuestionTpl->assign('survey', $survey);		    
    $pageTitle = get_lang("Add a question to the survey");   
	
    renderContents($addQuestionTpl->render(), $survey, $pageTitle, $dialogBox);
}
    
function renderContents($contents,$survey, $pageTitle, $dialogBox = NULL)
{
	//create breadcrumbs
	$claroline = Claroline::getInstance();
    $claroline->display->banner->breadcrumbs->append(get_lang('Surveys'), 'survey_list.php');
    $claroline->display->banner->breadcrumbs->append(htmlspecialchars($survey->title), 'show_survey.php?surveyId='.$survey->id);
    $claroline->display->banner->breadcrumbs->append($pageTitle);
    
    // append output
    $out = claro_html_tool_title($pageTitle);
    if(!is_null($dialogBox))
    {
    	$out .= $dialogBox->render();
    }
    $out .= $contents;
    $claroline->display->body->appendContent($out);
   
    // render output
    echo $claroline->display->render();
}
?>