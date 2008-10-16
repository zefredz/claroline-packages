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
    private $id;

    /**
     * @var $title name of the path
     */
    private $title;

    /**
     * @var $description statement of the path
     */
    private $description;

    /**
     * @var $visibility visibility of the path (default is invisible)
     */
    private $visibility;

    /**
     * @var $rank order of the path in the path list
     */
    private $rank;

    /**
     * @var $version
     */
    private $version;

    /**
     * @var $lock
     */
    private $lock;

    /**
     * @var $identifier SCORM manifest ressource identifier
     */
    private $identifier;

    /**
     * @var $allowReinit allow to start path items again (default is false)
     */
    private $allowReinit;

    /**
     * @var $viewMode embedded or in full screen (default is embedded)
     */
    private $viewMode;

    /**
     * @var $encoding encoding of the path (default is utf-8)
     */
    private $encoding;

    /**
     * @var $tblPath
     */
    private $tblPath;

    const VERSION_12 = 'scorm12';
    const VERSION_13 = 'scorm13';

    /**
     * Constructor
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function __construct()
    {
        $this->id = (int) -1;
        $this->title = '';
        $this->description = '';
        $this->visibility = 'INVISIBLE';
        $this->rank = 0;
        $this->version = self::VERSION_13;
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
    public function load($id)
    {
        $sql = "SELECT
                    `id`,
                    `title`,
                    `description`,
                    `visibility`,
                    `rank`,
                    `version`,
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
            $this->version = $data['version'];
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
    public function save()
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
                        `version` = '".addslashes($this->version)."',
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
                        `version` = '".addslashes($this->version)."',
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
    public function delete()
    {
        if( $this->id == -1 ) return true;

        // delete all items related to this path
        $itemList = new PathItemList($this->id);
        $thisPathItemList = $itemList->load();

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
    public function validate()
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
    public function getId()
    {
        return (int) $this->id;
    }

    /**
     * get title
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * set title
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value
     */
    public function setTitle($value)
    {
        $this->title = trim($value);
    }

    /**
     * get description
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * set description
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value
     */
    public function setDescription($value)
    {
        $this->description = trim($value);
    }

    /**
     * set visible
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function setVisible()
    {
        $this->visibility = 'VISIBLE';
    }

    /**
     * set invisible
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function setInvisible()
    {
        $this->visibility = 'INVISIBLE';
    }

    /**
     * is the path visible
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */
    public function isVisible()
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
    public function isInvisible()
    {
        return !$this->isVisible();
    }

    /**
     * get rank
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string
     */
    public function getRank()
    {
        return (int) $this->rank;
    }

    /**
     * set rank
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value
     */
    public function setRank($value)
    {
        $this->rank = trim($value);
    }

    /**
     * get version
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * set version
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value
     */
    public function setVersion($value)
    {
        $acceptedValues = array(self::VERSION_12, self::VERSION_13);

        if( in_array($value, $acceptedValues) )
        {
            $this->version = $value;
            return true;
        }
        return false;
    }

    /**
     * get lock
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string
     */
    public function getLock()
    {
        return $this->lock;
    }

    /**
     * set lock
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value
     */
    public function setLock($value)
    {
        $this->lock = trim($value);
    }
    /**
     * set lock
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function lock()
    {
        $this->lock = 'CLOSE';
    }

    /**
     * set unlock
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function unlock()
    {
        $this->lock = 'OPEN';
    }

    /**
     * is the path locked ?
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */
    public function isLocked()
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
    public function isUnlocked()
    {
        return !$this->isLocked();
    }

    /**
     * set viewMode
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function setViewMode($value)
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
    public function getViewMode()
    {
        return $this->viewMode;
    }

    /**
     * show the path fullscreen ?
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */
    public function isFullscreen()
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
    public function getHigherRank()
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
class pathListIterator implements SeekableIterator, Countable
{
    /**
     * @var $tblPath name of the path table
     */
    private $tblPath;
    private $pathList;


    /**
     * Constructor
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function __construct()
    {
        $tblNameList = array(
            'lp_path'
        );

        // convert to Claroline course table names
        $tbl_lp_names = get_module_course_tbl( $tblNameList, claro_get_current_course_id() );
        $this->tblPath = $tbl_lp_names['lp_path'];
        
        $this->pathList = array();
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
        if( !is_null($userId) )
        {
            $this->pathList = $this->loadUserProgress($userId);
        }
        else
        {
            $this->pathList = $this->loadAll();
        }
    }

    /**
     * load list of all learning paths
     *
     * @return array 2d array containing list of all available learning paths
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function loadAll()
    {
        $sql = "SELECT
                    `id`,
                    `title`,
                    `description`,
                    `visibility`,
                    `rank`,
                    `version`,
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
    public function loadUserProgress( $userId )
    {
        // TODO ... write the correct code ?
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
    public function movePathUp($path)
    {
        // find where is the path in the list to get the id of the previous one
        $i = 0;
        while( $i < count($this->pathList) )
        {
            if( $this->pathList[$i]['id'] == $path->getId() )
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
        $otherPathId = $this->pathList[$i-1]['id'];


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

            // then move paths in the list
            $tempPath = $this->pathList[$i-1];
            $this->pathList[$i-1] = $this->pathList[$i];
            $this->pathList[$i] = $tempPath;
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
    public function movePathDown($path)
    {
        // find where is the path is the list to get the id of the next one
        $i = 0;
        while( $i < count($this->pathList) )
        {
            if( $this->pathList[$i]['id'] == $path->getId() )
            {
                break;
            }
            $i++;
        }

        // if the path is the first of the list
        if( $i == count($this->pathList) - 1 )
        {
            return false;
        }

        $currentRank = $path->getRank();
        $otherPathId = $this->pathList[$i+1]['id'];


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
            
            // then move paths in the list
            $tempPath = $this->pathList[$i+1];
            $this->pathList[$i+1] = $this->pathList[$i];
            $this->pathList[$i] = $tempPath;
            return true;
        }
        else
        {
            return false;
        }
    }
    
    // iterator implementation
    public function first()
    {
        $this->seek(0);
        return $this->current();
    }
    
    public function last()
    {
        $this->seek($this->count() - 1);
        return $this->current();
    }
    
    // Countable
    
    public function count()
    {
        return count( $this->pathList );
    }
    
    // Iterator
    
    protected $idx = 0;
    
    public function valid()
    {
        return !empty($this->pathList)
            && $this->idx >= 0
            && $this->idx < $this->count();
    }
    
    public function rewind()
    {
        $this->idx = 0;
    }
    
    public function next()
    {
        $this->idx++;
    }
    
    public function current()
    {
        return $this->pathList[$this->idx];
    }
    
    public function key()
    {
        return $this->idx;
    }
    
    // SeekableIterator
    
    public function seek( $index )
    {
        $this->idx = $index;
        
        if ( !$this->valid() )
        {
            throw new OutOfBoundsException('Invalid seek position');
        }
    }
}

?>