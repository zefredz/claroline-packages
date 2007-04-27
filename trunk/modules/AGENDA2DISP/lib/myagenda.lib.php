<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * CLAROLINE
 *				
 *
 * @version 1.8 $Revision$
 *
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLAG2D
 *
 * @author Marc Lavergne <marc86.lavergne@gmail.com>
 */

/**
 * Get the user course list where the user is registered
 *
 * @param array $tbl_mdb_names list of main tables names
 * @author Marc Lavergne <marc86.lavergne@gmail.com>
 * @return (sysCode,officialCode,t,db,dir)
 */
function get_user_course_list($tbl_mdb_names)
{
	$tbl_course          = $tbl_mdb_names['course'];
	$tbl_rel_course_user = $tbl_mdb_names['rel_course_user'];
	$sql = "SELECT cours.code       AS sysCode,
				   cours.fake_code  AS officialCode,
				   cours.intitule   AS title,
				   cours.titulaires AS t,
				   cours.dbName     AS db,
				   cours.directory  AS dir
	
			FROM    `" . $tbl_course . "`          AS cours,
					`" . $tbl_rel_course_user . "` AS cours_user
	
			WHERE cours.code         = cours_user.code_cours
			AND   cours_user.user_id = " . (int) claro_get_current_user_id() ;
	
	return claro_sql_query_fetch_all($sql);
}
/**
 * Delete an event
 *
 * @param integer   $event_id id the requested event
 * @param string    $repeat to delete similar events
 * @author Marc Lavergne
 * @return result of deletion query
 */
function myagenda_delete_item($event_id,$repeat='this' )
{
	$final_result=true;
	$result=array();

	$tbl = get_conf('mainTblPrefix') . 'event'; //get master_event_id of tyhe event
	$sql = "SELECT master_event_id
            FROM " . $tbl . "
            WHERE id = " .(int) $event_id;
	$master_event_id = claro_sql_query_fetch_all($sql);	
	if ($master_event_id == false)$result[] = $master_event_id;

    $tbl = get_conf('mainTblPrefix') . 'event';
    $sql = "DELETE FROM  " . $tbl . "
            WHERE id = " . (int) $event_id;
	$result[] = claro_sql_query($sql);

    $tbl = get_conf('mainTblPrefix') . 'rel_event_recipient';
    $sql = "DELETE FROM  " . $tbl . "
            WHERE event_id= " . (int) $event_id;
	$result[] = claro_sql_query($sql);

	if ($repeat=='all')
	{
		foreach ($master_event_id as $this_master_event_id)
		{
			$tbl = get_conf('mainTblPrefix') . 'event';
			$sql = "SELECT id
                    FROM " . $tbl . "
                    WHERE master_event_id = " .(int) $this_master_event_id['master_event_id'];
			$event_id_list = claro_sql_query_fetch_all($sql);
			if ($event_id_list == false)$result[] = $event_id_list;
		}

		foreach ($event_id_list as $this_event_id) 
		{
			$tbl = get_conf('mainTblPrefix') . 'event';
			$sql = "DELETE FROM  " . $tbl . "
                    WHERE id = " . (int) $this_event_id['id'];
			$result[] = claro_sql_query($sql);

			$tbl = get_conf('mainTblPrefix') . 'rel_event_recipient';
			$sql = "DELETE FROM  " . $tbl . "
                    WHERE event_id= " . (int) $this_event_id['id'];
			$result[] = claro_sql_query($sql);
		}
	}
	if (is_array($result) && !empty($result))
	{
		foreach($result as $this_result)
		{
			if ($this_result==false) $final_result=false;
		}
	}
    return $final_result;
}


/**
 * Delete all user events
 *
 * @param integer $user_id id of the current user
 * @author Marc Lavergne
 * @return result of deletion query
 */
