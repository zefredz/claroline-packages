<?php // $Id$
/**
 * CLAROLINE
 *
 * This  script  manage the choice of the tools which need to be copied
 * 
 * The tools Groups & Users will never be copied
 * 
 * If learnpath is chosen, so are documents and exercices
 * a Forum can be copied but only for the categories and forums, no posts
 * assignments tools will be emptied of all its submissions
 * 
 * 
 *
 * @version 1.0 
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @see http://www.claroline.net/wiki/CLCRS/
 *
 * @package LVDUPLIC
 *
 *
 * @author Systho
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
// Init
//=================================



$source_course_data = DUPSessionMgr::getSourceCourseData();
if(! isset($source_course_data))
{
    claro_redirect('index.php');
}
$target_course_data = DUPSessionMgr::getTargetCourseData();
if(! isset($target_course_data))
{
    claro_redirect('create_target.php');
}

//=================================
// Main section
//=================================


    $cmd = isset($_REQUEST['cmd']) ? $_REQUEST['cmd'] : null;
    $out = "";
    //list of all the available  tools
    $toolList = claro_get_course_tool_list($source_course_data['sysCode'],true,true);
    
    //display form
    //TODO use templates
    if(!isset($cmd))
    {
		$out .= claro_html_tool_title(get_lang('Select tools'));
	    $out .= '<H3>'.get_lang('Source').'</H3>';
	    $out .= '<p>'. $source_course_data['name'].'</p>'."\n";
	    $out .= '<H3>'.get_lang('Target').'</H3>';
	    $out .= '<p>'. $target_course_data['name'] .'</p>'."\n";
    
	    $out .= '<H3>'.get_lang('Select the tools that you want to copy').'</H3>';
	    $out .= '<p><ul>';
	    $out .= '<li>'.get_lang('If learnpath is selected, documents and exercises will be copied too').'</li>';
	    $out .= '<li>'.get_lang('Copying Groups or Users will have no effect').'</li>';
	    $out .= '<li>'.get_lang('Assignments will be emptied of all its submissions').'</li>';
	    $out .= '<li>'.get_lang('Forum will be emptied of all its posts (only the categories and forums will be copied)').'</li>';
	    $out .= '</ul></p>';
    
    
        //form 
	    $out .= '<p><form method="POST" name="tool_selection_form" ><input type="hidden" name="cmd" value="process_selection" /> '."\n";
	    foreach ($toolList as $toolItem) {
	    	 $label = $toolItem['label'];
	        
	        $out .= '<input type="checkbox"  id="check'.$toolItem['label'].'" value="'.$toolItem['label'].'" name="toolItem[]" checked="true" >';
	               
	        $out .= get_lang($toolItem['name']);
	        $out .= '</input><br/>'."\n";
	    }   
	    $out .= '<input type="submit" value="'.get_lang('Ok').'"/><br/>'."\n";
	    $out .='</form></p>'."\n";	    
	    
    	
    }
    if('process_selection' == $cmd)
    { 
    	$labelList = array();
    	foreach($toolList as $toolItem)
    	{
    		$labelList[] = $toolItem['label'];
    	}
    	$selected_tools =  array_intersect($labelList,$_REQUEST['toolItem']);
    	DUPSessionMgr::setToolList($selected_tools);
    	
    	claro_redirect('index.php?cmd='.DUPConstants::$DUP_STEP_COPY_CONTENTS);
    	die;    	
    }
    



//=================================
// Display section
//=================================


    // Display header
include get_path('includePath') . '/claro_init_header.inc.php' ;
//if ( !empty($dialogBox) ) echo claro_html_message_box($dialogBox);

echo $out;

include get_path('includePath') . '/claro_init_footer.inc.php';

?>
