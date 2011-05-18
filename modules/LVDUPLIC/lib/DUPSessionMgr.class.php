<?php
class DUPSessionMgr{
	public static $DUP_SESSION_SOURCE_COURSE 	= 'DUPsource_course';
	public static $DUP_SESSION_TARGET_COURSE 	= 'DUPtarget_course';
	public static $DUP_SESSION_TOOL_LIST 		= 'DUPtool_list';
		
	/**
	 * set the source course Id
	 */
	public static function setSourceCourseData( $courseData )
	{
	    $_SESSION[DUPSessionMgr::$DUP_SESSION_SOURCE_COURSE] = $courseData;
	}
	/**
	 * get the source course data (if set)
	 */
	public static function getSourceCourseData()
	{
	    return isset($_SESSION[DUPSessionMgr::$DUP_SESSION_SOURCE_COURSE]) ? $_SESSION[DUPSessionMgr::$DUP_SESSION_SOURCE_COURSE] : NULL ;
	}
	/**
	 * set the target course Data
	 */
	public static function setTargetCourseData( $courseData )
	{
	    $_SESSION[DUPSessionMgr::$DUP_SESSION_TARGET_COURSE] = $courseData;
	}
	/**
	 * get the target course Data (if set)
	 */
	public static function getTargetCourseData()
	{
	    return isset($_SESSION[DUPSessionMgr::$DUP_SESSION_TARGET_COURSE]) ? $_SESSION[DUPSessionMgr::$DUP_SESSION_TARGET_COURSE] : NULL ;
	}
	
	/**
	 * set the tools which need to be copied
	 * @param $toolList : array of string : each string is the label of a tool
	 */
	public static function setToolList( $toolList )
	{
	    $_SESSION[DUPSessionMgr::$DUP_SESSION_TOOL_LIST] = $toolList;
	}
	/**
	 * get the list of the tools which need to be copied
	 */
	public static function getToolList()
	{
	    return isset($_SESSION[DUPSessionMgr::$DUP_SESSION_TOOL_LIST]) ? $_SESSION[DUPSessionMgr::$DUP_SESSION_TOOL_LIST] : NULL ;
	}
	
	/**
	 *  clear every data we have put in the session
	 */
	public static function clearDupDataFromSession(){
	    unset($_SESSION[DUPSessionMgr::$DUP_SESSION_SOURCE_COURSE]);
	    unset($_SESSION[DUPSessionMgr::$DUP_SESSION_TARGET_COURSE]);
	    unset($_SESSION[DUPSessionMgr::$DUP_SESSION_TOOL_LIST]);
	
	}
	/**
	 * From course Data (array) to Course object
	 */
	public function arrayToCourse( $array )
	{
		include get_path('rootSys') . '/platform/currentVersion.inc.php';		
		
		
		$res = new ClaroCourse();
		$res->courseId 			= $array['sysCode'];
                $res->title 			= $array['name'];
                $res->officialCode 		= $array['officialCode'];
		$res->titular 			= $array['titular'];
		$res->email 			= $array['email'];		
		$res->departmentName 	= $array['extLinkName'];		
		$res->language 			= $array['language'];
		
		if ("1.8" == substr($clarolineVersion,0,3))
		{
			$res->enrolment 		= $array['registrationAllowed'];
			$res->enrolmentKey 		= $array['enrollmentKey'];
			$res->departmentUrl 	= $array['extLinkUrl'];
			$res->access 			= $array['visibility'];
                        $res->category 			= $array['categoryCode'];
		}
		if ("1.9" == substr($clarolineVersion,0,3))
		{
			$res->registration       = $array['registrationAllowed'];
			$res->registrationKey    = $array['registrationKey'];			
			$res->extLinkUrl         = $array['extLinkUrl'];
			$res->access             = $array['access'];
			$res->visibility         = $array['visibility'];
			$res->publicationDate    = $array['publicationDate'];
                        $res->category 			= $array['categoryCode'];
                        $res->expirationDate     = $array['expirationDate'];
                        $res->status             = $array['status'];
                        $res->useExpirationDate  = ('NULL' != $array['expirationDate']);
		}
                if ("1.10" == substr($clarolineVersion,0,4))
                {
                    $res->registration       = $array['registrationAllowed'];
                    $res->registrationKey    = $array['registrationKey'];			
                    $res->extLinkUrl         = $array['extLinkUrl'];
                    $res->access             = $array['access'];
                    $res->visibility         = $array['visibility'];
                    $res->publicationDate    = $array['publicationDate'];
                    $res->expirationDate     = $array['expirationDate'];
                    $res->status             = $array['status'];
                    $res->useExpirationDate  = ('NULL' != $array['expirationDate']);
                    
                    $categories_data = $array['categories'];
                    foreach($categories_data as $category_data)
                    {
                        $cat_id = $category_data['categoryId'];
                        $category = new ClaroCategory();
                        $category->load($cat_id);
                        $res->categories[] = $category;
                    }                    
                }
                			
		return $res;
	}
	/**
	 * From course Object to Course Data (array)
	 */
	public function courseToArray( $course )
	{
		include get_path('rootSys') . '/platform/currentVersion.inc.php';
		
		$res = array();
		$res['sysCode'] 			= $course->courseId;
		$res['name'] 				= $course->title;
		$res['officialCode'] 		= $course->officialCode;
		$res['titular'] 			= $course->titular;
		$res['email'] 				= $course->email;
		$res['extLinkName'] 		= $course->departmentName;	
		$res['language'] 			= $course->language;
		
		if ("1.8" == substr($clarolineVersion,0,3))
		{
			$res['registrationAllowed'] 	= $course->enrolment;
			$res['enrollmentKey']			= $course->enrolmentKey;
			$res['extLinkUrl'] 				= $course->departmentUrl;
			$res['visibility'] 				= $course->access;
                        $res['categoryCode'] 			= $course->category;
		}
		if ("1.9" == substr($clarolineVersion,0,3))
		{
			$res['registrationAllowed']  	= $course->registration;
			$res['registrationKey']  		= $course->registration;			
			$res['extLinkUrl']  			= $course->extLinkUrl;
			$res['access']  				= $course->access;
			$res['visibility']  			= $course->visibility;
			$res['publicationDate']  		= $course->publicationDate;
                        $res['expirationDate']  		= isset($course->expirationDate) ? $course->expirationDate : 'NULL' ;
                        $res['status']  				= $course->status;
                        $res['categoryCode'] 			= $course->category;
		}
                if ("1.10" == substr($clarolineVersion,0,4))
                {
                    $res['registrationAllowed']  	= $course->registration;
                    $res['registrationKey']  		= $course->registration;			
                    $res['extLinkUrl']  			= $course->extLinkUrl;
                    $res['access']  				= $course->access;
                    $res['visibility']  			= $course->visibility;
                    $res['publicationDate']  		= $course->publicationDate;
                    $res['expirationDate']  		= isset($course->expirationDate) ? $course->expirationDate : 'NULL' ;
                    $res['status']  				= $course->status;
                    
                    $res['categories'] = array();
                    foreach($course->categories as $category)
                    {
                        $res['categories'][] = array('categoryId' => $category->id);
                    }
                }
                		
		return $res;	
	}
	
}
?>