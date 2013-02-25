<?php

/**
 * Collection of useful functions
 * 
 * @version LPUTRACK 1.0
 * @package LPUTRACK
 * @author Anh Thao PHAM <anhthao.pham@claroline.net>
 */
class TrackingUtils
{
    /**
     * Add 2 strings representing time of format hour:min:sec(.xx)
     * @param string $totalTime
     * @param string $sessionTime
     * @return string The addition of the 2 given times
     * @throws Exception
     */
    public static function addTime( $totalTime, $sessionTime )
    {
        $resultTime = $totalTime;
        
        $tempTime = $totalTime;
        $pattern = "/^\d{2,4}:\d{2}:\d{2}(\.\d{1,2})?$/";
        if( preg_match( $pattern, $totalTime ) === 1 && preg_match( $pattern, $sessionTime ) === 1 )
        {
            $totalTimeDetails = preg_split("/[:\.]/", $totalTime);
            $sessionTimeDetails = preg_split("/[:\.]/", $sessionTime);
            
            $tempTime = array( (int)$totalTimeDetails[0], (int)$totalTimeDetails[1], (int)$totalTimeDetails[2] );
            if( isset( $totalTimeDetails[3] ) )
            {
                if( preg_match( "/^\d{1}$/", $totalTimeDetails[3] ) )
                {
                    $tempTime[3] = (int)$totalTimeDetails[3] * 10;
                }
                elseif( preg_match( "/^\d{2}$/", $totalTimeDetails[3] ) )
                {
                    $tempTime[3] = (int)$totalTimeDetails[3];
                }
            }
            
            $tempTime[0] += (int)$sessionTimeDetails[0];
            $tempTime[1] += (int)$sessionTimeDetails[1];
            $tempTime[2] += (int)$sessionTimeDetails[2];
            
            if( isset( $sessionTimeDetails[3] ) )
            {
                if( preg_match( "/^\d{1}$/", $sessionTimeDetails[3] ) )
                {
                    $tempTime[3] = isset( $tempTime[3] ) ? ( $tempTime[3] + (int)$sessionTimeDetails[3] * 10 ) : (int)( $sessionTimeDetails[3] * 10 );
                }
                elseif( preg_match( "/^\d{2}$/", $sessionTimeDetails[3] ) )
                {
                    $tempTime[3] = isset( $tempTime[3] ) ? ( $tempTime[3] + (int)$sessionTimeDetails[3] ) : (int)$sessionTimeDetails[3];
                }
            }
            
            if( isset( $tempTime[3] ) && $tempTime[3] >= 100 )
            {
                $transfer = (int)( $tempTime[3] / 100 );
                $tempTime[2] += $transfer;
                $tempTime[3] %= 100;
            }
            
            for( $i = 2; $i > 0; $i--)
            {
                if( $tempTime[ $i ] > 60 )
                {
                    $transfer = (int)( $tempTime[ $i ] / 60 );
                    $tempTime[ $i - 1 ] += $transfer;
                    $tempTime[ $i ] %= 60;
                }
            }
            
            $resultTime = "";
            if( $tempTime[0] > 9999 )
            {
                $resultTime .= '9999:59:59.99';
            }
            else
            {
                if( $tempTime[0] < 10 )
                {
                    $resultTime .= "0" . $tempTime[0];
                }
                else
                {
                    $resultTime .= $tempTime[0];
                }

                if( $tempTime[1] < 10 )
                {
                    $resultTime .= ":0$tempTime[1]";
                }
                else
                {
                    $resultTime .= ":$tempTime[1]";
                }

                if( $tempTime[2] < 10 )
                {
                    $resultTime .= ":0$tempTime[2]";
                }
                else
                {
                    $resultTime .= ":$tempTime[2]";
                }

                if( isset( $tempTime[3] ) && $tempTime[3] > 0 )
                {
                    if( $tempTime[3] < 10 )
                    {
                        $resultTime .= ".0$tempTime[3]";
                    }
                    else
                    {
                        $resultTime .= ".$tempTime[3]";
                    }
                }
            }
        }
        else
        {
            throw new Exception( "Invalid time format : $totalTime OR $sessionTime" );
        }
        
        return $resultTime;
    }
    
