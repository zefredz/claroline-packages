<?php
/**
 * CLAROLINE
 *
 * - For a Student ->  - View agenda Content and personal events
 *                      - Update/delete his events personal events
 * - For a Prof    -> - View agenda Content and personal events
 *                    - Update/delete his events personal events
 *
 * @version 1.8 $Revision$
 *
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLAG2D
 *
 * @author Marc Lavergne <marc86.lavergne@gmail.com> Michel Carbone <michel_c12@yahoo.fr>
 */
//Module label
$tlabelReq = 'CLAG2';

// Initialisation du noyau claroline

require_once dirname(__FILE__) . "/../../claroline/inc/claro_init_global.inc.php";

require_once './lib/myagenda.lib.php';
require_once get_path('includePath') . '/lib/form.lib.php';
require_once './lib/clarocalendar.lib.php';
require_once './lib/claroevent.lib.php';
require_once './lib/clarodate.lib.php';
require_once get_path('clarolineRepositorySys') . '/linker/linker.inc.php';
include_once claro_get_conf_repository().'CLAG2D.conf.php';

/*==============================================================================
 Main Code
===============================================================================*/

$user_id   	= claro_get_current_user_id(); //find the user ID
$nameTools 	= get_lang('My calendar');
$eventList 	= Array();
$display_form = FALSE;
$dialogBox 	= '';
$currentDate = mktime();

$tbl_mdb_names  = claro_sql_get_main_tbl();
$userCourseList = get_user_course_list($tbl_mdb_names );


if ( isset($_REQUEST['refMonth'] ) )$refMonth = $_REQUEST['refMonth'];
else 								$refMonth = clarodate::getMonthFromTimeStamp($currentDate);

if (isset($_REQUEST['refYear'])) $refYear=$_REQUEST['refYear'];
else 							 $refYear = clarodate::getYearFromTimeStamp($currentDate);

if (isset($_REQUEST['refDay']) )$refDay = $_REQUEST['refDay'];
else 							$refDay = clarodate::getDayFromTimeStamp($currentDate);

$referenceDate = mktime(0, 0, 0, $refMonth, $refDay, $refYear);

if ( isset($_REQUEST['cmd']) ) $cmd =$_REQUEST['cmd'];
else 						   $cmd ='monthview';

if ( isset($_REQUEST['id']) ) $id = (int) $_REQUEST['id'];
else                          $id = 0;

if ( isset($_REQUEST['title']) ) $title = trim($_REQUEST['title']);
else                             $title = '';

if ( isset($_REQUEST['description']) ) $description = trim($_REQUEST['description']);
else                               $description = '';

if ( isset($_REQUEST['update_repeat']) ) $update_repeat = trim($_REQUEST['update_repeat']);
else                               $update_repeat = 'this';

if ( isset($_REQUEST['repeat']) ) $repeat = trim($_REQUEST['repeat']);
else                             $repeat = 1;

if ( isset($_REQUEST['repeat_type']) ) $repeat_type = trim($_REQUEST['repeat_type']);
else                             $repeat_type = get_lang('Each week');

if ( isset($_REQUEST['delete_item']) ) $delete_item = trim($_REQUEST['delete_item']);
else                             $delete_item = 'this';

