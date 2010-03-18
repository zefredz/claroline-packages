<?php // $Id$
/**
 * CLPRES tool
 * Tableau de liste de présence
 * 
 * @version     1.0
 * @author      Lambert Jérôme <lambertjer@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE version 2.0
 * @package     CLPRES
 */
function push_date_format ($date)
{
    if ($date < 9) {
        return (0) . $date;
    } else {
        return $date;
    }
}
//type YYYY-MM-DD
function compare_date ($date1, $date2)
{
    $date1_obj = new DateTime();
    $date2_obj = new DateTime();
    $yearDate1 = substr($date1, 0, 4);
    $monthDate1 = substr($date1, 5, 2);
    $dayDate1 = substr($date1, - 2);
    $date1_obj->setDate($yearDate1, $monthDate1, $dayDate1);
    $yearDate2 = substr($date2, 0, 4);
    $monthDate2 = substr($date2, 5, 2);
    $dayDate2 = substr($date2, - 2);
    $date2_obj->setDate($yearDate2, $monthDate2, $dayDate2);
    if ($date1_obj > $date2_obj)
        return 1;
    if ($date1_obj == $date2_obj)
        return 0;
    if ($date1_obj < $date2_obj)
        return - 1;
}
function get_attendance_course_list ()
{
    $toolTables = get_module_course_tbl('clpres_attendance', claro_get_current_course_id());
    $sql = "SELECT `id`, `date_att`, `user_id`, `is_att` 
                                    FROM `" . $toolTables['clpres_attendance'] . "` 
                                    ORDER BY date_att";
    return claro_sql_query_fetch_all($sql);
}
function is_attendance ($user_id, $date_att)
{
    $toolTables = get_module_course_tbl(array('clpres_attendance'), claro_get_current_course_id());
    $sql = "SELECT `id`, `date_att`, `user_id`, `is_att` 
			FROM `" . $toolTables['clpres_attendance'] . "` 
           WHERE user_id='" . $user_id . "' AND date_att='" . $date_att . "'";
    $attendanceRow = claro_sql_query_fetch_all($sql);
    if ($attendanceRow == false || $attendanceRow == NULL) {
        return - 1;
    } else {
        foreach ($attendanceRow as $val) {
            return $val['is_att'];
        }
    }
}
function get_total_attendance ($date_att)
{
    $toolTables = get_module_course_tbl(array('clpres_attendance'), claro_get_current_course_id());
    $sql = "SELECT COUNT(*) 
			FROM `" . $toolTables['clpres_attendance'] . "` 
           WHERE date_att='" . $date_att . "' AND is_att=1";
    $result = claro_sql_query_get_single_row($sql);
    return $result;
}
function set_attendance ($user_id, $date_att)
{
    $toolTables = get_module_course_tbl(array('clpres_attendance'), claro_get_current_course_id());
    if ((is_attendance($user_id, $date_att) == - 1)) {
        $sql = "INSERT INTO `" . $toolTables['clpres_attendance'] . "` (`date_att`, `user_id`, `is_att`) VALUES ('" . $date_att . "', '" . $user_id . "', '1')";
        $attendanceRow = claro_sql_query($sql);
    } else {
        $sql = "UPDATE `" . $toolTables['clpres_attendance'] . "` SET is_att='1' WHERE user_id='" . $user_id . "' AND date_att='" . $date_att . "' LIMIT 1";
        $attendanceRow = claro_sql_query($sql);
    }
}
function unset_attendance ($user_id, $date_att)
{
    $toolTables = get_module_course_tbl(array('clpres_attendance'), claro_get_current_course_id());
    if ((is_attendance($user_id, $date_att) == - 1)) {
        $sql = "INSERT INTO `" . $toolTables['clpres_attendance'] . "` (`date_att`, `user_id`, `is_att`) VALUES ('" . $date_att . "', '" . $user_id . "', '0')";
        $attendanceRow = claro_sql_query($sql);
    } else {
        $sql = "UPDATE `" . $toolTables['clpres_attendance'] . "` SET is_att='0' WHERE user_id='" . $user_id . "' AND date_att='" . $date_att . "' LIMIT 1";
        $attendanceRow = claro_sql_query($sql);
    }
}
?>