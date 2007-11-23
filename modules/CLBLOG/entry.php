<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    // vim>600: set foldmethod=marker:
    
    /**
     * Main Controller for Blog Application
     *
     * @version     1.9 $Revision: 49 $
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
    uses ( 'core/service.lib', 'display/dialogBox.lib' );

    // display mode
    claro_set_display_mode_available(true);
    
    // get Claroline course table names
    $blogTables = get_module_course_tbl( array( 'blog_posts', 'blog_comments' )
        , claro_get_current_course_id() );
} 
// }}}
// {{{ MODEL
{    
    // instanciate dispatcher and bind services 
    $dispatcher = Dispatcher::getInstance();
    $dispatcher->setDefault( new ScriptService('./services/posts.svc.php') );
    $dispatcher->bind( 'blog', new ScriptService('./services/posts.svc.php') );
    
    $cssLoader = CssLoader::getInstance();
    $cssLoader->load('blog','all');
    $cssLoader->load('form','all');
}
// }}}
// {{{ CONTROLLER    
{
    // set shared display variables
    if( isset( $_REQUEST['inPopup'] )
            && 'true' == $_REQUEST['inPopup'] )
    {
        $claroline->display->popupMode();
    }

    if ( isset( $_REQUEST['hide_banner'] )
            && 'true' == $_REQUEST['hide_banner'] )
    {
        $claroline->display->banner->hide();
    }

    if( isset( $_REQUEST['hide_footer'] )
            && 'true' == $_REQUEST['hide_footer'] )
    {
        $claroline->display->footer->hide();
    }

    if( isset( $_REQUEST['hide_body'] )
            && 'true' == $_REQUEST['hide_body'] )
    {
        $claroline->display->body->hideClaroBody();
    }
    
    // set dispatcher requested service identifier
    $requestedService = isset( $_REQUEST['page'] )
        ? $_REQUEST['page']
        : null
        ;
        
    // serve requested page
    try
    {
        $svc = is_null( $requestedService ) 
            ? $dispatcher->serveDefault()
            : $dispatcher->serve( $requestedService )
            ;
        
        $claroline->display->setContent( $svc->getOutput() );
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