    /**
     * Get all modules (id, name, contentType, rank in the learnPath) associated to a given learnPath
     * @param string $courseCode
     * @param int $learnPathId
     * @return ResultSet List of modules
     */
    public static function getModuleFromLearnPath( $courseCode, $learnPathId )
    {
        $tbl = get_module_course_tbl( array( 'lp_rel_learnpath_module', 'lp_module' ), $courseCode );
        
        $resultSet = Claroline::getDatabase()->query(
            "SELECT tm.module_id, tm.name, tm.contentType, trlpm.rank
               FROM `{$tbl['lp_module']}` AS tm
         INNER JOIN `{$tbl['lp_rel_learnpath_module']}` AS trlpm
                 ON tm.module_id = trlpm.module_id
              WHERE trlpm.learnPath_id = " . Claroline::getDatabase()->escape( (int)$learnPathId ) . "
                AND tm.contentType IN ( 'DOCUMENT', 'EXERCISE', 'SCORM' )
           ORDER BY trlpm.rank"
        );

        return $resultSet;
    }
    
    /**
     * Get all courses (id, intitule) associated to a given user
     * @param int $userId
     * @return ResultSet List of courses
     */
    public static function getAllCourseFromUser( $userId )
    {
        $tbl = get_module_main_tbl( array( 'rel_course_user', 'cours' ) );
        
        $resultSet = Claroline::getDatabase()->query(
            "SELECT c.code, c.intitule
               FROM `{$tbl['cours']}` AS c
         INNER JOIN `{$tbl['rel_course_user']}` AS rcu
                 ON c.code = rcu.code_cours
              WHERE rcu.user_id = " . Claroline::getDatabase()->escape( (int)$userId ) . "
           ORDER BY c.code"
        );
        
        return $resultSet;
    }
    
    /**
     * Get intutile of a course from its code
     * @param string $courseCode
     * @return string The intitule
     */
    public static function getCourseIntituleFromCourseCode( $courseCode )
    {
        $tblCourse = get_module_main_tbl( array( 'cours' ) );
        
        $intitule = null;
        $resultSet = Claroline::getDatabase()->query(
            "SELECT intitule
               FROM `{$tblCourse['cours']}`
              WHERE code = " . Claroline::getDatabase()->quote( $courseCode )
        );
               
        if( !$resultSet->isEmpty() )
        {
            $intitule = $resultSet->fetch( Database_ResultSet::FETCH_VALUE );
        }
               
        return $intitule;
    }
    
    /**
     * Get all classes
     * @return ResultSet List of all classes
     */
    public static function getAllClasses()
    {
        $tblClass = get_module_main_tbl( array( 'class' ) );
        
        $resultSet = Claroline::getDatabase()->query(
            "SELECT id, name, class_parent_id
               FROM `{$tblClass['class']}`"
        );
               
        return $resultSet;
    }
    
    /**
     * Get all courses associated to a given class
     * @param int $classId
     * @return ResultSet List of courses
     */
    public static function getCourseFromClass( $classId )
    {
        $tbl = get_module_main_tbl( array( 'cours', 'rel_course_class' ) );
        
        $resultSet = Claroline::getDatabase()->query(
            "SELECT tc.code,
                    tc.intitule
               FROM `{$tbl['cours']}` AS tc
         INNER JOIN `{$tbl['rel_course_class']}` AS trcc
                 ON tc.code = trcc.courseId
              WHERE trcc.classId = " . Claroline::getDatabase()->escape( (int)$classId ) . "
           ORDER BY tc.code"
        );
         
        return $resultSet;
    }
    
    /**
     * Compute the number of courses in a given class
     * @param int $classId
     * @return int The number of courses
     */
    public static function getNbCourseFromClass( $classId )
    {
        $nbCourse = 0;
        $tbl = get_module_main_tbl( array( 'cours', 'rel_course_class' ) );
        
        $resultSet = Claroline::getDatabase()->query(
            "SELECT COUNT( tc.code )
               FROM `{$tbl['cours']}` AS tc
         INNER JOIN `{$tbl['rel_course_class']}` AS trcc
                 ON tc.code = trcc.courseId
              WHERE trcc.classId = " . Claroline::getDatabase()->escape( (int)$classId )
        );
        if( !$resultSet->isEmpty() )
        {
            $nbCourse = $resultSet->fetch( Database_ResultSet::FETCH_VALUE );
        }
        
        return $nbCourse;
    }
    
