<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

FromKernel::uses('utils/htmlsanitizer.lib');

function blog_sanitize_html( $str, $key = null )
{
    static $san = null;
    
    $str = claro_parse_user_text($str);
    
    if ( is_null( $san ) ) $san = new Claro_Html_Sanitizer;
    
    return $san->sanitize( $str );
}
