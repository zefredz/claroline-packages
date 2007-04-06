<?php
//Label du module
$tlabelReq = 'CLAG2';
// Initialisation du noyau claroline
require_once "../../claroline/inc/claro_init_global.inc.php";
require_once "../../claroline/inc/lib/form.lib.php";
require_once './lib/agenda.lib.php';

// Code métier
$sayhello = "hello world";

$cours_id =  claro_get_current_course_id();
$user_id    = claro_get_current_user_id(); //find the user ID
$display_form=FALSE;
$display_command = FALSE;
$display='';
$dialogBox='';

$is_allowedToEdit = claro_is_allowed_to_edit();

if( !empty($_REQUEST['order']) )
    $orderDirection = strtoupper($_REQUEST['order']);
elseif( !empty($_SESSION['orderDirection']) )
    $orderDirection = strtoupper($_SESSION['orderDirection']);
else
    $orderDirection = 'ASC';

$acceptedValues = array('DESC','ASC');

if( ! in_array($orderDirection, $acceptedValues) )
{
    $orderDirection = 'ASC';
}

$_SESSION['orderDirection'] = $orderDirection;

if ( $is_allowedToEdit )
{
	if ( isset($_REQUEST['cmd']) ) $cmd =$_REQUEST['cmd'];
	else 						   $cmd =NULL;

	if ( isset($_REQUEST['type']) ) $type =$_REQUEST['type'];
	else 						    $type ='';

    if ( isset($_REQUEST['id']) ) $id = (int) $_REQUEST['id'];
    else                          $id = 0;

    if ( isset($_REQUEST['title']) ) $title = trim($_REQUEST['title']);
    else                             $title = '';

    if ( isset($_REQUEST['content']) ) $content = trim($_REQUEST['content']);
    else                               $content = '';

    if ( isset($_REQUEST['multi']) ) $multi = trim($_REQUEST['multi']);
    else                             $multi = 1;

    /*------------------------------------------------------------------------
    EVENT EDIT AND ADD
	function called durring an add or edit request.
	if request edit event take back old information 
    --------------------------------------------------------------------------*/

	if ( 'rqEdit' == $cmd  || 'rqAdd' == $cmd  )
    {
        claro_set_display_mode_available(false);

        if ( 'rqEdit' == $cmd  && !empty($id) )
        {
            $editedEvent = agenda_get_item($id) ;
            $editedEvent['startdate'] = strtotime($editedEvent['startdayOld'].' '.$editedEvent['starthourOld']);
			$editedEvent['enddate']   = strtotime($editedEvent['enddayOld'].' '.$editedEvent['endhourOld']);
			$editedEvent['type']      = $editedEvent['typeOld'];
            $nextCommand = 'exEdit';
        }
        else
        {
            $editedEvent['user_id'       ] = '';
            $editedEvent['title'         ] = '';
            $editedEvent['content'       ] = '';
            $editedEvent['startdate'	 ] = time();
            $editedEvent['enddate'	     ] = time();
            $editedEvent['type'          ] = '';
            $editedEvent['id'            ] = '';
            $editedEvent['type'          ] = '';


            $nextCommand = 'exAdd';
        }
        $display_form =TRUE;
    } // end if cmd == 'rqEdit' && cmd == 'rqAdd'


    /*------------------------------------------------------------------------
    EVENT ADD
	use only for an add request. 
    --------------------------------------------------------------------------*/
	if ( 'exAdd' == $cmd )
    {
        $startdate_selection = $_REQUEST['fyear'] . '-' . $_REQUEST['fmonth'] . '-' . $_REQUEST['fday'];
        $starthour           = $_REQUEST['fhour'] . ':' . $_REQUEST['fminute'] . ':00';
        $enddate_selection 	 = $_REQUEST['ayear'] . '-' . $_REQUEST['amonth'] . '-' . $_REQUEST['aday'];
        $endhour          	 = $_REQUEST['ahour'] . ':' . $_REQUEST['aminute'] . ':00';
        $type           	 = $_REQUEST['type'];
		$multi				 = $_REQUEST['multi'];

		//date desting
		$firstcompdate = date("Y-m-d-H-i-s",mktime($_REQUEST['fhour'],$_REQUEST['fminute'],0,$_REQUEST['fmonth'],$_REQUEST['fday'],$_REQUEST['fyear']));
		$secondcompdate = date("Y-m-d-H-i-s",mktime($_REQUEST['ahour'],$_REQUEST['aminute'],0,$_REQUEST['amonth'],$_REQUEST['aday'],$_REQUEST['ayear']));
		if ($secondcompdate < $firstcompdate)
		{
			$dialogBox .= '<p>' . get_lang('Invalid Dates') . '</p>' . "\n";
		}
		else
		{
			if ( $multi > 1)
			{
				//function that automaticaly create multi events
				for($i=0; $i < $multi; $i++)
				{
					$startdate_elements  = explode("-",$startdate_selection);
					$starttimestamp 	 = mktime(0,0,0,$startdate_elements[1],$startdate_elements[2]+7*$i,$startdate_elements[0]);
					$newstartdate_selection = strftime('%Y-%m-%d',$starttimestamp);
					$enddate_elements    = explode("-",$enddate_selection);
					$endtimestamp 		 = mktime(0,0,0,$enddate_elements[1],$enddate_elements[2]+7*$i,$enddate_elements[0]);
					$newenddate_selection	 = strftime('%Y-%m-%d',$endtimestamp);
					$entryId   			 = agenda_add_item($cours_id,$user_id,$title,$content, $newstartdate_selection, $starthour, $newenddate_selection, $endhour, $type) ; //send data to the DB
				}
			}
			else
			{
				$entryId = agenda_add_item($cours_id,$user_id,$title,$content, $startdate_selection, $starthour, $enddate_selection, $endhour, $type) ; //send data to the D
			}
		}
		if ( $entryId != false )
        {
            $dialogBox .= '<p>' . get_lang('Event added to the agenda') . '</p>' . "\n";
 /*         $dialogBox .= linker_update(); //return textual error msg
            if ( CONFVAL_LOG_CALENDAR_INSERT )
            {
                event_default('CALENDAR', array ('ADD_ENTRY' => $entryId));
            }
            // notify that a new agenda event has been posted
            $eventNotifier->notifyCourseEvent('agenda_event_added', claro_get_current_course_id(), claro_get_current_tool_id(), $entryId, claro_get_current_group_id(), '0');
            $autoExportRefresh = TRUE;
    */    }
        else
        {
            $dialogBox .= '<p>' . get_lang('Unable to add the event to the agenda') . '</p>' . "\n";
        }
    }


    /*------------------------------------------------------------------------
    EDIT EVENT COMMAND
    --------------------------------------------------------------------------*/


    if ( 'exEdit' == $cmd )
    {
        $startdate_selection = $_REQUEST['fyear'] . '-' . $_REQUEST['fmonth'] . '-' . $_REQUEST['fday'];
        $starthour           = $_REQUEST['fhour'] . ':' . $_REQUEST['fminute'] . ':00';
        $enddate_selection 	 = $_REQUEST['ayear'] . '-' . $_REQUEST['amonth'] . '-' . $_REQUEST['aday'];
        $endhour           	 = $_REQUEST['ahour'] . ':' . $_REQUEST['aminute'] . ':00';
		$type           	 = $_REQUEST['type'];
		$author 			 = $user_id;

        if ( !empty($id) )
        {	
            if ( agenda_update_item($id,$title,$content,$startdate_selection,$starthour,$enddate_selection,$endhour,$author,$type,$cours_id))
            {
//                $dialogBox .= linker_update(); //return textual error msg
//                $eventNotifier->notifyCourseEvent('agenda_event_modified', claro_get_current_course_id(), claro_get_current_tool_id(), $id, claro_get_current_group_id(), '0'); // notify changes to event manager
//                $autoExportRefresh = TRUE;
                $dialogBox .= '<p>' . get_lang('Event updated into the agenda') . '</p>' . "\n";
            }
            else
            {
                $dialogBox .= '<p>' . get_lang('Unable to update the event into the agenda') . '</p>' . "\n";
            }
        }
    }


    /*------------------------------------------------------------------------
    DELETE EVENT COMMAND
    --------------------------------------------------------------------------*/

    if ( 'exDelete' == $cmd && !empty($id) )
    {

        if ( agenda_delete_item($id,$course_id) )
        {
            $dialogBox .= '<p>' . get_lang('Event deleted from the agenda') . '</p>' . "\n";

/*            $eventNotifier->notifyCourseEvent('agenda_event_deleted', claro_get_current_course_id(), claro_get_current_tool_id(), $id, claro_get_current_group_id(), '0'); // notify changes to event manager
            $autoExportRefresh = TRUE;
            if ( CONFVAL_LOG_CALENDAR_DELETE )
            {
                event_default('CALENDAR',array ('DELETE_ENTRY' => $id));
            }
 */       }
        else
        {
            $dialogBox = '<p>' . get_lang('Unable to delete event from the agenda') . '</p>' . "\n";
        }

//        linker_delete_resource();
    }


    /*----------------------------------------------------------------------------
    DELETE ALL EVENTS COMMAND
    ----------------------------------------------------------------------------*/

    if ( 'exDeleteAll' == $cmd )
    {
        if ( agenda_delete_all_items($cours_id))
        {
            $dialogBox .= '<p>' . get_lang('Event deleted from the agenda') . '</p>' . "\n";

/*            if ( CONFVAL_LOG_CALENDAR_DELETE )
            {
                event_default('CALENDAR', array ('DELETE_ENTRY' => 'ALL') );
            }
*/        }
        else
        {
            $dialogBox = '<p>' . get_lang('Unable to delete event from the agenda') . '</p>' . "\n";
        }

//        linker_delete_all_tool_resources();
    }

}


    /*-------------------------------------------------------------------------
    EDIT EVENT VISIBILITY
    ---------------------------------------------------------------------------*/

    if ( 'mkShow' == $cmd  || 'mkHide' == $cmd )
    {
        if ($cmd == 'mkShow')
        {
            $visibility = 'SHOW';
 //           $eventNotifier->notifyCourseEvent('agenda_event_visible', claro_get_current_course_id(), claro_get_current_tool_id(), $id, claro_get_current_group_id(), '0'); // notify changes to event manager
 //           $autoExportRefresh = TRUE;
        }

        if ($cmd == 'mkHide')
        {
            $visibility = 'HIDE';
  //          $eventNotifier->notifyCourseEvent('agenda_event_invisible', claro_get_current_course_id(), claro_get_current_tool_id(), $id, claro_get_current_group_id(), '0'); // notify changes to event manager
   //         $autoExportRefresh = TRUE;
        }

        if ( agenda_set_item_visibility($id, $visibility,$cours_id) )
        {
            $dialogBox = get_lang('Visibility modified');
        }
        //        else
        //        {
        //            //error on delete
        //        }
    }