    /**
     * Get all users (id, first name, last name) from a given class
     * @param int $classId
     * @return ResultSet List of users
     */
    public static function getUserFromClass( $classId )
    {
        $tbl = get_module_main_tbl( array( 'user', 'rel_class_user' ) );
            
        $resultSet = Claroline::getDatabase()->query(
            "SELECT u.user_id, u.nom, u.prenom
               FROM `{$tbl['user']}` AS u
         INNER JOIN `{$tbl['rel_class_user']}` AS rcu
                 ON u.user_id = rcu.user_id
              WHERE rcu.class_Id = " . Claroline::getDatabase()->escape( (int)$classId ) . "
           ORDER BY u.nom, u.prenom"
        );
               
        return $resultSet;
    }
    
    /**
     * Compute the number of users in a given class
     * @param int $classId
     * @return int The number of users
     */
    public static function getNbUserFromClass( $classId )
    {
        $nbUser = 0;
        $tblRelClassUser = get_module_main_tbl( array( 'rel_class_user' ) );
            
        $resultSet = Claroline::getDatabase()->query(
            "SELECT COUNT( user_id )
               FROM `{$tblRelClassUser['rel_class_user']}`
              WHERE class_Id = " . Claroline::getDatabase()->escape( (int)$classId )
        );
        if( !$resultSet->isEmpty() )
        {
            $nbUser = $resultSet->fetch( Database_ResultSet::FETCH_VALUE );
        }
        
        return $nbUser;
    }
    
    /**
     * Get all learnPaths (id, name, rank in course) from a given course
     * @param string $courseCode
     * @return ResultSet List of learnPaths
     */
    public static function getLearnPathFromCourse( $courseCode )
    {
        $tblLearnPath = get_module_course_tbl( array( 'lp_learnpath' ), $courseCode );
        
        $resultSet = Claroline::getDatabase()->query(
            "SELECT learnPath_id, name, rank
               FROM `{$tblLearnPath['lp_learnpath']}`
           ORDER BY rank"
        );
               
        return $resultSet;
    }
    
    /**
     * Compute the number of learnPaths in a given course
     * @param string $courseCode
     * @return int The number of learnPaths
     */
    public static function getNbLearnPathInCourse( $courseCode )
    {
        $nbLearnPath = 0;
        $tblLearnPath = get_module_course_tbl( array( 'lp_learnpath' ), $courseCode );
        
        $resultSet = Claroline::getDatabase()->query(
            "SELECT COUNT( learnPath_id )
               FROM `{$tblLearnPath['lp_learnpath']}`"
        );
        if( !$resultSet->isEmpty() )
        {
            $nbLearnPath = $resultSet->fetch( Database_ResultSet::FETCH_VALUE );
        }
        
        return $nbLearnPath;      
    }
    
    /**
     * Computer the number of modules in a given learnPath
     * @param string $courseCode
     * @param int $learnPathId
     * @return int The number of modules
     */
    public static function getNbModuleInLearnPath( $courseCode, $learnPathId )
    {
        $nbModule = 0;
        $tbl = get_module_course_tbl( array( 'lp_rel_learnpath_module', 'lp_module' ), $courseCode );
        
        $resultSet = Claroline::getDatabase()->query(
            "SELECT COUNT( trlm.module_id )
               FROM `{$tbl['lp_rel_learnpath_module']}` AS trlm
         INNER JOIN `{$tbl['lp_module']}` AS tm
                 ON trlm.module_id = tm.module_id
              WHERE trlm.learnPath_id = " . Claroline::getDatabase()->escape( (int)$learnPathId ) . "
                AND tm.contentType IN ( 'DOCUMENT', 'EXERCISE', 'SCORM' )"
        );
        if( !$resultSet->isEmpty() )
        {
            $nbModule = $resultSet->fetch( Database_ResultSet::FETCH_VALUE );
        }
        
        return $nbModule;
    }
    
