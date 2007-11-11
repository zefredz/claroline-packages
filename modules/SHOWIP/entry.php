<?php // $Id$
/**
 *
 * @version 0.1 $Revision: 40 $
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @author Claroline team <info@claroline.net>
 *
 * @package SHOWIP
 *
 */

if ( count( get_included_files() ) == 1 ) die( '---' );

//$tlabelReq = 'SHOWIP';
include_once claro_get_conf_repository().'SHOWIP.conf.php';

$html = "\n\n"
.   '<p style="border: 1px solid #'.get_conf('showip_borderColor').';'
.   '   background-color: #'.get_conf('showip_bgColor').';'
.   '   color: #'.get_conf('showip_textColor').'; padding: 2px; margin: 15px 0;">'
.   str_replace('%ip', $_SERVER['REMOTE_ADDR'], get_conf('showip_text'))
.   '</p>' . "\n\n";


$claro_buffer->append($html);


?>
