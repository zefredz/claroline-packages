<?php

/**
 * Tracking for a module
 *
 * @version LPUTRACK 1.0
 * @package LPUTRACK
 * @author Anh Thao PHAM <anhthao.pham@claroline.net>
 */
class TrackingModule
{
    private $courseCode;
    private $learnPathId;
    private $moduleId;
    private $moduleName;
    private $contentType;
    private $trackingList;
    private $generalTracking;
    private $mode;

    /**
     * Constructor
     * @param string $courseCode
     * @param int $learnPathId
     * @param int $moduleId
     * @param string $moduleName
     * @param string $contentType
     */
    public function __construct( $courseCode, $learnPathId, $moduleId, $moduleName, $contentType )
    {
        $this->courseCode = $courseCode;
        $this->learnPathId = $learnPathId;
        $this->moduleId = $moduleId;
        $this->moduleName = $moduleName;
        $this->contentType = $contentType;
        $this->trackingList = null;
        $this->generalTracking = null;
        $this->mode = 0;
    }

    /**
     * Get code of the course the module belongs to
     * @return string The course code
     */
    public function getCourseCode()
    {
        return $this->courseCode;
    }

    /**
     * Get id of the learnPath the module belongs to
     * @return int
     */
    public function getLearnPathId()
    {
        return $this->learnPathId;
    }

    /**
     * Get the id of the module
     * @return int
     */
    public function getModuleId()
    {
        return $this->moduleId;
    }

