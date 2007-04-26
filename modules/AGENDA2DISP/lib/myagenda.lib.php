<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
require_once get_path('clarolineRepositorySys') . '/linker/linker.inc.php';
/**
 * CLAROLINE
 *
 * - For a Student -> View angeda Content
 * - For a Prof    -> - View agenda Content
 *         - Update/delete existing entries
 *         - Add entries
 *         - generate an "announce" entries about an entries
 *
 * @version 1.8 $Revision$
 *
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLCAL
 *
 * @author Claro Team <cvs@claroline.net>
 * @author Christophe Gesché <moosh@claroline.net>
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
 * Delete an event in the given or current course
 *
 * @param integer $event_id id the requested event
 * @param string $course_id current :sysCode of the course (leaveblank for current course)
 * @author Christophe Gesché <moosh@claroline.net>
 * @return result of deletion query
 * @since  1.7
 */
function myagenda_delete_item($event_id,$repeat='this' )
{
	$tbl = get_conf('mainTblPrefix') . 'event';
	$sql = "SELECT master_event_id
		FROM " . $tbl . "
		WHERE id = " .(int) $event_id;
	$master_event_id = claro_sql_query_fetch_all($sql);	

    $tbl = get_conf('mainTblPrefix') . 'event';
    $sql = "DELETE FROM  " . $tbl . "
            WHERE id = " . (int) $event_id;
	$result = claro_sql_query($sql);

    $tbl = get_conf('mainTblPrefix') . 'rel_event_recipient';
    $sql = "DELETE FROM  " . $tbl . "
            WHERE event_id= " . (int) $event_id;
	$result = claro_sql_query($sql);

	if ($repeat=='all')
	{
		foreach ($master_event_id as $this_master_event_id)
		{
			$tbl = get_conf('mainTblPrefix') . 'event';
			$sql = "SELECT id
				FROM " . $tbl . "
				WHERE master_event_id = " .(int) $this_master_event_id['master_event_id'];
			$event_id_list = claro_sql_query_fetch_all($sql);
		}

		foreach ($event_id_list as $this_event_id) 
		{
			$tbl = get_conf('mainTblPrefix') . 'event';
			$sql = "DELETE FROM  " . $tbl . "
				WHERE id = " . (int) $this_event_id['id'];
			$result = claro_sql_query($sql);

			$tbl = get_conf('mainTblPrefix') . 'rel_event_recipient';
			$sql = "DELETE FROM  " . $tbl . "
				WHERE event_id= " . (int) $this_event_id['id'];
			$result = claro_sql_query($sql);
		}
	}
    return $result;
}


/**
 * Delete an event in the given or current course
 *
 * @param integer $event_id id the requested event
 * @param string $course_id current :sysCode of the course (leaveblank for current course)
 * @author Christophe Gesché <moosh@claroline.net>
 * @return result of deletion query
 * @since  1.7
 */
function myagenda_delete_all_items($user_id)
{
	$tbl = get_conf('mainTblPrefix') . 'event' . ' INNER JOIN ' 
		. get_conf('mainTblPrefix') . 'rel_event_recipient' 
		. ' ON ' 
		. get_conf('mainTblPrefix') . 'event.id' . ' = ' 
		. get_conf('mainTblPrefix') . 'rel_event_recipient.event_id';
	$sql = "SELECT " . get_conf('mainTblPrefix') . "rel_event_recipient.event_id AS event_id
			FROM " . $tbl . "
			WHERE " . get_conf('mainTblPrefix') . "event.author_id = " .(int) $user_id ."
			 AND  " . get_conf('mainTblPrefix') . "rel_event_recipient.user_id = " .(int) $user_id ."
			 AND  " . get_conf('mainTblPrefix') . "rel_event_recipient.group_id is NULL
			 AND  " . get_conf('mainTblPrefix') . "rel_event_recipient.course_id is NULL";
	$event_id_list = claro_sql_query_fetch_all($sql);

	foreach ($event_id_list as $this_event_id)
	{	
		$tbl = get_conf('mainTblPrefix') . 'event';
		$sql = "DELETE FROM  " . $tbl . "
				WHERE id = " . (int) $this_event_id['event_id'];
		$result = claro_sql_query($sql);
	
		$tbl = get_conf('mainTblPrefix') . 'rel_event_recipient';
		$sql = "DELETE FROM  " . $tbl . "
				WHERE event_id= " . (int) $this_event_id['event_id'];
		$result = claro_sql_query($sql);
	}
	return $result;
}

/**
 * add an new event in the given or current course
 *
 * @param string   $title   title of the new item
 * @param string   $content content of the new item
 * @param date     $time    publication dat of the item def:now
 * @param string   $course_id sysCode of the course (leaveblank for current course)
 * @author Christophe Gesché <moosh@claroline.net>
 * @return id of the new item
 * @since  1.7
 */

