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
    */
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
    /*
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
}*/


//common vars
$sco['_children'] = "student_id,student_name,lesson_location,credit,lesson_status,entry,score,total_time,exit,session_time";
$sco['score_children'] = "raw,min,max";
$sco['exit'] = "";
$sco['session_time'] = "0000:00:00.00";

header( 'Content-Type: text/javascript' );
?>
    var init_total_time = "<?php echo $sco['total_time']; ?>";

    // ====================================================
    // API Class Constructor
    function APIClass() {

            //-- SCORM 1.2

            // Execution State
            this.LMSInitialize = LMSInitialize;
            this.LMSFinish = LMSFinish;

            // Data Transfer
            this.LMSGetValue = LMSGetValue;
            this.LMSSetValue = LMSSetValue;
            this.LMSCommit = LMSCommit;

            // State Management
            this.LMSGetLastError = LMSGetLastError;
            this.LMSGetErrorString = LMSGetErrorString;
            this.LMSGetDiagnostic = LMSGetDiagnostic;

            //-- SCORM 1.3 / 2004

            // Execution State
            this.Initialize = LMSInitialize;
            this.Terminate = LMSFinish;

            // Data Transfer
            this.GetValue = LMSGetValue;
            this.SetValue = LMSSetValue;
            this.Commit = LMSCommit;
            this.Terminate = LMSTerminate;

            // State Management
            this.GetLastError = LMSGetLastError;
            this.GetErrorString = LMSGetErrorString;
            this.GetDiagnostic = LMSGetDiagnostic;

            //-- Others
            // Private
            this.APIError = APIError;
    }


    // ====================================================
    // Execution State
    //

    // Initialize
    // According to SCORM 1.2 reference :
    //    - arg must be "" (empty string)
    //    - return value : "true" or "false"
    function LMSInitialize(arg) {
            debug("LMSInitialize()", 1);
            if ( arg!="" ) {
                    this.APIError("201");
                    return "false";
            }
            this.APIError("0");
            APIInitialized = true;

            if ( this.LMSGetValue("cmi.core.lesson_status") == "not_started" ) {
                    this.LMSSetValue("cmi.core.lesson_status","started");
            }

            return "true";
    }
    // Finish
    // According to SCORM 1.2 reference
    //    - arg must be "" (empty string)
    //    - return value : "true" or "false"
    function LMSFinish(arg) {
            debug("LMSFinish()", 1);
            if ( APIInitialized ) {
                    if ( arg!="" ) {
                            this.APIError("201");
                            return "false";
                    }
                    this.APIError("0");

                    //setTimeout("doCommit()",1000);

                    APIInitialized = false; //
                    return "true";
            } else {
                    this.APIError("301");   // not initialized
                    return "false";
            }
    }

    // TODO Terminate
    // 1.3 only
    // According to SCORM 1.2 reference
    //    - arg must be "" (empty string)
    //    - return value : "true" or "false"
    function LMSTerminate(arg) {
            debug("LMSTerminate()", 1);
            //TODO
    }

    // ====================================================
    // Data Transfer
    //
    function LMSGetValue(ele) {
            debug("LMSGetValue(" + ele + ")", 1);
            if ( APIInitialized )
            {
                switch (ele)
                {
                    case 'cmi._version' :
                            APIError("0");
                            return elementList[ele];
                            break;
                    case 'cmi.comments_from_learner._children' :
                            APIError("0");
                            return elementList[ele];
                            break;
                    case 'cmi.comments_from_learner._count' :
                            APIError("0");
                            return elementList[ele].length;
                            break;
                    case 'cmi.comments_from_lms._children' :
                            APIError("0");
                            return elementList[ele];
                            break;
                    case 'cmi.comments_from_lms._count' :
                            APIError("0");
                            return elementList[ele].length;
                            break;
                    case 'cmi.completion_status' :  // TODO handle completion_threshold and completion_status and progress_measure
                            APIError("0");
                            return elementList[ele];
                            break;
                    case 'cmi.progress_measure' :
                            if( elementList[ele] == "" )
                            {
                                APIError("403"); // data model element value not initialized
                                return "";
                            }
                            else
                            {
                                APIError("0");
                                return elementList[ele];
                            }
                            break;
                    case 'cmi.success_status' :
                    // TODO compute result
                            break;
                    // todo threshold, measure, status
                    case 'cmi.entry' :
                            APIError("0");
                            return elementList[ele];
                            break;
                    case 'cmi.exit' :
                            APIError("405"); // write only
                            return "";
                            break;
                    case 'cmi.launch_data' :
                            if( elementList[ele] == "" )
                            {
                                APIError("403"); // data model element value not initialized
                                return "";
                            }
                            else
                            {
                                APIError("0");
                                return elementList[ele];
                            }
                            break;
                    case 'cmi.learner_id' :
                            APIError("0");
                            return elementList[ele];
                            break;
                    case 'cmi.learner_name' :
                            APIError("0");
                            return elementList[ele];
                            break;
                    case 'cmi.location' :
                            if( elementList[ele] == "" )
                            {
                                APIError("403"); // data model element value not initialized
                                return "";
                            }
                            else
                            {
                                APIError("0");
                                return elementList[ele];
                            }
                            break;
                    case 'cmi.max_time_allowed' :
                            if( elementList[ele] == "" )
                            {
                                APIError("403"); // data model element value not initialized
                                return "";
                            }
                            else
                            {
                                APIError("0");
                                return elementList[ele];
                            }
                            break;
                    case 'cmi.mode' :
                            APIError("0");
                            return elementList[ele];
                            break;
                    case 'cmi.credit' :
                            APIError("0");
                            return elementList[ele];
                            break;
                    case 'cmi.scaled_passing_score' :
                            APIError("0");
                            return elementList[ele];
                            break;
                    case 'cmi.score._children' :
                            APIError("0");
                            return elementList[ele];
                            break;
                    case 'cmi.score.scaled' :
                            if( elementList[ele] == "" )
                            {
                                APIError("403"); // data model element value not initialized
                                return "";
                            }
                            else
                            {
                                APIError("0");
                                return elementList[ele];
                            }
                            break;
                    case 'cmi.score.min' :
                            if( elementList[ele] == "" )
                            {
                                APIError("403"); // data model element value not initialized
                                return "";
                            }
                            else
                            {
                                APIError("0");
                                return elementList[ele];
                            }
                            break;
                    case 'cmi.score.max' :
                            if( elementList[ele] == "" )
                            {
                                APIError("403"); // data model element value not initialized
                                return "";
                            }
                            else
                            {
                                APIError("0");
                                return elementList[ele];
                            }
                            break;
                    case 'cmi.score.raw' :
                            if( elementList[ele] == "" )
                            {
                                APIError("403"); // data model element value not initialized
                                return "";
                            }
                            else
                            {
                                APIError("0");
                                return elementList[ele];
                            }
                            break;
                    case 'cmi.suspend_data' :
                            if( elementList[ele] == "" )
                            {
                                APIError("403"); // data model element value not initialized
                                return "";
                            }
                            else
                            {
                                APIError("0");
                                return elementList[ele];
                            }
                            break;
                    case 'cmi.time_limit_action' :
                            APIError("0");
                            return elementList[ele];
                            break;
                    case 'cmi.session_time' :
                            // find """something""" that could check that val correspond to : P[yY][mM][dD][T[hH][nM][s[.s]S]]
                            // sum this to total_time on terminate before commit
                            APIError("0");
                            return elementList[ele];
                            break;
                    case 'cmi.total_time' :
                            if( elementList['cmi.session_time'] == "" )
                            {
                                APIError("0");
                                return 0;
                            }
                            APIError("0");
                            return elementList[ele];
                            break;
                    default :
                            // not implemented error
                            APIError("401");
                            return "";
                            break;

                }

            }
            else
            {
                    // not initialized error
                    this.APIError("301");
                    return "false";
            }
    }

    function LMSSetValue(ele,val) {
            debug("LMSSetValue(" + ele +","+ val + ")", 1);
            if ( APIInitialized )
            {
                switch (ele)
                {
                    case 'cmi._version' :
                            APIError("404"); // read only
                            return "false";
                            break;
                    case 'cmi.comments_from_learner._children' :
                            APIError("404"); // read only
                            return "false";
                            break;
                    case 'cmi.comments_from_learner._count' :
                            APIError("404"); // read only
                            return "false";
                            break;
                    case 'cmi.comments_from_lms._children' :
                            APIError("404"); // read only
                            return "false";
                            break;
                    case 'cmi.comments_from_lms._count' :
                            APIError("404"); // read only
                            return "false";
                            break;
                    case 'cmi.completion_status' :
                            var upperCaseVal = val.toUpperCase();
                            if ( upperCaseVal != "COMPLETED" && upperCaseVal != "INCOMPLETE"
                                && upperCaseVal != "NOT ATTEMPTED" && upperCaseVal != "UNKNOWN" )
                            {
                                APIError("406"); // data model element type mismatch
                                return "false";
                            }
                            elementList[ele] = val;
                            APIError("0");
                            return "true";
                            break;
                    case 'cmi.progress_measure' :
                            if( isNaN(parseFloat(val)) )
                            {
                                APIError("406"); // data model element type mismatch
                                return "false";
                            }
                            if( (val < 0) || (val > 1) )
                            {
                                APIError("407"); // data model element out of range
                                return "false";
                            }
                            elementList[ele] = val;
                            return "true";
                            break;
                    case 'cmi.success_status' :
                            var upperCaseVal = val.toUpperCase();
                            if ( upperCaseVal != "PASSED" && upperCaseVal != "FAILED"
                                && upperCaseVal != "UNKNOWN" )
                            {
                                APIError("406"); // data model element type mismatch
                                return "false";
                            }
                            elementList[ele] = val;
                            APIError("0");
                            return "true";
                            break;

                    case 'cmi.entry' :
                            APIError("404"); // read only
                            return "false";
                            break;
                    case 'cmi.exit' :
                            var upperCaseVal = val.toUpperCase();
                            if ( upperCaseVal != "TIME-OUT" && upperCaseVal != "SUSPEND"
                                && upperCaseVal != "LOGOUT" && upperCaseVal != "" )
                            {
                                APIError("406"); // data model element type mismatch
                                return "false";
                            }
                            elementList[ele] = val;
                            APIError("0");
                            return "true";
                            break;
                    case 'cmi.launch_data' :
                            APIError("404"); // read only
                            return "false";
                            break;
                    case 'cmi.learner_id' :
                            APIError("404"); // read only
                            return "false";
                            break;
                    case 'cmi.learner_name' :
                            APIError("404"); // read only
                            return "false";
                            break;
                    case 'cmi.location' :
                            if( val.length > 255 )
                            {
                                APIError("405");
                                return "false";
                            }
                            elementList[ele] = val;
                            APIError("0");
                            return "true";
                            break;
                    case 'cmi.max_time_allowed' :
                            APIError("404"); // read only
                            return "false";
                            break;
                    case 'cmi.mode' :
                            APIError("404"); // read only
                            return "false";
                            break;
                    case 'cmi.credit' :
                            APIError("404"); // read only
                            return "false";
                            break;
                    case 'cmi.scaled_passing_score' :
                            APIError("404"); // read only
                            return "false";
                            break;
                    case 'cmi.score._children' :
                            APIError("404"); // read only
                            return "false";
                            break;
                    case 'cmi.score.scaled' :
                            if( isNaN(parseFloat(val)) )
                            {
                                APIError("406"); // data model element type mismatch
                                return "false";
                            }
                            if( (val < 0) || (val > 1) )
                            {
                                APIError("407"); // data model element out of range
                                return "false";
                            }
                            elementList[ele] = val;
                            return "true";
                            break;
                    case 'cmi.score.min' :
                            if( isNaN(parseFloat(val)) )
                            {
                                APIError("406"); // data model element type mismatch
                                return "false";
                            }
                            if( (val < 0) || (val > 1) )
                            {
                                APIError("407"); // data model element out of range
                                return "false";
                            }
                            elementList[ele] = val;
                            return "true";
                            break;
                    case 'cmi.score.max' :
                            if( isNaN(parseFloat(val)) )
                            {
                                APIError("406"); // data model element type mismatch
                                return "false";
                            }
                            if( (val < 0) || (val > 1) )
                            {
                                APIError("407"); // data model element out of range
                                return "false";
                            }
                            elementList[ele] = val;
                            return "true";
                            break;
                    case 'cmi.score.raw' :
                            if( isNaN(parseFloat(val)) )
                            {
                                APIError("406"); // data model element type mismatch
                                return "false";
                            }
                            if( (val < 0) || (val > 1) )
                            {
                                APIError("407"); // data model element out of range
                                return "false";
                            }
                            elementList[ele] = val;
                            return "true";
                            break;
                    case 'cmi.session_time' :
                            // find """something""" that could check that val correspond to : P[yY][mM][dD][T[hH][nM][s[.s]S]]
                            elementList[ele] = val;
                            APIError("0");
                            return "true";
                            break;
                    case 'cmi.total_time' :
                            APIError("404"); // read only
                            return "false";
                            break;
                    case 'cmi.suspend_data' :
                            elementList[ele] = val;
                            APIError("0");
                            return "true";
                            break;
                    case 'cmi.time_limit_action' :
                            APIError("404"); // read only
                            return "false";
                            break;
                    default :
                            // not implemented error
                            APIError("401");
                            return "";
                            break;

                }
            }
            else
            {
                    // not initialized error
                    this.APIError("301");
                    return "false";
            }
    }

    function LMSCommit(arg)
    {
            debug("LMScommit()", 1);
            if ( APIInitialized ) {
                    if ( arg!="" ) {
                            this.APIError("201");
                            return "false";
                    } else {
                            this.APIError("0");

                            doCommit();

                            return "true";
                    }
            } else {
                    this.APIError("301");
                    return "false";
            }
    }


    // ====================================================
    // State Management
    //
    function LMSGetLastError() {
            debug("LMSGetLastError() : returns " + APILastError, 1);

            return APILastError;
    }

    function LMSGetErrorString(num) {
            debug("LMSGetErrorString(" + num +") : returns " + errCodes[num], 1);

            return errCodes[num];

    }

    function LMSGetDiagnostic(num) {
            debug("LMSGetDiagnostic(" + num + ") : returns " + errDiagn[num], 1);

            if ( num == "" ) num = APILastError;
            return errDiagn[num];
    }


    // ====================================================
    // Private
    //
    function APIError(num) {
            APILastError = num;
    }

    // ====================================================
    // Error codes and Error diagnostics
    //
    var errCodes = new Array();
    errCodes["0"]   = "No Error";
    errCodes["101"] = "General Exception";
    errCodes["102"] = "Server is busy";
    errCodes["201"] = "Invalid Argument Error";
    errCodes["202"] = "Element cannot have children";
    errCodes["203"] = "Element not an array.  Cannot have count";
    errCodes["301"] = "Not initialized";
    errCodes["401"] = "Not implemented error";
    errCodes["402"] = "Invalid set value, element is a keyword";
    errCodes["403"] = "Element is read only";
    errCodes["404"] = "Element is write only";
    errCodes["405"] = "Incorrect Data Type";

    var errDiagn = new Array();
    errDiagn["0"]   = "No Error";
    errDiagn["101"] = "Possible Server error.  Contact System Administrator";
    errDiagn["102"] = "Server is busy and cannot handle the request.  Please try again";
    errDiagn["201"] = "The course made an incorrect function call.  Contact course vendor or system administrator";
    errDiagn["202"] = "The course made an incorrect data request. Contact course vendor or system administrator";
    errDiagn["203"] = "The course made an incorrect data request. Contact course vendor or system administrator";
    errDiagn["301"] = "The system has not been initialized correctly.  Please contact your system administrator";
    errDiagn["401"] = "The course made a request for data not supported by Answers.";
    errDiagn["402"] = "The course made a bad data saving request.  Contact course vendor or system administrator";
    errDiagn["403"] = "The course tried to write to a read only value.  Contact course vendor";
    errDiagn["404"] = "The course tried to read a value that can only be written to.  Contact course vendor";
    errDiagn["405"] = "The course gave an incorrect Data type.  Contact course vendor";



    // ====================================================
    // CMI Elements and Values
    //

    var elementList = {};
    elementList['cmi._version'] = '1.0';
    elementList['cmi.comments_from_learner._children']  = "comment,location,timestamp";
    elementList['cmi.comments_from_learner._count']  = "";
    elementList['cmi.comments_from_learner'] = {}; // TODO handle collections
    elementList['cmi.comments_from_lms._children']  = "comment,location,timestamp";
    elementList['cmi.comments_from_lms._count']  = "";
    elementList['cmi.comments_from_lms'] = {}; // TODO handle collections
    elementList['cmi.completion_status']  = "UNKNOWN"; // progress measure == 0 -> not attempted, 1 -> completed, 0 < < 1 -> incomplete but depends on complete threshold
    elementList['cmi.entry']  = "";
    elementList['cmi.exit']  = "";
    elementList['cmi.launch_data']  = "";
    elementList['cmi.learner_id']  = "";
    elementList['cmi.learner_name'] = "";
    elementList['cmi.location'] = "";
    elementList['cmi.max_time_allowed'] = ""; // TODO get that from manifest !
    elementList['cmi.mode'] = "";   // if mode is browse or review credit is always no-credit, if mode == "normal" credit my be credit or no-credit
    elementList['cmi.credit']  = "no-credit";
    elementList['cmi.progress_measure'] = ""; // not attempted, completed, incomplete, depends on completionThreshold
    elementList['cmi.scaled_passing_score'] = "";
    elementList['cmi.score._children'] = "scaled,min,max,raw";
    elementList['cmi.score.scaled'] = "";
    elementList['cmi.score.min'] = "";
    elementList['cmi.score.max'] = "";
    elementList['cmi.score.raw'] = "";
    elementList['cmi.session_time'] = ""; // check value ? find a way to store it ? probably use php to convert and serve a correct string
    elementList['cmi.success_status'] = ""; // passed failed or unknown
    elementList['cmi.suspend_data'] = "";
    elementList['cmi.time_limit_action'] = ""; // exit,message/exit,no message/continue,message/continue,no message // should be initialized to continue,no message if nothing found in manifest
    elementList['cmi.total_time'] = "";


    // todo
    elementList['cmi.completion_threshold']  = "";
    elementList['cmi.interactions']  = {};
    elementList['cmi.learner_preference'] = "";
    elementList['cmi.objectives'] = {};






    // ====================================================
    // Final Setup
    //


    APIInitialized = false;
    APILastError = "301";

    // Declare Scorm API object for 1.2

    //API = new APIClass();
    //api = API;

    // Declare Scorm API object for 2004

    API_1484_11 = new APIClass();
    //api_1484_11 = API_1484_11;

    // other usefull vars
    var itemId = "<?php echo $itemId ?>";

