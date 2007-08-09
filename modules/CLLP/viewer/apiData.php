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

lpClient.APIInitialized = false;
lpClient.APILastError = "301";

lpClient.itemId = "<?php echo $itemId; ?>";

// ====================================================
// CMI Elements and Values
//

lpClient.elementList = {};
lpClient.elementList['cmi._version'] = '1.0';
lpClient.elementList['cmi.comments_from_learner._children']  = "comment,location,timestamp";
lpClient.elementList['cmi.comments_from_learner._count']  = "";
lpClient.elementList['cmi.comments_from_learner'] = {}; /* TODO handle collections */
lpClient.elementList['cmi.comments_from_lms._children']  = "comment,location,timestamp";
lpClient.elementList['cmi.comments_from_lms._count']  = "";
lpClient.elementList['cmi.comments_from_lms'] = {}; /* TODO handle collections */
lpClient.elementList['cmi.completion_status']  = "UNKNOWN"; /* progress measure == 0 -> not attempted, 1 -> completed, 0 < < 1 -> incomplete but depends on complete threshold */
lpClient.elementList['cmi.entry']  = "";
lpClient.elementList['cmi.exit']  = "";
lpClient.elementList['cmi.launch_data']  = "";
lpClient.elementList['cmi.learner_id']  = "";
lpClient.elementList['cmi.learner_name'] = "";
lpClient.elementList['cmi.location'] = "";
lpClient.elementList['cmi.max_time_allowed'] = ""; /* TODO get that from manifest ! */
lpClient.elementList['cmi.mode'] = "";   /* if mode is browse or review credit is always no-credit, if mode == "normal" credit my be credit or no-credit */
lpClient.elementList['cmi.credit']  = "no-credit";
lpClient.elementList['cmi.progress_measure'] = ""; /* not attempted, completed, incomplete, depends on completionThreshold */
lpClient.elementList['cmi.scaled_passing_score'] = "";
lpClient.elementList['cmi.score._children'] = "scaled,min,max,raw";
lpClient.elementList['cmi.score.scaled'] = "";
lpClient.elementList['cmi.score.min'] = "";
lpClient.elementList['cmi.score.max'] = "";
lpClient.elementList['cmi.score.raw'] = "";
lpClient.elementList['cmi.session_time'] = ""; /* check value ? find a way to store it ? probably use php to convert and serve a correct string */
lpClient.elementList['cmi.success_status'] = ""; /* passed failed or unknown */
lpClient.elementList['cmi.suspend_data'] = "";
lpClient.elementList['cmi.time_limit_action'] = ""; /* exit,message/exit,no message/continue,message/continue,no message // should be initialized to continue,no message if nothing found in manifest */
lpClient.elementList['cmi.total_time'] = "";


// todo
lpClient.elementList['cmi.completion_threshold']  = "";
lpClient.elementList['cmi.interactions']  = {};
lpClient.elementList['cmi.learner_preference'] = "";
lpClient.elementList['cmi.objectives'] = {};
