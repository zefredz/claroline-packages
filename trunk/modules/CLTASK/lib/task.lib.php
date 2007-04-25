<?php // $Id$

if ( count( get_included_files() ) == 1 )
{
	die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
}

/**
 * CLAROLINE
 *
 * language library
 * contains function to manage l10n
 *
 * @copyright 2001-2007 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @see http://www.claroline.net/wiki/CLUSR
 * @package CLTASK
 * @author Tanguy Delooz <tdelooz32@hotmail.com>
 *
 */

	//if ( !defined('TASK_MAX_PRIORITY') ) 
	{
        define('TASK_MAX_PRIORITY', 3);
    }

/**
 * Class Task
 * 
 * A Task is an object containing :
 * title : a title, short description of the task
 * startDate :  the date where the task should start
 * endDate :    the date the task was actually done
 * dueDate :    the date the task is supposed to be done at last
 * description : text describing the task
 * priority :   a number reflecting how urgently the task should be done
 *              the lower the priority is, the more urgent the task is
 * A task can be saved, loaded and deleted.
 */
class Task 
{   
	// public properties
	var $id = 0;
	var $title = '';  // this field must be filled
	var $startDate = '0000-00-00 00:00:00';  // 'datetime' format
	var $dueDate = '0000-00-00 00:00:00';
	var $endDate = '0000-00-00 00:00:00';
	var $description = '';
	var $priority = 0;
    var $progress = NULL;
    var $visible = true;
	
	// private properties
	var $_tblNameList;  // array containing full names of the DB tables
	
    // var $_maxPriority = 10;

	/**
	 * Constructor
	 */
	function Task()
	{		
		// construction of the names of the table in DB
        $this->_tblNameList = get_module_course_tbl( array("cltask_tasks")
        	, claro_get_current_course_id());
	}
	
	/**
	 * Accessors and mutators
	 */
	
	/**
	 * @return int An integer containing the ID of the task
	 */
	function getId()
	{
		return (int) $this->id;
	}	
	
	/**
	 * @param int $id The task's ID in the DataBase table
	 */
	function setId($id)
	{
		$this->id = (int) $id;
	}
	
	/**
	 * @return string containing the title of the task
	 */
	function getTitle()
	{
		return $this->title;
	}
	
	/**
	 * @param string representing the task's title
	 */
	function setTitle($title)
	{   // data verif of $title could be written here
		$this->title = $title;
	}
	
	/**
	 * @return string in DATETIME format
	 */
	function getStartDate()
	{
		return $this->startDate;
	}
	
	function setStartDate($startDate)
	{
		$this->startDate = $startDate;
	}
	
	function getDueDate()
	{
		return $this->dueDate;
	}
	
	function setDueDate($dueDate)
	{
		$this->dueDate = $dueDate;
	}
	
	function getEndDate()
	{
		return $this->endDate;
	}
	
	function setEndDate($endDate)
	{
		$this->endDate = $endDate;
	}
	
	function getDescription()
	{
		return $this->description;
	}
	
	function setDescription($description)
	{
		$this->description = $description;
	}
	
	function getPriority()
	{
		return (int) $this->priority;
	}
	
	/**
	 * @param int priority $priority must be 0 <= $priority <= TASK_MAX_PRIORITY
	 * @return boolean true if the field is setted and false if not
     * @post if param < 0, the setted value will be 0
     *       if param > TASK_MAX_PR, the setted value will be TASK_MAX_PR
     *       else the value passed in parameter will setted as property
	 */
	function setPriority($priority)
	{
		if ( 0 <= $priority && $priority <= TASK_MAX_PRIORITY )
		{
			$this->priority = (int) $priority; 
			return true;
		}
        elseif ( $priority < 0 )
        {
        	$this->priority = 0;
            return true;
        }
        elseif ( TASK_MAX_PRIORITY < $priority )
        {
        	$this->priority = TASK_MAX_PRIORITY;
            return true;
        }
		else
		{
			return false;
		}
	}
    
    /**
     * @return NULL Null if nothing was setted (default/initial value)
     *         int An integer between 0 and 100 included if a value was setted
     */
    function getProgress()
    {
    	return $this->progress; //do not cast to (int) if it contains NULL
    }
    
