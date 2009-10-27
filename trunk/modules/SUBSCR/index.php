<?php
/**
 * CLAROLINE
 *
 * Subscription tool for Claroline
 *
 * @author Pierre Raynaud <pierre.raynaud@u-clermont1.fr>
 *
 * @package SUBSCRIBE
 *
 */

$tlabelReq = 'SUBSCR';

if (isset($_REQUEST['cmd']) ) $cmd = $_REQUEST['cmd'];
else                           $cmd = null;

/**
 *  CLAROLINE MAIN SETTINGS
 */

require '../../claroline/inc/claro_init_global.inc.php';

if ( ! get_init('in_course_context') || ! get_init('is_courseAllowed') || !get_init('is_authenticated') ) claro_disp_auth_form(true);

// initialise view mode tool
claro_set_display_mode_available(true);

$dialogBox = new DialogBox();

/*
 * Library for images
 */

require_once get_path ( 'incRepositorySys' )  . '/lib/image.lib.php';
require_once get_path ( 'incRepositorySys' ) .'/lib/form.lib.php';
require_once get_path ( 'incRepositorySys' ) . '/lib/datavalidator.lib.php'; 

require_once dirname( __FILE__ ) . '/lib/subscribe.lib.php';
require_once dirname( __FILE__ ) . '/lib/CLFDform.lib.php';
require_once dirname( __FILE__ ) . '/lib/CLFDdatavalidator.lib.php';


//  TABLE

$tbl_subscription = claro_sql_get_tbl('CLFDsubscription');
$tbl_subscriptionUsers = claro_sql_get_tbl('CLFDsubscriptionUser');
$tbl_incompatibility = claro_sql_get_tbl('CLFDsuscriptionIncompat');

$tbl_subscription = $tbl_subscription['CLFDsubscription'];
$tbl_subscriptionUsers = $tbl_subscriptionUsers['CLFDsubscriptionUser'];
$tbl_incompatibility = $tbl_incompatibility['CLFDsuscriptionIncompat'];

$is_allowedToEdit  = claro_is_allowed_to_edit();

//  CHECK DATA INPUT
    $infos_session = array();
    
    if ( isset($_REQUEST['session_id']) ) $session_id = $_REQUEST['session_id'];
    else                           $session_id = null;
    
    if (isset($session_id))
    {
        $infos_session = CLFDinfoSession($session_id);
    	
    	if (isset($_REQUEST['allow_modification']))
    	$allow_users_modification = $_REQUEST['allow_modification'];
    	else
    	$allow_users_modification = $infos_session['allow_modification'];
    	
    	//Create timestamp from $_REQUEST
    	if (isset($_REQUEST['startDay']))
    	$startDate = mktime($_REQUEST['startHour'], $_REQUEST['startMinute'], 0,$_REQUEST['startMonth'], $_REQUEST['startDay'], $_REQUEST['startYear']);
    	else
    	$startDate = $infos_session['startDate'];
    
    	if (isset($_REQUEST['endDay']))
    	$endDate = mktime($_REQUEST['endHour'], $_REQUEST['endMinute'], 0,$_REQUEST['endMonth'], $_REQUEST['endDay'], $_REQUEST['endYear']);
    	else
    	$endDate = $infos_session['endDate'];
    	
    	if (isset($_REQUEST['title']))
    	$title = $_REQUEST['title'];
    	else
    	$title = $infos_session['title'];
    	
    	if (isset($_REQUEST['intro_text']))
    	$intro_text = $_REQUEST['intro_text'];
    	else
    	$intro_text = $infos_session['intro_text'];
    	
    	if (isset($_REQUEST['max_users']))
    	$max_users = $_REQUEST['max_users'];
    	else
    	$max_users = $infos_session['max_users'];
    	
        if ( isset($_REQUEST['incompatibilities']) ) 
        $incompatibilities = $_REQUEST['incompatibilities'];
        else    $incompatibilities = null;
    }
