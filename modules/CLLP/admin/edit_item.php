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
require_once dirname( __FILE__ ) . '/../lib/blockingcondition.class.php';

require_once dirname( __FILE__ ) . '/../linker/linker.inc.php';

/*
 * init request vars
 */
$acceptedCmdList = array(   'blockcondAdd', 'blockcondDelete'
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
$blockcond = new blockingcondition( $itemId );

if( $cmd == 'blockcondAdd' )
{
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

if( $cmd == 'blockcondDelete' )
{
    if( $blockcond->delete() )
    {
        $dialogBox->success( get_lang('Blocking conditions successfully deleted') );
    }
    else
    {
        $dialogBox->error( get_lang('Fatal error : cannot delete blocking conditions') );
    }
}


/*
 * Output
 */

ClaroBreadCrumbs::getInstance()->prepend( get_lang('Learning path list'), '../index.php' );
ClaroBreadCrumbs::getInstance()->prepend( get_lang('Learning path'), '../admin/edit_path.php?pathId=' . $pathId . claro_url_relay_context('&amp;') );



//-- Content
$jsloader = JavascriptLoader::getInstance();
$jsloader->load('jquery');
$jsloader->load('jquery.json');

$jsloader->load('CLLP');

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

// load blocking conditions dependencies

$item = new item();
if( $item->load( $itemId ) )
{
    if ( $item->getParentId() > 0 )
    {
        $blockcondsDependencies = array_reverse( $blockcond->loadRecursive( $item->getParentId(), true ) );
        
        $htmlBlockCondDep = '<div>' . "\n"
        .   '<strong>'. get_lang('Blocking conditions dependencies') .'</strong> <br /> <br />' . "\n";
        foreach( $blockcondsDependencies  as $dependency)
        {
           $blockconds = $dependency['data'];
           $htmlBlockCondDep .= '<div>' . "\n"
           .    '<strong>'. htmlspecialchars($dependency['title']) .'</strong>';
           foreach( $blockconds['item'] as $key => $value)
           {
                $htmlBlockCondDep .= '<div>' . "\n";
                if( $key > 0 )
                {
                    $htmlBlockCondDep .= '<select name="_condition[]" disabled="disabled">' . "\n"
                    .   '<option value="AND" '.($blockconds['condition'][$key-1] == 'AND' ? 'selected="selected"' : '').'>'.get_lang('AND').'</option>' . "\n"
                    .   '<option value="OR" '.($blockconds['condition'][$key-1] == 'OR' ? 'selected="selected"' : '').'>'.get_lang('OR').'</option>' . "\n"
                    .   '</select>'
                    .   '<br />' . "\n";
                }
                
                $htmlBlockCondDep .= '<select name="_item[]" disabled="disabled">' . "\n";
                foreach( $itemListArray as $anItem )
                {
                    $htmlBlockCondDep .= '<option value="'. $anItem['id'] .'" style="padding-left:'.(5 + $anItem['deepness']*10).'px;" '.($value == $anItem['id'] && $anItem['type'] != 'CONTAINER' ? 'selected="selected"' : '').' '.($anItem['type'] == 'CONTAINER' ? 'disabled="disabled"' : '').'>'.$anItem['title'].'</option>' . "\n";
                }
                $htmlBlockCondDep .= '</select>'
                .   '<select name="_operator[]" disabled="disabled">' . "\n"
                .   '<option value="=" '.( $blockconds['operator'][$key] == '=' ? 'selected="selected"' : '').'>=</option>' . "\n"
                .   '</select>'
                .   '<select name="_status[]" disabled="disabled">' . "\n"
                .   '<option value="COMPLETED" '.( $blockconds['status'][$key] == 'COMPLETED' ? 'selected="selected"' : '' ).'>'.get_lang('completed').'</option>' . "\n"
                .   '<option value="INCOMPLETE" '.( $blockconds['status'][$key] == 'INCOMPLETE' ? 'selected="selected"' : '' ).'>'.get_lang('incomplete').'</option>' . "\n"
                .   '<option value="PASSED" '.( $blockconds['status'][$key] == 'PASSED' ? 'selected="selected"' : '' ).'>'.get_lang('passed').'</option>' . "\n"
                .   '</select>'
                .   '</div>' . "\n";
           }
        }
        $htmlBlockCondDep .=   '</div> <br />' . "\n";
        
        $out .= $htmlBlockCondDep;   
    }    
}




$htmlConditionsForm = "\n\n"
.   '<strong>'.get_lang('Blocking conditions').'</strong>' . "\n"
.   '<form action="' . $_SERVER['PHP_SELF'] . '?cmd=blockcondAdd&pathId='.$pathId.'&itemId='.$itemId.'"  method="post" id="blocking_conditions">' . "\n";

if( $blockcond->load() )
{
    $blockconds = $blockcond->getBlockConds();
    
    foreach( $blockconds['item'] as $key => $value )
    {
        $htmlConditionsForm .= '<div>' . "\n";
        if( $key > 0 )
        {
            $htmlConditionsForm .= '<select name="condition[]">' . "\n"
            .   '<option value="AND" '.($blockconds['condition'][$key-1] == 'AND' ? 'selected="selected"' : '').'>'.get_lang('AND').'</option>' . "\n"
            .   '<option value="OR" '.($blockconds['condition'][$key-1] == 'OR' ? 'selected="selected"' : '').'>'.get_lang('OR').'</option>' . "\n"
            .   '</select>'
            .   '<button onclick="$(this).parent().remove();">'.get_lang('Remove').'</button>'
            .   '<br />' . "\n";
        }
        
        $htmlConditionsForm .= '<select name="item[]">' . "\n";
        foreach( $itemListArray as $anItem )
        {
            $htmlConditionsForm .= '<option value="'. $anItem['id'] .'" style="padding-left:'.(5 + $anItem['deepness']*10).'px;" '.($value == $anItem['id'] && $anItem['type'] != 'CONTAINER' ? 'selected="selected"' : '').' '.($anItem['type'] == 'CONTAINER' ? 'disabled="disabled"' : '').'>'.$anItem['title'].'</option>' . "\n";
        }
        $htmlConditionsForm .= '</select>'
        .   '<select name="operator[]">' . "\n"
        .   '<option value="=" '.( $blockconds['operator'][$key] == '=' ? 'selected="selected"' : '').'>=</option>' . "\n"
        .   '</select>'
        .   '<select name="status[]">' . "\n"
        .   '<option value="COMPLETED" '.( $blockconds['status'][$key] == 'COMPLETED' ? 'selected="selected"' : '' ).'>'.get_lang('completed').'</option>' . "\n"
        .   '<option value="INCOMPLETE" '.( $blockconds['status'][$key] == 'INCOMPLETE' ? 'selected="selected"' : '' ).'>'.get_lang('incomplete').'</option>' . "\n"
        .   '<option value="PASSED" '.( $blockconds['status'][$key] == 'PASSED' ? 'selected="selected"' : '' ).'>'.get_lang('passed').'</option>' . "\n"
        .   '</select>'
        .   '</div>' . "\n";
    }
}

$htmlConditionsForm .= '<input type="button" value="'. get_lang( 'Add a blocking condition' ) .'" id="block_condition_button" onclick="addBlockingCondition('.$pathId.')" />' . "\n"
.   '<input type="submit" value="'.get_lang( 'Save the blocking condition(s)' ) .'" />' . "\n"    
.   '</form>' . "\n";
$htmlConditionsForm .= '<form action="' . $_SERVER['PHP_SELF'] . '?cmd=blockcondDelete&pathId='.$pathId.'&itemId='.$itemId.'"  method="post" id="blocking_conditions">' . "\n"
.   '<input type="submit" value="'.get_lang( 'Remove all blocking conditions' ).'" />' . "\n"
.   '</form>' . "\n";

$out .= $htmlConditionsForm;


$claroline->display->body->appendContent($out);

echo $claroline->display->render();
?>