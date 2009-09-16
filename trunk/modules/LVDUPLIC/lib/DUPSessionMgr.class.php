<?php
class DUPSessionMgr{
	public static $DUP_SESSION_SOURCE_COURSE 	= "DUPsource_course";
	public static $DUP_SESSION_TARGET_COURSE 	= "DUPtarget_course";
	public static $DUP_SESSION_TOOL_LIST 		= "DUPtool_list";
	public static $DUP_SESSION_ADMIN_CONTEXT	= "DUPadminContext";
	public static $DUP_SESSION_ADMIN_BACKURL	= "DUPadminBackURL";
	
	/**
	 * set the admin Context (Does the user come from admin Panel ?)
	 */
	public static function setAdminContext($fromAdmin){
	    $_SESSION[DUPSessionMgr::$DUP_SESSION_ADMIN_CONTEXT] = $fromAdmin;
	}
	/**
	 * get the admin Context (true = admin)
	 */
	public static function getAdminContext(){
	    return $_SESSION[DUPSessionMgr::$DUP_SESSION_ADMIN_CONTEXT];
	}
	/**
	 * set the admin back URL (if adminContext = true ) 
	 */
	public static function setAdminBackURL($adminBackURL){
	    $_SESSION[DUPSessionMgr::$DUP_SESSION_ADMIN_BACKURL] = $adminBackURL;
	}
	/**
	 * get the admin back URL (if adminContext = true ) 
	 */
	public static function getAdminBackURL(){
	    return $_SESSION[DUPSessionMgr::$DUP_SESSION_ADMIN_BACKURL];
	}
	
	/**
	 * set the source course Id
	 */
	public static function setSourceCourseData($courseData){
	    $_SESSION[DUPSessionMgr::$DUP_SESSION_SOURCE_COURSE] = $courseData;
	}
	/**
	 * get the source course data (if set)
	 */
	public static function getSourceCourseData(){
	    return $_SESSION[DUPSessionMgr::$DUP_SESSION_SOURCE_COURSE];
	}
	/**
	 * set the target course Data
	 */
	public static function setTargetCourseData($courseData){
	    $_SESSION[DUPSessionMgr::$DUP_SESSION_TARGET_COURSE] = $courseData;
	}
	/**
	 * get the target course Data (if set)
	 */
	public static function getTargetCourseData(){
	    return $_SESSION[DUPSessionMgr::$DUP_SESSION_TARGET_COURSE];
	}
	
	/**
	 * set the tools which need to be copied
	 * @param $toolList : array of string : each string is the label of a tool
	 */
	public static function setToolList($toolList){
	    $_SESSION[DUPSessionMgr::$DUP_SESSION_TOOL_LIST] = $toolList;
	}
	/**
	 * get the list of the tools which need to be copied
	 */
	public static function getToolList(){
	    return $_SESSION[DUPSessionMgr::$DUP_SESSION_TOOL_LIST];
	}
	
	/**
	 *  clear every data we have put in the session
	 */
	public static function clearDupDataFromSession(){
	    unset($_SESSION[DUPSessionMgr::$DUP_SESSION_SOURCE_COURSE]);
	    unset($_SESSION[DUPSessionMgr::$DUP_SESSION_TARGET_COURSE]);
	    unset($_SESSION[DUPSessionMgr::$DUP_SESSION_TOOL_LIST]);
	    unset($_SESSION[DUPSessionMgr::$DUP_SESSION_ADMIN_BACKURL]);
	    unset($_SESSION[DUPSessionMgr::$DUP_SESSION_ADMIN_CONTEXT]);
	
	}
	/**
	 * From course Data (array) to Course object
	 */
	public function arrayToCourse($array)
	{
		$res = new ClaroCourse();
		$res->courseId 			= $array['sysCode'];
        $res->title 			= $array['name'];
        $res->officialCode 		= $array['officialCode'];
		$res->titular 			= $array['titular'];
		$res->email 			= $array['email'];
		$res->category 			= $array['categoryCode'];
		$res->departmentName 	= $array['extLinkName'];
		$res->departmentUrl 	= $array['extLinkUrl'];
		$res->language 			= $array['language'];
		$res->access 			= $array['visibility'];
		$res->enrolment 		= $array['registrationAllowed'];
		$res->enrolmentKey 		= $array['enrollmentKey'];	
		return $res;
	}
/**
	 * From course Object to Course Data (array)
	 */
	public function courseToArray($course)
	{
		$res = array();
		$res['sysCode'] 			= $course->courseId;
		$res['name'] 				= $course->title;
		$res['officialCode'] 		= $course->officialCode;
		$res['titular'] 			= $course->titular;
		$res['email'] 				= $course->email;
		$res['categoryCode'] 		= $course->category;
		$res['extLinkName'] 		= $course->departmentName;
		$res['extLinkUrl'] 			= $course->departmentUrl;
		$res['language'] 			= $course->language;
		$res['visibility'] 			= $course->access;
		$res['registrationAllowed'] = $course->enrolment;
		$res['enrollmentKey'] 		= $course->enrolmentKey;
		
		return $res;	
	}
	
}
?>