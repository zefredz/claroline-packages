<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * Librairy to manage attempt, item attempt & item attempt list
 *
 * @version 0.1 $Revision$
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package CLLP
 * @author Sebastien Piraux
 * @author Dimitri Rambout <dim@claroline.net>
 */

/**
 * Attempt class
 *
 * Manage an attempt in a learning path
 *
 * @author Sebastien Piraux
 * @author Dimitri Rambout <dim@claroline.net>
 */
class attempt
{
    /**
     * @var $id id of attempt, -1 if attempt doesn't exist already
     */
    private $id;

    /**
     * @var $pathId id of the path related to this attempt
     */
    private $pathId;

    /**
     * @var $userId id of the user that realize this attempt
     */
    private $userId;

    /**
     * @var $lastItem id of the last item the user was in (to resume progression when re-entering the path)
     */
    private $lastItemId;

    /**
     * @var $progress pourcent of progression in path $pathId for user $userId
     */
    private $progress;

    /**
     * @var $attemptNumber this attempt is the $attemptNumber for path $pathId
     */
    private $attemptNumber;

    /**
     * @var $itemAttemptList object that represent the list of all item attempts
     */
    private $itemAttemptList;

    /**
     * @var $tblAttempt name of the item table
     */
    private $tblAttempt;
    /**
     * @var $tblItemAttempt name of the item attempt table
     */
    private $tblItemAttempt;