/*
 *	exCreate
 *	Create a new subscription session
*/
if ($cmd == 'exCreate')
{
		$validator = new CLFDdataValidator();
        $dataList = array('title'  => $title,
                        'max_users' => $max_users,
                        'startDate' => array($_REQUEST['startHour'], $_REQUEST['startMinute'],0,$_REQUEST['startMonth'],$_REQUEST['startDay'],$_REQUEST['startYear']),
                        'endDate' => array($_REQUEST['endHour'], $_REQUEST['endMinute'],0,$_REQUEST['endMonth'],$_REQUEST['endDay'],$_REQUEST['endYear']));

 		$validator->setDataList($dataList);

		$validator->addRule('title' , get_lang('Title is missing'), 'required'  );
		$validator->addRule('max_users', get_lang('The number of places must be numeric')   , 'numeric');
		$validator->addRule('startDate'    , get_lang('%date not valid',array('%date'=>get_lang('Start date'))), 'checkdate'      );
		$validator->addRule('endDate'    , get_lang('%date not valid',array('%date'=>get_lang('End date'))), 'checkdate'      );
		$validator->addRule(array('startDate','endDate')    , get_lang('Incorrect date range'), 'checkDateRange'      );

		if ( $validator->validate(DATAVALIDATOR_STRICT_MODE) )
	   {
	   	$dataList['allow_modification'] = $allow_users_modification;
	   	$dataList['intro_text'] = $intro_text;
	   	
			$new_session_id = CLFDcreateEditSession($dataList,$session_id);

	   	
			if (isset($new_session_id))
			{
				if (CLFDaddIncompatibilities($_cid,$new_session_id,$incompatibilities))
				{
					if ($session_id)
					$dialogBox->success(get_lang('Session updated'));
					else				
					$dialogBox->success(get_lang('Session created'));
				}			
			}
		}
		else
		{
			foreach ($validator->getErrorList() as $erreur)
			{
				$dialogBox->error($erreur.'<br />');
			}
			
			if (isset($session_id))
			$cmd = "rqEdit";
			else
			$cmd = "rqCreate";
		}
}


/*
 *	RqCreate
 *	Create a new subscription session
*/
if ($cmd == 'rqCreate')
{

	$strToBox = '<form action="'.$_SERVER['PHP_SELF'].'" method="post">' . "\n"
    .    '<input type="hidden" name="claroFormId" value="'.uniqid('').'" />' . "\n"
    .    '<label for="comment">'.get_lang('Title').'</label><br />' . "\n"
    .    '<input type="text" name="title" value="'.$title.'" />' . "\n"
    .    '<input type="hidden" name="cmd" value="exCreate" />' . "\n"
    .    '<p>' . "\n"
    .    '<label for="intro_text">'. get_lang('Introduction text') .' : </Label><br />' . "\n"
    .    claro_html_textarea_editor('intro_text',$intro_text)  . "\n"
    .    '<p>'
    .    '<label for="startDate">'. get_lang('Start date') .' : </Label><br />' . "\n"
    .    claro_disp_date_form('startDay', 'startMonth', 'startYear', $startDate, 'long') . ' ' . claro_disp_time_form('startHour', 'startMinute', $startDate)
    .    '&nbsp;<small>' . get_lang('(d/m/y hh:mm)') . '</small><br />' . "\n"
    .    '<label for="endDate">'. get_lang('End date') .' : </Label><br />' . "\n"
    .    claro_disp_date_form('endDay', 'endMonth', 'endYear', $endDate, 'long') . ' ' . claro_disp_time_form('endHour', 'endMinute', $endDate)
    .    '&nbsp;<small>' . get_lang('(d/m/y hh:mm)') . '</small><br /><br />' . "\n"
    .    '<label for="places">'. get_lang('Places') .' : </Label><br />' . "\n"
    .    '<input type="text" name="max_users" value="'.$max_users.'" maxlength="6" />' . "\n";
		      	 
	
	if (get_conf('allow_users_to_modify'))
	{
		$strToBox .= '<br /><br />' . "\n"
		.    '<label for="allow_users_to_modify">'. get_lang('Allow users to modify their choice') .' : </Label><br />' . "\n"
        .    '<input type="radio" name="allow_modification" value="1"';
		      	 
		if (isset($allow_users_to_modify) && $allow_users_modification==1)
		$strToBox .= ' checked="checked"';
					 
		$strToBox .= ' />'.get_lang('Yes')
			        . '<input type="radio" name="allow_modification" value="0"';
		        
		if (!isset($allow_users_to_modify) && $allow_users_modification==0)
		$strToBox .= ' checked="checked"';

		$strToBox .= ' />'.get_lang('No')
			      	 .'<br />';
	}
	else
	$strToBox .= '<br /><input type="hidden" name="allow_modification" value="0" />';
	
	// Session incompatibility
	if (get_conf('session_incompatibility'))
	{
		$session_array = CLFDdisplayList($_cid,$is_allowedToEdit);
		
		$session_list = array();

		if (isset($session_array))		
		{
			foreach ($session_array as $session_info => $value)
			{
				$session_list[$value['id']] = $value['title'];
			}
	
			$strToBox .= '<br /><br />' . "\n"
		   	.    '<label for="incompatibilities">'. get_lang('Session subscription incompatible with') .' : </label><br />' . "\n"
		    .    ''.claro_html_form_select('incompatibilities[]',$session_list,'',array('size'=>'8', 'multiple'=>'multiple'),true);	
		}	
	}
	
		$strToBox .='<p><input type="submit" value="'. get_lang('Create') .'" />'.claro_html_button($_SERVER['PHP_SELF'], get_lang('Cancel'))  .'</p>'
						 ."</form>";
	$dialogBox->question($strToBox);
}


