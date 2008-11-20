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
 * @package DIMDIM
 *
 * @author Sebastien Piraux - Sokay Benjamin
 *
 */

class Conference
{
    /**
     * @private variable $id id of conference, -1 if conference doesn't exist already
     */
    private $_id;

    /**
     * @private variable $title name of the conference
     */
    private $_title;

    /**
     *@ private variable $description statement of the conference
     */
    private $_description;

    /**
     * @private variable $visibility visibility of the conference (default is visible)
     */
    private $_visibility;

    /**
     * @private variable $waitingArea enable or disable the dimdim waiting area (default is disable)
     */
    private $_waitingArea;

    /**
     * @private variable $maxUsers int
     */
    private $_maxUsers;

    /**
     *@ private variable $duration int
     */
    private $_duration;

    /**
     * @private variable $type video and audio or audio only, default is audio
     */
    private $_type;
	
    /**
     * @private variable $attendeeMikes
     */
    private $_attendeeMikes;

    /**
     * @private variable $network network type
     */
    private $_network;

    /**
     * @private variable $startTime
     */
    private $_startTime;

    /**
     * @private variable $confKey conference key
     */
    private $_confKey;

    /**
     * @private variable $tblConference
     */
    private $_tblConference;

	
    /**
     * Constructor
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function __construct()
    {
        $this->_id = (int) -1;
        $this->_title = '';
        $this->_description = '';
        $this->_visibility = 'VISIBLE';
        $this->_waitingArea = 'DISABLE';
        $this->_maxUsers = (int) 20;
        $this->_duration = (int) 1;
        $this->_type = 'AUDIO';
        $this->_attendeeMikes = (int) 0;
        $this->_network = 'DIALUP';
        $this->_startTime = claro_time()+3600;
        
		$tbl_names = get_module_course_tbl( array( 'dim_conference'),  claro_get_current_course_id() );
        $this->_tblConference = $tbl_names['dim_conference'];
    }

    /**
     * load a conference data from DB
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param integer $id id of conference
     * @return boolean load successfull ?
     */
    public function load($id)
    {
       $sql = "SELECT
                    `id`,
                    `title`,
                    `description`,
                    `visibility`,
                    `waitingArea`,
                    `maxUsers`,
                    `duration`,
                    `type`,
                    `attendeeMikes`,
                    `network`,
                    UNIX_TIMESTAMP(`startTime`) AS `startTime`,
                    `confKey`
            FROM `".$this->_tblConference."`
            WHERE `id` = ".(int) $id;

        $data = claro_sql_query_get_single_row($sql);

        if( !empty($data) )
        {
            // from query
            $this->_id = (int) $data['id'];
            $this->_title = $data['title'];
            $this->_description = $data['description'];
            $this->_visibility = $data['visibility'];
            $this->_waitingArea = $data['waitingArea'];
            $this->_maxUsers = (int) $data['maxUsers'];
            $this->_duration = (int) $data['duration'];
            $this->_type = $data['type'];
            $this->_attendeeMikes = (int) $data['attendeeMikes'];
            $this->_network = $data['network'];
            $this->_startTime = $data['startTime'];
            $this->_confKey = $data['confKey'];

            return true;
        }
        else
        {
            return false;
        }
    }

        /**
     * save path to DB
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return mixed false or id of the record
     */
    public function save()
    {
        if( $this->_id == -1 )
        {
            $this->_confKey = $this->generateConferenceKey();

            // insert
            $sql = "INSERT INTO `".$this->_tblConference."`
                    SET `title` = '".addslashes($this->_title)."',
                        `description` = '".addslashes($this->_description)."',
                        `visibility` = '".addslashes($this->_visibility)."',
                        `waitingArea` = '".addslashes($this->_waitingArea)."',
                        `maxUsers` = '".(int) $this->_maxUsers."',
                        `duration` = '".(int) $this->_duration."',
                        `type` = '".addslashes($this->_type)."',
                        `attendeeMikes` = ".(int) $this->_attendeeMikes.",
                        `network` = '".addslashes($this->_network)."',
                        `startTime` = FROM_UNIXTIME('".$this->_startTime."'),
                        `confKey` = '".addslashes($this->_confKey)."'";

            // execute the creation query and get id of inserted assignment
            $insertedId = claro_sql_query_insert_id($sql);

            if( $insertedId )
            {
                $this->_id = (int) $insertedId;

                return $this->_id;
            }
            else
            {
                return false;
            }
        }
        else
        {
            // update, main query
            $sql = "UPDATE `".$this->_tblConference."`
                    SET `title` = '".addslashes($this->_title)."',
                        `description` = '".addslashes($this->_description)."',
                        `visibility` = '".addslashes($this->_visibility)."',
                        `waitingArea` = '".addslashes($this->_waitingArea)."',
                        `maxUsers` = '".(int) $this->_maxUsers."',
                        `duration` = '".(int)$this->_duration."',
                        `type` = '".addslashes($this->_type)."',
                        `attendeeMikes` = ".(int) $this->_attendeeMikes.",
                        `network` = '".addslashes($this->_network)."',
                        `startTime` = FROM_UNIXTIME('".$this->_startTime."')
                    WHERE `id` = '".$this->_id."'";

            // execute and return main query
            if( claro_sql_query($sql) )
            {
                return $this->_id;
            }
            else
            {
                return false;
            }
        }
    }

