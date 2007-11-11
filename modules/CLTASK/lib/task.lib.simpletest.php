<?php // $Id$

/**
 * CLAROLINE
 *
 * TEST Task library
 *
 * @version version 0.1 $Revision: 101 $
 * @copyright 2001 - 2007 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @author Tanguy Delooz <tdelooz@gmail.com>
 *
 * @package
 *
 */

if (!defined('GROUP_TEST')) define('GROUP_TEST',FALSE);

$testNeedACourse = true;

if (!GROUP_TEST)
{
	define( 'DEBUG_MODE', true );
	define( 'DEVEL_MODE', true );
	include('../init.claro.simpletest.php');
}

include_once(dirname(__FILE__) . '/task.lib.php');


/**
 * Class TaskLibTestCase 
 * 
 * Extends UnitTestCase
 * (Is parent of Task- and TaskListTestCase)
 * Contains methods, used by both children,
 * that mainly permit these children to share the same tables in DataBase.
 */
 class TaskLibTestCase extends UnitTestCase
 {
 	var $tblTasks; // contains the full name of the table in DB
    
    /**
     * Constructor
     * 
     * @param string A string containg the title of the Task.lib test case
     */
    function TaskLibTestCase( $TestCaseTitle )
    {        
        $this->UnitTestCase( $TestCaseTitle );
        // construction of the name of the table in DB
        $this->tblTasks = get_module_course_tbl( array("cltask_tasks")
            , claro_get_current_course_id());
    }
    
    
    // SETUP AND TEARDOWN
    
    function createTaskLibInDB()
    {
        $this->dropTaskLibInDB();
        
        // construction of the table before the test
        $sqlCreate = "CREATE TABLE IF NOT EXISTS `".$this->tblTasks['cltask_tasks']."` (
          `id` int(11) unsigned NOT NULL auto_increment,
          `title` varchar(255) NOT NULL,
          `startDate` datetime default NULL,
          `endDate` datetime default NULL,
          `dueDate` datetime default NULL,
          `description` text,
          `priority` tinyint(4) unsigned default NULL,
          `progress` tinyint(4) unsigned default NULL,
          `visibility` ENUM('SHOW', 'HIDE') DEFAULT 'SHOW' NOT NULL,
          PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM";
        claro_sql_query($sqlCreate);
    }
    
    function dropTaskLibInDB()
    {
    	// destruction of the table
        $sqlDrop ="DROP TABLE IF EXISTS `".$this->tblTasks['cltask_tasks']."`";
        claro_sql_query($sqlDrop);
    }
    
    // For now, both children share same setUp() and tearDown()
    function setUp()
    {
    	$this->createTaskLibInDB();
    }
    
    function tearDown()
    {
    	$this->dropTaskLibInDB();
    }
    
    
    // TEST FUNCTIONS (ASSERTIONS)
         
    function testTables()
    {
        // verifies the table is empty
        $sqlTest = "SELECT COUNT(*) AS nbr FROM `".$this->tblTasks['cltask_tasks']."`";
        $nbr = claro_sql_query_get_single_value($sqlTest);      
        $this->assertNotIdentical( false, $nbr );
        $this->assertIdentical( 0, (int)$nbr ); 
    }
        
    /**
     * @param : a and b are arrays. msg is a string.
     * @post : An assertion is made and passes if a and b have the same contents.
     */
    function assertArrayEqual( $a, $b, $msg = null )
    {
        $equal = true;
        
        if ( is_array( $a ) && is_array( $b ) )
        {
            if ( count( $a ) != count( $b ) )
            {
                $equal = false;
            }
            
            if ( count( $a ) < count( $b ) )
            {
            	$c = $a;
                $a = $b;
                $b = $c;
            }
            
            foreach ( $a as $key => $element )
            {
            	$this->assertTrue( array_key_exists($key, $b), "$key not found");
            	$this->assertIdentical( $element, $b[$key]
                    , "$key not identical: ".@var_export($element,true). " and " .@var_export($b[$key], true) );
                
                if ( ( is_null( $element ) && !is_null( $b[$key] ) )
                    || ( !is_null( $element ) && is_null( $b[$key] ) ) 
                    || $element !== $b[$key])
                {
                    $equal = false;
                }
            }
        }
        else
        {
            $equal = false;
        }
        
        if ( !empty( $msg ) )
        {
            $this->assertTrue( $equal, $msg );
        }
        else
        {
            $this->assertTrue( $equal );
        }
    }     
 }


