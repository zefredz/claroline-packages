<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );

/**
 * get list of all agenda item (cours and directed items) in the current course
 * @param string $order  'ASC' || 'DESC' : ordering of the list.
 * @param string $course_id current :sysCode of the course
 * @author Marc Lavergne
 * @return array of array(`id`, `title`, `description`, `start_date`, `end_date`, `author_id`, `visibility`)
 */

function agenda_get_item_list($cours_id, $user_id, $order='DESC') //OK

{	
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
		WHERE 	(". get_conf('mainTblPrefix') . "rel_event_recipient.cours_id = " .  (int) $cours_id ."
				AND ". get_conf('mainTblPrefix') . "rel_event_recipient.user_id is NULL)
			OR
				(". get_conf('mainTblPrefix') . "rel_event_recipient.cours_id = " .  (int) $cours_id ."
				AND ". get_conf('mainTblPrefix') . "rel_event_recipient.user_id = " . (int) $user_id .")
		ORDER BY ". get_conf('mainTblPrefix') . "event.start_date " . ('DESC' == $order?'DESC':'ASC');

	return claro_sql_query_fetch_all($sql);
}

/**
 * Delete an event in the current course
 *
 * @param integer $event_id id the requested event
 * @param repeat to delete similar events
 * @param string $course_id current :sysCode of the course (leaveblank for current course)
 * @author Marc Lavergne
 * @return result of deletion query
 */
