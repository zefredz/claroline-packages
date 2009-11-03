<?php

/**
 * Get users list of the current course
 * @return array list of users
 * @author Philippe Dekimpe
 */
function get_evaluation_course_user_list()
{
    
     // tool global variables
    $tbl_mdb_names      = claro_sql_get_main_tbl();
    $tbl_user           = $tbl_mdb_names['user'];
    $tbl_course_user    = $tbl_mdb_names['rel_course_user'];

    $sql = "SELECT DISTINCT `$tbl_user`.`user_id`, `$tbl_user`.`nom`, `$tbl_user`.`prenom`, `$tbl_user`.`username`,
					  `$tbl_course_user`.`isCourseManager`
				FROM `$tbl_user`, `$tbl_course_user`
				WHERE `$tbl_user`.`user_id`=`$tbl_course_user`.`user_id`
				AND `$tbl_course_user`.`code_cours`='".claro_get_current_course_id()."'
				AND `$tbl_course_user`.`isCourseManager`='0'
				ORDER BY
					UPPER(`$tbl_user`.`username`)";
    return claro_sql_query_fetch_all($sql);
}
/**
 * Get list of evaluation from a course
 *
 * @param 
 * @return array list of evaluation : evaluation_id, titre, maximum, ponderation
 */
function get_evaluation_course_list()
{
    $toolTables = get_module_course_tbl( array( 'results_evaluations', 'results_entries' ), claro_get_current_course_id() );
    
    $sql = "SELECT `evaluation_id`, `titre`, `maximum`, `ponderation` 
                                    FROM `".$toolTables['results_evaluations']."` 
                                    ORDER BY evaluation_id";
    return claro_sql_query_fetch_all($sql);
}

/**
 * Get list of evaluation from one user
 *
 * @param int $thisUser
 * @return array list of evaluation : note, evaluation_id, maximum, ponderation
 */
function get_evaluation_student_note($thisUser = '')
{
    $toolTables = get_module_course_tbl( array( 'results_evaluations', 'results_entries' ), claro_get_current_course_id() );
    
    $sql = "SELECT DISTINCT result.note, result.evaluation_id, eval.maximum, eval.ponderation 
    				    FROM  `$toolTables[results_entries]` result ,  `$toolTables[results_evaluations]` eval 
    				    WHERE ";
    if ($thisUser != '') 
    {
      $sql .= "result.user_id='".$thisUser."' AND ";
    }
    
    $sql .= "result.evaluation_id = eval.evaluation_id 
    				    ORDER BY result.user_id, result.evaluation_id"; 
                
	return claro_sql_query_fetch_all($sql);
}

function get_evaluation_average()
{
    	//Compter la moyenne de chacun des travaux du cours. 
        //Compter dans la moyenne seulement les étudiants INSCRITS au cours (les étudiants peuvent être désinscrits en cours de session et avoir une note) 
        //et seulement les travaux dont la note a été entrée (différente de '');
    $toolTables = get_module_course_tbl( array( 'results_evaluations', 'results_entries' ), claro_get_current_course_id() );
    
    // tool global variables
    $tbl_mdb_names      = claro_sql_get_main_tbl();
   $tbl_course_user    = $tbl_mdb_names['rel_course_user'];
     	
    $sql = "SELECT `result`.`evaluation_id`, `eval`.`ponderation`, `eval`.`maximum`, avg(`result`.`note`) AS moyenne
    			FROM `$toolTables[results_entries]` result ,  
    				 `$toolTables[results_evaluations]` eval, 
    				 `$tbl_course_user`
    		    WHERE result.evaluation_id = `eval`.`evaluation_id` 
    			AND   `result`.`user_id`= `$tbl_course_user`.`user_id`
    			AND   `result`.`note` <> ''
    			AND `$tbl_course_user`.`code_cours`='".claro_get_current_course_id()."'
    			AND `$tbl_course_user`.`isCourseManager`='0'
    			GROUP BY `result`.`evaluation_id`
    			ORDER BY `eval`.`evaluation_id`";
    		return claro_sql_query_fetch_all($sql);
    
}


function del_evaluation($idEvalToDel)
{
    $toolTables = get_module_course_tbl( array( 'results_evaluations', 'results_entries' ), claro_get_current_course_id() );
    
	 claro_sql_query("DELETE FROM `".$toolTables['results_evaluations']."` 
								WHERE evaluation_id ='".$idEvalToDel."'");
	return claro_sql_query("DELETE FROM `".$toolTables['results_entries']."` 
								WHERE evaluation_id ='".$idEvalToDel."'"); 

}


function insert_evaluation()
{
    $toolTables = get_module_course_tbl( array( 'results_evaluations', 'results_entries' ), claro_get_current_course_id() );
     
    $sql = "INSERT INTO `".$toolTables['results_evaluations']."` 
    			SET titre = '-', maximum = '0', ponderation='0'";
	
    return claro_sql_query($sql);	   
}

function set_evaluation($titre,$maxEval,$ponderation,$idEvaluation)
{
    $toolTables = get_module_course_tbl( array( 'results_evaluations', 'results_entries' ), claro_get_current_course_id() );
    
    $sql = "UPDATE `". $toolTables['results_evaluations'] ."` 
    		SET `titre` = '". $titre ."', `maximum` = '". $maxEval ."', `ponderation`='". $ponderation ."' 
    		WHERE `evaluation_id` = '". $idEvaluation. "'" ;
	claro_sql_query($sql);
    
}

function set_evaluation_note($userId,$evaluation_id,$note_etudiant)
{
      $toolTables = get_module_course_tbl( array( 'results_entries' ), claro_get_current_course_id() );
    
    					$sql = "INSERT INTO `$toolTables[results_entries]` (user_id,evaluation_id,note) 
    					        VALUES ('".$userId."',".$evaluation_id.",'".addslashes(trim($note_etudiant))."') 
    							ON DUPLICATE KEY UPDATE note='".addslashes(trim($note_etudiant))."'";
					    return claro_sql_query($sql);
}
?>