    /**
     * Get the content type of a given module
     * @param string $courseCode
     * @param int $moduleId
     * @return string The content type
     */
    public static function getContentTypeFromModuleId( $courseCode, $moduleId )
    {
        $tblModule = get_module_course_tbl( array( 'lp_module' ), $courseCode );
        
        $resultSet = Claroline::getDatabase()->query(
            "SELECT contentType
               FROM `{$tblModule['lp_module']}`
              WHERE module_id = " . Claroline::getDatabase()->escape( (int)$moduleId )
        );     
               
        return $resultSet;
    }
    
    /**
     * Get the Id of the learnPath associated to an entry in the 'lp_user_module_progress' table
     * @param string $courseCode
     * @param int $userModuleProgressId
     * @return int Id of the associated learnPath
     */
    public static function getLearnPathModuleIdFromUserModuleProgressId( $courseCode, $userModuleProgressId )
    {
        $learnPathModuleId = null;
        $tblUserModuleProgress = get_module_course_tbl( array( 'lp_user_module_progress' ), $courseCode );
        
        $resultSet = Claroline::getDatabase()->query(
            "SELECT learnPath_module_id
               FROM `{$tblUserModuleProgress['lp_user_module_progress']}`
              WHERE user_module_progress_id = " . Claroline::getDatabase()->escape( (int)$userModuleProgressId )
        );
        if( !$resultSet->isEmpty() )
        {
            $learnPathModuleId = $resultSet->fetch( Database_ResultSet::FETCH_VALUE );
        }
        
        return $learnPathModuleId;
    }
    
    /**
     * Get the Id of the learnPath associated to an entry of the 'lp_rel_learnpath_module' table
     * @param string $courseCode
     * @param int $learnPathModuleId
     * @return int Id of the associated learnPath
     */
    public static function getLearnPathIdFromRelLearnPathModuleId( $courseCode, $learnPathModuleId )
    {
        $learnPathId = null;
        $tblRelLearnPathModule = get_module_course_tbl( array( 'lp_rel_learnpath_module' ), $courseCode );
        
        $resultSet = Claroline::getDatabase()->query(
            "SELECT learnPath_id
               FROM `{$tblRelLearnPathModule['lp_rel_learnpath_module']}`
              WHERE learnPath_module_id = " . Claroline::getDatabase()->escape( (int)$learnPathModuleId )
        );
        if( !$resultSet->isEmpty() )
        {
            $learnPathId = $resultSet->fetch( Database_ResultSet::FETCH_VALUE );
        }
        
        return $learnPathId;
    }
    
    /**
     * Get the Id of the module associated to an entry of the 'lp_rel_learnpath_module' table
     * @param string $courseCode
     * @param int $learnPathModuleId
     * @return int Id of the associated module
     */
    public static function getModuleIdFromRelLearnPathModuleId( $courseCode, $learnPathModuleId )
    {
        $moduleId = null;
        $tblRelLearnPathModule = get_module_course_tbl( array( 'lp_rel_learnpath_module' ), $courseCode );
        
        $resultSet = Claroline::getDatabase()->query(
            "SELECT module_id
               FROM `{$tblRelLearnPathModule['lp_rel_learnpath_module']}`
              WHERE learnPath_module_id = " . Claroline::getDatabase()->escape( (int)$learnPathModuleId )
        );
        if( !$resultSet->isEmpty() )
        {
            $moduleId = $resultSet->fetch( Database_ResultSet::FETCH_VALUE );
        }
        
        return $moduleId;
    }
    
    /**
     * Get the name of a class from its Id
     * @param int $classId
     * @return string The name of the given class
     */
    public static function getClassNameFromClassId( $classId )
    {
        $className = null;
        
        $tblClass = get_module_main_tbl( array( 'class' ) );
        $resultSet = Claroline::getDatabase()->query(
            "SELECT name
               FROM `{$tblClass['class']}`
              WHERE id = " . Claroline::getDatabase()->escape( (int)$classId )
        );
        if( !$resultSet->isEmpty() )
        {
            $className = $resultSet->fetch( Database_ResultSet::FETCH_VALUE );
        }
        
        return $className;
    }
    
    /**
     * Get details of an user (id, first name, last name) from its Id
     * @param int $userId
     * @return ResultSet The user
     */
    public static function getUserFromUserId( $userId )
    {
        $tblUser = get_module_main_tbl( array( 'user' ) );
        
        $resultSet = Claroline::getDatabase()->query(
            "SELECT user_id, nom, prenom
               FROM `{$tblUser['user']}`
              WHERE user_id = " . Claroline::getDatabase()->escape( (int)$userId )
        );
        
        return $resultSet->fetch();
    }
    
