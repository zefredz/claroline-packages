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

        if( !empty($data) )
        {
            // from query
            $this->id = (int) $data['id'];
            $this->pathId = (int) $data['path_id'];
            $this->userId = $data['user_id'];
            $this->lastItemId = $data['last_item_id'];
            $this->progress = $data['progress'];
            $this->attemptNumber = $data['attempt_number'];

			// load list of related item attempts
			$this->itemAttemptList = new itemAttemptList();
			$this->itemAttemptList->load($this->id);

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

		if( !is_null($this->itemAttemptList) )
		{
			$this->itemAttemptList->delete();
		}

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

    function setHighetAttemptNumber()
    {
    	$sql = "SELECT COUNT(`attemptNumber`) " .
    			"FROM ".$this->tblAttempt."" .
    			WHERE `path_id`"
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
class item_attempt
{
    /**
     * Constructor
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
	function item_attempt()
	{

	}
}