<?php // $Id$
    
    // vim: expandtab sw=4 ts=4 sts=4:
    
    if ( count( get_included_files() ) == 1 ) die( '---' );
    
    require_once dirname(__FILE__) . '/../html/sanitizer.class.php';
    
    function blog_sanitize_html( $str, $key = null )
    {
        return HTML_Sanitizer::sanitize( $str );
    }
?>