<?php
/**
 * CLAROLINE
 *
 * - For a Student -> - View agenda Content and add events to a group
 *					  - Update/delete his events
 * - For a Prof    -> - View agenda Content
 *         - Update/delete existing entries
 *         - Add entries
 *
 * @version 1.8 $Revision$
 *
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLAG2
 *
 * @author Claro Team Marc Lavergne <marc86.lavergne@gmail.com>
 */
//Label du module
$tlabelReq = 'CLAG2';
// Initialisation du noyau claroline
require_once dirname(__FILE__) . "/../../claroline/inc/claro_init_global.inc.php";
require_once get_path('includePath') . "/lib/form.lib.php";
require_once './lib/agenda.lib.php';
require_once './lib/shared_event.lib.php';
require_once get_path('clarolineRepositorySys') . '/linker/linker.inc.php';
include_once claro_get_conf_repository().'CLAG2.conf.php';
claro_set_display_mode_available(TRUE);


/*==============================================================================
 Main Code
===============================================================================*/

if ( ! claro_is_in_a_course() || !claro_is_course_allowed() ) claro_disp_auth_form(true);

$course_id  	= claro_get_current_course_id();
$user_id    	= claro_get_current_user_id(); //find the user ID

$tbl_cdb_names 	= claro_sql_get_course_tbl();
$tbl_mdb_names 	= claro_sql_get_main_tbl();
	
$tbl_group      = $tbl_cdb_names['group_team'];
$tbl_groupUser  = $tbl_cdb_names['group_rel_team_user'];
	
$tbl_user       = $tbl_mdb_names['user'];
$tbl_courseUser = $tbl_mdb_names['rel_course_user'];

$is_allowedToEdit = claro_is_allowed_to_edit();

$display_form=FALSE;
$display_command = FALSE;
$dialogBox='';
$selectuser='';
$exEditshevt='';
$monthBar     = '';

//function that cleans the agenda DB
if (get_conf('activate_auto_delete'))agenda_delete_old_event(get_conf('auto_delete_timestamp'));

//get order by data
if( !empty($_REQUEST['order']) ) 
{
	$orderDirection = strtoupper($_REQUEST['order']);
}
else	
{
	if( !empty($_SESSION['orderDirection']) )
	{
		$orderDirection = strtoupper($_SESSION['orderDirection']);
	}
	else
	{
		$orderDirection = 'ASC';
	}
}

$acceptedValues = array('DESC','ASC');

if( ! in_array($orderDirection, $acceptedValues) )
{
    $orderDirection = 'ASC';
}

$_SESSION['orderDirection'] = $orderDirection;

//request data
if ( isset($_REQUEST['cmd']) ) $cmd =$_REQUEST['cmd'];
else 						   $cmd =NULL;

if ( isset($_REQUEST['id']) ) $id = (int) $_REQUEST['id'];
else                          $id = 0;

if ( isset($_REQUEST['title']) ) $title = trim($_REQUEST['title']);
else                             $title = '';

if ( isset($_REQUEST['description']) )  $description = trim($_REQUEST['description']);
else                                    $description = '';

if ( isset($_REQUEST['update_repeat']) )    $update_repeat = trim($_REQUEST['update_repeat']);
else                                        $update_repeat = 'this';

if ( isset($_REQUEST['repeat']) )   $repeat = trim($_REQUEST['repeat']);
else                                $repeat = 1;

if ( isset($_REQUEST['repeat_type']) )  $repeat_type = trim($_REQUEST['repeat_type']);
else                                    $repeat_type = get_lang('Each week');

if ( isset($_REQUEST['visibility_set']) )   $visibility_set = trim($_REQUEST['visibility_set']);
else                                        $visibility_set = 'SHOW';

if ( isset($_REQUEST['delete_item']) )  $delete_item = trim($_REQUEST['delete_item']);
else                                    $delete_item = 'this';

