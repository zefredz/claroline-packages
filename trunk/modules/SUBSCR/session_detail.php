<?php
/**
 * CLAROLINE
 *
 * Subscription tool for Claroline - detail of a given session
 *
 * @author Pierre Raynaud <pierre.raynaud@u-clermont1.fr>
 *
 * @package SUBSCRIBE
 *
 */

$tlabelReq = 'SUBSCR';

if ( isset($_REQUEST['cmd']) ) $cmd = $_REQUEST['cmd'];
else                           $cmd = null;

/**
 *  CLAROLINE MAIN SETTINGS
 */

require '../../claroline/inc/claro_init_global.inc.php';

add_module_lang_array($tlabelReq);

if ( ! get_init('in_course_context') || ! get_init('is_courseAllowed') || !get_init('is_authenticated') ) claro_disp_auth_form(true);

claro_set_display_mode_available(TRUE);

// CHECK DATA INPUT

    if ( isset($_REQUEST['session_id']) ) $session_id = $_REQUEST['session_id'];
    else                           $session_id = null;
    
        if ( isset($_REQUEST['user_id']) ) $user_id = $_REQUEST['user_id'];
    else                           $user_id = null;

// LIBRAIRIES

require_once dirname( __FILE__ ) . '/lib/subscribe.lib.php';
require_once get_path ( 'incRepositorySys' ) . '/lib/form.lib.php';

// TABLES

$tbl_subscription = claro_sql_get_tbl('CLFDsubscription');
$tbl_subscriptionUsers = claro_sql_get_tbl('CLFDsubscriptionUser');

$tbl_subscription = $tbl_subscription['CLFDsubscription'];
$tbl_subscriptionUsers = $tbl_subscriptionUsers['CLFDsubscriptionUser'];

$is_allowedToEdit  = claro_is_allowed_to_edit();

$dialogBox = new DialogBox();

// User subscription
if ($cmd == "exSubscribe")
{
	$infos_subscription = CLFDuserSubscription($_uid,$session_id);
	
	if ($infos_subscription)
	{
		if (CLFDunsubscribeUser($_uid,$session_id))
		header('Location:index.php?dialogBox='.get_lang('Unsubscription successful'));
		else
		header('Location:index.php?dialogBox='.get_lang('Unsubscription failed'));
	}
	else
	{
		if (CLFDsubscribeUser($_uid,$session_id))
		header('Location:index.php?dialogBox='.get_lang('Subscription successful'));
		else
		header('Location:index.php?dialogBox='.get_lang('Subscription failed'));
	}
}

