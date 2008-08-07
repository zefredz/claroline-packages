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
 * Shared libraries
 */


/*
 * init request vars
 */
$acceptedCmdList = array(   'rqEdit', 'exEdit',
							'rqAddItem', 'exAddItem',
							'rqAddContainer', 'exAddContainer',
                            'rqDelete', 'exDelete',
                            'rqPrereq', 'exPrereq',
                            'exVisible', 'exInvisible',
                            'rqMove', 'exMove', 'exMoveUp','exMoveDown'
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

	if( !is_null($pathId) )
	{
	    if( !$path->load($pathId) )
	    {
	        // path is required exept for creation
	        header("Location: ../index.php");
	    	exit();
	    }
	}
	else
	{
		// no pathId so force cmd to rqEdit but do not force if we are creating the path(exEdit)
		$cmd == 'exEdit' ? $cmd = 'exEdit': $cmd = 'rqEdit';
	}
}


$dialogBox = new DialogBox();

/*
 * Admin only page
 */

// obejct that will be used to handle the list of items (display and move of items...)
$itemList = new itemList();

/*
 * Commands that acts on or create an item require $item to be set
 */
$item = new item();

if( !is_null($itemId) )
{
    if( !$item->load($itemId) )
    {
        $itemId = null;
    }
}


if( $cmd == 'exEdit' )
{
	$path->setTitle($_REQUEST['title']);
	$path->setDescription($_REQUEST['description']);
	$path->setViewMode($_REQUEST['viewMode']);
	// if we edit do not change version, only change it if manual creation of a claroline path
	if( is_null($pathId) ) 
	{
	   $path->setVersion(Path::VERSION_CLAROLINE);
	}

	if( $path->validate() )
    {
        if( $insertedId = $path->save() )
        {
        	if( is_null($pathId) )
            {
                $dialogBox->success( get_lang('Empty learning path successfully created') );
                $pathId = $insertedId;
            }
            else
            {
            	$dialogBox->success( get_lang('Learning path successfully modified') );
            }
        }
        else
        {
            // sql error in save() ?
            $cmd = 'rqEdit';
        }

    }
    else
    {
        if( claro_failure::get_last_failure() == 'path_no_title' )
        {
            $dialogBox->error( get_lang('Field \'%name\' is required', array('%name' => get_lang('Title'))) );
        }
        $cmd = 'rqEdit';
    }
}

if( $cmd == 'rqEdit' )
{
	// show form
    $htmlEditForm = "\n\n";

    if( !is_null($pathId) )
    {
    	$htmlEditForm .= '<strong>' . get_lang('Edit learning path settings') . '</strong>' . "\n";
    	$cancelUrl = $_SERVER['PHP_SELF'] . '?pathId='.$pathId;
    }
    else
    {
    	$htmlEditForm .= '<strong>' . get_lang('Create a new learning path') . '</strong>' . "\n";
    	$cancelUrl = get_module_url('CLLP') . '/index.php';
    }

    $htmlEditForm .= '<form action="' . $_SERVER['PHP_SELF'] . '?pathId='.$pathId.'" method="post">' . "\n"
    .    claro_form_relay_context()
    .	 '<input type="hidden" name="claroFormId" value="'.uniqid('').'" />' . "\n"
    .	 '<input type="hidden" name="cmd" value="exEdit" />' . "\n"

    // title
    .	 '<label for="title">' . get_lang('Title') . '</label>&nbsp;<span class="required">*</span><br />' . "\n"
    .	 '<input type="text" name="title" id="title" maxlength="255" value="'.htmlspecialchars($path->getTitle()).'" /><br />' . "\n"
    // description
    .	 '<label for="title">' . get_lang('Description') . '</label><br />' . "\n"
    .	 '<textarea name="description" id="description" cols="50" rows="5">'.htmlspecialchars($path->getDescription()).'</textarea><br />'
    /*
    // allow reinit : TODO
    .	 get_lang('Allow reinit') . '&nbsp;<span class="required">*</span><br />' . "\n"
	.	 '<input type="radio" name="allowReinit" id="allowReinitYes" value="true">'
	.	 '<label for="allowReinitYes">'.get_lang('Yes').'</label><br />' . "\n"
	.	 '<input type="radio" name="allowReinit" id="allowReinitNo" value="false" >'
	.	 '<label for="allowReinitNo">'.get_lang('No').'</label>' . "\n"
	.	 '<br /><br />'
	*/
    // viewmode
    .	 get_lang('Default view mode') . '&nbsp;<span class="required">*</span><br />' . "\n"
	.	 '<input type="radio" name="viewMode" id="viewModeEmb" value="EMBEDDED" '.($path->isFullscreen()?'':'checked="checked"').'>'
	.	 '<label for="viewModeEmb">'.get_lang('Embedded').'</label><br />' . "\n"
	.	 '<input type="radio" name="viewMode" id="viewModeFull" value="FULLSCREEN" '.($path->isFullscreen()?'checked="checked"':'').'>'
	.	 '<label for="viewModeFull">'.get_lang('Fullscreen').'</label>' . "\n"
	.	 '<br /><br />'
    // charset : TODO

    .	 '<span class="required">*</span>&nbsp;'.get_lang('Denotes required fields') . '<br />' . "\n"
    .    '<input type="submit" value="' . get_lang('Ok') . '" />&nbsp;' . "\n"
    .    claro_html_button($cancelUrl, get_lang('Cancel'))
    .    '</form>' . "\n"
    ;

    $dialogBox->form($htmlEditForm);

}