$eventList = agenda_get_item_list($cours_id, $orderDirection);
    /*------------------------------------------------------------------------
    DISPLAY INPUT BOX
    --------------------------------------------------------------------------*/
if ($display_form)
{
    $display= '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">'
    .    claro_form_relay_context()
    .    '<input type="hidden" name="cmd" value="' . $nextCommand . '" />'
    .    '<input type="hidden" name="id"  value="' . $editedEvent['id'] . '" />'
    .    '<table>' . "\n"
    .    '<tr valign="top">' . "\n"
    .    '<td align="right">' . get_lang('Start date') . ' : '
    .    '</td>' . "\n"
    .    '<td>'
    .    claro_html_date_form('fday', 'fmonth', 'fyear', $editedEvent['startdate'], 'long' ) . ' '
    .    claro_html_time_form('fhour','fminute', $editedEvent['startdate']) . '&nbsp;'
    .    '<small>' . get_lang('(d/m/y hh:mm)') . '</small>'
    .    '</td>' . "\n"
    .    '</tr>' . "\n"
    .    '<td align="right">' . get_lang('End date') . ' : '
    .    '</td>' . "\n"
    .    '<td>'
    .    claro_html_date_form('aday', 'amonth', 'ayear', $editedEvent['enddate'], 'long' ) . ' '
    .    claro_html_time_form('ahour','aminute', $editedEvent['enddate']) . '&nbsp;'
    .    '<small>' . get_lang('(d/m/y hh:mm)') . '</small>'
    .    '</td>' . "\n"
    .    '</tr>' . "\n"
    .    '<td align="right">'
    .    '<label for="type">' . get_lang('Occurence') . '</label> : '
    .    '</td>' . "\n"
    .    '<td>'
    .    '<input type="text" name="multi" id="multi" size="20" maxlength="20" value="1" />'
    .    '</td>' . "\n"
    .    '</tr>' . "\n"
    .    '<tr valign="top">' . "\n"
    .    '<td align="right">' . "\n"
    .    '<label for="title">' . "\n"
    .    get_lang('Title') . "\n"
    .    ' : </label>' . "\n"
    .    '</td>' . "\n"
    .    '<td>' . "\n"
    .    '<input size="80" type="text" name="title" id="title" value="'
    .    htmlspecialchars($editedEvent['title']). '" />' . "\n"
    .    '</td>' . "\n"
    .    '</tr>' . "\n"
    .    '<tr valign="top">' . "\n"
    .    '<td align="right">' . "\n"
    .    '<label for="content">' . "\n"
    .    get_lang('Detail')
    .    ' : ' . "\n"
    .    '</label>' . "\n"
    .    '</td>' . "\n"
    .    '<td>' . "\n"
    .    claro_html_textarea_editor('content', $editedEvent['content'], 12, 67, $optAttrib = ' wrap="virtual" ') . "\n"
    .    '</td>' . "\n"
    .    '</tr>' . "\n"
    .    '<td align="right">'
    .    '<label for="type">' . get_lang('Type') . '</label> : '
    .    '</td>' . "\n"
    .    '<td>'
	.	 '<select name="type">';
	$typeList = agenda_get_type_list();
	foreach ( $typeList as $thistype )
	{
		$display.=('<option>' . get_lang($thistype['type']) .'</option>');
	}
	$display.= '</select>'
    .    '</td>' . "\n"
    .    '</tr>' . "\n"
    .    '<tr valign="top">' . "\n"
    .    '<td>&nbsp;</td>' . "\n"
    .    '<td>' . "\n"
    ;


    //---------------------
    // linker

/*    if( claro_is_jpspan_enabled() )
    {
        linker_set_local_crl( isset ($_REQUEST['id']) );
        linker_set_display();
    }
    else // popup mode
    {
        if(isset($_REQUEST['id'])) linker_set_display($_REQUEST['id']);
        else                       linker_set_display();
    }*/

    $display.= '</td></tr>' . "\n"
    .    '<tr valign="top"><td>&nbsp;</td><td>' . "\n"
    ;

/*    if( claro_is_jpspan_enabled() )
    {
        $display.= '<input type="submit" onClick="linker_confirm();"  class="claroButton" name="submitEvent" value="' . get_lang('Ok') . '" />' . "\n";
    }
    else // popup mode
   {*/
        $display.= '<input type="submit" class="claroButton" name="submitEvent" value="' . get_lang('Ok') . '" />' . "\n";
//    }

    // linker
    //---------------------
    $display.= claro_html_button($_SERVER['PHP_SELF'], 'Cancel') . "\n"
    .    '</td>' . "\n"
    .    '</tr>' . "\n"
    .    '</table>' . "\n"
    .    '</form>' . "\n"
    ;
}


    if ('rqEdit' != $cmd  && 'rqAdd' != $cmd ) // display main commands only if we're not in the event form
    {
        $display_command = TRUE;
    } // end if diplayMainCommands





	/*---------------------------------------------------------------------------
	DISLPAY USER OPTIONS
	---------------------------------------------------------------------------*/

