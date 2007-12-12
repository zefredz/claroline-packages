<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * Main Controller for Vocabulary Application
     *
     * @version     1.9 $Revision$
     * @copyright   2001-2006 Universite catholique de Louvain (UCL)
     * @author      Frederic Minne <zefredz@claroline.net> and KOCH Gregory <gregk84@gate71.be>
     * @license     http://www.gnu.org/copyleft/gpl.html 
     *              GNU GENERAL PUBLIC LICENSE
     * @package     CLVOC
     */

// {{{ SCRIPT INITIALISATION
{
    $tlabelReq = 'CLVOC';
    // load Claroline kernel
    require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php'; 

    //require_once get_path('includePath') . '/lib/embed.lib.php';
    
    require_once dirname(__FILE__) . '/lib/access.lib.php';
    
    // load service architecture
    require_once dirname(__FILE__) . '/lib/service/dispatcher.class.php';
    require_once dirname(__FILE__) . '/lib/service/service.class.php';
    
    // load shared libraries for scripts
    require_once dirname(__FILE__) . '/lib/database/connection/claroline.class.php';
    require_once dirname(__FILE__) . '/lib/html/datagrid/table.class.php';
    require_once dirname(__FILE__) . '/lib/html/messagebox.class.php';
    require_once dirname(__FILE__) . '/lib/glossary/display.lib.php';
    # require_once dirname(__FILE__) . '/lib/plugit.lib.php'; 
    //require_once dirname(__FILE__) . '/lib/search/search.class.php';
    //require_once dirname(__FILE__) . '/lib/print/print.class.php';
    //require_once dirname(__FILE__) . '/lib/import/import.class.php';
    //require_once dirname(__FILE__) . '/lib/export/export.class.php';

    // check tool access
    claro_course_tool_allowed( true );
    
    // display mode
    claro_set_display_mode_available(true);
    
    // define module table names
    $tblNameList = array(
        'glossary_words',
        'glossary_definitions',
        'glossary_texts',
        'glossary_word_definitions',
        'glossary_dictionaries',
        'glossary_text_dictionaries',
        'glossary_dictionary_tree',
        'glossary_tags',
        'glossary_tags_entries',
        'glossary_print',
        'glossary_import',
        'glossary_export'
    );
    
    // convert to Claroline course table names
    $glossaryTables = get_module_course_tbl( $tblNameList
        , claro_get_current_course_id() );
    
    // run course installer for on the fly table creation
    install_module_in_course( 'CLVOC', claro_get_current_course_id() ) ;
    
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
    $dispatcher->setDefault( new ScriptService('./services/text.svc.php') );
    $dispatcher->bind( 'text', new ScriptService('./services/text.svc.php') );
    $dispatcher->bind( 'list', new ScriptService('./services/dictlist.svc.php') );
    $dispatcher->bind( 'dict', new ScriptService('./services/dictionary.svc.php') );
    $dispatcher->bind( 'tags', new ScriptService('./services/tags.svc.php') );
    $dispatcher->bind( 'help', new ScriptService('./services/help.svc.php') );
    $dispatcher->bind( 'print', new ScriptService('./services/print.svc.php') );
    $dispatcher->bind( 'import', new ScriptService('./services/import.svc.php') );
    $dispatcher->bind( 'export', new ScriptService('./services/export.svc.php') );
    
    // instanciate display
    $claroline->display->header->addHtmlHeader('<script type="text/javascript" src="'
        .$moduleJavascriptRepositoryWeb.'/phpcompat.js"></script>');
        
    $claroline->display->header->addHtmlHeader('<script type="text/javascript" src="'
        .$moduleJavascriptRepositoryWeb.'/claroline.js.php"></script>');
        
    $claroline->display->header->addHtmlHeader('<script type="text/javascript" src="'
        .$moduleJavascriptRepositoryWeb.'/popup.js"></script>');
        
    $claroline->display->header->addHtmlHeader('<script type="text/javascript" src="'
        .$moduleJavascriptRepositoryWeb.'/itemlist.js"></script>');
        
    $claroline->display->header->addHtmlHeader('<link rel="stylesheet" type="text/css" href="'
        .$moduleCssRepositoryWeb.'/form.css" media="all" />');
        
    $claroline->display->header->addHtmlHeader('<link rel="stylesheet" type="text/css" href="'
        .$moduleCssRepositoryWeb.'/clvoc.css" media="all" />');
        
    $claroline->display->header->addHtmlHeader('<link rel="stylesheet" type="text/css" href="'
        .$moduleCssRepositoryWeb.'/print.css" media="print" />');
        
}
// }}}
// {{{ CONTROLLER    
{
    // set shared display variables
    $inPopup = ( isset( $_REQUEST['inPopup'] ) 
            && 'true' == $_REQUEST['inPopup'] )
        ? true
        : false
        ;
        
    $hide_banner = ( isset( $_REQUEST['hide_banner'] ) 
            && 'true' == $_REQUEST['hide_banner'] )
        ? true
        : false
        ;
        
    $hide_footer = ( isset( $_REQUEST['hide_footer'] ) 
            && 'true' == $_REQUEST['hide_footer'] )
        ? true
        : false
        ;
        
    $hide_body = ( isset( $_REQUEST['hide_body'] ) 
            && 'true' == $_REQUEST['hide_body'] )
        ? true
        : false
        ;
    
    // set display Popup
    if ( $inPopup )
    {
        $claroline->display->popupMode();
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
        $claroline->display->body->setContent( MessageBox::FatalError( $dispatcher->getError() ) );
    }
    elseif ( $svc->hasError() )
    {
        $claroline->display->body->setContent( MessageBox::FatalError( $dispatcher->getError() ) );
    }
    else
    {
        $claroline->display->body->setContent( $svc->getOutput() );
    }

    }
// }}}
// {{{ VIEW    
{
    echo $claroline->display->render();
}
// }}}
?>