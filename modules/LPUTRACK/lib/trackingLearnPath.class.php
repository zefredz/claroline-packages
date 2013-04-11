<?php

/**
 * Tracking of a learnPath
 *
 * @version LPUTRACK 1.0
 * @package LPUTRACK
 * @author Anh Thao PHAM <anhthao.pham@claroline.net>
 */
class TrackingLearnPath
{
    private $courseCode;
    private $learnPathId;
    private $learnPathName;
    private $nbModule;
    private $trackingModuleList;
    private $trackingList;
    private $generalTracking;
    private $mode;

    /**
     * Contructor
     * @param string $courseCode
     * @param int $learnPathId
     * @param string $learnPathName
     */
    public function __construct( $courseCode, $learnPathId, $learnPathName )
    {
        $this->courseCode = $courseCode;
        $this->learnPathId = $learnPathId;
        $this->learnPathName = $learnPathName;
        $this->nbModule = TrackingUtils::getNbModuleInLearnPath( $courseCode, $learnPathId );
        $this->trackingModuleList = null;
        $this->trackingList = null;
        $this->generalTracking = null;
        $this->mode = 0;
    }

    /**
     * Get code of the course the learnPath belongs to
     * @return string The course code
     */
    public function getCourseCode()
    {
        return $this->courseCode;
    }

    /**
     * Get id of the learnPath
     * @return int
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
     * Get the TrackingModule of a given module
     * @param int $moduleId
     * @return TrackingModule
     */
    public function getTrackingModule( $moduleId )
    {
        $trackingModule = null;
        if( is_array( $this->trackingModuleList ) && isset( $this->trackingModuleList[ $moduleId ] ) )
        {
            $trackingModule = $this->trackingModuleList[ $moduleId ];
        }
        return $trackingModule;
    }

    /**
     * Get the list of TrackingModule
     * @return array
     */
    public function getTrackingModuleList()
    {
        return $this->trackingModuleList;
    }

    /**
     * Get the list of tracking for the learnPath
     * @return array of TrackingEntry
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
     * Get the tracking for the learnPath generated in mode 1
     * @return TrackingEntry
     */
    public function getGeneralTracking()
    {
        return $this->generalTracking;
    }