if (claro_is_user_authenticated())
{
	/*----------------------------------------------------------------------------
	DELETE ALL EVENTS COMMAND
	----------------------------------------------------------------------------*/
	
	if ( 'rquserDellAll' == $cmd )
	{
		if ( myagenda_delete_all_items($user_id))
		{
			$dialogBox .= '<p>' . get_lang('Event deleted from the agenda') . '</p>' . "\n";
		}
		else
		{
			$dialogBox = '<p>' . get_lang('Unable to delete event from the agenda') . '</p>' . "\n";
		}
	} 

    /*------------------------------------------------------------------------
    DELETE EVENT COMMAND
    --------------------------------------------------------------------------*/

    if ( 'exuserDelete' == $cmd && !empty($id) )
    {

        if ( myagenda_delete_item($id,$delete_item) )
        {
            $dialogBox .= '<p>' . get_lang('Event deleted from the agenda') . '</p>' . "\n";
        }
        else
        {
            $dialogBox = '<p>' . get_lang('Unable to delete event from the agenda') . '</p>' . "\n";
        }
    }
	
	
	/*------------------------------------------------------------------------
	EVENT EDIT AND ADD
	function called durring an add or edit request.
	if request edit event take back old information 
	--------------------------------------------------------------------------*/
	
	if ( 'rquserEdit' == $cmd  || 'rquserAdd' == $cmd  )
	{
		claro_set_display_mode_available(false);

		if ( 'rquserEdit' == $cmd  && !empty($id) )
		{
			$editedEvent = myagenda_get_item($id) ;
			$editedEvent['start_date'] = strtotime($editedEvent['old_start_date']);
			$editedEvent['end_date'	 ] = strtotime($editedEvent['old_end_date']);
			$nextCommand = 'exuserEdit';
			
		}
		else
		{
			$editedEvent['user_id'        ] = '';
			$editedEvent['title'          ] = '';
			$editedEvent['description'    ] = '';
			$editedEvent['start_date'	  ] = time();
			$editedEvent['end_date'	      ] = time();
			$editedEvent['id'             ] = '';
			$editedEvent['master_event_id'] = '';
			$editedEvent['visibity'		  ] = 'SHOW';


			$nextCommand = 'exuserAdd';
		}
		$display_form =TRUE;
	} // end if cmd == 'rqEdit' && cmd == 'rqAdd'
	
	/*------------------------------------------------------------------------
	EVENT ADD
	use only for an add request. 
	--------------------------------------------------------------------------*/
	if ( 'exuserAdd' == $cmd )
	{
		//convert to timestamp
		$start_date = mktime($_REQUEST['fhour'],$_REQUEST['fminute'],0,$_REQUEST['fmonth'],$_REQUEST['fday'],$_REQUEST['fyear']);
		$end_date   = mktime($_REQUEST['ahour'],$_REQUEST['aminute'],0,$_REQUEST['amonth'],$_REQUEST['aday'],$_REQUEST['ayear']);
		
		if ($end_date < $start_date)//date desting
		{
			$dialogBox .= '<p>' . get_lang('Invalid Dates') . '</p>' . "\n";
		}
		else
		{
			$entryId = myagenda_add_item($user_id,$title,$description, $start_date, $end_date, $repeat, $repeat_type) ; //send data to the D
		}
		if ( $entryId != false )
		{
			$dialogBox .= '<p>' . get_lang('Event added to the agenda') . '</p>' . "\n";
		}
		else
		{
			$dialogBox .= '<p>' . get_lang('Unable to add the event to the agenda') . '</p>' . "\n";
		}
	}
	
	/*------------------------------------------------------------------------
	EDIT EVENT COMMAND
	use only for an edit request. find data and send to DB 
	--------------------------------------------------------------------------*/
	
	
	if ( 'exuserEdit' == $cmd )
	{
	    //convert to timestamp
		$start_date = mktime($_REQUEST['fhour'],$_REQUEST['fminute'],0,$_REQUEST['fmonth'],$_REQUEST['fday'],$_REQUEST['fyear']);
		$end_date   = mktime($_REQUEST['ahour'],$_REQUEST['aminute'],0,$_REQUEST['amonth'],$_REQUEST['aday'],$_REQUEST['ayear']);
	
		if ($end_date < $start_date) //date desting
		{
			$dialogBox .= '<p>' . get_lang('Invalid Dates') . '</p>' . "\n";
		}
		else
		{
			if ( !empty($id) )
			{	
				if ( myagenda_update_item($id,$title,$description,$start_date,$end_date,$user_id,$update_repeat))
				{
					$dialogBox .= '<p>' . get_lang('Event updated into the agenda') . '</p>' . "\n";
				}
				else
				{
					$dialogBox .= '<p>' . get_lang('Unable to update the event into the agenda') . '</p>' . "\n";
				}
			}
		}
	}
	
	
	/*---------------------------------------------------------------------------
	MAIN COMMANDS
	---------------------------------------------------------------------------*/
	
	//Add event button
	$cmdList[]=  '<a class="claroCmd" href="' . $_SERVER['PHP_SELF'] . '?cmd=rquserAdd">'
	.            '<img src="' . get_conf('imgRepositoryWeb') . 'agenda.gif" alt="" />'
	.            get_lang('Add an event')
	.            '</a>';
	
	//remove all event button
	$cmdList[]=  '<a class= "claroCmd" href="' . $_SERVER['PHP_SELF'] . '?cmd=rquserDellAll" '
	.    ' onclick="if (confirm(\'' . clean_str_for_javascript(get_lang('Delete all personal events')) . ' ? \')){return true;}else{return false;}">'
	.    '<img src="' . get_path('imgRepositoryWeb') . 'delete.gif" alt="" />'
	. 	 get_lang('Delete all personal events')
	.    '</a>';
	
	
	/*----------------------------------------------------------------------------
	GET ALL THE CALENDAR DATA
	----------------------------------------------------------------------------*/
	
	$eventList = get_myagenda_items($user_id,$userCourseList,$refMonth,$refDay,$refYear,$cmd);
}// end of if is_user_authenticated



