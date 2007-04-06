<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
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

/**
 * get list of all agenda item in the given or current course
 *
 * @param string $order  'ASC' || 'DESC' : ordering of the list.
 * @param string $course_id current :sysCode of the course (leaveblank for current course)
 * @author Christophe Gesché <moosh@claroline.net>
 * @return array of array(`id`, `titre`, `contenu`, `day`, `hour`, `visibility`)
 * @since  1.7
 */

function agenda_get_item_list($cours_id, $order='DESC')
{
    $tbl = get_conf('mainDbName') . '`.`' . get_conf('mainTblPrefix') .'agenda2_cours_events';

    $sql = "SELECT `id`,
                   `title`,
                   `content`,
                   `startday`,
                   `starthour`,
                   `endday`,
				   `endhour`,
                   `type`,
				   `author`,
				   `visibility`
        FROM `" . $tbl . "`
		WHERE cours_id = " .  (int) $cours_id ."
        ORDER BY `startday` " . ('DESC' == $order?'DESC':'ASC') . "
        , `starthour` " . ('DESC' == $order?'DESC':'ASC');

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
function agenda_delete_item($event_id, $cours_id)
{
    $tbl = get_conf('mainDbName') . '`.`' . get_conf('mainTblPrefix') .'agenda2_cours_events';

    $sql = "DELETE FROM  `" . $tbl . "`
            WHERE id= " . (int) $event_id . "
			AND  cours_id = " . (int) $cours_id ;
    return claro_sql_query($sql);
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
function agenda_delete_all_items($course_id=NULL)
{
    $tbl = get_conf('mainDbName') . '`.`' . get_conf('mainTblPrefix') .'agenda2_cours_events';

    $sql = "DELETE FROM  `" . $tbl . "`
			WHERE cours_id= " . (int)$cours_id ;
    return claro_sql_query($sql);
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

function agenda_add_item($cours_id, $author=NULL, $title='', $content='', $startday=NULL, $starthour=NULL, $endday=NULL, $endhour=NULL, $type=NULL, $visibility='SHOW' )
{
    $tbl = get_conf('mainDbName') . '`.`' . get_conf('mainTblPrefix') .'agenda2_cours_events';

    if (is_null($startday)) $day = date('Y-m-d');
    if (is_null($starthour)) $hour =  date('H:i:s');
    $sql = "INSERT INTO `" . $tbl . "`
        SET   cours_id ='" . $cours_id . "',
			  title   = '" . addslashes(trim($title)) . "',
              content = '" . addslashes(trim($content)) . "',
              startday     = '" . $startday . "',
              starthour    = '" . $starthour . "',
              endday     = '" . $endday . "',
              endhour    = '" . $endhour . "',
              author    = '" . $author . "',
              type    = '" . $type . "',
              visibility = '" . ($visibility=='HIDE'?'HIDE':'SHOW') . "'";

    return claro_sql_query_insert_id($sql);
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

function agenda_update_item($event_id, $title=NULL,$content=NULL, $startday=NULL, $starthour=NULL, $endday=NULL, $endhour=NULL,$author, $type, $cours_id, $visibility='SHOW')
{
    $tbl = get_conf('mainDbName') . '`.`' . get_conf('mainTblPrefix') .'agenda2_cours_events';

    $sqlSet = array();
    if(!is_null($cours_id))   $sqlSet[] = " `cours_id` 	= '" . addslashes(trim($cours_id)) . "' ";
    if(!is_null($title))      $sqlSet[] = " `title` 	= '" . addslashes(trim($title)) . "' ";
    if(!is_null($content))    $sqlSet[] = " `content` 	= '" . addslashes(trim($content)) . "' ";
    if(!is_null($startday))   $sqlSet[] = " `startday` 	= '" . addslashes(trim($startday)) . "' ";
    if(!is_null($starthour))  $sqlSet[] = " `starthour` = '" . addslashes(trim($starthour)) . "' ";
    if(!is_null($endday))     $sqlSet[] = " `endday` 	= '" . addslashes(trim($endday)) . "' ";
    if(!is_null($endhour))    $sqlSet[] = " `endhour` 	= '" . addslashes(trim($endhour)) . "' ";
    if(!is_null($author))     $sqlSet[] = " `author` 	= '" . addslashes(trim($author)) . "' ";
    if(!is_null($type))       $sqlSet[] = " `type` 		= '" . addslashes(trim($type)) . "' ";
    if(!is_null($visibility)) $sqlSet[] = " `visibility`= '" . ($visibility=='HIDE'?'HIDE':'SHOW') . "' ";

    if (count($sqlSet)>0)
    {
        $sql = "UPDATE `" . $tbl . "`
                SET " . implode(', ',$sqlSet) ."
                WHERE `id` = " . (int) $event_id ;

        return claro_sql_query($sql);
    }
    else return NULL;
}


/**
 * return data for the event  of the given id of the given or current course
 *
 * @param integer $event_id id the requested event
 * @param string  $course_id sysCode of the course (leaveblank for current course)
 * @author Christophe Gesché <moosh@claroline.net>
 * @return array(`id`, `title`, `content`, `dayAncient`, `hourAncient`) of the event
 * @since  1.7
 */

function agenda_get_item($event_id)
{
    $tbl = get_conf('mainDbName') . '`.`' . get_conf('mainTblPrefix') .'agenda2_cours_events';
    $sql = "SELECT `id`,
                   `title`,
                   `content`,
                   `startday` as `startdayOld`,
                   `starthour` as `starthourOld`,
                   `endday` as `enddayOld`,
				   `endhour` as `endhourOld`,
				   `author` as `authorOld`,
				   `type` as `typeOld`
            FROM `" . $tbl . "`

            WHERE `id` = " . (int) $event_id ;

    $event = claro_sql_query_get_single_row($sql);

    if ($event) return $event;
    else        return claro_failure::set_failure('EVENT_ENTRY_UNKNOW');

}

/**
 * return data for the event  of the given id of the given or current course
 *
 * @param integer $event_id id the requested event
 * @param string  $visibility 'SHOW' || 'HIDE'  ordering of the list.
 * @param string  $course_id  sysCode of the course (leaveblank for current course)
 * @author Christophe Gesché <moosh@claroline.net>
 * @return result handler
 * @since  1.7
 */

function agenda_set_item_visibility($event_id, $visibility, $cours_id=NULL)
{
    $tbl = get_conf('mainDbName') . '`.`' . get_conf('mainTblPrefix') .'agenda2_cours_events';

    $sql = "UPDATE `" . $tbl . "`
            SET   visibility = '" . ($visibility=='HIDE'?"HIDE":"SHOW") . "'
                  WHERE `id` =  " . (int) $event_id ."
				  AND  cours_id = " . (int) $cours_id ;
    return  claro_sql_query($sql);
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
function get_agenda_items($user_id)
{
    $items = array();

    // get agenda-items for every course

        $tbl = get_conf('mainDbName') . '`.`' . get_conf('mainTblPrefix') .'agenda2_cours_events';

		$sql = "SELECT `id`,
					   `title`,
					   `content`,
					   `startday`,
					   `starthour`,
					   `endday`,
					   `endhour`,
					   `type`,
					   `author`
                FROM `" . $tbl . "`
                WHERE visibility   = 'SHOW'";

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
            $eventStart = new claroDate($thisEvent['startday'] . ' ' .$thisEvent['starthour']);
			$eventEnd 	= new claroDate($thisEvent['endday'] . ' ' .$thisEvent['endhour']);
			if ($thisEvent['id']!=NULL) {
			$agendaEventList[] = new claroEvent( $eventStart, $thisEvent['title'],$thisEvent['content'],$eventEnd,$thisEvent['type'],$thisEvent['author'],$url);}
        } // end foreach courseEventList


    return $agendaEventList;
}

function claro_disp_monthly_calendar($agendaItemList, $month, $year, $weekdaynames, $monthName )
{

    pushClaroMessage( (function_exists('claro_html_debug_backtrace')
             ? claro_html_debug_backtrace()
             : 'claro_html_debug_backtrace() not defined'
             )
             .'claro_disp_monthly_calendar is deprecated , use claro_html_monthly_calendar','error');

    return claro_html_monthly_calendar($agendaItemList, $month, $year, $weekdaynames, $monthName );
}


function agenda_get_type_list()
{
    $tbl = get_conf('mainDbName') . '`.`' . get_conf('mainTblPrefix') .'agend2_events_type';
    $sql = "SELECT 	`id`,
					`type`
        FROM `". $tbl ."`";
    return claro_sql_query_fetch_all($sql);
}

?>