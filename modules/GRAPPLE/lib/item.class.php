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
 * @package GRAPPLE
 *
 * @author Sebastien Piraux
 *
 */

class item
{
    /**
     * @var $id id of item, -1 if item doesn't exist already
     */
    private $id;

    /**
     * @var $pathId id of path containing this item
     */
    private $pathId;

    /**
     * @var $type type of the item
     */
    private $type;

    /**
     * @var $title name of the item
     */
    private $title;

    /**
     * @var $description statement of the item
     */
    private $description;

    /**
     * @var $visibility visibility of the item (default is invisible)
     */
    private $visibility;

    /**
     * @var $rank order of the item in the item list
     */
    private $rank;

    /**
     * @var $identifier SCORM manifest ressource identifier
     */
    private $identifier;

    /**
     * @var $sysPath physical location of item ressources
     */
    private $sysPath;

    /**
     * @var $parentId id of item that is direct parent of this
     */
    private $parentId;

    /**
     * @var $previousId id of the item previous of this
     */
    private $previousId;

    /**
     * @var $nextId id of the item next to this
     */
    private $nextId;

    /**
     * @var $launchData text data required by the SCO to be launched (has been read in the manifest)
     */
    private $launchData;

    /**
     * @var $timeLimitAction define how the LMS must handle the sco if time is out
     * possible values are : 'exit,message', 'exit,no message', 'continue,message', 'continue,no message'
     */
    private $timeLimitAction;

    /**
     * @var $completionThreshold defineshow must be computed the completion status
     */
    private $completionThreshold;

    /**
     * @var $tblItem name of the item table
     */
    private $tblItem;
    
    /**
     * @var $tblBlockCond
     */
    private $tblBlockCond;
    
    /**
     * @var $redirectBranchConditions
     */
    private $redirectBranchConditions;
    
    /**
     * @var $branchConditions
     */
    private $branchConditions;
    
    /**
     * @var $newWindow
     **/
    private $newWindow;
    /**
     * Constructor
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */

    public function __construct()
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
        $this->redirectBranchConditions = 0;
        $this->newWindow = 0;

        // define module table names
        $tblNameList = array(
            'lp_item', 'lp_item_blockcondition'
        );

