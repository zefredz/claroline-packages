<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:

    if ( count( get_included_files() ) == 1 )
    {
        die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
    }

    if ( claro_is_user_authenticated() )
    {
        $GLOBALS['htmlHeadXtra'][] = '<link rel="alternate"'
            . ' type="text/x-opml"'
            . ' title="'.get_lang('List of RSS from all my courses').'"'
            . ' href="'.get_module_url('CLOPML')
            . '/index.php?userId=' . claro_get_current_user_id().'" />'
            ;
    }
?>