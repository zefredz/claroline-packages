<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * CLAROLINE
 *
 * $Revision$
 *
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLLP
 *
 * @author Sebastien Piraux
 *
 */

/*
 CREATE TABLE IF NOT EXISTS `__CL_COURSE__lp_attempt` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `path_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `last_item_id` INT(11),
  `progress` INT(11),
  `attempt_number` INT(11),
  PRIMARY KEY(`id`)
) TYPE=MyISAM;
 */
class attempt
{
    /**
     * @var $id id of attempt, -1 if attempt doesn't exist already
     */
    var $id;

    /**
     * @var $pathId id of the path related to this attempt
     */
    var $pathId;

    /**
     * @var $userId id of the user that realize this attempt
     */
    var $userId;

    /**
     * @var $lastItem id of the last item the user was in (to resume progression when re-entering the path)
     */
    var $lastItemId;

    /**
     * @var $progress pourcent of progression in path $pathId for user $userId
     */
    var $progress;

    /**
     * @var $attemptNumber this attempt is the $attemptNumber for path $pathId
     */
    var $attemptNumber;

	/**
	 * @var $itemAttemptList object that represent the list of all item attempts
	 */
	var $itemAttemptList;

    /**
     * @var $tblAttempt name of the item table
     */
    var $tblAttempt;

    /**
     * Constructor
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
	function attempt()
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
            'lp_attempt'
        );

        // convert to Claroline course table names
        $tbl_lp_names = get_module_course_tbl( $tblNameList, claro_get_current_course_id() );
        $this->tblAttempt = $tbl_lp_names['lp_attempt'];

	}

    /**
     * if no attemptNumber is specified load the last attempt for $userId in $pathId from DB
     * else loadthe specified attempt
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param integer $id id of path
     * @return boolean load successfull ?
     */
	function load($pathId, $userId, $attemptNumber = null)
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
     * save attempt to DB
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return mixed false or id of the record
     */
	function save()
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
     * delete attempt
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */
	function delete()
	{
        if( $this->id == -1 ) return true;

		// TODO delete all item_attempts

        $sql = "DELETE FROM `" . $this->tblAttempt . "`
                WHERE `id` = " . (int) $this->id ;

        if( claro_sql_query($sql) == false ) return false;

        $this->id = -1;
        return true;
	}

    //-- Getter & Setter

    /**
     * get id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int
     */
    function getId()
    {
        return (int) $this->id;
    }

    /**
     * get path id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int
     */
    function getPathId()
    {
        return (int) $this->pathId;
    }

    /**
     * set path id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $value int id of the path
     * @return int
     */
    function setPathId($value)
    {
        $this->pathId = (int) $value;
    }

    /**
     * get user id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int
     */
    function getUserId()
    {
        return (int) $this->userId;
    }

    /**
     * set user id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $value int id of the user
     * @return int
     */
    function setUserId($value)
    {
        $this->userId = (int) $value;
    }

    /**
     * get last item id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int
     */
    function getLastItemId()
    {
        return (int) $this->lastItemId;
    }

    /**
     * set last item id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $value int id of the last item viewed
     * @return int
     */
    function setLastItemId($value)
    {
        $this->lastItemId = (int) $value;
    }

    /**
     * get progress for this path (pourcent)
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int
     */
    function getProgress()
    {
        return (int) $this->progress;
    }

    /**
     * set progress for this path (pourcent)
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $value int progression
     * @return int
     */
    function setProgress($value)
    {
    	// value must be between 0 and 100
    	$value = max(0,$value);
    	$value = min(100,$value);

        $this->progress = (int) $value;
    }

    /**
     * get attempt number for this path and this user
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int
     */
    function getAttemptNumber()
    {
        return (int) $this->attemptNumber;
    }

    /**
     * set attempt number for this path and this user
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $value int progression
     * @return int
     */
    function setAttemptNumber($value)
    {
        $this->attemptNumber = (int) $value;
    }

