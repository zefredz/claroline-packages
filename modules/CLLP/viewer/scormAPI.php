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

header( 'Content-Type: text/javascript' );

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
                    if( isDefined(elementList[ele]) )
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
                                    return elementList['cmi.comments_from_learner'].length;
                                    break;
                            case 'cmi.comments_from_lms._children' :
                                    APIError("0");
                                    return elementList[ele];
                                    break;
                            case 'cmi.comments_from_lms._count' :
                                    APIError("0");
                                    return elementList['cmi.comments_from_lms'].length;
                                    break;
                            case 'cmi.core._children' :
                                    APIError("0");
                                    return values[i];
                                    break;
                            case 'cmi.core.student_id' :
                                    APIError("0");
                                    return values[i];
                                    break;
                            case 'cmi.core.student_name' :
                                    APIError("0");
                                    return values[i];
                                    break;
                            case 'cmi.core.lesson_location' :
                                    APIError("0");
                                    return values[i];
                                    break;
                            case 'cmi.core.credit' :
                                    APIError("0");
                                    return values[i];
                                    break;
                            case 'cmi.core.lesson_status' :
                                    APIError("0");
                                    return values[i];
                                    break;

                            //-----------------------------------
                            //deal with SCORM 2004 new elements :
                            //-----------------------------------

                            case 'cmi.completion_status' :
                                    APIError("0");
                                    ele = 'cmi.core.lesson_status';
                                    return values[i];
                                    break;

                            case 'cmi.success_status' :
                                    APIError("0");
                                    ele = 'cmi.core.lesson_status';
                                    return values[i];
                                    break;

                            //-----------------------------------

                            case 'cmi.core.entry' :
                                    APIError("0");
                                    return values[i];
                                    break;
                            case 'cmi.core.score._children' :
                                    APIError("0");
                                    return values[i];
                                    break;
                            case 'cmi.core.score.raw' :
                                    APIError("0");
                                    return values[i];
                                    break;
                            case 'cmi.core.score.min' :
                                    APIError("0");
                                    return values[i];
                                    break;
                            case 'cmi.core.score.max' :
                                    APIError("0");
                                    return values[i];
                                    break;
                            case 'cmi.core.total_time' :
                                    APIError("0");
                                    return values[i];
                                    break;
                            case 'cmi.core.exit' :
                                    APIError("404"); // write only
                                    return "";
                                    break;
                            case 'cmi.core.session_time' :
                                    APIError("404"); // write only
                                    return "";
                                    break;
                            case 'cmi.suspend_data' :
                                    APIError("0");
                                    return values[i];
                                    break;
                            case 'cmi.launch_data' :
                                    APIError("0");
                                    return values[i];
                                    break;

                        }
                    }
                    else // ele not implemented
                    {
                        // not implemented error
                        APIError("401");
                        return "";
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
                    if( isDefined(elementList[ele]) )
                    {
                        switch (ele)
                        {
                            case 'cmi._version' : 
                                    APIError("403"); // read only
                                    return false;
                                    break;
                            case 'cmi.comments_from_learner._children' :
                                    APIError("403"); // read only
                                    return false;
                                    break;
                            case 'cmi.comments_from_learner._count' :
                                    APIError("403"); // read only
                                    return false;
                                    break;
                            case 'cmi.core._children' :
                                    APIError("402"); // invalid set value, element is a keyword
                                    return "false";
                                    break;
                            case 'cmi.core.student_id' :
                                    APIError("403"); // read only
                                    return "false";
                                    break;
                            case 'cmi.core.student_name' :
                                    APIError("403"); // read only
                                    return "false";
                                    break;
                            case 'cmi.core.lesson_location' :
                                    if( val.length > 255 )
                                    {
                                        APIError("405");
                                        return "false";
                                    }
                                    values[i] = val;
                                    APIError("0");
                                    return "true";
                                    break;
                            case 'cmi.core.lesson_status' :
                                    var upperCaseVal = val.toUpperCase();
                                    if ( upperCaseVal != "PASSED" && upperCaseVal != "FAILED"
                                        && upperCaseVal != "COMPLETED" && upperCaseVal != "INCOMPLETE"
                                        && upperCaseVal != "BROWSED" && upperCaseVal != "NOT ATTEMPTED" )
                                    {
                                        APIError("405");
                                        return "false";
                                    }

                                    values[i] = val;
                                    APIError("0");
                                    return "true";
                                    break;


                            //-------------------------------
                            // Deal with SCORM 2004 element :
                            // completion_status and success_status are new element,
                            // we use them together with the old element lesson_status in the claro DB
                            //-------------------------------

                            case 'cmi.completion_status' :
                                    var upperCaseVal = val.toUpperCase();
                                    if ( upperCaseVal != "PASSED" && upperCaseVal != "FAILED"
                                        && upperCaseVal != "COMPLETED" && upperCaseVal != "INCOMPLETE"
                                        && upperCaseVal != "BROWSED" && upperCaseVal != "NOT ATTEMPTED" && upperCaseVal != "UNKNOWN" )
                                    {
                                        APIError("405");
                                        return "false";
                                    }
                                    ele = 'cmi.core.lesson_status';
                                    values[4] = val;  // deal with lesson_status element from scorm 1.2 instead
                                    APIError("0");
                                    return "true";
                                    break;

                            case 'cmi.success_status' :
                                    var upperCaseVal = val.toUpperCase();
                                    if ( upperCaseVal != "PASSED" && upperCaseVal != "FAILED"
                                        && upperCaseVal != "COMPLETED" && upperCaseVal != "INCOMPLETE"
                                        && upperCaseVal != "BROWSED" && upperCaseVal != "NOT ATTEMPTED" && upperCaseVal != "UNKNOWN" )
                                    {
                                        APIError("405");
                                        return "false";
                                    }

                                    ele = 'cmi.core.lesson_status';
                                    values[4] = val;  // deal with lesson_status element from scorm 1.2 instead
                                    APIError("0");
                                    return "true";
                                    break;

                            //-------------------------------


                            case 'cmi.core.credit' :
                                    APIError("403"); // read only
                                    return "false";
                                    break;
                            case 'cmi.core.entry' :
                                    APIError("403"); // read only
                                    return "false";
                                    break;
                            case 'cmi.core.score._children' :
                                    APIError("402");  // invalid set value, element is a keyword
                                    return "false";
                                    break;
                            case 'cmi.core.score.raw' :
                                    if( isNaN(parseInt(val)) || (val < 0) || (val > 100) )
                                    {
                                        APIError("405");
                                        return "false";
                                    }
                                    values[i] = val;
                                    APIError("0");
                                    return "true";
                                    break;
                            case 'cmi.core.score.min' :
                                    if( isNaN(parseInt(val)) || (val < 0) || (val > 100) )
                                    {
                                        APIError("405");
                                        return "false";
                                    }
                                    values[i] = val;
                                    APIError("0");
                                    return "true";
                                    break;
                            case 'cmi.core.score.max' :
                                    if( isNaN(parseInt(val)) || (val < 0) || (val > 100) )
                                    {
                                        APIError("405");
                                        return "false";
                                    }
                                    values[i] = val;
                                    APIError("0");
                                    return "true";
                                    break;
                            case 'cmi.core.total_time' :
                                    APIError("403"); //read only
                                    return "false";
                                    break;
                            case 'cmi.core.exit' :
                                    var upperCaseVal = val.toUpperCase();
                                    if ( upperCaseVal != "TIME-OUT" && upperCaseVal != "SUSPEND"
                                        && upperCaseVal != "LOGOUT" && upperCaseVal != "" )
                                    {
                                        APIError("405");
                                        return "false";
                                    }
                                    values[i] = val;
                                    APIError("0");
                                    return "true";
                                    break;
                            case 'cmi.core.session_time' :
                                    // regexp to check format
                                    // hhhh:mm:ss.ss
                                    var re = /^[0-9]{2,4}:[0-9]{2}:[0-9]{2}(.)?[0-9]?[0-9]?$/;

                                    if ( !re.test(val) )
                                    {
                                        APIError("405");
                                        return "false";
                                    }

								  // check that minuts and second are 0 <= x < 60
                                    var splitted_val = val.split(":");
                                    if( splitted_val[1] < 0 || splitted_val[1] >= 60 || splitted_val[2] < 0 || splitted_val[2] >= 60 )
                                    {
                                        APIError("405");
                                        return "false";
								  }

                                    values[i] = val;
                                    APIError("0");
                                    return "true";
                                    break;
                            case 'cmi.suspend_data' :
                                    if( val.length > 4096 )
                                    {
                                        APIError("405");
                                        return "false";
                                    }
                                    values[i] = val;
                                    APIError("0");
                                    return "true";
                                    break;
                            case 'cmi.launch_data' :
                                    APIError("403"); //read only
                                    return "false";
                                    break;

                        }
                    }
                    else // ele not implemented
                    {
                        // not implemented error
                        APIError("401");
                        return "";
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
    var elements = new Array();
    elements[0]  = "cmi.core._children";
    elements[1]  = "cmi.core.student_id";
    elements[2]  = "cmi.core.student_name";
    elements[3]  = "cmi.core.lesson_location";
    elements[4]  = "cmi.core.lesson_status";
    elements[5]  = "cmi.core.credit";
    elements[6]  = "cmi.core.entry";
    elements[7]  = "cmi.core.score._children";
    elements[8]  = "cmi.core.score.raw";
    elements[9]  = "cmi.core.total_time";
    elements[10] = "cmi.core.exit";
    elements[11] = "cmi.core.session_time";
    elements[12] = "cmi.suspend_data";
    elements[13] = "cmi.launch_data";
    elements[14] = "cmi.core.score.min";
    elements[15] = "cmi.core.score.max";
    elements[16] = "cmi.completion_status";
    elements[17] = "cmi.success_status";

    var values = new Array();
    values[0]  = "<?php echo $sco['_children']; ?>";
    values[1]  = "<?php echo $sco['student_id']; ?>";
    values[2]  = "<?php echo $sco['student_name']; ?>";
    values[3]  = "<?php echo $sco['lesson_location']; ?>";
    values[4]  = "<?php echo $sco['lesson_status'];?>";
    values[5]  = "<?php echo $sco['credit']; ?>";
    values[6]  = "<?php echo $sco['entry'];?>";
    values[7]  = "<?php echo $sco['score_children']; ?>";
    values[8]  = "<?php echo $sco['raw'];?>";
    values[9]  = "<?php echo $sco['total_time'] ?>";
    values[10] = "<?php echo $sco['exit']; ?>";
    values[11] = "<?php echo $sco['session_time']; ?>";
    values[12] = "<?php echo $sco['suspend_data'];?>";
    values[13] = "<?php echo $sco['launch_data'];?>";
    values[14] = "<?php echo $sco['scoreMin'];?>";
    values[15] = "<?php echo $sco['scoreMax'];?>";
    values[16] = "<?php echo $sco['lesson_status']?>"; //we do deal the completion_status element with the old lesson_status element, this will change in further versions...
    values[17] = "<?php echo $sco['lesson_status']?>"; //we do deal the sucess_status element with the old lesson_status element, this will change in further versions...

    var elementList = new Array();
    elementList['cmi._version'] = '1.0';
    elementList['cmi.comments_from_learner._children']  = "comment,location,timestamp";
    elementList['cmi.comments_from_learner._count']  = "";
    elementList['cmi.comments_from_learner'] = new Array(); // TODO handle collections
    elementList['cmi.comments_from_lms._children']  = "comment,location,timestamp";
    elementList['cmi.comments_from_lms._count']  = "";
    elementList['cmi.comments_from_lms'] = new Array(); // TODO handle collections
    
    // todo
    elementList['cmi.completion_status']  = "";
    elementList['cmi.completion_threshold']  = "";    
    elementList['cmi.credit']  = "";
    elementList['cmi.entry']  = "";
    elementList['cmi.exit']  = "";
    elementList['cmi.interactions']  = new Array();
    elementList['cmi.launch_data']  = "";
    elementList['cmi.learner_id']  = "";
    elementList['cmi.learner_name'] = "";
    elementList['cmi.learner_preference'] = "";
    elementList['cmi.location'] = "";
    elementList['cmi.max_time_allowed'] = "";
    elementList['cmi.mode'] = "";
    elementList['cmi.objectives'] = new Array();
    elementList['cmi.progress_measure'] = "";
    elementList['cmi.scaled_passing_score'] = "";
    elementList['cmi.score'] = "";
    elementList['cmi.session_time'] = "";
    elementList['cmi.success_status'] = "";
    elementList['cmi.suspend_data'] = "";                
    elementList['cmi.time_limit_action'] = "";
    elementList['cmi.total_time'] = "";
        
    // ====================================================
    // Final Setup
    //


    APIInitialized = false;
    APILastError = "301";

    // Declare Scorm API object for 1.2

    API = new APIClass();
    api = API;

    // Declare Scorm API object for 2004

    API_1484_11 = new APIClass();
    api_1484_11 = API_1484_11;

