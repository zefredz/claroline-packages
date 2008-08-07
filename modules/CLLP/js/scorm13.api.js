    // ====================================================
    // API Class Constructor

	var API_1484_11 = {

	    // ====================================================
	    // Private
	    //

	    _Initialized : false,
    	_Terminated : false,
    	_lastError : "0",
	    _lastDiagnostic : "",

	    _datamodel : {},


	    // Standard Data Type Definitions
	    CMIString200 : '^.{0,200}$',
	    CMIString250 : '^.{0,250}$',
	    CMILangString250 : '^(\{lang:([a-zA-Z]{2,3}|i|x)(\-[a-zA-Z0-9\-]{2,8})?\})?([^\{].{0,250}$)?',
	    CMIString1000 : '^.{0,1500}$',
	    CMIString4000 : '^.{0,4000}$',
	    CMILangString4000 : '^(\{lang:([a-zA-Z]{2,3}|i|x)(\-[a-zA-Z0-9\-]{2,8})?\})?([^\{].{0,4000}$)?',
	    CMIString64000 : '^.{0,64000}$',
	    CMILang : '^([a-zA-Z]{2,3}|i|x)(\-[a-zA-Z0-9\-]{2,8})?$|^$',
	    CMITime : '^(19[7-9]{1}[0-9]{1}|20[0-2]{1}[0-9]{1}|203[0-8]{1})((-(0[1-9]{1}|1[0-2]{1}))((-(0[1-9]{1}|[1-2]{1}[0-9]{1}|3[0-1]{1}))(T([0-1]{1}[0-9]{1}|2[0-3]{1})((:[0-5]{1}[0-9]{1})((:[0-5]{1}[0-9]{1})((\\.[0-9]{1,2})((Z|([+|-]([0-1]{1}[0-9]{1}|2[0-3]{1})))(:[0-5]{1}[0-9]{1})?)?)?)?)?)?)?)?$',
	    CMITimespan : '^P(\\d+Y)?(\\d+M)?(\\d+D)?(T(((\\d+H)(\\d+M)?(\\d+(\\.\\d{1,2})?S)?)|((\\d+M)(\\d+(\\.\\d{1,2})?S)?)|((\\d+(\\.\\d{1,2})?S))))?$',
	    CMIInteger : '^\\d+$',
	    CMISInteger : '^-?([0-9]+)$',
	    CMIDecimal : '^-?([0-9]{1,4})(\\.[0-9]{1,18})?$',
	    CMIIdentifier : '^\\S{0,200}[a-zA-Z0-9]$',
	    CMILongIdentifier : '^\\S{0,4000}[a-zA-Z0-9]$',
	    CMIFeedback : this.CMIString200, // This must be redefined
	    CMIIndex : '[._](\\d+).',
	    CMIIndexStore : '.N(\\d+).',
	    CMICStatus : '^completed$|^incomplete$|^not attempted$|^unknown$',
	    CMISStatus : '^passed$|^failed$|^unknown$',
	    CMICredit : '^credit$|^no-credit$',
	    CMIExit : '^time-out$|^suspend$|^logout$|^normal$|^$',
	    CMIEntry : '^ab-initio$|^resume$|^$',
	    CMIType : '^true-false$|^choice$|^(long-)?fill-in$|^matching$|^performance$|^sequencing$|^likert$|^numeric$|^other$',
	    CMIResult : '^correct$|^incorrect$|^unanticipated$|^neutral$|^-?([0-9]{1,4})(\\.[0-9]{1,18})?$',
	    NAVEvent : '^previous$|^continue$|^exit$|^exitAll$|^abandon$|^abandonAll$|^suspendAll$|^{target:\\S{0,200}[a-zA-Z0-9]}choice$',
	    NAVBoolean : '^unknown$|^true$|^false$',
	    NAVTarget : '^previous$|^continue$|^choice.{target:\\S{0,200}[a-zA-Z0-9]}$',

	    // Data ranges
	    _rangeOf : {
	    	"scaled" : "-1#1",
		    "audio" : "0#*",
		    "speed" : "0#*",
		    "text" : "-1#1",
		    "progress" : "0#1"
	    },

	    // Children lists
	    _childrenOf : {
	    	"cmi" : "_version, comments_from_learner, comments_from_lms, completion_status, credit, entry, exit, interactions, launch_data, learner_id, learner_name, learner_preference, location, max_time_allowed, mode, objectives, progress_measure, scaled_passing_score, score, session_time, success_status, suspend_data, time_limit_action, total_time",
	    	"cmi.comments_from_learner" : "comment,location,timestamp",
			"cmi.comments_from_lms" : "comment,location,timestamp",
			"cmi.score" : "scaled,min,max,raw",
			"cmi.objectives" : "id,score,success_status,completion_status,progress_measure,description",
			"cmi.interactions" : "id,type,objectives,timestamp,correct_responses,weighting,learner_response,result,latency,description",
			"cmi.learner_preference" : "audio_level,language,delivery_speed,audio_captioning",
		},

		// make object ready to use in a fresh context
		init : function() {
			this._Initialized = false;
			this._Terminated = false;
			this._lastError = "0";
			this._lastDiagnostic = "";

			this._datamodel =  {
		        'cmi._children':{'value': this._childrenOf['cmi'], 'mod':'r'},
		        'cmi._version':{'value':'1.0', 'mod':'r'},
		        'cmi.comments_from_learner._children':{'value': this._childrenOf['cmi.comments_from_learner'], 'mod':'r'},
		        'cmi.comments_from_learner._count':{'value':'0', 'mod':'r'},

		        'cmi.comments_from_lms._children':{'value': this._childrenOf['cmi.comments_from_lms'], 'mod':'r'},
		        'cmi.comments_from_lms._count':{'value':'0', 'mod':'r'},

		        'cmi.completion_status':{'value':'unknown', 'format': this.CMICStatus, 'mod':'rw'},
		        'cmi.credit':{'value':'credit', 'format' : this.CMICredit, 'mod':'r'},
		        'cmi.entry':{'value':'ab-initio', 'format' : this.CMIEntry, 'mod':'r'},
		        'cmi.exit':{'value':'', 'format': this.CMIExit, 'mod':'w'},

		        'cmi.interactions._children':{'value': this._childrenOf['cmi.interactions'], 'mod':'r'},
		        'cmi.interactions._count':{'value':'0', 'mod':'r'},

		        'cmi.launch_data':{'value': null, 'mod':'r'},

		        'cmi.learner_id':{'value':'-1', 'mod':'r'},
		        'cmi.learner_name':{'value':'Anonymous User', 'mod':'r'},
		        'cmi.learner_preference._children':{'value': this._childrenOf['cmi.learner_preference'], 'mod':'r'},
		        'cmi.learner_preference.audio_level':{'value':'1', 'format': this.CMIDecimal, 'range': this._rangeOf['audio'], 'mod':'rw'},
		        'cmi.learner_preference.language':{'value':'', 'format': this.CMILang, 'mod':'rw'},
		        'cmi.learner_preference.delivery_speed':{'value':'1', 'format': this.CMIDecimal, 'range': this._rangeOf['speed'], 'mod':'rw'},
		        'cmi.learner_preference.audio_captioning':{'value':'0', 'format': this.CMISInteger, 'range': this._rangeOf['text'], 'mod':'rw'},

		        'cmi.location':{'value': null, 'format': this.CMIString1000, 'mod':'rw'},
		        'cmi.max_time_allowed':{'value': null, 'mod':'r'},
		        'cmi.mode':{'value':'normal', 'mod':'r'},

		        'cmi.objectives._children':{'value': this._childrenOf['cmi.objectives'], 'mod':'r'},
		        'cmi.objectives._count':{'value':'0', 'mod':'r'},

		        'cmi.progress_measure':{'value': null, 'format': this.CMIDecimal, 'range': this._rangeOf['progress'], 'mod':'rw'},

		        'cmi.scaled_passing_score':{'value': null, 'format': this.CMIDecimal, 'range': this._rangeOf['scaled'], 'mod':'r'},

		        'cmi.score._children':{'value': this._childrenOf['cmi.score'], 'mod':'r'},
		        'cmi.score.scaled':{'value':null, 'format': this.CMIDecimal, 'range': this._rangeOf['scaled'], 'mod':'rw'},
		        'cmi.score.raw':{'value': null, 'format': this.CMIDecimal, 'mod':'rw'},
		        'cmi.score.min':{'value': null, 'format': this.CMIDecimal, 'mod':'rw'},
		        'cmi.score.max':{'value': null, 'format': this.CMIDecimal, 'mod':'rw'},

		        'cmi.session_time':{'value':'PT0H0M0S', 'format': this.CMITimespan, 'mod':'w'},
		        'cmi.success_status':{'value': 'unknown', 'format': this.CMISStatus, 'mod':'rw'},
		        'cmi.suspend_data':{'value': null, 'format': this.CMIString64000, 'mod':'rw'},
		        'cmi.time_limit_action':{'value': 'continue, no message', 'mod':'r'},
		        'cmi.total_time':{'value': 'PT0H0M0S', 'mod':'r'},
		        'adl.nav.request':{'value': null, 'format': this.NAVEvent, 'mod': 'rw'}
		    };
		    /*
		        'cmi.completion_threshold':{'value':<?php echo isset($userdata->threshold)?'\''.$userdata->threshold.'\'':'null' ?>, 'mod':'r'},
		        'cmi.comments_from_learner.n.comment':{'format':CMILangString4000, 'mod':'rw'},
		        'cmi.comments_from_learner.n.location':{'format':CMIString250, 'mod':'rw'},
		        'cmi.comments_from_learner.n.timestamp':{'format':CMITime, 'mod':'rw'},
		        'cmi.comments_from_lms.n.comment':{'format':CMILangString4000, 'mod':'r'},
		        'cmi.comments_from_lms.n.location':{'format':CMIString250, 'mod':'r'},
		        'cmi.comments_from_lms.n.timestamp':{'format':CMITime, 'mod':'r'},
		        'cmi.interactions.n.id':{'pattern':CMIIndex, 'format':CMILongIdentifier, 'mod':'rw'},
		        'cmi.interactions.n.type':{'pattern':CMIIndex, 'format':CMIType, 'mod':'rw'},
		        'cmi.interactions.n.objectives._count':{'pattern':CMIIndex, 'mod':'r', 'value':'0'},
		        'cmi.interactions.n.objectives.n.id':{'pattern':CMIIndex, 'format':CMILongIdentifier, 'mod':'rw'},
		        'cmi.interactions.n.timestamp':{'pattern':CMIIndex, 'format':CMITime, 'mod':'rw'},
		        'cmi.interactions.n.correct_responses._count':{'value':'0', 'pattern':CMIIndex, 'mod':'r'},
		        'cmi.interactions.n.correct_responses.n.pattern':{'pattern':CMIIndex, 'format':CMIFeedback, 'mod':'rw'},
		        'cmi.interactions.n.weighting':{'pattern':CMIIndex, 'format':CMIDecimal, 'mod':'rw'},
		        'cmi.interactions.n.learner_response':{'pattern':CMIIndex, 'format':CMIFeedback, 'mod':'rw'},
		        'cmi.interactions.n.result':{'pattern':CMIIndex, 'format':CMIResult, 'mod':'rw'},
		        'cmi.interactions.n.latency':{'pattern':CMIIndex, 'format':CMITimespan, 'mod':'rw'},
		        'cmi.interactions.n.description':{'pattern':CMIIndex, 'format':CMILangString250, 'mod':'rw'},
		        'cmi.objectives.n.id':{'pattern':CMIIndex, 'format':CMILongIdentifier, 'mod':'rw'},
		        'cmi.objectives.n.score._children':{'value': this._childrenOf['cmi.score'], 'pattern':CMIIndex, 'mod':'r'},
		        'cmi.objectives.n.score.scaled':{'value':null, 'pattern':CMIIndex, 'format':CMIDecimal, 'range':scaled_range, 'mod':'rw'},
		        'cmi.objectives.n.score.raw':{'value':null, 'pattern':CMIIndex, 'format':CMIDecimal, 'mod':'rw'},
		        'cmi.objectives.n.score.min':{'value':null, 'pattern':CMIIndex, 'format':CMIDecimal, 'mod':'rw'},
		        'cmi.objectives.n.score.max':{'value':null, 'pattern':CMIIndex, 'format':CMIDecimal, 'mod':'rw'},
		        'cmi.objectives.n.success_status':{'value':'unknown', 'pattern':CMIIndex, 'format':CMISStatus, 'mod':'rw'},
		        'cmi.objectives.n.completion_status':{'value':'unknown', 'pattern':CMIIndex, 'format':CMICStatus, 'mod':'rw'},
		        'cmi.objectives.n.progress_measure':{'value':null, 'format':CMIDecimal, 'range':progress_range, 'mod':'rw'},
		        'cmi.objectives.n.description':{'pattern':CMIIndex, 'format':CMILangString250, 'mod':'rw'},
		    };*/
		},

	    // ====================================================
	    // Session methods
	    //

	    // According to SCORM 1.3 reference :
	    //    - arg must be "" (empty string)
	    //    - return value : "true" or "false"
	    Initialize : function (arg) {
	        lpHandler.debug("LMSInitialize()", 1);

	        if ( arg == "" ) {
	        	if( ! this._Initialized && ! this._Terminated ) {

		            this._Initialized = true;
		            this._APIError("0");

		            return "true";
	        	} else {
	        		if( this._Initialized ) {
	        			this._APIError("103");
	        		} else {
	        			this._APIError("104");
	        		}
	        	}
	        } else {
				this._APIError("201");
	    	}
	            return "false";
	    },


	    // TODO Terminate : handling of nav request
	    // According to SCORM 1.3 reference
	    //    - arg must be "" (empty string)
	    //    - return value : "true" or "false"
	    Terminate : function (arg) {
	        lpHandler.debug("LMSTerminate()", 1);

	        if( arg == "" ) {
	        	if( this._Initialized && ! this._Terminated ) {

	            	this._Initialized = false;
	            	this._Terminated = true;
	            	this._APIError("0");

	            	lpHandler.commit();
	            	/* TODO check this part
	            	if (adl.nav.request != '_none_') {
	                    switch (adl.nav.request) {
	                        case 'continue':
	                            setTimeout('top.nextSCO();',500);
	                        break;
	                        case 'previous':
	                            setTimeout('top.prevSCO();',500);
	                        break;
	                        case 'choice':
	                        break;
	                        case 'exit':
	                        break;
	                        case 'exitAll':
	                        break;
	                        case 'abandon':
	                        break;
	                        case 'abandonAll':
	                        break;
	                    }
	                } else {
	                    if (<?php echo $scorm->auto ?> == 1) {
	                        setTimeout('top.nextSCO();',500);
	                    }
	                }
					*/
					// TODO if commit ok /// check that I have to commit in terminate ?
					return "true";
	            } else {
	                if( this._Terminated ) {
	                    this._APIError("113");
	                } else {
	                    this._APIError("112");
	                }
	        	}
	        } else {
	            this._APIError("201");
	        }
	        return "false";
	    },

	    // ====================================================
	    // Data Transfer methods
	    //
	    GetValue : function (ele) {
	            lpHandler.debug("LMSGetValue(" + ele + ")", 1);
	            if ( this._Initialized )
	            {
	            	element = eval(this._datamodel[ele]);

           			if( typeof element == 'undefined' )
           			{
           				this._APIError("401"); // Not implemented
           				return false;
           			}

           			if( element.mod == 'w' )
					{
						this._APIError("405");
						return false;
					}

					if( typeof element.value == 'undefined' || element.value == null )
					{
						this._APIError("403");
						return "";
					}
					else
					{
						this._APIError("0");
						return element.value;
					}



	            }
	            else
	            {
	                    // not initialized error
	                    this._APIError("301");
	                    return "false";
	            }
	    },

	    SetValue : function (ele,val) {
	            lpHandler.debug("LMSSetValue(" + ele +","+ val + ")", 1);

	            if ( this._Initialized )
	            {
					element = eval(this._datamodel[ele]);

					// exists ?
           			if( typeof element == 'undefined' )
           			{
           				this._APIError("401"); // Not implemented
           				return false;
           			}

					// writeable ?
					if( element.mod == 'r' )
					{
						this._APIError("404");
						return false;
					}

					// is format ok ?
					if( typeof element.format != 'undefined' )
					{

						expression = new RegExp(element.format, 'gi');
						lpHandler.debug(element.format, 1);
                        value = val + '';
                        matches = value.match(expression);
                        if( matches == null )
                        {
                        	this._APIError("406");
                        	return false;
                        }
                    }

                    // is range ok ?
					if( typeof element.range != 'undefined' )
					{
						var ranges = element.range.split('#');
						value = val*1.0;

						if( value < ranges[0] || ( value > ranges[1] && ranges[1] != '*' )  )
						{
							this._APIError("407");
							return false;
						}
					}

					// everything seems ok
					this._APIError("0");
					element.value = val;
					return true;

	            }
	            else
	            {
	                    // not initialized error
	                    this._APIError("301");
	                    return false;
	            }
	    },

	    Commit : function (arg)
	    {
	            lpHandler.debug("LMScommit()", 1);
	            if ( this._Initialized ) {
	                    if ( arg!="" ) {
	                            this._APIError("201");
	                            return "false";
	                    } else {
	                            this._APIError("0");
								// API should handle total time because server side will have to receive 1.2 data or 1.3 data
								//lpHandler.elementList['cmi.total_time'] = totalTime();
	                            lpHandler.commit(this._datamodel);

	                            return "true";
	                    }
	            } else {
	                    this._APIError("301");
	                    return "false";
	            }
	    },


	    // ====================================================
	    // Support methods
	    //
	    GetLastError : function () {
            lpHandler.debug("LMSGetLastError() : returns " + this.APILastError, 1);

            return this.APILastError;
	    },

	    GetErrorString : function (num) {

	        if (num != "") {
	            var errorString = "";
	            switch(num) {
	                case "0":
	                    errorString = "No error";
	                break;
	                case "101":
	                    errorString = "General exception";
	                break;
	                case "102":
	                    errorString = "General Inizialization Failure";
	                break;
	                case "103":
	                    errorString = "Already Initialized";
	                break;
	                case "104":
	                    errorString = "Content Instance Terminated";
	                break;
	                case "111":
	                    errorString = "General Termination Failure";
	                break;
	                case "112":
	                    errorString = "Termination Before Inizialization";
	                break;
	                case "113":
	                    errorString = "Termination After Termination";
	                break;
	                case "122":
	                    errorString = "Retrieve Data Before Initialization";
	                break;
	                case "123":
	                    errorString = "Retrieve Data After Termination";
	                break;
	                case "132":
	                    errorString = "Store Data Before Inizialization";
	                break;
	                case "133":
	                    errorString = "Store Data After Termination";
	                break;
	                case "142":
	                    errorString = "Commit Before Inizialization";
	                break;
	                case "143":
	                    errorString = "Commit After Termination";
	                break;
	                case "201":
	                    errorString = "General Argument Error";
	                break;
	                case "301":
	                    errorString = "General Get Failure";
	                break;
	                case "351":
	                    errorString = "General Set Failure";
	                break;
	                case "391":
	                    errorString = "General Commit Failure";
	                break;
	                case "401":
	                    errorString = "Undefinited Data Model";
	                break;
	                case "402":
	                    errorString = "Unimplemented Data Model Element";
	                break;
	                case "403":
	                    errorString = "Data Model Element Value Not Initialized";
	                break;
	                case "404":
	                    errorString = "Data Model Element Is Read Only";
	                break;
	                case "405":
	                    errorString = "Data Model Element Is Write Only";
	                break;
	                case "406":
	                    errorString = "Data Model Element Type Mismatch";
	                break;
	                case "407":
	                    errorString = "Data Model Element Value Out Of Range";
	                break;
	                case "408":
	                    errorString = "Data Model Dependency Not Established";
	                break;
	            }
	        } else {
	           errorString = "";
	        }

	        lpHandler.debug("LMSGetErrorString(" + num +") : returns " + errorString, 1);

	        return errorString;

	    },

	    GetDiagnostic : function (num) {
	            lpHandler.debug("LMSGetDiagnostic(" + num + ") : returns " + errDiagn[num], 1);
				// todo : do something here ^^

	            return "";
	    },

	    // ====================================================
	    // internal methods
	    //
	    _APIError : function (num) {
	            this.APILastError = num;
	    },

	    _APIDiagnostic : function (string) {
	            lastDiagnostic = string;
	    },


    } // end of api object




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

