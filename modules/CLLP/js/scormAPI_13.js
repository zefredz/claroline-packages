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
	    CMITimespan : '^P(\\d+Y)?(\\d+M)?(\\d+D)?(T(((\\d+H)(\\d+M)?(\\d+(\.\\d{1,2})?S)?)|((\\d+M)(\\d+(\.\\d{1,2})?S)?)|((\\d+(\.\\d{1,2})?S))))?$',
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
	    CMIExit : '^time-out$|^suspend$|^logout$|^normal$|^$',
	    CMIType : '^true-false$|^choice$|^(long-)?fill-in$|^matching$|^performance$|^sequencing$|^likert$|^numeric$|^other$',
	    CMIResult : '^correct$|^incorrect$|^unanticipated$|^neutral$|^-?([0-9]{1,4})(\\.[0-9]{1,18})?$',
	    NAVEvent : '^previous$|^continue$|^exit$|^exitAll$|^abandon$|^abandonAll$|^suspendAll$|^{target:\\S{0,200}[a-zA-Z0-9]}choice$',
	    NAVBoolean : '^unknown$|^true$|^false$',
	    NAVTarget : '^previous$|^continue$|^choice.{target:\\S{0,200}[a-zA-Z0-9]}$',

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
		        'cmi._children':{'defaultvalue': this._childrenOf['cmi'], 'mod':'r'},
		        'cmi.comments_from_learner._children':{'defaultvalue': this._childrenOf['cmi.comments_from_learner'], 'mod':'r'},


		        'cmi._version':{'defaultvalue':'1.0', 'mod':'r'}
		    };
		    /*
		        'cmi.comments_from_learner._count':{'mod':'r', 'defaultvalue':'0'},
		        'cmi.comments_from_learner.n.comment':{'format':CMILangString4000, 'mod':'rw'},
		        'cmi.comments_from_learner.n.location':{'format':CMIString250, 'mod':'rw'},
		        'cmi.comments_from_learner.n.timestamp':{'format':CMITime, 'mod':'rw'},
		        'cmi.comments_from_lms._children':{'defaultvalue': this._childrenOf['cmi.comments_from_lms'], 'mod':'r'},
		        'cmi.comments_from_lms._count':{'mod':'r', 'defaultvalue':'0'},
		        'cmi.comments_from_lms.n.comment':{'format':CMILangString4000, 'mod':'r'},
		        'cmi.comments_from_lms.n.location':{'format':CMIString250, 'mod':'r'},
		        'cmi.comments_from_lms.n.timestamp':{'format':CMITime, 'mod':'r'},
		        'cmi.completion_status':{'defaultvalue':'<?php echo isset($userdata->{'cmi.completion_status'})?$userdata->{'cmi.completion_status'}:'unknown' ?>', 'format':CMICStatus, 'mod':'rw'},
		        'cmi.completion_threshold':{'defaultvalue':<?php echo isset($userdata->threshold)?'\''.$userdata->threshold.'\'':'null' ?>, 'mod':'r'},
		        'cmi.credit':{'defaultvalue':'<?php echo isset($userdata->credit)?$userdata->credit:'' ?>', 'mod':'r'},
		        'cmi.entry':{'defaultvalue':'<?php echo $userdata->entry ?>', 'mod':'r'},
		        'cmi.exit':{'defaultvalue':'<?php echo isset($userdata->{'cmi.exit'})?$userdata->{'cmi.exit'}:'' ?>', 'format':CMIExit, 'mod':'w'},
		        'cmi.interactions._children':{'defaultvalue': this._childrenOf['cmi.interactions'], 'mod':'r'},
		        'cmi.interactions._count':{'mod':'r', 'defaultvalue':'0'},
		        'cmi.interactions.n.id':{'pattern':CMIIndex, 'format':CMILongIdentifier, 'mod':'rw'},
		        'cmi.interactions.n.type':{'pattern':CMIIndex, 'format':CMIType, 'mod':'rw'},
		        'cmi.interactions.n.objectives._count':{'pattern':CMIIndex, 'mod':'r', 'defaultvalue':'0'},
		        'cmi.interactions.n.objectives.n.id':{'pattern':CMIIndex, 'format':CMILongIdentifier, 'mod':'rw'},
		        'cmi.interactions.n.timestamp':{'pattern':CMIIndex, 'format':CMITime, 'mod':'rw'},
		        'cmi.interactions.n.correct_responses._count':{'defaultvalue':'0', 'pattern':CMIIndex, 'mod':'r'},
		        'cmi.interactions.n.correct_responses.n.pattern':{'pattern':CMIIndex, 'format':CMIFeedback, 'mod':'rw'},
		        'cmi.interactions.n.weighting':{'pattern':CMIIndex, 'format':CMIDecimal, 'mod':'rw'},
		        'cmi.interactions.n.learner_response':{'pattern':CMIIndex, 'format':CMIFeedback, 'mod':'rw'},
		        'cmi.interactions.n.result':{'pattern':CMIIndex, 'format':CMIResult, 'mod':'rw'},
		        'cmi.interactions.n.latency':{'pattern':CMIIndex, 'format':CMITimespan, 'mod':'rw'},
		        'cmi.interactions.n.description':{'pattern':CMIIndex, 'format':CMILangString250, 'mod':'rw'},
		        'cmi.launch_data':{'defaultvalue':<?php echo isset($userdata->datafromlms)?'\''.$userdata->datafromlms.'\'':'null' ?>, 'mod':'r'},
		        'cmi.learner_id':{'defaultvalue':'<?php echo $userdata->student_id ?>', 'mod':'r'},
		        'cmi.learner_name':{'defaultvalue':'<?php echo addslashes($userdata->student_name) ?>', 'mod':'r'},
		        'cmi.learner_preference._children':{'defaultvalue': this._childrenOf['cmi.learner_preference'], 'mod':'r'},
		        'cmi.learner_preference.audio_level':{'defaultvalue':'1', 'format':CMIDecimal, 'range':audio_range, 'mod':'rw'},
		        'cmi.learner_preference.language':{'defaultvalue':'', 'format':CMILang, 'mod':'rw'},
		        'cmi.learner_preference.delivery_speed':{'defaultvalue':'1', 'format':CMIDecimal, 'range':speed_range, 'mod':'rw'},
		        'cmi.learner_preference.audio_captioning':{'defaultvalue':'0', 'format':CMISInteger, 'range':text_range, 'mod':'rw'},
		        'cmi.location':{'defaultvalue':<?php echo isset($userdata->{'cmi.location'})?'\''.$userdata->{'cmi.location'}.'\'':'null' ?>, 'format':CMIString1000, 'mod':'rw'},
		        'cmi.max_time_allowed':{'defaultvalue':<?php echo isset($userdata->maxtimeallowed)?'\''.$userdata->maxtimeallowed.'\'':'null' ?>, 'mod':'r'},
		        'cmi.mode':{'defaultvalue':'<?php echo $userdata->mode ?>', 'mod':'r'},
		        'cmi.objectives._children':{'defaultvalue': this._childrenOf['cmi.objectives'], 'mod':'r'},
		        'cmi.objectives._count':{'mod':'r', 'defaultvalue':'0'},
		        'cmi.objectives.n.id':{'pattern':CMIIndex, 'format':CMILongIdentifier, 'mod':'rw'},
		        'cmi.objectives.n.score._children':{'defaultvalue': this._childrenOf['cmi.score'], 'pattern':CMIIndex, 'mod':'r'},
		        'cmi.objectives.n.score.scaled':{'defaultvalue':null, 'pattern':CMIIndex, 'format':CMIDecimal, 'range':scaled_range, 'mod':'rw'},
		        'cmi.objectives.n.score.raw':{'defaultvalue':null, 'pattern':CMIIndex, 'format':CMIDecimal, 'mod':'rw'},
		        'cmi.objectives.n.score.min':{'defaultvalue':null, 'pattern':CMIIndex, 'format':CMIDecimal, 'mod':'rw'},
		        'cmi.objectives.n.score.max':{'defaultvalue':null, 'pattern':CMIIndex, 'format':CMIDecimal, 'mod':'rw'},
		        'cmi.objectives.n.success_status':{'defaultvalue':'unknown', 'pattern':CMIIndex, 'format':CMISStatus, 'mod':'rw'},
		        'cmi.objectives.n.completion_status':{'defaultvalue':'unknown', 'pattern':CMIIndex, 'format':CMICStatus, 'mod':'rw'},
		        'cmi.objectives.n.progress_measure':{'defaultvalue':null, 'format':CMIDecimal, 'range':progress_range, 'mod':'rw'},
		        'cmi.objectives.n.description':{'pattern':CMIIndex, 'format':CMILangString250, 'mod':'rw'},
		        'cmi.progress_measure':{'defaultvalue':<?php echo isset($userdata->{'cmi.progess_measure'})?'\''.$userdata->{'cmi.progress_measure'}.'\'':'null' ?>, 'format':CMIDecimal, 'range':progress_range, 'mod':'rw'},
		        'cmi.scaled_passing_score':{'defaultvalue':<?php echo isset($userdata->{'cmi.scaled_passing_score'})?'\''.$userdata->{'cmi.scaled_passing_score'}.'\'':'null' ?>, 'format':CMIDecimal, 'range':scaled_range, 'mod':'r'},
		        'cmi.score._children':{'defaultvalue': this._childrenOf['cmi.score'], 'mod':'r'},
		        'cmi.score.scaled':{'defaultvalue':<?php echo isset($userdata->{'cmi.score.scaled'})?'\''.$userdata->{'cmi.score.scaled'}.'\'':'null' ?>, 'format':CMIDecimal, 'range':scaled_range, 'mod':'rw'},
		        'cmi.score.raw':{'defaultvalue':<?php echo isset($userdata->{'cmi.score.raw'})?'\''.$userdata->{'cmi.score.raw'}.'\'':'null' ?>, 'format':CMIDecimal, 'mod':'rw'},
		        'cmi.score.min':{'defaultvalue':<?php echo isset($userdata->{'cmi.score.min'})?'\''.$userdata->{'cmi.score.min'}.'\'':'null' ?>, 'format':CMIDecimal, 'mod':'rw'},
		        'cmi.score.max':{'defaultvalue':<?php echo isset($userdata->{'cmi.score.max'})?'\''.$userdata->{'cmi.score.max'}.'\'':'null' ?>, 'format':CMIDecimal, 'mod':'rw'},
		        'cmi.session_time':{'format':CMITimespan, 'mod':'w', 'defaultvalue':'PT0H0M0S'},
		        'cmi.success_status':{'defaultvalue':'<?php echo isset($userdata->{'cmi.success_status'})?$userdata->{'cmi.success_status'}:'unknown' ?>', 'format':CMISStatus, 'mod':'rw'},
		        'cmi.suspend_data':{'defaultvalue':<?php echo isset($userdata->{'cmi.suspend_data'})?'\''.$userdata->{'cmi.suspend_data'}.'\'':'null' ?>, 'format':CMIString64000, 'mod':'rw'},
		        'cmi.time_limit_action':{'defaultvalue':<?php echo isset($userdata->timelimitaction)?'\''.$userdata->timelimitaction.'\'':'null' ?>, 'mod':'r'},
		        'cmi.total_time':{'defaultvalue':'<?php echo isset($userdata->{'cmi.total_time'})?$userdata->{'cmi.total_time'}:'PT0H0M0S' ?>', 'mod':'r'},
		        'adl.nav.request':{'defaultvalue':'_none_', 'format':NAVEvent, 'mod':'rw'}
		    };*/
		},

	    // ====================================================
	    // Execution State
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
	                    errorCode = "113";
	                } else {
	                    errorCode = "112";
	                }
	        	}
	        } else {
	            errorCode = "201";
	        }
	        return "false";
	    },

	    // ====================================================
	    // Data Transfer
	    //
	    GetValue : function (ele) {
	            lpHandler.debug("LMSGetValue(" + ele + ")", 1);
	            if ( this._Initialized )
	            {
	            	this._APIError("0");

	                switch (ele)
	                {
	                	case 'cmi._children' :
	                			return this._datamodel.elementList[ele];
	                			break;
	                    case 'cmi._version' :
	                            return lpHandler.elementList[ele];
	                            break;
	                    case 'cmi.comments_from_learner._children' :
	                            return lpHandler.elementList[ele];
	                            break;
	                    case 'cmi.comments_from_learner._count' :
	                            return lpHandler.elementList[ele].length;
	                            break;
	                    // TODO cmi.comment_from_learner.n.comment/location/timestamp
	                    case 'cmi.comments_from_lms._children' :
	                            return lpHandler.elementList[ele];
	                            break;
	                    case 'cmi.comments_from_lms._count' :
	                            return lpHandler.elementList[ele].length;
	                            break;
	                    // TODO cmi.comment_from_lms.n.comment/location/timestamp
	                    case 'cmi.completion_status' :  // TODO handle completion_threshold and completion_status and progress_measure
	                            return lpHandler.elementList[ele];
	                            break;
	                    case 'cmi.completion_threshold' :
	                    		return lpHandler.elementList[ele];
	                    		break;
						case 'cmi.credit' :
	                            return lpHandler.elementList[ele];
	                            break;
	                    case 'cmi.entry' :
	                            this._APIError("0");
	                            return lpHandler.elementList[ele];
	                            break;
	                    case 'cmi.exit' :
	                            this._APIError("405"); // write only
	                            return "";
	                            break;
	                    case 'cmi.launch_data' :
	                            if( lpHandler.elementList[ele] == "" )
	                            {
	                                this._APIError("403"); // data model element value not initialized
	                                return "";
	                            }
	                            else
	                            {
	                                return lpHandler.elementList[ele];
	                            }
	                            break;
	                    case 'cmi.progress_measure' :
	                            if( lpHandler.elementList[ele] == "" )
	                            {
	                                this._APIError("403"); // data model element value not initialized
	                                return "";
	                            }
	                            else
	                            {
	                                return lpHandler.elementList[ele];
	                            }
	                            break;
	                    case 'cmi.success_status' :
	                    // TODO compute result
	                            break;
	                    // todo threshold, measure, status


	                    case 'cmi.learner_id' :
	                            this._APIError("0");
	                            return lpHandler.elementList[ele];
	                            break;
	                    case 'cmi.learner_name' :
	                            this._APIError("0");
	                            return lpHandler.elementList[ele];
	                            break;
	                    case 'cmi.location' :
	                            if( lpHandler.elementList[ele] == "" )
	                            {
	                                this._APIError("403"); // data model element value not initialized
	                                return "";
	                            }
	                            else
	                            {
	                                this._APIError("0");
	                                return lpHandler.elementList[ele];
	                            }
	                            break;
	                    case 'cmi.max_time_allowed' :
	                            if( lpHandler.elementList[ele] == "" )
	                            {
	                                this._APIError("403"); // data model element value not initialized
	                                return "";
	                            }
	                            else
	                            {
	                                this._APIError("0");
	                                return lpHandler.elementList[ele];
	                            }
	                            break;
	                    case 'cmi.mode' :
	                            this._APIError("0");
	                            return lpHandler.elementList[ele];
	                            break;

	                    case 'cmi.scaled_passing_score' :
	                            this._APIError("0");
	                            return lpHandler.elementList[ele];
	                            break;
	                    case 'cmi.score._children' :
	                            this._APIError("0");
	                            return lpHandler.elementList[ele];
	                            break;
	                    case 'cmi.score.scaled' :
	                            if( lpHandler.elementList[ele] == "" )
	                            {
	                                this._APIError("403"); // data model element value not initialized
	                                return "";
	                            }
	                            else
	                            {
	                                this._APIError("0");
	                                return lpHandler.elementList[ele];
	                            }
	                            break;
	                    case 'cmi.score.min' :
	                            if( lpHandler.elementList[ele] == "" )
	                            {
	                                this._APIError("403"); // data model element value not initialized
	                                return "";
	                            }
	                            else
	                            {
	                                this._APIError("0");
	                                return lpHandler.elementList[ele];
	                            }
	                            break;
	                    case 'cmi.score.max' :
	                            if( lpHandler.elementList[ele] == "" )
	                            {
	                                this._APIError("403"); // data model element value not initialized
	                                return "";
	                            }
	                            else
	                            {
	                                this._APIError("0");
	                                return lpHandler.elementList[ele];
	                            }
	                            break;
	                    case 'cmi.score.raw' :
	                            if( lpHandler.elementList[ele] == "" )
	                            {
	                                this._APIError("403"); // data model element value not initialized
	                                return "";
	                            }
	                            else
	                            {
	                                this._APIError("0");
	                                return lpHandler.elementList[ele];
	                            }
	                            break;
	                    case 'cmi.suspend_data' :
	                            if( lpHandler.elementList[ele] == "" )
	                            {
	                                this._APIError("403"); // data model element value not initialized
	                                return "";
	                            }
	                            else
	                            {
	                                this._APIError("0");
	                                return lpHandler.elementList[ele];
	                            }
	                            break;
	                    case 'cmi.time_limit_action' :
	                            this._APIError("0");
	                            return lpHandler.elementList[ele];
	                            break;
	                    case 'cmi.session_time' :
	                            // find """something""" that could check that val correspond to : P[yY][mM][dD][T[hH][nM][s[.s]S]]
	                            // sum this to total_time on terminate before commit
	                            this._APIError("0");
	                            return lpHandler.elementList[ele];
	                            break;
	                    case 'cmi.total_time' :
	                            if( lpHandler.elementList['cmi.session_time'] == "" )
	                            {
	                                this._APIError("0");
	                                return 0;
	                            }
	                            this._APIError("0");
	                            return lpHandler.elementList[ele];
	                            break;
	                    default :
	                            // not implemented error
	                            this._APIError("401");
	                            return "";
	                            break;

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
	                switch (ele)
	                {
	                    case 'cmi._version' :
	                            this._APIError("404"); // read only
	                            return "false";
	                            break;
	                    case 'cmi.comments_from_learner._children' :
	                            this._APIError("404"); // read only
	                            return "false";
	                            break;
	                    case 'cmi.comments_from_learner._count' :
	                            this._APIError("404"); // read only
	                            return "false";
	                            break;
	                    case 'cmi.comments_from_lms._children' :
	                            this._APIError("404"); // read only
	                            return "false";
	                            break;
	                    case 'cmi.comments_from_lms._count' :
	                            this._APIError("404"); // read only
	                            return "false";
	                            break;
	                    case 'cmi.completion_status' :
	                            var upperCaseVal = val.toUpperCase();
	                            if ( upperCaseVal != "COMPLETED" && upperCaseVal != "INCOMPLETE"
	                                && upperCaseVal != "NOT ATTEMPTED" && upperCaseVal != "UNKNOWN" )
	                            {
	                                this._APIError("406"); // data model element type mismatch
	                                return "false";
	                            }
	                            lpHandler.elementList[ele] = val;
	                            this._APIError("0");
	                            return "true";
	                            break;
	                    case 'cmi.progress_measure' :
	                            if( isNaN(parseFloat(val)) )
	                            {
	                                this._APIError("406"); // data model element type mismatch
	                                return "false";
	                            }
	                            if( (val < 0) || (val > 1) )
	                            {
	                                this._APIError("407"); // data model element out of range
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
	                                this._APIError("406"); // data model element type mismatch
	                                return "false";
	                            }
	                            lpHandler.elementList[ele] = val;
	                            this._APIError("0");
	                            return "true";
	                            break;

	                    case 'cmi.entry' :
	                            this._APIError("404"); // read only
	                            return "false";
	                            break;
	                    case 'cmi.exit' :
	                            var upperCaseVal = val.toUpperCase();
	                            if ( upperCaseVal != "TIME-OUT" && upperCaseVal != "SUSPEND"
	                                && upperCaseVal != "LOGOUT" && upperCaseVal != "" )
	                            {
	                                this._APIError("406"); // data model element type mismatch
	                                return "false";
	                            }
	                            lpHandler.elementList[ele] = val;
	                            this._APIError("0");
	                            return "true";
	                            break;
	                    case 'cmi.launch_data' :
	                            this._APIError("404"); // read only
	                            return "false";
	                            break;
	                    case 'cmi.learner_id' :
	                            this._APIError("404"); // read only
	                            return "false";
	                            break;
	                    case 'cmi.learner_name' :
	                            this._APIError("404"); // read only
	                            return "false";
	                            break;
	                    case 'cmi.location' :
	                            if( val.length > 255 )
	                            {
	                                this._APIError("405");
	                                return "false";
	                            }
	                            lpHandler.elementList[ele] = val;
	                            this._APIError("0");
	                            return "true";
	                            break;
	                    case 'cmi.max_time_allowed' :
	                            this._APIError("404"); // read only
	                            return "false";
	                            break;
	                    case 'cmi.mode' :
	                            this._APIError("404"); // read only
	                            return "false";
	                            break;
	                    case 'cmi.credit' :
	                            this._APIError("404"); // read only
	                            return "false";
	                            break;
	                    case 'cmi.scaled_passing_score' :
	                            this._APIError("404"); // read only
	                            return "false";
	                            break;
	                    case 'cmi.score._children' :
	                            this._APIError("404"); // read only
	                            return "false";
	                            break;
	                    case 'cmi.score.scaled' :
	                            if( isNaN(parseFloat(val)) )
	                            {
	                                this._APIError("406"); // data model element type mismatch
	                                return "false";
	                            }
	                            if( (val < 0) || (val > 1) )
	                            {
	                                this._APIError("407"); // data model element out of range
	                                return "false";
	                            }
	                            lpHandler.elementList[ele] = val;
	                            return "true";
	                            break;
	                    case 'cmi.score.min' :
	                            if( isNaN(parseFloat(val)) )
	                            {
	                                this._APIError("406"); // data model element type mismatch
	                                return "false";
	                            }
	                            if( (val < 0) || (val > 1) )
	                            {
	                                this._APIError("407"); // data model element out of range
	                                return "false";
	                            }
	                            lpHandler.elementList[ele] = val;
	                            return "true";
	                            break;
	                    case 'cmi.score.max' :
	                            if( isNaN(parseFloat(val)) )
	                            {
	                                this._APIError("406"); // data model element type mismatch
	                                return "false";
	                            }
	                            if( (val < 0) || (val > 1) )
	                            {
	                                this._APIError("407"); // data model element out of range
	                                return "false";
	                            }
	                            lpHandler.elementList[ele] = val;
	                            return "true";
	                            break;
	                    case 'cmi.score.raw' :
	                            if( isNaN(parseFloat(val)) )
	                            {
	                                this._APIError("406"); // data model element type mismatch
	                                return "false";
	                            }
	                            if( (val < 0) || (val > 1) )
	                            {
	                                this._APIError("407"); // data model element out of range
	                                return "false";
	                            }
	                            lpHandler.elementList[ele] = val;
	                            return "true";
	                            break;
	                    case 'cmi.session_time' :
	                            // find """something""" that could check that val correspond to : P[yY][mM][dD][T[hH][nM][s[.s]S]]
	                            lpHandler.elementList[ele] = val;
	                            this._APIError("0");
	                            return "true";
	                            break;
	                    case 'cmi.total_time' :
	                            this._APIError("404"); // read only
	                            return "false";
	                            break;
	                    case 'cmi.suspend_data' :
	                            lpHandler.elementList[ele] = val;
	                            this._APIError("0");
	                            return "true";
	                            break;
	                    case 'cmi.time_limit_action' :
	                            this._APIError("404"); // read only
	                            return "false";
	                            break;
	                    default :
	                            // not implemented error
	                            this._APIError("401");
	                            return "";
	                            break;

	                }
	            }
	            else
	            {
	                    // not initialized error
	                    this._APIError("301");
	                    return "false";
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
	                            lpHandler.commit();

	                            return "true";
	                    }
	            } else {
	                    this._APIError("301");
	                    return "false";
	            }
	    },


	    // ====================================================
	    // State Management
	    //
	    GetLastError : function () {
            lpHandler.debug("LMSGetLastError() : returns " + this.APILastError, 1);

            return this.APILastError;
	    },

	    GetErrorString : function (num) {

	        if (param != "") {
	            var errorString = "";
	            switch(param) {
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