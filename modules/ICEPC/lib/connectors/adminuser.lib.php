<?php // $Id$

/**
 * @since Claroline 1.11.6
 */
interface Module_AdminUser
{
    public function deleteUserCourseTrackingData( $userId, $courseCode );
    
    public function deleteUserCourseResources( $userId, $courseCode );
    
    public function deleteUserListCourseTrackingData( $userIdList, $courseCode );
    
    public function deleteUserListCourseResources( $userIdList, $courseCode );
}

/**
 * @since Claroline 1.11.6
 */
abstract class GenericModule_AdminUser implements Module_AdminUser
{
    private $database;
    
    public function __construct ( $database )
    {
        $this->database = $database ? $database : Claroline::getDatabase ();
    }
    abstract public function getCourseTrackingTables( $courseCode );
    
    abstract public function getCourseResourceTables( $courseCode );
    
    public function deleteUserListCourseTrackingData ( $userIdList, $courseCode )
    {
        foreach ( $this->getCourseTrackingTables( $courseCode ) as $tableName => $userIdColumn )
        {
            $this->database->exec("DELETE FROM `{$tableName}` WHERE {$userIdColumn} IN (".implode( ',', $userIdList ).")");
        }
    }
    
    public function deleteUserListCourseResources ( $userIdList, $courseCode )
    {
        foreach ( $this->getCourseResourceTables( $courseCode ) as $tableName => $userIdColumn )
        {
            $this->database->exec("DELETE FROM `{$tableName}` WHERE {$userIdColumn} IN (".implode( ',', $userIdList ).")");
        }
    }
    
    public function deleteUserCourseTrackingData ( $userId, $courseCode )
    {
        return $this->deleteUserListCourseTrackingData ( array( (int) $userId ), $courseCode );
    }
    
    public function deleteUserCourseResources ( $userId, $courseCode )
    {
        return $this->deleteUserListCourseResources ( array( (int) $userId ), $courseCode );
    }
}
