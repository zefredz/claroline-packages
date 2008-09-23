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
 * @package CLPAGES
 *
 * @author Sebastien Piraux
 *
 */

$tlabelReq = 'CLPAGES';

require_once dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';

if ( !claro_is_allowed_to_edit() || !claro_is_in_a_course() )
{
    claro_die( get_lang( "Not allowed" ) );
}


/*
 * Tool libraries
 */
require_once dirname( __FILE__ ) . '/lib/clpages.lib.php';
require_once dirname( __FILE__ ) . '/lib/pluginRegistry.lib.php';
// load and register all plugins
$pluginRegistry = pluginRegistry::getInstance();


/*
 * init request vars
 */
$acceptedCmdList = array(   
    'addComponent', 
    'deleteComponent',
    'exEdit',
    'getEditor',
    'mkVisible', 
    'mkInvisible',
    'mkUp', 
    'mkDown',
    'getComponent'
);

if( isset($_REQUEST['cmd']) && in_array($_REQUEST['cmd'],$acceptedCmdList) ) 
{
    $cmd = $_REQUEST['cmd'];
}
else
{
    claro_die( get_lang('Missing or invalid command') );
}

if( isset($_REQUEST['pageId']) && is_numeric($_REQUEST['pageId']) )
{
    $pageId = (int) $_REQUEST['pageId'];
}
else
{
    $pageId = null;
}

if( isset($_REQUEST['itemId']) && is_numeric($_REQUEST['itemId']) )
{
    $itemId = (int) $_REQUEST['itemId'];
}
else
{
    $itemId = null;
}

if( isset($_REQUEST['itemType']) )
{
    $itemType = $_REQUEST['itemType'];
}
else
{
    $itemType = null;
}

// force headers
header('Content-Type: text/html; charset=UTF-8'); // Charset
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

if( $cmd == 'mkUp' || $cmd == 'mkDown' )
{
    $page = new Page();

    if( is_null($pageId) || !$page->load($pageId) )
    {
        return false;
    }

    $list = $page->getComponentList();

    if( array_key_exists($itemId, $list) )  $thisItem = $list[$itemId];
    else                                    return false;

    if( $cmd == 'mkDown' )
    {
        // find the next one
        $otherItemToMove = null;

        $found = false;
        foreach( $list as $item )
        {
            if( $found )
            {
                // we found item on previous iteration so this one is the "next"
                $otherItemToMove = $item;
                break;
            }

            if( $item->getId() == $itemId )
            {
                // we found it
                $found = true;
            }
        }
        // stop here if there is no next item (this item is the last)
        if( is_null($otherItemToMove) ) return false;
    }
    else // mkUp
    {
        // find the previous one
        $otherItemToMove = null;

        foreach( $list as $item )
        {
            if( $item->getId() == $itemId )
            {
                // we found it, previous item should have been setted in previous iteration
                break;
            }
            else
            {
                $otherItemToMove = $item;
            }
        }

        // stop here if there is no previous item (this item is the first)
        if( is_null($otherItemToMove) ) return false;
    }

    // get old position
    $oldPos = $thisItem->getRank();
    // get new position
    $newPos = $otherItemToMove->getRank();

    // change positions
    $thisItem->setRank($newPos);
    $otherItemToMove->setRank($oldPos);

    // save the two items
    if( $thisItem->save() && $otherItemToMove->save() )
    {
        echo 'true';
        return true;
    }
    else
    {
        return false;
    }
}


if( $cmd == 'addComponent')
{
    if( is_null($pageId) || is_null($itemType) ) return false;

    $factory = new ComponentFactory();

    $component = $factory->createComponent( $itemType );

    if( $component )
    {
        // save component as we need to have an id for it !
        $component->setPageId($pageId);
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
    if( is_null($pageId) || is_null($itemType) || is_null($itemId) ) return false;

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


if( $cmd == 'deleteComponent' )
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
            // get it from request (this function ensure data is correctly encoded , etc...)
            $title = $component->getFromRequest('title_'.$component->getId());

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