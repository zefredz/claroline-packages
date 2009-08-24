<?php // $Id$

/**
 * Who is onlin@?
 *
 * @version     UCONLINE 0.9 $Revision$ - Claroline 1.9
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

$userPerPage = get_conf( 'usersPerPage' );

$tbl = claro_sql_get_tbl( array( 'user_online' , 'user_property' , 'user' ), array( 'course' => null ) );

$sql = "SELECT
            U.`nom`                 AS `lastname`,
            U.`prenom`              AS `firstname`,
            U.`email`               AS `email`,
            U.`user_id`             AS `user_id`,
            U.`isCourseCreator`     AS `isCourseCreator`,
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
                S.`propertyId` = 'skypeName'
        LEFT JOIN
            `{$tbl[ 'user_online' ]}` AS O
            ON
                O.`user_id` = U.`user_id`
        WHERE
                O.`user_id` = U.`user_id`";

$myPager = new claro_sql_pager( $sql, $offset, $userPerPage );
$myPager->set_sort_key( $pagerSortKey , $pagerSortDir );

$userList = $myPager->get_result_list();

$sortUrlList = $myPager->get_sort_url_list( $_SERVER['PHP_SELF'] );

$refreshTime = get_conf('UCONLINE_refreshTime',5);

$listView = new PhpTemplate( dirname( __FILE__ ) . '/templates/onlineusrlist.tpl.php' );

$listView->assign( 'userList' , $userList );
$listView->assign( 'sortUrlList' , $sortUrlList );
$listView->assign( 'refreshTime' , $refreshTime );
$listView->assign( 'toolName' , $toolName );

Claroline::getInstance()->display->body->appendContent( $listView->render() );
echo  Claroline::getInstance()->display->render();