// CHILDREN TESTCASES


/**
 * Class TaskTestCase tests Task
 * 
 * This unit test case tests the class Task
 * that is written in the file task.lib.php
 */
class TaskTestCase extends TaskLibTestCase
{    		
    /**
     * Constructor
     */
    function TaskTestCase()
    {
        $this->TaskLibTestCase( 'Test of class Task' );
    }
    
    
    // REFACTORING METHODS
    
    /**
     * A method returning an array with the properties of Task object
     * so these can be compared with the fields loaded from the Task in DB.
     * 
     * @param Task() A Task object
     * @return Array An array containing the Task properties
     */
    function taskFieldsToArray( $task )
    {
        return array(
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'startDate' => $task->getStartDate(),
            'endDate' => $task->getEndDate(),
            'dueDate' => $task->getDueDate(),
            'description' => $task->getDescription(),
            'priority' => $task->getPriority(),
            'progress' => $task->getProgress(),
            'visible' => $task->isVisible(),
        );
    }
    
    /**
     * A method loading the fields directly from database
     * so these can be compared with those loaded by the Task object.
     * 
     * @return Array An array containing the fields from db, converted to Task fields
     */
    function getTaskFieldArrayFromDB( $sql )
    {
        $taskFieldsFromDB = claro_sql_query_get_single_row($sql);
        $taskFieldsFromDB['id'] = (int) $taskFieldsFromDB['id'];
        $taskFieldsFromDB['priority'] = (int) $taskFieldsFromDB['priority'];
        $taskFieldsFromDB['progress'] = is_null($taskFieldsFromDB['progress']) 
            ? null 
            : (int) $taskFieldsFromDB['progress']
            ;
        $taskFieldsFromDB['visible'] = ( 'HIDE' == $taskFieldsFromDB['visibility'] ) 
            ? false 
            : true
            ;
        unset( $taskFieldsFromDB['visibility'] );
        
        return $taskFieldsFromDB;
    }   
      
    /**
     * Assertion method comparing fields of a Task object
     * with the fields in database for a specific ID.
     * 
     * @param Task A Task object
     * @param int The id of the task in database
     * @param String A message to show if the assertion fails
     */
    function assertTaskEqualInDB( $task, $id, $msg = null )
    {
    	$taskFields = $this->taskFieldsToArray( $task );
        
        $sql = "SELECT * FROM `" . $this->tblTasks['cltask_tasks']
            . "` WHERE id = " . $id
            ;
                       
        $taskFieldsFromDB = $this->getTaskFieldArrayFromDB( $sql );
                
        $this->assertArrayEqual( $taskFields, $taskFieldsFromDB, $msg );
    }
    
      
    // TEST METHODS
         
