<?php // $Id$
/**
 *
 * @version 0.1 $Revision: 247 $
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @author Claroline team <info@claroline.net>
 *
 * @package CLONLINE
 *
 */
//$tlabelReq = 'CLONLINE';
$cidReset = TRUE;

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

require_once $includePath . '/lib/pager.lib.php';
require_once $includePath . '/lib/user.lib.php';

$nameTools   = get_lang('User(s) online');

$userPerPage = get_conf('usersPerPage');

$tbl = claro_sql_get_tbl(array('user_online','user'), array('course'=>null));

$sql = "SELECT U.`nom`                  AS `lastname`,
               U.`prenom`               AS `firstname`,
               U.`email`                AS `email`,
               U.`user_id`              AS `user_id`,
               U.`isCourseCreator`      AS `isCourseCreator`,
               O.`last_action`          AS `last_action`
          FROM `" . $tbl['user_online'] . "` AS O,
               `" . $tbl['user'] . "`        AS U
         WHERE U.`user_id` = O.`user_id`";

//Build pager with SQL request

$offset       = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0 ;
$myPager      = new claro_sql_pager($sql, $offset, $userPerPage);

$pagerSortKey = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'lastname';
$pagerSortDir = isset($_REQUEST['dir' ]) ? $_REQUEST['dir' ] : SORT_ASC;
$myPager->set_sort_key($pagerSortKey, $pagerSortDir);

$userList = $myPager->get_result_list();
$sortUrlList = $myPager->get_sort_url_list($_SERVER['PHP_SELF']);

/*
 * Output
 */

include($includePath.'/claro_init_header.inc.php');

echo claro_html_tool_title($nameTools);

// Refresh time
$refreshTime = get_conf('clonline_refreshTime',5);

echo '<p>' . get_lang('List of active users for the last %time minutes :', array('%time' => $refreshTime)) . '</p>' . "\n";

echo $myPager->disp_pager_tool_bar($_SERVER['PHP_SELF'])

.    "\n" . '<table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">' . "\n"
.    '<thead>' . "\n"
.    '<tr class="headerX" align="center" valign="top">' . "\n";

if( get_conf('showUserId') ) echo '<th><a href="' . $sortUrlList['user_id'] . '">' .  get_lang('No.') . '</a></th>' . "\n";

echo '<th><a href="' . $sortUrlList['lastname'] . '">' .  get_lang('Last name')    . '</a></th>' . "\n"
.    '<th><a href="' . $sortUrlList['firstname'] . '">' .  get_lang('First name')   . '</a></th>' . "\n";

if( get_conf('showEmail') ) echo '<th><a href="' . $sortUrlList['email'] . '">' .  get_lang('Email') . '</a></th>' . "\n";

if( get_conf('showStatus') ) echo '<th><a href="' . $sortUrlList['isCourseCreator'] . '">' .  get_lang('Status') . '</a></th>' . "\n";


echo '</tr>' . "\n"
.    '</thead>' . "\n"
.    '<tbody>' . "\n\n";


foreach($userList as $user)
{
    echo '<tr>' . "\n";

    if( get_conf('showUserId') ) echo  '<td align="center">' . $user['user_id'] . '</td>' . "\n";

    echo '<td>' . ( !empty($user['lastname'])?$user['lastname']:'&nbsp;')  . '</td>' . "\n";
    echo '<td>' . ( !empty($user['firstname'])?$user['firstname']:'&nbsp;')  . '</td>' . "\n";

    if( get_conf('showEmail') )
    {
        if( !empty($user['email']) )
        {
            echo '<td><a href="mailto:'. $user['email'] .'">' . $user['email'] . '</a></td>' . "\n";
        }
        else
        {
            echo '<td>-</td>' . "\n";
        }
    }

    if( get_conf('showStatus') ) echo '<td>' . ( $user['isCourseCreator']?get_lang('Course creator'):get_lang('User')) . '</td>' . "\n";

    echo '</tr>' . "\n\n";
}

echo '</tbody>' . "\n"
.    '</table>' . "\n"
.    $myPager->disp_pager_tool_bar($_SERVER['PHP_SELF']);


include $includePath . '/claro_init_footer.inc.php';

?>
