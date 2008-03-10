<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * Description
     *
     * @version     1.8 $Revision$
     * @copyright   2001-2008 Universite catholique de Louvain (UCL)
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
    
    require_once get_path('incRepositorySys') . '/lib/sendmail.lib.php';
    
    function icmail_get_user_list( $type )
    {
        $tbl_cdb_names   = claro_sql_get_main_tbl();
        $tbl_course_user = $tbl_cdb_names['rel_course_user'];
        $tbl_user        = $tbl_cdb_names['user'];
        
        switch($type)
        {
            // all users
            case 'all':
                $sql = 'SELECT user_id AS id
                        FROM `'.$tbl_user.'`';
            break;
            // course creators
            case 'creators':
                $sql = 'SELECT user_id AS id
                        FROM `'.$tbl_user.'`
                        WHERE `isCourseCreator` = 1';
            break;
            // users with no courses
            case 'nocourse':
                $sql = 'SELECT DISTINCT  user_id AS id
                        FROM `'.$tbl_user.'` AS u INNER JOIN `'.$tbl_course_user.'` AS cu
                        ON  u.user_id = cu.user_id
                        WHERE cu.user_id IS NULL';
            break;
            /*case 'todelete':
                $sql = 'SELECT user_id AS id
                        FROM `'.$tbl_user.'`
                        WHERE `aEffacer` = 1';
                break;*/
            // course managers
            case 'managers':
                $sql = 'SELECT DISTINCT user_id AS id
                        FROM `'.$tbl_user.'` AS u INNER JOIN `'.$tbl_course_user.'` AS cu
                        ON  u.user_id = cu.user_id
                        AND `isCourseManager` = 1';
        	break;
            // admins
            case 'admin':
            default:
            	$sql = 'SELECT user_id AS id
                        FROM `'.$tbl_user.'` 
            	        WHERE `isPlatformAdmin` = 1';
        }
    
        return claro_sql_query_fetch_all_rows($sql);
    }
?>