    /**
     * Test of Constructor and public properties
     */
    function testConstructorAndProperties()
    {
    	$task = &new Task();
    	
        // id
    	$this->assertIdentical($task->getId(), 0);
    	$task->setId('1');
    	$this->assertNotIdentical($task->getId(), '1', 'String must be converted to int');
    	$this->assertIdentical($task->getId(), 1, 'String must be converted to int');
    	
        // title
    	$this->assertIdentical($task->getTitle(),'');
    	$task->setTitle('title');
    	$this->assertIdentical($task->getTitle(),'title');
    	
        // dates
    	$this->assertIdentical($task->getStartDate(), '0000-00-00 00:00:00');
    	$task->setStartDate('2007-03-10 12:00:00');
    	$this->assertIdentical($task->getStartDate(),'2007-03-10 12:00:00');
    	
    	$this->assertIdentical($task->getDueDate(), '0000-00-00 00:00:00');
    	$task->setDueDate('2007-03-10 12:00:00');
    	$this->assertIdentical($task->getDueDate(), '2007-03-10 12:00:00');
    	
    	$this->assertIdentical($task->getEndDate(), '0000-00-00 00:00:00');
    	$task->setEndDate('2007-03-10 12:00:00');
    	$this->assertIdentical($task->getEndDate(), '2007-03-10 12:00:00');
    	
        // description
    	$this->assertIdentical($task->getDescription(), '');
    	$task->setDescription('description');
    	$this->assertIdentical($task->getDescription(), 'description');
    	
        // priority
    	$this->assertIdentical($task->getPriority(), 0);
    	$task->setPriority('1');
    	$this->assertNotIdentical($task->getPriority(), '1', 'String must be converted to int');
    	$this->assertIdentical($task->getPriority(), 1, 'String must be converted to int');
    	
    	//$this->assertFalse( $task->setPriority( -2), 'priority must be positive (>=0)' );
    	//$this->assertFalse( $task->setPriority( TASK_MAX_PRIORITY + 1 ), 'Do not exceed MAX_PRIORITY' );
        $task->setPriority( -2);
        $this->assertEqual( 0, $task->getPriority() 
            , 'if a nŽgative parameter is passed to setPriority(), the property must be set to 0' );
        
        $task->setPriority( TASK_MAX_PRIORITY + 1 );
        $this->assertEqual( TASK_MAX_PRIORITY, $task->getPriority() 
            , 'if the parameter passed to setPriority() exceeds TASK_MAX_PRIORITY, the property must be set to TASK_MAX_PRIORITY' );
    	
        $task->setPriority( 0 );
        $this->assertEqual( 0, $task->getPriority() 
            , 'if 0 is passed to setPriority(), the property must be set to 0' );
        
        $task->setPriority( TASK_MAX_PRIORITY );
        $this->assertEqual( TASK_MAX_PRIORITY, $task->getPriority() 
            , 'if TASK_MAX_PRIORITY is passed to setPriority(), the property must be set to TASK_MAX_PRIORITY' );
            
        $this->assertEqual( 3, TASK_MAX_PRIORITY, 'TASK_MAX_PRIORITY has changed !!!!' );
        
        // progress
        $this->assertIdentical( $task->getProgress(), NULL );
        $task->setProgress( '1' );
        $this->assertNotIdentical( $task->getProgress(), '1', 'String must be converted to int' );
        $this->assertIdentical( $task->getProgress(), 1, 'String must be converted to int' );
        $task->setProgress( null );
        $this->assertIdentical( $task->getProgress(), NULL, 'Progress must be able to be setted with NULL' );     
        $task->setProgress( '-1' );
        $this->assertIdentical( $task->getProgress(), 0, 'Minimum value of Progress must be 0' );
        $task->setProgress( '101' );
        $this->assertIdentical( $task->getProgress(), 100, 'Maximum value of Progress must be 100' );  
        
        // visibility
        $this->assertTrue( $task->isVisible() );
        $this->assertFalse( $task->isInvisible() );
        $task->setInvisible();
        $this->assertFalse( $task->isVisible() );
        $this->assertTrue( $task->isInvisible() );
        $task->setVisible();
        $this->assertTrue( $task->isVisible() );
        $this->assertFalse( $task->isInvisible() );       
    }
      
    /*
    function testGetData() { }
    function testSetData() { }    
    function testCreate() { }
	function testModify() { }
    */
    
