<?php

/**
 * Singleton used to access tracking data
 * As datas are not easily accessible from 'tracking_event' table
 * the content of that table is fetched and inserted in an array
 * 
 * @version LPUTRACK 1.0
 * @package LPUTRACK
 * @author Anh Thao PHAM <anhthao.pham@claroline.net>
 */
class TrackingData {
    
    private static $trackingData = null;
    private $dataTab;
    private $userIdList;
    private $courseCodeList;
    
    /**
     * Constructor
     */
    private function __construct()
    {
        $this->dataTab = array();
        $this->userIdList = array();
        $this->courseCodeList = array();
    }
    
    /**
     * Get instance of this class
     * @return TrackingData The singleton
     */
    public static function getInstance()
    {
        if( is_null( self::$trackingData ) )
        {
            self::$trackingData = new TrackingData();
        }
        
        return self::$trackingData;
    }
    
    /**
     * Add a given user to the list of users
     * @param int $userId
     */
    public function addUser( $userId )
    {
        $this->userIdList[] = (int)$userId;
    }
    
    /**
     * Add a given course to the list of courses
     * @param string $courseCode
     */
    public function addCourse( $courseCode )
    {
        $this->courseCodeList[] = $courseCode;
    }
    
    /**
     * Get the list of users associated to the datas
     * @return array List of users
     */
    public function getUserList()
    {
        return $this->userIdList;
    }
    
    /**
     * Get the list of courses associated to the datas
     * @return array List of courses
     */
    public function getCourseList()
    {
        return $this->courseCodeList;
    }
    
    /**
     * Get the array of datas
     * @return array List of datas
     */
    public function getDataTab()
    {
        return $this->dataTab;
    }
    
    /**
     * Generate tracking data for the users and courses in the associated lists
     */
    public function generateData()
    {
        if( is_array( $this->userIdList ) && count( $this->userIdList ) > 0 && is_array( $this->courseCodeList ) && count( $this->courseCodeList ) > 0 )
        {
            $rawData = TrackingUtils::getLearnPathTrackingData( $this->userIdList, $this->courseCodeList );
            
            if( !$rawData->isEmpty() )
            {
                $rawRow = $rawData->fetch();
                while( $rawRow )
                {
                    if( isset( $rawRow['data'] ) && preg_match( 
                        "/^\d+;\d+;\d+;\d+;\d+;\d{2,4}:\d{2}:\d{2}(\.\d{1,2})?;(NOT ATTEMPTED|INCOMPLETE|COMPLETED|PASSED|FAILED|BROWSED|UNKNOWN)$/",
                        $rawRow['data'] ) )
                    {
                        $extractedData = preg_split( "/;/", $rawRow['data'] );
                        $row = array( 'courseCode' => $rawRow['course_code'],
                                      'userId' => $rawRow['user_id'],
                                      'date' => $rawRow['date'],
                                      'learnPathId' => $extractedData[0],
                                      'moduleId' => $extractedData[1],
                                      'scoreRaw' => $extractedData[2],
                                      'scoreMin' => $extractedData[3],
                                      'scoreMax' => $extractedData[4],
                                      'sessionTime' => $extractedData[5],
                                      'status' => $extractedData[6]
                                    );
                        $row['progress'] = TrackingUtils::computeProgress( $rawRow['course_code'],
                                                                           $extractedData[1],
                                                                           $extractedData[6],
                                                                           $extractedData[2],
                                                                           $extractedData[3],
                                                                           $extractedData[4] );
                        $this->dataTab[] = $row;
                    }
                    $rawRow = $rawData->fetch();
                }
            }
        }
    }
    
    /**
     * Get tracking datas associated to a given user and a given course
     * @param int $userId
     * @param string $courseCode
     * @return array List of tracking datas
     */
    public function getCourseRecords( $userId, $courseCode )
    {
        $result = array();
        foreach( $this->dataTab as $record )
        {
            if( in_array( $record['userId'], $userId ) && in_array( $record['courseCode'], $courseCode ) )
            {
                $result[] = $record;
            }
        }
        return $result;
    }
    
    /**
     * Get tracking datas associated to a given user, a given course and a given learnPath
     * @param int $userId
     * @param string $courseCode
     * @param int $learnPathId
     * @return array List of tracking datas
     */
    public function getLearnPathRecords( $userId, $courseCode, $learnPathId )
    {
        $result = array();
        foreach( $this->dataTab as $record )
        {
            if( in_array( $record['userId'], $userId )
                && in_array( $record['courseCode'], $courseCode )
                && in_array( $record['learnPathId'], $learnPathId ) )
            {
                $result[] = $record;
            }
        }
        return $result;
    }
    
    /**
     * Get tracking datas associated to a given user, a given course, a given learnPath and a given module
     * @param int $userId
     * @param string $courseCode
     * @param int $learnPathId
     * @param int $moduleId
     * @return array List of tracking datas
     */
    public function getModuleRecords( $userId, $courseCode, $learnPathId, $moduleId )
    {
        $result = array();
        foreach( $this->dataTab as $record )
        {
            if( in_array( $record['userId'], $userId )
                && in_array( $record['courseCode'], $courseCode )
                && in_array( $record['learnPathId'], $learnPathId )
                && in_array( $record['moduleId'], $moduleId ) )
            {
                $result[] = $record;
            }
        }
        return $result;
    }
}

?>
