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
 * @package CLAG2
 *
 * @author Marc Lavergne <marc86.lavergne@gmail.com>
 */
 
/**
 * Delete all items older than a specific number of days
 * @param int $timestamp number of days
 * @author Marc Lavergne
 */
function agenda_delete_old_event($timestamp)
{
	$current_date=date("Y-m-d");
	$current_date_elements 	= explode("-",$current_date);
	$delete_date 	  		= mktime(0,0,0,$current_date_elements[1],$current_date_elements[2]-$timestamp,$current_date_elements[0]);
	$formated_delete_date 	= strftime('%Y-%m-%d 00:00:00',$delete_date);

	$tbl = get_conf('mainTblPrefix') . 'event';
	$sql = "SELECT id
		FROM " . $tbl . "
		WHERE end_date < '" . $formated_delete_date . "'";
	$event_id_list = claro_sql_query_fetch_all($sql);
	if(is_array($event_id_list) && !empty($event_id_list))
	{
		foreach ($event_id_list as $this_event_id)
		{	
			$tbl = get_conf('mainTblPrefix') . 'event';
			$sql = "DELETE FROM  " . $tbl . "
					WHERE id = " . (int) $this_event_id['id'];
			claro_sql_query($sql);
		
			$tbl = get_conf('mainTblPrefix') . 'rel_event_recipient';
			$sql = "DELETE FROM  " . $tbl . "
					WHERE event_id= " . (int) $this_event_id['id'];
			claro_sql_query($sql);
		}
	}
}

/**
 * get list of all agenda item (course and directed items) in the current course
 *  
 * @param string    $order  'ASC' || 'DESC' : ordering of the list.
 * @param string    $course_id current      : sysCode of the course
 * @param integer   $user_id                : User id
 * @param array     $user_group_list list of groups where the user is registered
 *  
 * @author Marc Lavergne
 * @return array of array(`id`, `title`, `description`, `start_date`, `end_date`, `author_id`, `master_event_id`,`user_id`,`group_id`,`visibility`)
 */

function agenda_get_item_list($course_id, $user_id, $user_group_list, $order='DESC')

{
	$tbl = get_conf('mainTblPrefix') . 'event AS event INNER JOIN ' 
		. get_conf('mainTblPrefix') . 'rel_event_recipient AS rel_event_recipient' 
		. ' ON  event.id = rel_event_recipient.event_id';

	$sql = "SELECT event.id 						AS id,
				   event.title 						AS title,
				   event.description 				AS description,
				   event.start_date 				AS start_date,
				   event.end_date 					AS end_date,
				   event.author_id 					AS author_id,
				   event.master_event_id			AS master_event_id,
				   rel_event_recipient.user_id	 	AS user_id,
				   rel_event_recipient.group_id 	AS group_id,
				   rel_event_recipient.visibility 	AS visibility
		FROM " . $tbl . "
		WHERE 	(rel_event_recipient.course_id = '" . $course_id ."'
				AND rel_event_recipient.user_id = " . (int) $user_id .")
			OR
				(rel_event_recipient.course_id = '" . $course_id ."'
				AND rel_event_recipient.user_id is NULL
				AND rel_event_recipient.group_id is NULL)";
		foreach($user_group_list as $this_group)
		{
			$sql .=" OR
			(rel_event_recipient.course_id = '" . $course_id ."'
			AND rel_event_recipient.user_id is NULL
			AND rel_event_recipient.group_id = ".  (int) $this_group .")";
		}


		$sql .="ORDER BY event.start_date " . ('DESC' == $order?'DESC':'ASC');

	return claro_sql_query_fetch_all($sql);
}

/**
 * Delete an event in the current course
 *
 * @param integer   $event_id   : id the requested event
 * @param string    $repeat     : to delete similar events
 *  
 * @author Marc Lavergne
 * @return result of deletion query
 */
