<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.5.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
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
        SELECT user_id AS userId,
               nom     AS lastName,
               prenom  AS firstName,
               isCourseCreator,
               isPlatformAdmin
           FROM `{$tbl_mdb_names['user']}`
           WHERE prenom LIKE '%". Claroline::getDatabase()->escape( $searchString ) ."%'
           OR nom LIKE '%". Claroline::getDatabase()->escape( $searchString ) ."%'
           OR user_id = " . Claroline::getDatabase()->escape( (int)$searchString ) );
}