function myagenda_add_item($author_id, $title='', $description='', $start_date=NULL, $end_date=NULL, $repeat=0, $visibility='SHOW')
{
	$formated_start_day = date("Y-m-d",$start_date);
	$formated_start_hour = date("H:i:s",$start_date);
	$formated_end_day   = date("Y-m-d",$end_date);
	$formated_end_hour   = date("H:i:s",$end_date);

    $tbl = get_conf('mainDbName') . '.' . get_conf('mainTblPrefix') . 'event';
    $sql = "INSERT INTO  " . $tbl . "
        SET title   	 = '" . addslashes(trim($title)) . "',
            description  = '" . addslashes(trim($description)) . "',
            start_date   = '" . $formated_start_day . ' ' . $formated_start_hour . "',
            end_date     = '" . $formated_end_day . ' ' . $formated_end_hour . "',
            author_id    = '" . $author_id . "'";
	$event_id = claro_sql_query_insert_id($sql);

    $tbl = get_conf('mainDbName') . '.' . get_conf('mainTblPrefix') . 'rel_event_recipient';
	$sql = "INSERT INTO " . $tbl . "
        SET event_id   	= '" . (int) $event_id . "',
			user_id   	= '" . (int) $author_id . "',
			visibility	= '" . ($visibility=='HIDE'?'HIDE':'SHOW') . "'"; 
    $result = claro_sql_query_insert_id($sql);

	if ($repeat > 1)
	{
		$tbl = get_conf('mainDbName') . '.' . get_conf('mainTblPrefix') . 'event';
		$sqlSet = array();
		
        $sql = "UPDATE " . $tbl . "
                SET master_event_id = '" . (int) $event_id . "'
                WHERE `id` = " . (int) $event_id ;
		claro_sql_query($sql);

		for($i=1; $i < $repeat; $i++)
		{
			$start_date_elements  = explode("-",$formated_start_day);
			$start_timestamp 	  = mktime(0,0,0,$start_date_elements[1],$start_date_elements[2]+7*$i,$start_date_elements[0]);
			$repeat_start_date 	  = strftime('%Y-%m-%d',$start_timestamp) . ' ' .$formated_start_hour;
			$end_date_elements    = explode("-",$formated_end_day);
			$end_timestamp 		  = mktime(0,0,0,$end_date_elements[1],$end_date_elements[2]+7*$i,$end_date_elements[0]);
			$repeat_end_date	  = strftime('%Y-%m-%d',$end_timestamp) . ' ' . $formated_end_hour;
				
			$tbl = get_conf('mainDbName') . '.' . get_conf('mainTblPrefix') . 'event';
			$sql = "INSERT INTO  " . $tbl . "
				SET title   	 = '" . addslashes(trim($title)) . "',
					description  = '" . addslashes(trim($description)) . "',
					author_id    = '" . (int) $author_id . "',
					start_date   = '" . $repeat_start_date . "',
					end_date     = '" . $repeat_end_date . "',
					master_event_id = '" . (int) $event_id . "'";
			$repeat_event_id = claro_sql_query_insert_id($sql);
		
			$tbl = get_conf('mainDbName') . '.' . get_conf('mainTblPrefix') . 'rel_event_recipient';
			$sql = "INSERT INTO " . $tbl . "
				SET event_id   	= '" . (int) $repeat_event_id . "',
					user_id		= '" . (int) $author_id . "',
					visibility	= '" . ($visibility=='HIDE'?'HIDE':'SHOW') . "'"; 
			$result = claro_sql_query_insert_id($sql);
		}
	}
	return $result;
}


/**
 * Update an announcement in the given or current course
 *
 * @param string     $title     title of the new item
 * @param string     $content   content of the new item
 * @param date       $time      publication dat of the item def:now
 * @param string     $course_id sysCode of the course (leaveblank for current course)
 * @author Christophe Gesché <moosh@claroline.net>
 * @return handler of query
 * @since  1.7
 */

