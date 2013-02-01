<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 1.1.5 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * THIS FILE WILL BE REMOVED WHEN A EQUIVALENT FUNCTION WILL BE INTEGRATED TO CLAROLINE
 */

/**
 * Searchs an user
 */
function searchUser( $searchString )
{
    $tbl_mdb_names = claro_sql_get_main_tbl();
    
    return Claroline::getDatabase()->query( "
        SELECT
            user_id AS userId,
            nom     AS lastName,
            prenom  AS firstName,
            isCourseCreator,
            isPlatformAdmin
        FROM
                `{$tbl_mdb_names['user']}`
        WHERE
            prenom LIKE '%". Claroline::getDatabase()->escape( $searchString ) ."%'
        OR
            nom LIKE '%". Claroline::getDatabase()->escape( $searchString ) ."%'
        OR
            user_id = " . Claroline::getDatabase()->escape( (int)$searchString ) );
}

/**
 * Searchs for course which user is manager
 */
function getManagerCourseList( $userId )
{
    $tbl_mdb_names = claro_sql_get_main_tbl();
    
    return Claroline::getDatabase()->query( "
        SELECT
            code AS id,
            administrativeNumber AS code
            intitule AS title
        FROM
            `{$tbl_mdb_names['cours']}` AS C
        INNER JOIN
            `{$tbl_mdb_names['rel_course_user']}` AS U
        ON
            U.code_cours = C.id
        WHERE
            U.user_id =" . Claroline::getDatabase()->escape( $userId ) . "
        AND
            U.isCourseManager = TRUE" );
}