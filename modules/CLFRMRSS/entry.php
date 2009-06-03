<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * OPML Generator entry point
 *
 * @version     1.9 $Revision$
 * @copyright   2001-2009 Universite catholique de Louvain (UCL)
 * @author      Dimitri Rambout <dimitri.rambout@uclouvain.be>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE
 * @package     CLFRMRSS
 */

if ( count( get_included_files() ) == 1 )
{
    die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
}

// display link to Forum RSS file for current user
if ( claro_is_user_authenticated() )
{
    $GLOBALS['currentModuleLabel'] = 'CLFRMRSS';
    
    /*$out = '<a href="'
        . get_module_url('CLFRMRSS')
        . '/index.php?userId=' . claro_get_current_user_id()
        . '"><img src="'.get_icon('rss.png').'" />&nbsp;</a>'."\n"
        ;
    */    
    $GLOBALS['currentModuleLabel'] = null;
    
    //$claro_buffer->append( $out );
}
