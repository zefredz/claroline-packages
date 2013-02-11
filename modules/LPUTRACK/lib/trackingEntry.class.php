<?php

/**
 * A tracking information representation
 * 
 * @version LPUTRACK 1.0
 * @package LPUTRACK
 * @author Anh Thao PHAM <anhthao.pham@claroline.net>
 */
class TrackingEntry
{
    private $time;
    private $date;
    private $scoreRaw;
    private $scoreMin;
    private $scoreMax;
    private $progress;
    // Boolean used to indicate that this tracking could have been generated with information
    // not provided by this module (LPUTRACK)
    private $warning;
    
    /**
     * Constructor
     * @param string $time (format -> hh:mm:ss.cc)
     * @param date $date
     * @param int $scoreRaw
     * @param int $scoreMin
     * @param int $scoreMax
     * @param int $progress
     * @param boolean $warning
     */
    public function __construct( $time, $date, $scoreRaw, $scoreMin, $scoreMax, $progress, $warning = false )
    {
        $this->time = $time;
        $this->date = $date;
        $this->scoreRaw = $scoreRaw;
        $this->scoreMin = $scoreMin;
        $this->scoreMax = $scoreMax;
        $this->progress = $progress;
        $this->warning = $warning;
    }
    
    /**
     * Get spent time
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }
    
    /**
     * Get date
     * @return date
     */
    public function getDate()
    {
        return $this->date;
    }
    
    /**
     * Get raw score
     * @return int
     */
    public function getScoreRaw()
    {
        return $this->scoreRaw;
    }
    
    /**
     * Get min score
     * @return int
     */
    public function getScoreMin()
    {
        return $this->scoreMin;
    }
    
    /**
     * Get max score
     * @return int
     */
    public function getScoreMax()
    {
        return $this->scoreMax;
    }
    
    /**
     * Get progress
     * @return int
     */
    public function getProgress()
    {
        return $this->progress;
    }
    
    /**
     * Get warning flag
     * @return boolean
     */
    public function getWarning()
    {
        return $this->warning;
    }
}

?>