if( $cmd == 'exAddItem' )
{
    if( isset($_REQUEST['itemList']) && is_array($_REQUEST['itemList']) && count($_REQUEST['itemList']) )
    {
        $i = 0;
        foreach( $_REQUEST['itemList'] as $itemToAdd )
        {
            // get title in path
            if( !empty($_REQUEST['titleList'][$i]) )
            {
                $title = substr($_REQUEST['titleList'][$i], strrpos($_REQUEST['titleList'][$i],'>') + 2);
            }
            else
            {
                $title = get_lang('No title');
            }

            $addedItem = new item();
          	$addedItem->setType('MODULE');
            $addedItem->setTitle($title);
            $addedItem->setPathId($pathId);
        	$addedItem->setSysPath(urldecode($itemToAdd));

        	if( $addedItem->validate() )
            {
                if( $addedItem->save() )
                {
                    $dialogBox->success( get_lang('Item "%itemTitle" successfully added', array('%itemTitle' => $title) ) );
                }
                else
                {
                    $dialogBox->error( get_lang('Fatal error, cannot save "%itemTitle"', array('%itemTitle' => $title) ) );
                }
            }

            $i++;
        }
    }
    else
    {
    	$dialogBox->error( get_lang('You didn\'t choose any ressource to add as item.') );
    	$cmd = 'rqAddItem';
    }
}

if( $cmd == 'rqAddItem' )
{
    //------------------------
    //linker
    linker_init_session();
    linker_html_head_xtra();


    $htmlAddItem = "\n\n"
    .    '<strong>' . get_lang('Add item(s)') . '</strong>' . "\n"
    .    '<form action="' . $_SERVER['PHP_SELF'] . '?pathId='.$pathId.'" method="post">' . "\n"
    .    claro_form_relay_context() . "\n"
    .    '<input type="hidden" name="claroFormId" value="'.uniqid('').'" />'."\n"
    .    '<input type="hidden" name="cmd" value="exAddItem" />' . "\n";

    if( claro_is_jpspan_enabled() )
    {
        linker_set_local_crl( isset ($_REQUEST['id']) );
        $htmlAddItem .= linker_set_display();

        $htmlAddItem .= '<input type="submit" onclick="linker_confirm();" class="claroButton" name="submitEvent" value="' . get_lang('Ok') . '" />'."\n";
    }
    else
    {
        if(isset($_REQUEST['id'])) $htmlAddItem .= linker_set_display($_REQUEST['id']);
        else                       $htmlAddItem .= linker_set_display();

        $htmlAddItem .= '<input type="submit" class="claroButton" name="submitEvent" value="' . get_lang('Ok') . '" />'."\n";
    }

    $htmlAddItem .= '<div id="lnk_panel">' 
    . '<div id="lnk_resources"></div>' . "\n" 
    . '<div id="lnk_selected_resources"></div>' . "\n" 
    . '<input type="submit" class="claroButton" name="submitEvent" value="' . get_lang('Ok') . '" />'."\n"
    . '</div>' . "\n";
    
    $htmlAddItem .= claro_html_button($_SERVER['PHP_SELF'] . '?pathId='.$pathId, get_lang('Cancel'))
    .    '</form>' . "\n";

    $dialogBox->form($htmlAddItem);
}

