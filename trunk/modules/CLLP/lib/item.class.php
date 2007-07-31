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

class item
{
    /**
     * @var $id id of item, -1 if item doesn't exist already
     */
    var $id;

    /**
     * @var $pathId id of path containing this item
     */
    var $pathId;

    /**
     * @var $type type of the item
     */
    var $type;

    /**
     * @var $title name of the item
     */
    var $title;

    /**
     * @var $description statement of the item
     */
    var $description;

    /**
     * @var $visibility visibility of the item (default is invisible)
     */
    var $visibility;

    /**
     * @var $rank order of the item in the item list
     */
    var $rank;

    /**
     * @var $identifier SCORM manifest ressource identifier
     */
    var $identifier;

    /**
     * @var $sysPath physical location of item ressources
     */
    var $sysPath;

    /**
     * @var $parentId id of item that is direct parent of this
     */
    var $parentId;

    /**
     * @var $previousId id of the item previous of this
     */
    var $previousId;

    /**
     * @var $nextId id of the item next to this
     */
    var $nextId;

    /**
     * @var $launchData text data required by the SCO to be launched (has been read in the manifest)
     */
    var $launchData;

    /**
     * @var $timeLimitAction define how the LMS must handle the sco if time is out
     * possible values are : 'exit,message', 'exit,no message', 'continue,message', 'continue,no message'
     */
    var $timeLimitAction;

    /**
     * @var $completionThreshold defineshow must be computed the completion status
     */
    var $completionThreshold;

    /**
     * @var $tblItem name of the item table
     */
    var $tblItem;


    /**
     * Constructor
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */

    function item()
    {
        $this->id = (int) -1;
        $this->pathId = (int) -1;
        $this->type = 'MODULE';
        $this->title = '';
        $this->description = '';
        $this->visibility = 'INVISIBLE';
        $this->rank = 0;
        $this->identifier = '';
        $this->sysPath = '';
        $this->parentId = (int) -1;
        $this->previousId = (int) -1;
        $this->nextId = (int) -1;
        $this->launchData = '';
        $this->timeLimitAction = 'continue,no message';
        $this->completionThreshold = '';

        // define module table names
        $tblNameList = array(
            'lp_item'
        );

        // convert to Claroline course table names
        $tbl_lp_names = get_module_course_tbl( $tblNameList, claro_get_current_course_id() );
        $this->tblItem = $tbl_lp_names['lp_item'];
    }

