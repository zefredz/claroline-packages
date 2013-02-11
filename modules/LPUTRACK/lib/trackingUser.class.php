<?php

/**
 * Tracking for an user 
 * 
 * @version LPUTRACK 1.0
 * @package LPUTRACK
 * @author Anh Thao PHAM <anhthao.pham@claroline.net>
 */
class TrackingUser
{
    private $userId;
    private $firstName;
    private $lastName;
    private $trackingCourseList;
    
    /**
     * Constructor
     * @param int $userId
     * @param string $firstName
     * @param string $lastName
     */
    public function __construct( $userId, $firstName, $lastName )
    {
        $this->userId = $userId;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->trackingCourseList = null;
    }
    
    /**
     * Get id of the user
     * @return int The id of the user
     */
    public function getUserId()
    {
        return $this->userId;
    }
    
    /**
     * Get the first name of the user
     * @return string The first name of the user
     */
    public function getFirstName()
    {
        return $this->firstName;
    }
    
    /**
     * Get the last name of the user
     * @return string The last name of the user
     */
    public function getLastName()
    {
        return $this->lastName;
    }
    
    /**
     * Get the list of TrackingCourse associated to user
     * @return array List of TrackingCourse
     */
    public function getTrackingCourseList()
    {
        return $this->trackingCourseList;
    }
    
    /**
     * Get the TrackingCourse for a given course
     * @param string $courseCode
     * @return TrackingCourse
     */
    public function getTrackingCourse( $courseCode )
    {
        $trackingCourse = null;
        if( is_array( $this->trackingCourseList ) && isset( $this->trackingCourseList[ $courseCode ] ) )
        {
            $trackingCourse = $this->trackingCourseList[ $courseCode ];
        }
        return $trackingCourse;
    }
    
    /**
     * Generate the list of TrackingCourse for a given list of course
     * @param array $courseList By default the function generate a TrackingCourse for each course associated to the user
     * @throws Exception
     */
    public function generateTrackingCourseList( $courseList = null )
    {
        $this->trackingCourseList = array();
        
        if( is_null( $courseList ) )
        {
            $resultSet = TrackingUtils::getAllCourseFromUser( $this->userId );
            if( !$resultSet->isEmpty() )
            {
                $resultRow = $resultSet->fetch();
                while( $resultRow )
                {
                    $this->trackingCourseList[ $resultRow['code'] ] = new TrackingCourse( $resultRow['code'], $resultRow['intitule'] );
                    $resultRow = $resultSet->fetch();
                }
            }
        }
        elseif( is_array( $courseList ) )
        {
            foreach( $courseList as $courseCode )
            {
                $intitule = TrackingUtils::getCourseIntituleFromCourseCode( $courseCode );
                if( !is_null( $intitule ) )
                {
                    $this->trackingCourseList[ $courseCode ] = new TrackingCourse( $courseCode, $intitule );
                }
            }
        }
        else
        {
            throw new Exception( "Argument type must be an array" );
        }
    }
    
    /**
     * Generate tracking for each TrackingCourse
     * @param int $mode 1 : An unique tracking
     *                  2 : A tracking per day
     */
    public function generateCourseTrackingList( $mode )
    {
        if( !is_array( $this->trackingCourseList ) )
        {
            $this->generateTrackingCourseList();
        }
        foreach( $this->trackingCourseList as $trackingCourse )
        {
            $trackingCourse->generateTrackingList( $this->userId, $mode );
        }
    }
    
    /**
     * Generate tracking for each TrackingLearnPath in each TrackingCourse
     * @param int $mode 1 : An unique tracking
     *                  2 : A tracking per day
     */
    public function generateLearnPathTrackingList( $mode )
    {
        if( !is_array( $this->trackingCourseList ) )
        {
            $this->generateTrackingCourseList();
        }
        foreach( $this->trackingCourseList as $trackingCourse )
        {
            $trackingCourse->generateLearnPathTrackingList( $this->userId, $mode );
        }
    }
    
    /**
     * Generate tracking for each TrackingModule in each TrackingLearnPath in each TrackingCourse
     * @param int $mode 1 : An unique tracking
     *                  2 : A tracking per day
     *                  3 : All available tracking
     */
    public function generateModuleTrackingList( $mode )
    {
        if( !is_array( $this->trackingCourseList ) )
        {
            $this->generateTrackingCourseList();
        }
        foreach( $this->trackingCourseList as $trackingCourse )
        {
            $trackingCourse->generateModuleTrackingList( $this->userId, $mode );
        }
    }
}

?>
