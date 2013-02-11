<?php

/**
 * Information about a learnPath
 * 
 * @version LPUTRACK 1.0
 * @package LPUTRACK
 * @author Anh Thao PHAM <anhthao.pham@claroline.net>
 */
class InfoLearnPath
{
    private $courseCode;
    private $learnPathId;
    private $learnPathName;
    private $infoModuleList;
    
    /**
     * Constructor
     * @param string $courseCode
     * @param int $learnPathId
     * @param string $learnPathName
     */
    public function __construct( $courseCode, $learnPathId, $learnPathName )
    {
        $this->courseCode = $courseCode;
        $this->learnPathId = $learnPathId;
        $this->learnPathName = $learnPathName;
        $this->infoModuleList = array();
        $this->generateInfoModuleList();
    }
    
    /**
     * Get the course code from which the learnPath belongs
     * @return string The course code
     */
    public function getCourseCode()
    {
        return $this->courseCode;
    }
    
    /**
     * Get the id of the learnPath
     * @return int Id of the learnPath
     */
    public function getLearnPathId()
    {
        return $this->learnPathId;
    }
    
    /**
     * Get the name of the learnPath
     * @return string The name of the learnPath
     */
    public function getLearnPathName()
    {
        return $this->learnPathName;
    }
    
    /**
     * Get the list of InfoModule
     * @return array List of InfoModule
     */
    public function getInfoModuleList()
    {
        return $this->infoModuleList;
    }
    
    /**
     * Compute the number of modules associated to the learnPath
     * @return int The number of modules
     */
    public function getNbModule()
    {
        return count( $this->infoModuleList );
    }
    
    /**
     * Generate the list of InfoModule
     */
    private function generateInfoModuleList()
    {
        $resultSet = TrackingUtils::getModuleFromLearnPath( $this->courseCode, $this->learnPathId );
         
        if( !$resultSet->isEmpty() )
        {
            $resultRow = $resultSet->fetch();
            while( $resultRow )
            {
                $this->infoModuleList[ $resultRow['module_id'] ] = new InfoModule( $this->courseCode,
                                                                                   $this->learnPathId,
                                                                                   $resultRow['module_id'],
                                                                                   $resultRow['name'],
                                                                                   $resultRow['contentType'] );
                $resultRow = $resultSet->fetch();
            }
        }
    }
}

?>
