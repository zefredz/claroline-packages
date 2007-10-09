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
if ( count( get_included_files() ) == 1 ) die( '---' );

include_once claro_get_conf_repository().'CLONLINE.conf.php';
include_once dirname(__FILE__) . '/lib/Login_listener.class.php';

if( isset($GLOBALS['_uid']) )
{
    // record last action time only if authed
    $tbl = claro_sql_get_tbl('user_online', array('course'=>null));

    $sql = "DELETE FROM `" . $tbl['user_online'] . "`
            WHERE `user_id`=" . (int) $GLOBALS['_uid'];

    claro_sql_query($sql);

    $sql = "INSERT INTO `" . $tbl['user_online'] . "`
            SET `user_id` = " . (int) $GLOBALS['_uid'] . ",
            `last_action` = '" . date('Y-m-d H:i:s') . "'";

    claro_sql_query($sql);

}

// declare event manager dependencies and listener
$claro_login_listener = new Login_listener;

//set required event listener in the Claroline event manager
$claro_login_listener->addListener( "user_logout", 'delete_login_online' );
$claro_login_listener->addListener( "user_login", 'insert_login_online' );
$claro_login_listener->refresh_login_DB();

?>