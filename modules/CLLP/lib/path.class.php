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

class path
{
    /**
     * @var $id id of path, -1 if path doesn't exist already
     */
    var $id;

    /**
     * @var $title name of the path
     */
    var $title;

    /**
     * @var $description statement of the path
     */
    var $description;
        
    /**
     * @var $visibility visibility of the path (default is invisible)
     */
    var $visibility;      
    
    /**
     * @var $rank order of the path in the path list
     */
    var $rank;
    
    /**
     * @var $type
     */
    var $type;

    /**
     * @var $lock 
     */
    var $lock; 
    
    /**
     * @var $identifier SCORM manifest ressource identifier
     */
    var $identifier;        

    /**
     * @var $allowReinit allow to start path items again (default is false)
     */
    var $allowReinit;            

    /**
     * @var $viewMode embedded or in full screen (default is embedded)
     */
    var $viewMode;        
    
    /**
     * @var $encoding encoding of the path (default is utf-8)
     */
    var $encoding;           


    /**
     * @var $tblPath
     */
    var $tblPath;
    
        
    /**
     * Constructor
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */    
    function path()
    {
        $this->id = (int) -1;
        $this->title = '';
        $this->description = '';
        $this->visibility = 'INVISIBLE';
        $this->rank = 0;        
        $this->type = 'CLAROLINE'; // SCORM
        $this->lock = 'OPEN';        
        $this->identifier = '';
        $this->allowReinit = false;
        $this->viewMode = 'EMBEDDED'; // or 'FULLSCREEN'
        $this->encoding = 'UTF-8'; // or 'ISO-8859-1', ...
        
        // define module table names
        $tblNameList = array(
            'lp_path',
            'lp_item',
            'lp_attempt',
            'lp_item_attempt'
        );

        // convert to Claroline course table names
        $tbl_lp_names = get_module_course_tbl( $tblNameList, claro_get_current_course_id() ); 
        $this->tblPath = $tbl_lp_names['lp_path'];
    }

