<?php // $Id$

/**
 * CLATT tool
 * Tableau de liste de présence
 * 
 * @version     1.0
 * @author      Lambert Jérôme <lambertjer@gmail.com>
 * @author 		Philippe Dekimpe
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE version 2.0
 * @package     CLATT
 */

/**
 * return the list of user of the course in parameter. It use by default the 
 * current course identification
 *
 * @param char $courseId course identication
 * @return array of int
 */
function get_course_user_list($order = 'nom')
{
    $tbl_mdb_names = claro_sql_get_main_tbl();
    
    $tbl_rel_course_user = $tbl_mdb_names['rel_course_user'  ];
    $tbl_users           = $tbl_mdb_names['user'             ];
    
    $sql = "SELECT `user`.`user_id`      AS `user_id`,
                           `user`.`nom`          AS `nom`,
                           `user`.`prenom`       AS `prenom`,
                           `user`.`email`        AS `email`,
                           `course_user`.`profile_id`,
                           `course_user`.`isCourseManager`,
                           `course_user`.`tutor`  AS `tutor`,
                           `course_user`.`role`   AS `role`
                    FROM `" . $tbl_users . "`           AS user,
                        `" . $tbl_rel_course_user . "` AS course_user
                    WHERE `user`.`user_id`=`course_user`.`user_id`
                    	AND   `course_user`.`code_cours`='" .  claro_get_current_course_id() . "' 
                    	AND   `isCourseManager` = 0
                    	AND   `tutor` = 0
                    ORDER BY " . Claroline::getDatabase()->escape($order) . " ;";
    
    $result = Claroline::getDatabase()->query($sql);
    
    return $result;
}

/**
 * list of attendance for the current course
 * 
 * @return array of list of attendance for the current course
 */
function get_attendance_course_list()
{
    $toolTables = get_module_course_tbl( array('attendance_session'), claro_get_current_course_id() );
    
    $sql = "SELECT `id`, UNIX_TIMESTAMP(`date_att`) as date_att, `title`
                                    FROM `".$toolTables['attendance_session']."` 
                                    ORDER BY date_att";
    $result = Claroline::getDatabase()->query($sql);
    
    return $result;
}

/**
 * get all users' attendance of a register
 * 
 * @return array of users with attendance and comment for one list
 */
function get_attendance_users($idList)
{
	$toolTables = get_module_course_tbl( array('attendance'), claro_get_current_course_id() );
    
    $sql = "SELECT `user_id`, `attendance` , `comment`
			FROM `".$toolTables['attendance']."` 
           WHERE id_list='".$idList ."'";
    $result = Claroline::getDatabase()->query($sql);
    
    return $result;

}

/**
 * check attendance exist for a user
 * 
 * @return number of attendance for this user in this list 0 or 1
 */
function is_attendance($userId,$idList)
{
	$toolTables = get_module_course_tbl( array('attendance'), claro_get_current_course_id() );
    
    $sql = "SELECT `attendance`
			FROM `".$toolTables['attendance']."` 
           WHERE user_id ='" . $userId . "'AND id_list='".$idList ."'";
    $result = Claroline::getDatabase()->query($sql);
    
    return $result->numRows();
}

/**
 * get title of list
 * 
 * @param id of the attendance list
 * @return string
 */
function get_title($idList)
{
	$toolTables = get_module_course_tbl( array('attendance_session'), claro_get_current_course_id() );
    
    $sql = "SELECT `title`
			FROM `".$toolTables['attendance_session']."` 
           WHERE id='".$idList."'";
    $result = Claroline::getDatabase()->query($sql);
    
    return $result->fetch( Database_ResultSet::FETCH_VALUE );
}

/**
 * get date of list
* @return string
 */
function get_date($idList)
{
	$toolTables = get_module_course_tbl( array('attendance_session'), claro_get_current_course_id() );
    
    $sql = "SELECT UNIX_TIMESTAMP(`date_att`) as date_att
			FROM `".$toolTables['attendance_session']."` 
           WHERE id='".$idList."'";
    $result = Claroline::getDatabase()->query($sql);
    
    return $result->fetch( Database_ResultSet::FETCH_VALUE );
}

/**
 * Save attendance of a user
 */