function myagenda_delete_all_items($user_id)
{
	$final_result=true;
	$result=array();

	$tbl = get_conf('mainTblPrefix') . 'event As event' . ' INNER JOIN ' 
        .   get_conf('mainTblPrefix') . 'rel_event_recipient AS rel_event_recipient' 
        .   ' ON event.id = rel_event_recipient.event_id';
	$sql = "SELECT rel_event_recipient.event_id AS event_id
			FROM " . $tbl . "
			WHERE event.author_id = " .(int) $user_id ."
			 AND  rel_event_recipient.user_id = " .(int) $user_id ."
			 AND  rel_event_recipient.group_id is NULL
			 AND  rel_event_recipient.course_id is NULL";
	$event_id_list = claro_sql_query_fetch_all($sql);
	if ($event_id_list == false)$result[] = $event_id_list;

	foreach ($event_id_list as $this_event_id)
	{	
		$tbl = get_conf('mainTblPrefix') . 'event';
		$sql = "DELETE FROM  " . $tbl . "
				WHERE id = " . (int) $this_event_id['event_id'];
		$result[] = claro_sql_query($sql);
	
		$tbl = get_conf('mainTblPrefix') . 'rel_event_recipient';
		$sql = "DELETE FROM  " . $tbl . "
				WHERE event_id= " . (int) $this_event_id['event_id'];
		$result[] = claro_sql_query($sql);
	}
	if (is_array($result) && !empty($result))
	{
		foreach($result as $this_result)
		{
			if ($this_result==false) $final_result=false;
		}
	}
    return $final_result;
}

/**
 * add an new user event
 *
 * @param string   $title       title of the new item
 * @param string   $description description of the new item
 * @param date     $star_date   start date of the event
 * @param date     $end_date    end date of the event
 * @param integer  $author_id   id of the creator of the event
 * @param integer  $repeat      number of times the event must be repeadted
 * @param integer  $visibility  set the visibility state
 * @author Marc Lavergne
 * @return id of the new item
 */

function myagenda_add_item($author_id, $title='', $description='', $start_date=NULL, $end_date=NULL, $repeat=0, $repeat_type, $visibility='SHOW')
{
	$final_result=true;
	$result=array();

	$formated_start_day = date("Y-m-d",$start_date);
	$formated_start_hour = date("H:i:s",$start_date);
	$formated_end_day   = date("Y-m-d",$end_date);
	$formated_end_hour   = date("H:i:s",$end_date);

    $tbl = get_conf('mainTblPrefix') . 'event';
    $sql = "INSERT INTO  " . $tbl . "
            SET title   	 = '" . addslashes(trim($title)) . "',
                description  = '" . addslashes(trim($description)) . "',
                start_date   = '" . $formated_start_day . ' ' . $formated_start_hour . "',
                end_date     = '" . $formated_end_day . ' ' . $formated_end_hour . "',
                author_id    = '" . $author_id . "'";
	$event_id = claro_sql_query_insert_id($sql);
	if ($event_id == false)$result[] = $event_id;

    $tbl = get_conf('mainTblPrefix') . 'rel_event_recipient';
	$sql = "INSERT INTO " . $tbl . "
            SET event_id   	= '" . (int) $event_id . "',
    			user_id   	= '" . (int) $author_id . "',
    			visibility	= '" . ($visibility=='HIDE'?'HIDE':'SHOW') . "'"; 
    $result[] = claro_sql_query_insert_id($sql);

	if ($repeat > 1)
	{
		$tbl = get_conf('mainTblPrefix') . 'event';
		$sqlSet = array();
		
        $sql = "UPDATE " . $tbl . "
                SET master_event_id = '" . (int) $event_id . "'
                WHERE `id` = " . (int) $event_id ;
		$result[] = claro_sql_query($sql);

		for($i=1; $i < $repeat; $i++)
		{
			$start_date_elements  = explode("-",$formated_start_day);
			$end_date_elements    = explode("-",$formated_end_day);

			if ($repeat_type == get_lang('Each week')) //find the new date depending on the repeat event type
			{
				$start_timestamp 	  = mktime(0,0,0,$start_date_elements[1],$start_date_elements[2]+7*$i,$start_date_elements[0]);
				$end_timestamp 		  = mktime(0,0,0,$end_date_elements[1],$end_date_elements[2]+7*$i,$end_date_elements[0]);
			}
			if ($repeat_type == get_lang('Each day')) //find the new date depending on the repeat event type
			{
				$start_timestamp 	  = mktime(0,0,0,$start_date_elements[1],$start_date_elements[2]+1*$i,$start_date_elements[0]);
				$end_timestamp 		  = mktime(0,0,0,$end_date_elements[1],$end_date_elements[2]+1*$i,$end_date_elements[0]);
			}
			if ($repeat_type == get_lang('Each month')) //find the new date depending on the repeat event type
			{
				$start_timestamp 	  = mktime(0,0,0,$start_date_elements[1]+1*$i,$start_date_elements[2],$start_date_elements[0]);
				$end_timestamp 		  = mktime(0,0,0,$end_date_elements[1]+1*$i,$end_date_elements[2],$end_date_elements[0]);
			}

			$repeat_start_date 	  = strftime('%Y-%m-%d',$start_timestamp) . ' ' .$formated_start_hour;
			$repeat_end_date	  = strftime('%Y-%m-%d',$end_timestamp) . ' ' . $formated_end_hour;
				
			$tbl = get_conf('mainTblPrefix') . 'event';
			$sql = "INSERT INTO  " . $tbl . "
    				SET title   	 = '" . addslashes(trim($title)) . "',
    					description  = '" . addslashes(trim($description)) . "',
    					author_id    = '" . (int) $author_id . "',
    					start_date   = '" . $repeat_start_date . "',
    					end_date     = '" . $repeat_end_date . "',
    					master_event_id = '" . (int) $event_id . "'";
			$repeat_event_id = claro_sql_query_insert_id($sql);
			if ($repeat_event_id == false)$result[] = $repeat_event_id;
		
			$tbl = get_conf('mainTblPrefix') . 'rel_event_recipient';
			$sql = "INSERT INTO " . $tbl . "
    				SET event_id   	= '" . (int) $repeat_event_id . "',
    					user_id		= '" . (int) $author_id . "',
    					visibility	= '" . ($visibility=='HIDE'?'HIDE':'SHOW') . "'"; 
			$result[] = claro_sql_query_insert_id($sql);
		}
	}
	if (is_array($result) && !empty($result))
	{
		foreach($result as $this_result)
		{
			if ($this_result==false) $final_result=false;
		}
	}
    return $final_result;
}