/*==============================================================================
 Display Code
===============================================================================*/
//Inclusion du header et du banner Claroline
require_once get_path('includePath') . "/claro_init_header.inc.php";

// Display
echo claro_html_tool_title($nameTools);


	/*----------------------------------------------------------------------------
    DISPLAY TYPE OF CALENDAR
	----------------------------------------------------------------------------*/

echo '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=yearview" >' . get_lang('Year view') . ' | </a>';
echo '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=monthview" >' . get_lang('Month view') . ' | </a>';
echo '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=weekview" >' . get_lang('Week view') . ' | </a>';
echo '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=dayview" >' . get_lang('Day view') . ' | </a>';
echo '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=listview" >' . get_lang('List view') . '</a>';

//display year view of the agenda

if ( $cmd == 'yearview' )
{   
	YearView::yearViewDisplay($referenceDate, $eventList);
}

/// display month view of the agenda

if ( $cmd == 'monthview' )
{
	monthView::monthViewDisplay($referenceDate, $eventList,'LONG', 'monthView',$refMonth, $refYear);		
}


/// display week view of the agenda
if ( $cmd=='weekview')
{
	weekView::weekViewDisplay($referenceDate, $eventList);
}


/// display day view of the agenda
if ( $cmd=='dayview')
{
	dayView::dayViewDisplay($referenceDate, $eventList);
}


/// display list of the events
if ( $cmd=='listview')
{
	listview::listViewDisplay($referenceDate, $eventList);
}

    /*------------------------------------------------------------------------
    DISPLAY DIALOGUE BOX 
    --------------------------------------------------------------------------*/

