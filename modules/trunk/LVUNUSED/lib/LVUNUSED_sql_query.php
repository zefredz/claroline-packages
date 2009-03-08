<?php
/**
 * CLAROLINE
 * This file contains all the sql requests needed for de module.
 *
 * @version 1.9 
 * @copyright 2001-2009 HE LEONARD DE VINCI
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @see http://www.claroline.net/wiki/index.php/ADMIN
 *
 *
 */

//define('DISP_RESULT',__LINE__);
//define('DISP_NOT_ALLOWED',__LINE__);
require_once dirname(__FILE__) . '/../../../claroline/inc/lib/admin.lib.inc.php';

/**
 * DB tables definition
 */

$tbl_mdb_names       = claro_sql_get_main_tbl();
$tbl_cdb_names       = claro_sql_get_course_tbl();
$tbl_course          = $tbl_mdb_names['course'];
$tbl_rel_course_user = $tbl_mdb_names['rel_course_user'];
$tbl_user            = $tbl_mdb_names['user'];
$tbl_track_e_login   = $tbl_mdb_names['tracking_event'];
$tbl_document        = $tbl_cdb_names['document'];
$toolNameList = claro_get_tool_name_list();



			/*$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$*/
			/*					FUNCTIONS					*/
			/*$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$*/


//________Courses without professor

			
function courseNoTeach(){
global $tbl_course;
global $tbl_rel_course_user;

					$sql = "
					SELECT  CONCAT(c.`code`)AS course,
                            c.`status`,c.`creationDate`,c.`expirationDate`
                      FROM  `" . $tbl_course . "` c
                 LEFT JOIN  `" . $tbl_rel_course_user . "` cu
                        ON  c.code = cu.code_cours
                       AND  cu.isCourseManager = 1
                  GROUP BY  c.code, isCourseManager,status,creationDate,expirationDate
                    HAVING  count( cu.user_id ) =0
                  ORDER BY  code_cours
                     LIMIT  100";

                    $data = claro_sql_query_fetch_all($sql);					
					return $data;
}
function courseNoUser(){

global $tbl_course;
global $tbl_rel_course_user;
                    $sql = "
                    	SELECT  CONCAT(c.code)AS course,                                                   
                            	count( cu.user_id ) AS qty,
                            	status,creationDate,
                            	expirationDate
                          FROM 	`" . $tbl_course . "` AS c
                  	 LEFT JOIN  `" . $tbl_rel_course_user . "` AS cu
                          	ON  c.code = cu.code_cours
                           AND  cu.isCourseManager = 0
                      GROUP BY  c.code,
                  				status,
                  				creationDate,
                  				expirationDate
                        HAVING  qty = 0
                      ORDER BY  code_cours
                         LIMIT  100";                  
                    $data = claro_sql_query_fetch_all($sql);
                    return $data;

}

function courseDisabled(){
global $tbl_course;
global $tbl_rel_course_user;
                    $sql = "
                    		SELECT 	`code` AS course,
                    				`status`,
                    				`creationDate`,
                    				`expirationDate`
                    		  FROM 	`" . $tbl_course . "` 
                    		  WHERE (`status` != 'enable')";           
                
                    $data = claro_sql_query_fetch_all($sql);
                    return $data;

}

function delUnusedCourse($timeLap){
global $tbl_course;
global $tbl_track_e_login;
global $tbl_document;
				$sql="
					SELECT 	`code`as course ,
					 		`creationDate` ,
					  		`expirationDate` ,
					   		`status`
					  FROM  `".$tbl_course."`";
				$data=claro_sql_query_fetch_all($sql);
				
				$result= array();				
				foreach( $data as $check )		
				{
					$checkvalue=$check['course'];
					$track=strtolower($checkvalue);
					$track='c_'.$track.'_tracking_event';
					$sql2="
							SELECT 	DATEDIFF( CURDATE( ) , MAX( `date` ) ) AS DIF
							  FROM 	`".$track."`
							 WHERE 	1 ";
					$dif=claro_sql_query_fetch_single_value($sql2);
						if($dif>$timeLap)
						{
						$check['Elaps']=$dif;
						$result[]=$check;
						}		
    			}  
				return $result;  		   
			}
function UnpublishedCourse(){
global $tbl_course;
				$sql="	SELECT 	`code` as course ,
				 				`creationDate` , 
				 				`expirationDate` , 
				 				`status`
						  FROM 	`".$tbl_course."`
						 WHERE 	CURDATE( )>`expirationDate` OR DATEDIFF( CURDATE( ) ,
						 		`creationDate` )<0;";
				$data=claro_sql_query_fetch_all($sql);
				return $data;

}
function NoAdminEmail(){
global $tbl_user;
global $tbl_rel_course_user;
global $tbl_course;
			$sql="	
				SELECT 	user.`user_id`, 
						cuser.`code_cours` as course ,
						cours.`expirationDate`,
						cours.`creationDate`,
						cours.`status` 
				  FROM 	`".$tbl_user."` as user
				  JOIN  `".$tbl_rel_course_user."` as cuser 
					ON 	user.`user_id`=cuser.`user_id`
				  JOIN	`".$tbl_course."` as cours 
					ON 	cours.`code`=cuser.`code_cours`
				 WHERE	`isCourseManager`='1' AND user.`email`=''
					";
			$data=claro_sql_query_fetch_all($sql);
			return $data;
}
			                 
function UpdateDisabled($code,$change){
global $tbl_course;
global $tbl_user;
				$sql="
					UPDATE 	`".$tbl_course."` 
					   SET 	`status` = '".$change."' 
					 WHERE 	`".$tbl_course."`.`code` ='".$code."'";
				$result = claro_sql_query($sql);
				return $result;

}
function UpdateDatePublish($code,$change){
global $tbl_course;
				$sql="
					UPDATE 	`".$tbl_course."` 
					   SET 	`creationDate` ='".$change."' 
					 WHERE 	`".$tbl_course."`.`code` ='".$code."'";
				$result = claro_sql_query($sql);
				return $result;
}
function SetUnpublish($code,$change){
global $tbl_course;
				$sql="
					UPDATE 	`".$tbl_course."` 
					   SET 	`expirationDate` = '".$change."' 
					 WHERE 	`".$tbl_course."`.`code` ='".$code."'";
				$result = claro_sql_query($sql);
				return $result;
}
function getIdForMail($code){
				
global $tbl_course;
global $tbl_rel_course_user;
global $tbl_user;
                    $sql ="
                    		SELECT 	user.`user_id` as id,user.
                    				`email` as mail ,
                    				user.`nom`,
                    				user.`prenom` 
                    		  FROM 	`".$tbl_user."` as user
							  JOIN  `".$tbl_rel_course_user."` as cuser 
								ON  user.`user_id`=cuser.`user_id`
							 WHERE 	`isCourseManager`='1' AND cuser.`code_cours`='".$code."'";
					 
					$data = claro_sql_query_fetch_all($sql);
                    return $data;
}

/*None sql query fonctions*/
function deleteCourse($code){
				$delete=delete_course($code);
				return $delete;
}

function interArray($table1,$table2,$critere){

			$result= array();
			foreach( $table1 as $check )		
			{
			
			foreach($table2 as $dtest)
			{
    			if($dtest[$critere] == $check[$critere])
    			{
    			$result[]=$dtest;      			
    			}    		   
			}
			}
			return $result;

}


?>