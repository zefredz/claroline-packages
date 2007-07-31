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

if( isset($_REQUEST['view']) )   $rqFullscreen = ($_REQUEST['view'] == 'fullscreen')? true: false;
else                             $rqFullscreen = false;

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

$fullScreen = $path->isFullScreen() || ( isset($rqFullscreen) && $rqFullscreen );

$thisAttempt = new attempt();
$thisAttempt->load($pathId, claro_get_current_user_id());

$_SESSION['thisAttempt'] = serialize($thisAttempt);

/*
 * Output
 */

// prepare frames and framesets
$menuFrameset = new ClaroFrameset();

// menu frames
$menuFrameToc = new ClaroFrame('lp_toc', 'toc.php?pathId='.$pathId);
$menuFrameToc->allowScrolling(true);
$menuFrameToc->noFrameBorder();
$menuFrameNav = new ClaroFrame('lp_nav', 'navigation.php?pathId='.$pathId);
$menuFrameNav->allowScrolling(true);
$menuFrameNav->noFrameBorder();

$menuFrameset->addRow($menuFrameToc, '*');
$menuFrameset->addRow($menuFrameNav, '100');


// content frame
$contentFrame = new ClaroFrame('lp_content', 'blank.htm');
$contentFrame->allowScrolling(true);
$contentFrame->noFrameBorder();

// prepare inner frameset that contains
$innerFrameset = new ClaroFrameSet();
$innerFrameset->addCol($menuFrameset, '200');
$innerFrameset->addCol($contentFrame, '*');

// prepare html header
$htmlHeaders = getViewerHtmlHeaders($pathId);

$htmlHeaders .= "\n"
.    '<script type="text/javascript">' . "\n"
.	 '  $(document).ready(function() {' . "\n"
.    '    setTimeout("refreshToc()", 1000);' . "\n"
.	 '  });' . "\n"
.    '</script>' . "\n\n";

if( !$fullScreen )
{
    // create outer frameset
    $outerFrameset = new ClaroFrameset();

    // header frame
    $headerFrame = new ClaroFrame('lp_header', 'header.php?pathId='.$pathId);
    $headerFrame->noFrameBorder();

    // add header frame and inner frameset
    $outerFrameset->addRow($headerFrame, '150');
    $outerFrameset->addRow($innerFrameset, '*');

    $outerFrameset->addHtmlHeader($htmlHeaders);
    // output outer frameset with inner frameset within in embedded mode
    $outerFrameset->output();
}
else
{
    $innerFrameset->addHtmlHeader($htmlHeaders);
    // output inner frameset only in fullscreen mode
    $innerFrameset->output();
}



?>
