<?php // $Id$
/**
 *
 * @version 0.1 $Revision$
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @author Claroline team <info@claroline.net>
 *
 * @package CLSKYPE
 *
 */

if ( count( get_included_files() ) == 1 ) die( '---' );

if( claro_is_in_a_course() )
{
    add_module_lang_array('CLSKYPE');

    include_once get_module_path('CLSKYPE') . '/lib/skype.status.class.php';


    $skypeStatusNotifier = new SkypeStatus(claro_get_current_course_id());
    $skypeStatusNotifier->load();
    // prévenir utilisateur qu'il doit modifier ses réglages dans son skype pour que cela marche !

    $claro_buffer->append($skypeStatusNotifier->output());
}

?>
