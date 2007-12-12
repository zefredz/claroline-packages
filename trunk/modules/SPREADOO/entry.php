<?php // $Id$
/**
 *
 * @version 0.1 $Revision$
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @author Claroline team <info@claroline.net>
 *
 * @package SPREADOO
 *
 */

if ( count( get_included_files() ) == 1 ) die( '---' );

include_once claro_get_conf_repository().'SPREADOO.conf.php';

$html = '';

$html .= "\n\n"
.   '<!-- Spread Open Office -->' . "\n"
.   '<div align="center">'
.   '<a href="'.get_conf('spreadoo_link').'">'
.   '<img border="0" title="" src="'.get_conf('spreadoo_img_url').'" alt="OpenOffice.org" />'
.   '</a>'
.   '</div>' . "\n"
.   '<!-- Spread Open Office -->' . "\n\n";


$claro_buffer->append($html);

?>
