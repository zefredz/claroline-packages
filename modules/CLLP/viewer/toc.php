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
$htmlHeaders = '<script type="text/javascript">' . "\n"
.	 '	var lpClient = window.parent.lpClient;' . "\n"
.	 '</script>' . "\n";

/*
 * Output
 */

$display = new ClarolineScriptEmbed();
$display->frameMode();
$display->hideClaroBody();

$html = "\n" . '<div id="table_of_content">' . "\n" . '</div>' . "\n";

$html .= '<a href="#" onClick="lpClient.isolateContent(); return false;" >debug</a>';
$display->setContent($html);

$display->addHtmlHeader($htmlHeaders);
$display->output();
?>
