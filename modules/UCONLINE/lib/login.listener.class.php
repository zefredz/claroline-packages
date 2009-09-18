<?php // $Id$

/**
 * Who is onlin@?
 *
 * @version     UCONLINE 0.9 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCONLINE
 * @author      Frederic Minne <frederic.minne@uclouvain.be>
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * Listener for logged in users' actions
 * @author      Frederic Minne <frederic.minne@uclouvain.be>
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */
class LoginListener extends EventDriven
{
    var $tbl;
    
    /**
     * constructor
     */
    public function __construct()
    {
        $this->tbl = claro_sql_get_tbl('user_online' , array('course'=>null));
    }
    
    /**
     * Updates the 'last action' database entry for the current user
     */
    function insert_login_online()
    {
        $timeOffset = ( isset( $_COOKIE[ 'time_offset' ] ) ) ? (int)$_COOKIE[ 'time_offset' ] : 0;
        
        $tbl = claro_sql_get_tbl('user_online', array( 'course'=>null ) );
        
        Claroline::getDatabase()->exec( "
            DELETE FROM
                `{$tbl['user_online']}`
            WHERE
                `user_id` = " . Claroline::getDatabase()->escape( claro_get_current_user_id() )
        );
        
        Claroline::getDatabase()->exec( "
            INSERT INTO
                `{$tbl['user_online']}`
            SET
                `user_id` = " . Claroline::getDatabase()->escape( claro_get_current_user_id() ) . ",
                `last_action` = " . Claroline::getDatabase()->quote( date('Y-m-d H:i:s') ) . ",
                `time_offset` = " . Claroline::getDatabase()->escape( $timeOffset )
        );
    }
    
    /**
     * Deletes the corresponding entries when the user logs himself off
     * ( any entry in the database with the same user )
     * @param object $event
     */
    function delete_login_online( $event )
    {
        $event_arguments = $event->getArgs();
        
        return Claroline::getDatabase()->exec( "
            DELETE FROM
            `{$this->tbl[ 'user_online' ]}`
            WHERE
            `user_id` = " . Claroline::getDatabase()->escape( (int) $event_arguments[ 'uid' ] )
        );
    }
    
    /**
     * flush old connection
     */
    function refresh_login_DB()
    {
        $refreshTime = get_conf( 'clonline_refreshTime' , 5 );
        
        $someTimeAgo = date( 'Y-m-d H:i:s' , time() - ( $refreshTime * 60 ) );
        
        return Claroline::getDatabase()->exec( "
            DELETE FROM
                `{$this->tbl[ 'user_online' ]}`
            WHERE `last_action` < " . Claroline::getDatabase()->quote( $someTimeAgo )
        );
    }
}