    /**
     * @param int $progress a integer between 0 and 100
     * @return bool true if a value was setted
     *         bool false if nothing was setted
     * @post If setted, the progress property lies between
     *       0 and 100 included.
     *       If the parameter is smaller or bigger then the
     *       property will respectely equal 0 or 100.
     */
    function setProgress($progress)
    {   
    	if ( is_null( $progress ) )
        {
            $this->progress = null;
        }
    	elseif ( 0 <= $progress && $progress <= 100 )
        {
            $this->progress = (int) $progress; 
            return true;
        }
        elseif ( $progress < 0 )
        {
            $this->progress = 0;
            return true;
        }
        elseif ( 100 < $progress )
        {
            $this->progress = 100;
            return true;
        }
        else
        {
            return false;
        }
    }
	
    function isVisible()
    {
        return $this->visible;	
    }
    
    function setVisible()
    {
    	$this->visible = true;    
    }
    
    function isInvisible()
    {
    	return !$this->visible;
    }
    
    function setInvisible()
    {
    	$this->visible = false; 
    }
    
    
	/* *
	 * @param int $id An int representing an ID
	 * @return boolean true if there is a table in DB with that number,
	 * 		   false if not
	 */
	/*function idExists($id)  // Untested
	{
		$sql = "SELECT id FROM `".$this->tblNameList['cltask_tasks']
			."` WHERE id = ".(int)$id;
		
		//if ( false !== ( $res = claro_sql_query_get_single_value($sql) ) )
		if ( is_null( claro_sql_query_get_single_value($sql) ) )
			{
				return false;
			}
		else
			{
				return true;
			}
	}*/
	
	/**
	 * Saves a task in DB.
	 * If the ID = 0 the task will be created otherwise,
	 * if the task exists, it will be modified.
	 * @return boolean true if the task has been saved, false if not.
	 * @pre : title must not be empty ('')
	 * 			if ($id > 0) that ID must exist (in DB)
	 * @post : the fields are saved in DB
	 */
	function save()
	{
		if ( !empty($this->title) )
		{
			// create or modifie
			
			if ( $this->id == 0 )  // create
			{
				$sql = "INSERT INTO `" . $this->_tblNameList['cltask_tasks'] . "`"
					. " SET"  
					. " title = '" . addslashes( $this->title ) . "', " 
					. " startDate = '" . addslashes( $this->startDate ) . "', "
					. " endDate = '" . addslashes( $this->endDate ) . "', "
					. " dueDate = '" . addslashes( $this->dueDate ) . "', "
					. " description = '" . addslashes( $this->description ) . "', "
					. " priority = " . (int) $this->priority . ", "
                    . " progress = " . ( is_null( $this->progress ) 
                        ? 'NULL' 
                        : (int) $this->progress 
                        ) 
                    . ", "
                    . " visibility = " . ( $this->visible ? "'SHOW'" : "'HIDE'" )
					;
                
				// memorize the ID initialized in DB	
				if ( false === ($id = claro_sql_query_insert_id($sql) ) )
				{
					return false;
				}
				else
				{
					$this->id = $id;
					return true;
				}
			}
			else // update
			{	
				$sql = "UPDATE `" . $this->_tblNameList['cltask_tasks'] . "`"
					. " SET"  
					. " title = '" . addslashes( $this->title ) . "', " 
					. " startDate = '" . addslashes( $this->startDate ) . "', "
					. " endDate = '" . addslashes( $this->endDate ) . "', "
					. " dueDate = '" . addslashes( $this->dueDate ) . "', "
					. " description = '" . addslashes( $this->description ) . "', "
					. " priority = " . (int) $this->priority . ", "
                    . " progress = " . ( is_null( $this->progress ) 
                        ? 'NULL' 
                        : (int) $this->progress 
                        ) 
                    . ", "
                    . " visibility = " . ( $this->visible ? "'SHOW'" : "'HIDE'" ) . " "
					. " WHERE id = " . (int)$this->id
					;
					
				if ( false === ($rows = claro_sql_query_affected_rows($sql)) )
				{
					return false;
				}
				else
				{
					return ($rows === 1);
				}
			}
		}
		else
		{
			// erreur : le titre n'est pas valide
			return false;
		}
	}
	
