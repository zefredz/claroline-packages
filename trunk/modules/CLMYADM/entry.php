<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:

    if ( claro_is_platform_admin() )
    {
        //$old_tlabel = $GLOBALS['currentModuleLabel'];
        $GLOBALS['currentModuleLabel'] = 'CLMYADM';
        
        $claro_buffer->append( 
            claro_html_icon_button(
                get_module_url('CLMYADM') . '/wmsa.php',
                'sqladmin',
                get_lang('MySQLAdmin'),
                get_lang('Database administration')
            )
        );

        $GLOBALS['currentModuleLabel'] = null;
    }
?>
