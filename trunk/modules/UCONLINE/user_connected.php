<?php // $Id$

/**
 * Who is onlin@?
 *
 * @version     UCONLINE 1.2.8 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCONLINE
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

$cidReset = TRUE;

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

FromKernel::uses( 'pager.lib' , 'user.lib' , 'utils/input.lib' , 'display/layout.lib' );

$userInput = Claro_UserInput::getInstance();

$offset = (int)$userInput->get( 'offset' );
$pagerSortKey = $userInput->get( 'sort' , 'lastname' );
$pagerSortDir = $userInput->get( 'dir' , 'SORT_ASC' );

$toolName = get_lang( 'User(s) online' );

$userPerPage = get_conf( 'UCONLINE_usersPerPage' );

$tbl = claro_sql_get_tbl( array( 'user_online' , 'user_property' , 'user' , 'rel_course_user' , 'cours' ), array( 'course' => null ) );

$additionalRestriction = get_conf( 'UCONLINE_privacy' ) == 2 ?
    "INNER JOIN
        `{$tbl[ 'cours' ]}` AS CL
    ON
        CL.`code` = CU.`code_cours`
    WHERE
        CL.`registration` = 'close'
    AND" : "WHERE";

$sql = "SELECT DISTINCT
            U.`nom`                 AS `lastname`,
            U.`prenom`              AS `firstname`,
            U.`email`               AS `email`,
            U.`user_id`             AS `id`,
            U.`isCourseCreator`     AS `isCourseCreator`,
            U.`isPlatformAdmin`     AS `isPlatformAdmin`,
            O.`last_action`         AS `last_action`,
            O.`time_offset`         AS `time_offset`,
            S.`propertyValue`       AS `skype_name`
        FROM
            `{$tbl[ 'user' ]}`        AS U
        LEFT JOIN
            `{$tbl[ 'user_property' ]}` AS S
            ON
                S.`userId` = U.`user_id`
            AND
                S.`propertyId` = 'skype'
        INNER JOIN
            `{$tbl[ 'user_online' ]}` AS O
            ON
                O.`user_id` = U.`user_id`";

if ( ! claro_is_platform_admin() && get_conf( 'UCONLINE_privacy' ) )
{
    $sql .= "LEFT JOIN
                `{$tbl[ 'rel_course_user' ]}` AS C
            ON
                C.`user_id` = U.`user_id`
            WHERE
                    C.`code_cours` IN (
                    SELECT
                        CU.`code_cours`
                    FROM
                        `{$tbl[ 'rel_course_user' ]}` AS CU"
            . "\n" . $additionalRestriction . "\n" .
                        "CU.`user_id` = " . Claroline::getDatabase()->escape( (int)claro_get_current_user_id() ) . ")";
}

$myPager = new claro_sql_pager( $sql, $offset, $userPerPage );
$myPager->set_sort_key( $pagerSortKey , $pagerSortDir );

$userList = $myPager->get_result_list();

if ( get_conf( 'UCONLINE_showUserPicture' ) )
{
    CssLoader::getInstance()->load( 'uconline' , 'screen' );
    
    foreach ( $userList as $index => $user )
    {
        $userData = user_get_properties( $user[ 'id' ] );
        
        $picturePath = user_get_picture_path( $userData );
        
        if ( $picturePath && file_exists( $picturePath ) )
        {
            $userList[ $index ][ 'picture' ] = user_get_picture_url( $userData );
        }
        else
        {
            $userList[ $index ][ 'picture' ] = get_icon_url( 'nopicture' );
        }
    }
}

$sortUrlList = $myPager->get_sort_url_list( $_SERVER['PHP_SELF'] );

$refreshTime = get_conf( 'UCONLINE_refreshTime' , 5 );

$listView = new PhpTemplate( dirname( __FILE__ ) . '/templates/onlineusrlist.tpl.php' );

$listView->assign( 'userList' , $userList );
$listView->assign( 'sortUrlList' , $sortUrlList );
$listView->assign( 'refreshTime' , $refreshTime );
$listView->assign( 'toolName' , $toolName );

//reloads the page within a specified amount of time
ClaroHeader::getInstance()->addHtmlHeader( '<meta http-equiv="Refresh" content="' . get_conf( 'UCONLINE_displayRefreshTime' ) . ';URL=user_connected.php" />' );

Claroline::getInstance()->display->body->appendContent( $listView->render() );
echo  Claroline::getInstance()->display->render();