    /**
     * load an item from DB
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param integer $id id of path
     * @return boolean load successfull ?
     */
    function load($id)
    {
        $sql = "SELECT
                    `id`,
                    `path_id`,
                    `type`,
                    `title`,
                    `description`,
                    `visibility`,
                    `rank`,
                    `identifier`,
                    `sys_path`,
                    `parent_id`,
                    `previous_id`,
                    `next_id`,
                    `launch_data`,
                    `timeLimitAction`,
                    `completionThreshold`
            FROM `".$this->tblItem."`
            WHERE `id` = ".(int) $id;

        $data = claro_sql_query_get_single_row($sql);

        if( !empty($data) )
        {
            // from query
            $this->id = (int) $data['id'];
            $this->pathId = (int) $data['path_id'];
            $this->type = $data['type'];
            $this->title = $data['title'];
            $this->description = $data['description'];
            $this->visibility = $data['visibility'];
            $this->rank = (int) $data['rank'];
            $this->identifier = $data['identifier'];
            $this->sysPath = $data['sys_path'];
            $this->parentId = $data['parent_id'];
            $this->previousId = $data['previous_id'];
            $this->nextId = $data['next_id'];
            $this->launchData = $data['launch_data'];
            $this->timeLimitAction = $data['timeLimitAction'];
            $this->completionThreshold = $data['completionThreshold'];


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
            $this->setHigherRank($this->pathId);

            // insert
            $sql = "INSERT INTO `".$this->tblItem."`
                    SET `path_id` = '".(int) $this->pathId."',
                        `type` = '".addslashes($this->type)."',
                    	`title` = '".addslashes($this->title)."',
                        `description` = '".addslashes($this->description)."',
                        `visibility` = '".addslashes($this->visibility)."',
                        `rank` = ".(int) $this->rank.",
                        `identifier` = '".addslashes($this->identifier)."',
                        `sys_path` = '".addslashes($this->sysPath)."',
                        `parent_id` = ".(int) $this->parentId.",
                        `previous_id` = ".(int) $this->previousId.",
                        `next_id` = ".(int) $this->nextId.",
                        `launch_data` = '".addslashes($this->launchData)."',
                        `timeLimitAction` = '".addslashes($this->timeLimitAction)."',
                        `completionThreshold` = '".addslashes($this->completionThreshold)."'";

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
            $sql = "UPDATE `".$this->tblItem."`
                    SET `path_id` = '".(int) $this->pathId."',
                        `type` = '".addslashes($this->type)."',
                    	`title` = '".addslashes($this->title)."',
                        `description` = '".addslashes($this->description)."',
                        `visibility` = '".addslashes($this->visibility)."',
                        `rank` = ".(int) $this->rank.",
                        `identifier` = '".addslashes($this->identifier)."',
                        `sys_path` = '".addslashes($this->sysPath)."',
                        `parent_id` = ".(int) $this->parentId.",
                        `previous_id` = ".(int) $this->previousId.",
                        `next_id` = ".(int) $this->nextId.",
                        `launch_data` = '".addslashes($this->launchData)."',
                        `timeLimitAction` = '".addslashes($this->timeLimitAction)."',
                        `completionThreshold` = '".addslashes($this->completionThreshold)."'
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
     * delete path
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */
    function delete()
    {
        if( $this->id == -1 ) return true;

        $sql = "DELETE FROM `" . $this->tblItem . "`
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
            claro_failure::set_failure('item_no_title');
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
     * get type
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string
     */
    function getType()
    {
        return $this->type;
    }

    /**
     * set type
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value
     */
    function setType($value)
    {
        $acceptedValues = array('CONTAINER', 'MODULE', 'SCORM');

        if( in_array($value, $acceptedValues) )
        {
            $this->type = $value;
            return true;
        }
        return false;
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
     * is the item visible
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
     * is the item invisible
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */
    function isInvisible()
    {
        return !$this->isVisible();
    }

    /**
     * get identifier
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string
     */
    function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * set identifier
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value
     */
    function setIdentifier($value)
    {
        $this->identifier = trim($value);
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
        $this->rank = (int) $value;
    }

    /**
     *
     *
     *
     */
    function setHigherRank($pathId)
    {
        $this->rank = (int) $this->getHigherRank($pathId) + 1;
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
     * get sysPath
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string crl of claroline ressource or SCORM ressource relative path or scorm webcontent url
     */
    function getSysPath()
    {
        return $this->sysPath;
    }

    /**
     * set sysPath
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value crl of claroline ressource or SCORM ressource relative path or scorm webcontent url
     */
    function setSysPath($value)
    {
        $this->sysPath = trim($value);
    }

    /**
     * get parent id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int
     */
    function getParentId()
    {
        return (int) $this->parentId;
    }

    /**
     * set parent id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $value int id of the parent
     * @return int
     */
    function setParentId($value)
    {
        $this->parentId = (int) $value;
    }

    /**
     * get launchData
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string data provided by manifest to the SCO
     */
    function getLaunchData()
    {
        return $this->launchData;
    }

    /**
     * set launchData
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value data provided by manifest to the SCO
     */
    function setLaunchData($value)
    {
        $this->launchData = trim($value);
    }

    /**
     * get timeLimitAction
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string defines how the LMS must handle SCO when time is out
     */
    function getTimeLimitAction()
    {
        return $this->timeLimitAction;
    }

    /**
     * set timeLimitAction
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string defines how the LMS must handle SCO when time is out
     */
    function setTimeLimitAction($value)
    {
        $acceptedValues = array('exit,message', 'exit,no message', 'continue,message', 'continue,no message');

        if( in_array($value, $acceptedValues) )
        {
            $this->timeLimitAction = $value;
            return true;
        }
        return false;
    }

    /**
     * get completionThreshold
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string value of threshold required if setted to compute tu completion_status
     */
    function getCompletionThreshold()
    {
        return $this->completionThreshold;
    }

    /**
     * set completionThreshold
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string value of threshold required if setted to compute tu completion_status
     */
    function setCompletionThreshold($value)
    {
        $this->completionThreshold = trim($value);
    }

    /**
     * get the higher rank of items in learning path
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $pathId
     * @param $parentId
     * @return int higher item rank for items that have same parentId
     */
    function getHigherRank($pathId)
    {
        $sql = "SELECT max(`rank`)
                FROM `" . $this->tblItem . "`
                WHERE `path_id` = ". (int) $pathId ."
                AND `parent_id` = ".$this->getParentId();

        $rankMax = claro_sql_query_get_single_value($sql);

        if( !is_null($rankMax) || !$rankMax ) return (int) $rankMax;
        else                     			  return 0;
    }
}

class itemList
{
	var $tblPath;
	var $tblItem;

	function itemList()
    {
        $tblNameList = array(
            'lp_path',
            'lp_item'
        );

        // convert to Claroline course table names
        $tbl_lp_names = get_module_course_tbl( $tblNameList, claro_get_current_course_id() );
        $this->tblPath = $tbl_lp_names['lp_path'];
        $this->tblItem = $tbl_lp_names['lp_item'];
    }

    // load correct flat list of modules depending on parameters
    function getFlatList($pathId = null, $userId = null)
    {
        $list = $this->load($pathId,$userId);

		return $this->flatList( $this->buildTree($list,-1) );
    }

    /**
     * Build an tree of $list from $id using the 'parent'
     * table. (recursive function)
     * Rows with a father id not existing in the array will be ignored
     *
     * @param $list modules of the learning path list
     * @param $id learnPath_module_id of the node to build
     * @return tree of the learning path
     *
     * @author Piraux Sebastien <pir@cerdecam.be>
     */
    function buildTree($list, $id = -1, $depth = 0)
    {
        $tree = array();

        if( is_array($list) && !empty($list) )
        {
            foreach ($list as $item)
            {
                if( $item['id'] == $id )
                {
                    $tree = $item; // keep all $list informations in the returned array

                    // add parameters that will be used in claro_build_nested_menu
                    $tree['name'] = $item['title'];
                    $tree['value'] = $item['id'];
                    break;
                }
            }

            foreach ($list as $item)
            {
                if( $item['parent_id'] == $id && $item['parent_id'] != $item['id'] )
                {
                    if($id == -1)
                    {
                        $tree[] = $this->buildTree($list, $item['id'], $depth++);
                    }
                    else
                    {
                        $tree['children'][] = $this->buildTree($list,  $item['id'], $depth++);
                    }
                }
            }
        }

        return $tree;
    }

    /**
     * return a flattened tree of the modules of a learnPath after having add
     * 'up' and 'down' fields to let know if the up and down arrows have to be
     * displayed. (recursive function)
     *
     * @param $elementList a tree array as one returned by build_element_list
     * @param $deepness
     * @return array containing infos of the learningpath, each module is an element
        of this array and each one has 'up' and 'down' boolean and deepness added in
     *
     * @author Piraux Sebastien <pir@cerdecam.be>
     */
    function flatList($treeList, $deepness = 0)
    {
        $count = 0;
        $itemIsFirst = true;
        $itemIsLast = false;
        $flatList = array();

        foreach($treeList as $item)
        {
            $count++;

            // temporary save the children (see buildTree() ) before overwritten it
            if( isset($item['children']) )
                $temp = $item['children'];
            else
                $temp = NULL; // re init temp value if there is nothing to put in it

            //
            $item['deepness'] = $deepness;
            // remove children before copying item to final array as we do not need it anymore.
            unset($item['children']);

            //--- up and down arrows displayed ?
            if( $count == count($treeList) ) $itemIsLast = true;

            $item['canMoveUp'] = ! $itemIsFirst;
            $item['canMoveDown'] = ! $itemIsLast;

            //---
            $itemIsFirst = false;

            $flatList[] = $item;

            if ( isset( $temp ) && sizeof( $temp ) > 0 )
            {
                $flatList = array_merge( $flatList, $this->flatList($temp, $deepness + 1 ) );
            }
        }

        return  $flatList;
    }

    // load correct list of modules depending on parameters
	function load($pathId = null, $userId = null)
	{
		if( !is_null($pathId) )
		{
			if( !is_null($userId) )
			{
				return $this->loadUserPath($pathId, $userId);
			}
			else
			{
				return $this->loadPath($pathId);
			}
		}
		else
		{
			return $this->loadAll();
		}
	}

	// should return a list of all available module (for module pool if any)
	function loadAll()
	{
        $sql = "SELECT
                    `id`,
                    `path_id`,
                    `type`,
                    `title`,
                    `description`,
                    `visibility`,
                    `rank`,
                    `identifier`,
                    `sys_path`,
                    `parent_id`,
                    `previous_id`,
                    `next_id`,
                    `launch_data`
            FROM `".$this->tblItem."`
            ORDER BY `rank` ASC";

        if ( false === ( $data = claro_sql_query_fetch_all_rows($sql) ) )
        {
            return array();
        }
        else
        {
            return $data;
        }
	}

	// should return a tree list of path items for course administrator
	function loadPath($pathId)
	{
		    $sql = "SELECT
                    `id`,
                    `path_id`,
                    `type`,
                    `title`,
                    `description`,
                    `visibility`,
                    `rank`,
                    `identifier`,
                    `sys_path`,
                    `parent_id`,
                    `previous_id`,
                    `next_id`,
                    `launch_data`
            FROM `".$this->tblItem."`
            WHERE `path_id` = ".(int) $pathId."
            ORDER BY `rank` ASC";

        if ( false === ( $data = claro_sql_query_fetch_all_rows($sql) ) )
        {
            return array();
        }
        else
        {
            return $data;
        }
	}

	// should return a tree list of path items for a single user with its progression
	function loadPathUserProgress($pathId, $userId)
	{

	}

	function loadContainerList($pathId)
	{
	   $sql = "SELECT
                    `id`,
                    `path_id`,
                    `type`,
                    `title`,
                    `description`,
                    `visibility`,
                    `rank`,
                    `identifier`,
                    `sys_path`,
                    `parent_id`,
                    `previous_id`,
                    `next_id`,
                    `launch_data`
            FROM `".$this->tblItem."`
            WHERE `type` = 'container'
            AND `path_id` = ".(int) $pathId."
            ORDER BY `rank` ASC";

        if ( false === ( $data = claro_sql_query_fetch_all_rows($sql) ) )
        {
            return array();
        }
        else
        {
            return $data;
        }
	}

	/**
     * move item one position up in the tree if possible
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $item object item to move up
     * @return boolean result of operation
     */
    function moveItemUp($item,$path)
    {
        $list = $this->getNodeChildren($path->getId(), $item->getParentId());

        // find where is the path is the list to get the id of the previous one
        $i = 0;
        while( $i < count($list) )
        {
            if( $list[$i]['id'] == $item->getId() )
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

        $currentRank = $item->getRank();
        $otherItemId = $list[$i-1]['id'];


        // get the item that is at the new position
        $otherItem = new item();
        $otherItem->load($otherItemId);

        // invert ranks
        $newRank = $otherItem->getRank();

        $otherItem->setRank($currentRank);
        $item->setRank($newRank);

        // save the two paths
        if( $item->validate() && $otherItem->validate() )
        {
            $item->save();
            $otherItem->save();

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * move item one position down in the tree if possible
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $item object item to move down
     * @return boolean result of operation
     */
    function moveItemDown($item,$path)
    {

        $list = $this->getNodeChildren($path->getId(), $item->getParentId());

        // find where is the path is the list to get the id of the previous one
        $i = 0;
        while( $i < count($list) )
        {
            if( $list[$i]['id'] == $item->getId() )
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

        $currentRank = $item->getRank();
        $otherItemId = $list[$i+1]['id'];


        // get the item that is at the new position
        $otherItem = new item();
        $otherItem->load($otherItemId);

        // invert ranks
        $newRank = $otherItem->getRank();

        $otherItem->setRank($currentRank);
        $item->setRank($newRank);

        // save the two paths
        if( $item->validate() && $otherItem->validate() )
        {
            $item->save();
            $otherItem->save();

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * returns, for a path, the children of a node
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $tree array that represent a tree (fields id and children should be setted)
     * @param $nodeId int id of the node to get
     * @return boolean result of operation
     */
    function getNodeChildren($pathId,$itemId)
    {
        $list = array();

        // get all items list
        $itemList = $this->buildTree( $this->load($pathId) );

        if( $itemId != -1 )
        {
            // only those with same parent as itemId so the node made of the parent of this one
            $parentNode = $this->getNode( $itemList, $itemId );

            if( is_array($parentNode['children']) && !empty($parentNode['children']) )
            {
                // list of the children of parent, the item to move and its sibblings
                $list = $parentNode['children'];
            }
        }
        else
        {
            //item to move is at node
            $list = $itemList;
        }

        return $list;
    }

	/**
     * returns a given node and its children
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $tree array that represent a tree (fields id and children should be setted)
     * @param $nodeId int id of the node to get
     * @return boolean result of operation
     */
	function getNode($tree,$nodeId)
	{
	    foreach( $tree as $branch )
	    {
    	    if( !empty($branch['id']) && $branch['id'] ==  $nodeId )
    	    {
    	        return $branch;
    	    }
    	    elseif( is_array($branch) && isset($branch['children']) && is_array($branch['children']) )
    	    {
                $node = $this->getNode($branch['children'],$nodeId);
                if( is_array($node) ) return $node;
	        }
	    }

	    // not found
	    return false;
	}
}
?>