if( $cmd == 'exAddContainer' )
{
	$item->setType('CONTAINER');
    $item->setTitle($_REQUEST['title']);
    $item->setVisible();
    $item->setPathId($pathId);

    if( $item->validate() )
    {
        if( $newItemId = $item->save() )
        {
            $dialogBox->success( get_lang('Chapter successfully created') );
        }
        else
        {
            $dialogBox->error( get_lang('Fatal error : cannot save') );
        }
    }
    else
    {
    	$dialogBox->error( get_lang('Missing field : title is mandatory.') );
    	$cmd = 'rqAddContainer';
    }
}

if( $cmd == 'rqAddContainer' )
{
    $htmlAddContainer = "\n\n"
    .    '<strong>' . get_lang('Add a chapter') . '</strong>' . "\n"
    .    '<form action="' . $_SERVER['PHP_SELF'] . '?pathId='.$pathId.'" method="post">' . "\n"
    .    claro_form_relay_context()
    .    '<input type="hidden" name="claroFormId" value="'.uniqid('').'" />'."\n"
    .    '<label for="title">' . get_lang('Title') . ' : </label>' . "\n"
    .    '<br />' . "\n"
    .    '<input type="text" name="title" id="title" maxlength="255" value="' . htmlspecialchars($item->getTitle()). '" />' . "\n"
    .    '<br /><br />' . "\n"
    .    '<input type="hidden" name="cmd" value="exAddContainer" />' . "\n"
    .    '<input type="submit" value="' . get_lang('Ok') . '" />&nbsp;' . "\n"
    .    claro_html_button($_SERVER['PHP_SELF'] . '?pathId='.$pathId, get_lang('Cancel'))
    .    '</form>' . "\n";

    $dialogBox->form( $htmlAddContainer );
}


if( $cmd == 'exDelete' )
{
	if( $item->delete() )
	{
		$dialogBox->success( get_lang('Item succesfully deleted') );
	}
	else
	{
		$dialogBox->error( get_lang('Fatal error : cannot delete item') );
	}
}

if( $cmd == 'rqDelete' )
{

	$htmlConfirmDelete = get_lang('Are you sure to delete item "%itemTitle" ?', array('%itemTitle' => htmlspecialchars($item->getTitle()) ))
	.	 '<br /><br />'
    .    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exDelete&amp;pathId='.$pathId.'&amp;itemId='.$itemId.'">' . get_lang('Yes') . '</a>'
    .    '&nbsp;|&nbsp;'
    .    '<a href="' . $_SERVER['PHP_SELF'] . '?pathId='.$pathId.'">' . get_lang('No') . '</a>'
    ;

    $dialogBox->question( $htmlConfirmDelete );
}

if( $cmd == 'exMove' )
{
    // check that moved item is not itself or one descendant
    // liste complète, on extrait le noeud ou on doit mettre, on vérifie que l'item n'est pas dans ce noeud ou ses fils
    // change parent of item
    $item->setParentId($_REQUEST['newParentId']);

    // get new rank of item for this path
    $item->setHigherRank($path->getId());

    if( $item->validate() )
    {
        if( $item->save() )
        {
            $dialogBox->success( get_lang('Item succesfully moved') );
        }
        else
        {
            $dialogBox->error( get_lang('Fatal error : cannot move item') );
        }
    }

}

if( $cmd == 'rqMove' )
{
    // get list of available target : every container but not this one if this is a container too
    $containerList = new itemList();
    $containerListArray = $containerList->loadContainerList($pathId);

    $containerListTree = $containerList->buildTree($containerListArray);

    // add root to containerListTree
    $topElement['name'] = get_lang('Root');
    $topElement['value'] = -1;

    if (!is_array($containerListTree)) $containerListTree = array();
    array_unshift($containerListTree,$topElement);

    // show form
    $htmlRqMove = "\n\n"
    .    '<form action="' . $_SERVER['PHP_SELF'] . '?pathId='.$pathId.'" method="post">' . "\n"
    .    claro_form_relay_context()
    .    '<input type="hidden" name="claroFormId" value="'.uniqid('').'" />'."\n"
    .    '<input type="hidden" name="itemId" value="'.$itemId.'" / >'  . "\n"
    .    '<strong>' . get_lang('Move \'%itemName\' to ',array('%itemName' => $item->getTitle())) . '</strong>' . "\n"
    .    claro_build_nested_select_menu("newParentId",$containerListTree)
    .    '<br /><br />' . "\n"
    .    '<input type="hidden" name="cmd" value="exMove" />' . "\n"
    .    '<input type="submit" value="' . get_lang('Ok') . '" />&nbsp;' . "\n"
    .    claro_html_button($_SERVER['PHP_SELF'] . '?pathId='.$pathId, get_lang('Cancel'))
    .    '</form>' . "\n";

    $dialogBox->form( $htmlRqMove );
}

