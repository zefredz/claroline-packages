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
require_once dirname( __FILE__ ) . '/../lib/item.class.php';

/*
 * Shared libraries
 */
include_once get_path('incRepositorySys') . '/lib/embed.lib.php';



/*
 * Output
 */
 
$display = new ClarolineScriptEmbed();
$display->frameMode();
$display->hideClaroBody();

$html = "\n" . '<div id="table_of_content">' . "\n" . '</div>' . "\n";

$display->setContent($html);

$display->output();
?>
