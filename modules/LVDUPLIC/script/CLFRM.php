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
 *  $__TOOL_LABEL__, $__SOURCE_COURSE_DATA__, $__TARGET_COURSE_DATA__;
 *  
 *  the courses data are the same as the data returned by the function claro_get_course_data() in claro_main.lib.php
 *  
 */

//According to add_course.lib.inc.php line 1043, the group forum category is always 1
$groupCategoryId = 1;
$prefixSource = $__SOURCE_COURSE_DATA__['dbNameGlu'];
$prefixTarget = $__TARGET_COURSE_DATA__['dbNameGlu'];
$sourceTableList = claro_sql_get_course_tbl($prefixSource);
$targetTableList = claro_sql_get_course_tbl($prefixTarget);

//TODO handdle transactions
$sqlDropCat = "
			DROP TABLE IF EXISTS `" . $targetTableList['bb_categories'] . "`; ";
$sqlCreateCat = "
            CREATE TABLE `" . $targetTableList['bb_categories'] . "` 
                    LIKE `" . $sourceTableList['bb_categories'] . "` ; ";
// WARNING : EXCEPTIONAL SELECT *
$sqlInsertCat = "
            INSERT INTO `" . $targetTableList['bb_categories']  . "` 
                 SELECT *
                   FROM `" .$sourceTableList['bb_categories'] . "` 
                  WHERE `cat_id` != ".$groupCategoryId." ; ";


$sqlDropForum = "
			DROP TABLE IF EXISTS `" . $targetTableList['bb_forums'] . "; ";
$sqlCreateForum = "
            CREATE TABLE `" . $targetTableList['bb_forums'] . "` 
                    LIKE `" . $sourceTableList['bb_forums'] . "` ; ";
// WARNING : EXCEPTIONAL SELECT *
$sqlInsertForum = "
            INSERT INTO `" . $targetTableList['bb_forums']  . "` 
                 SELECT * 
                   FROM `" .$sourceTableList['bb_forums'] . "` 
                  WHERE `cat_id` != ".$groupCategoryId." ; ";

$sqlUpdatePostsData = "	UPDATE `" . $targetTableList['bb_forums'] . "` AS F	
							SET F.forum_posts = (	
									SELECT COUNT(*) 
						  			FROM 		`" . $targetTableList['bb_posts'] . "` AS P1
						  			WHERE  P1.`forum_id` = F.`forum_id`
						  						), 
						  	 F.forum_topics = (	
									SELECT COUNT(*) 
						  			FROM 		`" . $targetTableList['bb_topics'] . "` AS T2
						  			WHERE  T2.`forum_id` = F.`forum_id`
						  						), 						  	
						  	 F.forum_last_post_id = (
						  			SELECT IFNULL(`post_id`,0)  
						  			FROM `".$targetTableList['bb_posts']."` AS P3 
						  			WHERE P3.`forum_id` = F.`forum_id` 
						  			ORDER BY P3.`post_time`  DESC 
						  			LIMIT 0 , 1 
						  								)
						  	; ";

       
claro_sql_query($sqlDropCat);            
claro_sql_query($sqlCreateCat);            
claro_sql_query($sqlInsertCat);

claro_sql_query($sqlDropForum);            
claro_sql_query($sqlCreateForum);            
claro_sql_query($sqlInsertForum);
claro_sql_query($sqlUpdatePostsData);

?>
