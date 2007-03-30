<?php

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

function jchat_display_message_list($onlyLastMsg = true)
{
    $messageList = jchat_get_message_list($onlyLastMsg);

	$resetLastReceivedMsg = false;
	
	$html = '';
	$previousDayTimestamp = 0;
	
	foreach( $messageList as $message )
	{
		if( get_days_from_timestamp($previousDayTimestamp) < get_days_from_timestamp($message['unixPostDate']) )
		{
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
    
	if( $resetLastReceivedMsg ) $_SESSION['jchat_lastReceivedMsg'] = time();
    
    
    sendHeader();	
   
	return $html;
}

function jchat_display_message($message)
{
	$html = '';
	$chatLine = ereg_replace("(http://)(([[:punct:]]|[[:alnum:]])*)","<a href=\"\\0\" target=\"_blank\">\\2</a>",$message['message']);
		
	$html .= '<span class="cl_jchat_msgDate">' . claro_html_localised_date('%H:%M:%S', $message['unixPostDate']) . '&nbsp;|</span>'
	.	 ' <span class="cl_jchat_userName">' . utf8_encode($message['prenom'] . ' ' . $message['nom']) 
	.	 '</span>&nbsp;: ' . $chatLine . "\n";
	
	return $html;
}

function jchat_get_message_list($onlyLastMsg = true)
{
    global $tblJchat, $tblUser;
    
	$sql = "SELECT UNIX_TIMESTAMP(JC.postDate) as unixPostDate, 
				`JC`.`message`, 
				`U`.`nom`,
				`U`.`prenom`, 
				`U`.`isCourseCreator` 
			FROM `".$tblJchat."` as JC, 
				`".$tblUser."` as U 
			WHERE JC.user_id = U.user_id ";
    
    if( $onlyLastMsg ) $sql .= " HAVING unixPostDate > ".$_SESSION['jchat_connectionTime'] . " ";
    
	$sql .=	" ORDER BY postDate";

	$messageList = claro_sql_query_fetch_all_rows($sql);
	
	return $messageList;
	
}

function jchat_flush_message_list()
{
    global $tblJchat;
    
    $sql = "DELETE FROM `".$tblJchat."`";
    
    return claro_sql_query($sql);
}

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

    
    $htmlContent = claro_parse_user_text( jchat_display_message_list(false) );
    
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

function sendHeader()
{
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
    header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
    header("Cache-Control: no-cache, must-revalidate" );
    header("Pragma: no-cache" );
    header("Content-Type: text/xml; charset=utf-8");
}

function get_days_from_timestamp($timestamp)
{
    return floor($timestamp/86400);
}
?>
