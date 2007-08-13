<?php // $Id$
/**
 * CLAROLINE
 *
 * @version 0.1 $Revision$
 *
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLLP
 *
 * @author Sebastien Piraux
 *
 */

$tlabelReq = 'CLLP';

require_once dirname( __FILE__ ) . '/../../../claroline/inc/claro_init_global.inc.php';

/*
 * init request vars
 */
if( isset($_REQUEST['pathId']) && is_numeric($_REQUEST['pathId']) )   $pathId = (int) $_REQUEST['pathId'];
else                                                                  $pathId = null;

if( isset($_REQUEST['itemId']) && is_numeric($_REQUEST['itemId']) )   $itemId = (int) $_REQUEST['itemId'];
else                                                                  $itemid = null;


/*
if(claro_is_user_authenticated())
{
    // Get general information to generate the right API inmplementation
    $sql = "SELECT *
              FROM `".$TABLEUSERMODULEPROGRESS."` AS UMP,
                   `".$TABLELEARNPATHMODULE."` AS LPM,
                   `".$TABLEUSERS."` AS U,
                   `".$TABLEMODULE."` AS M
             WHERE UMP.`user_id` = ". (int)claro_get_current_user_id()."
               AND UMP.`user_id` = U.`user_id`
               AND UMP.`learnPath_module_id` = LPM.`learnPath_module_id`
               AND M.`module_id` = LPM.`module_id`
               AND LPM.`learnPath_id` = ". (int)$_SESSION['path_id']."
               AND LPM.`module_id` = ". (int)$_SESSION['module_id'];

    $userProgressionDetails = claro_sql_query_get_single_row($sql);
}

if( ! claro_is_user_authenticated() || !$userProgressionDetails )
{

    $sco['student_id'] = "-1";
    $sco['student_name'] = "Anonymous, User";
    $sco['lesson_location'] = "";
    $sco['credit'] ="no-credit";
    $sco['lesson_status'] = "not attempted";
    $sco['entry'] = "ab-initio";
    $sco['raw'] = "";
    $sco['scoreMin'] = "0";
    $sco['scoreMax'] = "100";
    $sco['total_time'] = "0000:00:00.00";
    $sco['suspend_data'] = "";
    $sco['launch_data'] = "";

}
else // authenticated user and no error in query
{
    // set vars
    $sco['student_id'] = claro_get_current_user_id();
    $sco['student_name'] = $userProgressionDetails['nom'].", ".$userProgressionDetails['prenom'];
    $sco['lesson_location'] = $userProgressionDetails['lesson_location'];
    $sco['credit'] = strtolower($userProgressionDetails['credit']);
    $sco['lesson_status'] = strtolower($userProgressionDetails['lesson_status']);
    $sco['entry'] = strtolower($userProgressionDetails['entry']);
    $sco['raw'] = ($userProgressionDetails['raw'] == -1) ? "" : "".$userProgressionDetails['raw'];
    $sco['scoreMin'] = ($userProgressionDetails['scoreMin'] == -1) ? "" : "".$userProgressionDetails['scoreMin'];
    $sco['scoreMax'] = ($userProgressionDetails['scoreMax'] == -1) ? "" : "".$userProgressionDetails['scoreMax'];
    $sco['total_time'] = $userProgressionDetails['total_time'];
    $sco['suspend_data'] = $userProgressionDetails['suspend_data'];
    $sco['launch_data'] = stripslashes($userProgressionDetails['launch_data']);
}


//common vars
$sco['_children'] = "student_id,student_name,lesson_location,credit,lesson_status,entry,score,total_time,exit,session_time";
$sco['score_children'] = "raw,min,max";
$sco['exit'] = "";
$sco['session_time'] = "0000:00:00.00";
*/

header( 'Content-Type: text/javascript' );
?>

lpHandler.APIInitialized = false;
lpHandler.APILastError = "301";

lpHandler.itemId = "<?php echo $itemId; ?>";

// ====================================================
// CMI Elements and Values
//

lpHandler.elementList = {};
lpHandler.elementList['cmi._version'] = '1.0';
lpHandler.elementList['cmi.comments_from_learner._children']  = "comment,location,timestamp";
lpHandler.elementList['cmi.comments_from_learner._count']  = "";
lpHandler.elementList['cmi.comments_from_learner'] = {}; /* TODO handle collections */
lpHandler.elementList['cmi.comments_from_lms._children']  = "comment,location,timestamp";
lpHandler.elementList['cmi.comments_from_lms._count']  = "";
lpHandler.elementList['cmi.comments_from_lms'] = {}; /* TODO handle collections */
lpHandler.elementList['cmi.completion_status']  = "UNKNOWN"; /* progress measure == 0 -> not attempted, 1 -> completed, 0 < < 1 -> incomplete but depends on complete threshold */
lpHandler.elementList['cmi.entry']  = "";
lpHandler.elementList['cmi.exit']  = "";
lpHandler.elementList['cmi.launch_data']  = "";
lpHandler.elementList['cmi.learner_id']  = "";
lpHandler.elementList['cmi.learner_name'] = "";
lpHandler.elementList['cmi.location'] = "";
lpHandler.elementList['cmi.max_time_allowed'] = ""; /* TODO get that from manifest ! */
lpHandler.elementList['cmi.mode'] = "";   /* if mode is browse or review credit is always no-credit, if mode == "normal" credit my be credit or no-credit */
lpHandler.elementList['cmi.credit']  = "no-credit";
lpHandler.elementList['cmi.progress_measure'] = ""; /* not attempted, completed, incomplete, depends on completionThreshold */
lpHandler.elementList['cmi.scaled_passing_score'] = "";
lpHandler.elementList['cmi.score._children'] = "scaled,min,max,raw";
lpHandler.elementList['cmi.score.scaled'] = "";
lpHandler.elementList['cmi.score.min'] = "";
lpHandler.elementList['cmi.score.max'] = "";
lpHandler.elementList['cmi.score.raw'] = "";
lpHandler.elementList['cmi.session_time'] = ""; /* check value ? find a way to store it ? probably use php to convert and serve a correct string */
lpHandler.elementList['cmi.success_status'] = ""; /* passed failed or unknown */
lpHandler.elementList['cmi.suspend_data'] = "";
lpHandler.elementList['cmi.time_limit_action'] = ""; /* exit,message/exit,no message/continue,message/continue,no message // should be initialized to continue,no message if nothing found in manifest */
lpHandler.elementList['cmi.total_time'] = "";


// todo
lpHandler.elementList['cmi.completion_threshold']  = "";
lpHandler.elementList['cmi.interactions']  = {};
lpHandler.elementList['cmi.learner_preference'] = "";
lpHandler.elementList['cmi.objectives'] = {};
