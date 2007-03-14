<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:

    require_once dirname(__FILE__) . '/include/lib.fortune.php';
    

    $files = currentFileList();
    
    if ( false === $files )
    {
        $files = listFortuneFiles();
    }
    
    if ( !empty( $files ) )
    {
        $bofh = new Fortune( $files );

        $out =  $bofh->generate();
    }
    else
    {
        $out = '<p>' . get_lang( 'No fortune file to load' ) . '</p>';
    }

    $claro_buffer->append( $out );
?>