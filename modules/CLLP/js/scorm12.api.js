// ====================================================
// API Class Constructor

var API = {

    // ====================================================
    // Private
    //

    _Initialized : false,
  _Terminated : false,
  _lastError : "0",
    _lastDiagnostic : "",

    _datamodel : {},
    // ====================================================
    // Error codes and Error diagnostics
    //
    _errCodes : {
      "0" : "No Error",
      "101" : "General Exception",
      "102" : "Server is busy",
      "201" : "Invalid Argument Error",
      "202" : "Element cannot have children",
      "203" : "Element not an array.  Cannot have count",
      "301" : "Not initialized",
      "401" : "Not implemented error",
      "402" : "Invalid set value, element is a keyword",
      "403" : "Element is read only",
      "404" : "Element is write only",
      "405" : "Incorrect Data Type"
    },


    _errDiagn : {
      "0" : "No Error",
      "101" : "Possible Server error.  Contact System Administrator",
      "102" : "Server is busy and cannot handle the request.  Please try again",
      "201" : "The course made an incorrect function call.  Contact course vendor or system administrator",
      "202" : "The course made an incorrect function call.  Contact course vendor or system administrator",
      "203" : "The course made an incorrect function call.  Contact course vendor or system administratort",
      "301" : "The system has not been initialized correctly.  Please contact your system administrator",
      "401" : "The course made a request for data not supported.",
      "402" : "The course made a bad data saving request.  Contact course vendor or system administrator",
      "403" : "The course tried to write to a read only value.  Contact course vendor",
      "404" : "The course tried to read a value that can only be written to.  Contact course vendor",
      "405" : "The course gave an incorrect Data type.  Contact course vendor"
    },


    // make object ready to use in a fresh context
    init : function() {
        this._Initialized = false;
        this._Terminated = false;
        this._lastError = "0";
        this._lastDiagnostic = "";

        this._datamodel =  {
            'cmi.completion_threshold' : {'value' : null, 'mod':'r'}
        }
    },

// ====================================================
// Execution State
//

// Initialize
// According to SCORM 1.2 reference :
//    - arg must be "" (empty string)
//    - return value : "true" or "false"
LMSInitialize : function (arg) {
        lpHandler.debug("LMSInitialize()", 1);
        if ( arg!="" ) {
                this.APIError("201");
                return "false";
        }
        this.APIError("0");
        this._Initialized = true;

        if ( this.LMSGetValue("cmi.core.lesson_status") == "not_started" ) {
                this.LMSSetValue("cmi.core.lesson_status","started");
        }

        return "true";
},

// Finish
// According to SCORM 1.2 reference
//    - arg must be "" (empty string)
//    - return value : "true" or "false"
LMSFinish : function (arg) {
        lpHandler.debug("LMSFinish()", 1);
        if ( this._Initialized ) {
                if ( arg!="" ) {
                        this.APIError("201");
                        return "false";
                }
                this.APIError("0");

                //setTimeout("doCommit()",1000);

                this._Initialized = false; //
                return "true";
        } else {
                this.APIError("301");   // not initialized
                return "false";
        }
},

// ====================================================
// Data Transfer
//
LMSGetValue : function (ele) {
        lpHandler.debug("LMSGetValue(" + ele + ")", 1);
        if ( this._Initialized )
        {
            switch (ele)
            {
                case 'cmi._version' :
                        this.APIError("0");
                        return lpHandler.elementList[ele];
                        break;
                case 'cmi.comments_from_learner._children' :
                        this.APIError("0");
                        return lpHandler.elementList[ele];
                        break;
                case 'cmi.comments_from_learner._count' :
                        this.APIError("0");
                        return lpHandler.elementList[ele].length;
                        break;
                case 'cmi.comments_from_lms._children' :
                        this.APIError("0");
                        return lpHandler.elementList[ele];
                        break;
                case 'cmi.comments_from_lms._count' :
                        this.APIError("0");
                        return lpHandler.elementList[ele].length;
                        break;
                case 'cmi.completion_status' :  // TODO handle completion_threshold and completion_status and progress_measure
                        this.APIError("0");
                        return lpHandler.elementList[ele];
                        break;
                case 'cmi.progress_measure' :
                        if( lpHandler.elementList[ele] == "" )
                        {
                            this.APIError("403"); // data model element value not initialized
                            return "";
                        }
                        else
                        {
                            this.APIError("0");
                            return lpHandler.elementList[ele];
                        }
                        break;
                case 'cmi.success_status' :
                // TODO compute result
                        break;
                // todo threshold, measure, status
                case 'cmi.entry' :
                        this.APIError("0");
                        return lpHandler.elementList[ele];
                        break;
                case 'cmi.exit' :
                        this.APIError("405"); // write only
                        return "";
                        break;
                case 'cmi.launch_data' :
                        if( lpHandler.elementList[ele] == "" )
                        {
                            this.APIError("403"); // data model element value not initialized
                            return "";
                        }
                        else
                        {
                            this.APIError("0");
                            return lpHandler.elementList[ele];
                        }
                        break;
                case 'cmi.learner_id' :
                        this.APIError("0");
                        return lpHandler.elementList[ele];
                        break;
                case 'cmi.learner_name' :
                        this.APIError("0");
                        return lpHandler.elementList[ele];
                        break;
                case 'cmi.location' :
                        if( lpHandler.elementList[ele] == "" )
                        {
                            this.APIError("403"); // data model element value not initialized
                            return "";
                        }
                        else
                        {
                            this.APIError("0");
                            return lpHandler.elementList[ele];
                        }
                        break;
                case 'cmi.max_time_allowed' :
                        if( lpHandler.elementList[ele] == "" )
                        {
                            this.APIError("403"); // data model element value not initialized
                            return "";
                        }
                        else
                        {
                            this.APIError("0");
                            return lpHandler.elementList[ele];
                        }
                        break;
                case 'cmi.mode' :
                        this.APIError("0");
                        return lpHandler.elementList[ele];
                        break;
                case 'cmi.credit' :
                        this.APIError("0");
                        return lpHandler.elementList[ele];
                        break;
                case 'cmi.scaled_passing_score' :
                        this.APIError("0");
                        return lpHandler.elementList[ele];
                        break;
                case 'cmi.score._children' :
                        this.APIError("0");
                        return lpHandler.elementList[ele];
                        break;
                case 'cmi.score.scaled' :
                        if( lpHandler.elementList[ele] == "" )
                        {
                            this.APIError("403"); // data model element value not initialized
                            return "";
                        }
                        else
                        {
                            this.APIError("0");
                            return lpHandler.elementList[ele];
                        }
                        break;
                case 'cmi.score.min' :
                        if( lpHandler.elementList[ele] == "" )
                        {
                            this.APIError("403"); // data model element value not initialized
                            return "";
                        }
                        else
                        {
                            this.APIError("0");
                            return lpHandler.elementList[ele];
                        }
                        break;
                case 'cmi.score.max' :
                        if( lpHandler.elementList[ele] == "" )
                        {
                            this.APIError("403"); // data model element value not initialized
                            return "";
                        }
                        else
                        {
                            this.APIError("0");
                            return lpHandler.elementList[ele];
                        }
                        break;
                case 'cmi.score.raw' :
                        if( lpHandler.elementList[ele] == "" )
                        {
                            this.APIError("403"); // data model element value not initialized
                            return "";
                        }
                        else
                        {
                            this.APIError("0");
                            return lpHandler.elementList[ele];
                        }
                        break;
                case 'cmi.suspend_data' :
                        if( lpHandler.elementList[ele] == "" )
                        {
                            this.APIError("403"); // data model element value not initialized
                            return "";
                        }
                        else
                        {
                            this.APIError("0");
                            return lpHandler.elementList[ele];
                        }
                        break;
                case 'cmi.time_limit_action' :
                        this.APIError("0");
                        return lpHandler.elementList[ele];
                        break;
                case 'cmi.session_time' :
                        // find """something""" that could check that val correspond to : P[yY][mM][dD][T[hH][nM][s[.s]S]]
                        // sum this to total_time on terminate before commit
                        this.APIError("0");
                        return lpHandler.elementList[ele];
                        break;
                case 'cmi.total_time' :
                        if( lpHandler.elementList['cmi.session_time'] == "" )
                        {
                            this.APIError("0");
                            return 0;
                        }
                        this.APIError("0");
                        return lpHandler.elementList[ele];
                        break;
                default :
                        // not implemented error
                        this.APIError("401");
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
},

LMSSetValue : function (ele,val) {
        lpHandler.debug("LMSSetValue(" + ele +","+ val + ")", 1);
        if ( this._Initialized )
        {
            switch (ele)
            {
                case 'cmi._version' :
                        this.APIError("404"); // read only
                        return "false";
                        break;
                case 'cmi.comments_from_learner._children' :
                        this.APIError("404"); // read only
                        return "false";
                        break;
                case 'cmi.comments_from_learner._count' :
                        this.APIError("404"); // read only
                        return "false";
                        break;
                case 'cmi.comments_from_lms._children' :
                        this.APIError("404"); // read only
                        return "false";
                        break;
                case 'cmi.comments_from_lms._count' :
                        this.APIError("404"); // read only
                        return "false";
                        break;
                case 'cmi.completion_status' :
                        var upperCaseVal = val.toUpperCase();
                        if ( upperCaseVal != "COMPLETED" && upperCaseVal != "INCOMPLETE"
                            && upperCaseVal != "NOT ATTEMPTED" && upperCaseVal != "UNKNOWN" )
                        {
                            this.APIError("406"); // data model element type mismatch
                            return "false";
                        }
                        lpHandler.elementList[ele] = val;
                        this.APIError("0");
                        return "true";
                        break;
                case 'cmi.progress_measure' :
                        if( isNaN(parseFloat(val)) )
                        {
                            this.APIError("406"); // data model element type mismatch
                            return "false";
                        }
                        if( (val < 0) || (val > 1) )
                        {
                            this.APIError("407"); // data model element out of range
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
                            this.APIError("406"); // data model element type mismatch
                            return "false";
                        }
                        lpHandler.elementList[ele] = val;
                        this.APIError("0");
                        return "true";
                        break;

                case 'cmi.entry' :
                        this.APIError("404"); // read only
                        return "false";
                        break;
                case 'cmi.exit' :
                        var upperCaseVal = val.toUpperCase();
                        if ( upperCaseVal != "TIME-OUT" && upperCaseVal != "SUSPEND"
                            && upperCaseVal != "LOGOUT" && upperCaseVal != "" )
                        {
                            this.APIError("406"); // data model element type mismatch
                            return "false";
                        }
                        lpHandler.elementList[ele] = val;
                        this.APIError("0");
                        return "true";
                        break;
                case 'cmi.launch_data' :
                        this.APIError("404"); // read only
                        return "false";
                        break;
                case 'cmi.learner_id' :
                        this.APIError("404"); // read only
                        return "false";
                        break;
                case 'cmi.learner_name' :
                        this.APIError("404"); // read only
                        return "false";
                        break;
                case 'cmi.location' :
                        if( val.length > 255 )
                        {
                            this.APIError("405");
                            return "false";
                        }
                        lpHandler.elementList[ele] = val;
                        this.APIError("0");
                        return "true";
                        break;
                case 'cmi.max_time_allowed' :
                        this.APIError("404"); // read only
                        return "false";
                        break;
                case 'cmi.mode' :
                        this.APIError("404"); // read only
                        return "false";
                        break;
                case 'cmi.credit' :
                        this.APIError("404"); // read only
                        return "false";
                        break;
                case 'cmi.scaled_passing_score' :
                        this.APIError("404"); // read only
                        return "false";
                        break;
                case 'cmi.score._children' :
                        this.APIError("404"); // read only
                        return "false";
                        break;
                case 'cmi.score.scaled' :
                        if( isNaN(parseFloat(val)) )
                        {
                            this.APIError("406"); // data model element type mismatch
                            return "false";
                        }
                        if( (val < 0) || (val > 1) )
                        {
                            this.APIError("407"); // data model element out of range
                            return "false";
                        }
                        lpHandler.elementList[ele] = val;
                        return "true";
                        break;
                case 'cmi.score.min' :
                        if( isNaN(parseFloat(val)) )
                        {
                            this.APIError("406"); // data model element type mismatch
                            return "false";
                        }
                        if( (val < 0) || (val > 1) )
                        {
                            this.APIError("407"); // data model element out of range
                            return "false";
                        }
                        lpHandler.elementList[ele] = val;
                        return "true";
                        break;
                case 'cmi.score.max' :
                        if( isNaN(parseFloat(val)) )
                        {
                            this.APIError("406"); // data model element type mismatch
                            return "false";
                        }
                        if( (val < 0) || (val > 1) )
                        {
                            this.APIError("407"); // data model element out of range
                            return "false";
                        }
                        lpHandler.elementList[ele] = val;
                        return "true";
                        break;
                case 'cmi.score.raw' :
                        if( isNaN(parseFloat(val)) )
                        {
                            this.APIError("406"); // data model element type mismatch
                            return "false";
                        }
                        if( (val < 0) || (val > 1) )
                        {
                            this.APIError("407"); // data model element out of range
                            return "false";
                        }
                        lpHandler.elementList[ele] = val;
                        return "true";
                        break;
                case 'cmi.session_time' :
                        // find """something""" that could check that val correspond to : P[yY][mM][dD][T[hH][nM][s[.s]S]]
                        lpHandler.elementList[ele] = val;
                        this.APIError("0");
                        return "true";
                        break;
                case 'cmi.total_time' :
                        this.APIError("404"); // read only
                        return "false";
                        break;
                case 'cmi.suspend_data' :
                        lpHandler.elementList[ele] = val;
                        this.APIError("0");
                        return "true";
                        break;
                case 'cmi.time_limit_action' :
                        this.APIError("404"); // read only
                        return "false";
                        break;
                default :
                        // not implemented error
                        this.APIError("401");
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
},

LMSCommit : function (arg) {
        lpHandler.debug("LMScommit()", 1);
        if ( this._Initialized ) {
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
},


// ====================================================
// State Management
//
LMSGetLastError : function () {
        lpHandler.debug("LMSGetLastError() : returns " + lpHandler.APILastError, 1);

        return lpHandler.APILastError;
},

LMSGetErrorString : function (num) {
        lpHandler.debug("LMSGetErrorString(" + num +") : returns " + errCodes[num], 1);

        return errCodes[num];

},

LMSGetDiagnostic : function (num) {
        lpHandler.debug("LMSGetDiagnostic(" + num + ") : returns " + errDiagn[num], 1);

        if ( num == "" ) num = lpHandler.APILastError;
        return errDiagn[num];
},


// ====================================================
// Private
//
APIError : function (num) {
        lpHandler.APILastError = num;
}
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
// CMI Elements and Values
//
// see in apiData.php




