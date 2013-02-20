<?php

require_once dirname(__FILE__) . '/trackingUtils.lib.php';

/**
 * LearnPath Tracking Event Listener
 * 
 * @version LPUTRACK 1.0
 * @package LPUTRACK
 * @author Anh Thao PHAM <anhthao.pham@claroline.net>
 */
class LearnPathTrackingListerner extends EventDriven
{
    private $tblLearnPathTracking;

    public function __construct()
    {
        $this->tblLearnPathTracking = get_module_main_tbl( array( 'tracking_event' ) );        
    }
    
    /**
     * Retrieve all datas from event and add an entry in the 'tracking_event' table
     * @param Event $event
     * @throws Exception
     */
    public function updateModuleTracking( $event )
    {
        $eventArgs = $event->getArgs();
        
        $courseCode = isset( $eventArgs['courseCode'] ) ? $eventArgs['courseCode'] : null;
        $userId = isset( $eventArgs['userId'] ) ? (int)$eventArgs['userId'] : null;
        $type = isset( $eventArgs['type'] ) ? $eventArgs['type'] : "update";
        $date = isset( $eventArgs['date'] ) ? $eventArgs['date'] : date("Y-m-d H:i:s");
        
        $scoreRaw = isset( $eventArgs['scoreRaw'] ) ? (int)$eventArgs['scoreRaw'] : 0;
        $scoreMin = isset( $eventArgs['scoreMin'] ) ? (int)$eventArgs['scoreMin'] : 0;
        $scoreMax = isset( $eventArgs['scoreMax'] ) ? (int)$eventArgs['scoreMax'] : 0;
        $sessionTime = isset( $eventArgs['sessionTime'] ) ? $eventArgs['sessionTime'] : '0000:00:00.00';
        $status = isset( $eventArgs['status'] ) ? $eventArgs['status'] : "NOT ATTEMPTED";
        
        $learnPathId = isset( $eventArgs['learnPathId'] ) ? (int)$eventArgs['learnPathId'] : null;
        $moduleId = isset( $eventArgs['moduleId'] ) ? (int)$eventArgs['moduleId'] : null;
        $learnPathModuleId = isset( $eventArgs['learnPathModuleId'] ) ? (int)$eventArgs['learnPathModuleId'] : null;
        $userModuleProgressId = isset( $eventArgs['userModuleProgressId'] ) ? (int)$eventArgs['userModuleProgressId'] : null;
        
        if(is_null( $learnPathId ) || is_null( $moduleId ) )
        {
            if( is_null( $learnPathModuleId ) && !is_null( $userModuleProgressId ) )
            {
                $learnPathModuleId = TrackingUtils::getLearnPathModuleIdFromUserModuleProgressId( $courseCode, $userModuleProgressId );
            }
            
            if( !is_null( $learnPathModuleId ) )
            {
                if( is_null( $learnPathId ) )
                {
                    $learnPathId = TrackingUtils::getLearnPathIdFromRelLearnPathModuleId( $courseCode, $learnPathModuleId );
                }
                if(is_null( $moduleId ))
                {
                    $moduleId = TrackingUtils::getModuleIdFromRelLearnPathModuleId( $courseCode, $learnPathModuleId );
                }
            }
        }
        
        if( !is_null( $courseCode ) && !is_null( $userId ) && !is_null( $learnPathId ) && !is_null( $moduleId ) )
        {
            $data = LearnPathTrackingListerner::generateData( $learnPathId, $moduleId, $scoreRaw, $scoreMin, $scoreMax, $sessionTime, $status );
            if( $type == "init" )
            {
                $this->addNewLearnPathTracking( $courseCode, $userId, $date, $data, 'learnpath_tracking_init' );
            }
            else
            {
                $this->addNewLearnPathTracking( $courseCode, $userId, $date, $data, 'learnpath_tracking' );
            }
        }
        else
        {
            throw new Exception( "Unable to generate a tracking event log due to invalid data" );
        }
    }
    