function agenda_delete_item($event_id,$repeat=TRUE) //+-
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

	if ($repeat==TRUE)
	{
//var_dump($master_event_id);
		foreach ($master_event_id as $this_master_event_id) //---------------------------------triche
		{
		$tbl = get_conf('mainTblPrefix') . 'event';
		$sql = "SELECT id
			FROM " . $tbl . "
			WHERE master_event_id = " .(int) $this_master_event_id['master_event_id'];
		$event_id_list = claro_sql_query_fetch_all($sql);
//var_dump($event_id_list);
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
 * Delete all events in the current course
 *
 * @param string $course_id current :sysCode of the course (leaveblank for current course)
 * @author Marc Lavergne
 * @return result of deletion query
 */
function agenda_delete_all_items($cours_id=NULL) //OK
{
	$tbl = get_conf('mainTblPrefix') . 'rel_event_recipient';
	$sql = "SELECT event_id
		FROM " . $tbl . "
		WHERE cours_id = " .(int) $cours_id;
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
 * @param string   $title   	title of the new item
 * @param string   $description description of the new item
 * @param date     $star_date   start date of the event
 * @param date     $end_date   	end date of the event
 * @param string   $course_id 	sysCode of the course
 * @param integer  $author_id 	id of the creator of the event
 * @param integer  $repeat 		number of times the event must be repeadted
 * @param integer  $visibility	set the visibility state
 * @author Marc Lavergne
 * @return id of the new item
 */

function agenda_add_item($cours_id, $author_id=NULL, $title='', $description='', $start_date=NULL, $end_date=NULL, $repeat, $visibility='SHOW' ) //OK
{
	$formated_start_day = date("Y-m-d",$start_date);
	$formated_start_hour = date("H:i:s",$start_date);
	$formated_end_day   = date("Y-m-d",$end_date);
	$formated_end_hour   = date("H:i:s",$end_date);

    $tbl = get_conf('mainDbName') . '`.`' . get_conf('mainTblPrefix') . 'event';
    $sql = "INSERT INTO `" . $tbl . "`
        SET title   	 = '" . addslashes(trim($title)) . "',
            description  = '" . addslashes(trim($description)) . "',
            start_date   = '" . $formated_start_day . ' ' . $formated_start_hour . "',
            end_date     = '" . $formated_end_day . ' ' . $formated_end_hour . "',
            author_id    = '" . $author_id . "'";
	$event_id = claro_sql_query_insert_id($sql);

    $tbl = get_conf('mainDbName') . '`.`' . get_conf('mainTblPrefix') . 'rel_event_recipient';
	$sql = "INSERT INTO `" . $tbl . "`
        SET event_id   	= '" . (int) $event_id . "',
            cours_id	= '" . $cours_id . "',
			visibility	= '" . ($visibility=='HIDE'?'HIDE':'SHOW') . "'"; 
    $result = claro_sql_query_insert_id($sql);

	if ($repeat > 1)
	{
		$tbl = get_conf('mainDbName') . '`.`' . get_conf('mainTblPrefix') . 'event';
		$sqlSet = array();
		
        $sql = "UPDATE `" . $tbl . "`
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
				
			$tbl = get_conf('mainDbName') . '`.`' . get_conf('mainTblPrefix') . 'event';
			$sql = "INSERT INTO `" . $tbl . "`
				SET title   	 = '" . addslashes(trim($title)) . "',
					description  = '" . addslashes(trim($description)) . "',
					author_id    = '" . (int) $author_id . "',
					start_date   = '" . $repeat_start_date . "',
					end_date     = '" . $repeat_end_date . "',
					master_event_id = '" . (int) $event_id . "'";
			$repeat_event_id = claro_sql_query_insert_id($sql);
		
			$tbl = get_conf('mainDbName') . '`.`' . get_conf('mainTblPrefix') . 'rel_event_recipient';
			$sql = "INSERT INTO `" . $tbl . "`
				SET event_id   	= '" . (int) $repeat_event_id . "',
					cours_id	= '" . $cours_id . "',
					visibility	= '" . ($visibility=='HIDE'?'HIDE':'SHOW') . "'"; 
			$result = claro_sql_query_insert_id($sql);
		}
	}
	return $result;
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
 * @since  1.7
 */

function agenda_update_item($event_id, $title=NULL,$description=NULL, $start_date=NULL, $end_date=NULL, $author_id=NULL, $cours_id, $update_repeat='true', $visibility='SHOW')
{
	$formated_start_day = date("Y-m-d",$start_date);
	$formated_start_hour = date("H:i:s",$start_date);
	$formated_end_day   = date("Y-m-d",$end_date);
	$formated_end_hour   = date("H:i:s",$end_date);

    $sqlSet = array();
    if(!is_null($cours_id))   $sqlSet[] = get_conf('mainTblPrefix') . "rel_event_recipient.cours_id 	= '" . addslashes(trim($cours_id)) . "' ";
    if(!is_null($title))      $sqlSet[] = get_conf('mainTblPrefix') . "event.title 						= '" . addslashes(trim($title)) . "' ";
    if(!is_null($description))$sqlSet[] = get_conf('mainTblPrefix') . "event.description 				= '" . addslashes(trim($description)) . "' ";
    if(!is_null($start_date)) $sqlSet[] = get_conf('mainTblPrefix') . "event.start_date					= '" . $formated_start_day . ' ' . $formated_start_hour . "' ";
    if(!is_null($end_date))   $sqlSet[] = get_conf('mainTblPrefix') . "event.end_date					= '" . $formated_end_day . ' ' . $formated_end_hour . "' ";
    if(!is_null($author_id))  $sqlSet[] = get_conf('mainTblPrefix') . "event.author_id 					= '" . addslashes(trim($author_id)) . "' ";

	if ($update_repeat=='true')
	{
		$tbl = get_conf('mainTblPrefix') . 'event';
		$sql = "SELECT  master_event_id
			FROM " . $tbl . "
			WHERE id = " .(int) $event_id;
		$master_event_id = claro_sql_query_fetch_all($sql);
		foreach($master_event_id as $this_master_event_id)  //triche-------------------------------------------------------------------------------------<-----------
		{
			if (count($sqlSet)>0)
			{
				$tbl = get_conf('mainTblPrefix') . 'event' . ',' . get_conf('mainTblPrefix') . 'rel_event_recipient';
				$sql = "UPDATE " . $tbl . "
						SET " . implode(', ',$sqlSet) ."
						WHERE " . get_conf('mainTblPrefix') . "event.master_event_id = " . (int) $this_master_event_id['master_event_id'] ;
				$result = claro_sql_query($sql);
			}
		}
	}
	else
	{
		if (count($sqlSet)>0)
		{
			$tbl = get_conf('mainTblPrefix') . 'event' . ',' . get_conf('mainTblPrefix') . 'rel_event_recipient';
			$sqlSet[] = get_conf('mainTblPrefix') . "event.master_event_id	= NULL ";
			$sql = "UPDATE " . $tbl . "
				SET " . implode(', ',$sqlSet) ."
				WHERE " . get_conf('mainTblPrefix') . "event.id = " . (int) $event_id ;
			$result = claro_sql_query($sql);
		}
	}
    return $result;
}


/**
 * return data for the event  of the given id of the given or current course
 *
 * @param integer $event_id id the requested event
 * @author Marc Lavergne
 * @return array(`id`, `title`, `description`, `old_start_date`, `old_end_date`, `author_id`, `visibility`, `master_event_id`) of the event
 * @since  1.7
 */

function agenda_get_item($event_id) //ok
{
    $tbl = get_conf('mainTblPrefix') . 'event' . ',' . get_conf('mainTblPrefix') . 'rel_event_recipient';
    $sql = "SELECT 	". get_conf('mainTblPrefix') . "event.id 						AS id,
					". get_conf('mainTblPrefix') . "event.title 					AS title,
					". get_conf('mainTblPrefix') . "event.description 				AS description,
					". get_conf('mainTblPrefix') . "event.start_date 				AS old_start_date,
					". get_conf('mainTblPrefix') . "event.end_date 					AS old_end_date,
					". get_conf('mainTblPrefix') . "event.author_id 				AS author_id,
					". get_conf('mainTblPrefix') . "rel_event_recipient.visibility 	AS visibility,
					". get_conf('mainTblPrefix') . "event.master_event_id		 	AS master_event_id
            FROM " . $tbl . "

            WHERE ". get_conf('mainTblPrefix') . "event.id = " . (int) $event_id ;

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


//////////////////////////////////////////////////////////////////////////////

/**
 * fetch all agenda item of a user
 *
 * @param array $userCourseList
 * @param integer $month
 * @param integer $year
 * @return array list of items
 */
function get_agenda_items($userCourseList,$IsAllowedToEdit=FALSE)
{
    $items = array();

    // get agenda-items for every course
    foreach( $userCourseList as $thisCourse)
    {
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
                WHERE visibility   = 'SHOW'
					AND ". get_conf('mainTblPrefix') . "rel_event_recipient.cours_id = " .  (int) $thisCourse ;

        $courseEventList = claro_sql_query_fetch_all($sql);

        if ( is_array($courseEventList) )

        foreach($courseEventList as $thisEvent )
        {
            $eventLine = trim(strip_tags($thisEvent['title']));

            if ( $eventLine == '' )
            {
                $eventContent = trim(strip_tags($thisEvent['content']));
                $eventLine    = substr($eventContent, 0, 60) . (strlen($eventContent) > 60 ? ' (...)' : '');
            }
	
			$url       = 'agenda.php?cidReq=' . $thisCourse['sysCode'];
            $eventStart = new claroDate($thisEvent['start_date']);
			$eventEnd 	= new claroDate($thisEvent['end_date']);
			if ($thisEvent['id']!=NULL) 
			{
				$agendaEventList[] = new claroEvent( $eventStart, $thisEvent['title'],$thisEvent['description'],$eventEnd,$thisEvent['author_id'],$url);
			}
        } // end foreach courseEventList
	}
    return $agendaEventList;
}
?>