    function testSave()
    {	
    	// TEST CREATION WITH INVALID TITLE
    	
    	$task = &new Task();
    	$res = $task->save();
    	
    	$this->assertFalse( $res, 'The save method must fail with no title' );
    	
    	
    	// TEST CREATION
    	
    	$task = &new Task();
    	$task->setTitle('title');
    	$res = $task->save();
    	$this->assertTrue( $res, 'The save method must succeed' );
    	    	
        
    	// TEST CREATION AND DATA SAVINGS
    	
    	$this->assertTaskEqualInDB( $task, $task->getId()
            , 'Init values weren\'t saved in DB' );
        
			
		// TEST UPDATE WITH INVALID TITLE
		
		$task->setTitle('');
		$res = $task->save();
		// It is necessary to check that save() still returns 'false'
		// when the task's got an empty title and existing ID in the DB.
		$this->assertFalse( $res , 'must not save with an empty title');
		
		
		// TEST FIELD MODIFICATIONS OF AN UPDATE (in save())
		
		// set new values in task
		$task->setTitle('newTitle');
		$task->setStartDate(date('Y-m-d H:i:s'));  
		$task->setEndDate(date('Y-m-d H:i:s')); 
		$task->setDueDate(date('Y-m-d H:i:s')); 
		$task->setDescription('newDescr');
		$task->setPriority(4);
        $task->setProgress(80);
        $task->setInvisible();
		
		// save them in DB
		$res = $task->save();
		$this->assertTrue( $res , 'update must succeed');
				
		// comparing values in task with those in DB
		$this->assertTaskEqualInDB( $task, $task->getId()
            , 'New values weren\'t updated in DB' );
        
        // test back to visible        
        $task->setVisible();
        $task->save();
        $this->assertTaskEqualInDB( $task, $task->getId()
            , 'Task must be visible again' );
        	
		
		// TEST SAVE() WITH INVALID ID

		// set invalid ID
		$task->setId( 2695 );
		$res = $task->save(); 
		$this->assertFalse( $res, 'Must not save a task with invalid ID' );		
    }
    
    function testLoad()
    {
    	// TEST TASK LOADING FROM DB
    	
    	// insert a task in db knowing the ID will be 1
    	        
        
        // TEST LOAD() AND VERIFY INITIAL VALUES
        $taskSaver = &new Task();
        $taskSaver->setTitle('init');
        $taskSaver->save();
         
		$taskLoader = &new Task();  // must be a new object !!
        $res = $taskLoader->load( 1 );
        
        $this->assertTrue( $res, 'Loading task with initial values must succeed' );
        
        $taskInitValues = array(
            'id' => 1,
            'title' => 'init',
            'startDate' => '0000-00-00 00:00:00',
            'endDate' => '0000-00-00 00:00:00',
            'dueDate' => '0000-00-00 00:00:00',
            'description' => '',
            'priority' => 0,
            'progress' => null,
            'visible' => true
        );
        
        $taskFields = $this->taskFieldsToArray( $taskLoader );
        
        $this->assertArrayEqual( $taskInitValues, $taskFields,
            'Init values must not be changed when passed through database');
        
        
        // TEST LOAD() WITH  VALUES
		$taskSaver->setTitle('newTitle');
		$taskSaver->setStartDate(date('Y-m-d H:i:s'));  
		$taskSaver->setEndDate(date('Y-m-d H:i:s')); 
		$taskSaver->setDueDate(date('Y-m-d H:i:s')); 
		$taskSaver->setDescription('newDescr');
		$taskSaver->setPriority(1);
        $taskSaver->setProgress(80);
    	$taskSaver->setInvisible();
        
    	$taskSaver->save();
        
    	// test load()
    	$res = $taskLoader->load(1);	
    	$this->assertTrue( $res, 'load(1) must succeed' );
    	
    	// get back task values from db for ID = 1 and
        // compare values from db with those freshly loaded
    	$this->assertTaskEqualInDB( $taskLoader, 1
            , 'Task values weren\'t loaded from DB' );
        
     
        // TEST LOAD() WITH 'progress' = NULL in database
        
        $taskSaver->setProgress(NULL);
        $taskSaver->save(); // this should be an update
        
        $res = $taskLoader->load(1);
        
        // get back task values from db for ID = 1 and
        // compare values from db with those freshly loaded
        $this->assertTaskEqualInDB( $taskLoader, 1
            , 'Task progress should be NULL' );
        
 
	    // TEST LOAD() WITH INVALID ID
	    
	    $res = $taskLoader->load(0);
	    $this->assertFalse( $res, 'load(0) must not succeed' );	    
    }
    
    
    function testDelete()
    {
    	// TEST DELETING A TASK FROM DB
    	
    	// insert a task in db knowing the ID will be 1
    	$task = &new Task();
		
		$task->setTitle('newTitle');
		$task->setStartDate(date('Y-m-d H:i:s'));  
		$task->setEndDate(date('Y-m-d H:i:s')); 
		$task->setDueDate(date('Y-m-d H:i:s')); 
		$task->setDescription('newDescr');
		$task->setPriority(1);
        $task->setInvisible();
    	
    	$task->save();
    	
    	// test delete(), then verify values are deleted in db
    	$task = &new Task();
    	$res = $task->delete(1);	// first task at ID = 1
    	$this->assertTrue( $res , 'delete(1) must succeed');
    	
    	// get back task values from db for ID = 1
    	$sql = "SELECT COUNT(*) FROM `".$this->tblTasks['cltask_tasks']
    		. "` WHERE id = 1";
    		
    	$nbr = claro_sql_query_get_single_value($sql);
    	$this->assertIdentical('0', $nbr);
    	
        // test delete() for the already deleted task
        $res = $task->delete(1);
        $this->assertFalse( $res, 'task for ID=1 was already deleted');
        
        // test delete() for ID = 0
        $res = $task->delete(0);
        $this->assertFalse( $res, 'cannot delete task for ID=0');
    }
} // End of class TaskTestCase


