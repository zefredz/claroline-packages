<?php // $Id$
/**
 * CLAROLINE
 *
 * @version 0.1 $Revision$
 *
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLLP
 *
 * @author Sebastien Piraux
 *
 */

$tlabelReq = 'CLLP';

require_once dirname( __FILE__ ) . '/../../../claroline/inc/claro_init_global.inc.php';

if ( !claro_is_tool_allowed() )
{
	if ( claro_is_in_a_course() )
	{
		claro_die( get_lang( "Not allowed" ) );
	}
    else
	{
		claro_disp_auth_form( true );
	}
}


/*
 * Tool libraries
 */
require_once dirname( __FILE__ ) . '/../lib/CLLP.lib.php';
require_once dirname( __FILE__ ) . '/../lib/path.class.php';
require_once dirname( __FILE__ ) . '/../lib/attempt.class.php';

/*
 * Shared libraries
 */
include_once get_path('incRepositorySys') . '/lib/embed.lib.php';
require_once get_path('clarolineRepositorySys') . '/linker/resolver.lib.php';

/*
 * init request vars
 */
if( isset($_REQUEST['pathId']) && is_numeric($_REQUEST['pathId']) )   $pathId = (int) $_REQUEST['pathId'];
else                                                                  $pathId = null;


/*
 * init other vars
 */

// admin only page and path is required as we edit a path ...
if( is_null($pathId) )
{
	header("Location: ../index.php");
	exit();
}
else
{
    $path = new path();

    if( !$path->load($pathId) )
    {
        // path is required
        header("Location: ../index.php");
    	exit();
    }
}

claro_set_display_mode_available(false);

$thisAttempt = new attempt();

if( !$thisAttempt->load($pathId, claro_get_current_user_id()) )
{
	// save the attempt as there is no attempt for this user yet
	$thisAttempt->setPathId($pathId);
	$thisAttempt->setUserId(claro_get_current_user_id());
	$thisAttempt->save();
}

$_SESSION['thisAttempt'] = serialize($thisAttempt);

/*
 * Output
 */


// prepare html header
$htmlHeaders = "\n"
.     '<script type="text/javascript">' . "\n"
.    '  var jQueryPath = "'.get_module_url('CLLP').'/js/jquery.js";' . "\n"
.    '</script>' . "\n\n"
.    '<script type="text/javascript" src="'.get_module_url('CLLP').'/js/jquery.js"></script>' . "\n"
.    '<script type="text/javascript" src="'.get_module_url('CLLP').'/js/jquery.frameready.js"></script>' . "\n"
.    '<script type="text/javascript" src="'.get_module_url('CLLP').'/js/json.jquery.js"></script>' . "\n"
.    '<script type="text/javascript" src="'.get_module_url('CLLP').'/js/CLLP.js"></script>' . "\n"
.    '<script type="text/javascript" src="'.get_module_url('CLLP').'/js/scormAPI.js"></script>' . "\n\n";

$htmlHeaders .= "\n"
.    '<script type="text/javascript">' . "\n"
.	 '  var pathId = "'.(int) $pathId.'";' . "\n"
.	 '  var cidReq = "'.claro_get_current_course_id().'";' . "\n"
.	 '  var moduleUrl = "'.get_module_url('CLLP').'/";' . "\n"
.    '  var debugMode = '.get_conf('scorm_api_debug').';' . "\n\n"
.	 '  var lpHandler = new lpHandler(pathId,cidReq,moduleUrl,debugMode);' . "\n"
.	 '  $(document).ready(function() {' . "\n"
.    '    setTimeout("lpHandler.refreshToc()", 900);' . "\n"
.    '    setTimeout("lpHandler.setContent(' .$thisAttempt->getLastItemId().')", 1000);' . "\n"
.	 '  });' . "\n"
.    '</script>' . "\n\n";


// prepare frames and framesets

// menu frames
$tocFrame = new ClaroFrame('lp_toc', 'toc.php?pathId='.$pathId);
$tocFrame->allowScrolling(true);
$tocFrame->noFrameBorder();


// content frame
$contentFrame = new ClaroFrame('lp_content', 'blank.htm');
$contentFrame->allowScrolling(true);
$contentFrame->noFrameBorder();

// inner frameset that contains toc and content frames
$innerFrameset = new ClaroFrameSet();
$innerFrameset->addCol($tocFrame, '200');
$innerFrameset->addCol($contentFrame, '*');

// outer frameset that contains header frame and inner frameset
$outerFrameset = new ClaroFrameset();

// header frame
$headerFrame = new ClaroFrame('lp_header', 'header.php?pathId='.$pathId);
$headerFrame->noFrameBorder();

// add header frame and inner frameset
$headerFrameSize = $path->isFullScreen()?'0':'150';

$outerFrameset->addRow($headerFrame, $headerFrameSize);
$outerFrameset->addRow($innerFrameset, '*');

$outerFrameset->addHtmlHeader($htmlHeaders);
// output outer frameset with inner frameset within in embedded mode
$outerFrameset->output();


?>