    /**
     * delete conference
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */
    public function delete()
    {
        if( $this->_id == -1 ) return true;

        // delete the conference
        $sql = "DELETE FROM `" . $this->_tblConference . "`
                WHERE `id` = " . (int) $this->_id ;

        if( claro_sql_query($sql) == false ) return false;

        $this->_id = -1;
        return true;
    }

    /**
     * check if data are valide
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */
    public function validate()
    {
        // title is a mandatory element
        $title = strip_tags($this->_title);

        if( empty($title) )
        {
            claro_failure::set_failure('conference_no_title');
            return false;
        }

        // time must be now or in the future but decount duration
        if( $this->_startTime < ( time() - $this->_duration*3600 ) )
        {
            claro_failure::set_failure('conference_invalid_date');
            return false;
        }

        return true; // no errors, form is valide
    }

    /**
     * Generate a conference key
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */
    public function generateConferenceKey()
    {
        $confKey = '';

    	// Random number generator to append for conference key

    	srand((double)microtime()*1000000);
	    $vowels = array ("a", "e", "i", "o", "u");
	    $cons = array ("b", "c", "d", "g", "h", "j", "k",
	                   "l", "m", "n", "p", "r", "s", "t",
	                   "u", "v", "w", "tr", "cr", "br", "fr",
	                   "th", "dr", "ch", "ph", "wr", "st", "sp",
	                   "sw", "pr", "sl", "cl");

	    $num_vowels = count($vowels);
	    $num_cons = count($cons);

	    for($i = 0; $i < 5; $i++)
	    {
	    	$confKey .= $cons[rand(0, $num_cons - 1)] . $vowels[rand(0, $num_vowels - 1)];
	    }

        return $confKey;
    }

    /**
     * Build url
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */
    public function buildUrl($confAdmin = false)
    {
        $url = get_conf('dimdim_server_url');

        if( trim(get_conf('dimdim_server_port')) != '' )
        {
            $url .= ':' . get_conf('dimdim_server_port');
        }


        if( $confAdmin )
        {
        	$url .= '/dimdim/html/signin/signin.action?action=host'
        	     . '&amp;email='. urlencode(claro_get_current_user_data('mail'))
        	     . '&amp;confKey='. urlencode($this->getConfkey())
        	     . '&amp;displayName='. urlencode(htmlentities(claro_get_current_user_data('firstName') . ' ' . claro_get_current_user_data('lastName')))
        	     . '&amp;confName='. urlencode($this->getTitle())
        	     . '&amp;lobby='. urlencode($this->getWaitingArea(true))
        	     . '&amp;networkProfile='. urlencode($this->getNetwork(true))
        	     . '&amp;meetingHours='. urlencode($this->getDuration())
        	     . '&amp;meetingMinutes='. 0
        	     . '&amp;maxParticipants='. urlencode($this->getMaxUsers())
        	     . '&amp;presenterAV='. urlencode($this->getType(true))
        	     . '&amp;attendees='. urlencode(' ')
        	     . '&amp;maxAttendeeMikes='. urlencode($this->getAttendeeMikes())
        	     . '&amp;returnUrl='. urlencode(get_conf('rootWeb'))
        	     . '&amp;submitFormOnLoad=true';
        }
        else
        {
        	$url .= '/dimdim/html/signin/signin.action?action=join'
        	     . '&amp;email='. urlencode(claro_get_current_user_data('mail'))
        	     . '&amp;confKey='. urlencode($this->getConfkey())
        	     . '&amp;displayName='. urlencode(htmlentities(claro_get_current_user_data('firstName') . ' ' . claro_get_current_user_data('lastName')))
        	     . '&amp;returnUrl='. urlencode(get_conf('rootWeb'))
        	     . '&amp;submitFormOnLoad=true';
        }

        return $url;
    }

    //-- Getter & Setter

    /**
     * get id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int
     */
    public function getId()
    {
        return (int) $this->_id;
    }

    /**
     * get title
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * set title
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value
     */
    public function setTitle($value)
    {
        $this->_title = trim($value);
    }

