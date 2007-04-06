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

/**
 * get list of all agenda item in the given or current course
 *
 * @param string $order  'ASC' || 'DESC' : ordering of the list.
 * @param string $course_id current :sysCode of the course (leaveblank for current course)
 * @author Christophe Gesché <moosh@claroline.net>
 * @return array of array(`id`, `titre`, `contenu`, `day`, `hour`, `lasting`, `visibility`)
 * @since  1.7
 */

function user_agenda_get_item_list($user_id, $order='DESC')
{
    $tbl_mdb_names = claro_sql_get_main_tbl();
    $tbl = $tbl_mdb_names['user_event'];

    $sql = "SELECT `id`,
				   `user_id`,
                   `title`,
                   `content`,
                   `startday`,
                   `starthour`,
                   `endday`,
				   `endhour`
        FROM `" . $tbl . "`
        ORDER BY `startday` " . ('DESC' == $order?'DESC':'ASC') . "
        , `starthour` " . ('DESC' == $order?'DESC':'ASC') . "
		WHERE user_id= " . (int) $user_id;

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
function user_agenda_delete_item($event_id,$user_id )
{
    $tbl_mdb_names = claro_sql_get_main_tbl();
    $tbl = $tbl_mdb_names['user_event'];

    $sql = "DELETE FROM  `" . $tbl . "`
                WHERE `id` = " . (int) $event_id ."
                 AND `user_id`  = " . (int) $user_id ;
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
function user_agenda_delete_all_items($user_id)
{
    $tbl_mdb_names = claro_sql_get_main_tbl();
    $tbl = $tbl_mdb_names['user_event'];

    $sql = "DELETE FROM  `" . $tbl . "`
			WHERE user_id= " . (int) $user_id;
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

function user_agenda_add_item($user_id, $title='', $content='', $startday=NULL, $starthour=NULL, $endday=NULL, $endhour=NULL, $type=NULL)
{
    $tbl_mdb_names = claro_sql_get_main_tbl();
    $tbl = $tbl_mdb_names['user_event'];

    if (is_null($startday)) $day = date('Y-m-d');
    if (is_null($starthour)) $hour =  date('H:i:s');
    if (is_null($endday)) $day = date('Y-m-d');
    if (is_null($endhour)) $hour =  date('H:i:s');
    $sql = "INSERT INTO `" . $tbl . "`
        SET   user_id 	= '" . $user_id."',
			  title   	= '" . addslashes(trim($title)) . "',
              content 	= '" . addslashes(trim($content)) . "',
              startday  = '" . $startday . "',
              starthour = '" . $starthour . "',
              endday    = '" . $endday . "',
              endhour   = '" . $endhour . "',
              type    	= '" . $type . "'";

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

function user_agenda_update_item($event_id, $title=NULL,$content=NULL, $startday=NULL, $starthour=NULL, $endday=NULL, $endhour=NULL, $user_id, $type=NULL)
{
    $tbl_mdb_names = claro_sql_get_main_tbl();
    $tbl = $tbl_mdb_names['user_event'];

    $sqlSet = array();
    if(!is_null($title))      $sqlSet[] = " `title` = '" . addslashes(trim($title)) . "' ";
    if(!is_null($content))    $sqlSet[] = " `content` = '" . addslashes(trim($content)) . "' ";
    if(!is_null($startday))        $sqlSet[] = " `startday` = '" . addslashes(trim($startday)) . "' ";
    if(!is_null($starthour))       $sqlSet[] = " `starthour` = '" . addslashes(trim($starthour)) . "' ";
    if(!is_null($endday))        $sqlSet[] = " `endday` = '" . addslashes(trim($endday)) . "' ";
    if(!is_null($endhour))       $sqlSet[] = " `endhour` = '" . addslashes(trim($endhour)) . "' ";
    if(!is_null($type))       $sqlSet[] = " `type` = '" . addslashes(trim($type)) . "' ";

    if (count($sqlSet)>0)
    {
        $sql = "UPDATE `" . $tbl . "`
                SET " . implode(', ',$sqlSet) ."
                WHERE `id` = " . (int) $event_id ."
                 AND `user_id`  = " . (int) $user_id ;

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
 * @return array(`id`, `title`, `content`, `dayAncient`, `hourAncient`, `lastingAncient`) of the event
 * @since  1.7
 */

function user_agenda_get_item($user_id)
{
    $tbl_mdb_names = claro_sql_get_main_tbl();
    $tbl = $tbl_mdb_names['user_event'];
    $sql = "SELECT `user_id`,
                   `title`,
                   `content`,
                   `startday` as `startdayAncient`,
                   `starthour` as `starthourAncient`,
                   `endday` as `enddayAncient`,
				   `endhour` as `endhourAncient`,
				   `type` as `typeAncient`
            FROM `" . $tbl . "`
            WHERE `user_id` = " . (int) $user_id ;

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
function user_get_agenda_items($user_id)
{
    $items = array();

    // get agenda-items for every course
    $tbl_mdb_names = claro_sql_get_main_tbl();
    $tbl = $tbl_mdb_names['user_event'];

	$sql = "SELECT `id`,
				   `title`,
				   `content`,
				   `startday`,
				   `starthour`,
				   `endday`,
				   `endhour`
            FROM `" . $tbl . "`
            WHERE `user_id`  = " . (int) $user_id   ;

    $UserEventList = claro_sql_query_fetch_all($sql);

    if ( is_array($UserEventList) )

        foreach($UserEventList as $thisuserEvent )
        {
            $eventLine = trim(strip_tags($thisuserEvent['title']));

            if ( $eventLine == '' )
            {
                $eventContent = trim(strip_tags($thisuserEvent['content']));
                $eventLine    = substr($eventContent, 0, 60) . (strlen($eventContent) > 60 ? ' (...)' : '');
            }

            $eventStart = new claroDate($thisuserEvent['startday'] . ' ' .$thisuserEvent['starthour']);
			$eventEnd 	= new claroDate($thisuserEvent['endday'] . ' ' .$thisuserEvent['endhour']);
			if (!empty($thisuserEvent['id'])) 
			{
				$agendaEventList[] = new claroEvent( $eventStart, $thisuserEvent['title'],$thisuserEvent['content'],$eventEnd);
			}
			else
			{
				$agendaEventList = '';
			}
        } // end foreach userEventList 
	
    return $agendaEventList;
}


?>