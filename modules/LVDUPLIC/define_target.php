<?php 
/**
 * CLAROLINE
 *
 * This  script  let the user define a target course for duplication and memorize it
 *
 * Once done it redirects to next step of duplication (choose tools)
 *
 * 
 *
 */

//=================================
// Include section
//=================================

require_once '../../claroline/inc/claro_init_global.inc.php';
require_once 'lib/LVDUPLIC.lib.php';

//=================================
// Security check
//=================================

// If you want to duplicate a course you need to be able to manage the source course and create a new one.
if ( ! claro_is_user_authenticated() )       claro_disp_auth_form();
if ( ! claro_is_allowed_to_create_course() ) claro_die(get_lang('Not allowed'));
// Actually you even need to be admin
if ( ! claro_is_platform_admin() ) claro_die(get_lang('Not allowed'));

//=================================
// Main section
//=================================


define('DISP_COURSE_CREATION_FORM'     ,__LINE__);
define('DISP_COURSE_CREATION_SUCCEED'  ,__LINE__);
define('DISP_COURSE_CREATION_FAILED'   ,__LINE__);
define('DISP_COURSE_CREATION_PROGRESS' ,__LINE__);



$cmd = isset($_REQUEST['cmd']) ? $_REQUEST['cmd'] : null;

//source course
$source_course_data = DUPSessionMgr::getSourceCourseData();
if(!isset($source_course_data))
{
	claro_die("Source course not set");
}


//default values
$display = DISP_COURSE_CREATION_FORM; 
$dialogBox = '' ;
$backUrl = get_module_entry_url('LVDUPLIC');
$target_course_data = DUPSessionMgr::getTargetCourseData();
$target_course = new ClaroCourse();

//define target
if( !isset($cmd)  || '' == $cmd){
	if(! isset($target_course_data))
	{		
		// New course object		
		$target_course = DUPSessionMgr::arrayToCourse($source_course_data);
		$target_course->courseId = '';
		$target_course->title = '';
		$target_course->officialCode = '';
				
	} else {
		// Target already defined		
		$target_course = DUPSessionMgr::arrayToCourse($target_course_data);
		$target_course->courseId = '';		
	}
}


// waiting screen
elseif( $cmd == 'rqProgress' )
{
    $target_course->handleForm();

    if( $target_course->validate() )
    {
        // Trig a waiting screen as course creation may take a while ...

        $target_course_data = DUPSessionMgr::courseToArray($target_course);
        DUPSessionMgr::setTargetCourseData($target_course_data);
        
        $backUrl .= "?cmd=".DUPConstants::$DUP_STEP_CHOOSE_TOOLS;
        claro_redirect($backUrl);
        die();

    }
    else
    {
        $dialogBox .= $target_course->backlog->output();
        $display = DISP_COURSE_CREATION_FAILED;
    }
}


//=================================
// Display section
//=================================

include get_path('incRepositorySys') . '/claro_init_header.inc.php';


echo claro_html_tool_title(get_lang('Create a Duplicata'));

if ( !empty($dialogBox) ) echo claro_html_message_box($dialogBox);

echo $target_course->displayForm($backUrl);

include get_path('incRepositorySys') . '/claro_init_footer.inc.php';
?>