/**
 * Update a user event
 *
 * @param integer  $event_id 	id of the event
 * @param string   $title   	title of the new item
 * @param string   $description description of the new item
 * @param date     $star_date   start date of the event
 * @param date     $end_date   	end date of the event
 * @param integer  $author_id 	id of the creator of the event
 * @param string   $update_repeat update all same events
 * @param integer  $visibility	set the visibility state
 * @author Marc Lavergne
 * @return handler of query
 */

function myagenda_update_item($event_id, $title=NULL,$description=NULL, $start_date=NULL, $end_date=NULL, $author_id=NULL, $update_repeat='this')
{
	$final_result=true;
	$result=array();

	$formated_start_day  = date("Y-m-d",$start_date);
	$formated_start_hour = date("H:i:s",$start_date);
	$formated_end_day    = date("Y-m-d",$end_date);
	$formated_end_hour   = date("H:i:s",$end_date);

    $sqlSet = array();
    if(!is_null($title))      $sqlSet[] = "event.title 						= '" . addslashes(trim($title)) . "' ";
    if(!is_null($description))$sqlSet[] = "event.description 				= '" . addslashes(trim($description)) . "' ";
    if(!is_null($author_id))  $sqlSet[] = "event.author_id 					= '" . addslashes(trim($author_id)) . "' ";
	if(!is_null($author_id))  $sqlSet[] = "rel_event_recipient.user_id	    = '" . addslashes(trim($author_id)) . "' ";
	if(!is_null($start_date)) $sqlSet[] = "event.start_date                 = '" . $formated_start_day . ' ' . $formated_start_hour . "' ";
	if(!is_null($end_date))   $sqlSet[] = "event.end_date	                = '" . $formated_end_day . ' ' . $formated_end_hour . "' ";

	if ($update_repeat=='this')
	{
		if (count($sqlSet)>0)
		{
			$sqlSet[] = "event.master_event_id	= NULL ";
			
			$tbl =  get_conf('mainTblPrefix') . 'event AS event' . ' INNER JOIN ' 
				.   get_conf('mainTblPrefix') . 'rel_event_recipient AS rel_event_recipient' 
				.   ' ON event.id = rel_event_recipient.event_id';

			$sql = "UPDATE " . $tbl . "
					SET " . implode(', ',$sqlSet) ."
					WHERE event.id = " . (int) $event_id ;
			$result[] = claro_sql_query($sql);			
		}
	}
	if ($update_repeat=='from_this') //update from the selected event
	{
		$tbl = get_conf('mainTblPrefix') . 'event'; //get the master_event_id and the start_date from the selected event
		$sql = "SELECT  master_event_id,
						start_date
				FROM " . $tbl . "
				WHERE id = " .(int) $event_id;
		$original_event = claro_sql_query_fetch_all($sql);
		if ($original_event == false)$result[] = $original_event;

		foreach($original_event as $this_original_event)
		{
			$tbl = get_conf('mainTblPrefix') . 'event'; //find the id of all events after the selected event
			$sql = "SELECT  id,
							start_date,
							end_date
					FROM " . $tbl . "
					WHERE master_event_id = " .(int) $this_original_event['master_event_id'] ."
						AND start_date >= '" . $this_original_event['start_date'] . "'
					ORDER BY start_date ASC";
			$event_id_list = claro_sql_query_fetch_all($sql);

			if ($event_id_list == false)$result[] = $event_id_list;
			$nb_event = count($event_id_list);

			if ($nb_event>1) //find the repeat_type for this event
			{
				$first_comp_element = explode(" ",$event_id_list[0]['start_date']);
				$first_comp_date = explode("-",$first_comp_element[0]);
				$first_comp= mktime(0,0,0,$first_comp_date[1],$first_comp_date[2],$first_comp_date[0]);

				$second_comp_element = explode(" ",$event_id_list[1]['start_date']);
				$second_comp_date = explode("-",$second_comp_element[0]);
				$second_comp= mktime(0,0,0,$second_comp_date[1],$second_comp_date[2],$second_comp_date[0]);
				$day_numbers=($second_comp-$first_comp)/(24*60*60);

				if ($day_numbers==7) $repeat_type=get_lang('Each week');
				if ($day_numbers==1) $repeat_type=get_lang('Each day');
				if ($day_numbers==28 || $day_numbers==29 || $day_numbers==30 || $day_numbers==31) $repeat_type=get_lang('Each month');
			}	

			foreach($event_id_list as $this_event_id)
			{
				myagenda_delete_item($this_event_id['id'],'false'); //delete the events after the selected event
			}
			$result[] = myagenda_add_item($author_id, $title, $description, $start_date, $end_date, $nb_event, $repeat_type); //create the new updated events
		}
	}
	if (is_array($result) && !empty($result))
	{
		foreach($result as $this_result)
		{
			if ($this_result==false) $final_result=false;
		}
	}
    return $final_result;
}


