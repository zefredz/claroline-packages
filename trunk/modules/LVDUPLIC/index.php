<?php
/**
 * TODO :
 * liste des cours
 * executer un php pour tool speciaux :  Handle problem of group forums
 * copier les infos des outils dupliqués (c_XXX_tool_list)
 * 
 * 
 *
 * @version 1.0.0
 *
 * @copyright (c) 2001-2009 Haute Ecole Léonard de Vinci
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package LVDUPLIC
 *
 * @author Systho <pve@ipl.be>
 * 
 *
 */

require_once '../../claroline/inc/claro_init_global.inc.php';
require_once get_path('incRepositorySys') . '/lib/claroCourse.class.php';
require_once 'lib/LVDUPLIC.lib.php';



// If you want to duplicate a course you need to be able to manage the source course and create a new one.
if ( ! claro_is_user_authenticated() ) claro_disp_auth_form();
if ( ! claro_is_allowed_to_create_course() ) claro_die(get_lang('Not allowed'));




$cmd = isset($_REQUEST['cmd']) ? $_REQUEST['cmd'] : null;
$out = '';
$dialogBox = '';

//STEP 0 reset
if (!isset($cmd) )
{
	DUPSessionMgr::clearDupDataFromSession();
	$cmd = DUPConstants::$DUP_STEP_DEFINE_SOURCE;
}
//STEP 1 memorize current course as source course
if(DUPConstants::$DUP_STEP_DEFINE_SOURCE == $cmd)
{
	$cid = isset($_REQUEST['cid'])?$_REQUEST['cid']:NULL;
	$isAdminContext = isset($_REQUEST['adminContext'])?$_REQUEST['adminContext']==1:false;
	if(!$isAdminContext)
	{
    	$cid = claro_get_current_course_id();
	}
    $source_course_data = claro_get_course_data($cid);
    //Bad or No CID
    if(! $source_course_data){
    	claro_die(get_lang('Unknown course'));
    }
    if($isAdminContext)
    {
    	DUPSessionMgr::setAdminContext(true);
    	$referer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:get_path('rootAdminWeb') . '/admincourses.php';
    	DUPSessionMgr::setAdminBackURL($referer);
    } 
    DUPSessionMgr::setSourceCourseData($source_course_data);    
    $cmd = DUPConstants::$DUP_STEP_DEFINE_TARGET;
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
	$source_course_data = DUPSessionMgr::getSourceCourseData();	
	$target_course_data = DUPSessionMgr::getTargetCourseData();
	$selected_tools = DUPSessionMgr::getToolList();
	$sucess = true;
	
	//Create target course
	$target_course = DUPSessionMgr::arrayToCourse($target_course_data);
	
	if(sucess && $target_course->save() )
   	{ 
    	$target_course->mailAdministratorOnCourseCreation($thisUser['firstName'], $thisUser['lastName'], $thisUser['mail']);
    	//save id
    	$target_course_data['sysCode'] = $target_course->courseId;
    } else	{
    	$dialogBox .= $target_course->backlog->output();
    	$sucess = false;
   	}
   	
   	//copy course managers
   	copy_course_managers($source_course_data, $target_course_data);
	
	//copy contents
   	$sourceCID = $source_course_data['sysCode'];    	
    $targetCID = $target_course_data['sysCode'];
   	foreach($selected_tools as $tool_label)
   	{
   		$tool_manager = new DUPToolManager($tool_label);
   		$tool_manager->copyTool($sourceCID,$targetCID );	
   	}          
    	
     
    
    
    //PREPARE DISPLAY
    if($sucess)
    {
    	
    	    	
	    $sourceCourseURL =  get_path('clarolineRepositoryWeb') . 'course/index.php?cid=' . htmlspecialchars($sourceCID) ;
	    $targetCourseURL =  get_path('clarolineRepositoryWeb') . 'course/index.php?cid=' . htmlspecialchars($targetCID) ;
	    
	    $link_array = array(  '%sourceCourseURL' => $sourceCourseURL
	                        , '%sourceCourseName' => $source_course_data['name']
	                        , '%targetCourseURL' => $targetCourseURL
	                        , '%targetCourseName' => $target_course_data['name']);
	    
	    $out .= '<H3>'.get_lang('Course has been Duplicated').'</H3>';
	    $out .= '<p>'. get_lang('Go Back to <a href="%sourceCourseURL">%sourceCourseName</a> 
	                                    or go to <a href="%targetCourseURL">%targetCourseName</a>'
	                                , $link_array).'</p>'."\n";
	    if(DUPSessionMgr::getAdminContext())
	    {
	    	$out .= '<p>' . get_lang(	 'Administation : Go Back to <a href="%backURL%"> Course List </a>'
	    								, array('%backURL%' => DUPSessionMgr::getAdminBackURL()));
	    }
    } else {
    	//TODO display if cannot save target course
    	//claro_html_message_box
    }
    
    DUPSessionMgr::clearDupDataFromSession();
    
}

//DISPLAY SECTION




// Display header
include get_path('includePath') . '/claro_init_header.inc.php' ;
if ( !empty($dialogBox) ) echo claro_html_message_box($dialogBox);

echo claro_html_tool_title(get_lang('Select tools'));

echo $out;

include get_path('includePath') . '/claro_init_footer.inc.php';







