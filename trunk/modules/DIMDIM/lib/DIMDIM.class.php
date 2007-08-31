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
 * @author Sebastien Piraux
 *
 */

class conference 
{
    /**
     * @var $id id of conference, -1 if conference doesn't exist already
     */
    var $id;

    /**
     * @var $title name of the conference
     */
    var $title;

    /**
     * @var $description statement of the conference
     */
    var $description;

    /**
     * @var $visibility visibility of the conference (default is visible)
     */
    var $visibility;      
    
    /**
     * @var $waitingArea enable or disable the dimdim waiting area (default is disable)
     */
    var $waitingArea;

    /**
     * @var $maxUsers int
     */
    var $maxUsers; 
    
    /**
     * @var $duration int
     */
    var $duration; 
            
    /**
     * @var $type video and audio or audio only, default is audio
     */
    var $type;

    /**
     * @var $attendeeMikes 
     */
    var $attendeeMikes; 
    
    /**
     * @var $network network type
     */
    var $network;        

    /**
     * @var $startTime
     */
    var $startTime;
    
    /**
     * @var $confKey conference key
     */
    var $confKey;
    
    /**
     * @var $tblConference
     */
    var $tblConference;
    
    
    /**
     * Constructor
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */    
    function conference()
    {
        $this->id = (int) -1;
        $this->title = '';
        $this->description = '';
        $this->visibility = 'VISIBLE';
        $this->waitingArea = 'DISABLE';     
        $this->maxUsers = (int) 20;   
        $this->duration = (int) 1;
        $this->type = 'AUDIO';
        $this->attendeeMikes = (int) 0;        
        $this->network = 'DIALUP';
        $this->startTime = claro_time();
        
        // define module table names
        $tblNameList = array(
            'dim_conference'
        );

        // convert to Claroline course table names
        $tbl_lp_names = get_module_course_tbl( $tblNameList, claro_get_current_course_id() ); 
        $this->tblConference = $tbl_lp_names['dim_conference'];
    }
    
    /**
     * load a conference data from DB
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param integer $id id of conference
     * @return boolean load successfull ?
     */    
    function load($id)
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
                    `startTime`,
                    `confKey`
            FROM `".$this->tblConference."`
            WHERE `id` = ".(int) $id;

        $data = claro_sql_query_get_single_row($sql);