/**
 * return data for the event  of the given id
 *
 * @param integer $event_id id the requested event
 * @author Marc Lavergne
 * @return array(`id`, `title`, `description`, `old_start_date`, `old_end_date`, `author_id`, `visibility`, `master_event_id`, `user_id`, `group_id`) of the event
 */

function myagenda_get_item($event_id)
{
	$tbl = get_conf('mainTblPrefix') . 'event AS event INNER JOIN ' 
		. get_conf('mainTblPrefix') . 'rel_event_recipient AS rel_event_recipient' 
		. ' ON event.id = rel_event_recipient.event_id';

    $sql = "SELECT 	event.id 						AS id,
					event.title 					AS title,
					event.description 				AS description,
					event.start_date 				AS old_start_date,
					event.end_date 					AS old_end_date,
					event.author_id 				AS author_id,
					rel_event_recipient.visibility 	AS visibility,
					event.master_event_id		 	AS master_event_id,
					rel_event_recipient.user_id		AS user_id,
					rel_event_recipient.group_id	AS group_id
            FROM " . $tbl . "

            WHERE event.id = " . (int) $event_id ;

    $event = claro_sql_query_get_single_row($sql);

    if ($event) return $event;
    else        return claro_failure::set_failure('EVENT_ENTRY_UNKNOW');

}

//////////////////////////////////////////////////////////////////////////////

/**
 * fetch all agenda item of a user  for a given month
 *
 * @param array $userCourseList list of this user courses
 * @param integer $user_id
 * @param integer $refMonth
 * @param integer $refDay
 * @param integer $refYear
 * @param integer $user_id
 * @return array list of items
 */
