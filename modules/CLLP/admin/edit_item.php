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
	$error = false;
		
	if( !empty( $_REQUEST['title'] ) )
	{
		$item->setTitle( $_REQUEST['title'] );
	}
	else
	{
		$error = true;
	}
	if( !empty( $_REQUEST['description'] ) )
	{
		$item->setDescription( $_REQUEST['description'] );
	}
	else
	{
		$error = true;
	}
	if( !empty( $_REQUEST['completionThreshold'] ) )
	{
		$item->setCompletionThreshold( $_REQUEST['completionThreshold'] );
	}
	
	$_REQUEST['redirectBranchConditions'] = ( !empty( $_REQUEST['redirectBranchConditions'] ) ? 1 : 0);
	$item->setRedirectBranchConditions( $_REQUEST['redirectBranchConditions'] );
	
	$_REQUEST['newWindow'] = ( !empty( $_REQUEST['newWindow'] ) ? 1 : 0);
	$item->setNewWindow( $_REQUEST['newWindow'] );
	
	if( !empty( $_REQUEST['branchConditions'] ) )
	{
		$item->setBranchConditions( $_REQUEST['branchConditions'] );
	}
	else
	{
		$item->setBranchConditions();
	}

	if( $item->validate() && !$error )
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
ClaroBreadCrumbs::getInstance()->setCurrent( get_lang('Learning path'), '../admin/edit_path.php?pathId=' . $pathId . claro_url_relay_context('&amp;') );

//-- Content
$jsloader = JavascriptLoader::getInstance();
$jsloader->load('jquery');
$jsloader->load('jquery.json');

$jsloader->load('CLLP');

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
    $htmlEditForm = '<form action="' . $_SERVER['PHP_SELF'] . '?cmd=exEdit&pathId='.$pathId.'&itemId='.$itemId.'"  method="post">' . "\n"
		.		'<fieldset>' . "\n"
		.		'<legend>' . get_lang( 'Edit item' ) . '</legend>' . "\n"
		.		'<dl>' . "\n"
		.		'<dt><label for="title">' . get_lang( 'Title' ) . '&nbsp;:</label></dt>'
    .   '<dd><input type="text" name="title" id="title" value="' . htmlspecialchars( $item->getTitle() ) . '" style="width: 700px;" /></dd>' . "\n"
		.		'<dt><label for="description">' . get_lang( 'Description' ) . '&nbsp;:</label></dt>' . "\n"
		.		'<dd>' . "\n"
		.   '<div style="width: 700px;">' . claro_html_textarea_editor('description', $item->getDescription()) . '</div>' . "\n"
		.		'</dd>' . "\n"
		;
		
		if( $item->getType() != 'CONTAINER' )
		{
			$htmlEditForm .=		'<dt><label for="completionThreshold">' . get_lang( 'Completion threshold' ). '&nbsp;:</label></dt>'
			.		'<dd><input type="text" name="completionThreshold" id="completionThreshold" value="' . htmlspecialchars( $item->getCompletionThreshold() ) . '" style="width: 60px; text-align: right;" />%</dd>' . "\n"
			.		'<dt><label for="newWindow">' . get_lang( 'Open in a new window' ). '&nbsp;:</label></dt>'
			.		'<dd><input type="checkbox" id="newWindow" name="newWindow" value="1" ' . ( $item->getNewWindow() ? 'checked="checked"' : '' ) . ' />'
			.		'<dt><label for="branchConditions">' . get_lang( 'Branching conditions' ) . '&nbsp;:</label></dt>'
			.		'<dd id="branchConditions">'
			.		'<input type="checkbox" id="redirectBranchConditions" name="redirectBranchConditions" value="1" ' . ($item->getRedirectBranchConditions() ? 'checked="checked"' : '') . ' /> <label for="redirectBranchConditions">' . get_lang( 'Redirect automatically on the good branching condition' ) . '</label>' . "\n"		
			;
			
			$branchConditions = $item->getBranchConditions();
			if( is_array( $branchConditions ) && count( $branchConditions ) )
			{
				foreach( $branchConditions as $key => $branchCondition )
				{
					if(isset($branchCondition['sign']) && isset($branchCondition['value']) && isset($branchCondition['item']))
					{
						$htmlEditForm .= '<div style="padding: 2px;">'
						. get_lang('Score') . ' '
						.		'<select name="branchConditions[sign][]">' . "\n"
						.   '<option value="0"></option>' . "\n"
						.   '<option value="&#60;" '.($branchCondition['sign'] == '<' ? 'selected="selected"' : '' ).'>&#60;</option>' . "\n"
						.   '<option value="&#8804;" '.($branchCondition['sign'] == '&#8804;' ? 'selected="selected"' : '' ).'>&#8804;</option>' . "\n"
						.   '<option value="&#62;" '.($branchCondition['sign'] == '>' ? 'selected="selected"' : '' ).'>&#62;</option>' . "\n"
						.   '<option value="&#8805;" '.($branchCondition['sign'] == '&#8805;' ? 'selected="selected"' : '' ).'>&#8805;</option>' . "\n"
						.   '<option value="=">=</option>' . "\n"
						.   '</select>' . "\n"
						.		get_lang('to') . ' '
						.		'<input type="text" name="branchConditions[value][]" value="'.(int) $branchCondition['value'].'" style="width: 25px;" /> % ' . get_lang('go to') . ' ' . "\n"
						.   '<select name="branchConditions[item][]">'
						;
						$options = '<option value="0"></option>' . "\n";
			
						foreach( $itemListArray as $anItem )
						{
								$options .= '<option value="'. $anItem['id'] .'" style="padding-left:'.(5 + $anItem['deepness']*10).'px;" '.($anItem['type'] == 'CONTAINER' ? 'disabled="disabled"' : '').' '. ( $branchCondition['item'] == $anItem['id'] ? 'selected="selected"' : '') .'>'.$anItem['title'].'</option>' . "\n";
						}
						$htmlEditForm .= $options;
						$htmlEditForm .=		'</select>' . "\n"
						.	'<img src="'.get_icon_url('delete').'" alt="'.get_lang('Delete').'" title="' . get_lang('Delete') .'" onclick="$(this).parent().remove();" />' . "\n"
						.	'</div>' . "\n"
						;
					}				
				}
			}
			
			
			$htmlEditForm .= '<div><input type="button"id="branch_condition_button" value="' . get_lang( 'Add a branching condition' ) . '" onclick="addBranchCondition(' . $pathId .');" /></div>' . "\n"			
			;			
		}
		$htmlEditForm .= '</dd>' . "\n"		
		.	'</dl>' . "\n"
		.	'</fieldset>' . "\n"
		.	'<input type="submit" value="' . get_lang( 'Save' ) . '" />' . "\n"
		.   '</form> <br />' . "\n"
		;
    
    $out .= $htmlEditForm; 
}

$claroline->display->body->appendContent($out);

echo $claroline->display->render();
?>