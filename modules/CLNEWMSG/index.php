<?php // $Id$
/**
 * New message notifier
 *
 * @version     CLNEWMSG 0.8 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLNEWMSG
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

require dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';

if ( claro_is_user_authenticated() )
{
    $tbl_mdb_names = claro_sql_get_main_tbl();
    $tbl_messages = $tbl_mdb_names[ 'im_message' ];
    $tbl_msg_status = $tbl_mdb_names[ 'im_message_status' ];
    
    if ( time() - (int)$_SESSION[ 'start_time' ] < get_conf( 'displayTime' ) )
    {
        $newMsg = Claroline::getDatabase()->query( "
            SELECT
                COUNT(*)
            FROM
                `{$tbl_msg_status}`
            WHERE
                is_read = 0
            AND
                is_deleted = 0
            AND
                user_id =" . Claroline::getDatabase()->escape( claro_get_current_user_id() )
        )->fetch( Database_ResultSet::FETCH_VALUE );
        
        if ( $newMsg )
        {
            $text = '<a href="' . htmlspecialchars( get_path( 'clarolineRepositoryWeb' ) . '/messaging/messagebox.php?box=inbox&cidReset=true' ) .'">';
            $text .= ( $newMsg == 1 ) ?  get_lang( 'You have an unread message!' ) :
                                        get_lang( 'You have %newMsg unread messages!' , array( '%newMsg' => $newMsg ) );
            $text .='</a>';
        }
    }
    else
    {
        $timeTrigger = date( 'Y-m-d H:i:s' , time() - get_conf( 'displayTime' ) );
        
        $newMsg = Claroline::getDatabase()->query( "
            SELECT
                COUNT(*)
            FROM
                `{$tbl_messages}` AS MSG
            INNER JOIN
                `{$tbl_msg_status}` AS STATUS
            ON
                STATUS.message_id = MSG.message_id
            AND
                STATUS.user_id =" . Claroline::getDatabase()->escape( claro_get_current_user_id() ) . "
            AND
                STATUS.is_read = 0
            AND
                STATUS.is_deleted = 0
            WHERE
                MSG.send_time >" . Claroline::getDatabase()->quote( $timeTrigger ) . "
        ;")->fetch( Database_ResultSet::FETCH_VALUE );
        
        if ( $newMsg )
        {
            $text = '<a href="' . htmlspecialchars( get_path( 'clarolineRepositoryWeb' ) . '/messaging/messagebox.php?box=inbox&cidReset=true' ) .'">';
            $text .= ( $newMsg == 1 ) ?  get_lang( 'You got a new message!' ) :
                                        get_lang( 'You got %newMsg new messages!' , array( '%newMsg' => $newMsg ) );
            $text .='</a>';
        }
    }
    
    if ( isset( $text ) ) echo '<span class="up">' . $text . '</span>';
    else echo get_lang('No new message');
}
