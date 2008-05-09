<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:

    /**
     * @author  Frederic Minne <zefredz@claroline.net>
     * @copyright Copyright &copy; 2006-2007, Frederic Minne
     * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @version 1.0
     * @package PlugIt
     */

    function getCourseUserList( $userIdList = null, $cid = null )
    {
        if ( empty( $cid ) )
        {
            $cid = claro_get_current_course_id();
        }
        
        if ( !empty( $userIdList ) )
        {
            $and = " AND `user`.`user_id` IN (" 
                .implode(',', $userIdList). ")"
                ;
        }
        else
        {
            $and = '';
        }
        
        $tbl_mdb_names = claro_sql_get_main_tbl();

        $tbl_rel_course_user = $tbl_mdb_names['rel_course_user'  ];
        $tbl_users           = $tbl_mdb_names['user'             ];
        
        $sqlGetUsers = "SELECT `user`.`user_id`      AS `user_id`,
                       `user`.`nom`          AS `nom`,
                       `user`.`prenom`       AS `prenom`,
                       `user`.`email`        AS `email`,
                       `course_user`.`profile_id`,
                       `course_user`.`isCourseManager`,
                       `course_user`.`tutor`  AS `tutor`,
                       `course_user`.`role`   AS `role`
               FROM `" . $tbl_users . "`           AS user,
                    `" . $tbl_rel_course_user . "` AS course_user
               WHERE `user`.`user_id`=`course_user`.`user_id`
               AND   `course_user`.`code_cours`='" . addslashes($cid) . "'\n"
               . $and
               ;
               
        if ( false !== ($result = claro_sql_query_fetch_all( $sqlGetUsers ) ) )
        {
            $ret = array();
            
            foreach ( $result as $row )
            {
                $ret[$row['user_id']] = $row;
            }
            
            return $ret;
        }
        else
        {
            return false;
        }
    }
?>
