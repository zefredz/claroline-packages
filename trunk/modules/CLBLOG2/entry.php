<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * Main Controller for Blog Application
     *
     * @version     1.9 $Revision$
     * @copyright   2001-2007 Universite catholique de Louvain (UCL)
     * @author      Frederic Minne <zefredz@claroline.net>
     * @license     http://www.gnu.org/copyleft/gpl.html 
     *              GNU GENERAL PUBLIC LICENSE
     * @package     CLBLOG
     */

// {{{ SCRIPT INITIALISATION
{
    $tlabelReq = 'CLBLOG';
    // load Claroline kernel
    require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php'; 
    
    // load service architecture
    FromKernel::uses ( 'utils/controller.lib', 'display/dialogBox.lib' );

    // display mode
    claro_set_display_mode_available(true);
} 
// }}}
// {{{ MODEL
{    
    // instanciate dispatcher and bind services 
    $dispatcher = Claro_FrontController::getInstance();
    $dispatcher->bind( 'blog', new Claro_Controller_Script('./services/posts.svc.php') );
    $dispatcher->setDefault( 'blog' );
    
    
    $cssLoader = CssLoader::getInstance();
    $cssLoader->load('blog','all');
}
// }}}
// {{{ CONTROLLER    
{
    // set dispatcher requested service identifier
    $requestedService = isset( $_REQUEST['page'] )
        ? $_REQUEST['page']
        : null
        ;
        
    // serve requested page
    try
    {
        $claroline->display->body->appendContent( $dispatcher->serve( $requestedService ) );
    }
    catch ( Exception $e )
    {
        $dialogBox = new DialogBox;
        $dialogBox->error( $e->getMessage() );
        
        $claroline->display->setContent( $dialogBox->render() );
    }
}
// }}}
// {{{ VIEW    
{
    echo $claroline->display->render();
}
// }}}
?>