    /**
     * load a path from DB
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param integer $id id of path
     * @return boolean load successfull ?
     */    
    function load($id)
    {
        $sql = "SELECT
                    `id`,
                    `title`,
                    `description`,
                    `visibility`,
                    `rank`,                    
                    `type`,
                    `lock`,
                    `identifier`,
                    `allow_reinit`,
                    `view_mode`,
                    `encoding`
            FROM `".$this->tblPath."`
            WHERE `id` = ".(int) $id;

        $data = claro_sql_query_get_single_row($sql);

        if( !empty($data) )
        {
            // from query
            $this->id = (int) $data['id'];
            $this->title = $data['title'];
            $this->description = $data['description'];
            $this->visibility = $data['visibility'];      
            $this->rank = (int) $data['rank'];
            $this->lock = $data['lock'];
            $this->identifier = $data['identifier'];
            $this->allowReinit = $data['allow_reinit'];
            $this->viewMode = $data['view_mode'];
            $this->encoding = $data['encoding'];

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
            // set correct value for rank on creation
            $this->rank = $this->getHigherRank() + 1 ;
            
            // insert
            $sql = "INSERT INTO `".$this->tblPath."`
                    SET `title` = '".addslashes($this->title)."',
                        `description` = '".addslashes($this->description)."',
                        `visibility` = '".addslashes($this->visibility)."',
                        `rank` = '".(int) $this->rank."',
                        `lock` = '".addslashes($this->lock)."',
                        `identifier` = '".addslashes($this->identifier)."',
                        `allow_reinit` = ".(int) $this->allowReinit.",
                        `view_mode` = '".addslashes($this->viewMode)."',
                        `encoding` = '".addslashes($this->encoding)."'";

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
            $sql = "UPDATE `".$this->tblPath."`
                    SET `title` = '".addslashes($this->title)."',
                        `description` = '".addslashes($this->description)."',
  						`visibility` = '".addslashes($this->visibility)."',
                        `rank` = '".(int) $this->rank."',
                        `lock` = '".addslashes($this->lock)."',
                        `identifier` = '".addslashes($this->identifier)."',
                        `allow_reinit` = ".(int) $this->allowReinit.",
                        `view_mode` = '".addslashes($this->viewMode)."',
                        `encoding` = '".addslashes($this->encoding)."'
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
     * delete path
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */    
    function delete()
    {
        if( $this->id == -1 ) return true;
        
        // delete all items related to this path
        $itemList = new itemList();
        $thisPathItemList = $itemList->load($this->id);
        
        if( !empty($thisPathItemList) )
        {
            foreach( $thisPathItemList as $item )
            { 
                $itemObj = new item();
                $itemObj->load($item['id']);
                
                $itemObj->delete();
            }
        
        }
        
        // delete the path
        $sql = "DELETE FROM `" . $this->tblPath . "`
                WHERE `id` = " . (int) $this->id ;

        if( claro_sql_query($sql) == false ) return false;
        
        // delete path repository
        claro_delete_file(get_path('coursesRepositorySys') . claro_get_course_path() . '/scormPackages/path_' . $this->id );
        
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
            claro_failure::set_failure('path_no_title');
            return false;
        }

        return true; // no errors, form is valide
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
     * is the path visible
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
     * is the path invisible
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */    
    function isInvisible()
    {
        return !$this->isVisible();
    }
            
    /**
     * get rank
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string
     */
    function getRank()
    {
        return (int) $this->rank;
    }

    /**
     * set rank
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value
     */
    function setRank($value)
    {
        $this->rank = trim($value);
    }    
    
    /**
     * get lock
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string
     */
    function getLock()
    {
        return (int) $this->lock;
    }

    /**
     * set lock
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value
     */
    function setLock($value)
    {
        $this->lock = trim($value);
    } 
    /**
     * set lock
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    function lock()
    {
        $this->lock = 'CLOSE';
    } 

    /**
     * set unlock
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    function unlock()
    {
        $this->lock = 'OPEN';
    } 
    
    /**
     * is the path locked ?
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */    
    function isLocked()
    {
        if( $this->lock == 'CLOSE' )    return true;
        else                            return false;
    }

    /**
     * is the path unlocked ?
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */    
    function isUnlocked()
    {
        return !$this->isLocked();
    }
    
    /**
     * set viewMode
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    function setViewMode($value)
    {
        $acceptedValues = array('FULLSCREEN', 'EMBEDDED');

        if( in_array($value, $acceptedValues) )
        {
            $this->viewMode = $value;
            return true;
        }
        return false;
    } 

    /**
     * get viewMode
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    function getViewMode()
    {
        return $this->viewMode;
    } 
    
    /**
     * show the path fullscreen ?
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */    
    function isFullscreen()
    {
        if( $this->viewMode == 'FULLSCREEN' )    return true;
        else                                     return false; // EMBEDDED
    }
    
    /**
     * get the higher rank of available learning path
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int higher path rank
     */
    function getHigherRank()
    {
        $sql = "SELECT max(`rank`)
                FROM `" . $this->tblPath . "`";

        $rankMax = claro_sql_query_get_single_value($sql);

        if( !is_null($rankMax) ) return (int) $rankMax;
        else                     return 0;
    }
}

/**
 * path list is an class used to get a list of learning path.
 *
 * @author Sebastien Piraux <pir@cerdecam.be>
 * @return boolean
 */    
class pathList
{
    /**
     * @var $tblPath name of the path table
     */
    var $tblPath;
    
        
    /**
     * Constructor
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */ 	
    function pathList()
    {
        $tblNameList = array(
            'lp_path'
        );

        // convert to Claroline course table names
        $tbl_lp_names = get_module_course_tbl( $tblNameList, claro_get_current_course_id() ); 
        $this->tblPath = $tbl_lp_names['lp_path'];
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
        if( !is_null($userId) )
        {
            return $this->loadUserProgress($userId);
        }
        else
        {
            return $this->loadAll();
        }
    }
    
    /**
     * load list of all learning paths
     *
     * @return array 2d array containing list of all available learning paths
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    function loadAll()
    {
        $sql = "SELECT
                    `id`,
                    `title`,
                    `description`,
                    `visibility`,
                    `rank`,                    
                    `type`,
                    `lock`,
                    `identifier`,
                    `allow_reinit`,
                    `view_mode`,
                    `encoding`
            FROM `".$this->tblPath."`
            ORDER BY `rank`";
            
        if ( false === ( $data = claro_sql_query_fetch_all_rows($sql) ) )
        {
            return false;
        }
        else
        { 
            return $data;
        }          
    }
    
    /**
     * load list of learning path progression related to $userId
     *
     * @param userId integer id of the user we need to display the path progression
     * @return array 2d array containing list of visible learning paths and progression of userId in it
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    function loadUserProgress( $userId )
    {
        $sql = "SELECT 
                    `id`,
                    `title`,
                    `description`
            FROM `".$this->tblPath."`
            WHERE `visibility` = 'VISIBLE'";
//            AND 'userId' = " . (int) $userId; 
                    
        if ( false === ( $data = claro_sql_query_fetch_all_rows($sql) ) )
        {
            return false;
        }
        else
        { 
            return $data;
        } 
    }
    
    /**
     * move path one position up in the list if possible (rank becomes lower than before)
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $path object path to move up
     * @return boolean result of operation
     */
    function movePathUp($path)
    {
        // get path list
        $list = $this->load();
        
        // find where is the path is the list to get the id of the previous one
        $i = 0;
        while( $i < count($list) )
        {
            if( $list[$i]['id'] == $path->getId() )
            {
                break;
            }
            $i++;
        }
        
        // if the path is the first of the list
        if( $i == 0 )
        {
            return false;
        }
        
        $currentRank = $path->getRank();
        $otherPathId = $list[$i-1]['id'];
        
        
        // get the path that is at the new position
        $otherPath = new path();
        $otherPath->load($otherPathId);

        // invert ranks
        $newRank = $otherPath->getRank();
         
        $otherPath->setRank($currentRank);
        $path->setRank($newRank);
    
        // save the two paths
        if( $path->validate() && $otherPath->validate() )
        {
            $path->save();
            $otherPath->save();
            
            return true;
        }
        else
        {
            return false;
        }
    }
    
    /**
     * move path one position down in the list if possible
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $path object path to move down
     * @return boolean result of operation
     */
    function movePathDown($path)
    {
        // get path list
        $list = $this->load();
        
        // find where is the path is the list to get the id of the next one
        $i = 0;
        while( $i < count($list) )
        {
            if( $list[$i]['id'] == $path->getId() )
            {
                break;
            }
            $i++;
        }
        
        // if the path is the first of the list
        if( $i == count($list) - 1 )
        {
            return false;
        }
        
        $currentRank = $path->getRank();
        $otherPathId = $list[$i+1]['id'];
        
        
        // get the path that is at the new position
        $otherPath = new path();
        $otherPath->load($otherPathId);

        // invert ranks
        $newRank = $otherPath->getRank();
         
        $otherPath->setRank($currentRank);
        $path->setRank($newRank);
    
        // save the two paths
        if( $path->validate() && $otherPath->validate() )
        {
            $path->save();
            $otherPath->save();
            
            return true;
        }
        else
        {
            return false;
        }
    }
}

?>