function myagenda_update_item($event_id, $title=NULL,$description=NULL, $start_date=NULL, $end_date=NULL, $author_id=NULL, $update_repeat='this')
{
	$formated_start_day  = date("Y-m-d",$start_date);
	$formated_start_hour = date("H:i:s",$start_date);
	$formated_end_day    = date("Y-m-d",$end_date);
	$formated_end_hour   = date("H:i:s",$end_date);

    $sqlSet = array();
    if(!is_null($title))      $sqlSet[] = get_conf('mainTblPrefix') . "event.title 						= '" . addslashes(trim($title)) . "' ";
    if(!is_null($description))$sqlSet[] = get_conf('mainTblPrefix') . "event.description 				= '" . addslashes(trim($description)) . "' ";
    if(!is_null($author_id))  $sqlSet[] = get_conf('mainTblPrefix') . "event.author_id 					= '" . addslashes(trim($author_id)) . "' ";
	if(!is_null($author_id))  $sqlSet[] = get_conf('mainTblPrefix') . "rel_event_recipient.user_id	= '" . addslashes(trim($author_id)) . "' ";

	if ($update_repeat=='all')
	{
		$tbl = get_conf('mainTblPrefix') . 'event';
		$sql = "SELECT  master_event_id
				FROM " . $tbl . "
				WHERE id = " .(int) $event_id;
		$master_event_id = claro_sql_query_fetch_all($sql);
		foreach($master_event_id as $this_master_event_id)
		{
			if (count($sqlSet)>0)
			{
				$tbl = get_conf('mainTblPrefix') . 'event' . ' INNER JOIN ' 
					. get_conf('mainTblPrefix') . 'rel_event_recipient' 
					. ' ON ' 
					. get_conf('mainTblPrefix') . 'event.id' . ' = ' 
					. get_conf('mainTblPrefix') . 'rel_event_recipient.event_id';

				$sql = "UPDATE " . $tbl . "
						SET " . implode(', ',$sqlSet) ."
						WHERE " . get_conf('mainTblPrefix') . "event.master_event_id = " . (int) $this_master_event_id['master_event_id'] ;
				$result = claro_sql_query($sql);
			}
		}
	}
	if ($update_repeat=='this')
	{
		if (count($sqlSet)>0)
		{
			$sqlSet[] = get_conf('mainTblPrefix') . "event.master_event_id	= NULL ";
			if(!is_null($start_date)) $sqlSet[] = get_conf('mainTblPrefix') . "event.start_date	= '" . $formated_start_day . ' ' . $formated_start_hour . "' ";
			if(!is_null($end_date))   $sqlSet[] = get_conf('mainTblPrefix') . "event.end_date	= '" . $formated_end_day . ' ' . $formated_end_hour . "' ";
			
			$tbl = get_conf('mainTblPrefix') . 'event' . ' INNER JOIN ' 
				. get_conf('mainTblPrefix') . 'rel_event_recipient' 
				. ' ON ' 
				. get_conf('mainTblPrefix') . 'event.id' . ' = ' 
				. get_conf('mainTblPrefix') . 'rel_event_recipient.event_id';

			$sql = "UPDATE " . $tbl . "
					SET " . implode(', ',$sqlSet) ."
					WHERE " . get_conf('mainTblPrefix') . "event.id = " . (int) $event_id ;
			$result = claro_sql_query($sql);			
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

		foreach($original_event as $this_original_event)
		{
			$tbl = get_conf('mainTblPrefix') . 'event'; //find the id of all events after the selected event
			$sql = "SELECT  id
					FROM " . $tbl . "
					WHERE master_event_id = " .(int) $this_original_event['master_event_id'] ."
					AND start_date >= '" . $this_original_event['start_date'] . "'";
			$event_id_list = claro_sql_query_fetch_all($sql);
			$nb_event = count($event_id_list);

			foreach($event_id_list as $this_event_id)
			{
				myagenda_delete_item($this_event_id['id'],'false'); //delete the events after the selected event
			}
			$result = myagenda_add_item($author_id, $title, $description, $start_date, $end_date, $nb_event); //create the new updated events
		}
	}
    return $result;
}


/**
 * return data for the event  of the given id of the given or current course
 *
 * @param integer $event_id id the requested event
 * @param string  $course_id sysCode of the course (leaveblank for current course)
 * @author Christophe Gesché <moosh@claroline.net>
 * @return array(`id`, `title`, `content`, `dayAncient`, `hourAncient`, `lastingAncient`) of the event
 * @since  1.7
 */

function myagenda_get_item($event_id)
{
	$tbl = get_conf('mainTblPrefix') . 'event' . ' INNER JOIN ' 
		. get_conf('mainTblPrefix') . 'rel_event_recipient' 
		. ' ON ' 
		. get_conf('mainTblPrefix') . 'event.id' . ' = ' 
		. get_conf('mainTblPrefix') . 'rel_event_recipient.event_id';

    $sql = "SELECT 	". get_conf('mainTblPrefix') . "event.id 						AS id,
					". get_conf('mainTblPrefix') . "event.title 					AS title,
					". get_conf('mainTblPrefix') . "event.description 				AS description,
					". get_conf('mainTblPrefix') . "event.start_date 				AS old_start_date,
					". get_conf('mainTblPrefix') . "event.end_date 					AS old_end_date,
					". get_conf('mainTblPrefix') . "event.author_id 				AS author_id,
					". get_conf('mainTblPrefix') . "rel_event_recipient.visibility 	AS visibility,
					". get_conf('mainTblPrefix') . "event.master_event_id		 	AS master_event_id,
					". get_conf('mainTblPrefix') . "rel_event_recipient.user_id		AS user_id,
					". get_conf('mainTblPrefix') . "rel_event_recipient.group_id	AS group_id
            FROM " . $tbl . "

            WHERE ". get_conf('mainTblPrefix') . "event.id = " . (int) $event_id ;

    $event = claro_sql_query_get_single_row($sql);

    if ($event) return $event;
    else        return claro_failure::set_failure('EVENT_ENTRY_UNKNOW');

}

//////////////////////////////////////////////////////////////////////////////

/**
 * fetch all agenda item of a user  for a given month
 *
 * @param array $userCourseList
 * @param integer $month
 * @param integer $year
 * @return array list of items
 */
function get_myagenda_items($user_id,$userCourseList)
{
    $items = array();
	$agendaEventList = array();

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


		$tbl = get_conf('mainTblPrefix') . 'event' . ' INNER JOIN ' 
			. get_conf('mainTblPrefix') . 'rel_event_recipient' 
			. ' ON ' 
			. get_conf('mainTblPrefix') . 'event.id' . ' = ' 
			. get_conf('mainTblPrefix') . 'rel_event_recipient.event_id';

		$sql = "SELECT ". get_conf('mainTblPrefix') . "event.id 						AS id,
					   ". get_conf('mainTblPrefix') . "event.title 						AS title,
					   ". get_conf('mainTblPrefix') . "event.description 				AS description,
					   ". get_conf('mainTblPrefix') . "event.start_date 				AS start_date,
					   ". get_conf('mainTblPrefix') . "event.end_date 					AS end_date,
					   ". get_conf('mainTblPrefix') . "event.author_id 					AS author_id,
					   ". get_conf('mainTblPrefix') . "rel_event_recipient.visibility 	AS visibility
                FROM " . $tbl . "
                WHERE ( visibility   = 'SHOW'
						AND ". get_conf('mainTblPrefix') . "rel_event_recipient.course_id = '" . $thisCourse['sysCode'] ."'
						AND ". get_conf('mainTblPrefix') . "rel_event_recipient.user_id is NULL
						AND ". get_conf('mainTblPrefix') . "rel_event_recipient.group_id is NULL)
					  OR
					  ( visibility   = 'SHOW'
						AND ". get_conf('mainTblPrefix') . "rel_event_recipient.course_id = '" . $thisCourse['sysCode'] ."'
						AND ". get_conf('mainTblPrefix') . "rel_event_recipient.user_id = ". (int) $user_id ."
						AND ". get_conf('mainTblPrefix') . "rel_event_recipient.group_id is NULL)";
						foreach($user_group_list as $this_user_group)
						{
							$sql .="OR (visibility   = 'SHOW'
									AND ". get_conf('mainTblPrefix') . "rel_event_recipient.course_id = '" . $thisCourse['sysCode'] ."'
									AND ". get_conf('mainTblPrefix') . "rel_event_recipient.user_id is NULL
									AND ". get_conf('mainTblPrefix') . "rel_event_recipient.group_id = ". (int) $this_user_group['team'] .")";
						}
				$sql .= "ORDER BY start_date ASC";

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
	$tbl = get_conf('mainTblPrefix') . 'event' . ' INNER JOIN ' 
		. get_conf('mainTblPrefix') . 'rel_event_recipient' 
		. ' ON ' 
		. get_conf('mainTblPrefix') . 'event.id' . ' = ' 
		. get_conf('mainTblPrefix') . 'rel_event_recipient.event_id';

	$sql = "SELECT ". get_conf('mainTblPrefix') . "event.id 						AS id,
				   ". get_conf('mainTblPrefix') . "event.title 						AS title,
				   ". get_conf('mainTblPrefix') . "event.description 				AS description,
				   ". get_conf('mainTblPrefix') . "event.start_date 				AS start_date,
				   ". get_conf('mainTblPrefix') . "event.end_date 					AS end_date,
				   ". get_conf('mainTblPrefix') . "event.author_id 					AS author_id,
				   ". get_conf('mainTblPrefix') . "rel_event_recipient.visibility 	AS visibility,
				   ". get_conf('mainTblPrefix') . "event.master_event_id 			AS master_event_id
			FROM " . $tbl . "
			WHERE ". get_conf('mainTblPrefix') . "event.author_id =" . (int) $user_id ."
				AND ". get_conf('mainTblPrefix') . "rel_event_recipient.user_id =" . (int) $user_id ."
				AND ". get_conf('mainTblPrefix') . "rel_event_recipient.course_id is NULL";
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
				$url .=	'<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exuserDelete&amp;id=' . $thisEvent['id'] . '&amp;delete_item=all" '
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
    return $agendaEventList;
}
?>