        if( !empty($data) )
        {
            // from query
            $this->id = (int) $data['id'];
            $this->title = $data['title'];
            $this->description = $data['description'];
            $this->visibility = $data['visibility'];      
            $this->waitingArea = $data['waitingArea'];
            $this->maxUsers = (int) $data['maxUsers'];
            $this->duration = (int) $data['duration'];
            $this->type = $data['type'];
            $this->attendeeMikes = (int) $data['attendeeMikes'];
            $this->network = $data['network'];
            $this->startTime = $data['startTime'];
            $this->confKey = $data['confKey'];

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
    function save()
    {           
        if( $this->id == -1 )
        {
            $this->confKey = $this->generateConferenceKey();        
        
            // insert
            $sql = "INSERT INTO `".$this->tblConference."`
                    SET `title` = '".addslashes($this->title)."',
                        `description` = '".addslashes($this->description)."',
                        `visibility` = '".addslashes($this->visibility)."',
                        `waitingArea` = '".addslashes($this->waitingArea)."',
                        `maxUsers` = '".(int) $this->maxUsers."',
                        `duration` = '".(int) $this->duration."',
                        `type` = '".addslashes($this->type)."',
                        `attendeeMikes` = ".(int) $this->attendeeMikes.",
                        `network` = '".addslashes($this->network)."',
                        `startTime` = FROM_UNIXTIME('".$this->startTime."'),
                        `confKey` = '".addslashes($this->type)."'";

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
            $sql = "UPDATE `".$this->tblConference."`
                    SET `title` = '".addslashes($this->title)."',
                        `description` = '".addslashes($this->description)."',
                        `visibility` = '".addslashes($this->visibility)."',
                        `waitingArea` = '".addslashes($this->waitingArea)."',
                        `maxUsers` = '".(int) $this->maxUsers."',
                        `duration` = '".(int)$this->duration."',
                        `type` = ".addslashes($this->type).",
                        `attendeeMikes` = ".(int) $this->attendeeMikes.",
                        `network` = '".addslashes($this->network)."',
                        `startTime` = FROM_UNIXTIME('".$this->startTime."')
                    WHERE `id` = '".$this->id."'";

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
     * delete conference
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */    
    function delete()
    {
        if( $this->id == -1 ) return true;

        // delete the conference
        $sql = "DELETE FROM `" . $this->tblConference . "`
                WHERE `id` = " . (int) $this->id ;

        if( claro_sql_query($sql) == false ) return false;
        
        $this->id = -1;
        return true;
    }    
    
    /**
     * check if data are valide
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */
    function validate()
    {
        // title is a mandatory element
        $title = strip_tags($this->title);

        if( empty($title) )
        {
            claro_failure::set_failure('conference_no_title');
            return false;
        }

        // time must be now or in the future but decount duration 
        if( $this->startTime < ( time() - $this->duration ) )
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
    function generateConferenceKey()
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
    function buildUrl()
    {
        $url = get_conf('dimdim_server_url') 
        
        if( trim(get_conf('dimdim_server_port')) != '' )
        {
            $url .= ':' . get_conf('dimdim_server_port');
        }
        
        
        if( claro_is_allowed_to_edit() )
        {
        	$url .= '/dimdim/html/signin/signin.action?action=host'
        	     . '&amp;email='. urlencode(claro_get_current_user_data('mail'))
        	     . '&amp;confKey='. urlencode($this->getConfkey())
        	     . '&amp;displayName='. urlencode(claro_get_current_user_data('firstName') . ' ' . claro_get_current_user_data('lastName'))
        	     . '&amp;confName='. urlencode($this->getTitle())
        	     . '&amp;lobby='. urlencode($this->getWaitingArea(true))
        	     . '&amp;networkProfile='. urlencode($this->getNetwork(true))
        	     . '&amp;meetingHours='. urlencode($this->getDuration())
        	     . '&amp;meetingMinutes='. 0
        	     . '&amp;maxParticipants='. urlencode($this->getMaxUser())
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
        	     . '&amp;displayName='. urlencode(claro_get_current_user_data('firstName') . ' ' . claro_get_current_user_data('lastName'))
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
    function getId()
    {
        return (int) $this->id;
    }

    /**
     * get title
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string
     */
    function getTitle()
    {
        return $this->title;
    }

    /**
     * set title
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value
     */
    function setTitle($value)
    {
        $this->title = trim($value);
    }

    /**
     * get description
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string
     */
    function getDescription()
    {
        return $this->description;
    }

    /**
     * set description
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value
     */
    function setDescription($value)
    {
        $this->description = trim($value);
    }

    /**
     * set visible
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    function setVisible()
    {
        $this->visibility = 'VISIBLE';
    } 

    /**
     * set invisible
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    function setInvisible()
    {
        $this->visibility = 'INVISIBLE';
    } 
    
    /**
     * is the conference visible
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */    
    function isVisible()
    {
        if( $this->visibility == 'VISIBLE' )    return true;
        else                                    return false;
    }

    /**
     * is the conference invisible
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */    
    function isInvisible()
    {
        return !$this->isVisible();
    }    
    
    /**
     * get waitingArea
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string
     */
    function getWaitingArea($url = false)
    {
        if( !$url )
        {
            return $this->waitingArea;
        }
        else
        {
            return ($this->waitingArea == 'ENABLE' ) ? true: false;
        }
    }

    /**
     * set waitingArea
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value
     */
    function setWaitingArea($value)
    {
        $this->waitingArea = trim($value);
    }  
    
    /**
     * set maxUsers
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    function setMaxUsers($value)
    {
        $acceptedValues = array('20', '40', '60', '80', '100', '200', '300', '400', '500');

        if( in_array($value, $acceptedValues) )
        {
            $this->maxUsers = (int) $value;
            return true;
        }
        return false;
    } 

    /**
     * get maxUsers
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    function getMaxUsers()
    {
        return (int) $this->maxUsers;
    } 
    
    /**
     * get duration
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return integer
     */
    function getDuration()
    {
        return (int) $this->duration;
    }

    /**
     * set duration
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param integer value
     */
    function getDuration($value)
    {
        $this->duration = (int) $value;
    }  

    /**
     * get type
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string
     */
    function getType($url = false)
    {
        if( !$url )
        {
            return $this->type;
        }
        else
        {
            return ($this->type == 'AUDIO' ) ? 'audio' : 'av';
        }
    }

    /**
     * set type
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value
     */
    function setType($value)
    {
        $this->type = trim($value);
    } 

    /**
     * get attendeeMikes
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    function getAttendeeMikes()
    {
        return (int) $this->attendeeMikes;
    } 
    
    /**
     * set attendeeMikes
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    function setAttendeeMikes($value)
    {
        if( 0 <= $value && $value <= 5  )
        {
            $this->attendeeMikes = (int) $value;
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
    function getNetwork($url = false)
    {
        if( !$url )
        {
            return $this->network;
        }
        else
        {
            if( $this->network == 'DIALUP' ) return 1;
            elseif( $this->network == 'CABLEDSL' ) return 2;
            else return 3;
        }
    }

    /**
     * set network
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value
     */
    function setNetwork($value)
    {
        $this->network = trim($value);
    }     

    /**
     * get startTime
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return integer
     */
    function getStartTime()
    {
        return (int) $this->startTime;
    }

    /**
     * set startTime
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param integer value
     */
    function setStartTime($value)
    {
        $this->startTime = (int) $value;
    }  

    /**
     * get confKey
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string
     */
    function getConfKey()
    {
        return $this->confKey;
    }

    /**
     * set confKey
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value
     */
    function setConfKey($value)
    {
        $this->confKey = trim($value);
    }

}

/**
 * conference list is an class used to get a list of all course conferences
 *
 * @author Sebastien Piraux <pir@cerdecam.be>
 * @return boolean
 */    
class conferenceList
{
    /**
     * @var $tblConference name of the path table
     */
    var $tblConference;
    
        
    /**
     * Constructor
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */ 	
    function pathList()
    {
        // define module table names
        $tblNameList = array(
            'dim_conference'
        );

        // convert to Claroline course table names
        $tbl_lp_names = get_module_course_tbl( $tblNameList, claro_get_current_course_id() ); 
        $this->tblConference = $tbl_lp_names['dim_conference'];
    }
    
	/**
     * Load the correct list depending on parameter
     *
     * @param userId integer id of the user we need to display the path progression, can be ommitted default is null
     * @return array 2d array containing list of all available learning paths
     * @author Sebastien Piraux <pir@cerdecam.be>
     */ 	
    function load( $userId = null )
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
                    `startTime`
            FROM `".$this->tblConference."`
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
