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

if ( count( get_included_files() ) == 1 ) die ( 'The file ' . basename( __FILE__ ) . 'Cannot be accessed directly, use include instead' );

$tlabelReq = 'CLCTACT2';

$html = '<div id="contactLink"><a href="' .
        get_module_url( 'CLCTACT2' ) . '/contact.php">' .
        get_lang( 'Contact' ) . '</a></div>';

$claro_buffer->append( $html );