    /**
     * Add entry in 'tracking_event' table
     * @param string $courseCode
     * @param int $userId
     * @param date $date
     * @param string $data
     * @param string $type
     */
    protected function addNewLearnPathTracking( $courseCode, $userId, $date, $data, $type = 'learnpath_tracking')
    {
        if( claro_is_user_authenticated() )
        {
            Claroline::getDatabase()->exec(
                "INSERT
                   INTO `{$this->tblLearnPathTracking['tracking_event']}` (`course_code`, `user_id`, `date`, `data`, `type`)
                 VALUES (" . Claroline::getDatabase()->quote( $courseCode ) . ", "
                           . Claroline::getDatabase()->escape( (int)$userId ) . ", "
                           . Claroline::getDatabase()->quote( $date ) . ", "
                           . Claroline::getDatabase()->quote( $data ) . ", "
                           . Claroline::getDatabase()->quote( $type )
                           . ")"
            );
        }
    }
    
    /**
     * Generate a string to be inserted in the 'data' field of the 'tracking_event' table
     * @param int $learnPathId
     * @param int $moduleId
     * @param int $scoreRaw
     * @param int $scoreMin
     * @param int $scoreMax
     * @param string $sessionTime
     * @param string $status
     * @return string
     */
    protected static function generateData( $learnPathId, $moduleId, $scoreRaw, $scoreMin, $scoreMax, $sessionTime, $status )
    {
        $data = $learnPathId . ";" . $moduleId . ";" . $scoreRaw . ";" . $scoreMin . ";" . $scoreMax . ";" . $sessionTime . ";" . $status;
        return $data;
    }  
    
    /**
     * Replace the default time set to a document-type module tracking
     * by the time generated by the time counting script
     * @param EVENT $event
     * @return null
     */
    public function updateDocumentTrackingTime( $event )
    {
        $eventArgs = $event->getArgs();
        
        $date = isset( $eventArgs['date'] ) ? $eventArgs['date'] : null;
        $sessionTime = isset( $eventArgs['sessionTime'] ) ? $eventArgs['sessionTime'] : null;
        $courseCode = isset( $eventArgs['courseCode'] ) ? $eventArgs['courseCode'] : null;
        $userId = isset( $eventArgs['userId'] ) ? (int)$eventArgs['userId'] : null;
        $learnPathId = isset( $eventArgs['learnPathId'] ) ? (int)$eventArgs['learnPathId'] : null;
        $moduleId = isset( $eventArgs['moduleId'] ) ? (int)$eventArgs['moduleId'] : null;
        
        if( is_null( $courseCode ) || is_null( $userId ) || is_null( $date )
            || is_null( $learnPathId ) || is_null( $moduleId ) || is_null( $sessionTime ) )
        {
            return null;
        }
        
        if( $GLOBALS['tlabelReq'] != 'CLLNP'
            || claro_get_current_course_id() != $courseCode
            || claro_get_current_user_id() != $userId )
        {
            return null;
        }
        
        $dataTextPattern = $learnPathId . ';' . $moduleId . ';%';
        
        $tblTrackingEvent = get_module_main_tbl( array( 'tracking_event' ) );
        
        $resultSet = Claroline::getDatabase()->query(
            "SELECT id, data
               FROM `{$tblTrackingEvent['tracking_event']}`
              WHERE date = " . Claroline::getDatabase()->quote( $date ) . "
                AND course_code = " . Claroline::getDatabase()->quote( $courseCode ) . "
                AND user_id = " . Claroline::getDatabase()->escape( (int)$userId ) . "
                AND type = 'learnpath_tracking'
                AND data LIKE " . Claroline::getDatabase()->quote( $dataTextPattern )
        );
        $trackingRow = $resultSet->fetch();
        if( $trackingRow )
        {echo ' chalut tracking OK ';
            $dataTab = explode( ';', $trackingRow['data'] );
            $dataTab[5] = $sessionTime;
            $newData = implode( ';', $dataTab );
            Claroline::getDatabase()->exec(
                "UPDATE `{$tblTrackingEvent['tracking_event']}`
                    SET data = " . Claroline::getDatabase()->quote( $newData ) . "
                  WHERE id = " . Claroline::getDatabase()->escape( (int)$trackingRow['id'] )
            );
        }
    }
}

?>