        // convert to Claroline course table names
        $tbl_lp_names = get_module_course_tbl( $tblNameList, claro_get_current_course_id() );
        $this->tblItem = $tbl_lp_names['lp_item'];
        $this->tblBlockCond = $tbl_lp_names['lp_item_blockcondition'];
    }

    /**
     * load an item from DB
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param integer $id id of path
     * @return boolean load successfull ?
     */
    public function load($id)
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
                    `completionThreshold`,
                    `redirectBranchConditions`,
                    `branchConditions`,
                    `newWindow`
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
            $this->redirectBranchConditions = $data['redirectBranchConditions'];
            $this->branchConditions = $data['branchConditions'];
            $this->newWindow = $data['newWindow'];

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
                        `completionThreshold` = '".addslashes($this->completionThreshold)."',
                        `redirectBranchConditions` = '".(int) $this->redirectBranchConditions."',
                        `branchConditions` = '". addslashes( $this->branchConditions ) ."',
                        `newWindow` = '".(int) $this->newWindow."'";

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
                        `completionThreshold` = '".addslashes($this->completionThreshold)."',
                        `redirectBranchConditions` = '". (int) $this->redirectBranchConditions ."',
                        `branchConditions` = '" . addslashes( $this->branchConditions ) ."',
                        `newWindow` = '". (int) $this->newWindow ."'
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
    public function delete()
    {
        if( $this->id == -1 ) return true;
        
        if( $this->type == 'CONTAINER' )
        {
            $itemList = new PathItemList( $this->pathId );
            $itemListChildren = $itemList->getNodeChildrenId( $this->pathId, $this->id );
            foreach( $itemListChildren as $itemId)
            {
                $sql = "DELETE FROM `" . $this->tblItem . "`
                        WHERE `id` = ". (int) $itemId;
                
                if( claro_sql_query($sql) == false )
                {
                    return false;
                    break;
                }
                
                // delete blocking conditions
                $sql = "DELETE FROM `" . $this->tblBlockCond . "`
                        WHERE `item_id` = ". (int) $itemId;
                
                if( claro_sql_query($sql) == false )
                {
                    return false;
                    break;
                }
            }
        }
        
        $sql = "DELETE FROM `" . $this->tblItem . "`
                WHERE `id` = " . (int) $this->id ;

        if( claro_sql_query($sql) == false ) return false;
        
        // delete blocking conditions
        $sql = "DELETE FROM `" . $this->tblBlockCond . "`
                WHERE `item_id` = ". $this->id;
        
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
    public function validate()
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
    public function getId()
    {
        return (int) $this->id;
    }

    /**
     * get path id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int
     */
    public function getPathId()
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
    public function setPathId($value)
    {
        $this->pathId = (int) $value;
    }

    /**
     * get type
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * set type
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value
     */
    public function setType($value)
    {
        $acceptedValues = array('CONTAINER', 'MODULE', 'SCORM', 'GRAPPLE' );

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
     * is the item visible
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
     * is the item invisible
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */
    public function isInvisible()
    {
        return !$this->isVisible();
    }

    /**
     * get identifier
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * set identifier
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value
     */
    public function setIdentifier($value)
    {
        $this->identifier = trim($value);
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
        $this->rank = (int) $value;
    }

    /**
     *
     *
     *
     */
    public function setHigherRank($pathId)
    {
        $this->rank = (int) $this->getHigherRank($pathId) + 1;
    }

    /**
     * get lock
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string
     */
    public function getLock()
    {
        return (int) $this->lock;
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
     * get sysPath
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string crl of claroline ressource or SCORM ressource relative path or scorm webcontent url
     */
    public function getSysPath()
    {
        return $this->sysPath;
    }

    /**
     * set sysPath
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value crl of claroline ressource or SCORM ressource relative path or scorm webcontent url
     */
    public function setSysPath($value)
    {
        $this->sysPath = trim($value);
    }

    /**
     * get parent id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int
     */
    public function getParentId()
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
    public function setParentId($value)
    {
        $this->parentId = (int) $value;
    }

    /**
     * get launchData
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string data provided by manifest to the SCO
     */
    public function getLaunchData()
    {
        return $this->launchData;
    }

    /**
     * set launchData
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value data provided by manifest to the SCO
     */
    public function setLaunchData($value)
    {
        $this->launchData = trim($value);
    }

    /**
     * get timeLimitAction
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string defines how the LMS must handle SCO when time is out
     */
    public function getTimeLimitAction()
    {
        return $this->timeLimitAction;
    }

    /**
     * set timeLimitAction
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string defines how the LMS must handle SCO when time is out
     */
    public function setTimeLimitAction($value)
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
    public function getCompletionThreshold()
    {
        return $this->completionThreshold;
    }

    /**
     * set completionThreshold
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string value of threshold required if setted to compute tu completion_status
     */
    public function setCompletionThreshold($value)
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
    private function getHigherRank($pathId)
    {
        $sql = "SELECT max(`rank`)
                FROM `" . $this->tblItem . "`
                WHERE `path_id` = ". (int) $pathId ."
                AND `parent_id` = ".$this->getParentId();

        $rankMax = claro_sql_query_get_single_value($sql);

        if( !is_null($rankMax) || !$rankMax ) return (int) $rankMax;
        else                     			  return 0;
    }
    
    public function getRedirectBranchConditions()
    {
        return $this->redirectBranchConditions;
    }
    
    public function setRedirectBranchConditions( $value )
    {
        $value = (int) $value;
        if( $value == 0 || $value == 1)
        {
            $this->redirectBranchConditions = $value;
            return true;
        }
        else
        {
            return false;
        }        
    }
    
    public function getBranchConditions()
    {
        $_branchConditions = unserialize( $this->branchConditions );
        
        return $_branchConditions;
    }
    
    public function setBranchConditions( $branchConditions )
    {
        if( !( isset( $branchConditions['sign'] ) && isset( $branchConditions['item'] ) && isset( $branchConditions['value'] ) ) )
        {
            return false;
        }
        
        if( !( ( count( $branchConditions['sign'] ) == count( $branchConditions['item'] ) )
            && ( count( $branchConditions['sign'] ) == count( $branchConditions['value'] ) ) ) )
        {
            return false;
        }
        
        $_branchConditions = array();
        
        foreach( $branchConditions['sign'] as $key => $sign )
        {
            $_branchConditions[$key]['sign'] = $sign;
            $_branchConditions[$key]['value'] = $branchConditions['value'][$key];
            $_branchConditions[$key]['item'] = $branchConditions['item'][$key];
        }
        $this->branchConditions = serialize( $_branchConditions );
    }
    
    public function getNewWindow()
    {
        return (int) $this->newWindow;
    }
    
    public function setNewWindow( $value )
    {
        $value = (int) $value;
        if( $value == 0 || $value == 1)
        {
            $this->newWindow = $value;
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function evalBranchConditions( $pathId )
    {
        $attempt = new attempt();
        if( !$attempt->load($this->getPathId(), claro_get_current_user_id()) )
        {
            return false;
        }
        $itemAttempt = new itemAttempt();
        if( ! $itemAttempt->load( $attempt->getId(), $this->getId() ) )
        {
            return false;
        }

        $branchConditions = $this->getBranchConditions();
        if( is_array($branchConditions) && count($branchConditions) )
        {
        
            $_conditions = array(
                                     0 => array(),
                                     1 => array(),
                                     2 => array(),
                                     3 => array(),
                                     4 => array()
                                     );
            //array keys : 0 = '=', 1 = '>', 2 = '>=', 3 = '<=', 4 = '<'
            foreach( $branchConditions as $branchCondition )
            {
                if( $branchCondition['sign'] == '=' )
                {
                    $_conditions[0][$branchCondition['value']] = $branchCondition['item'];
                }
                elseif( $branchCondition['sign'] == '>' )
                {
                    $_conditions[1][$branchCondition['value']] = $branchCondition['item'];
                }
                elseif( $branchCondition['sign'] == '&#8805;' )
                {
                    $_conditions[2][$branchCondition['value']] = $branchCondition['item'];
                }
                elseif( $branchCondition['sign'] == '&#8804;' )
                {
                    $_conditions[3][$branchCondition['value']] = $branchCondition['item'];
                }
                elseif( $branchCondition['sign'] == '<' )
                {
                    $_conditions[4][$branchCondition['value']] = $branchCondition['item'];
                }                
            }
            if( ! $this->getRedirectBranchConditions() )
            {
                foreach($_conditions as $key => $condition)
                {
                    if(empty($condition))
                    {
                        unset($_conditions[$key]);
                    }
                }
                return $_conditions;
            }
            else
            {
                foreach( $_conditions as $key => $condition )
                {
                    krsort($_conditions[$key]);
                }
                foreach( $_conditions as $key => $condition )
                {
                    foreach($condition as $value => $item )
                    {
                        switch( $key )
                        {
                            case 0 :
                            {
                                if(  $value == $itemAttempt->getScoreRaw() )
                                {
                                    return (int) $item;
                                }
                            }
                            break;
                            case 1 :
                            {
                                if( $itemAttempt->getScoreRaw() > $value )
                                {
                                    return (int) $item;
                                }
                            }
                            break;
                            case 2 :
                            {
                                if( $itemAttempt->getScoreRaw() >= $value )
                                {
                                    return (int) $item;
                                }
                            }
                            break;
                            case 3 :
                            {
                                if( $itemAttempt->getScoreRaw() <= $value )
                                {
                                    return (int) $item;
                                }
                            }
                            break;
                            case 4 :
                            {
                                if( $itemAttempt->getScoreRaw() < $value )
                                {
                                    return (int) $item;
                                }
                            }
                            break;
                        }
                    }
                }
                
                return false;   
            }
        }
        else
        {
            return false;
        }
    }
}

class itemList
{
    protected $pathId;
    protected $tblPath;
    protected $tblItem;
    protected $treeItemList;


    public function __construct($pathId)
    {
        $this->pathId = (int) $pathId;
        
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
    public function getFlatList()
    {
        return $this->flatList( $this->treeItemList );
    }

    public function getItemTree()
    {
        return $this->treeItemList;
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
    protected function buildTree($list, $excludedItemId = null)
    {
        return $this->recursiveBuildTree($list, $excludedItemId);
    }
    
    protected function recursiveBuildTree($list, $excludedItemId = null, $id = -1, $depth = 0 )
    {
        $tree = array();
        
        if( !is_array($list) || empty($list) )
        {
            return $tree;
        }
        else
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
                if( $item['parent_id'] == $id && $item['parent_id'] != $item['id'] && $item['id'] != $excludedItemId )
                {
                    if($id == -1)
                    {
                        array_push($tree, $this->recursiveBuildTree($list, $excludedItemId, $item['id'], $depth++));
                    }
                    else
                    {
                        $tree['children'][] = $this->recursiveBuildTree($list, $excludedItemId, $item['id'], $depth++);
                    }
                }
            }
            return $tree;
        }

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
    protected function flatList($treeList, $deepness = 0)
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



	/**
     * move item one position up in the tree if possible
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $item object item to move up
     * @return boolean result of operation
     */
    public function moveItemUp($item,$path)
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
    public function moveItemDown($item,$path)
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
    public function getNodeChildren($pathId,$itemId)
    {
        $list = array();

        // get all items list
        $itemList = $this->buildTree( $this->load($pathId) );

        if( $itemId != -1 )
        {
            // only those with same parent as itemId so the node made of the parent of this one
            $parentNode = $this->getNode( $itemList, $itemId );

            if( isset($parentNode['children']) && is_array($parentNode['children']) && !empty($parentNode['children']) )
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
     * returns an array of all children ids
     * @author Dimitri Rambout <dimitri.rambout@uclouvain.be>
     * @param $tree array that represent a tree
     * @return array
     * 
     */
    public function getNodeChildrenId( $pathId, $itemId )
    {
        $tree = $this->flatList( $this->getNodeChildren( $pathId, $itemId ) );
        $list = array();
        
        if( is_array( $tree ) && $treeCount = count( $tree ) )
        {
            $tree = $this->flatList($tree);
            for( $i = 0; $i < $treeCount; $i++ )
            {
                $list[] = $tree[$i]['id'];
            }
            
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
    public function getNode($tree,$nodeId)
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
	
    // methods used to jump from one item to another in LP viewer
    public function getNext( $currentId )
    {
        $itemFlatList = $this->getFlatList();
        $itemFlatListCount = count( $itemFlatList );
        
        for( $i = 0; $i < $itemFlatListCount; $i++ )
        {
            if($itemFlatList[$i]['id'] ==  $currentId){
                break;
            }
        }
        // check if it's not the last item of the array
        if( $i < $itemFlatListCount-1 ){
            //set pointer at the good position in the array
            while( $i != key( $itemFlatList ))
            {
                next( $itemFlatList );
            }
            
            //get the next item which is not a CONTAINER
            do
            {
                $item = next( $itemFlatList );    
            }while( $item['type'] == 'CONTAINER' );
            
            if( $item ['type'] != 'CONTAINER' )
            {
                $nextId = $item[ 'id' ];
                return $nextId;
            }
            else
            {
                return false;
            }            
        }
        else
        {
            return false;
        }
    }
    
    public function getPrevious( $currentId )
    {
        $itemFlatList = $this->getFlatList();
        $itemFlatListCount = count( $itemFlatList );
        
        for( $i = 0; $i < $itemFlatListCount; $i++ )
        {
            if($itemFlatList[$i]['id'] ==  $currentId){
                break;
            }
        }
        // check if it's not the first item of the array
        if( $i != 0 ){
            //set pointer at the good position in the array
            while( $i != key( $itemFlatList ))
            {
                next( $itemFlatList );
            }
            
            //get the previous item which is not a CONTAINER
            do
            {
                $item = prev( $itemFlatList );    
            }while( $item['type'] == 'CONTAINER' );
            
            if( $item ['type'] != 'CONTAINER' )
            {
                $previousId = $item[ 'id' ];
                return $previousId;
            }
            else
            {
                return false;
            }            
        }
        else
        {
            return false;
        }
    }
}

class PathItemList extends ItemList
{
    public function __construct($pathId)
    {
        parent::__construct($pathId);

        $this->treeItemList = $this->buildTree($this->load());
    }
    
    public function load()
    {
        // prevent a query made on incorrect data
        if( is_null($this->pathId) || !is_numeric($this->pathId) )
        {
            return array();
        }

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
            WHERE `path_id` = ".(int) $this->pathId."
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


    public function loadContainerList()
    {
    // prevent a query made on incorrect data
        if( is_null($this->pathId) || !is_numeric($this->pathId) )
        {
            return array();
        }
        
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
            AND `path_id` = ".(int) $this->pathId."
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
    
    public function getContainerList()
    {
        return $this->loadContainerList();
    }
    
    public function getContainerTree()
    {
        return $this->buildTree($this->loadContainerList());
    }
}

class PathUserItemList extends ItemList 
{
    private $userId;
    private $attemptId;
    private $tblAttempt;
    private $tblItemAttempt;
    
    public function __construct($pathId, $userId, $attemptId)
    {
        parent::__construct($pathId);
        
        $this->userId = (int) $userId;
        $this->attemptId = (int) $attemptId;
        
        $tblNameList = array(
            'lp_attempt',
            'lp_item_attempt'
        );
        // convert to Claroline course table names
        $tbl_lp_names = get_module_course_tbl( $tblNameList, claro_get_current_course_id() );
        $this->tblAttempt = $tbl_lp_names['lp_attempt'];
        $this->tblItemAttempt = $tbl_lp_names['lp_item_attempt'];
        
        $this->treeItemList = $this->buildTree($this->load());
    }
    
    public function load()
    {
        if( is_null($this->pathId) || !is_numeric($this->pathId) )
        {
            return array();
        }
        // TODO : manage visibility for teacher/student/admin
        $sql = "SELECT
                    `I`.`id`,
                    `I`.`path_id`,
                    `I`.`type`,
                    `I`.`title`,
                    `I`.`description`,
                    `I`.`visibility`,
                    `I`.`rank`,
                    `I`.`identifier`,
                    `I`.`sys_path`,
                    `I`.`parent_id`,
                    `I`.`previous_id`,
                    `I`.`next_id`,
                    `I`.`launch_data`,
                    `IA`.`location`,
                    `IA`.`completion_status`,
                    `IA`.`entry`,
                    `IA`.`score_raw`,
                    `IA`.`score_min`,
                    `IA`.`score_max`,
                    `IA`.`total_time`,
                    `IA`.`session_time`,
                    `IA`.`suspend_data`,
                    `IA`.`credit`
            FROM `".$this->tblItem."` AS `I`
            LEFT JOIN `".$this->tblItemAttempt."` AS `IA`
              ON `IA`.`item_id` = `I`.`id`
              AND `IA`.`attempt_id` = ".(int) $this->attemptId."
            WHERE `path_id` = ".(int) $this->pathId." AND `I`.`visibility` = 'VISIBLE'
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
}
?>