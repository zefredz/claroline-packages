<?php

class EpcLog
{
    private $database, $tables;
    
    const
        ERROR = 'error',
        SUCCESS = 'success',
        LOG = 'log';
    
    public function __construct( $database = null )
    {
        $this->database = $database ? $database : Claroline::getDatabase();
        $this->tables = get_module_main_tbl(array('epc_log'));
    }
    
    public function syncError( $epcClassName, $message = '', $classId = null, $courseId = null, $userId = null )
    {
        $this->log( 'sync', self::ERROR, $epcClassName, $message, $courseId, $userId, $classId);
    }
    
    public function syncDone( $epcClassName, $message = '', $classId = null, $courseId = null, $userId = null )
    {
        $this->log( 'sync', self::SUCCESS, $epcClassName, $message, $classId, $courseId, $userId);
    }
    
    public function classAdded( $epcClassName, $message = '', $classId = null, $courseId = null, $userId = null )
    {
        $this->log( 'class_added', self::LOG, $epcClassName, $message, $classId, $courseId, $userId );
    }

    public function log( $action, $status, $epcClassName, $message = '', $classId = null, $courseId = null, $userId = null )
    {
        $userId = $userId ? $userId : claro_get_current_user_id();
        $courseId = $courseId ? $courseId : claro_get_current_course_id();
        
        $sqlClassName = $this->database->quote($epcClassName->__toString());
        $sqlClassId = $classId ? $this->database->escape($classId) : 'NULL';
        $sqlClientIP = $this->database->quote($_SERVER['REMOTE_ADDR']);
        $sqlClientFIP = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $this->database->quote($_SERVER['HTTP_X_FORWARDED_FOR']) : 'NULL';
        $sqlAction = $this->database->quote( $action );
        $sqlUserId = $this->database->quote( $userId );
        $sqlCourseId = $courseId ? $this->database->quote( $courseId ) : 'NULL';
        
        if ( is_array($message) )
        {
            $message = var_export( $message, true );
        }
        
        $sqlMessage = $this->database->quote( $message );
        
        switch( $status )
        {
            case self::ERROR:
            case self::SUCCESS:
            case self::LOG:
                $sqlStatus = $this->database->quote( $status );
                break;
            default:
                Console::debug("wrong status given in " . __CLASS__ . " '{$status}', using 'log' instead");
                $sqlStatus = $this->database->quote( self::LOG );
        }
        
        $this->database->exec("
            INSERT INTO
                `{$this->tables['epc_log']}`
            SET
                `class_name` = {$sqlClassName},
                `class_id` = {$sqlClassId},
                `client_ip` = {$sqlClientIP},
                `client_forwarded_ip` = {$sqlClientFIP},
                `date` = NOW(),
                `action` = {$sqlAction},
                `user_id` = {$sqlUserId},
                `course_id` = {$sqlCourseId},
                `status` = {$sqlStatus},
                `message` = {$sqlMessage}");
    }
    
    private static $_instance = false;
    
    public static function getInstance()
    {
        if ( ! self::$_instance )
        {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
}

class EpcLogMessage
{
    private
        $validUsers = array(),
        $insertedUsers = array(),
        $failedUsers = array(),
        $courseList = array(),
        $msgStr = '';
    
    public function setValidUsers( $validUsers )
    {
        $this->validUsers = $validUsers;
    }
    
    public function setInsertedUsers( $insertedUsers )
    {
        $this->insertedUsers = $insertedUsers;
    }
    
    public function setFailedUsers( $failedUsers )
    {
        $this->failedUsers = $failedUsers;
    }
    
    /*public function setCourseList( $courseList )
    {
        $this->courseList = $courseList;
    }*/
    
    public function addCourse( $courseId )
    {
        $this->courseList[] = $courseId;
    }
    
    public function setMessageString( $str )
    {
        $this->msgStr = $str;
    }
    
    public function __toString ()
    {
        $cntValidUsers = count($this->validUsers);
        $strValidUsers = var_export($this->validUsers, true);
        $cntInsertedUsers = count($this->insertedUsers);
        $strInsertedUsers = var_export($this->insertedUsers, true);
        $cntFailedUsers = count($this->failedUsers);
        $strFailedUsers = var_export($this->failedUsers, true);
        $cntCourseList = count($this->courseList);
        $strCourseList = var_export($this->courseList, true);
        $strMsg = $this->msgStr ? "{$this->msgStr}\n" : '';
        
        return "{$strMsg}Valid users ({$cntValidUsers}) : {$strValidUsers}\nFailed users ({$cntFailedUsers}) : {$strFailedUsers}\n Inserted users ({$cntInsertedUsers}) : {$strInsertedUsers}\n Course list ({$cntCourseList}) : {$strCourseList}";  
    }
}
