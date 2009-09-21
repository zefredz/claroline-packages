<?php
/**
 *
 * @version 1.0.0
 *
 * @copyright (c) 2001-2009 Haute Ecole L�onard de Vinci
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package LVDUPLIC
 *
 * @author Philippe Dekimpe <dkp@ecam.be>
 *
 */

require_once get_path ( 'incRepositorySys' ) . '/lib/admin.lib.inc.php';
require_once get_path ( 'incRepositorySys' ) . '/lib/course.lib.inc.php';
require_once get_path ( 'incRepositorySys' ) . '/lib/form.lib.php';
require_once get_path ( 'incRepositorySys' ) . '/lib/user.lib.php';
require_once get_path ( 'incRepositorySys' ) . '/lib/add_course.lib.inc.php';
require_once get_path ( 'incRepositorySys' ) . '/lib/sendmail.lib.php';
require_once get_path('incRepositorySys') . '/lib/claroCourse.class.php';

require_once 'DUPUtils.class.php';
require_once 'DUPToolManager.class.php';
require_once 'DUPSessionMgr.class.php';

class DUPConstants{
	public static $DUP_STEP_DEFINE_SOURCE = __LINE__;
	public static $DUP_STEP_DEFINE_TARGET = __LINE__;
	public static $DUP_STEP_CHOOSE_TOOLS = __LINE__;
	public static $DUP_STEP_COPY_CONTENTS = __LINE__;
}

//FUNCTIONS

/** delete all the course managers from target course then copy the ones from the source course 
 *  as managers of the target course
 */
function copy_course_managers($source_course_data, $target_course_data)
{
	$claro_main_tables = claro_sql_get_main_tbl();
	$rel_cours_user = $claro_main_tables['rel_course_user'];
	
	//TODO manage transaction / use REPLACE keyword (not standard)
	$sqlDelete = 	"
						DELETE FROM `" . $rel_cours_user . "`  
						WHERE 	`code_cours` LIKE '" . $target_course_data['sysCode'] . "' 
						AND 	isCourseManager = 1;";
	$sqlInsert = 	"
						INSERT INTO `" . $rel_cours_user . "` 
						( `code_cours` , `user_id` , `profile_id` , `isCourseManager` , `role` , 
						  `team` , `tutor` , `count_user_enrol` , `count_class_enrol` )
						SELECT 	'". $target_course_data['sysCode'] ."' , `user_id` , `profile_id` , `isCourseManager` , `role` , 
						  		`team` , `tutor` , `count_user_enrol` , `count_class_enrol` 
						FROM  `" . $rel_cours_user . "` 
						WHERE 	`code_cours` LIKE '" . $source_course_data['sysCode'] . "' 
						AND 	isCourseManager = 1;";
						  
	claro_sql_query($sqlDelete);
	claro_sql_query($sqlInsert);
}

function copy_tool($tool_label, $sourceCID, $targetCID)
{
	$scriptFile = "script/".$tool_label.".php";
	$configFile = "conf/".$tool_label.".xml";
	if(is_file($scriptFile))
	{
		$__TOOL_LABEL__ = $tool_label;
		$__SOURCE_COURSE_DATA__  = claro_get_course_data($sourceCID);
		$__TARGET_COURSE_DATA__ = claro_get_course_data($targetCID);
		
				
		include($scriptFile);
	} 
	elseif(is_file($configFile)) 
	{
		$tool_manager = new DUPToolManager($tool_label, $configFile);
   		$tool_manager->copyTool($sourceCID,$targetCID );
	}
}

?>