    /**
     * Get entries associated to a given user from 'lp_user_module_progress' table
     * @param int $userId
     * @param string $courseCode
     * @return ResultSet List of tracking info
     */
    public static function getCourseTrackingFromUserModuleProgress( $userId, $courseCode )
    {
        $tbl = get_module_course_tbl( array( 'lp_user_module_progress', 'lp_rel_learnpath_module' ), $courseCode );
        $resultSet = Claroline::getDatabase()->query(
            "SELECT tump.user_id,
                    tump.learnPath_id,
                    tump.raw,
                    tump.scoreMin,
                    tump.scoreMax,
                    tump.total_time,
                    tump.lesson_status,
                    trlm.module_id
               FROM `{$tbl['lp_user_module_progress']}` AS tump
         INNER JOIN `{$tbl['lp_rel_learnpath_module']}` AS trlm
                 ON tump.learnPath_module_id = trlm.learnPath_module_id
              WHERE tump.user_id = " . Claroline::getDatabase()->escape( (int)$userId ) . "
           ORDER BY tump.user_id"
        );
        
        return $resultSet;
    }
    
    /**
     * Get entries associated to a given user and a given learnPath from 'lp_user_module_progress' table
     * @param int $userId
     * @param string $courseCode
     * @param int $learnPathId
     * @return ResultSet List of tracking info
     */
    public static function getLearnPathTrackingFromUserModuleProgress( $userId, $courseCode, $learnPathId )
    {
        $tbl = get_module_course_tbl( array( 'lp_user_module_progress', 'lp_rel_learnpath_module' ), $courseCode );
        $resultSet = Claroline::getDatabase()->query(
            "SELECT tump.user_id,
                    tump.learnPath_id,
                    tump.raw,
                    tump.scoreMin,
                    tump.scoreMax,
                    tump.total_time,
                    tump.lesson_status,
                    trlm.module_id
               FROM `{$tbl['lp_user_module_progress']}` AS tump
         INNER JOIN `{$tbl['lp_rel_learnpath_module']}` AS trlm
                 ON tump.learnPath_module_id = trlm.learnPath_module_id
              WHERE tump.user_id = " . Claroline::getDatabase()->escape( (int)$userId ) . "
                AND tump.learnPath_id = " . Claroline::getDatabase()->escape( (int)$learnPathId ) . "
           ORDER BY tump.user_id"
        );
        
        return $resultSet;
    }
    
    /**
     * Get entries associated to a given user, a given learnPath and a given module from 'lp_user_module_progress' table
     * @param int $userId
     * @param string $courseCode
     * @param int $learnPathId
     * @param int $moduleId
     * @return ResultSet List of tracking info
     */
    public static function getModuleTrackingFromUserModuleProgress( $userId, $courseCode, $learnPathId, $moduleId )
    {
        $tbl = get_module_course_tbl( array( 'lp_user_module_progress', 'lp_rel_learnpath_module' ), $courseCode );
        $resultSet = Claroline::getDatabase()->query(
            "SELECT tump.user_id,
                    tump.learnPath_id,
                    tump.raw,
                    tump.scoreMin,
                    tump.scoreMax,
                    tump.total_time,
                    tump.lesson_status,
                    trlm.module_id
               FROM `{$tbl['lp_user_module_progress']}` AS tump
         INNER JOIN `{$tbl['lp_rel_learnpath_module']}` AS trlm
                 ON tump.learnPath_module_id = trlm.learnPath_module_id
              WHERE tump.user_id = " . Claroline::getDatabase()->escape( (int)$userId ) . "
                AND tump.learnPath_id = " . Claroline::getDatabase()->escape( (int)$learnPathId ) . "
                AND trlm.module_id = " . Claroline::getDatabase()->escape( (int)$moduleId ) . "
           ORDER BY tump.user_id"
        );
        
        return $resultSet;
    }
    
