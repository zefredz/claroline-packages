<?php

function jchat_add_message($message)
{
    global $tblJchat, $_uid;
    
    if( $message != '' )
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
	foreach( $messageList as $message )
	{
		if( $_SESSION['jchat_lastReceivedMsg'] < $message['unixPostDate'] )
		{	
			$spanClass = ' class="newLine"'; 
			$resetLastReceivedMsg = true;
		}
		else
		{
			$spanClass = ' ';	
		}
		
		$html .= "\n" . '<span' . $spanClass . '>'.jchat_display_message($message).'</span>' . "\n";
	}
	 
	if( $resetLastReceivedMsg ) $_SESSION['jchat_lastReceivedMsg'] = time();
	
	return $html;
}

function jchat_display_message($message)
{
	$html = '';
	$chatLine = ereg_replace("(http://)(([[:punct:]]|[[:alnum:]])*)","<a href=\"\\0\" target=\"_blank\">\\2</a>",$message['message']);
		
	$html .= '<small>'
	.	 claro_html_localised_date(get_locale('dateTimeFormatShort'), $message['unixPostDate'])
	.	 ' &lt;<b>' . utf8_encode($message['prenom'] . ' ' . $message['nom']) 
	.	 '</b>&gt; ' . $chatLine 
	.	 '</small><br />' . "\n";
	
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

function jchat_archive_message_list()
{
	// create html content
	// create file from content
	// copy file to directory
}
?>