function agenda_delete_item($event_id,$repeat='this')
{
	$final_result=true;
	$result=array();

	$tbl = get_conf('mainTblPrefix') . 'event';
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
			$result [] = claro_sql_query($sql);

			$tbl = get_conf('mainTblPrefix') . 'rel_event_recipient';
			$sql = "DELETE FROM  " . $tbl . "
				WHERE event_id= " . (int) $this_event_id['id'];
			$result []= claro_sql_query($sql);
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
 * Delete all events in the current course
 *
 * @param string $course_id current :sysCode of the course (leaveblank for current course)
 * @author Marc Lavergne
 * @return result of deletion query
 */
function agenda_delete_all_items($course_id=NULL, $user_id)
{
	$final_result=true;
	$result=array();

	$tbl = get_conf('mainTblPrefix') . 'event AS event INNER JOIN ' 
		. get_conf('mainTblPrefix') . 'rel_event_recipient AS rel_event_recipient' 
		. ' ON event.id = rel_event_recipient.event_id';
		
	$sql = "SELECT rel_event_recipient.event_id
            FROM " . $tbl . "
            WHERE rel_event_recipient.course_id = " .(int) $course_id . "
                AND	event.author_id = " . (int) $user_id;
	$event_id_list = claro_sql_query_fetch_all($sql);
	if ($event_id_list == false)$result[] = $event_id_list;

	foreach ($event_id_list as $this_event_id)
	{	
		$tbl = get_conf('mainTblPrefix') . 'event';
		$sql = "DELETE FROM  " . $tbl . "
				WHERE id = " . (int) $this_event_id['event_id']. "
					AND	author_id = " . (int) $user_id;
		$result[] = claro_sql_query($sql);
	
		$tbl = get_conf('mainTblPrefix') . 'rel_event_recipient';
		$sql = "DELETE FROM  " . $tbl . "
				WHERE event_id = " . (int) $this_event_id['event_id'] ;
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
 * add an new event in the given or current course
 *
 * @param string   $title   	title of the new item
 * @param string   $description description of the new item
 * @param date     $star_date   start date of the event
 * @param date     $end_date   	end date of the event
 * @param string   $course_id 	sysCode of the course
 * @param integer  $author_id 	id of the creator of the event
 * @param integer  $repeat 		number of times the event must be repeated
 * @param integer  $visibility	set the visibility state
 * @param integer  $repeat_type	type of repeated event
 * @author Marc Lavergne
 * @return id of the new item
 */

function agenda_add_item($course_id, $author_id=NULL, $title='', $description='', $start_date=NULL, $end_date=NULL, $repeat, $repeat_type, $visibility='SHOW' )
{
	$final_result = true;
	$result = array();

	$formated_start_day    = date("Y-m-d",$start_date);
	$formated_start_hour   = date("H:i:s",$start_date);
	$formated_end_day      = date("Y-m-d",$end_date);
	$formated_end_hour     = date("H:i:s",$end_date);

    $tbl =  get_conf('mainTblPrefix') . 'event';
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
            course_id	= '" . $course_id . "',
			visibility	= '" . ($visibility=='HIDE'?'HIDE':'SHOW') . "'"; 
    $result[] = claro_sql_query_insert_id($sql);

	if ($repeat > 1)
	{
		$tbl =  get_conf('mainTblPrefix') . 'event';
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
    					course_id	= '" . $course_id . "',
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
 * Update an announcement in the current course
 *
 * @param integer  $event_id 	id of the event
 * @param string   $title   	title of the new item
 * @param string   $description description of the new item
 * @param date     $star_date   start date of the event
 * @param date     $end_date   	end date of the event
 * @param string   $course_id 	sysCode of the course
 * @param integer  $author_id 	id of the creator of the event
 * @param string   $update_repeat update all same events
 * @param integer  $visibility	set the visibility state
 * @author Marc Lavergne
 * @return handler of query
 */

function agenda_update_item($event_id, $title=NULL,$description=NULL, $start_date=NULL, $end_date=NULL, $author_id=NULL, $course_id, $update_repeat='this', $visibility='SHOW')
{
	$final_result = true;
	$result = array();
	$repeat_type = get_lang('Each week');

	$formated_start_day    = date("Y-m-d",$start_date);
	$formated_start_hour   = date("H:i:s",$start_date);
	$formated_end_day      = date("Y-m-d",$end_date);
	$formated_end_hour     = date("H:i:s",$end_date);
	$today                 = date("Y-m-d H:i:s",mktime());

    $sqlSet = array();
    if(!is_null($course_id))  $sqlSet[] = "rel_event_recipient.course_id 	   = '" . addslashes(trim($course_id)) . "' ";
    if(!is_null($title))      $sqlSet[] = "event.title 						   = '" . addslashes(trim($title)) . "' ";
    if(!is_null($description))$sqlSet[] = "event.description 				   = '" . addslashes(trim($description)) . "' ";
    if(!is_null($author_id))  $sqlSet[] = "event.author_id 				       = '" . addslashes(trim($author_id)) . "' ";
	if(!is_null($visibility)) $sqlSet[] = "rel_event_recipient.visibility      = '" . ($visibility=='HIDE'?'HIDE':'SHOW') . "'";
	if(!is_null($start_date)) $sqlSet[] = "event.start_date                    = '" . $formated_start_day . ' ' . $formated_start_hour . "' ";
	if(!is_null($end_date))   $sqlSet[] = "event.end_date                      = '" . $formated_end_day . ' ' . $formated_end_hour . "' ";

	if ($update_repeat == 'this') //update this event
	{
		if (count($sqlSet)>0)
		{
			$sqlSet[] = "event.master_event_id	= NULL "; //separates this event from a group of events 

			$tbl = get_conf('mainTblPrefix') . 'event AS event INNER JOIN ' 
				. get_conf('mainTblPrefix') . 'rel_event_recipient AS rel_event_recipient' 
				. ' ON event.id = rel_event_recipient.event_id'; //update only the selected event

			$sql = "UPDATE " . $tbl . "
    				SET " . implode(', ',$sqlSet) ."
    				WHERE event.id = " . (int) $event_id ;
			$result[] = claro_sql_query($sql);			
		}
	}
	if ($update_repeat=='from_this') //update from the selected event
	{
		$tbl = get_conf('mainTblPrefix') . 'event'; //get the master_event_id and the start_date from the selected event
		$sql =  "SELECT  master_event_id,
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
				$first_comp  = strtotime($event_id_list[0]['start_date']);
				$second_comp = strtotime($event_id_list[1]['start_date']);
				$day_numbers =($second_comp-$first_comp)/(24*60*60);

				if ($day_numbers==7) $repeat_type=get_lang('Each week');
				if ($day_numbers==1) $repeat_type=get_lang('Each day');
				if ($day_numbers==28 || $day_numbers==29 || $day_numbers==30 || $day_numbers==31) $repeat_type=get_lang('Each month');
			}		

			foreach($event_id_list as $this_event_id)
			{
				$result[] = agenda_delete_item($this_event_id['id'],'this'); //delete the events after the selected event
			}
			$result[] = agenda_add_item($course_id, $author_id, $title, $description, $start_date, $end_date, $nb_event,$repeat_type, $visibility ); //create the new updated events
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
 * @return array(`id`, `title`, `description`, `old_start_date`, `old_end_date`, `author_id`, `visibility`, `master_event_id`) of the event
 */

function agenda_get_item($event_id)
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

/**
 * return data for the event  of the given id of the given or current course
 *
 * @param integer $event_id id the requested event
 * @param string  $visibility 'SHOW' || 'HIDE'  ordering of the list.
 * @author Marc Lavergne
 * @return result handler
 */

function agenda_set_item_visibility($event_id, $visibility)
{
    $tbl = get_conf('mainTblPrefix') . 'rel_event_recipient';

    $sql = "UPDATE `" . $tbl . "`
            SET   visibility = '" . ($visibility=='HIDE'?"HIDE":"SHOW") . "'
                  WHERE `event_id` =  " . (int) $event_id;
    return  claro_sql_query($sql);
}
?>
