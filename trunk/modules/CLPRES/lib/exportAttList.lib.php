<?php // $Id$
/**
 *
 * @version 1.0
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @author Lambert Jérôme <lambertjer@gmail.com>
 *
 * @package CLPRES
 *
 */
require_once get_path('incRepositorySys') . '/lib/csv.class.php';
require_once 'attendance.lib.php';
class csvAttList extends csv
{
    var $course_id;
    var $exId;
    var $start_date;
    var $end_date;
    function csvAttList ()
    {
        parent::csv(); // call constructor of parent class
        $aArgs = func_get_args();
        $cptArgs = count($aArgs);
        $this->course_id = $aArgs[0];
        if ($cptArgs > 1) {
            $this->start_date = $aArgs[1];
            $this->end_date = $aArgs[2];
        } else {
            $this->start_date = 0;
            $this->end_date = 0;
        }
    }
    function buildRecords ()
    {
        $tbl_mdb_names = claro_sql_get_main_tbl();
        $tbl_user = $tbl_mdb_names['user'];
        $tbl_rel_course_user = $tbl_mdb_names['rel_course_user'];
        $toolTables = get_module_course_tbl(array('clpres_attendance'), claro_get_current_course_id());
        $totalJour = 0;
        $totalUser = 0;
        // get date list
        $sqlDate = "SELECT DISTINCT `AU`.`date_att`		AS `date`
					FROM `" . $toolTables['clpres_attendance'] . "` AS `AU`
					ORDER BY AU.`date_att`";
        $dateList = claro_sql_query_fetch_all($sqlDate);
        // build recordlist with good values for date
        if (! empty($dateList)) {
            $i = 1;
            //Fill the col with date
            $this->recordList[0][0] = 'date';
            foreach ($dateList as $date) {
                //FR comparaison ne fonctionne pas encore
                if ($this->start_date != 0 && $this->end_date != 0) {
                    if (compare_date($date['date'], $this->start_date) >= 0 && compare_date($date['date'], $this->end_date) <= 0) {
                        $this->recordList[$i][0] = $date['date'];
                        $totalJour ++;
                        $i ++;
                    }
                } else {
                    $this->recordList[$i][0] = $date['date'];
                    $totalJour ++;
                    $i ++;
                }
            }
        }
        // get user list
        $sqlUser = "SELECT DISTINCT `U`.`user_id`      AS `userId`,
                      `U`.`nom`          AS `lastname`,
                      `U`.`prenom`       AS `firstname`
					FROM `" . $tbl_user . "`           AS `U`,
						`" . $tbl_rel_course_user . "` AS `CU`,
						`" . $toolTables['clpres_attendance'] . "` AS `AU`
					WHERE `U`.`user_id` = `CU`.`user_id`
					AND   `CU`.`code_cours`= '" . claro_sql_escape(claro_get_current_course_id()) . "'
					AND `CU`.`isCourseManager`= 0 
					ORDER BY `U`.`user_id`";
        $userList = claro_sql_query_fetch_all($sqlUser);
        $cptUser = 1;
        //cpt for each att of one date
        $totalPresDate = array();
        //fill the colon 0 with name of user
        if (! empty($userList)) {
            $i = 1;
            //Fill the row 0 with name of user
            foreach ($userList as $user) {
                $this->recordList[0][$i] = $user['lastname'] . ' ' . $user['firstname'];
                $i ++;
            }
            //get attendance for each date
            foreach ($userList as $user) {
                for ($cptJour = 0; $cptJour < $totalJour; $cptJour ++) {
                    //Put 0 att if first time we see this date
                    if (! isset($totalPresDate[$cptJour + 1]))
                        $totalPresDate[$cptJour + 1] = 0;
                        //Check if user is att
                    $is_user_att = is_attendance($user['userId'], $this->recordList[$cptJour + 1][0]);
                    if ($is_user_att != - 1 && $is_user_att) {
                        $this->recordList[$cptJour + 1][$cptUser] = 'x';
                        $totalPresDate[$cptJour + 1] += 1;
                    } else {
                        $this->recordList[$cptJour + 1][$cptUser] = '';
                    }
                }
                $cptUser ++;
            }
        }
        //Put a last col with total of attendance
        $this->recordList[0][$cptUser] = get_lang('total attendance');
        for ($cptJour = 0; $cptJour < $totalJour; $cptJour ++) {
            $this->recordList[$cptJour + 1][$cptUser] = $totalPresDate[$cptJour + 1];
        }
        if (is_array($this->recordList) && ! empty($this->recordList))
            return true;
        return false;
    }
}
function export_attendance_list ()
{
    $aArgs = func_get_args();
    $cptArgs = count($aArgs);
    if ($cptArgs > 1) {
        $csvAttList = new csvAttList($aArgs[0], $aArgs[1], $aArgs[2]);
    } else {
        $csvAttList = new csvAttList($aArgs);
    }
    $csvAttList->buildRecords();
    $csvContent = $csvAttList->export();
    return $csvContent;
}
?>