<?php // $Id$

/** Online Help Form
 *
 * @version     ICHELP 0.1 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICHELP
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

function getAppletLocation( $label )
{
    $tbl = get_module_main_tbl( array( 'dock' , 'module' ) );
    
    return Claroline::getDatabase()->query( "
        SELECT
            D.name
        FROM
            `{$tbl['cl_dock']}` AS D
        INNER JOIN
            `{$tbl['cl_module']}` AS M
        ON
            M.id = D.module_id
        WHERE
            M.label = " . Claroline::getDatabase()->quote( $label ) );
}

function is_mail( $string )
{
    if( function_exists( 'filter_var' ) ) // PHP >= 5.2
    {
        if( filter_var( $string , FILTER_VALIDATE_EMAIL ) === false )
        {
            return false;
        }
        else
        {
            return true;
        }
    }
    else // PHP < 5.2
    {
        return preg_match( '#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#' , $string ); 
    }
}

function is_manager( $userId , $courseId )
{
    $tbl = get_module_main_tbl( array( 'rel_course_user' ) );
    
    return Claroline::getDatabase()->query( "
        SELECT
            isCourseManager
        FROM
            `{$tbl['rel_course_user']}`
        WHERE
            user_id = " . Claroline::getDatabase()->escape( $userId ) . "
        AND
            code_cours = " . Claroline::getDatabase()->quote( $courseId )
        )->fetch( Database_ResulSet::FETCH_VALUE );
}