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

require_once dirname(__FILE__).'/../../../claroline/inc/claro_init_global.inc.php';
require_once get_path ( 'incRepositorySys' ) . '/lib/admin.lib.inc.php';
require_once get_path ( 'incRepositorySys' ) . '/lib/course.lib.inc.php';
require_once get_path ( 'incRepositorySys' ) . '/lib/form.lib.php';
require_once get_path ( 'incRepositorySys' ) . '/lib/user.lib.php';
require_once get_path ( 'incRepositorySys' ) . '/lib/add_course.lib.inc.php';
require_once get_path ( 'incRepositorySys' ) . '/lib/sendmail.lib.php';
require_once get_path('incRepositorySys') . '/lib/claroCourse.class.php';

require_once dirname(__FILE__).'/DUPUtils.class.php';
require_once dirname(__FILE__).'/DUPLogger.class.php';
require_once dirname(__FILE__).'/DUPToolManager.class.php';
require_once dirname(__FILE__).'/DUPSessionMgr.class.php';

class DUPConstants{
	public static $DUP_STEP_CHOOSE_SOURCE 	= __LINE__;
	public static $DUP_STEP_DEFINE_TARGET 	= __LINE__;
	public static $DUP_STEP_CHOOSE_TOOLS 	= __LINE__;
	public static $DUP_STEP_COPY_CONTENTS 	= __LINE__;
}

//FUNCTIONS

//copy the table containing 
function copy_intro($sourceCourseData, $targetCourseData)
{
	$prefixSource = $sourceCourseData['dbNameGlu'];
    $prefixTarget = $targetCourseData['dbNameGlu'];
    
    $sourceTableList = claro_sql_get_course_tbl($prefixSource);
    $targetTableList = claro_sql_get_course_tbl($prefixTarget);
    
    $sourceTableName = $sourceTableList['tool_intro'];
    $targetTableName = $targetTableList['tool_intro'];
            
    //TODO handdle transactions
    $sqlDrop = "
    	DROP TABLE IF EXISTS `" . $targetTableName . "; ";
    //create like = copy structure with constraints (except fk)
    $sqlCreate = "
        CREATE TABLE `" . $targetTableName . "` LIKE `" . $sourceTableName . "`; ";
    $sqlInsert = "
    	INSERT INTO `" . $targetTableName . "` SELECT * FROM `" . $sourceTableName . "`; ";
    
    DUPLogger::log_copy_table('course',$sourceCourseData['sysCode'],$targetCourseData['sysCode'],
    	claro_get_current_user_data("firstName") . " " . claro_get_current_user_data("lastName") ,
    	$sourceTableName,$targetTableName);
    	
    //CREATE TABLE ... LIKE ... (INLCUDING DEFAULTS )
    claro_sql_query($sqlDrop);
    claro_sql_query($sqlCreate);
    claro_sql_query($sqlInsert);
            
        
	
	
}

/** delete all the course managers from target course then copy the ones from the source course 
 *  as managers of the target course
 */
function copy_course_managers( $sourceCourseData, $targetCourseData )
{
	$claroMainTableList = claro_sql_get_main_tbl();
	$relCoursUserTable = $claroMainTableList['rel_course_user'];
	
	//TODO manage transaction / use REPLACE keyword (not standard)
	$sqlDelete = 	"
						DELETE FROM `" . $relCoursUserTable . "`  
						      WHERE `code_cours` LIKE '" . $targetCourseData['sysCode'] . "' 
						        AND `isCourseManager` = 1; ";
	$sqlInsert = 	"
						INSERT INTO `" . $relCoursUserTable . "` ( 
									`code_cours`, 
									`user_id`, 
									`profile_id`, 
									`isCourseManager`, 
									`role`, 
						  			`team`, 
						  			`tutor`, 
						  			`count_user_enrol`, 
						  			`count_class_enrol` )
						     SELECT '". $targetCourseData['sysCode'] ."', 
						     		`user_id`, 
						     		`profile_id`, 
						     		`isCourseManager`, 
						     		`role`, 
						  			`team`, 
						  			`tutor`, 
						  			`count_user_enrol`, 
						  			`count_class_enrol` 
							   FROM `" . $relCoursUserTable . "` 
							  WHERE	`code_cours` LIKE '" . $sourceCourseData['sysCode'] . "' 
						        AND	`isCourseManager` = 1;";
						  
	
	DUPLogger::log_copy_row("COURSE",$sourceCourseData['sysCode'],$targetCourseData['sysCode'],
            	claro_get_current_user_data("firstName") . " " . claro_get_current_user_data("lastName") ,
            	$relCoursUserTable,$relCoursUserTable);
	
	claro_sql_query($sqlDelete);	
	claro_sql_query($sqlInsert);	
	
}

function copy_tool( $tool_label, $sourceCID, $targetCID )
{
	$scriptFile = dirname(__FILE__)."/../script/".$tool_label.".php";
	$configFile = dirname(__FILE__)."/../conf/".$tool_label.".xml";
	
	if(is_file( $scriptFile ) )
	{
		$__TOOL_LABEL__ 		= $tool_label;
		$__SOURCE_COURSE_DATA__ = claro_get_course_data($sourceCID);
		$__TARGET_COURSE_DATA__ = claro_get_course_data($targetCID);
				
		include($scriptFile);
	} 
	elseif( is_file( $configFile ) ) 
	{
		$tool_manager = new DUPToolManager( $tool_label, $configFile );
   		$tool_manager->copyTool( $sourceCID,$targetCID );
	}
}

?>
