<?php

class Upgrade_TaskException extends Exception{};

class Upgrade_TaskConstants
{
    const ON_ERROR_INTERRUPT = 'ON_ERROR_INTERRUPT';
    const ON_ERROR_CONTINUE = 'ON_ERROR_CONTINUE';
}

interface Upgrade_Task
{
    public function execute();
    public function onError();
    public function getMessage();
    public function setCourse( $course );
    public function getCourse();
}

abstract class Upgrade_Task_Abstract implements Upgrade_Task
{
    protected $onError, $message;
    protected $course = null ;
    
    public function __construct( $message, $onError )
    {
        $this->message = $message;
        $this->onError = $onError;
    }
    
    public function onError()
    {
        return $this->onError;
    }
    
    public function getMessage()
    {
        return $this->message;
    }
    
    public function setCourse( $course )
    {
        $this->course = $course;
    }
    
    public function getCourse()
    {
        return $this->course;
    }
}

class Upgrade_Task_Sql extends Upgrade_Task_Abstract
{
    protected $sql;
    
    public function __construct( $sql, $message = '', $onError = Upgrade_TaskConstants::ON_ERROR_CONTINUE )
    {
        parent::__construct( $message, $onError );
        $this->sql = $sql;
    }
    
    public function execute()
    {
        return Claroline::getDatabase()->exec( $this->contextualizeQuery( $this->sql ) );
    }
    
    protected function contextualizeQuery( $sql )
    {
        if ( ! is_null( $this->course ) )
        {
            $sql = str_replace( '__CL_COURSE__', $this->course['dbName'].get_conf('dbGlu').get_conf('courseTablePrefix'), $sql );
        }
        
        $sql = str_replace( '__CL_MAIN__', get_conf('mainDbName').'`.`'.get_conf('mainTblPrefix'), $sql );
        
        return $sql;
    }
}

class Upgrade_TaskQueue
{
    protected static $tasks = array();
    
    public static function add( Upgrade_Task $task )
    {
        self::$tasks[] = $task;
    }
    
    public static function execute( $course = null )
    {
        $errorStep = array();
        
        if ( ! is_null ( $course ) )
        {
            Upgrade_CourseDatabase::upgradeStarted( $course['code'] );
            Upgrade_CourseLog::getLog()->info( "Starting upgrade for course {$course['code']}" );
        }
        else
        {
            Upgrade_MainLog::getLog()->info( "Starting main upgrade" );
        }
        
        foreach ( self::$tasks as $step => $task )
        {
            if ( ! is_null ( $course ) )
            {
                $task->setCourse($course);
                Upgrade_CourseDatabase::updateStep( $course['code'], $step );
            }
            
            try
            {
                $task->execute();
                
                if ( ! is_null( $course ) )
                {
                    Upgrade_CourseLog::getLog()->success( Upgrade_CourseLog::format( $step, $task->getMessage(), $course['code'] ) );
                }
                else
                {
                    Upgrade_MainLog::getLog()->success( Upgrade_MainLog::format( $step, $task->getMessage() ) );
                }
            }
            catch ( Exception $e )
            {
                if ( ! is_null( $course ) )
                {
                    Upgrade_CourseLog::getLog()->error( Upgrade_CourseLog::format( $step, $task->getMessage().":".$e->getMessage(), $course['code'] ) );
                }
                else
                {
                    Upgrade_MainLog::getLog()->error( Upgrade_MainLog::format( $step, $task->getMessage().":".$e->getMessage() ) );
                }
                    
                if ( $task->onError() == Upgrade_TaskConstants::ON_ERROR_INTERRUPT )
                {
                    if ( ! is_null ( $course ) )
                    {
                        Upgrade_CourseDatabase::upgradeFailure( $course['code'] );
                    }
                    
                    throw $e;
                }
                else
                {
                    $errorStep[] = $step;
                    continue;
                }
            }
        }
        
        if ( ! is_null ( $course ) )
        {
            if ( count( $errorStep ) == 0 )
            {
                Upgrade_CourseDatabase::upgradeSuccess( $course['code'] );
            }
            else
            {
                Upgrade_CourseDatabase::upgradePartial( $course['code'], $errorStep );
            }
        }
        
        return $errorStep;
    }
}
