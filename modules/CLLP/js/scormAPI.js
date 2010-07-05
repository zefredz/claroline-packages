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
        lpHandler.debug("LMSInitialize()", 1);
        if ( arg!="" ) {
                this.APIError("201");
                return "false";
        }
        this.APIError("0");
        lpHandler.APIInitialized = true;

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
        lpHandler.debug("LMSFinish()", 1);
        if ( lpHandler.APIInitialized ) {
                if ( arg!="" ) {
                        this.APIError("201");
                        return "false";
                }
                this.APIError("0");

                //setTimeout("doCommit()",1000);

                lpHandler.APIInitialized = false; //
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
        lpHandler.debug("LMSTerminate()", 1);
        //TODO
}

// ====================================================
// Data Transfer
//
function LMSGetValue(ele) {
        lpHandler.debug("LMSGetValue(" + ele + ")", 1);
        if ( lpHandler.APIInitialized )
        {
            switch (ele)
            {
                case 'cmi._version' :
                        APIError("0");
                        return lpHandler.elementList[ele];
                        break;
                case 'cmi.comments_from_learner._children' :
                        APIError("0");
                        return lpHandler.elementList[ele];
                        break;
                case 'cmi.comments_from_learner._count' :
                        APIError("0");
                        return lpHandler.elementList[ele].length;
                        break;
                case 'cmi.comments_from_lms._children' :
                        APIError("0");
                        return lpHandler.elementList[ele];
                        break;
                case 'cmi.comments_from_lms._count' :
                        APIError("0");
                        return lpHandler.elementList[ele].length;
                        break;
                case 'cmi.completion_status' :  // TODO handle completion_threshold and completion_status and progress_measure
                        APIError("0");
                        return lpHandler.elementList[ele];
                        break;
                case 'cmi.progress_measure' :
                        if( lpHandler.elementList[ele] == "" )
                        {
                            APIError("403"); // data model element value not initialized
                            return "";
                        }
                        else
                        {
                            APIError("0");
                            return lpHandler.elementList[ele];
                        }
                        break;
                case 'cmi.success_status' :
                // TODO compute result
                        break;
                // todo threshold, measure, status
                case 'cmi.entry' :
                        APIError("0");
                        return lpHandler.elementList[ele];
                        break;
                case 'cmi.exit' :
                        APIError("405"); // write only
                        return "";
                        break;
                case 'cmi.launch_data' :
                        if( lpHandler.elementList[ele] == "" )
                        {
                            APIError("403"); // data model element value not initialized
                            return "";
                        }
                        else
                        {
                            APIError("0");
                            return lpHandler.elementList[ele];
                        }
                        break;
                case 'cmi.learner_id' :
                        APIError("0");
                        return lpHandler.elementList[ele];
                        break;
                case 'cmi.learner_name' :
                        APIError("0");
                        return lpHandler.elementList[ele];
                        break;
                case 'cmi.location' :
                        if( lpHandler.elementList[ele] == "" )
                        {
                            APIError("403"); // data model element value not initialized
                            return "";
                        }
                        else
                        {
                            APIError("0");
                            return lpHandler.elementList[ele];
                        }
                        break;
                case 'cmi.max_time_allowed' :
                        if( lpHandler.elementList[ele] == "" )
                        {
                            APIError("403"); // data model element value not initialized
                            return "";
                        }
                        else
                        {
                            APIError("0");
                            return lpHandler.elementList[ele];
                        }
                        break;
                case 'cmi.mode' :
                        APIError("0");
                        return lpHandler.elementList[ele];
                        break;
                case 'cmi.credit' :
                        APIError("0");
                        return lpHandler.elementList[ele];
                        break;
                case 'cmi.scaled_passing_score' :
                        APIError("0");
                        return lpHandler.elementList[ele];
                        break;
                case 'cmi.score._children' :
                        APIError("0");
                        return lpHandler.elementList[ele];
                        break;
                case 'cmi.score.scaled' :
                        if( lpHandler.elementList[ele] == "" )
                        {
                            APIError("403"); // data model element value not initialized
                            return "";
                        }
                        else
                        {
                            APIError("0");
                            return lpHandler.elementList[ele];
                        }
                        break;
                case 'cmi.score.min' :
                        if( lpHandler.elementList[ele] == "" )
                        {
                            APIError("403"); // data model element value not initialized
                            return "";
                        }
                        else
                        {
                            APIError("0");
                            return lpHandler.elementList[ele];
                        }
                        break;
                case 'cmi.score.max' :
                        if( lpHandler.elementList[ele] == "" )
                        {
                            APIError("403"); // data model element value not initialized
                            return "";
                        }
                        else
                        {
                            APIError("0");
                            return lpHandler.elementList[ele];
                        }
                        break;
                case 'cmi.score.raw' :
                        if( lpHandler.elementList[ele] == "" )
                        {
                            APIError("403"); // data model element value not initialized
                            return "";
                        }
                        else
                        {
                            APIError("0");
                            return lpHandler.elementList[ele];
                        }
                        break;
                case 'cmi.suspend_data' :
                        if( lpHandler.elementList[ele] == "" )
                        {
                            APIError("403"); // data model element value not initialized
                            return "";
                        }
                        else
                        {
                            APIError("0");
                            return lpHandler.elementList[ele];
                        }
                        break;
                case 'cmi.time_limit_action' :
                        APIError("0");
                        return lpHandler.elementList[ele];
                        break;
                case 'cmi.session_time' :
                        // find """something""" that could check that val correspond to : P[yY][mM][dD][T[hH][nM][s[.s]S]]
                        // sum this to total_time on terminate before commit
                        APIError("0");
                        return lpHandler.elementList[ele];
                        break;
                case 'cmi.total_time' :
                        if( lpHandler.elementList['cmi.session_time'] == "" )
                        {
                            APIError("0");
                            return 0;
                        }
                        APIError("0");
                        return lpHandler.elementList[ele];
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
        lpHandler.debug("LMSSetValue(" + ele +","+ val + ")", 1);
        if ( lpHandler.APIInitialized )
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
                        lpHandler.elementList[ele] = val;
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
                        lpHandler.elementList[ele] = val;
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
                        lpHandler.elementList[ele] = val;
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
                        lpHandler.elementList[ele] = val;
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
                        lpHandler.elementList[ele] = val;
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
                        lpHandler.elementList[ele] = val;
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
                        lpHandler.elementList[ele] = val;
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
                        lpHandler.elementList[ele] = val;
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
                        lpHandler.elementList[ele] = val;
                        return "true";
                        break;
                case 'cmi.session_time' :
                        // find """something""" that could check that val correspond to : P[yY][mM][dD][T[hH][nM][s[.s]S]]
                        lpHandler.elementList[ele] = val;
                        APIError("0");
                        return "true";
                        break;
                case 'cmi.total_time' :
                        APIError("404"); // read only
                        return "false";
                        break;
                case 'cmi.suspend_data' :
                        lpHandler.elementList[ele] = val;
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
        lpHandler.debug("LMScommit()", 1);
        if ( lpHandler.APIInitialized ) {
                if ( arg!="" ) {
                        this.APIError("201");
                        return "false";
                } else {
                        this.APIError("0");
                        // API should handle total time because server side will have to receive 1.2 data or 1.3 data
                        //lpHandler.elementList['cmi.total_time'] = totalTime();
                        lpHandler.commit();

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
        lpHandler.debug("LMSGetLastError() : returns " + lpHandler.APILastError, 1);

        return lpHandler.APILastError;
}

function LMSGetErrorString(num) {
        lpHandler.debug("LMSGetErrorString(" + num +") : returns " + errCodes[num], 1);

        return errCodes[num];

}

function LMSGetDiagnostic(num) {
        lpHandler.debug("LMSGetDiagnostic(" + num + ") : returns " + errDiagn[num], 1);

        if ( num == "" ) num = lpHandler.APILastError;
        return errDiagn[num];
}


// ====================================================
// Private
//
function APIError(num) {
        lpHandler.APILastError = num;
}

// ====================================================
// handle time format
//
function addTime (first, second) {
    var timestring = 'P';
    var matchexpr = /^P((\d+)Y)?((\d+)M)?((\d+)D)?(T((\d+)H)?((\d+)M)?((\d+(\.\d{1,2})?)S)?)?$/;
    var firstarray = first.match(matchexpr);
    var secondarray = second.match(matchexpr);
    if ((firstarray != null) && (secondarray != null)) {
        var secs = parseFloat(firstarray[13],10)+parseFloat(secondarray[13],10);  //Seconds
        change = Math.floor(secs / 60);
        secs = secs - (change * 60);
        mins = parseInt(firstarray[11],10)+parseInt(secondarray[11],10)+change;   //Minutes
        change = Math.floor(mins / 60);
        mins = mins - (change * 60);
        hours = parseInt(firstarray[10],10)+parseInt(secondarray[10],10)+change;  //Hours
        change = Math.floor(hours / 24);
        hours = hours - (change * 24);
        days = parseInt(firstarray[6],10)+parseInt(secondarray[6],10)+change; // Days
        months = parseInt(firstarray[4],10)+parseInt(secondarray[4],10)
        years = parseInt(firstarray[2],10)+parseInt(secondarray[2],10)
    }
    if (years > 0) {
        timestring += years + 'Y';
    }
    if (months > 0) {
        timestring += months + 'M';
    }
    if (days > 0) {
        timestring += days + 'D';
    }
    if ((hours > 0) || (mins > 0) || (secs > 0)) {
        timestring += 'T';
        if (hours > 0) {
            timestring += hours + 'H';
        }
        if (mins > 0) {
            timestring += mins + 'M';
        }
        if (secs > 0) {
            timestring += secs + 'S';
        }
    }
    return timestring;
}

function totalTime() {
    total_time = addTime(lpHandler.elementList['cmi.total_time'], lpHandler.elementList['cmi.session_time']);
    return total_time;
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
// see in apiData.php





// ====================================================
// Final Setup
//




// Declare Scorm API object for 1.2

API = new APIClass();
api = API;

// Declare Scorm API object for 2004

API_1484_11 = new APIClass();
api_1484_11 = API_1484_11;



