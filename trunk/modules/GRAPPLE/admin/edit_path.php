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
 * @package GRAPPLE
 *
 * @author Sebastien Piraux
 *
 */

$tlabelReq = 'GRAPPLE';

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
 * Shared libraries
 */
require_once get_conf( 'includePath' ) . '/lib/core/linker.lib.php';

/*
 * Tool libraries
 */
require_once dirname( __FILE__ ) . '/../lib/path.class.php';
require_once dirname( __FILE__ ) . '/../lib/item.class.php';
require_once dirname( __FILE__ ) . '/../lib/linker.lib.php';
require_once dirname( __FILE__ ) . '/../lib/blockingcondition.class.php';

require_once dirname( __FILE__ ) . '/../lib/grapple.class.php';


/*
 * init request vars
 */
$acceptedCmdList = array(   'rqEdit', 'exEdit',
                            'rqAddItem', 'exAddItem',
                            'rqAddContainer', 'exAddContainer',
                            'rqDelete', 'exDelete',
                            'rqPrereq', 'exPrereq', 'rqDeletePrereq', 'exDeletePrereq',
                            'exVisible', 'exInvisible',
                            'rqMove', 'exMove', 'exMoveUp','exMoveDown',
                            'rqGrappleCoursesList', 'exGrappleCoursesList'
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


/*
 * Commands that acts on or create an item require $item to be set
 */
// prepare list to display
$itemList = new PathItemList($pathId);
$itemListArray = $itemList->getFlatList();

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
    
    if( $path->validate() )
    {
        if( $insertedId = $path->save() )
        {
            if( is_null($pathId) )
            {
                $dialogBox->success( get_lang('Empty learning path successfully created') );
                $pathId = $insertedId;
                
                // Contact GEB for the learningActivityAddition
                $grapple = new grapple;
                if( isset( $_SESSION[ 'grapple' ][ 'previousGEBId' ] ) )
                {
                    $grapple_idAssignedEvent = (int) $_SESSION[ 'grapple' ][ 'previousGEBId' ];
                }
                else
                {
                    $grapple_idAssignedEvent = 0;
                }
                
                if( $data = $grapple->learningActivityAddition( claro_get_current_user_id(), claro_get_current_course_id(), $pathId,  $grapple_idAssignedEvent ) )
                {
                  $grapple_idAssignedEvent = $data->idAssignedEvent;
                  $_SESSION[ 'grapple' ][ 'previousGEBId' ] = $grapple_idAssignedEvent;
                }
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
        $cancelUrl = get_module_url('GRAPPLE') . '/index.php';
    }

    $htmlEditForm .= '<form action="' . $_SERVER['PHP_SELF'] . '?pathId='.$pathId.'" method="post">' . "\n"
    .    claro_form_relay_context()
    //.     '<input type="hidden" name="claroFormId" value="'.uniqid('').'" />' . "\n"
    .     '<input type="hidden" name="cmd" value="exEdit" />' . "\n"

    // title
    .     '<label for="title">' . get_lang('Title') . '</label>&nbsp;<span class="required">*</span><br />' . "\n"
    .     '<input type="text" name="title" id="title" maxlength="255" value="'.htmlspecialchars($path->getTitle()).'" /><br />' . "\n"
    // description
    .     '<label for="title">' . get_lang('Description') . '</label><br />' . "\n"
    .     '<textarea name="description" id="description" cols="50" rows="5">'.htmlspecialchars($path->getDescription()).'</textarea><br />'
    /*
    // allow reinit : TODO
    .     get_lang('Allow reinit') . '&nbsp;<span class="required">*</span><br />' . "\n"
    .     '<input type="radio" name="allowReinit" id="allowReinitYes" value="true">'
    .     '<label for="allowReinitYes">'.get_lang('Yes').'</label><br />' . "\n"
    .     '<input type="radio" name="allowReinit" id="allowReinitNo" value="false" >'
    .     '<label for="allowReinitNo">'.get_lang('No').'</label>' . "\n"
    .     '<br /><br />'
    */
    // viewmode
    .     get_lang('Default view mode') . '&nbsp;<span class="required">*</span><br />' . "\n"
    .     '<input type="radio" name="viewMode" id="viewModeEmb" value="EMBEDDED" '.($path->isFullscreen()?'':'checked="checked"').'>'
    .     '<label for="viewModeEmb">'.get_lang('Embedded').'</label><br />' . "\n"
    .     '<input type="radio" name="viewMode" id="viewModeFull" value="FULLSCREEN" '.($path->isFullscreen()?'checked="checked"':'').'>'
    .     '<label for="viewModeFull">'.get_lang('Fullscreen').'</label>' . "\n"
    .     '<br /><br />'
    // charset : TODO

    .     '<span class="required">*</span>&nbsp;'.get_lang('Denotes required fields') . '<br />' . "\n"
    .    '<input type="submit" value="' . get_lang('Ok') . '" />&nbsp;' . "\n"
    .    claro_html_button($cancelUrl, get_lang('Cancel'))
    .    '</form>' . "\n"
    ;

    $dialogBox->form($htmlEditForm);

}

if( $cmd == 'exAddItem' )
{
    
    if( isset( $_REQUEST['containerId']) && is_numeric( $_REQUEST['containerId'] ) ) $containerId = (int) $_REQUEST['containerId'];
    else                                                                             $containerId = null;
    
    if( isset($_REQUEST['resourceList']) && is_array($_REQUEST['resourceList']) && count($_REQUEST['resourceList']) )
    {
        $i = 0;
        foreach( $_REQUEST['resourceList'] as $crl )
        {
            // get title 
            ResourceLinker::init();
            $resourceName = ResourceLinker::$Resolver->getResourceName(ClarolineResourceLocator::parse( $crl ));
            if( !empty($resourceName) )
            {
                $title = substr($resourceName, strrpos($resourceName,'>') + 2);
            }
            else
            {
                $title = get_lang('No title');
            }
            $addedItem = new item();
            $addedItem->setType('MODULE');
            $addedItem->setTitle($title);
            $addedItem->setPathId($pathId);
            $addedItem->setSysPath(urldecode($crl));
            
            if( !( is_null( $containerId ) || $containerId == -1 ) )
            {
                $addedItem->setParentId( $containerId );
            }
            
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
    $htmlAddItem = "\n\n"
    .    '<strong>' . get_lang('Add item(s)') . '</strong>' . "\n"
    .    '<form action="' . $_SERVER['PHP_SELF'] . '?pathId='.$pathId.'" method="post">' . "\n"
    .    claro_form_relay_context() . "\n"
    .    '<input type="hidden" name="claroFormId" value="'.uniqid('').'" />'."\n"
    .    '<input type="hidden" name="cmd" value="exAddItem" />' . "\n"
    .    GRAPPLE_ResourceLinker::renderLinkerBlock(get_module_url('GRAPPLE').'/backends/linker.php')
    ;
    
    $containerList = new PathItemList($pathId);
    $containerListTree = $containerList->getContainerTree();
    
    if( ! empty( $containerListTree ) )
    {
        // add root to containerListTree
        $topElement['name'] = get_lang('Root');
        $topElement['value'] = -1;
        
        array_unshift($containerListTree,$topElement);
        
        
        $htmlAddItem .= '<br />' . "\n"
        .       '<strong>' . get_lang('Select chapter where you want to place the item') . '&nbsp;</strong>'
        .       claro_build_nested_select_menu("containerId",$containerListTree)
        .       '<br /><br/>' . "\n\n"
        ;
    }
    else
    {
        $htmlAddItem .= '<input type="hidden" name="containerId" value="-1" />';
    }

    $htmlAddItem .= '<input type="submit" class="claroButton" name="submitEvent" value="' . get_lang('Ok') . '" />'."\n"
    .    claro_html_button($_SERVER['PHP_SELF'] . '?pathId='.$pathId, get_lang('Cancel'))
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
    .     '<br /><br />'
    .    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exDelete&amp;pathId='.$pathId.'&amp;itemId='.$itemId.'">' . get_lang('Yes') . '</a>'
    .    '&nbsp;|&nbsp;'
    .    '<a href="' . $_SERVER['PHP_SELF'] . '?pathId='.$pathId.'">' . get_lang('No') . '</a>'
    ;

    $dialogBox->question( $htmlConfirmDelete );
}

if( $cmd == 'exMove' )
{

    $itemList = new PathItemList($pathId);
    $itemListArray = $itemList->getNodeChildrenId( $path->getId(), $item->getId() );
    
    if( $_REQUEST['newParentId'] == $item->getId() || in_array( $_REQUEST['newParentId'], $itemListArray ) )
    {
        $dialogBox->error( get_lang('Fatal error : new Parent item is the item iteself or one descendant') );    
    }
    else
    {
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

}

if( $cmd == 'rqMove' )
{
    // get list of available target : every container but not this one if this is a container too
    $containerList = new PathItemList($pathId);
    $containerListTree = $containerList->getContainerTree();


    if (!is_array($containerListTree)) 
    {
        $containerListTree = array();
    }
    // remove current item to avoid an item being a child of its parent
    
    // add root to containerListTree
    $topElement['name'] = get_lang('Root');
    $topElement['value'] = -1;
    
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
    $itemList = new PathItemList($pathId);
    $itemList->moveItemUp($item,$path);
}

if( $cmd == 'exMoveDown' )
{
    $itemList = new PathItemList($pathId);
    $itemList->moveItemDown($item,$path);
}

if( $cmd == 'exPrereq' )
{
    $blockcond = new blockingcondition( $itemId );
    $blockcond->setBlockConds($_POST);    
    
    if( $blockcond->save() )
    {
       $dialogBox->success( get_lang('Blocking condition(s) successfully saved') ); 
    }
    else
    {
        $dialogBox->error( get_lang('Unable to save the blocking condition(s)') );
    }
}

if( $cmd == 'rqPrereq' )
{    
    if( !is_null( $itemId ) )
    {
        $blockcond = new blockingcondition( $itemId );
        
        $htmlPrereqContainer = '<strong>' . htmlspecialchars( $item->getTitle() ) . '</strong><br /><br />' . "\n\n";
        
        // load blocking conditions dependencies
        if ( $item->getParentId() > 0 )
        {
            $blockcondsDependencies = array_reverse( $blockcond->loadRecursive( $item->getParentId(), true ) );
            if( count($blockcondsDependencies) )
            {            
                $htmlPrereqContainer .= '<div>' . "\n"
                .   '<strong>'. get_lang('Blocking conditions dependencies') .'</strong> <br />' . "\n";
                foreach( $blockcondsDependencies  as $dependency)
                {
                   if( isset( $dependency['data'] ) )
                   {
                        $blockconds = $dependency['data'];
                        $htmlPrereqContainer .= '<div>' . "\n"
                        .    '<strong><a href="'. $_SERVER['PHP_SELF'] . '?&cmd=rqPrereq&pathId='.$pathId.'&itemId='.$dependency['id']. '">'. htmlspecialchars($dependency['title']) .'</a></strong>';
                        foreach( $blockconds['item'] as $key => $value)
                        {
                             $htmlPrereqContainer .= '<div>' . "\n";
                             if( $key > 0 )
                             {
                                 $htmlPrereqContainer .= '<select name="_condition[]" disabled="disabled">' . "\n"
                                 .   '<option value="AND" '.($blockconds['condition'][$key-1] == 'AND' ? 'selected="selected"' : '').'>'.get_lang('AND').'</option>' . "\n"
                                 .   '<option value="OR" '.($blockconds['condition'][$key-1] == 'OR' ? 'selected="selected"' : '').'>'.get_lang('OR').'</option>' . "\n"
                                 .   '</select>'
                                 .   '<br />' . "\n";
                             }
                             
                             $htmlPrereqContainer .= '<select name="_item[]" disabled="disabled">' . "\n";
                             foreach( $itemListArray as $anItem )
                             {
                                 $htmlPrereqContainer .= '<option value="'. $anItem['id'] .'" style="padding-left:'.(5 + $anItem['deepness']*10).'px;" '.($value == $anItem['id'] && $anItem['type'] != 'CONTAINER' ? 'selected="selected"' : '').' '.($anItem['type'] == 'CONTAINER' ? 'disabled="disabled"' : '').'>'.$anItem['title'].'</option>' . "\n";
                             }
                             $htmlPrereqContainer .= '</select>'
                             .   '<select name="_operator[]" disabled="disabled">' . "\n"
                             .   '<option value="=" '.( $blockconds['operator'][$key] == '=' ? 'selected="selected"' : '').'>=</option>' . "\n"
                             .   '</select>'
                             .   '<select name="_status[]" disabled="disabled">' . "\n"
                             .   '<option value="COMPLETED" '.( $blockconds['status'][$key] == 'COMPLETED' ? 'selected="selected"' : '' ).'>'.get_lang('completed').'</option>' . "\n"
                             .   '<option value="INCOMPLETE" '.( $blockconds['status'][$key] == 'INCOMPLETE' ? 'selected="selected"' : '' ).'>'.get_lang('incomplete').'</option>' . "\n"
                             //.   '<option value="PASSED" '.( $blockconds['status'][$key] == 'PASSED' ? 'selected="selected"' : '' ).'>'.get_lang('passed').'</option>' . "\n"
                             .   '</select>'
                             .   '<span><input type="'.($blockconds['status'][$key] == 'COMPLETED' ? 'text' : 'hidden').'" name="raw_to_pass[]" disabled="disabled" value="'.(int) $blockconds['raw_to_pass'][$key].'" style="width: 50px; text-align: right;" />%</span>' . "\n"
                             .   '</div>' . "\n";
                        }
                        $htmlPrereqContainer .= '</div>' . "\n";
                   }           
                }
                $htmlPrereqContainer .=   '</div> <br />' . "\n";
            }
        }
        
        // show prerequisites form
        $htmlPrereqContainer .= '<strong>'.get_lang('Blocking conditions').'</strong>' . "\n"
        .   '<form action="'.$_SERVER['PHP_SELF'].'?cmd=exPrereq&pathId='. $pathId .'&itemId='.$itemId.'" method="post">' . "\n\n";
        
        if( $blockcond->load() )
        {
            $blockconds = $blockcond->getBlockConds();
            
            foreach( $blockconds['item'] as $key => $value )
            {
                $htmlPrereqContainer .= '<div>' . "\n";
                if( $key > 0 )
                {
                    $htmlPrereqContainer .= '<select name="condition[]">' . "\n"
                    .   '<option value="AND" '.($blockconds['condition'][$key-1] == 'AND' ? 'selected="selected"' : '').'>'.get_lang('AND').'</option>' . "\n"
                    .   '<option value="OR" '.($blockconds['condition'][$key-1] == 'OR' ? 'selected="selected"' : '').'>'.get_lang('OR').'</option>' . "\n"
                    .   '</select>'
                    .   '<button onclick="$(this).parent().remove();">'.get_lang('Remove').'</button>'
                    .   '<br />' . "\n";
                }
                
                $htmlPrereqContainer .= '<select name="item[]">' . "\n";
                foreach( $itemListArray as $anItem )
                {
                    $htmlPrereqContainer .= '<option value="'. $anItem['id'] .'" style="padding-left:'.(5 + $anItem['deepness']*10).'px;" '.($value == $anItem['id'] && $anItem['type'] != 'CONTAINER' ? 'selected="selected"' : '').' '.($anItem['type'] == 'CONTAINER' ? 'disabled="disabled"' : '').'>'.$anItem['title'].'</option>' . "\n";
                }
                $htmlPrereqContainer .= '</select>'
                .   '<select name="operator[]">' . "\n"
                .   '<option value="=" '.( $blockconds['operator'][$key] == '=' ? 'selected="selected"' : '').'>=</option>' . "\n"
                .   '</select>'
                .   '<select name="status[]" onchange="
                        $(this).parent().find(\'span\').remove();
                        var iPct = $(\'<input>\').attr(\'name\',\'raw_to_pass[]\').css(\'width\',\'50px\');
                        var sSpan = $(\'<span>\');
                        if( $(this).attr(\'value\') == \'COMPLETED\' )
                        {
                          $(iPct).attr(\'type\',\'text\');
                          sSpan.append(iPct);
                          sSpan.append(\'%\');
                        }
                        else
                        {
                          $(iPct).attr(\'type\',\'hidden\');
                          sSpan.append(iPct);
                        }
                        $(this).parent().append(sSpan);
                    ">' . "\n"
                .   '<option value="COMPLETED" '.( $blockconds['status'][$key] == 'COMPLETED' ? 'selected="selected"' : '' ).'>'.get_lang('completed').'</option>' . "\n"
                .   '<option value="INCOMPLETE" '.( $blockconds['status'][$key] == 'INCOMPLETE' ? 'selected="selected"' : '' ).'>'.get_lang('incomplete').'</option>' . "\n"
                //.   '<option value="PASSED" '.( $blockconds['status'][$key] == 'PASSED' ? 'selected="selected"' : '' ).'>'.get_lang('passed').'</option>' . "\n"
                .   '</select>' . "\n"
                .   '<span><input type="'.($blockconds['status'][$key] == 'COMPLETED' ? 'text' : 'hidden').'" name="raw_to_pass[]" value="'.(int) $blockconds['raw_to_pass'][$key].'" style="width: 50px; text-align: right;" />%</span>' . "\n"
                .   '</div>' . "\n";
            }
        }
        
        $htmlPrereqContainer .= '<input type="button" value="'. get_lang( 'Add a blocking condition' ) .'" id="block_condition_button" onclick="addBlockingCondition('.$pathId.')" />' . "\n"
        .   '<input type="submit" value="'.get_lang( 'Save the blocking condition(s)' ) .'" />' . "\n"    
        .   '</form>' . "\n"
        .   '<form action="' . $_SERVER['PHP_SELF'] . '?cmd=rqDeletePrereq&pathId='.$pathId.'&itemId='.$itemId.'"  method="post" id="blocking_conditions">' . "\n"
        .   '<input type="submit" value="'.get_lang( 'Remove all blocking conditions' ).'" />' . "\n"
        .   '</form>' . "\n";
        
        $dialogBox->form( $htmlPrereqContainer );
    }
}

if( $cmd == 'exDeletePrereq' )
{
    $blockcond = new blockingcondition( $itemId );
    if( $blockcond->delete() )
    {
        $dialogBox->success( get_lang('Blocking conditions successfully deleted') );
    }
    else
    {
        $dialogBox->error( get_lang('Fatal error : cannot delete blocking conditions') );
    }
}
if ( $cmd == 'rqDeletePrereq' )
{
    $htmlPrereqContainer = get_lang('Are you sure to delete blocking conditions for "%itemTitle" ?', array('%itemTitle' => htmlspecialchars($item->getTitle()) ))
    .     '<br /><br />'
    .    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exDeletePrereq&amp;pathId='.$pathId.'&amp;itemId='.$itemId.'">' . get_lang('Yes') . '</a>'
    .    '&nbsp;|&nbsp;'
    .    '<a href="' . $_SERVER['PHP_SELF'] . '?pathId='.$pathId.'">' . get_lang('No') . '</a>'
    ;

    $dialogBox->question( $htmlPrereqContainer );
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

if( $cmd == 'rqGrappleCoursesList' )
{
    $grapple = new grapple();
    
    $coursesList = $grapple->requestCoursesList();
    
    if( isset( $_SESSION['grapple']['coursesList'] ) )
    {
        unset( $_SESSION['grapple']['coursesList'] );
    }
    
    $_SESSION['grapple']['coursesList'] = $coursesList;
    
    if( ! $coursesList['success'] )
    {
        $dialogBox->error( $coursesList['error'] );
    }
    else
    {
        //Display courses list
        $htmlForm = '<form name="exGrappleCoursesList" action="' . $_SERVER['PHP_SELF'].'?cmd=exGrappleCoursesList&amp;pathId=' . $pathId . claro_url_relay_context( '&amp;' ) . '" method="post" >';
        
        foreach( $coursesList['courses'] as $gid => $course )
        {
            $htmlForm .= '<input type="checkbox" name="courses[' . $course['gid'] . ']" value="' . $course['gid'] . '" /> ' . htmlspecialchars( $course['name'] ) . ' ( <a href="' . $course['uri'] . '">' . $course['uri'] . '</a>)<br />';
        }
        $htmlForm .= '<input type="submit" value="'.get_lang( 'Import' ).'" />' . "\n"
        .   '</form>';
        
        $dialogBox->form( $htmlForm );
    }
}

if( $cmd == 'exGrappleCoursesList' )
{
    if( isset( $_POST['courses'] ) && count( $_POST['courses'] ) )
    {
        //check the coursesList if in session, if not, load it
        if( ! ( isset( $_SESSION['grapple']['coursesList'] ) && count( $_SESSION['grapple']['coursesList'] ) ) )
        {
            $_SESSION['grapple']['coursesList'] = $grapple->requestCoursesList();
        }
        
        foreach( $_POST['courses'] as $course )
        {
            
            if(isset( $_SESSION['grapple']['coursesList']['courses'][ $course ] ) )
            {
                $grappleResource = new grappleResource;
                
                $grappleResource->setId( $_SESSION['grapple']['coursesList']['courses'][ $course ]['gid'] )
                ->setName( $_SESSION['grapple']['coursesList']['courses'][ $course ]['name'] )
                ->setUri( $_SESSION['grapple']['coursesList']['courses'][ $course ]['uri'] )
                ->setPath( $_SESSION['grapple']['coursesList']['courses'][ $course ]['path'] )
                ;
                
                if( $grappleResource->save() )
                {
                    $dialogBox->success( get_lang( 'Course %courseName saved in database', array( '%courseName' => $grappleResource->getName() ) ) );
                    
                    $item = new item();
                    
                    $item->setPathId( $pathId );
                    $item->setTitle( $grappleResource->getName() );
                    $item->setSysPath( $grappleResource->getId() );
                    $item->setType( 'GRAPPLE' );
                    
                    if( $item->save() )
                    {
                        $dialogBox->success( get_lang( 'Grapple course %courseName linked in the learning path.', array( '%courseName' => $item->getTitle() ) ) );
                    }
                }
                else
                {
                    $dialogBox->error( get_lang( 'Unable to save courses in database.' ) );
                }
            }
        }
    }
    else
    {
        $dialogBox->error( get_lang( 'Nothing to import' ) );
    }
}



/*
 * Output
 */

ClaroBreadCrumbs::getInstance()->prepend( get_lang('Learning path list'), '../index.php'.claro_url_relay_context('?') );
ClaroBreadCrumbs::getInstance()->setCurrent( get_lang('Learning path'), './edit_path.php?pathId='.$pathId.claro_url_relay_context('&amp;') );
//-- Content
$jsloader = JavascriptLoader::getInstance();
$jsloader->load('jquery');
$jsloader->load('jquery.json');

$jsloader->load('GRAPPLE');

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
    $cmdMenu[] = claro_html_cmd_link($_SERVER['PHP_SELF'].'?cmd=rqGrappleCoursesList&amp&pathId=' . $pathId . claro_url_relay_context( '&amp;' ), get_lang( 'Import a Grapple course' ) );
}

$out .= '<p><small>' . htmlspecialchars($path->getDescription()). '</small></p>' . "\n";
$out .= '<p>'
.    claro_html_menu_horizontal( $cmdMenu )
.    '</p>'
;

$out .= '<table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">' . "\n"
.    '<thead>' . "\n"
.    '<tr class="headerX" align="center" valign="top">' . "\n"
.     '<th>' . get_lang('Item') . '</th>' . "\n"
.     '<th>' . get_lang('Modify') . '</th>' . "\n"
.     '<th>' . get_lang('Delete') . '</th>' . "\n"
.     '<th>' . get_lang('Prerequisites') . '</th>' . "\n"
.     '<th>' . get_lang('Visibility') . '</th>' . "\n"
.     '<th>' . get_lang('Move') . '</th>' . "\n"
.     '<th colspan="2">' . get_lang('Order') . '</th>' . "\n"
.    '</tr>' . "\n"
.    '</thead>' . "\n";

// Load refreshed list
$itemList = new PathItemList($pathId);
$itemListArray = $itemList->getFlatList();
if( !empty($itemListArray) && is_array($itemListArray) )
{
    $out .= '<tbody>' . "\n";

    foreach( $itemListArray as $anItem )
    {
        $out .= '<tr align="center"' . (($anItem['visibility'] == 'INVISIBLE')? 'class="invisible"': '') . '>' . "\n";

        // title
        $out .= '<td align="left" style="padding-left:'.(5 + $anItem['deepness']*10).'px;">'
        .    '<img src="'.(($anItem['type'] == 'CONTAINER')? get_icon_url('chapter'): get_icon_url('item')).'" alt="" />'
        .    '&nbsp;' . htmlspecialchars( claro_utf8_decode( $anItem['title'], get_conf('charset') ) )
        .    '</td>' . "\n";

        // edit
        $out .= '<td>' . "\n"
        .   '<a href="./edit_item.php?pathId=' . $pathId . '&amp;itemId='.$anItem['id'].'">' . "\n"
        .    '<img src="' . get_icon_url('edit') . '" border="0" alt="' . get_lang('Modify') . '" />' . "\n"
        .    '</a>'
        .   '</td>' . "\n";

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