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

require_once dirname( __FILE__ ) . '/../linker/linker.inc.php';

/*
 * init request vars
 */
$acceptedCmdList = array(   'exEdit'
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
	    header("Location: ../admin/edit_path.php?pathId=" . $pathId);
	    exit();
	}
}

$dialogBox = new DialogBox();

/*
 * Commands
 */

if( $cmd == 'exEdit' )
{
    $item->setTitle( $_REQUEST['title'] );
    
    if( $item->validate() )
    {
        if( $insertedId = $item->save() )
        {
            $dialogBox->success( get_lang('Item successfully modified') );
        }
        else
        {
            $dialogBox->error( get_lang('Fatal error : unable to save item') );
        }
    }
    else
    {
        if( claro_failure::get_last_failure() == 'item_no_title' )
        {
            $dialogBox->error( get_lang('Field \'%name\' is required', array('%name' => get_lang('Title'))) );
        }
        else
        {
            $dialogBox->error( get_lang('Fatal error : unable to save item') );
        }
    }
}

/*
 * Output
 */

ClaroBreadCrumbs::getInstance()->prepend( get_lang('Learning path list'), '../index.php' );
ClaroBreadCrumbs::getInstance()->prepend( get_lang('Learning path'), '../admin/edit_path.php?pathId=' . $pathId . claro_url_relay_context('&amp;') );



//-- Content

$out = '';

$nameTools = get_lang('Learning path');
$toolTitle['mainTitle'] = $nameTools;
$toolTitle['subTitle'] = '<a href="'.$_SERVER['PHP_SELF'] .'?pathId='. $pathId.'">' . htmlspecialchars($path->getTitle()) . '</a>'
.   '<br />'
.   '<a href="'. $_SERVER['PHP_SELF'] .'?pathId='. $pathId .'&itemId='. $itemId .'">' . htmlspecialchars($item->getTitle()) . '</a>';


$out .= claro_html_tool_title($toolTitle);

$out .= $dialogBox->render();

// prepare list to display
$itemList = new PathItemList($pathId);
$itemListArray = $itemList->getFlatList();

if( $item->load( $itemId ) )
{
    // edit title if type is container    
    $htmlEditTitleForm = '<strong>' . get_lang( 'Edit title' ) . '</strong>'. "\n"
    .   '<form action="' . $_SERVER['PHP_SELF'] . '?cmd=exEdit&pathId='.$pathId.'&itemId='.$itemId.'"  method="post">' . "\n"
    .   '<input type="text" name="title" value="' . htmlspecialchars( $item->getTitle() ) . '" /> <br />' . "\n"
    .   '<input type="submit" value="' . get_lang( 'Save' ) . '" />' . "\n"
    .   '</form> <br />' . "\n";
    
    $out .= $htmlEditTitleForm; 
}

$claroline->display->body->appendContent($out);

echo $claroline->display->render();
?>