if ( $is_allowedToEdit ) //options available only to course administrators
{
    /*------------------------------------------------------------------------
    COURSE EVENT ADD
	use only for an add request. 
    --------------------------------------------------------------------------*/
	if ( 'exAdd' == $cmd )
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
			$entryId = agenda_add_item($course_id,$user_id,$title,$description, $start_date, $end_date, $repeat, $repeat_type, $visibility_set) ; //send data to the D
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


    /*----------------------------------------------------------------------------
    DELETE ALL COURSE EVENTS FROM CURRENT USER 
    ----------------------------------------------------------------------------*/

    if ( 'exDeleteAll' == $cmd )
    {
        if ( agenda_delete_all_items($course_id,$user_id))
        {
            $dialogBox .= '<p>' . get_lang('Event deleted from the agenda') . '</p>' . "\n";
        }
        else
        {
            $dialogBox = '<p>' . get_lang('Unable to delete event from the agenda') . '</p>' . "\n";
        }
    }

}//end of if allowed 


if (claro_is_user_authenticated()) //options available only to logged users
{
    /*------------------------------------------------------------------------
    DELETE EVENT COMMAND
    --------------------------------------------------------------------------*/

    if ( 'exDelete' == $cmd && !empty($id) )
    {

        if ( agenda_delete_item($id,$delete_item) )
        {
            $dialogBox .= '<p>' . get_lang('Event deleted from the agenda') . '</p>' . "\n";
        }
        else
        {
            $dialogBox = '<p>' . get_lang('Unable to delete event from the agenda') . '</p>' . "\n";
        }
    }


    /*------------------------------------------------------------------------
    EDIT EVENT COMMAND
    --------------------------------------------------------------------------*/

	if ( 'exEdit' == $cmd )
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
				if ( agenda_update_item($id,$title,$description,$start_date,$end_date,$user_id,$course_id,$update_repeat,$visibility_set))
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


	/*-------------------------------------------------------------------------
    EDIT EVENT VISIBILITY
    ---------------------------------------------------------------------------*/

    if ( 'mkShow' == $cmd  || 'mkHide' == $cmd )
    {
        if ($cmd == 'mkShow')
        {
            $visibility = 'SHOW';
        }

        if ($cmd == 'mkHide')
        {
            $visibility = 'HIDE';
        }

        if ( agenda_set_item_visibility($id, $visibility) )
        {
            $dialogBox = get_lang('Visibility modified');
        }
        else
        {
			$dialogBox =  get_lang('Unable to modify visibility');
        }
    }


    /*------------------------------------------------------------------------
    EVENT EDIT AND ADD
	function called during an add or edit request.
	if request edit event take back old information 
    --------------------------------------------------------------------------*/

	if ( 'rqEdit' == $cmd  || 'rqAdd' == $cmd || 'rqAddshevt' == $cmd )
    {
        claro_set_display_mode_available(false);

        if ( 'rqEdit' == $cmd  && !empty($id) )
        {
            $editedEvent = agenda_get_item($id) ;
            $editedEvent['start_date']  = strtotime($editedEvent['old_start_date']);
			$editedEvent['end_date']    = strtotime($editedEvent['old_end_date']);
            if ($editedEvent['user_id']!= NULL || $editedEvent['group_id']!=NULL)
			{
				$exEditshevt = 'exEditshevt';
				$nextCommand = 'exEditshevt';
			}
			else
			{
				$nextCommand = 'exEdit';
			}
			
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


            if ( 'rqAdd' == $cmd )      $nextCommand = 'exAdd';
			if ( 'rqAddshevt' == $cmd ) $nextCommand = 'exAddshevt';
        }
        $display_form =TRUE;
    }


    /*------------------------------------------------------------------------
    SHARED EVENT ADD OR EDIT 
	use only for an add request. 
    --------------------------------------------------------------------------*/
	if ( 'exAddshevt' == $cmd || 'exEditshevt'==$cmd)
    {
		if ('exEditshevt'==$cmd)//When edit event it is easier to delete the old event and create a new one with the new data
		{
			agenda_delete_item($id);
		}

		$user_idlist  = array();
		$group_idlist = array();
		if ( isset($_REQUEST['selectedbox']) )
		{

			 // Explode the values of selected in groups and users
			foreach($_REQUEST['selectedbox'] as $this_selected)
			{
				list($type, $elmtId) = explode(':', $this_selected);	
				switch($type)
				{
					case 'GROUP':
					$group_idlist[] = $elmtId;
					break;
	
					case 'USER':
					$user_idlist[] = $elmtId;
					break;
				}
			} // end while
		} // end if - $_REQUEST['selectedbox']

		//Convert to timestamp
		$start_date = mktime($_REQUEST['fhour'],$_REQUEST['fminute'],0,$_REQUEST['fmonth'],$_REQUEST['fday'],$_REQUEST['fyear']);
		$end_date   = mktime($_REQUEST['ahour'],$_REQUEST['aminute'],0,$_REQUEST['amonth'],$_REQUEST['aday'],$_REQUEST['ayear']);
	
		if ($end_date < $start_date)//date desting
		{
			$dialogBox .= '<p>' . get_lang('Invalid Dates') . '</p>' . "\n";
		}
		else
		{
			$user_idlist[]=$user_id;//add the creator of the event to the user list
			$entryId = shared_add_item($course_id,$user_id,$user_idlist,$group_idlist,$title,$description, $start_date, $end_date, $visibility_set) ; //send data to the D
		}
		if ( $entryId != false )
        {
            $dialogBox = '<p>' . get_lang('Event added to the agenda') . '</p>' . "\n";
		}
        else
        {
            $dialogBox = '<p>'. get_lang('Unable to add the event to the agenda') . '</p>' . "\n";
        }
    }

	if ('rqEdit' != $cmd  && 'rqAdd' != $cmd && 'rqAddshevt' != $cmd ) // display main commands only if we're not in the event form
	{
		$display_command = TRUE;
	} // end if diplayMainCommands


	/*---------------------------------------------------------------------------
	SELECT USER BOX
	---------------------------------------------------------------------------*/
	$htmlHeadXtra[] = "<script type=\"text/javascript\" language=\"JavaScript\">
	
	<!-- Begin javascript menu swapper
	
	function move(fbox,    tbox)
	{
		var arrFbox   = new Array();
		var arrTbox   = new Array();
		var arrLookup = new Array();
	
		var i;
		for (i = 0; i < tbox.options.length; i++)
		{
			arrLookup[tbox.options[i].text] = tbox.options[i].value;
			arrTbox[i] = tbox.options[i].text;
		}
	
		var fLength = 0;
		var tLength = arrTbox.length;
	
		for(i = 0; i < fbox.options.length; i++)
		{
			arrLookup[fbox.options[i].text] = fbox.options[i].value;
	
			if (fbox.options[i].selected && fbox.options[i].value != \"\")
			{
				arrTbox[tLength] = fbox.options[i].text;
				tLength++;
			}
			else
			{
				arrFbox[fLength] = fbox.options[i].text;
				fLength++;
			}
		}
	
		arrFbox.sort();
		arrTbox.sort();
		fbox.length = 0;
		tbox.length = 0;
	
		var c;
		for(c = 0; c < arrFbox.length; c++)
		{
			var no   = new Option();
			no.value = arrLookup[arrFbox[c]];
			no.text  = arrFbox[c];
			fbox[c]  = no;
		}
		for(c = 0; c < arrTbox.length; c++)
		{
			var no   = new Option();
			no.value = arrLookup[arrTbox[c]];
			no.text  = arrTbox[c];
			tbox[c]  = no;
		}
	}
	
	function transfer() 
	{
		var f = document.data;
		var dat;
	
		var selectedbox = f.elements['selectedbox[]'];
	
		if (selectedbox.length < 1) 
        {
			alert(\"" . clean_str_for_javascript(get_lang('You must select some users')) . "\");
			return false;
		}
		for (var i=0; i<selectedbox.length; i++)
			selectedbox[i].selected = selectedbox[i].checked = true

		f.submit();
		return false;
	}
	
	//    End    -->
	</script>";

    /*------------------------------------------------------------------------
    EVENT ADD
	function called during an add request.
	this function recovers the information used.
    --------------------------------------------------------------------------*/
	if ($cmd == 'rqAddshevt'|| $exEditshevt == 'exEditshevt')
	{
		/*----------------------------------------
		DISPLAY FORM    TO FILL 
		--------------------------------------*/
		/*
		* Get user    list of    this course
		*/
		if ( $is_allowedToEdit )
		{
			$singleUserList  = get_user_course_list($tbl_user,$tbl_courseUser,$course_id);	
			$userList        = array();	
			if ( is_array($singleUserList) && !empty($singleUserList) )
			{
				foreach ( $singleUserList as $singleUser  )
				{
					$userList[] = $singleUser;
				}
			}
		}
	
		/*
		* Get group list of this course
		*/
		$groupSelect  = get_group_course_list($tbl_groupUser,$tbl_group);
		$groupList    = array();
		if ( is_array($groupSelect) && !empty($groupSelect) )
		{
			foreach ( $groupSelect as $groupData  )
			{
				$groupList[] = $groupData;
			}
		}

		/*
		* Create Form
		*/
	    if ($cmd=='rqAddshevt') $selectuser = '<h3>'. get_lang('Add a shared event') .'</h3>';//title depending on the action
        if ($cmd=='rqEdit') $selectuser     = '<h3>'. get_lang('Edit a shared event') .'</h3>';
        
		$selectuser .= get_lang('To set a meeting, select groups of users (marked with a * in the front) or single users from the list on the left.') . "\n" ;
	
		$selectuser .= '<form method="post" action="' . $_SERVER['PHP_SELF'] . '" name="data" '
		.    'onSubmit="return transfer();">' . "\n"
		.    claro_form_relay_context()
		.	 '<center>' . "\n\n"
		.    '<table border="0" cellspacing="3" cellpadding="4">' . "\n"
		.    '<tr valign="top" align="center">'
		.    '<td>' . "\n"
		.    '<p><b>' . get_lang('User list') . '</b></p>' . "\n"
		.    '<select name="unselected[]" size="15" multiple="multiple">' . "\n"
		;
	
		if ( !empty($groupList) )
		{
			foreach( $groupList as $thisGroup )
			{
				$selectuser .= '<option value="GROUP:' . $thisGroup['id'] . '">'
				.    '* ' . $thisGroup['name'] . ' (' . $thisGroup['userNb'] . ' ' . get_lang('Users') . ')'
				.    '</option>' . "\n";
			}
		}
	
		$selectuser .= '<option value="">'
		.    '---------------------------------------------------------'
		.    '</option>' . "\n"
		;
	
		// display user list
	
		if (!empty($userList))
		{
			foreach ( $userList as $thisUser )
			{
				$selectuser .= '<option value="USER:' . $thisUser['uid'] . '">'
				.    ucwords(strtolower($thisUser['lastName'] . ' ' . $thisUser['firstName']))
				.    '</option>' . "\n"
				;
			}
		}

		if ($exEditshevt == 'exEditshevt') //recovers the previous user list
		{
			$old_group_id_list 	= get_shared_group_list($id);
			$old_group_list 	= get_selected_group_list($tbl_groupUser,$tbl_group,$old_group_id_list);
			$old_user_id_list 	= get_shared_user_list($id);
			$old_user_list 		= get_selected_user_cours_list($tbl_user,$tbl_courseUser,$course_id,$old_user_id_list);
		}
		// WATCH OUT ! form elements are called by numbers "form.element[3]"...
		// because select name contains "[]" causing a javascript
		// element name problem List of selected users
	
		$selectuser .= '</select>' . "\n"
		.    '</td>' . "\n"
		.    '<td valign="middle">' . "\n"
		.    '<input type="button" onClick="move(this.form.elements[\'unselected[]\'],this.form.elements[\'selectedbox[]\'])" value="   >>   " />' . "\n"
		.    '<p>&nbsp;</p>' . "\n"
		.    '<input type="button" onClick="move(this.form.elements[\'selectedbox[]\'],this.form.elements[\'unselected[]\'])" value="   <<   " />' . "\n"
		.    '</td>' . "\n"
		.    '<td>' . "\n"
		.    '<p>' . "\n"
		.    '<b>' . get_lang('Selected Users') . '</b></p>' . "\n"
		.    '<p>'
		.    '<select name="selectedbox[]" size="15" multiple="multiple" style="width:200">';
		if ( !empty($old_group_list) )//display the old group list
		{
			foreach($old_group_list as $this_old_group_list)
			{
				if ( !empty($this_old_group_list) )
				{
					foreach($this_old_group_list as $this_old_group )
					{
						$selectuser .= '<option value="GROUP:' . $this_old_group['id'] . '">'
						.    '* ' . $this_old_group['name'] . ' (' . $this_old_group['userNb'] . ' ' . get_lang('Users') . ')'
						.    '</option>' . "\n";
					}
				}
			}
		}
		// display user list
	
		if (!empty($old_user_list))//display the old user list
		{
			foreach ( $old_user_list as $this_old_user_list )
			{
				if (!empty($this_old_user_list))
				{	
					foreach($this_old_user_list as $this_old_user)
					{
						if($this_old_user['uid']!=$user_id) //prevents repeated author
						{
							$selectuser .= '<option value="USER:' . $this_old_user['uid'] . '">'
							.    ucwords(strtolower($this_old_user['lastName'] . ' ' . $this_old_user['firstName']))
							.    '</option>' . "\n";
						}
					}
				}
			}
		}
		$selectuser .=    '</select>'
		.    '</p>' . "\n"
		.    '</td>' . "\n"
		.    '</tr>' . "\n\n"
		.    '<tr>' . "\n"
		.    '<td colspan="3" align="center">' . "\n"
		.    '</td>' . "\n"
		.    '</tr>' . "\n\n"
		.	 '</table>' . "\n\n"
		.	 '</center>' . "\n\n"
		;
	}//end of if event add
}//end of if logged user

$user_group_list = get_user_group_list($user_id, $tbl_groupUser);//get list of group where the user is registered
$eventList = agenda_get_item_list($course_id, $user_id, $user_group_list, $orderDirection);//get event list

if (claro_is_user_authenticated()) //options available only to logged users
{
	/*---------------------------------------------------------------------------
	MAIN COMMANDS
	---------------------------------------------------------------------------*/

	if ( $is_allowedToEdit )
	{
		
		//Add event button
		$cmdList[]=  '<a class="claroCmd" href="' . $_SERVER['PHP_SELF'] . '?cmd=rqAdd">'
		.            '<img src="./img/agenda.gif" alt="" />'
		.            get_lang('Add an event')
		.            '</a>';
		
		//remove all event button
		if ( count($eventList) > 0 )
		{
			$cmdList[]=  '<a class= "claroCmd" href="' . $_SERVER['PHP_SELF'] . '?cmd=exDeleteAll" '
			.    ' onclick="if (confirm(\'' . clean_str_for_javascript(get_lang('Clear up event list')) . ' ? \')){return true;}else{return false;}">'
			.    '<img src="./img/delete.gif" alt="" />'
			. 	 get_lang('Clear up event list')
			.    '</a>';
		}
		else
		{
			$cmdList[]=  '<span class="claroCmdDisabled" >'
			.    '<img src="./img/delete.gif" alt="" />'
			.    get_lang('Clear up event list')
			.    '</span>'
			;
		}
	}
	if (get_conf('activate_shared_event')=='TRUE') //activate this function
	{
		//Add shared event button
		$cmdList[] = '<a class="claroCmd" href="' . $_SERVER['PHP_SELF'] . '?cmd=rqAddshevt">'
			.             '<img src="./img/announcement.gif" alt="" />'
		.             get_lang('Event for selected users')
		.             '</a>' . "\n"
		;
	}
}


/*==============================================================================
 Display Code
===============================================================================*/
// Update interbredcrump
$interbredcrump[] = array('url' => 'agenda.php','name' => 'Agenda2');

//Inclusion du header et du banner Claroline

require_once get_path('includePath') . '/claro_init_header.inc.php';

echo claro_html_tool_title(get_lang('Agenda'));


// Display

if ( $display_command ) echo '<p>' . claro_html_menu_horizontal($cmdList) . '</p>';

echo($selectuser); //Display the select user box


    /*------------------------------------------------------------------------
    DISPLAY INPUT BOX
    --------------------------------------------------------------------------*/
if ($display_form)
{	
	if ($cmd != 'rqAddshevt' && $exEditshevt !='exEditshevt') //prevent errors when shared event
	{
	    if ($cmd=='rqAdd') echo '<h3>'. get_lang('Add an event') .'</h3>';
        if ($cmd=='rqEdit') echo '<h3>'. get_lang('Edit an event') .'</h3>';
		echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">';
	}
    echo claro_form_relay_context()
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
	if ($cmd != 'rqEdit' && $cmd != 'rqAddshevt')
	{
		echo '<td align="right">'
		.    '<label for="repeat">' . get_lang('Occurence') . '</label> : '
		.    '</td>' . "\n"
		.    '<td>'
		.    '<input type="text" name="repeat" id="repeat" size="5" maxlength="5" value="1" />&nbsp;'
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
		.    get_lang('This is a mutliple occurence event. Do you want to update all the similar events ?')
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
	echo '<tr valign="top">' . "\n"
	.    '<td align="right">' . "\n"
	.    '<label for="visibility_set">' . "\n"
	.    get_lang('Set the visibility of the event')
    .    ' : ' . "\n"
	.    '</label>' . "\n"
	.    '</td>' . "\n"
	.    '<td>' . "\n"
	.    '<label for="visibility_set_visible">' . "\n"
	. 	 get_lang('Visible') ."\n"
	.	 '<input type="radio" name="visibility_set" id="visibility_set_visible" value="SHOW" CHECKED>' ."\n"
	.    '</label>'
	.    '<label for="visibility_set_invisible">' . "\n"
	. 	 get_lang('Invisible') ."\n"
	.	 '<input type="radio" name="visibility_set" id="visibility_set_invisible" value="HIDE">' ."\n"
	.    '</label>'
	.    '</td>' . "\n"
	.    '</tr>' . "\n";

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
}//end if display_form


if ( !empty($dialogBox) ) echo claro_html_message_box($dialogBox); //dislay messages


//Change display order
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


//Display the list of cours events
foreach ( $eventList as $thisEvent )
{
	$start_date  = explode(" ",$thisEvent['start_date']);
	$startday	 = $start_date[0];
	$starthour	 = $start_date[1];
	$end_date  	 = explode(" ",$thisEvent['end_date']);
	$endday		 = $end_date[0];
	$endhour	 = $end_date[1];

	if ($thisEvent['group_id']==NULL || ($thisEvent['group_id']!=NULL && $thisEvent['author_id']!=$user_id)) //prevents an event to be repeated
	{
	
		if (('HIDE' == $thisEvent['visibility'] && $thisEvent['author_id']==$user_id) || 'SHOW' == $thisEvent['visibility'])
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
			if (( ( strtotime($thisEvent['start_date']) > time() ) &&  'ASC' == $orderDirection )
			||
			( ( strtotime($thisEvent['start_date']) < time() ) &&  'DESC' == $orderDirection )
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
	
			if ( $monthBar != date( 'm', strtotime($startday) ) )
			{
				$monthBar = date('m', strtotime($startday));
	
				echo '<tr>' . "\n"
				.    '<th class="superHeader" valign="top">'
				.    ucfirst(claro_html_localised_date('%B %Y', strtotime( $startday) ))
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
			.    '<a href="#form" name="event' . $thisEvent['id'] . '"></a>' . "\n";

			if($thisEvent['master_event_id']!=NULL)
			{
				echo    '<img src="./img/multi.gif" alt=" " />&nbsp;';
			}
			if($thisEvent['master_event_id']==NULL && $thisEvent['user_id']==NULL && $thisEvent['group_id']==NULL)
			{
				echo    '<img src="./img/course.gif" alt=" " />&nbsp;';
			}
			if(($thisEvent['master_event_id']==NULL && $thisEvent['user_id']!=NULL) || ($thisEvent['master_event_id']==NULL && $thisEvent['group_id']!=NULL))
			{
				echo    '<img src="./img/shared.gif" alt=" " />&nbsp;';
			}

			echo get_lang('From') . ' '
			.    ucfirst(claro_html_localised_date( get_locale('dateFormatLong'), strtotime($startday))) . ' '
			.    ucfirst( strftime( get_locale('timeNoSecFormat'), strtotime($starthour))) . ' ';
			if ( $startday !=$endday)
			{
				echo get_lang('to') . ' '
				.    ucfirst(claro_html_localised_date( get_locale('dateFormatLong'), strtotime($endday))) . ' ';
			}
			else
			{
				echo	 get_lang('Until') . ' ';
			}
			echo ucfirst( strftime( get_locale('timeNoSecFormat'), strtotime($endhour))) . ' '
			.    '</span>';
	
			/*
			* Display the event description
			*/
	
			echo '</th>' . "\n"
			.    '</tr>' . "\n"
			.    '<tr>' . "\n"
			.    '<td>' . "\n"
			.    '<div class="description ' . $cssInvisible . '">' . "\n"
			.    ( empty($thisEvent['title']  ) ? '' : '<p><strong>' . htmlspecialchars($thisEvent['title']) . '</strong></p>' . "\n" )
			.    ( empty($thisEvent['description']) ? '' :  claro_parse_user_text($thisEvent['description']) )
			.    '</div>' . "\n"
			;
		}
	
		if ($thisEvent['author_id']==$user_id)
		{
			echo '<a href="' . $_SERVER['PHP_SELF'].'?cmd=rqEdit&amp;id=' . $thisEvent['id'] . '">'
			.    '<img src="./img/edit.gif" border="0" alt="' . get_lang('Modify') . '">'
			.    '</a> '
			.    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exDelete&amp;id=' . $thisEvent['id'] . '" '
			.    'onclick="javascript:if(!confirm(\''
			.    clean_str_for_javascript(get_lang('Delete') . ' ' . $thisEvent['title'].' ?')
			.    '\')) {document.location=\'' . $_SERVER['PHP_SELF'] . '\'; return false}" >'
			.    '<img src="./img/delete.gif" border="0" alt="' . get_lang('Delete') . '" />'
			.    '</a>'
			;
	
			//  Visibility
			if ('SHOW' == $thisEvent['visibility'])
			{
				echo '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=mkHide&amp;id=' . $thisEvent['id'] . '">'
				.    '<img src="./img/visible.gif" alt="' . get_lang('Invisible') . '" />'
				.    '</a>' . "\n";
			}
			else
			{
				echo '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=mkShow&amp;id=' . $thisEvent['id'] . '">'
				.    '<img src="./img/invisible.gif" alt="' . get_lang('Visible') . '" />'
				.    '</a>' . "\n";
			}
			if ($thisEvent['master_event_id'] != NULL)
			{
				echo '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exDelete&amp;id=' . $thisEvent['id'] . '&amp;delete_item=all" '
				.    'onclick="javascript:if(!confirm(\''
				.    clean_str_for_javascript(get_lang('Delete all related events') . ' ' . $thisEvent['title'].' ?')
				.    '\')) {document.location=\'' . $_SERVER['PHP_SELF'] . '\'; return false}" >'
				.    '<img src="./img/deleteall.gif" border="0" alt="' . get_lang('Delete all related events') . '" />'
				.    '</a>'
				;			
			}
		} //end of if is hide or allowed
		echo '</td>'."\n"
		.    '</tr>'."\n"
		;
	} //end of repeat control
}   // end while

echo '</table>';

// footer  
require_once get_path('includePath') . '/claro_init_footer.inc.php';
?>