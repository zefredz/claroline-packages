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
require_once dirname( __FILE__ ) . '/../lib/path.class.php';

/*
 * Shared libraries
 */
include_once get_path('incRepositorySys') . '/lib/embed.lib.php';

/*
 * init request vars
 */
if( isset($_REQUEST['pathId']) && is_numeric($_REQUEST['pathId']) )   $pathId = (int) $_REQUEST['pathId'];
else                                                                  $pathId = null;

/*
 * Output
 */
 
$display = new ClarolineScriptEmbed();
$display->frameMode();
$display->hideClaroBody();

$html = '';

$html .= '<center>' . "\n";

// previous and next links
$html .= '';

// full screen switch
$html .= '<p>' . "\n"
.    '<small>' . "\n"
.    '<a href="index.php?pathId='.$pathId.'&amp;view=fullscreen" title="'.get_lang('Fullscreen').'" target="_top">'
.    '<img src="'.get_module_url('CLLP').'/img/view-fullscreen.png" alt="'.get_lang('Fullscreen').'" />'
.    '</a>' . "\n"
.    '<a href="index.php?pathId='.$pathId.'&amp;view=embedded" title="'.get_lang('In frames').'" target="_top">'
.    '<img src="'.get_module_url('CLLP').'/img/view-embedded.png" alt="'.get_lang('In frames').'" />'
.    '</a>' . "\n"
.    '</small>' . "\n"
.    '</p>' . "\n\n";

// back to list link
$html .= '<p><a href="'.get_module_url('CLLP').'/index.php" class="claroCmd" target="_top">'.get_lang('Back to list').'</a></p>' . "\n";

$html .= '</center>' . "\n";

// debug messages
$html .= "\n"
.   '<div id="lp_debug">' ."\n"
.   '</div>' . "\n\n";

$display->setContent($html);

$display->output();
?>
