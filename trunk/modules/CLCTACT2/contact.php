<?php // $Id$

/**
 * Claroline Contact Page Generator
 *
 * @version     CLCTACT2 1.0beta $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLCTACT2
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

$tlabelReq = 'CLCTACT2';

require dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';

$contentFileUrl = dirname( __FILE__ ) . '/content.txt';

$content = file_exists( $contentFileUrl ) ? implode( file( $contentFileUrl ) ) : get_lang( 'This page is empty for now!');

$html = '<div id="contactPage"><h2>' . get_lang( 'Contact Page' ) . '</h2>' . $content . '</div>';

if ( claro_is_platform_admin() )
{
    $html .= '<p><a class="claroCmd" href="' . get_module_url( 'CLCTACT2' ) . '/edit.php">
            <img src="' . get_icon( 'edit' ) . '" alt="edit contact page" />' . get_lang( 'Edit the content' ) . '</a></p>';
}

Claroline::getInstance()->display->body->appendContent( $html );

echo Claroline::getInstance()->display->render();