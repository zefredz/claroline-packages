<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * OPML Generator entry point
 *
 * @version     1.9 $Revision$
 * @copyright   2001-2007 Universite catholique de Louvain (UCL)
 * @author      Frederic Minne <zefredz@claroline.net>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE
 * @package     CLOPML
 */

if ( count( get_included_files() ) == 1 )
{
    die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
}

// display link to OPML file for current user
if ( claro_is_user_authenticated() )
{
    $GLOBALS['currentModuleLabel'] = 'CLOPML';
    
    $out = '<a href="'
        . htmlspecialchars( URL::Contextualize( get_module_url('CLOPML')
        . '/index.php?userId=' . claro_get_current_user_id() ) )
        . '"><img src="'.get_icon('opml.png').'" alt="" />&nbsp;'.get_lang('List of RSS from all my courses (opml file format)').'</a>'."\n"
        ;
        
    $GLOBALS['currentModuleLabel'] = null;
    
    $claro_buffer->append( $out );
}
