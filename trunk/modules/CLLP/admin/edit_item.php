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

/*
 * Prerequisites :
 * Another item must be at least "viewed", "passed", "completed",... for this item to be available
 *
 * We could have several items requirements
 * We will need to allow user to choose any module and to set its condition
 *
 *
 * Do not forget possible closed loop ... (A need B to be finished, B need C, C need A)
 *
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

require_once dirname( __FILE__ ) . '/../linker/linker.inc.php';

/*
 * init request vars
 */
$acceptedCmdList = array(   'rqEdit', 'exEdit',
                            'rqPrereq', 'exPrereq'
                    );

if( isset($_REQUEST['cmd']) && in_array($_REQUEST['cmd'], $acceptedCmdList) )   $cmd = $_REQUEST['cmd'];
else                                                                            $cmd = null;

if( isset($_REQUEST['pathId']) && is_numeric($_REQUEST['pathId']) )   $pathId = (int) $_REQUEST['pathId'];
else                                                                  $pathId = null;

if( isset($_REQUEST['itemId']) && is_numeric($_REQUEST['itemId']) )   $itemId = (int) $_REQUEST['itemId'];
else                                                                  $itemId = null;


/*
 * init other vars
 */

claro_set_display_mode_available(false);

$is_allowedToEdit = claro_is_allowed_to_edit();

// admin only page and path is required as we edit a path ...
if( !$is_allowedToEdit )
{
	header("Location: ../index.php");
	exit();
}
else
{
	$path = new path();

	if( is_null($pathId) || !$path->load($pathId) )
	{
	    // path is required
	    header("Location: ../index.php");
	    exit();
	}

	$item = new item();

	if( is_null($itemId) || !$item->load($itemId) )
	{
	    // item is required
	    header("Location: ./admin/edit_path.php?pathId" . $pathId);
	    exit();
	}
}


$dialogBox = new DialogBox();

/*
 * Commands
 */

/*
 * Output
 */

$interbredcrump[]= array ('url' => '../index.php' . claro_url_relay_context('?'), 'name' => get_lang('Learning path list'));
$interbredcrump[]= array ('url' => './admin/edit_path.php?pathId=' . $pathId . claro_url_relay_context('&amp;'), 'name' => get_lang('Learning path'));

//-- Content
$out = '';

$nameTools = get_lang('Learning path');
$toolTitle['mainTitle'] = $nameTools;
$toolTitle['subTitle'] = htmlspecialchars($path->getTitle()) . '<br />' . htmlspecialchars($item->getTitle());


$out .= claro_html_tool_title($toolTitle);

$out .= $dialogBox->render();




$claroline->display->body->appendContent($out);

echo $claroline->display->render();
?>