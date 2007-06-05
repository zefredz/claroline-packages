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
require_once get_path('clarolineRepositorySys') . '/linker/resolver.lib.php';

/*
 * init request vars
 */
if( isset($_REQUEST['pathId']) && is_numeric($_REQUEST['pathId']) )   $pathId = (int) $_REQUEST['pathId'];
else                                                                  $pathId = null;

/*
 * init other vars
 */

claro_set_display_mode_available(true);

$is_allowedToEdit = claro_is_allowed_to_edit();

// admin only page and path is required as we edit a path ...
if( !$is_allowedToEdit || is_null($pathId) )
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

$resolver = new Resolver(get_path('rootWeb'));



// prepare list to display
$itemList = new itemList();

$itemListArray = $itemList->getFlatList($pathId);

/*
 * Output
 */
 
$display = new ClarolineScriptEmbed();
$display->frameMode();
$display->hideClaroBody();

$display->addHtmlHeader('<script type="text/javascript" src="'.get_module_url('CLLP').'/js/jquery.js"></script>');
$display->addHtmlHeader('<script type="text/javascript">function hidetest(){ top.frames["lp_content"].document.location ="http://localhost/~seb/claroline/claroline/claroline/exercise/exercise_submit.php?exId=2&cidReq=LPTEST"; }</script>');

$html = "\n" . '<div id="table_of_content">dummy' . "\n" . '</div>' . "\n";

$display->setContent($html);

$display->output();
?>
