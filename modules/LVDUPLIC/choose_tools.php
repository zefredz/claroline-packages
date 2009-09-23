<?php 
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

require_once dirname(__FILE__).'/../../claroline/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/lib/LVDUPLIC.lib.php';

//=================================
// Security check
//=================================

// If you want to duplicate a course you need to be able to be an administrator
if ( ! claro_is_user_authenticated() )       	claro_disp_auth_form();
if ( ! claro_is_platform_admin() ) 				claro_die( get_lang('Not allowed') );


//=================================
// Init
//=================================

$sourceCourseData = DUPSessionMgr::getSourceCourseData();
if(! isset($sourceCourseData))
{
    claro_redirect('index.php');
}
$targetCourseData = DUPSessionMgr::getTargetCourseData();
if(! isset($targetCourseData))
{
    claro_redirect('create_target.php');
}
$backUrl = get_module_entry_url('LVDUPLIC');

// Deal with interbredcrumps
$interbredcrump[]= array ('url' => get_module_entry_url('LVDUPLIC') , 'name' => get_lang('Duplication'));
$nameTools = get_lang('Choose Tools');

//=================================
// Main section
//=================================


    $cmd = isset($_REQUEST['cmd']) ? $_REQUEST['cmd'] : null;
    $out = "";
    //list of all the available  tools
    $toolList = claro_get_course_tool_list( $sourceCourseData['sysCode'], true, true );
    
    //display form
    //TODO use templates
    if(!isset($cmd))
    {
		$out .= claro_html_tool_title(get_lang('Select tools'));
	    $out .= '<H3>'.get_lang('Source').'</H3>';
	    $out .= '<p>'. $sourceCourseData['name'].'</p>'."\n";
	    $out .= '<H3>'.get_lang('Target').'</H3>';
	    $out .= '<p>'. $targetCourseData['name'] .'</p>'."\n";
    
	    $out .= '<H3>'.get_lang('Select the tools that you want to copy').'</H3>';
	    $out .= '<p><ul>';
	    $out .= '<li>'.get_lang('If learnpath is selected, documents and exercises will be copied too').'</li>';
	    $out .= '<li>'.get_lang('Copying Groups or Users will have no effect').'</li>';
	    $out .= '<li>'.get_lang('Assignments will be emptied of all its submissions').'</li>';
	    $out .= '<li>'.get_lang('Forum will be emptied of all its posts (only the categories and forums will be copied)').'</li>';
	    $out .= '</ul></p>';
    
    
        //form 
	    $out .= '<p><form method="POST" name="tool_selection_form" >' . "\n";
	    $out .= '<input type="hidden" name="cmd" value="process_selection" /> '."\n";
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
    	$selectedTools =  array_intersect($labelList,$_REQUEST['toolItem']);
    	DUPSessionMgr::setToolList($selectedTools);
    	
    	claro_redirect($backUrl . '?cmd='.DUPConstants::$DUP_STEP_COPY_CONTENTS);
    	die;    	
    }


//=================================
// Display section
//=================================


// Display header
include get_path('includePath') . '/claro_init_header.inc.php' ;

echo $out;

include get_path('includePath') . '/claro_init_footer.inc.php';

?>