if ($display_form)
{	
    if ($cmd=='rquserAdd') echo '<h3>'. get_lang('Add an event') .'</h3>';
    if ($cmd=='rquserEdit') echo '<h3>'. get_lang('Edit an event') .'</h3>';
	echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">'
    .	 claro_form_relay_context()
    .    '<input type="hidden" name="cmd" value="' . $nextCommand . '" />'
    .    '<input type="hidden" name="id"  value="' . $editedEvent['id'] . '" />'
    .	 '<input type="hidden" name="claroFormId" value="'.uniqid('').'">' . "\n"
    .    '<table>' . "\n"
    .    '<tr valign="top">' . "\n"
    .    '<td align="right">' . get_lang('Start date') . ' : '
    .    '</td>' . "\n"
    .    '<td>'
    .    claro_html_date_form('fday', 'fmonth', 'fyear', $editedEvent['start_date'], 'long' ) . ' '
    .    claro_html_time_form('fhour','fminute', $editedEvent['start_date']) . '&nbsp;'
    .    '<small>' . get_lang('(d/m/y hh:mm)') . '</small>'
    .    '</td>' . "\n"
    .    '</tr>' . "\n"
    .    '<td align="right">' . get_lang('End date') . ' : '
    .    '</td>' . "\n"
    .    '<td>'
    .    claro_html_date_form('aday', 'amonth', 'ayear', $editedEvent['end_date'], 'long' ) . ' '
    .    claro_html_time_form('ahour','aminute', $editedEvent['end_date']) . '&nbsp;'
    .    '<small>' . get_lang('(d/m/y hh:mm)') . '</small>'
    .    '</td>' . "\n"
    .    '</tr>' . "\n";
	if ($cmd != 'rquserEdit')
	{
		echo '<td align="right">'
		.    '<label for="repeat">' . get_lang('Occurence') . '</label> : '
		.    '</td>' . "\n"
		.    '<td>'
		.    '<input type="text" name="repeat" id="repeat" size="20" maxlength="20" value="1" />'
		.    '&nbsp;'
		.	 '<select name="repeat_type">'
		.	 '<option>'. get_lang('Each day') .'</option>'
		.	 '<option SELECTED>'. get_lang('Each week') .'</option>'
		.	 '<option>'. get_lang('Each month') .'</option>'
		.	 '</select>'
		.    '</td>' . "\n"
		.    '</tr>' . "\n";
	}
    echo '<tr valign="top">' . "\n"
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
    .    '<label for="description">' . "\n"
    .    get_lang('Detail')
    .    ' : ' . "\n"
    .    '</label>' . "\n"
    .    '</td>' . "\n"
    .    '<td width="80%">' . "\n"
    .    claro_html_textarea_editor('description', $editedEvent['description'], 12, 67, $optAttrib = ' wrap="virtual" ') . "\n"
    .    '</td>' . "\n"
    .    '</tr>' . "\n"
    .    '<tr valign="top">' . "\n"
    .    '<td>&nbsp;</td>' . "\n"
    .    '<td>' . "\n"
    ;
	if ($editedEvent['master_event_id']!=NULL)
	{
		echo '<tr valign="top">' . "\n"
		.    '<td align="right">' . "\n"
		.    '<label for="update_repeat">' . "\n"
		.    get_lang('This is a mutliple occurence event. What do you want to update ?')
		.    ' : ' . "\n"
		.    '</label>' . "\n"
		.    '</td>' . "\n"
		.    '<td>' . "\n"
        .    '<label for="update_repeat_this">' . "\n"
		. 	 get_lang('This') ."\n"
		.	 '<input type="radio" name="update_repeat" id="update_repeat_this" value="this">' ."\n"
	    .    '</label>' . "\n"
	    .    '<label for="update_repeat_from_this">' . "\n"
		. 	 get_lang('from this') ."\n"
		.	 '<input type="radio" name="update_repeat" id="update_repeat_from_this" value="from_this" CHECKED>' ."\n"
	    .    '</label>' . "\n"
		.    '</td>' . "\n"
		.    '</tr>' . "\n";
	}
    echo '</td></tr>' . "\n"
    .    '<tr valign="top"><td>&nbsp;</td><td>' . "\n"
    ;

    if( claro_is_jpspan_enabled() )
    {
        echo '<input type="submit" onClick="linker_confirm();"  class="claroButton" name="submitEvent" value="' . get_lang('Ok') . '" />' . "\n";
    }
    else // popup mode
    {
        echo '<input type="submit" class="claroButton" name="submitEvent" value="' . get_lang('Ok') . '" />' . "\n";
    }
    echo claro_html_button($_SERVER['PHP_SELF'], 'Cancel') . "\n"
    .    '</td>' . "\n"
    .    '</tr>' . "\n"
    .    '</table>' . "\n"
    .    '</form>' . "\n"
    ;
}

if ( !empty($dialogBox) ) echo claro_html_message_box($dialogBox); //dislay messages


//CALENDAR OPTION
if (get_conf('activate_personal')==TRUE && $cmd!='rquserEdit' && $cmd!='rquserAdd' )
{
	echo '<p>' . claro_html_menu_horizontal($cmdList) . '</p>';
}

// Inclusion du footer

require_once get_path('includePath') . "/claro_init_footer.inc.php";
?>
