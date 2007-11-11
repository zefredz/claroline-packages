<?php // $Id$
/**
 *
 * @version 0.1 $Revision: 40 $
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @author Claroline team <info@claroline.net>
 *
 * @package SPREADFF
 *
 */

if ( count( get_included_files() ) == 1 ) die( '---' );

//$tlabelReq = 'SPREADFF';
include_once claro_get_conf_repository().'SPREADFF.conf.php';

$html = '';

if( !strstr($_SERVER['HTTP_USER_AGENT'], 'Firefox') || get_conf('spreadff_show_in_all_browsers') ) 
{

    $html .= "\n\n"
    .   '<!-- Spread Firefox -->' . "\n"
    .   '<div align="center">'
    .   '<a href="'.get_conf('spreadff_link').'">'
    .   '<img border="0" alt="" title="" src="'.get_conf('spreadff_img_url').'"/>'
    .   '</a>'
    .   '</div>' . "\n"
    .   '<!-- Spread Firefox -->' . "\n\n";

}

$claro_buffer->append($html);

?>
