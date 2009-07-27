<?php // $Id$

/**
 * Upgrade lib
 *
 * @version     1.9$Revision$
 * @copyright   2001-2009 Universite catholique de Louvain (UCL)
 * @author      Frederic Minne <zefredz@claroline.net> Revision by Sokay Benjamin
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE version 2 or later
 * @package     UPGTO19
 */

class UpgradeException extends Exception {};

class Claroline_File_Log
{
    const SUCCESS = 'SUCCESS';
    const ERROR = 'ERROR';
    const INFO = 'INFO';
    const WARNING = 'WARNING';
    
    protected $file;
    
    public function __construct( $file )
    {
        $this->file = $file;
        
        if ( ! file_exists( $this->file ) )
        {
            touch( $this->file );
            chmod( $this->file, CLARO_FILE_PERMISSIONS );
        }
    }
    
    public function timestamp()
    {
        return date('Y-m-d H:i:s');
    }
    
    public function mark( $message = 'MARK' )
    {
        $this->write( ">>> **** {$message} **** <<<" );
    }
    
    public function log( $type, $message )
    {
        $this->write( "[".$this->timestamp()."] {$type} : {$message}" );
    }
    
    public function write( $message )
    {
        file_put_contents( $this->file, "{$message}\n", FILE_APPEND );
    }
    
    public function info( $message )
    {
        $this->log( self::INFO, $message );
    }
    
    public function error( $message )
    {
        $this->log( self::ERROR, $message );
    }
    
    public function success( $message )
    {
        $this->log( self::SUCCESS, $message );
    }
}

class Upgrade_CourseLog extends Claroline_File_Log
{
    public function __construct()
    {
        parent::__construct( get_path('rootSys') . 'platform/upgto19.course.log' );
    }
    
    public static function format( $step, $name, $courseId )
    {
        return "at step {$step} : {$name} in [{$courseId}]\n";
    }
    
    protected static $instance = false;
    
