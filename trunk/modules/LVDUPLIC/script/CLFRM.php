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

//According to add_course.lib.inc.php line 1043, the group forum category is always 1
$groupCategoryId = 1;
$prefix_source = $__SOURCE_COURSE_DATA__['dbNameGlu'];
$prefix_target = $__TARGET_COURSE_DATA__['dbNameGlu'];
$source_tbl_list = claro_sql_get_course_tbl($prefix_source);
$target_tbl_list = claro_sql_get_course_tbl($prefix_target);

//TODO handdle transactions
$sqlDropCat = "
			DROP TABLE IF EXISTS `" . $target_tbl_list['bb_categories'] . "`; ";
$sqlCreateCat = "
            CREATE TABLE `" . $target_tbl_list['bb_categories'] . "` LIKE `" . $source_tbl_list['bb_categories'] . "` ; ";
$sqlInsertCat = "
            INSERT INTO `" . $target_tbl_list['bb_categories']  . "` 
            SELECT * FROM `" .$source_tbl_list['bb_categories'] . "` 
            WHERE `cat_id` != ".$groupCategoryId." ; ";


$sqlDropForum = "
			DROP TABLE IF EXISTS `" . $target_tbl_list['bb_forums'] . "; ";
$sqlCreateForum = "
            CREATE TABLE `" . $target_tbl_list['bb_forums'] . "` LIKE `" . $source_tbl_list['bb_forums'] . "` ; ";
$sqlInsertForum = "
            INSERT INTO `" . $target_tbl_list['bb_forums']  . "` 
            SELECT * FROM `" .$source_tbl_list['bb_forums'] . "` 
            WHERE `cat_id` != ".$groupCategoryId." ; ";


       
claro_sql_query($sqlDropCat);            
claro_sql_query($sqlCreateCat);            
claro_sql_query($sqlInsertCat);

claro_sql_query($sqlDropForum);            
claro_sql_query($sqlCreateForum);            
claro_sql_query($sqlInsertForum);


?>