/**
 * Class TaskTestListCase tests TaskList
 * 
 * This unit test case tests the class TaskList
 * that is written in the file task.lib.php
 */
class TaskListTestCase extends TaskLibTestCase
{   
    /**
     * Constructor
     */    
    function TaskListTestCase()
    {  
        $this->TaskLibTestCase( 'Test of class TaskList' );
    }

    // REFACTORING METHODS
    
    /**
     * @param Task A task object that is to be returned by loadAll()
     * @param Array An array, containing the fields of a Task,
     *        that is an element of the array returned by loadAll()
     */
    function assertTaskIndenticalInDB( $task, $taskListRow )
    {
        $this->assertIdentical( $taskListRow['id'], $task->getId() );        
        $this->assertIdentical( $taskListRow['title'], $task->getTitle() );       
        $this->assertIdentical( $taskListRow['startDate'], $task->getStartDate() );        
        $this->assertIdentical( $taskListRow['endDate'], $task->getEndDate() );        
        $this->assertIdentical( $taskListRow['dueDate'], $task->getDueDate() );        
        $this->assertIdentical( $taskListRow['description'], $task->getDescription() );       
        $this->assertIdentical( $taskListRow['priority'], $task->getPriority() );      
        $this->assertIdentical( $taskListRow['progress'], $task->getProgress() );           
        $this->assertIdentical( $taskListRow['visible'], $task->isVisible() );
    }

    // TEST METHODS

    /**
     * Test of Constructor and public properties
     */
    function testConstructorAndProperties()
    {
    	$taskList = &new TaskList();    
    }
    
    /*function setUp()
    {
    	parent::setUp();
    }
    
    function tearDown()
    {
    	
    }*/    
    