    public static function getLog()
    {
        if ( ! self::$instance )
        {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
}

class Upgrade_MainLog extends Claroline_File_Log
{
    public function __construct()
    {
        parent::__construct( get_path('rootSys') . 'platform/upgto19.main.log' );
    }
    
    public static function format( $step, $name )
    {
        return "at step {$step} : {$name}\n";
    }
    
    protected static $instance = false;
    
    public static function getLog()
    {
        if ( ! self::$instance )
        {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
}


class Upgrade_CourseDatabase
{
    
    
    public static function init( $reset = false )
    {
        $table = get_module_main_tbl( array('courses_to_upgrade','cours') );
        
        Claroline::getDatabase()->exec("
            CREATE TABLE IF NOT EXISTS `{$table['courses_to_upgrade']}` (
                code VARCHAR(12) NOT NULL,
                dbName VARCHAR(32) NOT NULL,
                step INT,
                status ENUM ('pending','success','failure','started','partial') DEFAULT 'pending',
                stepFailed varchar(255),
                PRIMARY KEY (code),
                KEY (status)
            );
        ");
        
        if( true === $reset )
        {
            Claroline::getDatabase()->exec("
                TRUNCATE TABLE `{$table['courses_to_upgrade']}`;
            ");
        }
        
        
        // populate database
        if ( ! count( Claroline::getDatabase()->query("SELECT code FROM `{$table['courses_to_upgrade']}`") ) )
        {
            Claroline::getDatabase()->exec("
                INSERT INTO `{$table['courses_to_upgrade']}`
                (code, dbName)
                SELECT c.code, c.dbName FROM `{$table['cours']}` AS c;
            ");
        }
    }
    
    public static function getCourse( $cid )
    {
        $table = get_module_main_tbl( array('courses_to_upgrade','cours') );
        
        $result = Claroline::getDatabase()->query("
            SELECT code, dbName, status, step, stepFailed
            FROM `{$table['courses_to_upgrade']}`
            WHERE code = ".Claroline::getDatabase()->quote($cid).";
        ");
        
        return $result->fetch();
    }
    
    public static function getCoursesByStatus( $status )
    {
        $table = get_module_main_tbl( array('courses_to_upgrade','cours') );
        
        $result = Claroline::getDatabase()->query("
            SELECT code, dbName, status, step, stepFailed
            FROM `{$table['courses_to_upgrade']}`
            WHERE status = ".Claroline::getDatabase()->quote($status).";
        ");
        
        return $result;
    }
    
    public static function countCoursesByStatus( $status )
    {
        $table = get_module_main_tbl( array('courses_to_upgrade','cours') );
        
        $result = Claroline::getDatabase()->query("
            SELECT COUNT(*) AS cnt
            FROM `{$table['courses_to_upgrade']}`
            WHERE status = ".Claroline::getDatabase()->quote($status).";
        ");
        
        return $result->fetch(Database_ResultSet::FETCH_VALUE);
    }
    
    public static function countCourses()
    {
        $table = get_module_main_tbl( array('courses_to_upgrade','cours') );
        
        $result = Claroline::getDatabase()->query("
            SELECT COUNT(*) AS cnt
            FROM `{$table['courses_to_upgrade']}`
        ");
        
        return $result->fetch(Database_ResultSet::FETCH_VALUE);
    }
    
    public static function upgradeStarted( $cid )
    {
        $table = get_module_main_tbl( array('courses_to_upgrade','cours') );
        
        Claroline::getDatabase()->exec("
            UPDATE `{$table['courses_to_upgrade']}`
            SET status = 'started'
            WHERE code = ".Claroline::getDatabase()->quote($cid).";
        ");
    }
    
    public static function updateStep ( $cid, $step )
    {
        $table = get_module_main_tbl( array('courses_to_upgrade','cours') );
        
        Claroline::getDatabase()->exec("
            UPDATE `{$table['courses_to_upgrade']}`
            SET step = ".Claroline::getDatabase()->quote($step)."
            WHERE code = ".Claroline::getDatabase()->quote($cid).";
        ");
    }
    
    public static function upgradeSuccess( $cid )
    {
        $table = get_module_main_tbl( array('courses_to_upgrade','cours') );
        
        Claroline::getDatabase()->exec("
            UPDATE `{$table['courses_to_upgrade']}`
            SET status = 'success'
            WHERE code = " . Claroline::getDatabase()->quote($cid) .";
        ");
    }
    
    public static function upgradeFailure( $cid )
    {
        $table = get_module_main_tbl( array('courses_to_upgrade','cours') );
        
        Claroline::getDatabase()->exec("
            UPDATE `{$table['courses_to_upgrade']}`
            SET status = 'failure'
            WHERE code = ".Claroline::getDatabase()->quote($cid).";
        ");
    }
    
    public static function upgradePartial( $cid, $stepFailedArr )
    {
        $table = get_module_main_tbl( array('courses_to_upgrade','cours') );
        
        $stepFailed = implode(',', $stepFailedArr);
        
        Claroline::getDatabase()->exec("
            UPDATE `{$table['courses_to_upgrade']}`
            SET
                status = 'partial',
                stepFailed = " .Claroline::getDatabase()->quote($stepFailed) . "
            WHERE code = ".Claroline::getDatabase()->quote($cid).";
        ");
    }
}

class Upgrade_CourseIterator implements Iterator, Countable
{
    protected $offset, $limit, $courseCount, $iterationCount;
    protected $table;
    protected $current, $idx;
    
    public function __construct( $limit = 250 )
    {
        $this->offset = 0;
        $this->limit = $limit;
        
        if ( $this->limit == 0 )
        {
            throw new OutOfRangeException("Limit must be >= 0 !");
        }
        
        $this->table = get_module_main_tbl( array('courses_to_upgrade') );
        
        $this->courseCount = $this->countCourses();
        
        $this->iterationCount = $this->countIterations();
    }
    
    public function countCourses()
    {
        $result = Claroline::getDatabase()->query("
            SELECT COUNT(*)
            FROM `{$this->table['courses_to_upgrade']}`
            WHERE status = 'pending';
        ");
        
        return (int) $result->fetch(Database_ResultSet::FETCH_VALUE);
    }
    
    public function countIterations()
    {
        if ( $this->courseCount == 0 )
        {
            return 0;
        }
        
        if ( $this->limit == 0 )
        {
            throw new OutOfRangeException("Limit must be >= 0 !");
        }
        
        $quot = floor( $this->courseCount / $this->limit );
        $rem = $this->courseCount % $this->limit;
        
        if ( $rem == 0 )
        {
            return $quot;
        }
        else
        {
            return $quot + 1;
        }
    }
    
    public function getNextBunchOfCourses()
    {
        if ( $this->offset >= $this->courseCount )
        {
            return false;
        }
        
        $result = Claroline::getDatabase()->query("
            SELECT code, dbName, status, stepFailed
            FROM `{$this->table['courses_to_upgrade']}`
            WHERE status = 'pending'
            LIMIT ".Claroline::getDatabase()->escape($this->limit)."
            OFFSET ".Claroline::getDatabase()->escape($this->offset).";
        ");
        
        $this->offset += $this->limit;
        
        return $result;
    }
    
    public function count()
    {
        return $this->iterationCount;
    }
    
    // --- Iterator ---
    
    /**
     * Check if the current position in the result set is valid
     * @see     Iterator
     * @return  boolean
     */
    public function valid()
    {
        return ($this->courseCount > 0)
            && ($this->iterationCount > 0)
            && ($this->offset < $this->countCourses)
            && ($this->offset >= 0);
    }
    
    /**
     * Return the current row
     * @see     Iterator
     * @return  mixed, current row
     */
    public function current()
    {
        return $this->current;
    }
    
    /**
     * Advance to the next row in the result set
     * @see     Iterator
     */
    public function next()
    {
        $this->current == $this->getNextBunchOfCourses();
        $this->idx++;
    }
    
    /**
     * Rewind to the first row
     * @see     Iterator
     */
    public function rewind()
    {
        $this->offset = 0;
        $this->idx = 0;
    }
    
    /**
     * Return the index of the current row
     * @see     Iterator
     * @return  int
     */
    public function key()
    {
        return $this->idx;
    }
}

class Upgrade_Course
{
    public static function execute( $course )
    {
        $errorSteps = array();
        
        if ( $course['status'] == 'pending' )
        {
            $CourseUpgradeTasks = new Upgrade_TaskQueue();
            
            include_once get_module_path('UPGTO19') .'/tasks/course.tasks.php';
            
            $errorSteps = $CourseUpgradeTasks->execute( $course );
            
            unset( $CourseUpgradeTasks );
        }
        
        return $errorSteps;
    }
    
    public static function upgradeNextBunchOfCourses()
    {
        $executionResult = array(
            'partial' => array(),
            'failure' => array(),
            'success' => array()
        );
        
        $courseList = new Upgrade_CourseIterator();
        
        $CourseUpgradeTasks = new Upgrade_TaskQueue();
        
        include_once get_module_path('UPGTO19') .'/tasks/course.tasks.php';
        
        if ( $bunch = $courseList->getNextBunchOfCourses() )
        {
            foreach ( $bunch as $course )
            {
                $errorSteps = array();
                
                try
                {
                    if ( $course['status'] == 'pending' )
                    {
                        $errorSteps = $CourseUpgradeTasks->execute( $course );
                        
                        if ( count ( $errorSteps) )
                        {
                            $executionResult['partial'][$course['code']] = $errorSteps;
                        }
                        else
                        {
                            $executionResult['success'][$course['code']] = true;
                        }
                    }
                }
                catch ( Exception $e )
                {
                    $executionResult['failure'][$course['code']] = $e->getMessage();
                }
            }
        }
        
        unset( $CourseUpgradeTasks );
        
        return $executionResult;
    }
}
