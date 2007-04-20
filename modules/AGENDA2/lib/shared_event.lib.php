<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * get list of groups where the user is registred in current course
 *
 * @param integer   $user_id   		id of the current user
 * @param string 	$bl_groupUser   name of this course group table
 * @author Marc Lavergne
 * @return (team) id of the groups
 */
function get_user_group_list($user_id, $tbl_groupUser)
{
	$sql = "SELECT team
			FROM ". $tbl_groupUser ."
			WHERE user =" . (int) $user_id;
	return claro_sql_query_fetch_all($sql);			
}


/**
 * get list of users in a course
 *
 * @param array   	$tbl_user   	table of users
 * @param array   	$tbl_courseUser table of course users
 * @param string 	$course_id   	id of the current cours
 * @author Marc Lavergne
 * @return (lastName, firstName, uid) of the course users
 */
function get_user_course_list($tbl_user,$tbl_courseUser,$course_id)
{
	$sql = "SELECT `u`.`nom`     AS `lastName`,
				  `u`.`prenom`  AS `firstName`,
				  `u`.`user_id` AS `uid`
			FROM `" . $tbl_user .     "` AS `u`
					, `" . $tbl_courseUser."` AS `cu`
				WHERE `cu`.`code_cours` = '" . addslashes($course_id) . "'
				AND `cu`.`user_id` = `u`.`user_id`
				ORDER BY `u`.`nom`, `u`.`prenom`";
	
	return claro_sql_query_fetch_all($sql);
}


/**
 * get list of groups in a course
 *
 * @param array   	$tbl_groupUser  	table of group users in a course
 * @param array   	$tbl_group 		table of groups in a course
 * @author Marc Lavergne
 * @return (id, name, userNB) 
 */
function get_group_course_list($tbl_groupUser,$tbl_group)
{
	$sql = "SELECT `g`.`id`,
				   `g`.`name`,
					COUNT(`gu`.`id`) AS `userNb`
			FROM `" . $tbl_group . "` AS `g` LEFT JOIN `" . $tbl_groupUser . "` AS `gu`
			ON `g`.`id` = `gu`.`team`
			GROUP BY `g`.`id`";
	
	return claro_sql_query_fetch_all($sql);
}


/**
 * add a shared item
 *
 * @param string   	$course_id  	id of the cours
 * @param integer  	$author_id 		id of the author
 * @param array   	$user_idlist	list of selected users
 * @param array   	$group_idlist	list of selected groups
 * @param string   	$title 			title of the event
 * @param string   	$description 	dscription of the event
 * @param timestamp	$start_date		start date
 * @param timestamp	$end_date		end date
 * @param string	$visibility		visibility
 * @author Marc Lavergne
 */
function shared_add_item($course_id, $author_id, $user_idlist, $group_idlist, $title='', $description='', $start_date=NULL, $end_date=NULL, $visibility='SHOW' )
{
	$final_result=true;
	$result=array();

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
	if ($event_id == false)$result[] = $event_id;

	if (!empty($user_idlist))
	{
		foreach ($user_idlist as $this_user_id)
		{
			$tbl = get_conf('mainDbName') . '`.`' . get_conf('mainTblPrefix') . 'rel_event_recipient';
			$sql = "INSERT INTO `" . $tbl . "`
				SET user_id ='" . (int) $this_user_id . "',
					event_id='" . (int) $event_id . "',
					course_id= '" . $course_id . "',
					visibility	= '" . ($visibility=='HIDE'?'HIDE':'SHOW') . "'"; 
			$result[] = claro_sql_query_insert_id($sql);
		}
	}
	if (!empty($group_idlist))
	{
		foreach ($group_idlist as $this_group_id)
		{
			$tbl = get_conf('mainDbName') . '`.`' . get_conf('mainTblPrefix') . 'rel_event_recipient';
			$sql = "INSERT INTO `" . $tbl . "`
				SET group_id ='" . (int) $this_group_id . "',
					event_id='" . (int) $event_id . "',
					course_id= '" . $course_id . "',
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
 * get list of selected shared groups
 *
 * @param string   	$event_id  	id of the selected event
 * @author Marc Lavergne
 * @return (group_id) 
 */
function get_shared_group_list($event_id)
{
	$tbl = get_conf('mainDbName') . '.' . get_conf('mainTblPrefix') . 'rel_event_recipient';

	$sql = "SELECT ". get_conf('mainTblPrefix') . "rel_event_recipient.group_id AS group_id
			FROM " . $tbl . "
			WHERE ". get_conf('mainTblPrefix') . "rel_event_recipient.event_id = $event_id";
	return claro_sql_query_fetch_all($sql);
}


/**
 * get list of selected shared users
 *
 * @param string   	$event_id  	id of the selected event
 * @author Marc Lavergne
 * @return (user_id) 
 */
function get_shared_user_list($event_id)
{
	$tbl = get_conf('mainDbName') . '.' . get_conf('mainTblPrefix') . 'rel_event_recipient';
	$sql = "SELECT ". get_conf('mainTblPrefix') . "rel_event_recipient.user_id AS user_id
			FROM " . $tbl . "
			WHERE ". get_conf('mainTblPrefix') . "rel_event_recipient.event_id = $event_id";
	return claro_sql_query_fetch_all($sql);
}


/**
 * identifie selected group
 *
 * @param array   	$tbl_groupUser  	table of group users in a course
 * @param array   	$tbl_group 			table of groups in a course
 * @param array   	$group_id_list 		table of selectd groups
 * @author Marc Lavergne
 * @return (id, name, userNB) 
 */
function get_selected_group_list($tbl_groupUser,$tbl_group,$group_id_list)
{
	$result=array();
	foreach ($group_id_list as $this_group_id)
	{
		if ($this_group_id['group_id']!=NULL)
		{
			$sql = "SELECT g.id,
						   g.name,
							COUNT(gu.id) AS userNb
					FROM " . $tbl_group . " AS g LEFT JOIN " . $tbl_groupUser . " AS gu
					ON g.id = gu.team
					WHERE g.id = ". $this_group_id['group_id']."
					GROUP BY g.id";			
			$result []= claro_sql_query_fetch_all($sql);
		}
	}
    return $result;
}


/**
 * identifie selected users
 *
 * @param array   	$tbl_user  			table of users
 * @param array   	$tbl_courseUser		table of users in a course
 * @param array   	$course_id			id of the current course
 * @param array   	$group_id_list 		table of selectd groups
 * @author Marc Lavergne
 * @return (lastName, firstName, uid) of the course users
 */
function get_selected_user_cours_list($tbl_user,$tbl_courseUser,$course_id,$user_id_list)
{
	$result=array();
	foreach ($user_id_list as $this_user_id)
	{
		if ($this_user_id['user_id']!=NULL)
		{
			$sql = "SELECT 	u.nom     AS lastName,
							u.prenom  AS firstName,
							u.user_id AS uid
						FROM `" . $tbl_user .  "` 	AS u, 
							 `" . $tbl_courseUser."` 	AS cu
						WHERE cu.code_cours = '" . addslashes($course_id) . "'
							AND cu.user_id = u.user_id
							AND cu.user_id = ". $this_user_id['user_id']."
						ORDER BY u.nom, u.prenom";
			
			$result[] = claro_sql_query_fetch_all($sql);
		}
	}
    return $result;
}
?>