<?php
//Label du module
$tlabelReq = 'CLAG2';

// Initialisation du noyau claroline

require_once dirname(__FILE__) . "/../../claroline/inc/claro_init_global.inc.php";

require_once './lib/myagenda.lib.php';
require_once get_path('includePath') . '/lib/form.lib.php';
require_once './lib/clarocalendar.lib.php';
require_once './lib/claroevent.lib.php';
require_once './lib/clarodate.lib.php';
include_once claro_get_conf_repository().'CLAG2D.conf.php';

// Code métier
$user_id   	= claro_get_current_user_id(); //find the user ID
$nameTools 	= get_lang('My calendar');
$eventList 	= Array();
$today 		= date("Y-m-d"); 
$display_form = FALSE;
$dialogBox 	= '';

/////
$currentDate = mktime();

$tbl_mdb_names  = claro_sql_get_main_tbl();
$userCourseList = get_user_course_list($tbl_mdb_names );

//////
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
		//date desting
		$start_date = mktime($_REQUEST['fhour'],$_REQUEST['fminute'],0,$_REQUEST['fmonth'],$_REQUEST['fday'],$_REQUEST['fyear']);
		$end_date   = mktime($_REQUEST['ahour'],$_REQUEST['aminute'],0,$_REQUEST['amonth'],$_REQUEST['aday'],$_REQUEST['ayear']);
		if ($end_date < $start_date)
		{
			$dialogBox .= '<p>' . get_lang('Invalid Dates') . '</p>' . "\n";
		}
		else
		{
			$entryId = myagenda_add_item($user_id,$title,$description, $start_date, $end_date, $repeat) ; //send data to the D
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
		$start_date = mktime($_REQUEST['fhour'],$_REQUEST['fminute'],0,$_REQUEST['fmonth'],$_REQUEST['fday'],$_REQUEST['fyear']);
		$end_date   = mktime($_REQUEST['ahour'],$_REQUEST['aminute'],0,$_REQUEST['amonth'],$_REQUEST['aday'],$_REQUEST['ayear']);
	
		if ($end_date < $start_date)
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
	
	$eventList = get_myagenda_items($user_id,$userCourseList);
}



/********************************************************************************************************************************************/
//Inclusion du header et du banner Claroline
require_once get_path('includePath') . "/claro_init_header.inc.php";

// Code d’affichage
echo claro_html_tool_title($nameTools);


	/*----------------------------------------------------------------------------
    DISPLAY TYPE OF CALENDAR
	----------------------------------------------------------------------------*/

echo '<br /><a href="' . $_SERVER['PHP_SELF'] . '?cmd=monthview" >' . get_lang('Month view') . ' | </a>';
echo '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=yearview" >' . get_lang('Year view') . ' | </a>';
echo '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=weekview" >' . get_lang('Week view') . ' | </a>';
echo '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=dayview" >' . get_lang('Day view') . ' | </a>';
echo '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=listview" >' . get_lang('List view') . '</a>';
if (!empty($eventList))
{
	//display year view of the agenda
	
	if ( $cmd == 'yearview' )
		{   
		   // $offset = $refYear-$offset;
		   // $refDate2 = time();
			//$calendar->paint( new YearView($referenceDate, $eventList, $offset) );
			YearView::yearViewDisplay($referenceDate, $eventList);
		}
		//else 
			//{
				//if($cmd == 'yearview')
				//$calendar->paint( new YearView() );
			//}
	
	/// display month view of the agenda
	
	if ( $cmd == 'monthview' )
	{
	   //** $refYear=clarodate::getYearFromTimeStamp($currentDate);
	   //$offset='1';
		//$referenceDate = $currentDate;
	  // if( !isset($refMonth) && !isset($refYear) && !isset($referenceDate) )
		//    {
				//$referenceDate = mktime($refMonth,$refYear,-1);
		  //  }
	   // else //$referenceDate = mktime();
		
			//if($offset!=0){
			//$defaultMonthView=$defaultMonthView+$offset;
			//var_dump($referenceDate);
		   //$referenceDate= clarodate::setMonthOffset($currentDate,$offset);  
			//echo'hello  ';
		  //  var_dump($referenceDate);
			$offset = null;
			if(isset($_REQUEST['refYear']) )
			$refYear = $_REQUEST['refYear'];
				else $refYear=null;
			if(isset($_REQUEST['refMonth']) )
			$refMonth = $_REQUEST['refMonth'];
				else $refMonth=null;
				
			monthView::monthViewDisplay($referenceDate, $eventList,'LONG', 'monthView',$refMonth, $refYear);
		//}
	
	   // $calendar->paint( new MonthView($referenceDate,$eventList) );
	   // $calendar->paint( new MonthView($referenceDate,$eventList, 'LONG', 1) );
	
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
}
else
{
	$dialogBox .= get_lang('No event in the agenda');
}
    /*------------------------------------------------------------------------
    DISPLAY DIALOGUE BOX 
    --------------------------------------------------------------------------*/

if ($display_form)
{	
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
		.    '<label for="type">' . get_lang('Occurence') . '</label> : '
		.    '</td>' . "\n"
		.    '<td>'
		.    '<input type="text" name="repeat" id="repeat" size="20" maxlength="20" value="1" />'
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
		.    get_lang('This is a mutliple occurence event. Do you want to update all the similar events ?')
		.    ' : ' . "\n"
		.    '</label>' . "\n"
		.    '</td>' . "\n"
		.    '<td>' . "\n"
		. 	 get_lang('All') ."\n"
		.	 '<input type="radio" name="update_repeat" value="all" >' ."\n"
		. 	 get_lang('This') ."\n"
		.	 '<input type="radio" name="update_repeat" value="this">' ."\n"
		. 	 get_lang('from this') ."\n"
		.	 '<input type="radio" name="update_repeat" value="from_this" CHECKED>' ."\n"
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
if (get_conf('activate_personal')==TRUE)
{
	echo '<p>' . claro_html_menu_horizontal($cmdList) . '</p>';
}

// Inclusion du footer

require_once get_path('includePath') . "/claro_init_footer.inc.php";
?>