if( $cmd == 'exMoveUp' )
{
    $itemList->moveItemUp($item,$path);
}

if( $cmd == 'exMoveDown' )
{
    $itemList->moveItemDown($item,$path);
}

if( $cmd == 'exPrereq' )
{
	// check save prerequisites

	$item->save();
}

if( $cmd == 'rqPrereq' )
{
    // show prerequisites form

}

if( $cmd == 'exVisible' )
{
	$item->setVisible();

	$item->save();
}

if( $cmd == 'exInvisible' )
{
	$item->setInvisible();

	$item->save();
}




// prepare list to display
$itemListArray = $itemList->getFlatList($pathId);

/*
 * Output
 */

ClaroBreadCrumbs::getInstance()->prepend( get_lang('Learning path list'), '../index.php'.claro_url_relay_context('?') );
ClaroBreadCrumbs::getInstance()->setCurrent( get_lang('Learning path'), './edit_path.php?pathId='.$pathId.claro_url_relay_context('&amp;') );
//-- Content
$out = '';

$nameTools = get_lang('Learning path');
$toolTitle['mainTitle'] = $nameTools;
$toolTitle['subTitle'] = htmlspecialchars($path->getTitle());

$out .= claro_html_tool_title($toolTitle);

$out .= $dialogBox->render();

$cmdMenu = array();
// do not display commands to student or when creating a new path (rqEdit)
if( $is_allowedToEdit && !is_null($pathId) )
{
	$cmdMenu[] = claro_html_cmd_link('../viewer/index.php?pathId=' . $pathId . claro_url_relay_context('&amp;'), '<img src="' . get_icon_url('play') . '" border="0" alt="" />' . get_lang('Play path'));
    $cmdMenu[] = claro_html_cmd_link($_SERVER['PHP_SELF'].'?cmd=rqEdit&amp;pathId=' . $pathId . claro_url_relay_context('&amp;'), '<img src="' . get_icon_url('edit') . '" border="0" alt="" />' . get_lang('Edit path settings'));
    $cmdMenu[] = claro_html_cmd_link($_SERVER['PHP_SELF'].'?cmd=rqAddContainer&amp;pathId=' . $pathId . claro_url_relay_context('&amp;'), '<img src="' . get_icon_url('chapter_add') . '" border="0" alt="" />' . get_lang('Add chapter'));
    $cmdMenu[] = claro_html_cmd_link($_SERVER['PHP_SELF'].'?cmd=rqAddItem&amp;pathId=' . $pathId . claro_url_relay_context('&amp;'), '<img src="' . get_icon_url('item_add') . '" border="0" alt="" />' . get_lang('Add item(s)'));
}

$out .= '<p><small>' . htmlspecialchars($path->getDescription()). '</small></p>' . "\n";
$out .= '<p>'
.    claro_html_menu_horizontal( $cmdMenu )
.    '</p>'
;

$out .= '<table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">' . "\n"
.    '<thead>' . "\n"
.    '<tr class="headerX" align="center" valign="top">' . "\n"
.	 '<th>' . get_lang('Item') . '</th>' . "\n"
.	 '<th>' . get_lang('Modify') . '</th>' . "\n"
.	 '<th>' . get_lang('Delete') . '</th>' . "\n"
.	 '<th>' . get_lang('Prerequisites') . '</th>' . "\n"
.	 '<th>' . get_lang('Visibility') . '</th>' . "\n"
.	 '<th>' . get_lang('Move') . '</th>' . "\n"
.	 '<th colspan="2">' . get_lang('Order') . '</th>' . "\n"
.    '</tr>' . "\n"
.    '</thead>' . "\n";