if( $is_allowedToEdit )
{

    // Unsubscription
    if ($cmd == "exUnsubscribe")
    {
    		if (CLFDunsubscribeUser($user_id,$session_id))
    		$dialogBox->success(get_lang('User successfully unsubscribed')."<br>");
    }

    // Subscription list
    if ($cmd == "rqSubscribe")
    {
    	$users = CLFDgetUsersInCourse($_cid);
    	
    	$liste_inscrits_cours = array();	
    	$liste_inscrits_session = array();
    	
    	foreach ($users as $user_info => $value)
    	{
    		$liste_inscrits_cours[$value['user_id']] = $value['name']." ".$value['firstname'];
    	}

    	$inscrits = CLFDgetSubscribersList($session_id);
    	
    	foreach ($inscrits as $user_info => $value)
    	{
    		$liste_inscrits_session[$value['user_id']] = $value['name']." ".$value['firstname'];
    	}
    	
    	
    	$liste_inscrits = array_diff($liste_inscrits_cours,$liste_inscrits_session);
    	

    	$dialogBox->question("<h4>".get_lang('Subscribe user list')."</h4>"
        .	  '<form name="groupedit" method="POST" action="' . $_SERVER['PHP_SELF'] . '?cmd=massSubscription">' . "\n"
        .	  '<input type="hidden" name="session_id" value="'.$session_id.'">'
        .    '<table border="0" cellspacing="3" cellpadding="5">' . "\n"
        .    '<tr valign="top">'
        .    '<td align="left">'
        .    '<label for="inGroup">' . get_lang("Users") . '</label>'
        .    ' : '
        .    '</td>' . "\n"
        .	  '<td></td>'
        .    '<td align="right">'
        .    '<label for="inGroup">' . get_lang("Users to subscribe") . '</label>'
        .    ' : '
        .    '</td>' . "\n"
        .	  '</tr><tr>'
        .    '<td>'
        .    claro_html_form_select('subscribed[]',$liste_inscrits,'',array('id'=>'subscribed', 'size'=>'8', 'multiple'=>'multiple'),true)
        .    '</td>' . "\n"
        .    '<td align="center">' . "\n"
        .    '<input type="button" onClick="move(this.form.elements[\'subscribed\'],this.form.elements[\'unsubscribed\'])" value="   >>   " />' . "\n"
        .    '<br /><br />' . "\n"
        .    '<input type="button" onClick="move(this.form.elements[\'unsubscribed\'],this.form.elements[\'subscribed\'])" value="   <<   " />' . "\n"
        .    '</td>' . "\n"
        .    '<td>' . "\n"
        .    claro_html_form_select('unsubscribed[]',array(),'',array('id'=>'unsubscribed', 'size'=>'8', 'multiple'=>'multiple'), true) . "\n"
        .    '</td></tr>' . "\n"
        .    '<tr><td><input type=submit value="' . get_lang("Ok") . '" name="modify" onClick="selectAll(this.form.elements[\'subscribed\'],true)" />' . "\n"
        .	  '</table>')
        ;
    }

    if ($cmd == "massSubscription")
    {	
    	if ($_REQUEST['unsubscribed'])
    	{
    		foreach ($_REQUEST['unsubscribed'] as $to_subscribe)
    		{
    			if (!CLFDsubscribeUser($to_subscribe,$session_id))
    			$dialogBox->warniing(get_lang('Subscription failed')."<br>");
    		}
    	}
    }

    // get the tracking of a question as a csv file
    if($cmd == 'exportCSV')
    {
        include($includePath.'/lib/csv.class.php');

    	 $subscribers = CLFDgetSubscribersList($session_id); 

    	 $fields_list = get_conf('export_csv_fields');

    	 // Do not display fields if not in conf 
    	 for ($i=0;$i<count($subscribers);$i++)
    	 {
    	 	foreach ($subscribers[$i] AS $field => $value)
    	 	{
    	 		if (!in_array($field,$fields_list))
    	 		{
    	 			unset($subscribers[$i][$field]);
    				continue;	 		
    	 		}
    	 		if ($field == 'subscription_date')
    	 		$subscribers[$i][$field] = claro_disp_localised_date($dateTimeFormatShort,$value);
			
			// UTF8			
			else
			$subscribers[$i][$field] = utf8_encode($value);
    	 	}
    	 }

        // contruction of XML flow
        $csv = new csv();
        $csv->recordList = $subscribers;

            header("Content-type: application/csv");
            header('Content-Disposition: attachment; filename="test.csv"');
            echo $csv->export();
            exit;
    }

}


$nameTools = get_lang("Subscription tool");

$QUERY_STRING = ''; // used for the breadcrumb 
                  // when one need to add a parameter after the filename

$htmlHeadXtra[]='
<script type="text/javascript" language="JavaScript">
<!-- Begin javascript menu swapper
function move( inBox, outBox )
{
	var arrInBox = new Array();
	var arrOutBox = new Array();

	for ( var i=0; i<outBox.options.length; i++ )
	{
		arrOutBox[i] = outBox.options[i];
	}

	var outLength = arrOutBox.length;
	var inLength = 0;

	for ( var i=0; i<inBox.options.length; i++ )
	{
		var opt = inBox.options[i];
		if ( opt.selected )
		{
			arrOutBox[outLength] = opt;
			outLength++;
		}
		else
		{
			arrInBox[inLength] = opt;
			inLength++;
		}
	}

	inBox.length = 0;
	outBox.length = 0;

	for ( var i = 0; i < arrOutBox.length; i++ )
	{
		outBox.options[i] = arrOutBox[i];
	}

	for ( var i = 0; i < arrInBox.length; i++ )
	{
		inBox.options[i] = arrInBox[i];
	}
}
//  End -->
</script>