/*
 *	exRm
 *	Create a new subscription session
*/
if ($cmd == 'exRm')
{
	if (CLFDremoveSession($session_id,$_cid))
	$dialogBox->success(get_lang('Session deleted'));
	else
	$dialogBox->error(get_lang('Error deleting session'));
}


/*
 *	rqEdit
 *	Edit a subscription session
*/
if ($cmd == 'rqEdit')
{
	$strToBox = '<form action="'.$_SERVER['PHP_SELF'].'" method="post">' . "\n"
    .    '<input type="hidden" name="claroFormId" value="'.uniqid('').'" />' . "\n"
    .    '<label for="comment">'.get_lang('Title').'</label><br />' . "\n"
    .    '<input type="text" name="title" value="'.$title.'" />' . "\n"
    .    '<input type="hidden" name="cmd" value="exCreate" />' . "\n"
    .    '<input type="hidden" name="session_id" value="'.$session_id.'">' . "\n"
    .    '<p>' . "\n"
    .    '<label for="intro_text">'. get_lang('Introduction text') .' : </Label><br />' . "\n"
    .    claro_html_textarea_editor('intro_text',$intro_text)  . "\n"
    .    '<p>'
    .    '<label for="startDate">'. get_lang('Start date') .' : </Label><br />' . "\n"
    .    claro_disp_date_form('startDay', 'startMonth', 'startYear', $startDate, 'long') . ' ' . claro_disp_time_form('startHour', 'startMinute', $startDate)
    .    '&nbsp;<small>' . get_lang('(d/m/y hh:mm)') . '</small><br />' . "\n"
    .    '<label for="endDate">'. get_lang('End date') .' : </Label><br />' . "\n"
    .    claro_disp_date_form('endDay', 'endMonth', 'endYear', $endDate, 'long') . ' ' . claro_disp_time_form('endHour', 'endMinute', $endDate)
    .    '&nbsp;<small>' . get_lang('(d/m/y hh:mm)') . '</small><br /><br />' . "\n"
    .    '<label for="places">'. get_lang('Places') .' : </Label><br />' . "\n"
    .    '<input type="text" name="max_users" value="'.$max_users.'" maxlength="6" />' . "\n";
   
	
	if (get_conf('allow_users_to_modify'))
	{
		$strToBox .= '<br /><br /><label for="allow_users_to_modify">'. get_lang('Allow users to modify their choice') .' : </Label>' . "\n"
			      	 .'<br /><input type="radio" name="allow_modification" value="1"';
		      	 
		if (isset($allow_users_modification) && $allow_users_modification==1)
		$strToBox .= " checked";
					 
		$strToBox .= '>'.get_lang('Yes')
			        . '<input type="radio" name="allow_modification" value="0"';
		        
		if (isset($allow_users_modification) && $allow_users_modification==0)
		$strToBox .= " checked";

		$strToBox .= '>'.get_lang('No')
			      	 .'<br />';
	}
	else
	$strToBox .= '<input type="hidden" name="allow_modification" value=0>';
	
	$dialogBox->question($strToBox);
	
	// Session incompatibility
	$selected_list = '';
	if (get_conf('session_incompatibility'))
	{
		$session_array = CLFDdisplayList($_cid,$is_allowedToEdit);

		$incompat_list = CLFDcheckIncompatibilities($_cid,$session_id);		
		
		if( is_array($session_array) )		
		{
		    $session_list = array();
			foreach ($session_array as $session_info => $value)
			{
				if ($value['id'] == $session_id)
				continue;
				
				if (is_array($incompat_list))
				{
					if (in_array($value['id'],$incompat_list))
					    $selected_list = $value['id'];
					else 
					    $selected_list = '';
				}
				
				$session_list[$value['id']] = $value['title'];
			}
	        
			$dialogBox->question('<br />'
		   	   	 	  .'<br /><label for="incompatibilities">'. get_lang('Session subscription incompatible with') .' : </Label>' . "\n"
		      		 	  .'<br />'.CLFDclaro_html_form_select('incompatibilities[]',$session_list,$selected_list,array('size'=>'8', 'multiple'=>'multiple'),true));	
		}	
	}	
	
	$dialogBox->question('<p><input type="submit" value="'. get_lang('Modify') .'" />'.claro_html_button($_SERVER['PHP_SELF'], get_lang('Cancel')) .'</p>'
					 ."</form>");
}