function set_attendance($user_id,$idList,$attendance,$comment)
{
	$toolTables = get_module_course_tbl( array('attendance'), claro_get_current_course_id() );
    if(!is_attendance($user_id,$idList))
	{
		$sql = "INSERT INTO `".$toolTables['attendance']."` SET
				`id_list` ='" . (int)$idList . "',
				`user_id`= '" . (int)$user_id . "',
				`attendance` ='". Claroline::getDatabase()->escape($attendance) ."',
				`comment` ='". Claroline::getDatabase()->escape($comment) ."' ;";
		$result = Claroline::getDatabase()->exec($sql);
	}
	else
	{
		$sql = "UPDATE `".$toolTables['attendance']."` 
    			SET attendance='" . $attendance . "', comment='" . $comment . "'  
    			WHERE user_id='".  (int)$user_id . "' 
    				AND id_list='" . (int)$idList . "' ";
		$result = Claroline::getDatabase()->exec($sql);
	}
	
	return $result;
}

 /**
  * Create new list of attendance
  * @params date of attendance list, title of attenande list
  */
function create_attendanceList($date_att, $titleToAdd)
{
	$toolTables = get_module_course_tbl( array('attendance_session'), claro_get_current_course_id() );

	$sql = "INSERT INTO `".$toolTables['attendance_session']."` SET
				`date_att` ='". $date_att ."', 
				`title` = '".Claroline::getDatabase()->escape($titleToAdd) ."' ;";

    $result = Claroline::getDatabase()->exec($sql);
    
    return $result;
}

 /**
  * Edit date and title of attendance
  * @params  id of the attendance list, date of attendance list, title of attenande list
  */
function edit_attendanceList($id_list,$date_att, $titleToAdd)
{
	$toolTables = get_module_course_tbl( array('attendance_session'), claro_get_current_course_id() );

	$sql = "UPDATE `".$toolTables['attendance_session']
	        . "` SET title='" . Claroline::getDatabase()->escape($titleToAdd )
	        . "',date_att='" . $date_att
	        . "' WHERE id = '". $id_list ."' ";
    $result = Claroline::getDatabase()->exec($sql);
    
    return $result;
}

/**
 * Delete list of attendance
 */
function del_attendanceList($id = NULL)
{
    $toolTables = get_module_course_tbl( array('attendance','attendance_session'), claro_get_current_course_id() );
    
    $sql = "DELETE FROM `".$toolTables['attendance']."` WHERE `id_list` = '". $id ."'";
    $result = Claroline::getDatabase()->exec($sql);
    
    $sql = "DELETE FROM `".$toolTables['attendance_session']."` WHERE `id` = '". $id ."'";
    $result2 = Claroline::getDatabase()->exec($sql);
    
    if ($result>0 && $result2) return true;
    else return false;
}

/**
 * Summary af the attendance (sum of presence,... for each user) 
 *
 * @param mysql date $dateBegin
 * @param mysql date $dateEnd
 */
function get_summary_attendance($dateBegin = 0,$dateEnd = 0)
{
        $tbl_mdb_names = claro_sql_get_main_tbl();
        $tbl_user = $tbl_mdb_names['user'];
        $toolTables = get_module_course_tbl( array( 'attendance','attendance_session' ), claro_get_current_course_id() );
		
        $sql = "SELECT 	s.`id`, s.`date_att`, s.`title`, 
        				u.`user_id`, u.`prenom`, u.`nom`,  
        				SUM(IF(attendance='present',1,0)) as present,
        				SUM(IF(attendance='partial',1,0)) as partial,
        				SUM(IF(attendance='absent',1,0)) as absent,
        				SUM(IF(attendance='excused',1,0)) as excused
                FROM `" . $tbl_user . "` u
                INNER JOIN `" . $toolTables['attendance'] . "` a ON u.`user_id` = a.`user_id`
                INNER JOIN `" . $toolTables['attendance_session'] . "` s ON s.`id` = a.`id_list` ";
        
        if ($dateBegin != 0 && $dateEnd != 0) 
            $sql .= "WHERE s.date_att >'" . $this->start_date 
                    . "' AND s.date_att <'" . $this->end_date ."' ";

        $sql .= "GROUP BY u.`user_id` ORDER BY u.nom, u.prenom  ;";
            
        $result = Claroline::getDatabase()->query($sql);
        
        return $result;
}
?>