<?php 
/**
 * CLAROLINE
 *
 * This  script  let the user define a target course for duplication and memorize it
 *
 * Once done it redirects to next step of duplication (choose tools)
 *
 */

//=================================
// Include section
//=================================

require_once dirname(__FILE__).'/../../claroline/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/lib/LVDUPLIC.lib.php';

//=================================
// Security check
//=================================

// If you want to duplicate a course you need to be able to be an administrator
if ( ! claro_is_user_authenticated() )       	claro_disp_auth_form();
if ( ! claro_is_platform_admin() ) 				claro_die( get_lang('Not allowed') );


//=================================
// Init section
//=================================

// Deal with interbredcrumps
$interbredcrump[]= array ('url' => get_module_entry_url('LVDUPLIC') , 'name' => get_lang('Duplication'));
$nameTools = get_lang('Define Target');

$cmd = isset($_REQUEST['cmd']) ? $_REQUEST['cmd'] : null;

//source course
$sourceCourseData = DUPSessionMgr::getSourceCourseData();
if(!isset($sourceCourseData))
{
	claro_die("Source course not set");
}

//default values
$dialogBox = '' ;
$backUrl = get_module_entry_url('LVDUPLIC');
$targetCourseData = DUPSessionMgr::getTargetCourseData();
$targetCourse = new ClaroCourse();

//=================================
// Main section
//=================================

//define target
if( ! isset($cmd)  || '' == $cmd)
{
	if( ! isset( $targetCourseData ) )
	{		
		// New course object		
		$targetCourse = DUPSessionMgr::arrayToCourse($sourceCourseData);
		$targetCourse->courseId = '';
		$targetCourse->title = '';
		$targetCourse->officialCode = '';
				
	} 
	else 
	{
		// Target already defined		
		$targetCourse = DUPSessionMgr::arrayToCourse( $targetCourseData );
		$targetCourse->courseId = '';		
	}
}


// waiting screen
elseif( $cmd == 'rqProgress' )
{
    $targetCourse->handleForm();

    if( $targetCourse->validate() )
    {
        // Trig a waiting screen as course creation may take a while ...

        $targetCourseData = DUPSessionMgr::courseToArray($targetCourse);
        DUPSessionMgr::setTargetCourseData($targetCourseData);
        
        claro_redirect( $backUrl . "?cmd=".DUPConstants::$DUP_STEP_CHOOSE_TOOLS );
        die();

    }
    else
    {
        $dialogBox .= $targetCourse->backlog->output();
    }
}


//=================================
// Display section
//=================================

include get_path('incRepositorySys') . '/claro_init_header.inc.php';


echo claro_html_tool_title(get_lang('Create a Duplicata'));

if ( !empty($dialogBox) ) echo claro_html_message_box($dialogBox);

echo $targetCourse->displayForm($backUrl);

include get_path('incRepositorySys') . '/claro_init_footer.inc.php';
?>