if( !empty($itemListArray) && is_array($itemListArray) )
{
    $out .= '<tbody>' . "\n";

    foreach( $itemListArray as $anItem )
    {
        $out .= '<tr align="center"' . (($anItem['visibility'] == 'INVISIBLE')? 'class="invisible"': '') . '>' . "\n";

        // title
        $out .= '<td align="left" style="padding-left:'.(5 + $anItem['deepness']*10).'px;">'
        .    '<img src="'.(($anItem['type'] == 'CONTAINER')? get_icon_url('chapter'): get_icon_url('item')).'" alt="" />'
        .    '&nbsp;' . $anItem['title']
        .    '</td>' . "\n";

        // edit
        $out .= '<td>' . "\n"
	    .    '<a href="./edit_item.php?pathId=' . $pathId . '&amp;itemId='.$anItem['id'].'">' . "\n"
	    .    '<img src="' . get_icon_url('edit') . '" border="0" alt="' . get_lang('Modify') . '" />' . "\n"
	    .    '</a>'
	    .    '</td>' . "\n";

        // delete
        $out .= '<td>' . "\n"
     	.    '<a href="'.$_SERVER['PHP_SELF'].'?cmd=rqDelete&amp;pathId=' . $pathId . '&amp;itemId='.$anItem['id'].'">' . "\n"
	    .    '<img src="' . get_icon_url('delete') . '" border="0" alt="' . get_lang('Delete') . '" />' . "\n"
    	.    '</a>'
    	.    '</td>' . "\n";

        // prerequisites
		$out .= '<td>' . "\n"
		.    '<a href="'.$_SERVER['PHP_SELF'].'?cmd=rqPrereq&amp;pathId=' . $pathId . '&amp;itemId='.$anItem['id'].'">' . "\n"
		.    '<img src="' . get_icon_url('unblock') . '" border="0" alt="' . get_lang('Unblock') . '" />' . "\n"
		.    '</a>'
		.    '</td>' . "\n";

        // visible/invisible
        if( $anItem['visibility'] == 'VISIBLE' )
        {
        	$out .= '<td>' . "\n"
      		.    '<a href="'.$_SERVER['PHP_SELF'].'?cmd=exInvisible&amp;pathId=' . $pathId . '&amp;itemId='.$anItem['id'].'">' . "\n"
      		.    '<img src="' . get_icon_url('visible') . '" border="0" alt="' . get_lang('Make invisible') . '" />' . "\n"
      		.    '</a>'
      		.    '</td>' . "\n";
        }
        else
        {
			$out .= '<td>' . "\n"
      		.    '<a href="'.$_SERVER['PHP_SELF'].'?cmd=exVisible&amp;pathId=' . $pathId . '&amp;itemId='.$anItem['id'].'">' . "\n"
      		.    '<img src="' . get_icon_url('invisible') . '" border="0" alt="' . get_lang('Make visible') . '" />' . "\n"
      		.    '</a>'
      		.    '</td>' . "\n";
        }

        $out .= '<td>' . "\n"
   		.    '<a href="'.$_SERVER['PHP_SELF'].'?cmd=rqMove&amp;pathId=' . $pathId . '&amp;itemId='.$anItem['id'].'">' . "\n"
  		.    '<img src="' . get_icon_url('move') . '" border="0" alt="' . get_lang('Move') . '" />' . "\n"
     	.    '</a>'
     	.    '</td>' . "\n";

        // order
        if( $anItem['canMoveUp'] )
        {
            $out .= '<td>' . "\n"
       		.    '<a href="'.$_SERVER['PHP_SELF'].'?cmd=exMoveUp&amp;pathId=' . $pathId . '&amp;itemId='.$anItem['id'].'">' . "\n"
      		.    '<img src="' . get_icon_url('move_up') . '" border="0" alt="' . get_lang('Move up') . '" />' . "\n"
         	.    '</a>'
         	.    '</td>' . "\n";
        }
        else
        {
            $out .= '<td>&nbsp;</td>' . "\n";
        }

        if( $anItem['canMoveDown'] )
        {
            $out .= '<td>' . "\n"
       		.    '<a href="'.$_SERVER['PHP_SELF'].'?cmd=exMoveDown&amp;pathId=' . $pathId . '&amp;itemId='.$anItem['id'].'">' . "\n"
      		.    '<img src="' . get_icon_url('move_down') . '" border="0" alt="' . get_lang('Move down') . '" />' . "\n"
         	.    '</a>'
         	.    '</td>' . "\n";
        }
        else
        {
            $out .= '<td>&nbsp;</td>' . "\n";
        }

        $out .= '</tr>' . "\n\n";
    }

    $out .= '</tbody>' . "\n";
}
else
{
    $out .= '<tfoot>' . "\n"
    .    '<tr>' . "\n"
    .    '<td align="center" colspan="8">' . get_lang('No item') . '</td>' . "\n"
    .    '</tr>' . "\n"
    .    '</tfoot>' . "\n";
}

$out .= '</table>' . "\n";

$claroline->display->body->appendContent($out);

echo $claroline->display->render();
?>