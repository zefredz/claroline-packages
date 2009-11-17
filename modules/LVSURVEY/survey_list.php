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
    $is_allowedToEdit = claro_is_allowed_to_edit();
    
    
//=================================
// Choose Action
//=================================
    
    if($is_allowedToEdit && (isset($_REQUEST['cmd'])) && (isset($_REQUEST['surveyId'])) )
    {
	    $surveyId = (int)$_REQUEST['surveyId'];
		$survey = Survey::load($surveyId);
	    switch($_REQUEST['cmd'])
	    {
	    	case 'toggleSurveyVisibility' :
	    		toggleSurveyVisibility($survey);
	    		break;
	    	case 'surveyStart' :
	    	case 'surveyRestart' :
	    		startSurvey($survey);
	    		break;
	    	case 'surveyStop' :
	    		stopSurvey($survey);
	    		break;
	    	case 'surveyMoveUp' :
	    		moveSurvey($survey,true);
	    		break;
	    	case 'surveyMoveDown' :
	    		moveSurvey($survey,false);
	    		break;
	    	case 'surveyDel' :
	    		deleteSurvey($survey);
	    		break;
	    	default :
	    		displaySurveyList();	    			    		
	    }    	
    } 
    else
    {
    	displaySurveyList();
    }

//=================================
// Action functions
//=================================
    
    function moveSurvey($survey, $up)
    {
    	$dialogBox = NULL;
        try{
            $survey->moveSurvey($up);
        }
        catch (Exception $e)
        {
        	$dialogBox = new DialogBox();
        	$dialogBox->error($e->getMessage());
        }
        displaySurveyList($dialogBox);
    	
    	
    }
    
    function toggleSurveyVisibility($survey)
    {
    	$survey->is_visible = !$survey->is_visible;
    	saveAndDisplayList($survey);
    }
    function startSurvey($survey)
    {
    	$survey->startDate = time();
       	if($survey->endDate < time())
        	$survey->endDate = 0;
        saveAndDisplayList($survey);
        	        
    }
    function stopSurvey($survey)
    { 
    	$survey->endDate = time();
        if($survey->startDate> time())
            $survey->startDate = 0;
        saveAndDisplayList($survey);
    }
    
    function saveAndDisplayList($survey)
    {
    	$dialogBox = NULL;
        try{
            $survey->save();
        }
        catch (Exception $e)
        {
        	$dialogBox = new DialogBox();
        	$dialogBox->error($e->getMessage());
        }
        displaySurveyList($dialogBox);
    }
    function deleteSurvey($survey)
    {    	
            if(!isset($_REQUEST['conf']) || ((int)$_REQUEST['conf']!=1))
            {
            	displayDeleteConfirmation($survey);
            	exit();                
                
            }
            
            //delete the survey            
            $dialogBox = new DialogBox();
            try{
            	$survey->delete();
            	$dialogBox->success( get_lang('Survey has been deleted')."!");
            }
            catch (Exception $e)
            {
            	$dialogBox->error($e->getMessage());
            }
            displaySurveyList($dialogBox);
    }
    
//=================================
// Display section
//=================================

	function displayDeleteConfirmation($survey)
	{		
		$delConfTpl = new PhpTemplate(dirname(__FILE__).'/templates/delete_survey_conf.tpl.php');
    	$delConfTpl->assign('survey', $survey);        
    	$form = $delConfTpl->render();
    	
    	$dialogBox = new DialogBox();	
		if($survey->isAnswered())
		{		
			$dialogBox->warning(get_lang('Some users have already answered to this survey.').' '.get_lang('Results will be removed.'));
		}
		$dialogBox->question( get_lang('Are you sure you want to delete this survey?'));
    	$dialogBox->form($form);
    	
    	$pageTitle = get_lang('Delete survey');    	
    	displayContents($dialogBox->render(), $pageTitle);
	}  
    
    
function displaySurveyList($dialogBox = NULL)
{   
	
    $surveyList = Survey::loadSurveyList(claro_get_current_course_id());
    $surveyListTpl = new PhpTemplate(dirname(__FILE__).'/templates/survey_list.tpl.php');
    $surveyListTpl->assign('surveyList', $surveyList);
    $surveyListTpl->assign('editMode', claro_is_allowed_to_edit());   
    $contentsToShow = "";
    if(!is_null($dialogBox))
    {
    	$contentsToShow .= $dialogBox->render();
    }
    $contentsToShow .= $surveyListTpl->render();

    
	displayContents($contentsToShow, get_lang('List of surveys'));
	
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