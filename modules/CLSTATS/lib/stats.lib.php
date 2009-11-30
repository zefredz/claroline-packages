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
    
    public static function load( $startDate, $stopDate )
    {
        $table = get_module_main_tbl( array( 'stats' ) );
        
        $sql = "SELECT *
                FROM `{$table['stats']}`
                WHERE `dateCreation` >= '" . Claroline::getDatabase()->escape( $startDate ) . "'
                AND `dateCreation` <= '" . Claroline::getDatabase()->escape( $stopDate ) . "'
                ORDER BY `code_course` ASC, `dateCreation` DESC";
        $result = Claroline::getDatabase()->query( $sql );
        
        return $result;
    }
    
    public function execute( $reset = true )
    {
        //$dbCoursesPath = dirname( __FILE__ ) . '/../databases/courses_stats.sqlite';
        //$dbCourses = new SQLite3( $dbCoursesPath );
        
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
        
        
        //$dbCourses->close();
        
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

class Stats_Report
{
    private $table;
    
    public function __construct()
    {
        $this->table = get_module_main_tbl( array( 'stats_reports' ) );
    }
    
    public function loadFreshContent()
    {
        $result = Claroline::getDatabase()->query(
            "SELECT max(`date`) as `lastReportDate` FROM `{$this->table['stats_reports']}` WHERE 1;"
            );
        $lastReportDate = (int) $result->fetch(Database_ResultSet::FETCH_VALUE);
        
        $stats = ClaroStats::load($lastReportDate, time());
        
        $cleanStats = array();
        
        foreach( $stats as $stat )
        {
            if( !(isset( $report[ $stat[ 'code_course'] ][ $stat[ 'toolLabel' ] ][ $stat[ 'itemName' ] ][ 'date' ] )
               && $cleanStats[ $stat[ 'code_course' ] ][ $stat[ 'toolLabel' ] ][ $stat[ 'itemName' ] ][ 'date' ] > $stat[ 'dateCreation' ]  )
               )
            {
                $cleanStats[ $stat[ 'code_course' ] ][ $stat[ 'toolLabel' ] ][ $stat[ 'itemName' ] ][ 'date' ] = $stat[ 'dateCreation' ];
                $cleanStats[ $stat[ 'code_course' ] ][ $stat[ 'toolLabel' ] ][ $stat[ 'itemName' ] ][ 'value' ] = (int) $stat[ 'itemValue' ];                
            }
        }
        
        $report = array();
        
        foreach( $cleanStats as $codeCourse => $tools )
        {
            foreach( $tools as $toolLabel => $items )
            {
                foreach( $items as $itemName => $item )
                {
                    if( ! isset( $report[ $toolLabel ][ $itemName ][ 'lessFive'] ) )
                    {
                        $report[ $toolLabel ][ $itemName ][ 'lessFive' ] = 0;
                    }
                    if( ! isset( $report[ $toolLabel ][ $itemName ][ 'moreFive'] ) )
                    {
                        $report[ $toolLabel ][ $itemName ][ 'moreFive' ] = 0;
                    }
                    //Less than 5 items                
                    if( $item[ 'value' ] < 5 )
                    {
                        $report[ $toolLabel ][ $itemName ][ 'lessFive' ]++;
                        
                    }
                    else
                    {
                       $report[ $toolLabel ][ $itemName ][ 'moreFive' ]++;
                    }
                    if( isset( $report[ $toolLabel ][ $itemName ]['value'] ) )
                    {
                        $report[ $toolLabel ][ $itemName ]['value'] += $item[ 'value' ];
                    }
                    else
                    {
                        $report[ $toolLabel ][ $itemName ]['value'] = $item[ 'value' ];
                    }
                }
            }
        }
        
        foreach( $report as $toolLabel => $item )
        {
            foreach( $item as $itemName => $thisItem )
            {
                //Max
                if( isset( $report[ $toolLabel ][ $itemName ][ 'max' ] ) )
                {
                    if( $report[ $toolLabel ][ $itemName ][ 'max' ] < $thisItem[ 'value' ] )
                    {
                        $report[ $toolLabel ][ $itemName ][ 'max' ] = $thisItem[ 'value' ];
                    }
                }
                else
                {
                    $report[ $toolLabel ][ $itemName ][ 'max' ] = $thisItem[ 'value' ];
                }
                //Average
                $report[ $toolLabel ][ $itemName ][ 'average' ] = round( $report[ $toolLabel ][ $itemName ][ 'max' ] / count( $cleanStats ) );
            }
            
        }
        
        
        unset( $stats );
        unset( $cleanStats );
        
        return $report;
    }
    
    public function load( $date )
    {
        $date = (int) $date;
        
        $result = Claroline::getDatabase()->query(
            "SELECT *
            FROM `{$this->table['stats_reports']}`
            WHERE `date` = " . $date . ";"
        );
        
        return $result;
    }
    
    public function save( $content )
    {
        if( is_array( $content['content'] ) && count( $content['content']) )
        {
            if( isset( $content['date'] ) )
            {
                //Update
            }
            else
            {
                //Insert
                $date = time();
                
                $sql = "";
                
                foreach( $content['content'] as $toolLabel => $items )
                {
                    foreach( $items as $itemName => $item )
                    {
                        if( $sql )
                        {
                            $sql .= ",";
                        }
                        
                        $sql .= "(" . (int) Claroline::getDatabase()->escape( $date ) . ",
                                '" . Claroline::getDatabase()->escape( $toolLabel ) . "',
                                '" . Claroline::getDatabase()->escape( $itemName ) . "',
                                " . (int) Claroline::getDatabase()->escape( $item['max'] ) . ",
                                " . (int) Claroline::getDatabase()->escape( $item['average'] ) . ",
                                " . (int) Claroline::getDatabase()->escape( $item['lessFive'] ) . ",
                                " . (int) Claroline::getDatabase()->escape( $item['moreFive'] ) . ")";
                    }
                }
                
                $sql = "INSERT INTO `{$this->table['stats_reports']}`
                ( `date`, `toolLabel`, `itemName`, `max`, `average`, `lessFive`, `moreFive` )            
                VALUES " . $sql;
                
                return Claroline::getDatabase()->exec( $sql );
            }
        }
        else
        {
            return false;
        }
    }
}

class Stats_ReportList
{
    public static function countReports()
    {
        $table = get_module_main_tbl( array( 'stats_reports' ) );
        
        $result = Claroline::getDatabase()->query(
            "SELECT DISTINCT `date`
            FROM `{$table['stats_reports']}`
            ORDER BY `date` DESC;"
        );
        
        return $result;
    }
}