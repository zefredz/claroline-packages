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

// We are logged in and must notice the last action date
$tbl = claro_sql_get_tbl('user_online', array('course'=>null));

if( isset($GLOBALS['_uid']) )
{
    $sql = "UPDATE `" . $tbl['user_online'] . "`
               SET `last_action` = '".date("Y-m-d H:i:s")."'
             WHERE `user_id` = " . (int) $GLOBALS['_uid'];

    claro_sql_query($sql);
}

// declare event manager dependencies and listener
$claro_login_listener = new Login_listener($claro_event_manager);

//set required event listener in the Claroline event manager
$claro_login_listener->addListener( 'delete_login_online', "user_logout");
$claro_login_listener->addListener( 'insert_login_online', "user_login");
$claro_login_listener->refresh_login_DB();


//declare  the online user listener for this module

class Login_listener extends EventDriven
{
    var $tblUserOnline;
    
    /**
     * constructor
     */
    function Login_listener ( &$registry )
    {
        parent::EventDriven( $registry );
        
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
        // Refresh time should not be less thant 10 minutes
        $refreshTime = max(10, (int)(ini_get('session.gc_maxlifetime')/60));
        
        $sql = "DELETE
                FROM `" . $this->tblUserOnline . "`
                WHERE `last_action` < DATE_SUB( NOW() , INTERVAL " . $refreshTime . " MINUTE )";
                
        claro_sql_query($sql);
    }
}
?>
