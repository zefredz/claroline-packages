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
From::module('LVSURVEY')->uses('survey.class', 'result.class', 'csvResults.class');
    
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
   	displayContents($dialogBox->render(),new Survey(claro_get_current_course_id()),get_lang("Error"));
   	exit;
}   
 
//=================================
// Choose Action
//=================================

if(claro_is_allowed_to_edit() && (isset($_REQUEST['cmd'])))
{
	switch($_REQUEST['cmd'])
	{
		case 'reset':
			resetResults($survey);
			break;		
	}
}
else
{
	$format = 'HTML';
	if (isset($_REQUEST['format']))
	{
		$format = $_REQUEST['format'];
	} 
	switch($format)
	{
		case 'SyntheticCSV' :
			sendSyntheticCSVResults($survey);
			break;
		case 'RawCSV' :
			sendRawCSVResults($survey);
			break;
		default:
		case 'HTML' :
			displayResults($survey);	
	}
	
}
 
    
//=================================
// Action functions
//=================================

function resetResults($survey)
{
	if(isset($_REQUEST['claroFormId']))
    {
        $survey->reset();
                
        $dialogBox = new DialogBox();
        $dialogBox->success(get_lang('Results have been deleted'));
        displayResults($survey);
    }
    else 
    {
        displayResetConf($survey);
    }
}    
    
//=================================
// Display section
//=================================

function displayResetConf($survey)
{
	$confirmationTpl = new PhpTemplate(dirname(__FILE__).'/templates/reset_survey_conf.tpl.php');
	$confirmationTpl->assign('survey', $survey);
    $pageTitle = get_lang('Delete all results');
    displayContents($confirmationTpl->render(),$survey, $pageTitle);
}

function sendSyntheticCSVResults($survey)
{
	sendCSV('surveyResults'.$survey->id.'.csv', new SyntheticResults($survey));
}

function sendRawCSVResults($survey)
{
	if($survey->is_anonymous)
	{
		sendCSV('surveyResults'.$survey->id.'.csv', new AnonymousCSVResults($survey));
	}
	else
	{
		sendCSV('surveyResults'.$survey->id.'.csv',new NamedCSVResults($survey));
	}	
}

function sendCSV($filename, $csvResults)
{
	$csvResults->buildRecords();
	header("Content-type: application/csv");
    header('Content-Disposition: attachment; filename="'.$filename.'"');
   	echo $csvResults->export();
}

function displayResults($survey, $dialogBox = NULL)
{
	$contents = '';
	if(!is_null($dialogBox))
	{
		$contents .= $dialogBox->render();
	}
	if(!claro_is_allowed_to_edit() && !$survey->areResultsVisibleNow())
	{
		$dialogBox = new DialogBox();
		$dialogBox->error(get_lang('You are not allowed to see these results.'));
		if($survey->resultsVisibility == 'VISIBLE_AT_END')
        {        	
        	if(is_null($survey->endDate))
        	{
        		$dialogBox->info(get_lang('Results will be visible only at the end of the survey.'));
        	}
        	else
        	{
        		$dialogBox->info(get_lang('Results will be visible only at the end of the survey on %date.', 
                        array('%date'=>claro_html_localised_date(get_locale('dateFormatLong'), $survey->endDate))));
        	}
        }
		$contents = $dialogBox->render();
	}
	else
	{
		$showResultsTpl = new PhpTemplate(dirname(__FILE__).'/templates/show_results.tpl.php');
		$showResultsTpl->assign('survey', $survey);
		$showResultsTpl->assign('editMode', claro_is_allowed_to_edit());
		$contents = $showResultsTpl->render();
	}	
    $pageTitle = get_lang('Results');    
    displayContents($contents,$survey, $pageTitle);
}


function displayContents($contents,$survey, $pageTitle)
{
	$claroline = Claroline::getInstance();
	
    $claroline->display->banner->breadcrumbs->append(get_lang('Surveys'), 'survey_list.php');
	$claroline->display->banner->breadcrumbs->append(htmlspecialchars($survey->title), 'show_survey.php?surveyId='.$survey->id); 
	$claroline->display->banner->breadcrumbs->append(get_lang('Results'));
	
    $claroline->display->body->appendContent($contents);
   
    // render output
    echo $claroline->display->render();
	
}   
    
?>