    /**
     * Get entries associated to a given list of users and a given list of courses in 'tracking_event' table
     * @param array $userIdList
     * @param array $courseCodeList
     * @return ResultSet List of tracking info
     * @throws Exception
     */
    public static function getLearnPathTrackingData( $userIdList, $courseCodeList )
    {
        $tblTrackingEvent = get_module_main_tbl( array( 'tracking_event' ) );
        
        if( is_array( $userIdList ) && is_array( $courseCodeList ) )
        {
            $courseTextList = "";
            foreach( $courseCodeList as $courseCode )
            {
                $courseTextList .= Claroline::getDatabase()->quote( $courseCode ) . ",";
            }
            $courseTextListFinal = trim( $courseTextList, "," );

            $resultSet = Claroline::getDatabase()->query(
                "SELECT course_code, user_id, date, data
                   FROM `{$tblTrackingEvent['tracking_event']}`
                  WHERE type = 'learnpath_tracking'
                    AND course_code IN ( " . $courseTextListFinal . " )
                    AND user_id IN ( " . Claroline::getDatabase()->escape( implode( ",", $userIdList ) ) . " )
               ORDER BY user_id, course_code, date DESC"
            );
        }
        else
        {
            throw new Exception( "Arguments must be of array type" );
        }
        
        return $resultSet;
    }
    
    /**
     * Get initialization entries associated to a given user in 'tracking_event' table
     * @param int $userId
     * @param string $courseCode
     * @return ResultSet List of tracking info
     */
    public static function getLearnPathTrackingInit( $userId, $courseCode )
    {
        $tblTrackingEvent = get_module_main_tbl( array( 'tracking_event' ) );
            
        $resultSet = Claroline::getDatabase()->query(
            "SELECT course_code, user_id, date, data
               FROM `{$tblTrackingEvent['tracking_event']}`
              WHERE type = 'learnpath_tracking_init'
                AND course_code = " . Claroline::getDatabase()->quote( $courseCode ) . "
                AND user_id = " . Claroline::getDatabase()->escape( (int)$userId ) . "
           ORDER BY date"
        );
        
        return $resultSet;
    }
    
    /**
     * Get tracking for modules that had been started before installation of this module (LPUTRACK) for a given user
     * @param int $userId
     * @param string $courseCode
     * @param int $learnPathId
     * @param int $moduleId
     * @return array List of tracking infos
     */
    public static function getNonInitLearnPathTrackingList( $userId, $courseCode, $learnPathId = null, $moduleId = null )
    {
        // Get the list of all modules that have been started after the istallation of this module (LPUTRACK)
        $initLearnPathTrackingSet = TrackingUtils::getLearnPathTrackingInit( $userId, $courseCode );
        if( is_null( $learnPathId ) && is_null( $moduleId ) )
        {
            $learnPathTrackingSet = TrackingUtils::getCourseTrackingFromUserModuleProgress( $userId, $courseCode );
        }
        elseif( is_null( $moduleId ) )
        {
            $learnPathTrackingSet = TrackingUtils::getLearnPathTrackingFromUserModuleProgress( $userId, $courseCode, $learnPathId );
        }
        else
        {
            $learnPathTrackingSet = TrackingUtils::getModuleTrackingFromUserModuleProgress( $userId, $courseCode, $learnPathId, $moduleId );
        }
        // Get the oldest date from 'tracking_event' table
        // This date will be used for tracking generated from 'lp_user_module_pogress'
        // as it is possible that many modules had been executed before the installation of this module (LPUTRACK)
        $oldestDate = TrackingUtils::getOldestDateFromTracking();
        
        $initList = array();
        
        $initRow = $initLearnPathTrackingSet->fetch();
        
        while( $initRow )
        {
            $extractedData = preg_split( "/;/", $initRow['data'] );
            if( !isset( $initList[ $extractedData[0] ] ) )
            {
                $initList[ $extractedData[0] ] = array();
            }
            $initList[ $extractedData[0] ][] = $extractedData[1];
            $initRow = $initLearnPathTrackingSet->fetch();
        }
        
        $nonInitList = array();
        $lpTrackRow = $learnPathTrackingSet->fetch();
        while( $lpTrackRow )
        {
            // Only keep tracking info of modules that haven't initialize by this module (LPUTRACK)
            if( !isset( $initList[ $lpTrackRow['learnPath_id'] ] )
                || !in_array( $lpTrackRow['module_id'], $initList[ $lpTrackRow['learnPath_id'] ] ) )
            {
                $nonInitList[] = array( 'courseCode' => $courseCode,
                                        'userId' => $userId,
                                        'date' => $oldestDate,
                                        'learnPathId' => $lpTrackRow['learnPath_id'],
                                        'moduleId' => $lpTrackRow['module_id'],
                                        'scoreRaw' => $lpTrackRow['raw'],
                                        'scoreMin' => $lpTrackRow['scoreMin'],
                                        'scoreMax' => $lpTrackRow['scoreMax'],
                                        'sessionTime' => $lpTrackRow['total_time'],
                                        'status' => $lpTrackRow['lesson_status']
                                      );
            }
            $lpTrackRow = $learnPathTrackingSet->fetch();
        }
        
        return $nonInitList;
    }
    
