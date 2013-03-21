<?php

/**
 * Get the number of users in a class, including sublclasses
 *
 * @author Guillaume Lederer
 * @param  id of the (parent) class ffrom which we want to know the number of users
 * @return (int) number of users in this class and its subclasses
 *
 */

function class_get_number_of_users( $class_id, $include_children = true )
{
    $tbl_mdb_names  = claro_sql_get_main_tbl();
    $tbl_class_user = $tbl_mdb_names['rel_class_user'];
    $tbl_class      = $tbl_mdb_names['class'];
    //1- get class users number

    $sqlcount = "SELECT COUNT(`user_id`) AS qty_user
                 FROM `" . $tbl_class_user . "`
                 WHERE `class_id`=" . (int) $class_id;

    $qty_user =  Claroline::getDatabase()->query($sqlcount)->setFetchMode ( Database_ResultSet::FETCH_VALUE )->fetch();
    
    if ( $include_children )
    {

        $sql = "SELECT `id`
                FROM `" . $tbl_class . "`
                WHERE `class_parent_id`=" . (int) $class_id;

        $subClassesList = Claroline::getDatabase()->query($sql);

        //2- recursive call to get subclasses'users too

        foreach ( $subClassesList as $subClass )
        {
            $qty_user += class_get_number_of_users( $subClass['id'], true );
        }
    }

    //3- return result of counts and recursive calls

    return $qty_user;
}

function class_get_number_of_users_in_course( $course_id, $class_id, $include_children = true )
{
    $tbl_mdb_names  = claro_sql_get_main_tbl();
    $tbl_class_user = $tbl_mdb_names['rel_class_user'];
    $tbl_class      = $tbl_mdb_names['class'];
    $tbl_course_user = $tbl_mdb_names['rel_course_user' ];
    //1- get class users number

    $sqlcount = "SELECT COUNT(clu.`user_id`) AS qty_user
                 FROM `{$tbl_class_user}` AS clu
                 INNER JOIN `{$tbl_course_user}` AS cu 
                 ON `cu`.`user_id` = `clu`.`user_id`
                 WHERE clu.`class_id` = " . (int) $class_id;

    $qty_user =  Claroline::getDatabase()->query($sqlcount)->setFetchMode ( Database_ResultSet::FETCH_VALUE )->fetch();
    
    if ( $include_children )
    {

        $sql = "SELECT `id`
                FROM `" . $tbl_class . "`
                WHERE `class_parent_id`=" . (int) $class_id;

        $subClassesList = Claroline::getDatabase()->query($sql);

        //2- recursive call to get subclasses'users too

        foreach ( $subClassesList as $subClass )
        {
            $qty_user += class_get_number_of_users_in_course( $subClass['id'], true );
        }
    }

    //3- return result of counts and recursive calls

    return $qty_user;
}
