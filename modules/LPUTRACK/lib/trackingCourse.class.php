<?php

/**
 * Tracking for a course
 *
 * @version LPUTRACK 1.0
 * @package LPUTRACK
 * @author Anh Thao PHAM <anhthao.pham@claroline.net>
 */
class TrackingCourse
{
    private $courseCode;
    private $intitule;
    private $nbLearnPath;
    private $trackingLearnPathList;
    private $trackingList;
    private $generalTracking;
    private $mode;

    /**
     * Constructor
     * @param string $courseCode
     * @param string $intitule
     */
    public function __construct( $courseCode, $intitule )
    {
        $this->courseCode = $courseCode;
        $this->intitule = $intitule;
        $this->nbLearnPath = TrackingUtils::getNbLearnPathInCourse( $courseCode );
        $this->trackingLearnPathList = null;
        $this->trackingList = null;
        $this->generalTracking = null;
        $this->mode = 0;
    }

    /**
     * Get code of the course
     * @return string The course code
     */
    public function getCourseCode()
    {
        return $this->courseCode;
    }

    /**
     * Get name of the course
     * @return string The name of the course
     */
    public function getIntitule()
    {
        return $this->intitule;
    }

    /**
     * Get the number of learnPaths in the course
     * @return int The number of learnPaths
     */
    public function getNbLearnPath()
    {
        return $this->nbLearnPath;
    }

    /**
     * Get TrackingLearnPath for a given learnPath
     * @param int $learnPathId
     * @return TrackingLearnPath
     */
    public function getTrackingLearnPath( $learnPathId )
    {
        $trackingLearnPath = null;
        if( is_array( $this->trackingLearnPathList ) && isset( $this->trackingLearnPathList[ $learnPathId ] ) )
        {
            $trackingLearnPath = $this->trackingLearnPathList[ $learnPathId ];
        }
        return $trackingLearnPath;
    }

    /**
     * Get list of TrackingLearnPath associated to the TrackingCourse
     * @return array List of TrackingLearnPath
     */
    public function getTrackingLearnPathList()
    {
        return $this->trackingLearnPathList;
    }

    /**
     * Get the list of Tracking for the course
     * @return array
     */
    public function getTrackingList()
    {
        return $this->trackingList;
    }

    /**
     * Get the ($id)th tracking of the tracking list
     * @param int $id
     * @return TrackingEntry
     */
    public function getTracking( $id )
    {
        return isset( $this->trackingList[ $id ] ) ? $this->trackingList[ $id ] : null;
    }

    /**
     * Get the tracking for the course generated in mode 1
     * @return TrackingEntry
     */
    public function getGeneralTracking()
    {
        return $this->generalTracking;
    }

    /**
     * Get the tracking generation mode
     * @return int
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Generate the list of TrackingLearnPath for the course
     */
    public function generateTrackingLearnPath()
    {
        if( !is_array( $this->trackingLearnPathList ) )
        {
            $this->trackingLearnPathList = array();
            $resultSet = TrackingUtils::getLearnPathFromCourse( $this->courseCode );

            if( !$resultSet->isEmpty() )
            {
                $resultRow = $resultSet->fetch();
                while( $resultRow )
                {
                    $this->trackingLearnPathList[ $resultRow['learnPath_id'] ] = new TrackingLearnPath( $this->courseCode,
                                                                                                        $resultRow['learnPath_id'],
                                                                                                        $resultRow['name'] );
                    $resultRow = $resultSet->fetch();
                }
            }
        }
    }

    /**
     * Generate tracking for each TrackingLearnPath associated to the TrackingCourse
     * @param int $userId
     * @param int $mode 1 : An unique tracking
     *                  2 : A tracking per day
     */
    public function generateLearnPathTrackingList( $userId, $mode )
    {
        if( !is_array( $this->trackingLearnPathList ) )
        {
            $this->generateTrackingLearnPath();
        }

        foreach( $this->trackingLearnPathList as $trackingLearnPath )
        {
            $trackingLearnPath->generateTrackingList( $userId, $mode );
        }
    }

