<?php // $Id$
/**
 * CLAROLINE
 *
 * This  script  let the user define a target course for duplication andmemorize it
 *
 * Once done it redirects to next step of duplication (choose tools)
 *
 * 
 *
 */

require  '../../claroline/inc/claro_init_global.inc.php';

//=================================
// Security check
//=================================

if ( ! claro_is_user_authenticated() )       claro_disp_auth_form();
if ( ! claro_is_allowed_to_create_course() ) claro_die(get_lang('Not allowed'));

//=================================
// Main section
//=================================

/*include claro_get_conf_repository() . 'course_main.conf.php';
require_once get_path('incRepositorySys') . '/lib/add_course.lib.inc.php';
require_once get_path('incRepositorySys') . '/lib/course.lib.inc.php';
require_once get_path('incRepositorySys') . '/lib/course_user.lib.php';
require_once get_path('incRepositorySys') . '/lib/user.lib.php'; // for claro_get_uid_of_platform_admin()
require_once get_path('incRepositorySys') . '/lib/fileManage.lib.php';
require_once get_path('incRepositorySys') . '/lib/form.lib.php';
require_once get_path('incRepositorySys') . '/lib/sendmail.lib.php';
require_once get_path('incRepositorySys') . '/lib/claroCourse.class.php';*/
require_once 'lib/LVDUPLIC.lib.php';

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

// target course has been created, go back to index with the target code, or fail with error message
/*elseif ( $cmd == 'exEdit' )
{
    $target_course->handleForm();

    if( $target_course->validate() )
    {
    	if( $target_course->save() )
    	{
            // include the platform language file with all language variables
            language::load_translation();
            language::load_locale_settings();
            

    		$target_course->mailAdministratorOnCourseCreation($thisUser['firstName'], $thisUser['lastName'], $thisUser['mail']);
    	
            setTargetCourse($target_course);
            
            $backUrl .= "?cmd=".DuplicationConstants::$DUP_STEP_CHOOSE_TOOLS;
            claro_redirect($backUrl);
            die();
    		
    	}
    	//TODO avoid else...else
    	else
    	{
    	    $dialogBox .= $course->backlog->output();
    		$display = DISP_COURSE_CREATION_FAILED;
    	}
    }
    else
    {
    	$dialogBox .= $course->backlog->output();
    	$display = DISP_COURSE_CREATION_FAILED;
    }
}
*/



//=================================
// Display section
//=================================

include get_path('incRepositorySys') . '/claro_init_header.inc.php';


echo claro_html_tool_title(get_lang('Create a Duplicata'));

if ( !empty($dialogBox) ) echo claro_html_message_box($dialogBox);


//if( $display == DISP_COURSE_CREATION_FORM || $display == DISP_COURSE_CREATION_FAILED )
//{
	// display form
	echo $target_course->displayForm($backUrl);
//}
/*elseif ( $display == DISP_COURSE_CREATION_PROGRESS )
{
	// display "progression" page
    $msg = get_lang('Creating course (it may take a while) ...') . '<br />' . "\n"
    .      '<p align="center">'
    .      '<img src="' . get_path('imgRepositoryWeb') . '/processing.gif" alt="" />'
    .      '</p>' . "\n"
    .      '<p>'
    .      get_lang('If after while no message appears confirming the course creation, please click <a href="%url">here</a>',array('%url' => $progressUrl))
    .      '</p>' . "\n\n"
    ;

    echo claro_html_message_box( $msg );
}*/


include get_path('incRepositorySys') . '/claro_init_footer.inc.php';
?>