function get_myagenda_items($user_id,$userCourseList,$refMonth,$refDay,$refYear,$cmd)
{
    $items = array();
	$agendaEventList = array();

	if ($cmd=='yearview')
	{
		$start_date_filter 	= ">= '". date('Y-01-01 00:00:00',mktime(0,0,0,$refMonth,$refDay,$refYear)) ."'";
		$end_date_filter	= "<= '". date('Y-12-31 23:59:59',mktime(0,0,0,$refMonth,$refDay,$refYear+1)) ."'";
	}
	if ($cmd=='monthview')
	{
		$start_date_filter 	= ">= '". date('Y-m-01 00:00:00',mktime(0,0,0,$refMonth,$refDay,$refYear)) ."'";
		$end_date_filter	= "<= '". date('Y-m-31 23:59:59',mktime(0,0,0,$refMonth+1,$refDay,$refYear)) ."'";
	}
	if ($cmd=='weekview')
	{
		$start_date_filter 	= ">= '". date('Y-m-d 00:00:00',mktime(0,0,0,$refMonth,$refDay,$refYear)) ."'";
		$end_date_filter	= "<= '". date('Y-m-d 23:59:59',mktime(0,0,0,$refMonth,$refDay+7,$refYear)) ."'";
	}
	if ($cmd=='dayview')
	{
		$start_date_filter 	= ">= '". date('Y-m-d 00:00:00',mktime(0,0,0,$refMonth,$refDay,$refYear)) ."'";
		$end_date_filter	= "<= '". date('Y-m-d 23:59:59',mktime(0,0,0,$refMonth,$refDay+1,$refYear)) ."'";
	}
	if ($cmd=='listview')
	{
		$start_date_filter 	= 'LIKE \'____-__-__ __:__:__\'';
		$end_date_filter	= 'LIKE \'____-__-__ __:__:__\'';
	}

	if ($cmd=='yearview' || $cmd=='monthview' || $cmd=='weekview' || $cmd=='dayview' || $cmd=='listview')
	{
		// get agenda-items for every course
		foreach( $userCourseList as $thisCourse) //for each course where the user is registred
		{
			$cours_talbes_names = claro_get_course_db_name_glued($thisCourse['sysCode']);
			$tbl_cdb_names = claro_sql_get_course_tbl($cours_talbes_names);
	
			$tbl_groupUser  = $tbl_cdb_names['group_rel_team_user'];
			$sql = "SELECT team
				FROM " . $tbl_groupUser . "
				WHERE user = " .(int) $user_id;
			$user_group_list = claro_sql_query_fetch_all($sql);//get al courses groups where the user is registred
	
	
			$tbl =  get_conf('mainTblPrefix') . 'event AS event' . ' INNER JOIN ' 
				.   get_conf('mainTblPrefix') . 'rel_event_recipient AS rel_event_recipient' 
				.   ' ON event.id = rel_event_recipient.event_id';
	
			$sql = "SELECT event.id 						AS id,
						   event.title 						AS title,
						   event.description 				AS description,
						   event.start_date 				AS start_date,
						   event.end_date 					AS end_date,
						   event.author_id 					AS author_id,
						   rel_event_recipient.visibility 	AS visibility
					FROM " . $tbl . "
					WHERE ( visibility   = 'SHOW'
							AND rel_event_recipient.course_id = '" . $thisCourse['sysCode'] ."'
							AND rel_event_recipient.user_id is NULL
							AND rel_event_recipient.group_id is NULL
							AND event.start_date ". $start_date_filter ."
							AND event.end_date ". $end_date_filter .")
						  OR
						  ( visibility   = 'SHOW'
							AND rel_event_recipient.course_id = '" . $thisCourse['sysCode'] ."'
							AND rel_event_recipient.user_id = ". (int) $user_id ."
							AND event.start_date ". $start_date_filter ."
							AND event.end_date ". $end_date_filter .")";
							
							foreach($user_group_list as $this_user_group)
							{
								$sql .="OR (visibility   = 'SHOW'
										AND rel_event_recipient.course_id = '" . $thisCourse['sysCode'] ."'
										AND rel_event_recipient.user_id is NULL
										AND rel_event_recipient.group_id = ". (int) $this_user_group['team'] ."
										AND event.start_date ". $start_date_filter ."
										AND event.end_date ". $end_date_filter .")";
							}
					$sql .= "ORDER BY event.start_date ASC";
	
			$courseEventList = claro_sql_query_fetch_all($sql);//get all course events and user shared events
	
			if ( is_array($courseEventList) && !empty($courseEventList))
			{
				foreach($courseEventList as $thisEvent ) 
				{
					$eventLine = trim(strip_tags($thisEvent['title']));
		
					if ( $eventLine == '' )
					{
						$eventContent = trim(strip_tags($thisEvent['description']));
						$eventLine    = substr($eventContent, 0, 60) . (strlen($eventContent) > 60 ? ' (...)' : '');
					}
			
					$url = '<a href="./../CLAG2/agenda.php?cidReq=' . $thisCourse['sysCode'] .'">'
						.	$thisCourse['officialCode']
						.	'</a>';
					$eventStart = new claroDate($thisEvent['start_date']);
					$eventEnd 	= new claroDate($thisEvent['end_date']);
					if ($thisEvent['id']!=NULL) 
					{
						$agendaEventList[] = new claroEvent( $eventStart, $thisEvent['title'],$thisEvent['description'],$eventEnd,$thisEvent['author_id'],$url);
					}
				} // end foreach courseEventList
			}
		}
		$tbl = get_conf('mainTblPrefix') . 'event AS event' . ' INNER JOIN ' 
			. get_conf('mainTblPrefix') . 'rel_event_recipient AS rel_event_recipient' 
			. ' ON event.id = rel_event_recipient.event_id';
	
		$sql = "SELECT event.id 						AS id,
					   event.title 						AS title,
					   event.description 				AS description,
					   event.start_date 				AS start_date,
					   event.end_date 					AS end_date,
					   event.author_id 					AS author_id,
					   rel_event_recipient.visibility 	AS visibility,
					   event.master_event_id 			AS master_event_id
				FROM " . $tbl . "
				WHERE event.author_id =" . (int) $user_id ."
					AND rel_event_recipient.user_id =" . (int) $user_id ."
					AND rel_event_recipient.course_id is NULL
					AND event.start_date ". $start_date_filter ."
					AND event.end_date ". $end_date_filter ."
                ORDER BY event.start_date ASC";
		$userEventList = claro_sql_query_fetch_all($sql); //get all personal events
	
		if(!empty($userEventList))
		{
			foreach($userEventList as $thisEvent )
			{
				$eventLine = trim(strip_tags($thisEvent['title']));
		
				if ( $eventLine == '' )
				{
					$eventContent = trim(strip_tags($thisEvent['description']));
					$eventLine    = substr($eventContent, 0, 60) . (strlen($eventContent) > 60 ? ' (...)' : '');
				}
		
				$url = 	'<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exuserDelete&amp;id=' . $thisEvent['id'] . '" '
					.   'onclick="javascript:if(!confirm(\''
					.   clean_str_for_javascript(get_lang('Delete') . ' ' . $thisEvent['title'].' ?')
					.   '\')) {document.location=\'' . $_SERVER['PHP_SELF'] . '\'; return false}" >'
					.   '<img src="./img/delete.gif" border="0" alt="' . get_lang('Delete') . '" />'
					.   '</a>'
					.	'<a href="' . $_SERVER['PHP_SELF'].'?cmd=rquserEdit&amp;id=' . $thisEvent['id'] . '">'
					.	'<img src="./img/edit.gif" border="O" alt="' . get_lang('Modify') . '">'
					.   '</a> ';
				if ($thisEvent['master_event_id']!=NULL)
				{
					$url .=	 '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exuserDelete&amp;id=' . $thisEvent['id'] . '&amp;delete_item=all" '
						.    'onclick="javascript:if(!confirm(\''
						.    clean_str_for_javascript(get_lang('Delete all related events') . ' ' . $thisEvent['title'].' ?')
						.    '\')) {document.location=\'' . $_SERVER['PHP_SELF'] . '\'; return false}" >'
						.    '<img src="./img/deleteall.gif" border="0" alt="' . get_lang('Delete all related events') . '" />'
						.    '</a>'
						;	
				}
				$eventStart = new claroDate($thisEvent['start_date']);
				$eventEnd 	= new claroDate($thisEvent['end_date']);
				if ($thisEvent['id']!=NULL) 
				{
					$agendaEventList[] = new claroEvent( $eventStart, $thisEvent['title'],$thisEvent['description'],$eventEnd,$thisEvent['author_id'],$url,TRUE);
				}
			} // end foreach courseEventList
		}
	}
    return $agendaEventList;
}
?>