<script type="text/javascript" language="JavaScript">

function selectAll(cbList,bSelect) {
  for (var i=0; i<cbList.length; i++)
    cbList[i].selected = cbList[i].checked = bSelect
}

function reverseAll(cbList) {
  for (var i=0; i<cbList.length; i++) {
    cbList[i].checked = !(cbList[i].checked)
    cbList[i].selected = !(cbList[i].selected)
  }
}
</script>
';


include($includePath.'/claro_init_header.inc.php');


//display tool title and subtitle

$titleElement['mainTitle'] = get_lang("Subscription tool");

echo claro_html_tool_title($titleElement,false);
                      
/*--------------------------------------------------------------------
                           DIALOG BOX SECTION
      --------------------------------------------------------------------*/

echo $dialogBox->render();

$infos_session = CLFDinfoSession($session_id);

echo "<h3>".$infos_session['title']."</h3>"
	 ."<p>".$infos_session['intro_text']."</p>";

if( $is_allowedToEdit )
{
	echo "<a href=\"".$_SERVER['PHP_SELF']."?cmd=rqSubscribe&session_id=".$infos_session['id']."\" class='claroCmd'>"
		  ."<img src=\"".get_icon_url('user')."\" border=\"0\" alt=\"".get_lang('Modify')."\">".get_lang('New subscription')."</a> "
		  ."| <a href=\"".$_SERVER['PHP_SELF']."?cmd=exportCSV&session_id=".$infos_session['id']."\" class='claroCmd'>"
		  ."<img src=\"".get_icon_url('export')."\" border=\"0\" alt=\"".get_lang('Modify')."\">".get_lang('Export')."</a> "
		  ."| <a href=\"entry.php\" class='claroCmd'>".get_lang('Back to list')."</a>";



	$subscribers = CLFDgetSubscribersList($session_id);

	if( empty($subscribers) )
	{
		echo '<p><i>'.get_lang('No subscription yet').'</i></p>';
	}
	else
	{
    	echo '<p><strong>'.get_lang('Subscribers list').'</strong></p>' . "\n"
    	.    '<ul>' . "\n";
    	
    	foreach ($subscribers as $subscriber_info => $value)
    	{
    		echo "<li>".$value['name']." ".$value['firstname']." : ".get_lang('Subscribed on %date',array('%date'=>claro_disp_localised_date($dateTimeFormatShort,$value['subscription_date'])))
    			 ."<a href=\"".$_SERVER['PHP_SELF']."?cmd=exUnsubscribe&session_id=".$infos_session['id']."&user_id=".$value['user_id']."\"> "
    			 ."<img src=\"".get_icon_url('delete')."\" border=\"0\" alt=\" ".get_lang('Unsubscribe')."\">"			 
    			 ."</a>"			 
    			 ."</li>";
    	}
    	echo '</ul>' . "\n";
    }
	
	
}
else
{
	$infos_subscription = CLFDuserSubscription($_uid,$session_id);
	
	if (isset($infos_subscription))
	{
		echo "<p>".get_lang('Subscribed on %date',array('%date'=>claro_disp_localised_date($dateTimeFormatShort,$infos_subscription['subscription_date'])))."</p>"
			 ."<a href=\"".$_SERVER['PHP_SELF']."?cmd=exSubscribe&session_id=".$infos_session['id']."\">".get_lang('Click here to unsubscribe')."<a><br>";
	}	
	else
	{
		echo "<p>".get_lang('You have not subscribed yet')."</p>"
			 ."<a href=\"".$_SERVER['PHP_SELF']."?cmd=exSubscribe&session_id=".$infos_session['id']."\">".get_lang('Click here to subscribe')."<a><br>";	
	}
}

include $includePath.'/claro_init_footer.inc.php';
?>
