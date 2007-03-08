<?php // $Id$
/**
 *
 * @version 0.1 $Revision$
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @author Claroline team <info@claroline.net>
 *
 * @package CLONLINE
 *
 */
//$tlabelReq = 'CLONLINE';

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

require_once $includePath . '/lib/pager.lib.php';
require_once $includePath . '/lib/user.lib.php';

$nameTools   = get_lang('User(s) online');
$userPerPage = get_conf('usersPerPage' , 10);

// should stay the same as session time on server

$tbl = claro_sql_get_tbl(array('user_online','user'), array('course'=>null));

$sql = "SELECT U.`nom`                  AS `lastname`,
               U.`prenom`               AS `firstname`,
               U.`email`                AS `email`,
               U.`user_id`              AS `user_id`,
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

//----------------------------------
// DISPLAY
//----------------------------------

//Display Claroline top banner

include($includePath.'/claro_init_header.inc.php');

//display title

echo claro_html_tool_title($nameTools)
//display desc message.
.    get_lang('User(s) active(s) for the last %time minute(s) :', array('%time' => get_conf('refreshTime')))

//Display Pager list

.    $myPager->disp_pager_tool_bar($_SERVER['PHP_SELF'])

.    "\n" . '<table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">' . "\n"
.    '<thead>' . "\n"
.    '<tr class="headerX" align="center" valign="top">' . "\n";

if( get_conf('showUserId') ) echo '<th><a href="' . $sortUrlList['user_id'     ] . '">' .  get_lang('No.')          . '</a></th>' . "\n";

echo '<th><a href="' . $sortUrlList['lastname'    ] . '">' .  get_lang('Last Name')    . '</a></th>' . "\n"
.    '<th><a href="' . $sortUrlList['firstname'   ] . '">' .  get_lang('First Name')   . '</a></th>' . "\n";

if( get_conf('showEmail') ) echo '<th><a href="' . $sortUrlList['email'       ] . '">' .  get_lang('Email')        . '</a></th>' . "\n";

echo '</tr>' . "\n\n" . '<tbody>' . "\n";


foreach($userList as $user)
{
    echo '<tr>' . "\n";

    if( get_conf('showUserId') ) echo  '<td align="center">' . $user['user_id'] . '</td>' . "\n";

    echo '<td>' . $user['lastname'] . '</td>' . "\n";
    echo '<td>' . $user['firstname'] . '</td>' . "\n";

    if( get_conf('showEmail') ) echo '<td>' . $user['email'] . '</td>' . "\n";
    
    echo '</tr>' . "\n\n";
}

//end table...
echo '</tbody>' . "\n"
.    '</table>' . "\n"

//pager

.    $myPager->disp_pager_tool_bar($_SERVER['PHP_SELF'])
;

//display Claroline footer

include $includePath . '/claro_init_footer.inc.php';

?>
