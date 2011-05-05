<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * ICPRINT web service access point
 *
 * @version     1.9 $Revision$
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @author      Claroline Team <info@claroline.net>
 * @author      Frederic Minne <zefredz@claroline.net>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE version 2 or later
 * @package     ICPRINT
 */
 
try
{
    // load Claroline kernel
    require dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';
    
    Fromkernel::uses('utils/input.lib','utils/validator.lib');
    From::Module('CLKRNG')->uses('keyring.lib');
    
    $userInput = Claro_userInput::getInstance();
    
    // Check access
    Keyring::checkForService('iccrslst');
    
    $cmd = $userInput->get('cmd','list');
    
    // get list
    if ( 'list' == $cmd )
    {
        $officialCode = trim( $userInput->get( 'officialCode' ) );
        
        if ( empty( $officialCode ) )
        {
            $username = trim( $userInput->get( 'username' ) );
            
            if ( empty( $username ) )
            {
                $userid = trim( $userInput->get( 'userid' ) );
            
                if ( empty( $userid ) )
                {

                    $email = trim( $userInput->get( 'email' ) );
            
                    if ( empty( $email ) )
                    {

                        throw new Exception( "Missing user" );
                    }
                    else
                    {
                        $user = "email = " . (int) Claroline::getDatabase()->quote( $email );
                    }
                }
                else
                {
                    $user = "user_id = " . (int) Claroline::getDatabase()->escape( $userid );
                }
            }
            else
            {
                $user = "username = " . Claroline::getDatabase()->quote( $username );
            }
        }
        else
        {
            $user = "officialCode = " . Claroline::getDatabase()->quote( $officialCode );
        }
        
        $filter = $userInput->get( 'filter', 'both' );
        
        $sqlCourseFilter = "";
        
        if ( $filter == 'managed' )
        {
            $sqlCourseFilter = "AND cu.isCourseManager = 1";
        }
        elseif ( $filter == 'unmanaged' )
        {
            $sqlCourseFilter = "AND cu.isCourseManager = 0";
        }
        elseif ( $filter == 'profile' )
        {
            $profile = trim($userInput->get( 'profile' ));
            
            if( empty( $profile ) )
            {
                throw new Exception( "Missing profile" );
            }
            
            $profileId = claro_get_profile_id( $profile );
            
            if ( ! $profileId )
            {
                throw new Exception( "Invalid profile" );
            }
            
            $sqlCourseFilter = "AND cu.profile_id = " . Claroline::getDatabase()->quote( $profileId );
        }
        else
        {
            $sqlCourseFilter = "";
        }
        
        $tbl = claro_sql_get_main_tbl();
        
        $res = Claroline::getDatabase()->query(
            "SELECT 
                user_id AS id, 
                officialCode AS officialCode,
                nom AS lastname, 
                prenom AS firstname, 
                email AS email
            FROM 
                `{$tbl['user']}`
            WHERE 
                {$user}
            ORDER BY id ASC
            LIMIT 1"
        );
        
        $user = $res->fetch();
        
        // if user found
        
        if ( $user )
        {
            $courses = Claroline::getDatabase()->query(
                "SELECT c.code AS id, c.administrativeNumber AS officialCode,
                    c.intitule AS title, cu.isCourseManager AS isCourseManager,
                    cu.profile_id AS profileId
                FROM `{$tbl['course']}` AS c
                INNER JOIN `{$tbl['rel_course_user']}` AS cu
                ON cu.code_cours = c.code
                WHERE cu.user_id = ".Claroline::getDatabase()->quote($user['id'])."
                " . $sqlCourseFilter
            );
            
            header("Content-type: text/xml; charset=utf-8");
            
            $tpl = new ModuleTemplate( 'ICCRSLST', 'courselist.xml.php' );
            $tpl->assign( 'user', $user ); 
            $tpl->assign( 'courses', $courses );
            
            echo claro_utf8_encode( $tpl->render() );
            
            exit();
        }
        else
        {
            header( 'Not found', true, 404 );
            
            echo '<h1>User not found !</h1>';
            
            exit();
        }
    }
    else
    {
        throw new Exception( "Invalid command" );
    }
}
catch ( Exception $e )
{
    header( 'Bad Request', true, 400 );
    
    if ( claro_debug_mode() )
    {
        claro_die( '<pre>' . $e->__toString() . '</pre>' );
    }
    else
    {
        claro_die( $e->getMessage() );
    }
}
