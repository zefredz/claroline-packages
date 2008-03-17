<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * Description
     *
     * @version     1.9 $Revision$
     * @copyright   2001-2007 Universite catholique de Louvain (UCL)
     * @author      Claroline Team <info@claroline.net>
     * @author      Frederic Minne <zefredz@claroline.net>
     * @license     http://www.gnu.org/copyleft/gpl.html
     *              GNU GENERAL PUBLIC LICENSE version 2 or later
     * @package     PACKAGE_NAME
     */

    if ( count( get_included_files() ) == 1 )
    {
        die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
    }
    
    function toClaroQuery( $sql )
    {
        $sql = str_replace ('__CL_MAIN__',get_conf('mainTblPrefix'), $sql);
        $sql = str_replace('__CL_COURSE__'
            , claro_get_course_db_name_glued( claro_get_current_course_id() )
            , $sql );
            
        return $sql;
    }
    
?>