    /**
     * Get the name of the module
     * @return string
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * Get the content type of the module
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Get the list of tracking generated for the module in mode 2 or 3
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
     * Get the tracking for the module generated in mode 1
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
     * Generate tracking for the module
     * @param int $userId
     * @param int $mode 1 : An unique tracking
     *                  2 : A tracking per day
     *                  3 : All available tracking
     * @throws Exception
     */
    public function generateTrackingList( $userId, $mode )
    {
        $this->mode = $mode;
        $trackingData = TrackingData::getInstance()->getModuleRecords( array( $userId ),
                                                                       array( $this->courseCode ),
                                                                       array( $this->learnPathId ),
                                                                       array( $this->moduleId ) );
        $nonInitTrackingData = TrackingUtils::getNonInitLearnPathTrackingList( $userId, $this->courseCode, $this->learnPathId, $this->moduleId );
        $isValidTrackingData = count( $trackingData ) > 0;
        $warning = count( $nonInitTrackingData ) > 0;

        if( $isValidTrackingData || $warning )
        {
            if( $warning )
            {
                $nonInitProgress = TrackingUtils::computeProgress( $nonInitTrackingData[0]['courseCode'],
                                                                   $nonInitTrackingData[0]['moduleId'],
                                                                   $nonInitTrackingData[0]['status'],
                                                                   $nonInitTrackingData[0]['scoreRaw'],
                                                                   $nonInitTrackingData[0]['scoreMin'],
                                                                   $nonInitTrackingData[0]['scoreMax'] );
            }
            switch( $mode )
            {
                // Generate an unique entry from all records
                case 1 :
                    if( $isValidTrackingData )
                    {
                        $dateTime = new DateTime( $trackingData[0]['date'] );
                        $latestDate = $dateTime->format( "Y-m-d" );
                        $progress = $trackingData[0]['progress'];
                        $scoreRaw = $trackingData[0]['scoreRaw'];
                        $scoreMin = $trackingData[0]['scoreMin'];
                        $scoreMax = $trackingData[0]['scoreMax'];
                        $totalTime = "0000:00:00";
                        $firstDateTime = new DateTime( $trackingData[count( $trackingData ) - 1]['date'] );
                        $firstDate = $firstDateTime->format( "Y-m-d" );
                    }
                    else
                    {
                        $latestDate = $nonInitTrackingData[0]['date'];
                        $progress = $nonInitProgress;
                        $scoreRaw = $nonInitTrackingData[0]['scoreRaw'];
                        $scoreMin = $nonInitTrackingData[0]['scoreMin'];
                        $scoreMax = $nonInitTrackingData[0]['scoreMax'];
                        $totalTime = $nonInitTrackingData[0]['sessionTime'];
                        $firstDate = "-";
                    }

                    foreach( $trackingData as $record )
                    {
                        if( $record['progress'] > $progress )
                        {
                            $progress = $record['progress'];
                        }
                        if( $record['scoreRaw'] > $scoreRaw )
                        {
                            $scoreRaw = $record['scoreRaw'];
                        }
                        $totalTime = TrackingUtils::addTime( $totalTime, $record['sessionTime'] );
                    }
                    if( $isValidTrackingData && $warning )
                    {
                        if( $nonInitProgress > $progress )
                        {
                            $progress = $nonInitProgress;
                        }
                        if( $nonInitTrackingData[0]['scoreRaw'] > $scoreRaw )
                        {
                            $scoreRaw = $nonInitTrackingData[0]['scoreRaw'];
                        }
                    }
                    $this->generalTracking = new TrackingEntry( $totalTime, $latestDate, $scoreRaw, $scoreMin, $scoreMax, $progress, $warning );
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
                        if( !isset( $dateTab[$dateDay] ) )
                        {
                            $dateTab[ $dateDay ] = array();
                            $dateTab[ $dateDay ]['progress'] = $record['progress'];
                            $dateTab[ $dateDay ]['scoreRaw'] = $record['scoreRaw'];
                            $dateTab[ $dateDay ]['scoreMin'] = $record['scoreMin'];
                            $dateTab[ $dateDay ]['scoreMax'] = $record['scoreMax'];
                            $dateTab[ $dateDay ]['totalTime'] = $record['sessionTime'];
                        }
                        else
                        {
                            if( $record['progress'] > $dateTab[ $dateDay ]['progress'] )
                            {
                                $dateTab[ $dateDay ]['progress'] = $record['progress'];
                            }
                            if( $record['scoreRaw'] > $dateTab[ $dateDay ]['scoreRaw'] )
                            {
                                $dateTab[ $dateDay ]['scoreRaw'] = $record['scoreRaw'];
                            }
                            $dateTab[ $dateDay ]['totalTime'] = TrackingUtils::addTime( $dateTab[ $dateDay ]['totalTime'], $record['sessionTime'] );
                        }
                    }

                    foreach( $dateTab as $date => $values )
                    {
                        $this->trackingList[] = new TrackingEntry( $values['totalTime'], $date,
                                                                   $values['scoreRaw'], $values['scoreMin'],
                                                                   $values['scoreMax'], $values['progress'], $warning );
                    }
                    if( count( $this->trackingList ) == 0 )
                    {
                        $this->trackingList[] = new TrackingEntry( $nonInitTrackingData[0]['sessionTime'],
                                                                   $nonInitTrackingData[0]['date'],
                                                                   $nonInitTrackingData[0]['scoreRaw'],
                                                                   $nonInitTrackingData[0]['scoreMin'],
                                                                   $nonInitTrackingData[0]['scoreMax'],
                                                                   $nonInitProgress, true );
                    }
                    break;

                // Generate an entry for each record
                case 3 :
                    if( is_null( $this->generalTracking ) )
                    {
                        $this->generateTrackingList( $userId, 1 );
                    }
                    foreach( $trackingData as $record )
                    {
                        $this->trackingList[] = new TrackingEntry( $record['sessionTime'], $record['date'],
                                                                   $record['scoreRaw'], $record['scoreMin'],
                                                                   $record['scoreMax'], $record['progress'] );
                    }
                    if( count( $this->trackingList ) == 0 )
                    {
                        $this->trackingList[] = new TrackingEntry( $nonInitTrackingData[0]['sessionTime'],
                                                                   $nonInitTrackingData[0]['date'],
                                                                   $nonInitTrackingData[0]['scoreRaw'],
                                                                   $nonInitTrackingData[0]['scoreMin'],
                                                                   $nonInitTrackingData[0]['scoreMax'],
                                                                   $nonInitProgress, true );
                    }
                    break;

                default :
                    throw new Exception( "Invalid mode : $mode" );
                    break;
            }
        }
    }

    /**
     * Get path of a module
     * @return string
     */
    public function getModulePath()
    {
        return TrackingUtils::getPathFromModule( $this->courseCode, $this->moduleId );
    }
}

?>
