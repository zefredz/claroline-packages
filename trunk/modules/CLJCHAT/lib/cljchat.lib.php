<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * CLAROLINE
 *
 * @version 1.8 $Revision$
 *
 * @copyright (c) 2001-2006 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLJCHAT
 *
 * @author Claro Team <cvs@claroline.net>
 * @author Sebastien Piraux <pir@cerdecam.be>
 */
 
/**
 * Add a message to chat
 *
 * @author Sebastien Piraux <pir@cerdecam.be>
 * @param string $message
 * @return boolean
 */ 
 
function jchat_add_message($message)
{
    global $tblJchat, $_uid;
    
    if( $message != '' && $_uid )
	{
    	$sql = "INSERT INTO `".$tblJchat."`
    			SET `user_id` = '".(int) $_uid."', 
    				`group_id` = '0',
    				`message` = '".addslashes(htmlspecialchars($message))."',
    				`postDate` = NOW()";
    				
    	return claro_sql_query($sql);
    }
    else
    {
        return false;
    }
}

/**
 * Get html to display the message list
 *
 * @author Sebastien Piraux <pir@cerdecam.be>
 * @param boolean $onlyLastMsg true : get only the messages posted after connection, false : get all recorded messages
 * @return string html output
 */ 
 
function jchat_display_message_list($onlyLastMsg = true)
{
    $messageList = jchat_get_message_list($onlyLastMsg);

	$resetLastReceivedMsg = false;
	
	$html = '';
	$previousDayTimestamp = 0; // keep track of the day of the last displayed message
	
	foreach( $messageList as $message )
	{
		if( get_days_from_timestamp($previousDayTimestamp) < get_days_from_timestamp($message['unixPostDate']) )
		{
		    // display day separator
		    $html .= "\n" . '<span class="cl_jchat_dayLimit">'.claro_html_localised_date(get_locale('dateFormatLong'), $message['unixPostDate']).'</span>' . "\n";
		    
		    $previousDayTimestamp = $message['unixPostDate'];
		}
			
		if( $_SESSION['jchat_lastReceivedMsg'] < $message['unixPostDate'] )
		{	
			$spanClass = ' newLine'; 
			$resetLastReceivedMsg = true;
		}
		else
		{
			$spanClass = '';	
		}
		
		$html .= "\n" . '<span class="cl_jchat_msgLine' . $spanClass . '">'.jchat_display_message($message).'</span>' . "\n";
	}
    
    // keep track of the last display time 
	if( $resetLastReceivedMsg ) $_SESSION['jchat_lastReceivedMsg'] = time();
    
    
    sendHeader();	
   
	return $html;
}

/**
 * Get html to display one message with clickable links
 *
 * @author Sebastien Piraux <pir@cerdecam.be>
 * @param array $message('unixPostDate','message','lastname','firstname','isCourseCreator')
 * @return string html output for $message
 */ 
 
function jchat_display_message($message)
{
	// transform url to clickable links
	$chatLine = claro_parse_user_text($message['message']);

    $html = '';
		
	$html .= '<span class="cl_jchat_msgDate">' . claro_html_localised_date('%H:%M:%S', $message['unixPostDate']) . '&nbsp;|</span>'
	.	 ' <span class="cl_jchat_userName">' . utf8_encode($message['firstname'] . ' ' . $message['lastname']) 
	.	 '</span>&nbsp;: ' . $chatLine . "\n";
	
	return $html;
}

/**
 * get message list from DB
 *
 * @author Sebastien Piraux <pir@cerdecam.be>
 * @param boolean $onlyLastMsg true : get only the messages posted after connection, false : get all recorded messages
 * @return array array of message('unixPostDate','message','lastname','firstname','isCourseCreator')
 */ 
 
function jchat_get_message_list($onlyLastMsg = true)
{
    global $tblJchat, $tblUser;
    
	$sql = "SELECT UNIX_TIMESTAMP(JC.postDate) as unixPostDate, 
				`JC`.`message`, 
				`U`.`nom` as `lastname`,
				`U`.`prenom` as `firstname`, 
				`U`.`isCourseCreator` 
			FROM `".$tblJchat."` as JC, 
				`".$tblUser."` as U 
			WHERE JC.user_id = U.user_id ";
    
    if( $onlyLastMsg ) $sql .= " HAVING unixPostDate > ".$_SESSION['jchat_connectionTime'] . " ";
    
	$sql .=	" ORDER BY postDate";

	$messageList = claro_sql_query_fetch_all_rows($sql);
	
	return $messageList;
	
}

/**
 * Delete all messages from DB
 *
 * @author Sebastien Piraux <pir@cerdecam.be>
 * @return boolean true if query was successfull
 */ 
 
function jchat_flush_message_list()
{
    global $tblJchat;
    
    $sql = "DELETE FROM `".$tblJchat."`";
    
    return claro_sql_query($sql);
}

/**
 * Generate a fil with all messages and copy it in the document tool
 *
 * @author Sebastien Piraux <pir@cerdecam.be>
 * @return mixed filename if successfull, false if failed
 */ 
 
function jchat_archive_message_list()
{
    // Prepare archive file content
	$htmlContentHeader = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">' . "\n"
    .    '<html>' . "\n"
    .    '<head>' . "\n"
    .    '<title>' . get_lang('Chat') . '</title>'
    .    '</head>' . "\n"
    .    '<body>' . "\n\n";

    $htmlContentFooter = '</body>' . "\n\n"
    .    '</html>' . "\n";

    
    $htmlContent = jchat_display_message_list(false);
    
    $htmlContent = $htmlContentHeader . $htmlContent . $htmlContentFooter; 
    
    
    // filepath
    $courseDir = claro_get_course_path() .'/document';
    $baseWorkDir = get_path('coursesRepositorySys') . $courseDir;

    // Try to determine a filename that does not exist anymore
    // in the directory where the chat file will be stored

    $chatDate = 'chat.'.date('Y-m-j').'_';
    $i = 1;
    
    while ( file_exists($baseWorkDir.'/'.$chatDate.$i.'.html') ) $i++;

    $chatFilename = $baseWorkDir.'/'. $chatDate.$i.'.html';
    
    $fp = fopen($chatFilename, 'w');

    if( fwrite($fp, $htmlContent) )
    {
        return $chatFilename;
    }
    else
    {
        return false;
    }
}

/**
 * Send header requested by ajax
 *
 * @author Sebastien Piraux <pir@cerdecam.be>
 * @return boolean true
 */ 
 
function sendHeader()
{
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
    header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
    header("Cache-Control: no-cache, must-revalidate" );
    header("Pragma: no-cache" );
    header("Content-Type: text/xml; charset=utf-8");
    
    return true;
}

/**
 * Get the number of days equivalent to timestamp
 *
 * @author Sebastien Piraux <pir@cerdecam.be>
 * @return boolean true
 */ 
 
function get_days_from_timestamp($timestamp)
{
    return floor($timestamp/86400);
}
?>
