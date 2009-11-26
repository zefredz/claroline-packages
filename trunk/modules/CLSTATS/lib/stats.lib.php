<?php

abstract class ClaroCourseTask
{
    public function before( $course )
    {
        return true;
    }
    
    public function after( $course )
    {
        return true;
    }
    
    public function around( $course )
    {
        return true;
    }
    
    abstract public function run( $course );
    
    // utilities
    
    public function getCourseTables( $arrayTblNames, $course )
    {
        foreach ( $arrayTblNames as $key => $value )
        {
            $arrayTblNames[ $value ] = claro_get_course_db_name( $course ).get_conf('dbGlu').get_conf('courseTablePrefix').$value;
        }
        
        return $arrayTblNames;
    }
}

abstract class ClaroStats_CourseTask extends ClaroCourseTask
{
    abstract public function getData($course);
    
    abstract public function getLabel();
    
    public function run( $course )
    {
        return array(
            'course' => $course['code'],
            'label' => $this->getLabel(),
            'data' => $this->getData($course),
            'date' => date('Y-m-d H:i:s')
        );
    }
}

/*class ClaroStats_TaskQueue extends ClaroStats_Task
{
    protected $tasks = array();
    
    public function queue( Upgrade_Task $task )
    {
        array_unshift( $this->tasks, $task );
    }
    
    public function dequeue()
    {
        if ( count( $this->tasks ) > 0 )
        {
            return array_pop( $this->tasks );
        }
        else
        {
            return null;
        }
    }
    
    public function run( $course )
    {
        while ( $task = $this->dequeue() )
        {
            if ( $task->before( $course ) && $task->around( $course ) )
            {
                $task->run( $course );
                
                $task->around( $course ) && $task->after( $course );
            }
        }
    }
}*/

class ClaroStats
{
    public function before()
    {
        
    }
    
    public function beforeCourse( $course )
    {
        
    }
    
    public function afterCourse( $course )
    {
        
    }
    
    public function after()
    {
        
    }
    
    public function add( $data )
    {
        $table = get_module_main_tbl( array( 'stats' ) );
        
        if( is_array( $data ) && count( $data ) )
        {
            foreach( $data as $code_course => $tools )
            {
                foreach( $tools as $toolLabel => $values )
                {
                    
                    foreach( $values as $key => $value )
                    {
                        $sql = "INSERT INTO `{$table['stats']}`
                                (`code_course`, `toolLabel`, `itemName`, `itemValue`, `dateCreation`)
                                VALUES
                                ('" . Claroline::getDatabase()->escape( $code_course ) . "',
                                '" . Claroline::getDatabase()->escape( $toolLabel ) . "',
                                '" . Claroline::getDatabase()->escape( $key ) . "',
                                '" . Claroline::getDatabase()->escape( $value ) . "',
                                '" . time() . "');
                                ";
                        Claroline::getDatabase()->exec( $sql );
                    }
                }
            }
        }
        
        return true;
        
        //echo "Stats added" . '<br />';
    }
    
    public function execute( $reset = true )
    {
        $dbCoursesPath = dirname( __FILE__ ) . '/../databases/courses_stats.sqlite';
        $dbCourses = new SQLite3( $dbCoursesPath );
        
        //populate sqlite database
        Stats_CourseList::init( $reset );
        
        //get course list
        $limit = 4;
        $courseList = new CourseList_Iterator( $limit );
        
        $nbCourses = $courseList->countCourses();
        
        $iterations = $courseList->countIterations();
        
        $plugins = $this->getPlugins();
        
        $data = array();
        foreach( $courseList as $nextBunchofCourses )
        {
            
            foreach( $nextBunchofCourses as $course )            
            {
                foreach( $plugins as $toolLabel => $plugin )
                {
                    require_once( $plugin );
                    
                    $class = $toolLabel . '_Stats';
                    $toolStats = new $class;
                    
                    $data[ $course['code_course'] ][ $toolLabel ] = $toolStats->getData( $course['code_course'] );                    
                }
                
                $courseList->updateCourseStatus( $course['code_course'], 'done' );
            }
        }
        $addedData = $this->add( $data );
        
        
        $dbCourses->close();
        
        return $addedData;
    }
    
    private function getPlugins()
    {
        $dir = dirname( __FILE__ ) . '/../plugins/';
        
        $files = new DirectoryIterator( $dir );
        $plugins = array();
        
        foreach( $files as $file )
        {
            if( !( $file->isDot() && $file->isDir() ) )
            {
                $toolLabel = substr( $file->getBasename(), 0, strpos( $file->getBasename(), '.' ) );
                require_once( $file->getPathname() );
                
                if( class_exists( $toolLabel . '_Stats' ) )
                {
                    $plugins[ $toolLabel ] = $file->getPathname();
                }
                
            }
        }
        
        return $plugins;
    }
}

class Stats_CourseList
{
    public static function init( $reset = true )
    {
        $table = get_module_main_tbl( array('courses_stats','cours') );
        
        //Todo, manage delete or not if all done.
        
        //clean course table
        if( $reset === true )
        {
            Claroline::getDatabase()->exec( "TRUNCATE TABLE  `{$table['courses_stats']}`;" );            
        
            //populate courses_stats
            
            $sql = "INSERT INTO `{$table['courses_stats']}`
                    (code_course, code_display, dbName, folderName )
                    SELECT c.code, c.administrativeNumber, c.dbName, c.`directory` FROM `{$table['cours']}` AS c";
            
            Claroline::getDatabase()->exec( $sql );
        }
        
    }
    
    public static function countCourses( $status = null )
    {
        $table = get_module_main_tbl( array('courses_stats') );
        
        $result = Claroline::getDatabase()->query(
            "SELECT COUNT(*)
            FROM `{$table['courses_stats']}`
            WHERE status = '" . Claroline::getDatabase()->escape( $status ) . "';"
        );
        
        return (int) $result->fetch(Database_ResultSet::FETCH_VALUE);
    }
    
    public static function countPendingCourses()
    {
        return self::countCourses('pending');
    }
    
    public static function countScannedCourses()
    {
        return self::countCourses('done');
    }
}