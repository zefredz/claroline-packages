<?php // $Id$
/**
 * @version 1.0.0
 *
 * @version 1.8 $Revision$
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLOOVOO
 *
 * @author Wanjee <wanjee.be@gmail.com>
 *
 */

if ( count( get_included_files() ) == 1 ) die( '---' );

if( claro_is_in_a_course() )
{
    load_module_language('CLOOVOO');

    include_once get_module_path('CLOOVOO') . '/lib/oovoo.class.php';

    $oovooLink = new OovooLink(claro_get_current_course_id());
    $oovooLink->load();

    $claro_buffer->append($oovooLink->render());
    
    unset($oovooLink);
}

?>
