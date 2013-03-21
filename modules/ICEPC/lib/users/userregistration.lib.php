<?php

// $Id$

/**
 * CLAROLINE
 *
 * @version 0.1 $Revision$
 * @copyright (c) 2013 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package kernel
 * @author Frederic Minne <zefredz@claroline.net>
 *
 */

class Claro_UserRecord
{
    private $record;
    
    public function __construct ( $row )
    {
        $this->record = $row;
    }
    
    public function __get( $name )
    {
        if ( $name == 'username' && isset($this->record['username']) )
        {
            return $this->record['username'];
        }
        elseif ( $name == 'firstname' && isset($this->record['firstname']) )
        {
            return $this->record['firstname'];
        }
        elseif ( $name == 'lastname' && isset($this->record['lastname']) )
        {
            return $this->record['lastname'];
        }
        elseif ( $name == 'email' && isset($this->record['email']) )
        {
            return $this->record['email'];
        }
        elseif ( $name == 'officialCode' && isset($this->record['officialCode']) )
        {
            return $this->record['officialCode'];
        }
        elseif ( $name == 'password' && isset($this->record['password']) )
        {
            return $this->record['password'];
        }
        else
        {
            return null;
        }
    }
}

class Claro_UserRecordIteratorIterator extends RowToObjectIteratorIterator
{
    public function current()
    {
        $record = new Claro_UserRecord( parent::current() );
        return $record;
    }
}

class Claro_UserRecordArrayIterator extends RowToObjectIteratorIterator
{
    public function current()
    {
        $record = new Claro_UserRecord( $this->collection[ $this->key () ] );
        return $record;
    }
}

interface Claro_PasswordGenerator
{
    public function generatePassword();
}

class Claro_GenericPasswordGenerator implements Claro_PasswordGenerator
{
    private $length;
    
    public function __construct( $length = MK_PASSWORD_DEFAULT_LENGTH )
    {
        $this->length = $length;
    }
    
    public function generatePassword ()
    {
        return mk_password ( $this->length );
    }
}

class Claro_EmptyPasswordGenerator implements Claro_PasswordGenerator
{
    public function generatePassword ()
    {
        return 'empty';
    }
}

class Claro_UserBatchRegistration_Manager
{
    private 
        $database, 
        $course = null, 
        $class = null, 
        $useEmailAsLogin,
        $overwriteAuthSourceWith,
        $emptyPasswordForOverwrittenAuthSource,
        $forceClassRegistrationOfExistingClassUsers;
    
    public function __construct( $database = null )
    {
        $this->database = $database ? $database : Claroline::getDatabase();
        $this->useEmailAsLogin = false;
        $this->overwriteAuthSourceWith = null;
        $this->emptyPasswordForOverwrittenAuthSource = false;
    }
    
    public function setEmptyPasswordForOverwrittenAuthSource()
    {
        $this->emptyPasswordForOverwrittenAuthSource = true;
    }
    
    public function setForceClassRegistrationOfExistingUsers()
    {
        $this->forceClassRegistrationOfExistingClassUsers = true;
    }
    
    public function setCourse( $course )
    {
        $this->course = $course;
    }
    
    public function setClass( $class )
    {
        $this->class = $class;
    }
    
    public function setOverwriteAuthSourceWith( $authSource )
    {
        $this->overwriteAuthSourceWith = $authSource;
    }
    
    public function registerUserList( $userListIterator )
    {     
        // register in platform
        
        $platformUserList = new Claro_PlatformUserList();
        $platformUserList->registerUserList( 
            $userListIterator, 
            $this->overwriteAuthSourceWith, 
            $this->emptyPasswordForOverwrittenAuthSource );
        
        if ( $this->class )
        {
            $claroClassUserList = new Claro_ClassUserList( $this->class );
            $claroClassUserList->addUserIdList( $platformUserList->getValidUserIdList () );

            if ( $this->course )
            {
                // register class in course       
                if ( ! $this->class->isRegisteredToCourse ( $this->course->courseId ) )
                {
                    $this->class->registerToCourse( $this->course->courseId );
                }
            }

            $courseList = $this->class->getClassCourseList();

            foreach ( $courseList as $course )
            {
                $courseObj = new Claro_Course( $course['code'] );
                $courseObj->load();
                $courseUserList = new Claro_BatchCourseRegistration($courseObj);
                $courseUserList->addUserIdListToCourse( 
                    $platformUserList->getValidUserIdList(), 
                    true, 
                    $this->forceClassRegistrationOfExistingClassUsers );
            }
        }
        else
        {
            if ( $this->course )
            {
                // register users in course       
                $courseUserList = new Claro_BatchCourseRegistration( $this->course );
                $courseUserList->addUserIdListToCourse( 
                    $platformUserList->getValidUserIdList(), 
                    false, 
                    false );         
            }
        }
        
        // done !
        
    }
}
