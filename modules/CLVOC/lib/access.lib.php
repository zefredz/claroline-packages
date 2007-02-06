<?php // $Id$
    
    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * @author  Frederic Minne <zefredz@claroline.net>
     * @copyright Copyright &copy; 2006-2007, Frederic Minne
     * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @version 1.0
     * @package PlugIt
     */
    
    if ( count( get_included_files() ) == 1 ) die( '---' );
    
    /**
     * Check if tool access allowed, else display login form
     * @return  void
     */
    function claro_course_tool_allowed( $anonymousAllowed = true )
    {
        // TODO use $is_toolAllowed and userRights
        if ( !claro_is_in_a_course()
            || ( !$anonymousAllowed && !claro_is_user_authenticated()))
        {
            claro_disp_auth_form( true );
        }
    }
    
    // ------- FOLOWING FUNCTIONS ARE NOT IN USE -----------
    
    function claro_tool_access_reserved_to_admin()
    {
        if ( ! claro_is_platform_admin() )
        {
            claro_die( "Not allowed here" );
        }
    }
?>