    /**
     * Generate tracking for each TrackingModule associated to each TrackingLearnPath associated to the TrackingCourse
     * @param int $userId
     * @param int $mode 1 : An unique tracking
     *                  2 : A tracking per day
     *                  3 : All available tracking
     */
    public function generateModuleTrackingList( $userId, $mode )
    {
        if( !is_array( $this->trackingLearnPathList ) )
        {
            $this->generateTrackingLearnPath();
        }

        foreach( $this->trackingLearnPathList as $trackingLearnPath )
        {
            $trackingLearnPath->generateModuleTrackingList( $userId, $mode );
        }
    }

    /**
     * Generate tracking for the course
     * @param int $userId
     * @param int $mode 1 : An unique tracking
     *                  2 : A tracking per day
     * @throws Exception
     */
    public function generateTrackingList( $userId, $mode )
    {
        $this->mode = $mode;
        $trackingData = TrackingData::getInstance()->getCourseRecords( array( $userId ), array( $this->courseCode ) );
        $nonInitTrackingData = TrackingUtils::getNonInitLearnPathTrackingList( $userId, $this->courseCode );
        $isValidTrackingData = count( $trackingData ) > 0;
        $warning = count( $nonInitTrackingData ) > 0;

        if( $isValidTrackingData || $warning )
        {
            switch( $mode )
            {
                // Generate an unique entry from all records
                case 1 :
                    if( $isValidTrackingData )
                    {
                        $dateTime = new DateTime( $trackingData[0]['date'] );
                        $latestDate = $dateTime->format( "Y-m-d" );
                        $firstDateTime = new DateTime( $trackingData[count( $trackingData ) - 1]['date'] );
                        $firstDate = $firstDateTime->format( "Y-m-d" );
                    }
                    else
                    {
                        $latestDate = $nonInitTrackingData[0]['date'];
                        $firstDate = "-";
                    }
                    $totalTime = "0000:00:00";
                    $progress = 0;

                    $progressTab = array();

                    foreach( $trackingData as $record)
                    {
                        $totalTime = TrackingUtils::addTime( $totalTime, $record['sessionTime'] );

                        if( !isset( $progressTab[ $record['learnPathId'] ] ) )
                        {
                            $progressTab[ $record['learnPathId'] ] = array();
                            $progressTab[ $record['learnPathId'] ][ $record['moduleId'] ] = $record['progress'];
                        }
                        elseif( !isset( $progressTab[ $record['learnPathId'] ][ $record['moduleId'] ] )
                            || $record['progress'] > $progressTab[ $record['learnPathId'] ][ $record['moduleId'] ] )
                        {
                            $progressTab[ $record['learnPathId'] ][ $record['moduleId'] ] = $record['progress'];
                        }
                    }

                    // inject missing tracking data with data fetched from table 'lp_user_module_progress'
                    foreach( $nonInitTrackingData as $nonInitTracking )
                    {
                        $nonInitProgress = TrackingUtils::computeProgress( $nonInitTracking['courseCode'],
                                                                           $nonInitTracking['moduleId'],
                                                                           $nonInitTracking['status'],
                                                                           $nonInitTracking['scoreRaw'],
                                                                           $nonInitTracking['scoreMin'],
                                                                           $nonInitTracking['scoreMax'] );
                        if( !isset( $progressTab[ $nonInitTracking['learnPathId'] ] ) )
                        {
                            $totalTime = TrackingUtils::addTime( $totalTime, $nonInitTracking['sessionTime'] );
                            $progressTab[ $nonInitTracking['learnPathId'] ] = array();
                            $progressTab[ $nonInitTracking['learnPathId'] ][ $nonInitTracking['moduleId'] ] = $nonInitProgress;
                        }
                        elseif( !isset( $progressTab[ $nonInitTracking['learnPathId'] ][ $nonInitTracking['moduleId'] ] ) )
                        {
                            $totalTime = TrackingUtils::addTime( $totalTime, $nonInitTracking['sessionTime'] );
                            $progressTab[ $nonInitTracking['learnPathId'] ][ $nonInitTracking['moduleId'] ] = $nonInitProgress;
                        }
                    }

                    foreach( $progressTab as $learnPathId => $learnPathProgress )
                    {
                        $learnPathProgressAverage = 0;
                        foreach( $learnPathProgress as $moduleProgress )
                        {
                            $learnPathProgressAverage += $moduleProgress;
                        }

                        $nbModule = TrackingUtils::getNbModuleInLearnPath( $this->courseCode, (int)$learnPathId );
                        if( $nbModule > 0 )
                        {
                            $learnPathProgressAverage = (int)( $learnPathProgressAverage / $nbModule );
                        }
                        else
                        {
                            $learnPathProgressAverage = 0;
                        }
                        $progress += $learnPathProgressAverage;
                    }

                    if( $this->nbLearnPath > 0 )
                    {
                        $progress = (int)( $progress / $this->nbLearnPath );
                    }
                    else
                    {
                        $progress = 0;
                    }

                    $this->generalTracking = new TrackingEntry( $totalTime, $latestDate, 0, 0, 0, $progress, $warning );
                    $this->generalTracking->setFirstConnection( $firstDate );

                    break;

                // Generate an unique entry per day
                case 2 :
                    if( is_null( $this->generalTracking ) )
                    {
                        $this->generateTrackingList( $userId, 1 );
                    }
                    $dateTab = array();
                    foreach( $trackingData as $record )
                    {
                        $dateTime = new DateTime( $record['date'] );
                        $dateDay = $dateTime->format( "Y-m-d" );

                        if( !isset( $dateTab[ $dateDay ] ) )
                        {
                            $dateTab[ $dateDay ] = array();
                            $dateTab[ $dateDay ]['totalTime'] = $record['sessionTime'];
                            $dateTab[ $dateDay ]['progress'] = array();
                            $dateTab[ $dateDay ]['progress'][ $record['learnPathId'] ] = array();
                            $dateTab[ $dateDay ]['progress'][ $record['learnPathId'] ][ $record['moduleId'] ] = $record['progress'];
                        }
                        else
                        {
                            $dateTab[ $dateDay ]['totalTime'] = TrackingUtils::addTime( $dateTab[ $dateDay ]['totalTime'], $record['sessionTime'] );

                            if( !isset( $dateTab[ $dateDay ]['progress'][ $record['learnPathId'] ] ) )
                            {
                                $dateTab[ $dateDay ]['progress'][ $record['learnPathId'] ] = array();
                                $dateTab[ $dateDay ]['progress'][ $record['learnPathId'] ][ $record['moduleId'] ] = $record['progress'];
                            }
                            elseif( !isset( $dateTab[ $dateDay ]['progress'][ $record['learnPathId'] ][ $record['moduleId'] ] )
                                    || $record['progress'] > $dateTab[ $dateDay ]['progress'][ $record['learnPathId'] ][ $record['moduleId'] ] )
                            {
                                $dateTab[ $dateDay ]['progress'][ $record['learnPathId'] ][ $record['moduleId'] ] = $record['progress'];
                            }
                        }
                    }

                    foreach( $nonInitTrackingData as $nonInitTracking )
                    {
                        $nonInitProgress = TrackingUtils::computeProgress( $nonInitTracking['courseCode'],
                                                                           $nonInitTracking['moduleId'],
                                                                           $nonInitTracking['status'],
                                                                           $nonInitTracking['scoreRaw'],
                                                                           $nonInitTracking['scoreMin'],
                                                                           $nonInitTracking['scoreMax'] );
                        $isModulePresent = false;
                        foreach( $dateTab as $record )
                        {
                            if( isset( $record['progress'][ $nonInitTracking['learnPathId'] ][ $nonInitTracking['moduleId'] ] ) )
                            {
                                $isModulePresent = true;
                            }
                        }

                        if( !$isModulePresent )
                        {
                            if( !isset( $dateTab[ $nonInitTracking['date'] ] ) )
                            {
                                $dateTab[ $nonInitTracking['date'] ] = array();
                                $dateTab[ $nonInitTracking['date'] ]['totalTime'] = $nonInitTracking['sessionTime'];
                                $dateTab[ $nonInitTracking['date'] ]['progress'] = array();
                                $dateTab[ $nonInitTracking['date'] ]['progress'][ $nonInitTracking['learnPathId'] ] = array();
                                $dateTab[ $nonInitTracking['date'] ]['progress'][ $nonInitTracking['learnPathId'] ][ $nonInitTracking['moduleId'] ] = $nonInitProgress;
                            }
                            else
                            {
                                $dateTab[ $nonInitTracking['date'] ]['totalTime'] = TrackingUtils::addTime( $dateTab[ $nonInitTracking['date'] ]['totalTime'],
                                                                                                            $nonInitTracking['sessionTime'] );

                                if( !isset( $dateTab[ $nonInitTracking['date'] ]['progress'][ $nonInitTracking['learnPathId'] ] ) )
                                {
                                    $dateTab[ $nonInitTracking['date'] ]['progress'][ $nonInitTracking['learnPathId'] ] = array();
                                    $dateTab[ $nonInitTracking['date'] ]['progress'][ $nonInitTracking['learnPathId'] ][ $nonInitTracking['moduleId'] ] = $nonInitProgress;
                                }
                                elseif( !isset( $dateTab[ $nonInitTracking['date'] ]['progress'][ $nonInitTracking['learnPathId'] ][ $nonInitTracking['moduleId'] ] ) )
                                {
                                    $dateTab[ $nonInitTracking['date'] ]['progress'][ $nonInitTracking['learnPathId'] ][ $nonInitTracking['moduleId'] ] = $nonInitProgress;
                                }
                            }
                        }
                    }

                    // Compute progress for each day
                    $reverseDateTab = array_reverse( $dateTab );
                    $currentProgress = array();
                    foreach( $reverseDateTab as $date => $values )
                    {
                        foreach( $values['progress'] as $learnPathId => $moduleProgressTab )
                        {
                            if( !isset( $currentProgress[ $learnPathId ] ) )
                            {
                                $currentProgress[ $learnPathId ] = array();
                            }
                            foreach( $moduleProgressTab as $moduleId => $progress)
                            {
                                if( !isset( $currentProgress[ $learnPathId ][ $moduleId ] )
                                    || $progress > $currentProgress[ $learnPathId ][ $moduleId ] )
                                {
                                    $currentProgress[ $learnPathId ][ $moduleId ] = $progress;
                                }
                            }

                            $learnPathProgress = 0;
                            foreach( $currentProgress[ $learnPathId ] as $moduleId => $moduleProgress )
                            {
                                if( $moduleId != 'final' )
                                {
                                    $learnPathProgress += $moduleProgress;
                                }
                            }
                            $nbModule = TrackingUtils::getNbModuleInLearnPath( $this->courseCode, $learnPathId );

                            if( $nbModule > 0 )
                            {
                                $learnPathProgress = (int)( $learnPathProgress / $nbModule );
                            }
                            else
                            {
                                $learnPathProgress = 0;
                            }
                            $currentProgress[ $learnPathId ]['final'] = $learnPathProgress;
                        }

                        $courseProgress = 0;
                        foreach( $currentProgress as $learnPathId => $progress )
                        {
                            $courseProgress += $progress['final'];
                        }
                        if( $this->nbLearnPath > 0 )
                        {
                            $courseProgress = (int)( $courseProgress / $this->nbLearnPath );
                        }
                        else
                        {
                            $courseProgress = 0;
                        }
                        $dateTab[ $date ]['progress']['final'] = $courseProgress;
                    }

                    foreach( $dateTab as $date => $values )
                    {
                        $this->trackingList[] = new TrackingEntry( $values['totalTime'], $date, 0, 0, 0, $values['progress']['final'], $warning );
                    }
                    break;

                default :
                    throw new Exception( "Invalid mode : $mode" );
                    break;
            }
        }
    }
}

?>
