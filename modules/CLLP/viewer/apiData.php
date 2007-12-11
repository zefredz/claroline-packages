<?php // $Id$

/**
 * CLAROLINE
 *
 * @version 0.1 $Revision: 1.2 $
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
//lpHandler.itemId = "<?php echo $itemId; ?>";
*/

header( 'Content-Type: text/javascript' );

?>
if( ! $.browser.msie ) console.info("API data refresh request for item #<?php echo $itemId; ?> in path #<?php echo $pathId; ?>");

API_1484_11.init();
<?
/*
//


// ====================================================
// CMI Elements and Values
//
/*
	// entry handling
    if (isset($userdata->status)) {
        //if ($userdata->status == ''&& (!(($userdata->{'cmi.exit'} == 'suspend') || ($userdata->{'cmi.exit'} == 'logout'))&& !($userdata->{'adl.nav.request'} == 'suspendAll'))||($userdata->{'cmi.exit'} == 'normal')) {      //antes solo llegaba esta l�nea hasta el &&
        if (!isset($userdata->{'cmi.exit'}) || (($userdata->{'cmi.exit'} == 'time-out') || ($userdata->{'cmi.exit'} == 'normal'))) {
                $userdata->entry = 'ab-initio';
        } else {
            //if ((isset($userdata->{'cmi.exit'}) && (($userdata->{'cmi.exit'} == 'suspend') || ($userdata->{'cmi.exit'} == 'logout')))||(($userdata->{'adl.nav.request'} == 'suspendAll')&& isset($userdata->{'adl.nav.request'}) )) {
            if (isset($userdata->{'cmi.exit'}) && (($userdata->{'cmi.exit'} == 'suspend') || ($userdata->{'cmi.exit'} == 'logout'))) {
                $userdata->entry = 'resume';
            } else {
                $userdata->entry = '';
            }
        }
    }

*/
exit;
?>