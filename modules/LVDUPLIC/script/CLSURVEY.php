<?php 
/**
 * This script is in charge for duplicating the Forums:
 * 
 * Only the general forums and categories must be copied, 
 * 
 * We do not want to copy :
 * 
 * 	- The topics and posts
 * 	- The "group forums" category
 *  - The Groups Forums
 *  
 *  We can acces those data in this file : 
 *  
 *  global $__TOOL_LABEL__, $__SOURCE_COURSE_DATA__, $__TARGET_COURSE_DATA__;
 *  
 *  the courses data are the same as the data returned by the function claro_get_course_data() in claro_main.lib.php
 *  
 */

//require_once get_path ( 'incRepositorySys' ) . '/lib/claro_main.lib.php';

//According to add_course.lib.inc.php line 1043, the group forum category is always 1
$groupCategoryId = 1;
$targetCourse = $__TARGET_COURSE_DATA__['sysCode'];
$sourceCourse = $__SOURCE_COURSE_DATA__['sysCode'];

$tbl = claro_sql_get_tbl(array( 'survey_question'
                                  , 'survey_question_list'
                                  , 'survey_list'), $context);
//TODO handdle transactions

// WARNING : EXCEPTIONAL SELECT *
$sql = " SELECT *
                   FROM `" . $tbl['survey_list'] . "` 
                   WHERE `cid` = '". $sourceCourse ."' ; ";

$listSurvey  = claro_sql_query_fetch_all($sql);

foreach($listSurvey as $survey)
{
        $sql = "INSERT INTO `" . $tbl['survey_list'] . "`
	           SET `title`        = '" . $survey['title'] . "',
                   `description`  = '" . $survey['description'] . "',
                   `cid`          = '" . $targetCourse . "',
                   `date_created` = '" . date('Y-m-d') . "'";
        $surveyId = claro_sql_query_insert_id($sql);

        DUPLogger::log_copy_table('CLSURVEY',$sourceCourse,$targetCourse,
    	    claro_get_current_user_data("firstName") . " " . claro_get_current_user_data("lastName") ,
    	    $tbl['survey_list'],$tbl['survey_list']);
        
        
        // Copy questions of this course
        $sql =" SELECT *
                   FROM `" . $tbl['survey_question_list'] . "` 
                   WHERE `cid` = '". $sourceCourse ."' ; ";
        
        $listQuestion  = claro_sql_query_fetch_all($sql);
        
        foreach($listQuestion as $question)
         {
        
            $sql = "INSERT INTO `" . $tbl['survey_question_list'] . "`
	        SET `title`       = '" . $question['title'] . "'
	        ,   `description` = '" . $question['description'] . "'
	        ,   `option`      = '" . $question['option'] . "'
	        ,   `type`        = '" . $question['type'] . "'
	        ,   `cid`         = '" . $targetCourse . "'";
            
            $questionId=claro_sql_query_insert_id($sql);
            
            DUPLogger::log_copy_table('CLSURVEY',$sourceCourse,$targetCourse,
            	claro_get_current_user_data("firstName") . " " . claro_get_current_user_data("lastName") ,
            	$tbl['survey_question_list'],$tbl['survey_question_list']);
            
             // copy relation between survey and question 
             $sql = " SELECT *
                       FROM `" . $tbl['survey_question'] . "` 
                       WHERE `id_question` = '". $question['id_question'] ."' ; ";
    
             $surveyQuestion  = claro_sql_query_fetch_all($sql);
             
             foreach($surveyQuestion as $item)
             {
                     $sql = "INSERT INTO `" . $tbl['survey_question']."`
            		        SET `id_question` = " . (int) $questionId . "
            		        ,   `id_survey`   = " . (int) $surveyId;
                     
                     claro_sql_query_insert_id($sql);      

                    DUPLogger::log_copy_table('CLSURVEY',$sourceCourse,$targetCourse,
                	    claro_get_current_user_data("firstName") . " " . claro_get_current_user_data("lastName") ,
                	    $tbl['survey_question'],$tbl['survey_question']);
             }
         }

}

?>
