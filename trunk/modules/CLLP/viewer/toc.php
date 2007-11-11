<?php // $Id$
/**
 * CLAROLINE
 *
 * @version 0.1 $Revision: 245 $
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
require_once dirname( __FILE__ ) . '/../lib/item.class.php';

/*
 * Shared libraries
 */
include_once get_path('incRepositorySys') . '/lib/embed.lib.php';

/*
 * init request vars
 */
if( isset($_REQUEST['pathId']) && is_numeric($_REQUEST['pathId']) )   $pathId = (int) $_REQUEST['pathId'];
else                                                                  $pathId = null;


// prepare html header
$htmlHeaders = '<link rel="stylesheet" type="text/css" href="' . get_module_url('CLLP') . '/css/cllp.css" media="screen, projection, tv" />' . "\n"
.	 '<script type="text/javascript">' . "\n"
.	 '	var lpHandler = window.parent.lpHandler;' . "\n"
.	 '</script>' . "\n";

// to fix png transparency problem using IE < 7
$htmlHeaders .= '<!--[if lt IE 7]>' . "\n"
.	 '<script defer type="text/javascript" src="pngfix.js"></script>' . "\n"
.	 '<![endif]-->' . "\n";

/*
 * Output
 */
$claroline->setDisplayType( CL_PAGE );

$html = '';

// Navigation
$html .= "\n" . '<div id="navigation">' . "\n";

//- previous and next links
$html .= '';


$html .= '<p>' . "\n"
//- back to list
.    '<a href="'.get_module_url('CLLP').'/index.php" title="'.get_lang('Back to list').'" target="_top">'
.    '<img src="'.get_module_url('CLLP').'/img/go-home.png" alt="'.get_lang('Back to list').'" />'
.    '</a>' . "\n"
//- tracking
.	 '&nbsp;&nbsp;'
.    '<a href="#" title="'.get_lang('View statistics').'" onClick="lpHandler.openStatistics(); return false;">'
.    '<img src="'.get_module_url('CLLP').'/img/statistics.gif" alt="'.get_lang('View statistics').'" />'
.    '</a>' . "\n"
//- previous and next buttons
.	 '&nbsp;&nbsp;'
.    '<a href="#" title="'.get_lang('Previous').'" onClick="lpHandler.goPrevious(); return false;" id="goPrevious">'
.    '<img src="'.get_module_url('CLLP').'/img/go-previous.png" alt="'.get_lang('Previous').'" />'
.    '</a>' . "\n"
.    '<a href="#" title="'.get_lang('Next').'" onClick="lpHandler.goNext(); return false;" id="goNext">'
.    '<img src="'.get_module_url('CLLP').'/img/go-next.png" alt="'.get_lang('Next').'" />'
.    '</a>' . "\n"
//- full screen switch
.	 '&nbsp;&nbsp;'
.    '<a href="#" title="'.get_lang('Fullscreen').'" onClick="lpHandler.setFullscreen(); return false;">'
.    '<img src="'.get_module_url('CLLP').'/img/view-fullscreen.png" alt="'.get_lang('Fullscreen').'" />'
.    '</a>' . "\n"
.    '<a href="#" title="'.get_lang('Embedded').'" onClick="lpHandler.setEmbedded(); return false;">'
.    '<img src="'.get_module_url('CLLP').'/img/view-embedded.png" alt="'.get_lang('Embedded').'" />'
.    '</a>' . "\n"

.    '</p>' . "\n\n";

// progression
$html .= '<p>' . claro_html_progress_bar(46, 1) . '&nbsp;<a href="#" onClick="lpHandler.isolateContent();return false;" >46%</a></p>' . "\n";

$html .= '</div>' . "\n";

// table of content
$html .= "\n" . '<div id="table_of_content">' . "\n" . '</div>' . "\n";

// debug messages
$html .= "\n"
.   '<div id="lp_debug">' ."\n"
.   '</div>' . "\n\n";


// output
$claroline->display->banner->hide();
$claroline->display->body->hideClaroBody();
$claroline->display->footer->hide();

$claroline->display->body->setContent($html);

$claroline->display->header->addHtmlHeader($htmlHeaders);


echo $claroline->display->render();
?>
