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
    
//=================================
// Init section
//=================================
From::module('LVSURVEY')->uses('survey.class');
    
// Tool label (must be in database)
$tlabelReq = 'LVSURVEY';
add_module_lang_array($tlabelReq);
claro_set_display_mode_available(true);
    
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
   	exit;
}   
    
    
//=================================
// Choose Action
//=================================

	if(isset($_REQUEST['claroFormId']))
	{
		try
		{
			saveParticipation();
			if($survey->areResultsVisibleNow())
			{
				claro_redirect('show_results.php?surveyId='.$survey->id);
				exit;
			}
			$dialogBox = new DialogBox();
			$dialogBox->success(get_lang('Participation saved'));
		}
		catch(Exception $e)
		{
			$dialogBox = new DialogBox();
			$dialogBox->error($e->getMessage());
			
		}
		displaySurvey($survey,$dialogBox);
		exit;
		
	}
    
    if(isset($_REQUEST['cmd']) && isset($_REQUEST['questionId']) )
    {
    	$questionId = (int)$_REQUEST['questionId'];	    
	    switch($_REQUEST['cmd'])
	    {
	    	case 'questionMoveUp' :
	    		moveQuestion($survey,$questionId, true );
	    		break;
	    	case 'questionMoveDown' :
	    		moveQuestion($survey, $questionId,false);
	    		break;
	    	case 'questionRemove' :
	    		removeQuestion($survey, $questionId);
	    		break;
	    	case 'setCommentSize' :
	    		setCommentSize($survey,$questionId);
	    		break;	    			    		
	    }    	
    } 
    
    displaySurvey($survey);

//=================================
// Action functions
//=================================
 
    
    function removeQuestion($survey, $questionId)
    {
    	try
    	{
    		$survey->removeQuestion($questionId);
    		$dialogBox = new DialogBox();
    		$dialogBox->success("Question removed from Survey");
    		displaySurvey($survey, $dialogBox);
    	}
    	catch(Exception $e)
    	{
    		$dialogBox = new DialogBox();
    		$dialogBox->error($e->getMessage());
    		displaySurvey($survey, $dialogBox);
    	}
    	exit;
    }
    function moveQuestion($survey,$questionId, $up )
    {
    	try{
            $survey->moveQuestion($questionId, $up);
        }
        catch (Exception $e)
        {
        	$dialogBox = new DialogBox();
        	$dialogBox->error($e->getMessage());
        }
        displaySurvey($survey, $dialogBox);
    }
   
    
    function saveParticipation()
    {    	
        $participation = Participation::loadFromForm();
        $participation->save(); 
    }
    
    function setCommentSize($survey,$questionId)
    {
    	$newCommentSize = (int)$_REQUEST['commentSize'];
    	if($newCommentSize < 0)
    		$newCommentSize = 0;
    	if($newCommentSize > 200)
    		$newCommentSize = 200;
    	$surveyLineList = $survey->getSurveyLineList();
    	$surveyLineList[$questionId]->maxCommentSize = $newCommentSize;
    	$surveyLineList[$questionId]->save();
    }

//=================================
// Display section
//=================================

function displaySurvey($survey, $dialogBox = NULL)
{

	$editMode = claro_is_allowed_to_edit();
	$participation = Participation::loadParticipationOfUserForSurvey(claro_get_current_user_id(), $survey->id);
	if(!$editMode && !$survey->isAccessible())
    {
    	$dialogBox = new DialogBox();
        $dialogBox->error( get_lang('This survey is not accessible'));
        displayContents($dialogBox->render(), $survey->title);
        return;
    }
	 
        
    $showSurveyTpl = new PhpTemplate(dirname(__FILE__).'/templates/show_survey.tpl.php');
    $showSurveyTpl->assign('survey', $survey);
    $showSurveyTpl->assign('participation', $participation);
    $showSurveyTpl->assign('editMode', claro_is_allowed_to_edit());

    $out = '';
    if(!is_null($dialogBox))
    {
    	$out .= $dialogBox->render();
    }
    $out .= $showSurveyTpl->render();
    
    displayContents($out, $survey->title);	
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