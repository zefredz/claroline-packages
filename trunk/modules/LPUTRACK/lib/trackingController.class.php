<?php

/**
 * Controller for the tracking
 * 
 * @version LPUTRACK 1.0
 * @package LPUTRACK
 * @author Anh Thao PHAM <anhthao.pham@claroline.net>
 */
class TrackingController
{
    private $infoClass;
    private $trackingUserList;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->infoClass = null;
        $this->trackingUserList = array();
    }
    
    /**
     * Get Information about the class
     * @return InfoClass
     */
    public function getInfoClass()
    {
        return $this->infoClass;
    }
    
    /**
     * Set class to be tracked
     * @param int $classId
     * @throws Exception
     */
    public function setInfoClass( $classId )
    {
        $className = TrackingUtils::getClassNameFromClassId( (int)$classId );
        if( !is_null( $className ) )
        {
            $this->infoClass = new InfoClass( (int)$classId, $className );
        }
        else
        {
            throw new Exception( "Invalid param classId : $classId" );
        }
    }
    
    /**
     * Get list of all classes
     * @return array List of class
     */
    public function getClassList()
    {
        $classList = array();
        $resultSet = TrackingUtils::getAllClasses();
        
        if( !$resultSet->isEmpty() )
        {
            $resultRow = $resultSet->fetch();
            while( $resultRow )
            {
                $classList[ $resultRow['id'] ] = $resultRow['name'];
                $resultRow = $resultSet->fetch();
            }
        }
        
        return $classList;
    }
    
    /**
     * Get list of associated TrackingUser
     * @return array List of TrackingUser
     */
    public function getTrackingUserList()
    {
        return $this->trackingUserList;
    }
    
    /**
     * Get associated TrackingUser for a given user
     * @param int $userId
     * @return TrackingUser
     */
    public function getTrackingUser( $userId )
    {
        $trackingUser = isset( $this->trackingUserList[ $userId ] ) ? $this->trackingUserList[ $userId ] : null;
        return $trackingUser;
    }
    
    /**
     * Add a TrackingUser
     * @param TrackingUser $trackingUser
     * @throws Exception
     */
    public function addTrackingUser( $trackingUser )
    {
        if( $trackingUser instanceof TrackingUser )
        {
            $this->trackingUserList[ $trackingUser->getUserId() ] = $trackingUser;
        }
        else
        {
            throw new Exception( 'Argument $trackinUser must be an instance of TrackingUser' );
        }
    }
    
    /**
     * Get the number of courses in a given class
     * @param int $classId
     * @return int The number of courses
     */
    public function getNbCourseFromClass( $classId )
    {
        return TrackingUtils::getNbCourseFromClass( $classId );
    }
    
    /**
     * Get the number of users in a given class
     * @param int $classId
     * @return int The number of Users
     */
    public function getNbUserFromClass( $classId )
    {
        return TrackingUtils::getNbUserFromClass( $classId );
    }
}

?>