    function testLoadAll()
    {
        // TEST LOADALL()
        
        $taskList = &new TaskList();    
        $list = $taskList->loadAll();
        
        $this->assertTrue( is_array($list), 'Task list must be an array' );
        
        $this->assertTrue( ( is_array($list) && count($list) == 0 ), 
            'No tasks in DB. Must return empty array.' );
        
        
        // TEST ALL TASKS ARE LOADED FROM DB
        
        // put 2 tasks in db    // This should be done manually with SQL queries !!
        $task1 = &new Task();   // This test should not depend on the Task class !!
        $task1->setTitle('tache visible');       // Another test should be written to test
        $task1->setStartDate(date('Y-m-d H:i:01'));  // compatibility between both.
        $task1->setEndDate(date('Y-m-d H:i:01')); 
        $task1->setDueDate(date('Y-m-d H:i:01')); 
        $task1->setDescription('description1');
        $task1->setPriority(1); 
        $task1->setVisible();
        $task1->save();
        
        $task2 = &new Task();
        $task2->setTitle('tache invisible');  
        $task2->setStartDate(date('Y-m-d H:i:02'));  
        $task2->setEndDate(date('Y-m-d H:i:02')); 
        $task2->setDueDate(date('Y-m-d H:i:02')); 
        $task2->setDescription('description2');
        $task2->setPriority(2);  
        $task2->setInvisible();     
        $task2->save();
        
        
        // load the tasks with taskList
        $list = $taskList->loadAll();
        
        $this->assertTrue(is_array($list), 'Task list must be an array');
        
        $this->assertTrue((is_array($list) && count($list) == 2), 
            'Two tasks in DB. Must return an array with two rows.');
        
        $this->assertTaskIndenticalInDB( $task1, $list[0] );
        $this->assertTaskIndenticalInDB( $task2, $list[1] );
        
        /*
        $this->assertIdentical( $list[0]['id'], $task1->getId() );
        $this->assertIdentical( $list[1]['id'], $task2->getId() );
        $this->assertIdentical( $list[0]['title'], $task1->getTitle() );
        $this->assertIdentical( $list[1]['title'], $task2->getTitle() );
        $this->assertIdentical( $list[0]['startDate'], $task1->getStartDate() );
        $this->assertIdentical( $list[1]['startDate'], $task2->getStartDate() );
        $this->assertIdentical( $list[0]['endDate'], $task1->getEndDate() );
        $this->assertIdentical( $list[1]['endDate'], $task2->getEndDate() );
        $this->assertIdentical( $list[0]['dueDate'], $task1->getDueDate() );
        $this->assertIdentical( $list[1]['dueDate'], $task2->getDueDate() );
        $this->assertIdentical( $list[0]['description'], $task1->getDescription() );
        $this->assertIdentical( $list[1]['description'], $task2->getDescription() );
        $this->assertIdentical( $list[0]['priority'], $task1->getPriority() );
        $this->assertIdentical( $list[1]['priority'], $task2->getPriority() );
        $this->assertIdentical( $list[0]['progress'], $task1->getProgress() );
        $this->assertIdentical( $list[1]['progress'], $task2->getProgress() );          
        $this->assertIdentical( $list[0]['visible'], $task1->isVisible() );
        $this->assertIdentical( $list[1]['visible'], $task2->isVisible() );   
        */
        
        
        // TEST $maskInvisibleTasks PARAMETER
        
        // load the ONLY the VISIBLE tasks with taskList
        $list = $taskList->loadAll( true );
        
        $this->assertTrue(is_array($list), 'Task list must be an array');
        
        $this->assertTrue((is_array($list) && count($list) == 1), 
            'Two tasks in DB, but one visible. Must return an array with one row.');
            
        $this->assertTaskIndenticalInDB( $task1, $list[0] );
        
        
        // load the VISIBLE AND INVISIBLE tasks with taskList
        $list = $taskList->loadAll( false );
        
        $this->assertTrue(is_array($list), 'Task list must be an array');
        
        $this->assertTrue((is_array($list) && count($list) == 2), 
            'Two tasks in DB, but one visible. Must return an array with one row.');
            
        $this->assertTaskIndenticalInDB( $task1, $list[0] );
        $this->assertTaskIndenticalInDB( $task2, $list[1] ); 
        
    }
    