    /**
     * Constructor
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function __construct()
    {
        $this->id = (int) -1;
        $this->pathId = (int) -1;
        $this->userId = (int) -1;
        $this->lastItemId = (int) -1;
        $this->progress = (int) 0;
        $this->attemptNumber = (int) -1;
        $this->itemAttemptList = null;

        // define module table names
        $tblNameList = array(
            'lp_attempt',
            'lp_item_attempt'
        );

        // convert to Claroline course table names
        $tbl_lp_names = get_module_course_tbl( $tblNameList, claro_get_current_course_id() );
        $this->tblAttempt = $tbl_lp_names['lp_attempt'];
        $this->tblItemAttempt = $tbl_lp_names['lp_item_attempt'];

    }

    /**
     * If no attemptNumber is specified load the last attempt for $userId in $pathId from DB
     * else load the specified attempt
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param integer $pathId id of path
     * @param int $userId id of the user
     * @param int $attemptNumber 
     * @return boolean load successfull ?
     */
    public function load($pathId, $userId, $attemptNumber = null)
    {
        // $userId will be null if user is anonymous
        if( is_null($userId) )
        {
          return false;
        }

        $sql = "SELECT
                    `id`,
                    `path_id`,
                    `user_id`,
                    `last_item_id`,
                    `progress`,
                    `attempt_number`
                FROM `".$this->tblAttempt."`
                WHERE `path_id` = ".(int) $pathId . "
                AND `user_id` = ".(int) $userId;

        if( is_null($attemptNumber) )
        {
          // take last attempt for this user on this path
          $sql .= " ORDER BY `attempt_number` DESC
          		LIMIT 1";
        }
        else
        {
          // take specified attempt
          $sql .= " AND `attempt_number` = ".(int) $attemptNumber;
        }

        $data = claro_sql_query_get_single_row($sql);

        if( $data !== false && !empty($data) )
        {
            // from query
            $this->id = (int) $data['id'];
            $this->pathId = (int) $data['path_id'];
            $this->userId = $data['user_id'];
            $this->lastItemId = $data['last_item_id'];
            $this->progress = $data['progress'];
            $this->attemptNumber = (int) $data['attempt_number'];

            // load list of related item attempts
            $itemAttemptTmpList = new itemAttemptList();
            $this->itemAttemptList = $itemAttemptTmpList->load($this->id);

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Save attempt into DB
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return mixed false or id of the record
     */
    public function save()
    {
        // TODO compute progress from itemAttemptList and save itemAttemptList
        if( $this->id == -1 )
              {
            // if no attemptNumber find the higher possible value
            if( $this->attemptNumber == -1 )
            {
              $this->setHigherAttemptNumber();
            }

            // insert
            $sql = "INSERT INTO `".$this->tblAttempt."`
                    SET `path_id` = '".(int) $this->pathId."',
                        `user_id` = '".(int) $this->userId."',
                        `last_item_id` = '".(int) $this->lastItemId."',
                        `progress` = '".(int) $this->progress."',
                        `attempt_number` = '".(int) $this->attemptNumber."'";

            // execute the creation query and get id of inserted assignment
            $insertedId = claro_sql_query_insert_id($sql);

            if( $insertedId )
            {
                $this->id = (int) $insertedId;

                return $this->id;
            }
            else
            {
                return false;
            }
        }
        else
        {
            // update, main query
            $sql = "UPDATE `".$this->tblAttempt."`
                    SET `path_id` = '".(int) $this->pathId."',
                        `user_id` = '".(int) $this->userId."',
                        `last_item_id` = '".(int) $this->lastItemId."',
                        `progress` = '".(int) $this->progress."',
                        `attempt_number` = '".(int) $this->attemptNumber."'
                    WHERE `id` = '".(int) $this->id."'";

            // execute and return main query
            if( claro_sql_query($sql) )
            {
                return $this->id;
            }
            else
            {
                return false;
            }
        }
    }

    /**
     * Delete attempt
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */
    public function delete()
    {
        if( $this->id == -1 ) return true;

        $sql = "DELETE FROM `" . $this->tblItemAttempt . "`
                WHERE `attempt_id` = " . (int) $this->id;
        
        if (claro_sql_query($sql) == false ) return false;
        
        $sql = "DELETE FROM `" . $this->tblAttempt . "`
                WHERE `id` = " . (int) $this->id ;

        if( claro_sql_query($sql) == false ) return false;

        $this->id = -1;
        return true;
    }

    //-- Getter & Setter

    /**
     * Get id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int
     */
    public function getId()
    {
        return (int) $this->id;
    }

    /**
     * Get path id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int
     */
    public function getPathId()
    {
        return (int) $this->pathId;
    }

    /**
     * Set path id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param int $value id of the path
     * @return int
     */
    public function setPathId($value)
    {
        $this->pathId = (int) $value;
    }

    /**
     * Get user id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int
     */
    public function getUserId()
    {
        return (int) $this->userId;
    }

    /**
     * Set user id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param int $value id of the user
     * @return int
     */
    public function setUserId($value)
    {
        $this->userId = (int) $value;
    }

    /**
     * Get last item id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int
     */
    public function getLastItemId()
    {
        return (int) $this->lastItemId;
    }

    /**
     * Set last item id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param int $value id of the last item viewed
     * @return int
     */
    public function setLastItemId($value)
    {
        $this->lastItemId = (int) $value;
    }

    /**
     * Get progress for this path (pourcent)
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int
     */
    public function getProgress()
    {
        return (int) $this->progress;
    }

    /**
     * Set progress for this path (pourcent)
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param int $value progression
     * @return int
     */
    public function setProgress($value)
    {
      // value must be between 0 and 100
      $value = max(0,$value);
      $value = min(100,$value);

        $this->progress = (int) $value;
    }

    /**
     * Get attempt number for this path and this user
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int
     */
    public function getAttemptNumber()
    {
        return (int) $this->attemptNumber;
    }

    /**
     * Set attempt number for this path and this user
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param int $value progression
     * @return int
     */
    public function setAttemptNumber($value)
    {
        $this->attemptNumber = (int) $value;
    }

    /**
     * Set the highest attempt number for this path and this user
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int
     */
    public function setHigherAttemptNumber()
    {
      // use max instead of count to handle suppressed attempts
      $sql = "SELECT MAX(`attempt_number`)
      		FROM `".$this->tblAttempt."`
      		WHERE `path_id` = ".(int) $this->pathId."
      		AND `user_id` = ".(int) $this->userId;

      $higherAttempt = claro_sql_query_get_single_value($sql);

      if( is_null($higherAttempt)  || !$higherAttempt )
      {
      	// error in query
      	$higherAttempt = 1;
      }
      else
      {
      	// value is at least 1
      	$higherAttempt = max(1,$higherAttempt);
      }

      $this->setAttemptNumber($higherAttempt);
    }
}

include_once get_module_path('CLLP') . '/lib/utils.lib.php';
/**
 * itemAttempt class
 *
 * Manage an itemAttempt in a learning path
 *
 * @author Sebastien Piraux
 * @author Dimitri Rambout <dim@claroline.net>
 */
class itemAttempt
{
    /**
     * @var $id id of item attempt, -1 if item attempt doesn't exist already
     */
    private $id;

    /**
     * @var $attemptId id of the attempt
     */
    private $attemptId;

    /**
     * @var $itemId id of the item
     */
    private $itemId;

    /**
     * @var $location SCORM location (position in the SCO)
     */
    private $location;

    /**
     * @var $completionStatus SCORM completion_status (completion level of the SCO)
     */
    private $completionStatus;

    /**
     * @var $entry SCORM entry (is the learner entered in the SCO)
     */
    private $entry;

    /**
     * @var $scoreRaw SCORM score.raw (score of the learner)
     */
    private $scoreRaw;

    /**
     * @var $scoreMin SCORM score.min (minimum possible score)
     */
    private $scoreMin;

    /**
     * @var $scoreMax SCORM score.max (maximum possible score)
     */
    private $scoreMax;

    /**
     * @var $totalTime SCORM total_time (sum of all session_time of this SCO)
     */
    private $totalTime;

    /**
     * @var $sessionTime SCORM session_time (time spent by learner for this session in the SCO)
     */
    private $sessionTime;

    /**
     * @var $suspendData SCORM suspend_data (data the SCO would like to get back on next attempt)
     */
    private $suspendData;

    /**
     * @var $credit SCORM credit ( indicates whether the learner will be credited for performance)
     */
    private $credit;

    /**
     * @var $tblItemAttempt name of the item table
     */
    private $tblItemAttempt;

    /**
     * Constructor
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function __construct()
    {
        $this->id = (int) -1;
        $this->attemptId = (int) -1;
        $this->itemId = (int) -1;
        $this->location = '';
        $this->completionStatus = 'NOT ATTEMPTED';
        $this->entry = 'AB-INITIO';
        $this->scoreRaw = (int) 0;
        $this->scoreMin = (int) 0;
        $this->scoreMax = (int) 100;
        $this->totalTime = ''; // TODO correct format
        $this->sessionTime = ''; // TODO correct format
        $this->suspendData = '';
        $this->credit = 'NO-CREDIT';

        // define module table names
        $tblNameList = array(
            'lp_item_attempt'
        );

        // convert to Claroline course table names
        $tbl_lp_names = get_module_course_tbl( $tblNameList, claro_get_current_course_id() );
        $this->tblItemAttempt = $tbl_lp_names['lp_item_attempt'];

    }

    /**
     * Load item attempt
     *
     * @param int $attemptId id of attempt
     * @param int $itemId id of item
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */
    public function load($attemptId, $itemId)
    {
      $sql = "SELECT `id`,
            `attempt_id`,
            `item_id`,
            `location`,
            `completion_status`,
            `entry`,
            `score_raw`,
            `score_min`,
            `score_max`,
            `total_time`,
            `session_time`,
            `suspend_data`,
            `credit`
            FROM `". $this->tblItemAttempt."`
            WHERE `attempt_id` = ". (int) $attemptId ."
            AND `item_id` = ". (int) $itemId ;
    
        $data = claro_sql_query_get_single_row($sql);

        if( $data !== false && !empty($data) )
        {
            $this->id = (int) $data['id'];
            $this->attemptId = (int) $data['attempt_id'];
            $this->itemId = (int) $data['item_id'];
            $this->location = $data['location'];
            $this->completionStatus = $data['completion_status'];
            $this->entry = $data['entry'];
            $this->scoreRaw = (int) $data['score_raw'];
            $this->scoreMin = (int) $data['score_min'];
            $this->scoreMax = (int) $data['score_max'];
            
            $this->totalTime = unixToScormTime($data['total_time']); // TODO correct format
            $this->sessionTime = unixToScormTime($data['session_time']); // TODO correct format
            $this->suspendData = $data['suspend_data'];
            $this->credit = $data['credit'];

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Save item attempt to DB
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return mixed false or id of the record
     */
    public function save()
    {        
        $tmpSessionTime = scormToUnixTime( $this->sessionTime );
        $tmpTotalTime = scormToUnixTime( $this->totalTime );
        //$sessionTime = unixToDHMS( $tmpSessionTime );
        $sessionTime = $tmpSessionTime;
        //$totalTime = unixToDHMS( $tmpTotalTime + $tmpSessionTime );
        $totalTime = $tmpTotalTime + $tmpSessionTime;
        
        if( $this->id == -1 )
        {
            // insert
            $sql = "INSERT INTO `".$this->tblItemAttempt."`
                    SET `attempt_id` = '".(int) $this->attemptId."',
                      `item_id` = '".(int) $this->itemId."',
                      `location` = '".addslashes($this->location)."',
                      `completion_status` = '".addslashes($this->completionStatus)."',
                      `entry` = '".addslashes($this->entry)."',
                      `score_raw` = '".(int) $this->scoreRaw."',
                      `score_min` = '".(int) $this->scoreMin."',
                      `score_max` = '".(int) $this->scoreMax."',
                      `total_time` = '".addslashes($totalTime)."',
                      `session_time` = '".addslashes($sessionTime)."',
                      `suspend_data` = '".addslashes($this->suspendData)."',
                        `credit` = '".addslashes($this->credit)."'";

            // execute the creation query and get id of inserted assignment
            $insertedId = claro_sql_query_insert_id($sql);

            if( $insertedId )
            {
                $this->id = (int) $insertedId;

                return $this->id;
            }
            else
            {
                return false;
            }
        }
        else
        {
            // update, main query
            $sql = "UPDATE `".$this->tblItemAttempt."`
                    SET `attempt_id` = '".(int) $this->attemptId."',
                        `item_id` = '".(int) $this->itemId."',
                        `location` = '".addslashes($this->location)."',
                        `completion_status` = '".addslashes($this->completionStatus)."',
                        `entry` = '".addslashes($this->entry)."',
                        `score_raw` = '".(int) $this->scoreRaw."',
                        `score_min` = '".(int) $this->scoreMin."',
                        `score_max` = '".(int) $this->scoreMax."',
                        `total_time` = '".addslashes($totalTime)."',
                        `session_time` = '".addslashes($sessionTime)."',
                        `suspend_data` = '".addslashes($this->suspendData)."',
                        `credit` = '".addslashes($this->credit)."'
                    WHERE `id` = '".(int) $this->id."'";

            // execute and return main query
            if( claro_sql_query($sql) )
            {
                return $this->id;
            }
            else
            {
                return false;
            }
        }
    }

    /**
     * Delete item attempt
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */
    public function delete()
    {
        if( $this->id == -1 ) return true;

        // TODO delete all item_attempts

        $sql = "DELETE FROM `" . $this->tblItemAttempt . "`
                WHERE `id` = " . (int) $this->id ;

        if( claro_sql_query($sql) == false ) return false;

        $this->id = -1;
        return true;
    }

    /**
     * Check that all scorm data are consistent and values are correct
     */
    public function validate()
    {
        return true;
    }

    /**
     * Get id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int
     */
    public function getId()
    {
        return (int) $this->id;
    }

    /**
     * Get attempt id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int
     */
    public function getAttemptId()
    {
        return (int) $this->attemptId;
    }

    /**
     * Set attempt id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param int $value id of the attempt
     */
    public function setAttemptId($value)
    {
        $this->attemptId = (int) $value;
    }

    /**
     * Get item id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int
     */
    public function getItemId()
    {
        return (int) $this->itemId;
    }

    /**
     * Set item id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param int $value id of the item
     */
    public function setItemId($value)
    {
        $this->itemId = (int) $value;
    }

    /**
    * set location
    *
    * @author Sebastien Piraux <pir@cerdecam.be>
    * @param string $value location in the SCO
    */
    public function setLocation($value)
    {
       $this->location = trim($value);
    }

    /**
     * Get location
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string location in the SCO
     */
    public function getLocation()
    {
       return $this->location;
    }

    /**
     * Set completionStatus
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value completionStatus of the SCO
     */
    public function setCompletionStatus($value)
    {
        $acceptedValues = array('NOT ATTEMPTED','PASSED','FAILED','COMPLETED','BROWSED','INCOMPLETE','UNKNOWN');

        if( in_array(strtoupper($value), $acceptedValues) )
        {
            $this->completionStatus = $value;
            return true;
        }
        return false;
    }

    /**
     * Get completionStatus
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string completionStatus of the SCO
     */
    public function getCompletionStatus()
    {
        return $this->completionStatus;
    }

    /**
     * Set entry
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value entry of the SCO
     */
    public function setEntry($value)
    {
        $acceptedValues = array('AB-INITIO','RESUME','');

        if( in_array(strtoupper($value), $acceptedValues) )
        {
            $this->entry = $value;
            return true;
        }
        return false;
    }

    /**
     * Get entry
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string entry of the SCO
     */
    public function getEntry()
    {
       return $this->entry;
    }

    /**
     * Get score raw
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int score raw
     */
    public function getScoreRaw()
    {
        return (int) $this->scoreRaw;
    }

    /**
     * Set score raw
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param int $value score raw
     */
    public function setScoreRaw($value)
    {
        $this->scoreRaw = (int) $value;
    }

    /**
     * Get score min
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int score min
     */
    public function getScoreMin()
    {
        return (int) $this->scoreMin;
    }

    /**
     * Set score min
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param int $value score min
     */
    public function setScoreMin($value)
    {
        $this->scoreMin = (int) $value;
    }

    /**
     * Get score max
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int score max
     */
    public function getScoreMax()
    {
        return (int) $this->scoreMax;
    }

    /**
     * Set score max
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param int $value score max
     */
    public function setScoreMax($value)
    {
        $this->scoreMax = (int) $value;
    }

    /**
     * Get sessionTime
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int session time in SCO
     */
    public function getSessionTime()
    {
        return $this->sessionTime;
    }

    /**
     * Set sessionTime
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param int $value sessionTime
     */
    public function setSessionTime($value)
    {
        $this->sessionTime = $value;
    }

    /**
     * Get totalTime
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int total time in SCO
     */
    public function getTotalTime()
    {
        return $this->totalTime;
    }

    /**
     * Set totalTime
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param int $value totalTime
     */
    public function setTotalTime($value)
    {
        $this->totalTime = $value;
    }


    /**
     * Get suspend data
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string suspend data
     */
    public function getSuspendData()
    {
        return $this->suspendData;
    }

    /**
     * Set suspend data
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value suspend data
     */
    public function setSuspendData($value)
    {
        $this->suspendData = $value;
    }

    /**
    * Set credit
    *
    * @author Sebastien Piraux <pir@cerdecam.be>
    * @param credit $value of the SCO
    */
    public function setCredit($value)
    {
        $acceptedValues = array('CREDIT','NO-CREDIT');
        
        if( in_array(strtoupper($value), $acceptedValues) )
        {
            $this->credit = $value;
            return true;
        }
        return false;
    }
    
    /**
    * Get credit
    *
    * @author Sebastien Piraux <pir@cerdecam.be>
    * @return string credit of the SCO
    */
    public function getCredit()
    {
        return $this->credit;
    }

}

/**
 * itemAttemptList class
 *
 * Manage an itemAttempt list in a learning path
 *
 * @author Sebastien Piraux
 * @author Dimitri Rambout <dim@claroline.net>
 */
class itemAttemptList
{
    private $tblPath;
    private $tblItem;

    /**
     * Constructor
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function __construct()
    {
        // define module table names
        $tblNameList = array(
            'lp_item_attempt'
        );

        // convert to Claroline course table names
        $tbl_lp_names = get_module_course_tbl( $tblNameList, claro_get_current_course_id() );
        $this->tblItemAttempt = $tbl_lp_names['lp_item_attempt'];
    }
    
    /**
     * Load a list of item attempt
     *
     * @param int $attemptId id of the attempt
     * @return array item attempt list
     */

    public function load($attemptId)
    {
        $sql = "SELECT
                    `id`,
                    `item_id`,
                    `location`,
                    `completion_status`,
                    `entry`,
                    `score_raw`,
                    `score_min`,
                    `score_max`,
                    `total_time`,
                    `session_time`,
                    `suspend_data`,
                    `credit`
                FROM
                    `". $this->tblItemAttempt."`
                WHERE
                    `attempt_id` = ". (int) $attemptId ;

        $data = claro_sql_query_fetch_all_rows($sql);

        if( is_array($data) && !empty($data) )
        {
            // for simplier use later we will use the item id as index for this array
            foreach( $data as $itemAttempt )
            {
                $itemAttemptList[$itemAttempt['item_id']] = $itemAttempt;
            }
        }
        else
        {
            $itemAttemptList = array();
        }
        return $itemAttemptList;
    }
}