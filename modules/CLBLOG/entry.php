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
    
    require_once get_path('includePath') . '/lib/embed.lib.php';
    
    require_once dirname(__FILE__) . '/lib/access.lib.php';
    
    // load service architecture
    require_once dirname(__FILE__) . '/lib/service/dispatcher.class.php';
    require_once dirname(__FILE__) . '/lib/service/service.class.php';
    
    // load shared libraries for scripts
    require_once dirname(__FILE__) . '/lib/database/connection/claroline.class.php';
    require_once dirname(__FILE__) . '/lib/html/messagebox.class.php';
    
    // check tool access
    claro_course_tool_allowed( true );
    // display mode
    claro_set_display_mode_available(true);
    
    // get Claroline course table names
    $blogTables = get_module_course_tbl( array( 'blog_posts', 'blog_comments' )
        , claro_get_current_course_id() );
    
    // run course installer for on the fly table creation
    install_module_in_course( 'CLBLOG', claro_get_current_course_id() ) ;
    
    // global variables
    $moduleImageRepositoryWeb = './img';
    $moduleImageRepositorySys = dirname(__FILE__).'/img';    
    $moduleJavascriptRepositoryWeb = './js';
    $moduleCssRepositoryWeb = './css';
    $helpDir = dirname(__FILE__) . '/help'; 
} 
// }}}
// {{{ MODEL
{    
    // instanciate dispatcher and bind services 
    $dispatcher = new Dispatcher();
    $dispatcher->setDefault( new ScriptService('./services/posts.svc.php') );
    $dispatcher->bind( 'blog', new ScriptService('./services/posts.svc.php') );
    $dispatcher->bind( 'help', new ScriptService('./services/help.svc.php') );
    
    // instanciate display
    $display = new ClarolineScriptEmbed;
    $display->addHtmlHeader('<script type="text/javascript" src="'
        .$moduleJavascriptRepositoryWeb.'/popup.js"></script>');
        
    $display->addHtmlHeader('<link rel="stylesheet" type="text/css" href="'
        .$moduleCssRepositoryWeb.'/blog.css" media="all" />');
        
    $display->addHtmlHeader('<link rel="stylesheet" type="text/css" href="'
        .$moduleCssRepositoryWeb.'/form.css" media="all" />');
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
    $svc = is_null( $requestedService ) 
        ? $dispatcher->serveDefault()
        : $dispatcher->serve( $requestedService )
        ;
    
    // prepare output
    if ( $dispatcher->hasError() )
    {
        $display->setContent( MessageBox::FatalError( $dispatcher->getError() ) );
    }
    elseif ( $svc->hasError() )
    {
        $display->setContent( MessageBox::FatalError( $dispatcher->getError() ) );
    }
    else
    {
        $display->setContent( $svc->getOutput() );
    }
}
// }}}
// {{{ VIEW    
{
    $display->output();
}
// }}}
?>