    function testLoadNotEndedTest()
    {       
        $taskList = &new TaskList();
        // TEST $maskEndedTasks PARAMETER
                
        // need 5 tasks in db : 3 different 'end dates'
        // 1) no 'end date'
        // 2) an 'end date' that is not exceeded yet
        // 3) an 'end date' that is exceeded
        // 4) no 'end date' but progress at 100
        // 5) an 'end date' that is not exceeded yet but progress at 100
        
        $timestamp_yesterday = time() - 3600 * 24;
        $timestamp_today = time();
        $timestamp_tomorrow = time() + 3600 * 24;
        
        $endDate1 = '0000-00-00 00:00:00';
        $endDate2 = date( 'Y-m-d H:i:s', $timestamp_yesterday );
        $endDate3 = date( 'Y-m-d H:i:s', $timestamp_tomorrow );
        $endDate4 = '0000-00-00 00:00:00';
        $endDate5 = date( 'Y-m-d H:i:s', $timestamp_tomorrow );
        
        $task1 =& new Task();
        $task2 =& new Task();
        $task3 =& new Task();
        $task4 =& new Task();
        $task5 =& new Task();
        
        $task1->setTitle( 'empty' );
        $task2->setTitle( 'yesterday' );
        $task3->setTitle( 'tomorrow' );
        $task4->setTitle( 'noEndDate but 100%' );
        $task5->setTitle( 'EndDateTomorrow but 100%' );
        
        $task1->setEndDate( $endDate1 );
        $task2->setEndDate( $endDate2 );
        $task3->setEndDate( $endDate3 );
        $task4->setEndDate( $endDate4 );
        $task5->setEndDate( $endDate5 );
        
        $task4->setProgress( 100 );
        $task5->setProgress( 100 );
        
        $task1->save();
        $task2->save();
        $task3->save();
        $task4->save();
        $task5->save();
        
        // loadAll(false, false) : show invisible and show ended tasks
        $list = $taskList->loadAll( false, false );  
             
        $this->assertTrue(is_array($list), 'Task list must be an array');       
        $this->assertTrue((is_array($list) && count($list) == 5), 
            'Must return an array with 5 rows.');
            
        $this->assertTaskIndenticalInDB( $task1, $list[0] );
        $this->assertTaskIndenticalInDB( $task2, $list[1] ); 
        $this->assertTaskIndenticalInDB( $task3, $list[2] ); 
        $this->assertTaskIndenticalInDB( $task4, $list[3] ); 
        $this->assertTaskIndenticalInDB( $task5, $list[4] );   
        
        // loadAll(false, true) : show invisible but hide ended tasks
        $list = $taskList->loadAll( false, true );
        
        $this->assertTrue(is_array($list), 'Task list must be an array');
        $this->assertTrue((is_array($list) && count($list) == 2), 
            'Must return an array with 2 rows.');
            
        $this->assertTaskIndenticalInDB( $task1, $list[0] );
        $this->assertTaskIndenticalInDB( $task3, $list[1] );             
    }
     
} // End of class TaskListTestCase


// A TEST SHOULD BE WRITTEN TO TEST COMPATIBILITY BETWEEN TASK AND TASKLIST CLASSES
// AS BOTH USE THE SAME TABLE(S) IN DATABASE.
// $thisTask->load($id) and $thisTaskList->loadAll() should have matching results !! 

// The classic debug commands :
//$this->dump(__LINE__);
//$this->dump(__FILE__);  // __CLASS__, __FUNCTION__
//$this->dump($taskFields);
//$this->dump(var_export($taskFieldsFromDB,true));
//$this->dump(date('Y-m-d H:i:s'));
//$this->dump($sql);
//$this->dump(claro_sql_error());


if (!GROUP_TEST)
{
	$group = new GroupTest('Test of task.lib');
    $group->addTestCase(new TaskTestCase());
    $group->addTestCase(new TaskListTestCase());
    $group->run(new htmlReporter());
}
else
{
    $test->addTestCase(new TaskTestCase());
    $test->addTestCase(new TaskListTestCase());
}

?>