<?php

function group_Id_List($group_idlist, $tbl_group_user)
{				
	$sql = "SELECT `user`
		FROM `".$tbl_group_user."` AS `user_group`
		WHERE `team` IN (".$group_idlist.")";	
	return claro_sql_query_fetch_all($sql);
}

function get_user_cours_list($tbl_user,$tbl_courseUser,$cours_id)
{
	$sql =    "SELECT `u`.`nom`     AS `lastName`,
					  `u`.`prenom`  AS `firstName`,
					  `u`.`user_id` AS `uid`
				FROM `" . $tbl_user .     "` AS `u`
						, `" . $tbl_courseUser."` AS `cu`
					WHERE `cu`.`code_cours` = '" . addslashes($cours_id) . "'
					AND `cu`.`user_id` = `u`.`user_id`
					ORDER BY `u`.`nom`, `u`.`prenom`";
	
	return claro_sql_query_fetch_all($sql);
}

function get_group_cours_list($tbl_groupUser,$tbl_group)
{
	$sql = "SELECT `g`.`id`,
				   `g`.`name`,
					COUNT(`gu`.`id`) AS `userNb`
			FROM `" . $tbl_group . "` AS `g` LEFT JOIN `" . $tbl_groupUser . "` AS `gu`
			ON `g`.`id` = `gu`.`team`
			GROUP BY `g`.`id`";
	
	return claro_sql_query_fetch_all($sql);
}

function shared_add_item($cours_id, $author_id, $user_idlist, $group_idlist, $title='', $description='', $start_date=NULL, $end_date=NULL, $visibility='SHOW' )
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

	if (!empty($user_idlist))
	{
		foreach ($user_idlist as $this_user_id)
		{
			$tbl = get_conf('mainDbName') . '`.`' . get_conf('mainTblPrefix') . 'rel_event_recipient';
			$sql = "INSERT INTO `" . $tbl . "`
				SET user_id ='" . (int) $this_user_id . "',
					event_id='" . (int) $event_id . "',
					cours_id= '" . $cours_id . "',
					visibility	= '" . ($visibility=='HIDE'?'HIDE':'SHOW') . "'"; 
			$reslut = claro_sql_query_insert_id($sql);
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
					cours_id= '" . $cours_id . "',
					visibility	= '" . ($visibility=='HIDE'?'HIDE':'SHOW') . "'"; 
			$reslut = claro_sql_query_insert_id($sql);
		}
	}
	return $reslut;
}
?>