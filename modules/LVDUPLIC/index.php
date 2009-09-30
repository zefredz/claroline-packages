<?php
/**
 * TODO 
 * - translation
 * - waiting screen 
 * - Step Chooser frame
 * 
 * DUPLICATION SCRIPT
 * 
 * this script allow the platform admin to duplicate a course with all its tools and data
 * The duplication of a specifi tool can be configured :
 * 	- in a config file which must be named like conf/[TOOL_LABEL].xml
 *  - a an php script which must be named like script/[TOOL_LABEL].php
 *  
 *  if none of these file is found, the tool will not be duplicated, it might still be present in the newly created course
 *  if it is a standard tool of the platform (a tool which is automatically created for all the new courses )
 * 
 * 
 * @version 1.0.0 
 *
 * @copyright (c) 2001-2009 Haute Ecole L�onard de Vinci
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package LVDUPLIC 
 *
 * @author Systho <pve@ipl.be>
 * 
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

$nameTools = get_lang('Duplication');


//=================================
// Main Section
//=================================


$cmd = isset($_REQUEST['cmd']) ? $_REQUEST['cmd'] : null;
$out = '';
$dialogBox = '';

//STEP 0 reset
if (!isset($cmd) )
{
	DUPSessionMgr::clearDupDataFromSession();
	$cmd = DUPConstants::$DUP_STEP_CHOOSE_SOURCE;
}
//STEP 1 memorize current course as source course
if(DUPConstants::$DUP_STEP_CHOOSE_SOURCE == $cmd)
{
	claro_redirect("choose_source.php");
	die();
}
//STEP 2 define target course data and memorize it
if (DUPConstants::$DUP_STEP_DEFINE_TARGET == $cmd)
{	
	claro_redirect("define_target.php");
	die();
}

//STEP 3 choose tools and contents to be copied
if( DUPConstants::$DUP_STEP_CHOOSE_TOOLS == $cmd )
{
	claro_redirect("choose_tools.php");
    die();	
}

//STEP 4 create target course, copy files & DB 
if( DUPConstants::$DUP_STEP_COPY_CONTENTS == $cmd )
{
	//gather data
	$sourceCourseData = DUPSessionMgr::getSourceCourseData();	
	$targetCourseData = DUPSessionMgr::getTargetCourseData();
	$selectedTools = DUPSessionMgr::getToolList();
	$success = true;
	
	//Create target course
	$targetCourse = DUPSessionMgr::arrayToCourse($targetCourseData);
	
	if($success && $targetCourse->save() )
   	{ 
   		$thisUser = claro_get_current_user_data();
    	$targetCourse->mailAdministratorOnCourseCreation($thisUser['firstName'], $thisUser['lastName'], $thisUser['mail']);
    	//save id
    	$targetCourseData = claro_get_course_data( $targetCourse->courseId);
    } 
    else	
    {
    	$dialogBox .= $targetCourse->backlog->output();
    	$success = false;
   	}
   	if($success)
   	{
	   	//copy course managers
	   	copy_course_managers($sourceCourseData, $targetCourseData);
	   	//copy course & tools intro
	   	copy_intro($sourceCourseData, $targetCourseData);
		
		//copy contents
	   	$sourceCID = $sourceCourseData['sysCode'];    	
	    $targetCID = $targetCourseData['sysCode'];
	   	foreach( $selectedTools as $toolLabel )
	   	{
	   		try
	   		{
	   			copy_tool( $toolLabel, $sourceCID, $targetCID );	
	   		}
	   		catch (Exception $exception)
	   		{
	   			$success = false;
	   			DUPLogger::log_error($toolLabel,$sourceCID, $exception->getMessage());
	   			$dialogBox .= $exception->getMessage();
	   			break;
	   		}
	   	}      
   	}        
    
    //PREPARE DISPLAY
    if( $success )
    { 	    	
	    $sourceCourseURL =  get_path('clarolineRepositoryWeb') . 'course/index.php?cid=' . htmlspecialchars($sourceCID) ;
	    $targetCourseURL =  get_path('clarolineRepositoryWeb') . 'course/index.php?cid=' . htmlspecialchars($targetCID) ;
	    
	    $linkArray = array(   '%sourceCourseURL' 	=> $sourceCourseURL
	                        , '%sourceCourseName' 	=> $sourceCourseData['name']
	                        , '%targetCourseURL' 	=> $targetCourseURL
	                        , '%targetCourseName' 	=> $targetCourseData['name']);
	    
	    $out .= '<H3>'.get_lang('Course has been Duplicated').'</H3>';
	    $out .= '<p>'. get_lang('Go Back to <a href="%sourceCourseURL">%sourceCourseName</a> 
	                                    or go to <a href="%targetCourseURL">%targetCourseName</a>'
	                                , $linkArray).'</p>'."\n";
	    $entryURL = get_module_entry_url('LVDUPLIC');
	    $out .= '<p>' . get_lang(	 'Administation : Go Back to <a href="%backURL%"> Course List </a>'
	    								, array('%backURL%' => $entryURL));
	    
    } 
    else 
    {
    	$out .= claro_html_message_box($dialogBox);
    }
    
    DUPSessionMgr::clearDupDataFromSession();
    
}

//=================================
// Display Section
//=================================


include get_path('includePath') . '/claro_init_header.inc.php' ;

echo $out;

include get_path('includePath') . '/claro_init_footer.inc.php';







