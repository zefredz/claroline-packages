<?php // $Id$
/**
 * CLAROLINE
 *
 * @version 0.1 $Revision$
 *
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLPAGES
 *
 * @author Sebastien Piraux
 *
 */

$tlabelReq = 'CLCHAT';

require_once dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';

/*
 * Tool libraries
 */
require_once dirname(__FILE__) . '/lib/chat.lib.php';
require_once dirname(__FILE__) . '/lib/chatUserList.class.php';
require_once dirname(__FILE__) . '/lib/chatMsgList.class.php';


/*
 * Context
 */
$is_allowedToEdit = claro_is_allowed_to_edit();



/*
 * Init request vars
 */
$acceptedCmdList = array(	'rqRefresh',
							'rqAdd',
							'rqFlush', 
							'rqLogs', 
							'rqArchive',
                            'rqRefreshUserList'
                        );
if ( isset($_REQUEST['cmd']) && in_array($_REQUEST['cmd'], $acceptedCmdList) )   $cmd = $_REQUEST['cmd'];
else                                                                             $cmd = null;

if( isset($_REQUEST['message']) )   $msg = $_REQUEST['message'];
else                                $msg = '';                                


/*
 * Force headers
 */
header("Content-Type: text/xml; charset=utf-8");
header("Cache-Control: no-cache, must-revalidate" );
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Pragma: no-cache" );


/*
 * Other commands
 */
if( $cmd == 'rqAdd' )
{
    if( !empty($msg) && claro_is_user_authenticated() )
    {
        $msgList = new ChatMsgList();
	    $msgList->addMsg($msg, claro_get_current_user_id());
    }

    // always request refresh to have a response for ajax call
	$cmd = 'rqRefresh';    
}

if( $cmd == 'rqRefresh' )
{
    $msgList = new ChatMsgList();
    $msgList->load($_SESSION['chat_connectionTime']);
    
	echo $msgList->render();
	
	// keep my user alive in user list
	$chatUserList = new ChatUserList();
	$chatUserList->ping(claro_get_current_user_id());
	$chatUserList->prune();
	
    return;
}

if( $cmd == 'rqRefreshUserList' )
{
	$chatUserList = new ChatUserList();
    $chatUserList->load();
    
    echo $chatUserList->render();
    
    return;
}

/*
 * Admin only commands
 */

if( $cmd == 'rqFlush' && $is_allowedToEdit )
{
    $msgList = new ChatMsgList();
    if( $msgList->flush() )
    {
        echo get_lang('Chat reset');
    }
	
    return;	
}

if( $cmd == 'rqLogs' && $is_allowedToEdit )
{
    $msgList = new ChatMsgList();
    $msgList->load(1, $_SESSION['chat_connectionTime'] );

	echo $msgList->render();
	
    return;	
}

if( $cmd == 'rqArchive' && $is_allowedToEdit )
{
    $msgList = new ChatMsgList();
    $msgList->load();
    
    if( $chatFilename = $msgList->archive() )
    {
        $downloadLink = '<a href="'.get_module_url('CLDOC').'/document.php'.claro_url_relay_context('?').'">' . basename($chatFilename) . '</a>';
        
        echo get_lang('%chat_filename is now in the document tool. (<em>This file is visible</em>)',array('%chat_filename' => $downloadLink));
        return;
    }
    else
    {
        echo get_lang('Store failed');
        return;
    }    
}
?>