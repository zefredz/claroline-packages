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
    From::module('LVSURVEY')->uses('question.class');
    
     // Tool label (must be in database)
    $tlabelReq = 'LVSURVEY';
    add_module_lang_array($tlabelReq);
    claro_set_display_mode_available(true);
    
    
//=================================
// Choose action
//=================================

    if(!isset($_REQUEST['cmd']) || !isset($_REQUEST['questionId']))
    {
    	displayQuestionPool();
    	exit();
    }
    
    $questionId = (int)$_REQUEST['questionId'];
    $question = Question::load($questionId);
    switch($_REQUEST['cmd'])
    {
    	case 'questionDel' :
    		deleteQuestion($question);
    		break;
    	default :
    		displayQuestionPool();
    		
    }
    
//=================================
// Action functions
//=================================

function deleteQuestion($question)
{
	if(!isset($_REQUEST['conf']) || ((int)$_REQUEST['conf']!=1))
    {
    	displayDeleteConfirmation($question);
        exit();                
    }
            
    //delete the survey            
    $dialogBox = new DialogBox();
    try{
    	$question->delete();
        $dialogBox->success( get_lang('Question has been deleted')."!");
    }
    catch (Exception $e)
    {
    	$dialogBox->error($e->getMessage());
    }
    displayQuestionPool($dialogBox);
}

//=================================
// Display Section
//=================================

function displayDeleteConfirmation($question)
{		
	$delConfTpl = new PhpTemplate(dirname(__FILE__).'/templates/delete_question_conf.tpl.php');
   	$delConfTpl->assign('question', $question);        
   	$form = $delConfTpl->render();
   	
   	$dialogBox = new DialogBox();
   	if($question->getUsed() > 0 )
	{		
		$dialogBox->error(get_lang('This question is used in some surveys. You can\'t delete it'));
		displayQuestionPool($dialogBox);
		exit();
	}
	$dialogBox->question( get_lang('Are you sure you want to delete this question?'));
   	$dialogBox->form($form);
   	$pageTitle = get_lang('Delete question');    	
   	displayContents($dialogBox->render(), $pageTitle);
}  
    
    
function displayQuestionPool($dialogBox = NULL)
{   
	$orderby = 'text';
	if(isset($_REQUEST['orderby']))
    	$orderby = $_REQUEST['orderby'];
    $ascDesc = 'ASC';
    if(isset($_REQUEST['ascDesc']))
        $ascDesc = $_REQUEST['ascDesc'];
      
        
	
    $contentsToShow = '';
	try
	{
	    $questionList = Question::loadQuestionPool($orderby, $ascDesc);
	    $questionListTpl = new PhpTemplate(dirname(__FILE__).'/templates/question_pool.tpl.php');
	    $questionListTpl->assign('questionList', $questionList);
	    $questionListTpl->assign('orderby', $orderby);
	    $questionListTpl->assign('ascDesc', $ascDesc);
	    if(isset($_REQUEST['surveyId']))
	    {
	    	$questionListTpl->assign('surveyId', (int)$_REQUEST['surveyId']);
	    }  	    
	    if(!is_null($dialogBox))
	    {
	    	$contentsToShow .= $dialogBox->render();
	    }
	    $contentsToShow .= $questionListTpl->render();
	}catch(Exception $e)
	{
		$dialogBox = new DialogBox();
		$dialogBox->error($e->getMessage());
		$contentsToShow = $dialogBox->render();
	}

    
	displayContents($contentsToShow, get_lang('Question pool'));
	
}

function displayContents($contents, $pageTitle)
{
	$claroline = Claroline::getInstance();
	
    $claroline->display->banner->breadcrumbs->append(get_lang('Surveys'), 'survey_list.php'); 
    $claroline->display->banner->breadcrumbs->append($pageTitle); 
	
    $claroline->display->body->appendContent($contents);
   
    // render output
    echo $claroline->display->render();
	
}  
        

?>