//Add event button
$cmdList[]=  '<a class="claroCmd" href="' . $_SERVER['PHP_SELF'] . '?cmd=rqAdd">'
.            '<img src="' . get_conf('imgRepositoryWeb') . 'agenda.gif" alt="" />'
.            get_lang('Add an event')
.            '</a>';

//remove all event button
if ( count($eventList) > 0 )
{
    $cmdList[]=  '<a class= "claroCmd" href="' . $_SERVER['PHP_SELF'] . '?cmd=exDeleteAll" '
    .    ' onclick="if (confirm(\'' . clean_str_for_javascript(get_lang('Clear up event list')) . ' ? \')){return true;}else{return false;}">'
    .    '<img src="' . get_path('imgRepositoryWeb') . 'delete.gif" alt="" />'
                                   . get_lang('Clear up event list')
    .    '</a>';
}
else
{
    $cmdList[]=  '<span class="claroCmdDisabled" >'
    .    '<img src="' . get_path('imgRepositoryWeb') . 'delete.gif" alt="" />'
    .    get_lang('Clear up event list')
    .    '</span>'
    ;
}

/********************************************************************************************************************************************************************/

//Inclusion du header et du banner Claroline
require_once "../../claroline/inc/claro_init_header.inc.php";

// Code d’affichage


