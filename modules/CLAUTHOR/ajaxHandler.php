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
 * @package CLAUTHOR
 *
 * @author Sebastien Piraux
 *
 */

$tlabelReq = 'CLAUTHOR';

require_once dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';

if ( !claro_is_allowed_to_edit() || !claro_is_in_a_course() )
{
	claro_die( get_lang( "Not allowed" ) );
}


/*
 * Tool libraries
 */
require_once dirname( __FILE__ ) . '/lib/clauthor.lib.php';
require_once dirname( __FILE__ ) . '/lib/pluginRegistry.lib.php';
// load and register all plugins
$pluginRegistry = pluginRegistry::getInstance();


/*
 * init request vars
 */
$acceptedCmdList = array('exOrder', 'addItem', 'deleteItem', 'getEditor', 'mkVisible', 'mkInvisible', 'exEdit', 'getComponent');

if( isset($_REQUEST['cmd']) && in_array($_REQUEST['cmd'],$acceptedCmdList) ) $cmd = $_REQUEST['cmd'];
else                                                                         $cmd = null;

if( isset($_REQUEST['docId']) && is_numeric($_REQUEST['docId']) )   $docId = (int) $_REQUEST['docId'];
else                                                                $docId = null;

if( isset($_REQUEST['itemId']) && is_numeric($_REQUEST['itemId']) )   $itemId = (int) $_REQUEST['itemId'];
else                                                                  $itemId = null;

if( isset($_REQUEST['itemType']) )  $itemType = $_REQUEST['itemType'];
else                             	$itemType = null;

// force header
header('Content-Type: text/html; charset=UTF-8');


if( $cmd == 'exOrder' )
{
	$tbl_lp_names = get_module_course_tbl( array('clauthor_contents'), claro_get_current_course_id() );
    $tblContents = $tbl_lp_names['clauthor_contents'];

	if( is_array($_REQUEST['componentsContainer']) && !is_null($docId) )
	{
		// $_REQUEST['componentsContainer'] is the ordered list of items, key is position, value is html id
		// html id is concatenation of item_ and the id of item
		foreach( $_REQUEST['componentsContainer'] as $position => $htmlItemId )
		{
			$position = (int) $position + 1; // add 1 to have lower rank == to 1 instead of 0
			$movedComponentId = (int) str_replace( 'component_', '', $htmlItemId );

			$sql = "UPDATE `".$tblContents."`
					SET `rank` = ".$position."
					WHERE `id` = ".$movedComponentId."
						AND `docId` = ".$docId;

			claro_sql_query($sql);
		}
	}


	return true;
}

if( $cmd == 'addItem')
{
	if( is_null($docId) || is_null($itemType) ) return false;

	$factory = new ComponentFactory();

	$component = $factory->createComponent( $itemType );

	if( $component )
	{
		// save component as we need to have an id for it !
		$component->setDocId($docId);
		$component->setType($itemType);
		$component->setInvisible();
		$component->save();

		echo claro_utf8_encode($component->renderBlock());
		return true;
	}

	return false;
}

if( $cmd == 'getComponent')
{
	if( is_null($docId) || is_null($itemType) || is_null($itemId) ) return false;

	$factory = new ComponentFactory();

	$component = $factory->createComponent( $itemType );

	if( $component )
	{
		if( $component->load($itemId) )
		{
			echo claro_utf8_encode($component->renderBlock());
			return true;
		}
		return false;
	}

	return false;
}


if( $cmd == 'deleteItem' )
{
	$factory = new ComponentFactory();

	$component = $factory->createComponent( $itemType );

	if( $component )
	{
		$component->load( $itemId );

		$component->delete();

		// we must echo a response status to know in the callback function if operation was sucessfull
		echo 'true';
		return true;
	}
	echo 'false';
	return false;
}



if( $cmd == 'mkVisible' )
{
	$factory = new ComponentFactory();

	$component = $factory->createComponent( $itemType );

	if( $component )
	{
		if( $component->load( $itemId ) )
		{
			$component->setVisible();
			$component->save();
			echo 'true';
			return true;
		}
	}

	echo 'false';
	return false;

}

if( $cmd == 'mkInvisible' )
{
	$factory = new ComponentFactory();

	$component = $factory->createComponent( $itemType );

	if( $component )
	{
		if( $component->load( $itemId ) )
		{
			$component->setInvisible();
			$component->save();
			echo 'true';
			return true;
		}
	}

	echo 'false';
	return false;
}


if( $cmd == 'getEditor' )
{
	$factory = new ComponentFactory();

	$component = $factory->createComponent( $itemType );

	if( $component )
	{
		if( $component->load( $itemId ) )
		{
			echo claro_utf8_encode($component->renderEditor());

			return true;
		}
	}

	return false;
}



if( $cmd == 'exEdit' )
{
	$factory = new ComponentFactory();

	$component = $factory->createComponent( $itemType );

	if( $component )
	{
		if( $component->load( $itemId ) )
		{
			if( isset($_REQUEST['title_'.$component->getId()]) ) 	$title = $_REQUEST['title_'.$component->getId()];
			else													$title = '';

			if( isset($_REQUEST['titleVisibility_'.$component->getId()]) && $_REQUEST['titleVisibility_'.$component->getId()] == 'VISIBLE' )
			{
				$component->setTitleVisible();
			}
			else
			{
				$component->setTitleInvisible();
			}

			$component->setTitle($title);
			$component->getEditorData();
			$component->save();

			echo 'true';
			return true;
		}
	}

	echo 'false';
	return false;
}

?>