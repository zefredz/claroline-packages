<?php

/**
 * Information about a class
 * 
 * @version LPUTRACK 1.0
 * @package LPUTRACK
 * @author Anh Thao PHAM <anhthao.pham@claroline.net>
 */
class InfoClass
{
    private $classId;
    private $className;
    private $infoCourseList;
    private $infoUserList;
    
    /**
     * Constructor
     * @param int $classId
     * @param string $className
     */
    public function __construct( $classId, $className )
    {
        $this->classId = (int)$classId;
        $this->className = $className;
        $this->infoCourseList = array();
        $this->generateInfoCourseList();
        $this->infoUserList = array();
        $this->generateInfoUserList();
    }
    
    /**
     * Get the id of the class
     * @return int The id of the class
     */
    public function getClassId()
    {
        return $this->classId;
    }
    
    /**
     * Get the name of the class
     * @return string The name of the class
     */
    public function getClassName()
    {
        return $this->className;
    }
    
    /**
     * Get a list of informations about courses in the class
     * @return array List of InfoCourse
     */
    public function getInfoCourseList()
    {
        return $this->infoCourseList;
    }
    
    /**
     * Get information about a given course
     * @param string $courseCode
     * @return InfoCourse Information about the given course
     */
    public function getInfoCourse( $courseCode )
    {
        return  isset( $this->infoCourseList[ $courseCode ] ) ? $this->infoCourseList[ $courseCode ] : null;
    }
    
    /**
     * Get a list of course code of all courses associated to the class
     * @return array List of course code
     */
    public function getCourseCodeList()
    {
        $courseIdList = array();
        foreach( $this->getInfoCourseList() as $infoCourse )
        {
            $courseIdList[] = $infoCourse->getCourseCode();
        }
        
        return $courseIdList;
    }
    
    /**
     * Get a list of informations about users associated to the class
     * @return array List of InfoUser
     */
    public function getInfoUserList()
    {
        return $this->infoUserList;
    }
    
    /**
     * Generate the list of informations about the courses associated to the class
     */
    private function generateInfoCourseList()
    {
        if( !is_null( $this->classId ) )
        {
            $resultSet = TrackingUtils::getCourseFromClass( $this->classId );
            
            if( !$resultSet->isEmpty() )
            {
                $resultRow = $resultSet->fetch();
                while( $resultRow )
                {
                    $this->infoCourseList[ $resultRow['code'] ] = new InfoCourse( $resultRow['code'],
                                                                                  $resultRow['intitule'] );
                    $resultRow = $resultSet->fetch();
                }
            }
        }
        
    }
    
    /**
     * Generate the list of informations about the users associated to the class
     */
    private function generateInfoUserList()
    {
        if( !is_null( $this->classId ) )
        {
            $resultSet = TrackingUtils::getUserFromClass( $this->classId );
            
            if( !$resultSet->isEmpty() )
            {
                $resultRow = $resultSet->fetch();
                while( $resultRow )
                {
                    $this->infoUserList[ $resultRow['user_id'] ] = new InfoUser( $resultRow['user_id'],
                                                                                 $resultRow['prenom'],
                                                                                 $resultRow['nom']);
                    $resultRow = $resultSet->fetch();
                }
            }
        }
    }
}

?>
