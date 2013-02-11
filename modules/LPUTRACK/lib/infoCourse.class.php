<?php

/**
 * Information about a course
 * 
 * @version LPUTRACK 1.0
 * @package LPUTRACK
 * @author Anh Thao PHAM <anhthao.pham@claroline.net>
 */
class InfoCourse
{
    private $courseCode;
    private $courseName;
    private $infoLearnPathList;
    
    /**
     * Constructor
     * @param string $courseCode
     * @param string $courseName
     */
    public function __construct( $courseCode, $courseName )
    {
        $this->courseCode = $courseCode;
        $this->courseName = $courseName;
        $this->infoLearnPathList = array();
        $this->generateInfoLearnPathList();
    }
    
    /**
     * Get the course code
     * @return string The code of the course
     */
    public function getCourseCode()
    {
        return $this->courseCode;
    }
    
    /**
     * Get the name of the course
     * @return string The name of the course
     */
    public function getCourseName()
    {
        return $this->courseName;
    }
    
    /**
     * Get a list of informations about the learnPaths associated to the course
     * @return array List of InfoLearnPath
     */
    public function getInfoLearnPathList()
    {
        return $this->infoLearnPathList;
    }
    
    /**
     * Get information about a given learnPath associated to the course
     * @param int $learnPathId
     * @return InfoLearnPath Information about the given learnPath
     */
    public function getInfoLearnPath( $learnPathId )
    {
        return isset( $this->infoLearnPathList[ $learnPathId ] ) ? $this->infoLearnPathList[ $learnPathId ] : null;
    }
    
    /**
     * Compute the number of learnPaths associated to the course
     * @return int The number of learnPaths
     */
    public function getNbLearnPath()
    {
        return count( $this->infoLearnPathList );
    }
    
    /**
     * Generate InfoLearnPath for each learnPaths associated to the course
     */
    private function generateInfoLearnPathList()
    {
        if( !is_null( $this->courseCode ) )
        {
            $resultSet = TrackingUtils::getLearnPathFromCourse( $this->courseCode );
            
            if( !$resultSet->isEmpty() )
            {
                $resultRow = $resultSet->fetch();
                while( $resultRow )
                {
                    $this->infoLearnPathList[ $resultRow['learnPath_id'] ] = new InfoLearnPath( $this->courseCode,
                                                                                                $resultRow['learnPath_id'],
                                                                                                $resultRow['name'] );
                    $resultRow = $resultSet->fetch();
                }
            }
        }
    }
}    

?>
