<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.1.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * Gets the name of the user with the specifed id
 * @param int $userId the user id
 * @return array $userName { [ firstName ] => 'Firstname' , [ lastName ] => 'Lastname' }
 */
function getUserName( $userId )
{
    $tbl_mdb_names = claro_sql_get_main_tbl();
    
    $userName = array();
    
    $result = Claroline::getDatabase()->query( "
            SELECT
                nom,
                prenom
            FROM
                `" . $tbl_mdb_names[ 'user' ] . "`
            WHERE
                user_id = " . Claroline::getDatabase()->escape( $userId ) .""
        )->fetch( Database_ResultSet::FETCH_ASSOC );
    
    $userName[ 'firstName' ] = $result[ 'prenom' ];
    $userName[ 'lastName' ] = $result[ 'nom' ];
    
    return $userName;
}

/**
 * Controls if the current user is registered in the current course
 */
function is_current_user_in_course()
{
    $tbl_mdb_names = claro_sql_get_main_tbl();
    
    return Claroline::getDatabase()->query( "
            SELECT
                user_id, code_cours
            FROM
                `" . $tbl_mdb_names[ 'rel_course_user' ] . "`
            WHERE
                user_id = " . Claroline::getDatabase()->escape( claro_get_current_user_id() ) . "
            AND
                code_cours = " . Claroline::getDatabase()->quote( claro_get_current_course_id() )
    )->numRows();
}

/**
 * Controls if the user with the specified id is registered in the current course
 */
function is_user_in_course( $userId )
{
    $tbl_mdb_names = claro_sql_get_main_tbl();
    
    return Claroline::getDatabase()->query( "
            SELECT
                user_id, code_cours
            FROM
                `" . $tbl_mdb_names[ 'rel_course_user' ] . "`
            WHERE
                user_id = " . Claroline::getDatabase()->escape( $userId ) . "
            AND
                code_cours = " . Claroline::getDatabase()->quote( claro_get_current_course_id() )
    )->numRows();
}

/**
 * Gets the id of all users for a course and put them into an array
 * @return a resultSet with all the id's
 */
function getCourseUserList()
{
    $tbl_mdb_names = claro_sql_get_main_tbl();
    
    $tbl_rel_course_user = $tbl_mdb_names[ 'rel_course_user' ];
    $tbl_users  = $tbl_mdb_names[ 'user' ];
    
    return Claroline::getDatabase()->query( "
            SELECT
                `user`.`user_id`      AS `user_id`
            FROM
                `" . $tbl_users . "`           AS user,
                `" . $tbl_rel_course_user . "` AS course_user
            WHERE
                `user`.`user_id`=`course_user`.`user_id`
            AND
                `course_user`.`code_cours`=" . Claroline::getDatabase()->quote( ( claro_get_current_course_id() ) )
        );
}

/**
 * Gets the name of all users for a course and put them into an array
 * @return array $userNameList { [ user_id ] => array { [ firstName ] => 'FirstName' , [ lastName ] => 'Lastname' } } 
 */
function getCourseUserNameList()
{
    $tbl_mdb_names = claro_sql_get_main_tbl();
    
    $tbl_rel_course_user = $tbl_mdb_names[ 'rel_course_user' ];
    $tbl_users  = $tbl_mdb_names[ 'user' ];
    
    $userNameList = array();
    $userName = array();
    
    $getUsers = Claroline::getDatabase()->query( "
            SELECT
                `user`.`user_id`      AS `user_id`,
                `user`.`nom`          AS `nom`,
                `user`.`prenom`       AS `prenom`
            FROM
                `" . $tbl_users . "`           AS user,
                `" . $tbl_rel_course_user . "` AS course_user
            WHERE
                `user`.`user_id`=`course_user`.`user_id`
            AND
                `course_user`.`code_cours`='" . Claroline::getDatabase()->quote( ( claro_get_current_course_id() ) )
        );
        
    foreach ( $getUsers as $user)
    {
        $userName[ 'firstName' ] = $user[ 'prenom' ];
        $userName[ 'lastName' ] = $user[ 'nom' ];
        $userNameList[ $user[ 'user_id' ] ] = $userName;
    }
    
    return $userNameList;
}