<?php // $Id$
/**
 * CLAROLINE
 *
 * @version 0.1 $Revision$
 *
 * @copyright 2001-2007 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @author Claroline team <info@claroline.net>
 *
 * @package W3CXHTML
 *
 */

if ( count( get_included_files() ) == 1 ) die( '---' );

$tlabelReq = 'W3CVALID';
include_once claro_get_conf_repository() . 'W3CVALID.conf.php';

$html = "\n\n"
.   '<p class="w3cxhtml" style="border: 1px solid #' . get_conf('w3cxhtml_borderColor') . ';'
.   '   background-color: #' . get_conf('w3cxhtml_bgColor') . ';'
.   '   color: #' . get_conf('w3cxhtml_textColor') . '; padding: 2px; margin: 15px 0;">'
.   '<a href="http://validator.w3.org/check?uri=referer">' . get_conf('w3cxhtml_text','xhtml') . '</a>' . "\n"
.   '</p>' . "\n\n"
;

$claro_buffer->append($html);
?>