    /**
     * Get the number of modules associated to the learnPath
     * @return int
     */
    public function getNbModule()
    {
        return $this->nbModule;
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
     * Generate the list of TrackingModule
     */
    public function generateTrackingModule()
    {
        if( !is_array( $this->trackingModuleList ) )
        {
            $this->trackingModuleList = array();
            $resultSet = TrackingUtils::getModuleFromLearnPath( $this->courseCode, $this->learnPathId );

            if( !$resultSet->isEmpty() )
            {
                $resultRow = $resultSet->fetch();
                while( $resultRow )
                {
                    $this->trackingModuleList[ $resultRow['module_id'] ] = new TrackingModule( $this->courseCode,
                                                                                       $this->learnPathId,
                                                                                       $resultRow['module_id'],
                                                                                       $resultRow['name'],
                                                                                       $resultRow['contentType']);
                    $resultRow = $resultSet->fetch();
                }
            }
        }
    }

    /**
     * Generate tracking for each TrackingModule associated to the learnPath
     * @param int $userId
     * @param int $mode 1 : An unique tracking
     *                  2 : A tracking per day
     *                  3 : All available tracking
     */
    public function generateModuleTrackingList( $userId, $mode )
    {
        if( !is_array( $this->trackingModuleList ) )
        {
            $this->generateTrackingModule();
        }

        foreach( $this->trackingModuleList as $trackingModule )
        {
            $trackingModule->generateTrackingList( $userId, $mode );
        }
    }

    /**
     * Generate tracking for the learnPath
     * @param int $userId
     * @param int $mode 1 : An unique tracking
     *                  2 : A tracking per day
     * @throws Exception
     */
    public function generateTrackingList( $userId, $mode )
    {
        $this->mode = $mode;
        $trackingData = TrackingData::getInstance()->getLearnPathRecords( array( $userId ),
                                                                          array( $this->courseCode ),
                                                                          array( $this->learnPathId ) );
        $nonInitTrackingData = TrackingUtils::getNonInitLearnPathTrackingList( $userId, $this->courseCode, $this->learnPathId );
        $isValidTrackingData = count( $trackingData ) > 0;
        $warning = count( $nonInitTrackingData ) > 0;

        if( $isValidTrackingData || $warning )
        {
            switch ( $mode )
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

                    foreach( $trackingData as $record )
                    {
                        $totalTime = TrackingUtils::addTime( $totalTime, $record['sessionTime'] );

                        if( !isset( $progressTab[ $record['moduleId'] ] ) || $record['progress'] > $progressTab[ $record['moduleId'] ] )
                        {
                            $progressTab[ $record['moduleId'] ] = $record['progress'];
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
                        if( !isset( $progressTab[ $nonInitTracking['moduleId'] ] ) )
                        {
                            $totalTime = TrackingUtils::addTime( $totalTime, $nonInitTracking['sessionTime'] );
                            $progressTab[ $nonInitTracking['moduleId'] ] = $nonInitProgress;
                        }
                        elseif( $nonInitProgress > $progressTab[ $nonInitTracking['moduleId'] ] )
                        {
                            $progressTab[ $nonInitTracking['moduleId'] ] = $nonInitProgress;
                        }
                    }

                    foreach( $progressTab as $progressTabValue )
                    {
                        $progress += $progressTabValue;
                    }
                    if( $this->nbModule > 0 )
                    {
                        $progress = (int)( $progress / $this->nbModule );
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
                        $this->generateTrackingList( $userId, 1);
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
                            $dateTab[ $dateDay ]['progress'][ $record['moduleId'] ] = $record['progress'];
                        }
                        else
                        {
                            $dateTab[ $dateDay ]['totalTime'] = TrackingUtils::addTime( $dateTab[ $dateDay ]['totalTime'], $record['sessionTime'] );

                            if( !isset( $dateTab[ $dateDay ]['progress'][ $record['moduleId'] ] )
                                || $record['progress'] > $dateTab[ $dateDay ]['progress'][ $record['moduleId'] ] )
                            {
                                $dateTab[ $dateDay ]['progress'][ $record['moduleId'] ] = $record['progress'];
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
                            if( isset( $record['progress'][ $nonInitTracking['moduleId'] ] ) )
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
                                $dateTab[ $nonInitTracking['date'] ]['progress'][ $nonInitTracking['moduleId'] ] = $nonInitProgress;
                            }
                            else
                            {
                                $dateTab[ $nonInitTracking['date'] ]['totalTime'] = TrackingUtils::addTime( $dateTab[ $nonInitTracking['date'] ]['totalTime'],
                                                                                                            $nonInitTracking['sessionTime'] );

                                if( !isset( $dateTab[ $nonInitTracking['date'] ]['progress'][ $nonInitTracking['moduleId'] ] ) )
                                {
                                    $dateTab[ $nonInitTracking['date'] ]['progress'][ $nonInitTracking['moduleId'] ] = $nonInitProgress;
                                }
                            }
                        }
                    }

                    // Compute progress for each day
                    $reverseDateTab = array_reverse( $dateTab );
                    $currentModuleProgress = array();
                    foreach( $reverseDateTab as $date => $values )
                    {
                        foreach( $values['progress'] as $moduleId => $progress )
                        {
                            if( !isset( $currentModuleProgress[ $moduleId ] ) || $progress > $currentModuleProgress[ $moduleId ] )
                            {
                                $currentModuleProgress[ $moduleId ] = $progress;
                            }
                        }
                        $dayProgress = 0;
                        foreach( $currentModuleProgress as $moduleProgress )
                        {
                            $dayProgress += $moduleProgress;
                        }
                        if( $this->nbModule > 0 )
                        {
                            $dayProgress = (int)( $dayProgress / $this->nbModule );
                        }
                        else
                        {
                            $dayProgress = 0;
                        }
                        $dateTab[$date]['progress']['final'] = $dayProgress;
                    }

                    foreach( $dateTab as $date => $values )
                    {
                        $this->trackingList[] = new TrackingEntry( $values['totalTime'],
                                                                   $date,
                                                                   0, 0, 0,
                                                                   $values['progress']['final'],
                                                                   $warning );
                    }
                    break;

                default:
                    throw new Exception( "Invalid mode : $mode" );
                    break;
            }
        }
    }
}

?>