    /**
     * get attempt number for this path and this user
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $value int progression
     * @return int
     */
    function setHigherAttemptNumber()
    {
    	// use max instead of count to handle suppressed attempts
    	$sql = "SELECT MAX(`attempt_number`)
    			FROM ".$this->tblAttempt."
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

/*
 CREATE TABLE IF NOT EXISTS `__CL_COURSE__lp_item_attempt` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `attempt_id` INT(11) NOT NULL,
  `item_id` INT(11) NOT NULL,
  `location` varchar(255) NOT NULL default '',
  `completion_status` enum('NOT ATTEMPTED','PASSED','FAILED','COMPLETED','BROWSED','INCOMPLETE','UNKNOWN') NOT NULL default 'NOT ATTEMPTED',
  `entry` enum('AB-INITIO','RESUME','') NOT NULL default 'AB-INITIO',
  `score_raw` tinyint(4) NOT NULL default '-1',
  `score_min` tinyint(4) NOT NULL default '-1',
  `score_max` tinyint(4) NOT NULL default '-1',
  `total_time` varchar(13) NOT NULL default '0000:00:00.00',
  `session_time` varchar(13) NOT NULL default '0000:00:00.00',
  `suspend_data` text NOT NULL,
  `credit` enum('CREDIT','NO-CREDIT') NOT NULL default 'NO-CREDIT',
  PRIMARY KEY(`id`)
) TYPE=MyISAM;
 */
class itemAttempt
{
    /**
     * @var $id id of item attempt, -1 if item attempt doesn't exist already
     */
    var $id;

    /**
     * @var $attemptId id of the attempt
     */
    var $attemptId;

    /**
     * @var $itemId id of the item
     */
    var $itemId;

    /**
     * @var $location SCORM location (position in the SCO)
     */
    var $location;

    /**
     * @var $completionStation SCORM completion_status (completion level of the SCO)
     */
    var $completionStation;

    /**
     * @var $entry SCORM entry (is the learner entered in the SCO)
     */
    var $entry;

	/**
	 * @var $scoreRaw SCORM score.raw (score of the learner)
	 */
	var $scoreRaw;

	/**
	 * @var $scoreMin SCORM score.min (minimum possible score)
	 */
	var $scoreMin;

	/**
	 * @var $scoreMax SCORM score.max (maximum possible score)
	 */
	var $scoreMax;

    /**
     * @var $totalTime SCORM total_time (sum of all session_time of this SCO)
     */
    var $totalTime;

	/**
	 * @var $sessionTime SCORM session_time (time spent by learner for this session in the SCO)
	 */
	var $sessionTime;

	/**
	 * @var $suspendData SCORM suspend_data (data the SCO would like to get back on next attempt)
	 */
	var $suspendData;

	/**
	 * @var $credit SCORM credit ( indicates whether the learner will be credited for performance)
	 */
	var $credit;

    /**
     * @var $tblItemAttempt name of the item table
     */
    var $tblItemAttempt;

    /**
     * Constructor
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
	function itemAttempt()
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
     * load item attempt
     *
     * @param $attemptId int id of attempt
     * @param $itemId int id of item
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */
	function load($attemptId, $itemId)
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
    			FROM ". $this->tblItemAttempt."
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
			$this->totalTime = $data['total_time']; // TODO correct format
			$this->sessionTime = $data['session_time']; // TODO correct format
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
     * save item attempt to DB
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return mixed false or id of the record
     */
	function save()
	{
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
                    	`total_time` = '".addslashes($this->totalTime)."',
                    	`session_time` = '".addslashes($this->sessionTime)."',
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
     * delete item attempt
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */
	function delete()
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
	 * check that all scorm data are consistent and that value are correct
	 */
	function validate()
	{
		return true;
	}

    /**
     * get id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int
     */
    function getId()
    {
        return (int) $this->id;
    }

    /**
     * get attempt id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int
     */
    function getAttemptId()
    {
        return (int) $this->attemptId;
    }

    /**
     * set attempt id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $value int id of the attempt
     */
    function setAttemptId($value)
    {
        $this->attemptId = (int) $value;
    }

    /**
     * get item id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int
     */
    function getItemId()
    {
        return (int) $this->itemId;
    }

    /**
     * set item id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $value int id of the item
     */
    function setItemId($value)
    {
        $this->itemId = (int) $value;
    }

	/**
	 * set location
	 *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $value location in the SCO
	 */
	 function setLocation($value)
	 {
	 	$this->location = trim($value);
	 }

    /**
     * get location
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string location in the SCO
     */
	 function getLocation()
	 {
	 	return $this->location;
	 }

	/**
	 * set completionStatus
	 *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $value completionStatus of the SCO
	 */
	 function setCompletionStatus($value)
	 {
	 	$acceptedValues = array('NOT ATTEMPTED','PASSED','FAILED','COMPLETED','BROWSED','INCOMPLETE','UNKNOWN');

        if( in_array($value, $acceptedValues) )
        {
            $this->completionStatus = $value;
            return true;
        }
        return false;
	 }

    /**
     * get completionStatus
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string completionStatus of the SCO
     */
	 function getCompletionStatus()
	 {
	 	return $this->completionStatus;
	 }

	/**
	 * set entry
	 *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $value entry of the SCO
	 */
	 function setEntry($value)
	 {
	 	$acceptedValues = array('AB-INITIO','RESUME','');

        if( in_array($value, $acceptedValues) )
        {
            $this->entry = $value;
            return true;
        }
        return false;
	 }

    /**
     * get entry
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string entry of the SCO
     */
	 function getEntry()
	 {
	 	return $this->entry;
	 }

    /**
     * get score raw
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int score raw
     */
    function getScoreRaw()
    {
        return (int) $this->scoreRaw;
    }

    /**
     * set score raw
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $value int score raw
     */
    function setScoreRaw($value)
    {
        $this->scoreRaw = (int) $value;
    }

    /**
     * get score min
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int score min
     */
    function getScoreMin()
    {
        return (int) $this->scoreMin;
    }

    /**
     * set score min
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $value int score min
     */
    function setScoreMin($value)
    {
        $this->scoreMin = (int) $value;
    }

    /**
     * get score max
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int score max
     */
    function getScoreMax()
    {
        return (int) $this->scoreMax;
    }

    /**
     * set score max
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $value int score max
     */
    function setScoreMax($value)
    {
        $this->scoreMax = (int) $value;
    }

    /**
     * get sessionTime
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int session time in SCO
     */
    function getSessionTime()
    {
        return $this->sessionTime;
    }

    /**
     * set sessionTime
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $value int sessionTime
     */
    function setSessionTime($value)
    {
        $this->sessionTime = (int) $value;
    }

    /**
     * get totalTime
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int total time in SCO
     */
    function getTotalTime()
    {
        return $this->totalTime;
    }

    /**
     * set totalTime
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $value int totalTime
     */
    function setTotalTime($value)
    {
        $this->totalTime = (int) $value;
    }


	/**
     * get suspend data
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string suspend data
     */
    function getSuspendData()
    {
        return $this->suspendData;
    }

    /**
     * set suspend data
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $value string suspend data
     */
    function setSuspendData($value)
    {
        $this->suspendData = $value;
    }

	/**
	 * set credit
	 *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $value credit of the SCO
	 */
	 function setCredit($value)
	 {
	 	$acceptedValues = array('CREDIT','NO-CREDIT');

        if( in_array($value, $acceptedValues) )
        {
            $this->credit = $value;
            return true;
        }
        return false;
	 }

    /**
     * get credit
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string credit of the SCO
     */
	 function getCredit()
	 {
	 	return $this->credit;
	 }





}

class itemAttemptList
{
	var $tblPath;
	var $tblItem;

    /**
     * Constructor
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
	function itemAttemptList()
    {
        // define module table names
        $tblNameList = array(
            'lp_item_attempt'
        );

        // convert to Claroline course table names
        $tbl_lp_names = get_module_course_tbl( $tblNameList, claro_get_current_course_id() );
        $this->tblItemAttempt = $tbl_lp_names['lp_item_attempt'];
    }

    function load($attemptId)
    {
    	$sql = "SELECT `id`,
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
    			FROM ". $this->tblItemAttempt."
    			WHERE `attempt_id` = ". (int) $attemptId ;

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