    /**
     * get description
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * set description
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value
     */
    public function setDescription($value)
    {
        $this->_description = trim($value);
    }

    /**
     * set visible
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function setVisible()
    {
        $this->_visibility = 'VISIBLE';
    }

    /**
     * set invisible
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function setInvisible()
    {
        $this->_visibility = 'INVISIBLE';
    }

    /**
     * is the conference visible
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */
    public function isVisible()
    {
        if( $this->_visibility == 'VISIBLE' ) 
		{
			return true;
		}
        else
		{
			return false;
		}	
    }

    /**
     * is the conference invisible
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */
    public function isInvisible()
    {
        return !$this->isVisible();
    }

    /**
     * get waitingArea
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string
     */
    public function getWaitingArea($url = false)
    {
        if( !$url )
        {
            return $this->_waitingArea;
        }
        else
        {
            return ($this->_waitingArea == 'ENABLE' ) ? true: false;
        }
    }

    /**
     * set waitingArea
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value
     */
    public function setWaitingArea($value)
    {
        $this->_waitingArea = trim($value);
    }

    /**
     * set maxUsers
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function setMaxUsers($value)
    {
        $acceptedValues = array('20', '40', '60', '80', '100', '200', '300', '400', '500');

        if( in_array($value, $acceptedValues) )
        {
            $this->_maxUsers = (int) $value;
            return true;
        }
        return false;
    }

    /**
     * get maxUsers
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function getMaxUsers()
    {
        return (int) $this->_maxUsers;
    }

    /**
     * get duration
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return integer
     */
    public function getDuration()
    {
        return (int) $this->_duration;
    }

    /**
     * set duration
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param integer value
     */
    public function setDuration($value)
    {
        $this->_duration = (int) $value;
    }

    /**
     * get type
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string
     */
    public function getType($url = false)
    {
        if( !$url )
        {
            return $this->_type;
        }
        else
        {
            return ($this->_type == 'AUDIO' ) ? 'audio' : 'av';
        }
    }

    /**
     * set type
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value
     */
    public function setType($value)
    {
        $this->_type = trim($value);
    }

    /**
     * get attendeeMikes
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function getAttendeeMikes()
    {
        return (int) $this->_attendeeMikes;
    }

    /**
     * set attendeeMikes
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function setAttendeeMikes($value)
    {
        if( 0 <= $value && $value <= 5  )
        {
            $this->_attendeeMikes = (int) $value;
            return true;
        }
        return false;
    }

    /**
     * get network
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string
     */
    public function getNetwork($url = false)
    {
        if( !$url )
        {
            return $this->_network;
        }
        else
        {
            if( $this->_network == 'DIALUP' ) return 1;
            elseif( $this->_network == 'CABLEDSL' ) return 2;
            else return 3;
        }
    }

    /**
     * set network
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value
     */
    public function setNetwork($value)
    {
        $this->_network = trim($value);
    }

    /**
     * get startTime
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return integer
     */
    public function getStartTime()
    {
        return (int) $this->_startTime;
    }

    /**
     * set startTime
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param integer value
     */
    public function setStartTime($value)
    {
        $this->_startTime = (int) $value;
    }

    /**
     * get confKey
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string
     */
    public function getConfKey()
    {
        return $this->_confKey;
    }

    /**
     * set confKey
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value
     */
    public function setConfKey($value)
    {
        $this->_confKey = trim($value);
    }

}

/**
 * conference list is an class used to get a list of all course conferences
 *
 * @author Sebastien Piraux <pir@cerdecam.be>
 * @return boolean
 */
class ConferenceList
{
    /**
     * @private variable $tblConference name of the path table
     */
    private $_tblConference;

    /**
     * Constructor
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function __construct()
    {
        $tbl_names = get_module_course_tbl( array( 'dim_conference'),  claro_get_current_course_id() );
        $this->_tblConference = $tbl_names['dim_conference'];
    }

	/**
     * Load the correct list depending on parameter
     *
     * @param userId integer id of the user we need to display the path progression, can be ommitted default is null
     * @return array 2d array containing list of all available learning paths
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function load( $userId = null )
    {
        $sql = "SELECT
                    `id`,
                    `title`,
                    `description`,
                    `visibility`,
                    `waitingArea`,
                    `maxUsers`,
                    `duration`,
                    `type`,
                    `attendeeMikes`,
                    `network`,
                    UNIX_TIMESTAMP(`startTime`) AS `startTime`
            FROM `". $this->_tblConference ."`
            ORDER BY `startTime` ASC";

        if ( false === ( $data = claro_sql_query_fetch_all_rows($sql) ) )
        {
            return false;
        }
        else
        {
            return $data;
        }
    }
}
?>
