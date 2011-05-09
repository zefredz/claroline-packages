<?php // $Id$

/*
 * CLAROLINE
 *
 * $Revision$
 * @copyright (c) 2001-2011 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package CLPAGES
 * @author Claroline team <info@claroline.net>
 */

try
{

    // load Claroline kernel
    $tlabelReq = 'CLPAGES';

    require_once dirname(__FILE__) . '/../../../../claroline/inc/claro_init_global.inc.php';

    // load required class
    require_once dirname( __FILE__ ) . '/../clpages.lib.php';
    require_once dirname( __FILE__ ) . '/../pluginRegistry.lib.php';

    if( isset($_REQUEST['pageId']) && is_numeric($_REQUEST['pageId']) )
    {
        $pageId = (int) $_REQUEST['pageId'];
    }
    else
    {
        $pageId = null;
    }

    if( isset($_REQUEST['componentId'])&& is_numeric($_REQUEST['pageId']))
    {
        $componentId = $_REQUEST['componentId'];
    }
    else
    {
        $componentId = 'all';
    }

    //Error redirections
    if( is_null($pageId) ) 
    {
        header("Location: ./../../index.php");
        exit();
    }
    else
    {
        $page = new Page();

        if( !$page->load($pageId) )
        {
            // required
            header("Location: ./../../index.php");
            exit();
        }
    }

    $s5Template = new ModuleTemplate( 'CLPAGES', 's5slideshow.tpl.php' );
    
    $s5Template->assign( 's5Date', claro_date("Y-m-d") );
    $s5Template->assign( 'page', $page );
    $s5Template->assign( 'displaySlideZero', ($componentId == 'all') );
    $s5Template->assign( 'displayAllSlides', ($componentId == 'all') );
    $s5Template->assign( 'componentId', $componentId );

    echo $s5Template->render();

}
catch ( Exception $e )
{
    if (claro_debug_mode() )
    {
        claro_die( '<pre>' . $e->__toString() . '</pre>' );
    }
    else
    {
        claro_die( $e->getMessage() );
    }
}