    /**
     * Compute progress for a module from its tracking data
     * EXERCISE and DOCUMENT type have directly 100% progress once done
     * SCORM type compute its progress from its status :
     * - PASSED, FAILED, COMPLETED and BROWSED : 100%
     * - NOT ATTEMPTED : 0%
     * - INCOMPLETE and UNKNOWN : compute from its score
     * @param string $courseCode
     * @param int $moduleId
     * @param string $status
     * @param int $scoreRaw
     * @param int $scoreMin
     * @param int $scoreMax
     * @return int The progress
     */
    public static function computeProgress( $courseCode, $moduleId, $status, $scoreRaw, $scoreMin, $scoreMax )
    {
        $progress = 0;
        
        $resultSet = TrackingUtils::getContentTypeFromModuleId( $courseCode, $moduleId );
               
        if( !$resultSet->isEmpty() )
        {
            $resultRow = $resultSet->fetch();
            $moduleType = $resultRow['contentType'];
            switch ( $moduleType )
            {
                case 'DOCUMENT' :
                    $progress = 100;
                    break;
                case 'EXERCISE' :
                    $progress = 100;
                    break;
                case 'SCORM' :
                    switch ( $status )
                    {
                        case 'PASSED' :
                        case 'FAILED' :
                        case 'COMPLETED' :
                        case 'BROWSED' :
                            $progress = 100;
                            break;
                        case 'INCOMPLETE' :
                        case 'UNKNOWN' :
                            if( $scoreRaw >= 0 && $scoreMax >= 0 && $scoreRaw <= $scoreMax )
                            {
                                if( $scoreMax == 0 )
                                {
                                    $progress = 0;
                                }
                                else
                                {
                                    $progress = (int)( ( $scoreRaw / $scoreMax ) * 100 );
                                }
                            }
                            break;
                        case 'NOT ATTEMPTED' :
                            $progress = 0;
                            break;
                        default:
                            break;
                    }
                    break;
                default:
                    break;
            }
        }
        
        return $progress;
    }
    
    /**
     * Get the oldest date from logs created by this module (LPUTRACK)
     * @return date The oldest date
     */
    public static function getOldestDateFromTracking()
    {
        $tblTrackingEvent = get_module_main_tbl( array( 'tracking_event' ) );
        
        $oldestDate = date( 'Y-m-d' );
        $resultSet = Claroline::getDatabase()->query(
            "SELECT MIN( date )
               FROM `{$tblTrackingEvent['tracking_event']}`"
        );
        if( !$resultSet->isEmpty() )
        {
            $tempDate = $resultSet->fetch( Database_ResultSet::FETCH_VALUE );
            $tempDateTab = preg_split( '/\s/', $tempDate );
            $oldestDate = $tempDateTab[0];
        }
        return $oldestDate;
    }
    
    /**
     * Get path of a module
     * @param string $courseCode
     * @param int $moduleId
     */
    public static function getPathFromModule( $courseCode, $moduleId )
    {
        $tbl = get_module_course_tbl( array( 'lp_module', 'lp_asset' ), $courseCode );
        
        $modulePath = Claroline::getDatabase()->query(
            "SELECT asset.path
               FROM `{$tbl['lp_asset']}` AS asset
         INNER JOIN `{$tbl['lp_module']}` AS module
                 ON asset.module_id = module.module_id
              WHERE module.module_id = " . Claroline::getDatabase()->escape( (int)$moduleId )
        );
        
        return $modulePath->fetch( Database_ResultSet::FETCH_VALUE );
    }
}

?>
