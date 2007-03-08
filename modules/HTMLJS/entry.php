<?php // $Id$
/**
 * CLAROLINE
 *
 * @copyright 2001-2006 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @see http://www.claroline.net/wiki/index.php/Config
 *
 * @author Claro Team <cvs@claroline.net>
 *
 * @package HTMLJS
 */

if ( count( get_included_files() ) == 1 ) die( '---' );

//$tlabelReq = 'HTMLJS';
include_once claro_get_conf_repository().'HTMLJS.conf.php';

$htmljs_text = get_conf('htmljs_text');

$html = '';

if( !empty( $htmljs_text ) )
{
    $html .= "\n\n"
    .   '<div>'
    .   get_conf('htmljs_text')
    .   '</div>' . "\n\n";
}

$claro_buffer->append($html);

?>
