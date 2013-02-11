<?php

/**
 * Information about a module
 * 
 * @version LPUTRACK 1.0
 * @package LPUTRACK
 * @author Anh Thao PHAM <anhthao.pham@claroline.net>
 */
class InfoModule
{
    private $moduleId;
    private $moduleName;
    private $moduleContentType;
    private $courseCode;
    private $learnPathId;
    
    /**
     * Constructor
     * @param string $courseCode
     * @param int $learnPathId
     * @param int $moduleId
     * @param string $moduleName
     * @param string $moduleContentType
     */
    public function __construct( $courseCode, $learnPathId, $moduleId, $moduleName, $moduleContentType )
    {
        $this->courseCode = $courseCode;
        $this->learnPathId = $learnPathId;
        $this->moduleId = $moduleId;
        $this->moduleName = $moduleName;
        $this->moduleContentType = $moduleContentType;
    }
    
    /**
     * Get the id of the module
     * @return int The id of the module
     */
    public function getModuleId()
    {
        return $this->moduleId;
    }
    
    /**
     * Get the name of the module
     * @return string The name of the module
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }
    
    /**
     * Get the content type of the module
     * @return string The content type of the module
     */
    public function getModuleContentType()
    {
        return $this->moduleContentType;
    }
    
    /**
     * Get the course code of the course the module belongs to
     * @return string The course code
     */
    public function getCourseCode()
    {
        return $this->courseCode;
    }
    
    /**
     * Get the id of the learnPath the module belongs to
     * @return int The id of the learnPath
     */
    public function getLearnPathId()
    {
        return $this->learnPathId;
    }
}

?>