echo $display;
if ( !empty($dialogBox) ) echo claro_html_message_box($dialogBox); //dislay messages
if ( $display_command ) echo '<p>' . claro_html_menu_horizontal($cmdList) . '</p>';

$monthBar     = '';

if ( count($eventList) < 1 )
{
    echo "\n" . '<br /><blockquote>' . get_lang('No event in the agenda') . '</blockquote>' . "\n";
}
else
{
    if ( $orderDirection == 'DESC' )
    {
        echo '<br /><a href="' . $_SERVER['PHP_SELF'] . '?order=asc" >' . get_lang('Oldest first') . '</a>' . "\n";
    }
    else
    {
        echo '<br /><a href="' . $_SERVER['PHP_SELF'] . '?order=desc" >' . get_lang('Newest first') . '</a>' . "\n";
    }

    echo "\n" . '<table class="claroTable" width="100%">' . "\n";
}

$nowBarAlreadyShowed = FALSE;

if (claro_is_user_authenticated()) $date = $claro_notifier->get_notification_date(claro_get_current_user_id());

foreach ( $eventList as $thisEvent )
{

    if (('HIDE' == $thisEvent['visibility'] && $is_allowedToEdit) || 'SHOW' == $thisEvent['visibility'])
    {
        //modify style if the event is recently added since last login
  /*      if (claro_is_user_authenticated() && $claro_notifier->is_a_notified_ressource(claro_get_current_course_id(), $date, claro_get_current_user_id(), claro_get_current_group_id(), claro_get_current_tool_id(), $thisEvent['id']))
        {
            $cssItem = 'item hot';
        }
        else
        {
            $cssItem = 'item';
        }*/

        $cssInvisible = '';
        if ($thisEvent['visibility'] == 'HIDE')
        {
            $cssInvisible = ' invisible';
        }

        // TREAT "NOW" BAR CASE
        if ( ! $nowBarAlreadyShowed )
        if (( ( strtotime($thisEvent['startday'] . ' ' . $thisEvent['starthour'] ) > time() ) &&  'ASC' == $orderDirection )
        ||
        ( ( strtotime($thisEvent['startday'] . ' ' . $thisEvent['starthour'] ) < time() ) &&  'DESC' == $orderDirection )
        )
        {
            if ($monthBar != date('m',time()))
            {
                $monthBar = date('m',time());

                echo '<tr>' . "\n"
                .    '<th class="superHeader" colspan="2" valign="top">' . "\n"
                .    ucfirst(claro_html_localised_date('%B %Y', time()))
                .    '</th>' . "\n"
                .    '</tr>' . "\n"
                ;
            }


            // 'NOW' Bar

            echo '<tr>' . "\n"
            .    '<td>' . "\n"
            .    '<img src="' . get_path('imgRepositoryWeb') . 'pixel.gif" width="20" alt=" " />'
            .    '<span class="highlight">'
            .    '<a name="today">'
            .    '<i>'
            .    ucfirst(claro_html_localised_date( get_locale('dateFormatLong'))) . ' '
            .    ucfirst(strftime( get_locale('timeNoSecFormat')))
            .    ' -- '
            .    get_lang('Now')
            .    '</i>'
            .    '</a>'
            .    '</span>' . "\n"
            .    '</td>' . "\n"
            .    '</tr>' . "\n"
            ;

            $nowBarAlreadyShowed = true;
        }

        /*
        * Display the month bar when the current month
        * is different from the current month bar
        */

        if ( $monthBar != date( 'm', strtotime($thisEvent['startday']) ) )
        {
            $monthBar = date('m', strtotime($thisEvent['startday']));

            echo '<tr>' . "\n"
            .    '<th class="superHeader" valign="top">'
            .    ucfirst(claro_html_localised_date('%B %Y', strtotime( $thisEvent['startday']) ))
            .    '</th>' . "\n"
            .    '</tr>' . "\n"
            ;
        }

        /*
        * Display the event date
        */
		echo '<tr class="headerX" valign="top">' . "\n"
		.    '<th>' . "\n"
//		.    '<span class="'. $cssItem . $cssInvisible .'">' . "\n"
		.    '<a href="#form" name="event' . $thisEvent['id'] . '"></a>' . "\n"
		.    '<img src="' . get_path('imgRepositoryWeb') . 'agenda.gif" alt=" " />&nbsp;'
		.	 get_lang('From') . ' '
		.    ucfirst(claro_html_localised_date( get_locale('dateFormatLong'), strtotime($thisEvent['startday']))) . ' '
		.    ucfirst( strftime( get_locale('timeNoSecFormat'), strtotime($thisEvent['starthour']))) . ' ';
		if ( $thisEvent['startday'] !=$thisEvent['endday'])
		{
			echo get_lang('to') . ' '
			.    ucfirst(claro_html_localised_date( get_locale('dateFormatLong'), strtotime($thisEvent['endday']))) . ' ';
		}
		else
		{
			echo	 get_lang('Until') . ' ';
		}
		echo ucfirst( strftime( get_locale('timeNoSecFormat'), strtotime($thisEvent['endhour']))) . ' '
		.    '</span>';

        /*
        * Display the event content
        */

        echo '</th>' . "\n"
        .    '</tr>' . "\n"
        .    '<tr>' . "\n"
        .    '<td>' . "\n"
        .    '<div class="content ' . $cssInvisible . '">' . "\n"
        .    ( empty($thisEvent['title']  ) ? '' : '<p><strong>' . htmlspecialchars($thisEvent['title']) . '</strong></p>' . "\n" )
        .    ( empty($thisEvent['content']) ? '' :  claro_parse_user_text($thisEvent['content']) )
        .    '</div>' . "\n"
        ;

 //       echo linker_display_resource();
    }

	if ($is_allowedToEdit)
    {
        echo '<a href="' . $_SERVER['PHP_SELF'].'?cmd=rqEdit&amp;id=' . $thisEvent['id'] . '">'
        .    '<img src="' . get_path('imgRepositoryWeb') . 'edit.gif" border="O" alt="' . get_lang('Modify') . '">'
        .    '</a> '
        .    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exDelete&amp;id=' . $thisEvent['id'] . '" '
        .    'onclick="javascript:if(!confirm(\''
        .    clean_str_for_javascript(get_lang('Delete') . ' ' . $thisEvent['title'].' ?')
        .    '\')) {document.location=\'' . $_SERVER['PHP_SELF'] . '\'; return false}" >'
        .    '<img src="' . get_path('imgRepositoryWeb') . 'delete.gif" border="0" alt="' . get_lang('Delete') . '" />'
        .    '</a>'
        ;

        //  Visibility
        if ('SHOW' == $thisEvent['visibility'])
        {
            echo '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=mkHide&amp;id=' . $thisEvent['id'] . '">'
            .    '<img src="' . get_path('imgRepositoryWeb') . 'visible.gif" alt="' . get_lang('Invisible') . '" />'
            .    '</a>' . "\n";
        }
        else
        {
            echo '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=mkShow&amp;id=' . $thisEvent['id'] . '">'
            .    '<img src="' . get_path('imgRepositoryWeb') . 'invisible.gif" alt="' . get_lang('Visible') . '" />'
            .    '</a>' . "\n"
            ;
        }
    }
    echo '</td>'."\n"
    .    '</tr>'."\n"
    ;

}   // end while

echo '</table>';


// Inclusion du footer
require_once "../../claroline/inc/claro_init_footer.inc.php";
?>