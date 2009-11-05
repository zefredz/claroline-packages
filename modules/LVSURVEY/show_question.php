 <?php 
 
 require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';
    
//=================================
// Security check
//=================================

if ( 	!claro_is_in_a_course() 
		|| !claro_is_course_allowed() 
		|| !claro_is_user_authenticated() )
{
	claro_disp_auth_form(true);
}

if(!claro_is_allowed_to_edit())
{
	//not allowed for normal user
    claro_redirect('survey_list.php');
    exit();
}
    
//=================================
// Init section
//=================================
From::module('LVSURVEY')->uses('Question.class');
    

$tlabelReq = 'LVSURVEY';
add_module_lang_array($tlabelReq);
claro_set_display_mode_available(true);    

try
{
	$questionId = (int)$_REQUEST['questionId'];
	$question = Question::load($questionId);
	displayQuestion($question);	
}
catch(Exception $e)
{
	$dialogBox = new DialogBox();
	$dialogBox->error( $e->getMessage());
   	displayContents($dialogBox->render(),get_lang("Error"));
   	exit;
}   
 

//=================================
// Display section
//=================================

function displayQuestion($question)
{

	$pageTitle = get_lang('Question preview');
	$editMode = claro_is_allowed_to_edit();
	        
    $previewQuestionTpl = new PhpTemplate(dirname(__FILE__).'/templates/preview_question.tpl.php');
    $previewQuestionTpl->assign('question', $question);
    $previewQuestionTpl->assign('editMode', claro_is_allowed_to_edit()); 
	if(isset($_REQUEST['surveyId']))
	{
		$previewQuestionTpl->assign('surveyId', (int)$_REQUEST['surveyId']);
	}  
    
    displayContents($previewQuestionTpl->render(), $pageTitle);	
}

function displayContents($contents, $pageTitle)
{
	$claroline = Claroline::getInstance();
	
    $claroline->display->banner->breadcrumbs->append(get_lang('Surveys'), 'survey_list.php');
    $claroline->display->banner->breadcrumbs->append(get_lang('Question pool'), 'question_pool.php');
	$claroline->display->banner->breadcrumbs->append($pageTitle);
	
    $claroline->display->body->appendContent($contents);
   
    // render output
    echo $claroline->display->render();
	
}    

    
?>