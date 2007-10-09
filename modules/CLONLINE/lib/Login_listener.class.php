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

//declare  the online user listener for this module

class LoginListener extends EventDriven
{
    var $tblUserOnline;

    /**
     * constructor
     */
    function LoginListener ()
    {
        $tbl_names = claro_sql_get_tbl('user_online', array('course'=>null));
		$this->tblUserOnline = $tbl_names['user_online'];
    }

    /**
     * @param object $event
     */
    function insert_login_online($event)
    {
        $event_arguments = $event->getArgs();

        // delete any potential entry in the database with the same user,
        // to avoid double entries

        $sql = "DELETE FROM `" . $this->tblUserOnline . "`
                      WHERE `user_id`=" . (int)$event_arguments['uid'];

        claro_sql_query($sql);

        $sql = "INSERT INTO `" . $this->tblUserOnline . "`
                SET `user_id` = " . (int) $event_arguments['uid'] . ",
                    `last_action` = '" . date('Y-m-d H:i:s') . "'";

        claro_sql_query($sql);

        $this->refresh_login_DB();
    }

    function delete_login_online($event)
    {
        $event_arguments = $event->getArgs();

        //delete any entry in the database with the same user

        $sql = "DELETE FROM `" . $this->tblUserOnline . "`
                      WHERE `user_id` = " . (int) $event_arguments['uid'];

        claro_sql_query($sql);

        $this->refresh_login_DB();

    }

    /**
     * flush old connection
     */
    function refresh_login_DB()
    {
        // Refresh time should not be less than 10 minutes
        $refreshTime = get_conf('clonline_refreshTime',5);

        $sql = "DELETE
                FROM `" . $this->tblUserOnline . "`
                WHERE `last_action` < DATE_SUB( NOW() , INTERVAL " . $refreshTime . " MINUTE )";

        claro_sql_query($sql);
    }
}

?>