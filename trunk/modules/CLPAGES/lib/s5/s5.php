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

    FromKernel::uses(
        'utils/input.lib',
        'utils/validator.lib'
    );
    
    From::Module('CLPAGES')->uses(
        'clpages.lib',
        'pluginRegistry.lib'
    );
    
    $userInput = Claro_UserInput::getInstance();
    
    $pageId = (int) $userInput->getMandatory( 'pageId');
    $componentId = $userInput->get( 'componentId', 'all' );

    //Error redirections
    $page = new Page();

        if( !$page->load($pageId) )
        {
            // required
            throw new Exception( strip_tags( "Impossible to load page {$pageId}" ) );
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
    $nameTools = get_lang('Pages');
    
    Claroline::getDisplay()->header->setTitle( $nameTools );
    ClaroBreadCrumbs::getInstance()->setCurrent( $nameTools );
    
    if (claro_debug_mode() )
    {
        claro_die( '<pre>' . $e->__toString() . '</pre>' );
    }
    else
    {
        claro_die( $e->getMessage() );
    }
}
