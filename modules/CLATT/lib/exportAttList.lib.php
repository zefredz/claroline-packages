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
    private  $course_id;
    private  $exId;
	private  $start_date;
	private  $end_date;
    
    function csvAttList( )
	{
		parent::csv(); // call constructor of parent class
		
		$aArgs = func_get_args ();
		$cptArgs = count ($aArgs);

		$this->course_id = $aArgs[0];
		$this->start_date = $aArgs[1];
		$this->end_date = $aArgs[2];
		$this->idList = $aArgs[3];
	}
    
    function buildRecords()
    {
        $tbl_mdb_names = claro_sql_get_main_tbl();
        $tbl_user = $tbl_mdb_names['user'];
        $toolTables = get_module_course_tbl( array( 'attendance','attendance_session' ), claro_get_current_course_id() );
		
        $sql = "SELECT 	s.`id`, s.`date_att`, s.`title`, 
        				u.`user_id`, u.`prenom`, u.`nom`,  
        				a.`attendance`, a.`comment`
                FROM `" . $tbl_user . "` u
                INNER JOIN `" . $toolTables['attendance'] . "` a ON u.`user_id` = a.`user_id`
                INNER JOIN `" . $toolTables['attendance_session'] . "` s ON s.`id` = a.`id_list` ";
        
        if ($this->start_date!=0 && $this->start_date!=0) 
            $sql .= "WHERE s.date_att >'" . $this->start_date 
                    . "' AND s.date_att <'" . $this->end_date ."' ";
                    
        if ($this->idList > 0)
            $sql .= "WHERE s.id = " . $this->idList . " ";

        $sql .= "ORDER BY s.`id`, u.`user_id` ;";
            
        $attList = Claroline::getDatabase()->query($sql);
            
        if(!empty($attList) )
		{
			$this->recordList[0][0] = 'idList';
			$this->recordList[0][1] = 'date';
			$this->recordList[0][2] = 'title';
		    $this->recordList[0][3] = 'idUser';
            $this->recordList[0][4] = 'lastname';
            $this->recordList[0][5] = 'firstname';
            $this->recordList[0][6] = 'attendance';
            $this->recordList[0][7] = 'comment';
		    
		    $i = 1;
			//Fill the row 0 with name of user
			foreach( $attList as $user )
			{
				$i++;  
			    $this->recordList[$i][0] = $user['id'];        
			    $this->recordList[$i][1] = $user['date_att'];
                $this->recordList[$i][2] = $user['title'];
                $this->recordList[$i][3] = $user['user_id'] ;
			    $this->recordList[$i][4] = $user['nom'] ;
				$this->recordList[$i][5] = $user['prenom'] ;
                $this->recordList[$i][6] = $user['attendance'];
                $this->recordList[$i][7] = $user['comment'];
				$i++;
			}
		}
        
		if( is_array($this->recordList) && !empty($this->recordList) ) return true;
        
        return false;
    }
}

function export_attendance_list($courseId,$start,$end,$listId)
{

    $csvAttList = new csvAttList($courseId,$start,$end,$listId);
    $csvAttList->buildRecords();
    $csvAttList->separator = ';';
    $csvContent = $csvAttList->export();
    
    return $csvContent;
}
?>