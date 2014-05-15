<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * Blog utility functions
 *
 * @version     2.0 $Revision$
 * @copyright   2001-2014 Universite catholique de Louvain (UCL)
 * @author      Frederic Minne <zefredz@claroline.net>
 * @license     http://www.gnu.org/copyleft/gpl.html 
 *              GNU GENERAL PUBLIC LICENSE
 * @package     CLBLOG
 */

FromKernel::uses('utils/htmlsanitizer.lib');

function blog_sanitize_html( $str, $key = null )
{
    static $san = null;
    
    $str = claro_parse_user_text($str);
    
    if ( is_null( $san ) ) $san = new Claro_Html_Sanitizer;
    
    return $san->sanitize( $str );
}