	/**
	 * Load a task with the ID
	 * The task must exist (valide ID)
	 * 
	 * @param int $id an integer representing the ID
	 * @return boolean true when the task is loaded, false if unsucceeded
	 * @pre : the ID matches an existing table in DB
	 * @post : the fields are filled with the values form DB
	 */
	function load( $id = null )
	{
		$id = is_null($id) ? $this->id : $id;
		
		// get the values in DB and return boolean
		$sql = "SELECT id, title, startDate, endDate, dueDate, "
			."description, priority, progress, visibility "
			."FROM `".$this->_tblNameList['cltask_tasks']."` "
			."WHERE id = ".(int)$id
			;
		
		if ( false === ( $taskFieldsInDB = claro_sql_query_get_single_row($sql) ) )
		{
			return false;
		}
		else
		{
			if ( is_array( $taskFieldsInDB ) && count( $taskFieldsInDB ) > 0 )
			{	
				$this->id = (int)$taskFieldsInDB['id'];
				$this->title = $taskFieldsInDB['title'];
				$this->startDate = $taskFieldsInDB['startDate'];
				$this->endDate = $taskFieldsInDB['endDate'];
				$this->dueDate = $taskFieldsInDB['dueDate'];
				$this->description = $taskFieldsInDB['description'];
				$this->priority = (int)$taskFieldsInDB['priority'];
                $this->progress = is_null($taskFieldsInDB['progress']) 
                    ? null 
                    : (int)$taskFieldsInDB['progress']
                    ;
                $this->visible = ( 'HIDE' == $taskFieldsInDB['visibility'] )
                    ? false
                    : true
                    ;
                    
                return true;
			}
			else
			{	
				return false;
			}
		}		
	}
	
	/**
	 * Delete a task that has been saved.
	 * 
	 * @return : 'true' if the method succeeded, 'false' if not
	 * @pre : the ID is valid
	 * @post : Values are deleted
	 */
	function delete($id = null)
	{
		$id = is_null($id) ? $this->id : $id;
		
		// delete the values in DB and return boolean
		$sql = "DELETE  "
			."FROM `".$this->_tblNameList['cltask_tasks']."` "
			."WHERE id = ".(int)$id
			;
		
		if ( false === ($rows = claro_sql_query_affected_rows($sql)) )
		{
			return false;
		}
		else
		{
			return ($rows === 1);
		}
	}
} // End of class Task


/**
 * Class TaskList
 * 
 * A TaskList is an object that can access and search all the registered tasks.
 */
class TaskList
{
	var $_tblNameList;  // array containing full names of the DB tables
    
    function TaskList()
    {
    	// construction of the names of the table in DB
        $this->_tblNameList = get_module_course_tbl( array("cltask_tasks")
            , claro_get_current_course_id());
    }
    
    /**
     * @param bool $maskInvisibleTasks False if all task should be loaded.
     * @return false False if loading failed, else
     *         array An (2D) array containing fields of all saved tasks.
     *         ('id' and 'priority' must be integers)
     *         ('progress' must be an integer or NULL if not setted)
     *         The array contains or not the visible tasks according 
     *         to the parameter.
     */
    function loadAll( $maskInvisibleTasks = false )
    {
    	$sql = "SELECT id, title, startDate, endDate, dueDate, "
            ."description, priority, progress, visibility "
            ."FROM `".$this->_tblNameList['cltask_tasks']."` "
            ;
        
        if ( $maskInvisibleTasks )
        {
        	$sql .= " WHERE visibility = 'SHOW'";
        }
            
        if ( false === ( $res = claro_sql_query_fetch_all_rows($sql) ) )
        {
            return false;
        }
        else
        { 
            foreach ( $res as $key => $task )
            {
                $res[$key]['id'] = (int) $task['id']; 
                $res[$key]['priority'] = (int) $task['priority'];
                $res[$key]['progress'] = is_null($task['progress']) 
                    ? null 
                    : (int) $task['progress']
                    ;   
                $res[$key]['visible'] = ( 'HIDE' == $res[$key]['visibility'] )
                    ? false
                    : true
                    ;
                unset( $res[$key]['visibility'] );
            }
            
            return $res;
        }        
    }
}

?>