$nameTools = get_lang("Subscription");


$htmlHeadXtra[] =
'<script>
function confirmation (name)
{
    if (confirm("' . clean_str_for_javascript(get_lang('Are you sure to delete')) . '"+\' \'+ name + "? "))
        {return true;}
    else
        {return false;}
}
</script>';


include($includePath.'/claro_init_header.inc.php');


//display tool title and subtitle

$titleElement['mainTitle'] = get_lang("Subscription tool");

echo claro_html_tool_title($titleElement,false);


/*--------------------------------------------------------------------
                           DIALOG BOX SECTION
      --------------------------------------------------------------------*/
    
echo $dialogBox->render();

$is_allowedToEdit ? $colspan = 7 : $colspan = 5;


echo '<p>' . "\n";

if( $is_allowedToEdit )
{
		echo '<a class="claroCmd" href="'.$_SERVER['PHP_SELF'].'?cmd=rqCreate">'
        .    '<img src="'.get_icon_url('subscription').'" alt="">'
        .    get_lang('New subscription session')
	    .   '</a>' . "\n";
}	

    echo '</p>' . "\n";

	// Ascending / descending
	if (isset($_REQUEST['asc']) && $_REQUEST['asc'] == 'DESC')
	$asc = 'ASC';
	else
	$asc = 'DESC';

	echo '<table class="claroTable emphaseLine" width="100%">' . "\n"
	.    '<tr class="headerX" align="center" valign="top">' . "\n"
    .    '<th><a href="'.$_SERVER['PHP_SELF'].'?order=title&asc='.$asc.'">'.get_lang('Name').'</a></th>' . "\n"
    .    '<th><a href="'.$_SERVER['PHP_SELF'].'?order=startDate&asc='.$asc.'">'.get_lang('Start date').'</a></th>' . "\n"
    .    '<th><a href="'.$_SERVER['PHP_SELF'].'?order=endDate&asc='.$asc.'">'.get_lang('End date').'</a></th>' . "\n"
    .    '<th>'.get_lang('Remaining places').'</th>' . "\n";
				
	if( $is_allowedToEdit ) 			
	{
		echo '<th>'.get_lang('Delete').'</th>' . "\n"
		.    '<th>'.get_lang('Edit').'</th>' . "\n"
		.    '<th>'.get_lang('Details').'</th>' . "\n";
	}
	else
		echo '<th>'.get_lang('Subscription state').'</th>' . "\n";

	echo '</tr>' . "\n" 
	.    '<tbody>';

    if (isset($_REQUEST['order'])) $order = $_REQUEST['order']; else $order ='';
	
	$liste_sessions = CLFDdisplayList($_cid,$is_allowedToEdit,$order,$asc);
	
	foreach ($liste_sessions as $session)
	{
	
		// Use the invisible class for the documents not in the date range
		unset ($style);
	
		if ($session['startDate'] > mktime() || $session['endDate'] < mktime())
		    $style=' class="invisible"';	
		else
		    $style = ''; 
	
		echo '<tr '.$style.' align="center" valign="top">' . "\n"
			 .'<td align="left">'.$session['title'].'</td>' . "\n"
			 .'<td>'.claro_disp_localised_date($dateTimeFormatShort,$session['startDate']).'</td>' . "\n"
			 .'<td>'.claro_disp_localised_date($dateTimeFormatShort,$session['endDate']).'</td>' . "\n"
			 .'<td>'.CLFDgetRemainingPlaces($session['id']).'</td>' . "\n";
		
		if( $is_allowedToEdit )			
		{
			/* DELETE COMMAND */
	
					echo '<td>'
					.    '<a href="'.$_SERVER['PHP_SELF'].'?cmd=exRm&session_id='.$session['id'].'" '
					.    'onClick="return confirmation(\''.clean_str_for_javascript($session['title']).'\');">'
				    .    '<img src="'.get_icon_url('delete').'" border="0" alt="'.get_lang('Delete').'" />'
					.    '</a>'
					.    '</td>' . "\n";
					

					/* EDIT COMMAND */

					echo "<td>"
						."<a href=\"".$_SERVER['PHP_SELF']."?cmd=rqEdit&session_id=".$session['id']."\">"
						."<img src=\"".get_icon_url('edit')."\" border=\"0\" alt=\"".get_lang('Modify')."\">"
						."</a>"
						."</td>\n";
						
				// export
				echo '<td align="center">'
				.	 '<a href="session_detail.php?session_id='.$session['id'].'">'
				.	 '<img src="'.get_icon_url('group').'" border="0" alt="'.get_lang('Details').'" />'
				.	 '</a>'
				.	 '</td>' . "\n";
					
		}	 
		else
		{
			echo "<td>";
	
			$infos = CLFDuserSubscription($_uid,$session['id']);
			if (isset($infos))
			echo get_lang('Subscribed on %date',array('%date'=>claro_disp_localised_date($dateTimeFormatShort,$infos)))."<br />";	
			
			if (CLFDisAllowedToSubscribe($_uid,$session['id'],$session['allow_modification']))
			{
				if (!isset($infos))
				echo '<a href="session_detail.php?session_id='.$session['id'].'">'.get_lang('Subscribe').'</a>';
			
				else
				echo '<a href="session_detail.php?session_id='.$session['id'].'">'.get_lang('Modify subscription').'</a>';
			}
			else
			echo '<i>'.get_lang('Subscription impossible')."</i>";			
			
			echo "</td>";
		}
		echo "</tr>";
	}

echo '</tbody>' . "\n"
.    '</table>' . "\n";

include $includePath.'/claro_init_footer.inc.php';
?>
