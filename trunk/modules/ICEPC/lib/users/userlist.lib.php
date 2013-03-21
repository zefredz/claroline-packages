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

// add or remove lists of user ids from a course
/**
 * Utility class to add or remove users into or from a course by batch
 * @since Claroline 1.11.6
 */
class Claro_BatchCourseRegistration
{
    const 
        STATUS_SUCCESS = 0,
        STATUS_ERROR_UPDATE_FAIL = 1,
        STATUS_ERROR_INSERT_FAIL = 2,
        STATUS_ERROR_DELETE_FAIL = 4;
    
    private 
        $database, 
        $course, 
        $status = null, 
        $errLog = array(), 
        $tableNames;
    
    private
        $insertedUserList = array(),
        $failedUserList = array(),
        $updateUserList = array(),
        $deletedUserList = array();
    
    /**
     * 
     * @param Claro_Course $course
     * @param mixed $database Database_Connection instance or null, if null, the default database connection will be used
     */
    public function __construct( $course, $database = null )
    {
        $this->course = $course;
        $this->database = $database ? $database : Claroline::getDatabase();
        $this->tableNames = get_module_main_tbl(array('rel_course_user'));
        $this->tableNames = array_merge( $this->tableNames, 
            get_module_course_tbl( 
                array( 'bb_rel_topic_userstonotify', 'group_team', 'userinfo_content', 'group_rel_team_user', 'tracking_event' ), 
                $this->course->courseId ) );
    }
    
    private function _setStatus( $status )
    {
        $this->status = $status;
    }
    
    /**
     * Get the status of the operation
     * @return int : STATUS_SUCCESS, STATUS_ERROR_UPDATE_FAIL, 
     *  STATUS_ERROR_INSERT_FAIL or STATUS_ERROR_DELETE_FAIL
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * Check if the operation ended with errors
     * @return bool
     */
    public function hasError()
    {
        return !is_null( $this->status ) && $this->status > 0;
    }
    
    /**
     * Get the error log
     * @return array
     */
    public function getErrorLog()
    {
        return $this->errLog;
    }
    
    /**
     * Get the list of users newly inserted in the course
     * @return array of user_id => user
     */
    public function getInsertedUserList()
    {
        return $this->insertedUserList;
    }
    
    /**
     * Get the list of users with updated registration in the course
     * @return array of user_id => user
     */
    public function getUpdatedUserList()
    {
        return $this->updateUserList;
    }
    
    /**
     * Get the list of users for which the insertion or deletion failed
     * @return array of user_id => user
     */
    public function getFailedUserList()
    {
        return $this->failedUserList;
    }
    
    /**
     * Get the list of users removed from the course
     * @return array of user_id => user
     */
    public function getDeletedUserList()
    {
        return $this->deletedUserList;
    }
    
    /**
     * Get the list of users in $userIdList already registered to the course
     * @param array $userIdList
     * @param string $courseCode
     * @return array of user_id => [ user_id => int,count_user_enrol => int,count_class_enrol => int ]
     */
    protected function getUsersAlreadyInCourse ( $userIdList, $courseCode )
    {
        $ids = array();
        
        $courseUserIdListResultSet = $this->database->query( "
                SELECT
                    user_id, count_user_enrol, count_class_enrol
                FROM
                    `{$this->tableNames['rel_course_user']}`
                WHERE
                    code_cours = " . $this->database->quote($courseCode) . "
                AND 
                    user_id IN (" . implode( ',', $userIdList ) .")" );
        
        foreach ( $courseUserIdListResultSet as $user )
        {
            $ids[$user['user_id']] = $user;
        }
        
        return $ids;
    }
    
    /**
     * Add a list of users given their user id to the course
     * @param array $userIdList list of user ids to add
     * @param bool $classMode execute class registration instead of individual registration if set to true (default: false)
     * @param bool $forceClassRegistrationOfExistingClassUsers transform individual registration to class registration if set to true (default: false)
     * @param array $userListAlreadyInClass user already in class as an array of user_id => user
     * @return boolean
     */
    public function addUserIdListToCourse( $userIdList, $classMode = false, $forceClassRegistrationOfExistingClassUsers = false, $userListAlreadyInClass = array() )
    {
        if ( ! count( $userIdList ) )
        {
            return false;
        }
        
        $courseCode = $this->course->courseId;
        $sqlCourseCode = $this->database->quote( $courseCode );
        
        // 1. PROCESS USERS ALREADY IN COURSE
        
        // get user id already in course
        
        $usersAlreadyInCourse = $this->getUsersAlreadyInCourse( $userIdList, $courseCode );
                    
        // update registration of existing users if classMode
                    
        if ( $classMode )
        {
            foreach ( $usersAlreadyInCourse as $userId => $courseUser )
            {
                if ( !$forceClassRegistrationOfExistingClassUsers || $courseUser['count_class_enrol'] != 0 )
                {
                    $courseUser['count_user_enrol'] = 0;                 
                }
                
                if ( ! array_key_exists( $courseUser['user_id'], $userListAlreadyInClass ) )
                {
                    $courseUser['count_class_enrol']++;
                }
                
                // update user in DB
                if ( !$this->database->exec("
                    UPDATE
                        `{$this->tableNames['rel_course_user']}`
                    SET
                        `count_user_enrol` = " . $courseUser['count_user_enrol'] . ",
                        `count_class_enrol` = " . $courseUser['count_class_enrol'] . "
                    WHERE
                        user_id = " . Claroline::getDatabase()->escape($userId) . "
                    AND
                        code_cours = {$sqlCourseCode}"
                ) )
                {
                    $this->failedUserList[$courseUser['user_id']];
                }
                
                $this->updateUserList[$courseUser['user_id']] = $courseUser;
            }
            
            if ( count ( $this->failedUserList ) )
            {
                $this->_setStatus( self::STATUS_ERROR_UPDATE_FAIL );
                $this->errLog[] = "Cannot update course registration information for users " . implode(",",$this->failedUserList ) . " in course {$courseCode}";
                Console::error( "Cannot update course registration information for users " . implode(",",$this->failedUserList ) . " in course {$courseCode}" );
            }
        }
        
        // 2. PROCESS USERS NOT ALREADY IN COURSE
                    
        // construct the query for insertion of new users
        
        $sqlProfileId = $this->database->escape( claro_get_profile_id(USER_PROFILE) );
        
        $userNewRegistrations = array();
        $userListToInsert = array();
        
        foreach ( $userIdList as $userId )
        {
            if ( !array_key_exists ( $userId, $usersAlreadyInCourse ) )
            {
                if ( $classMode )
                {
                    $userNewRegistration = array(
                        'user_id' => $this->database->escape( $userId ),
                        'count_user_enrol' => 0,
                        'count_class_enrol' => 1
                    );
                }
                else
                {
                    $userNewRegistration = array(
                        'user_id' => $this->database->escape( $userId ),
                        'count_user_enrol' => 1,
                        'count_class_enrol' => 0
                    );
                }

                // user_id, profile_id, isCourseManager, isPending, tutor, count_user_enrol, count_class_enrol, enrollment_date
                $userNewRegistrations[] = "({$userNewRegistration['user_id']},{$sqlCourseCode}, {$sqlProfileId}, 0, 0, 0, {$userNewRegistration['count_user_enrol']},{$userNewRegistration['count_class_enrol']}, NOW())";
                $userListToInsert[$userId] = $userNewRegistration;
            }
        }
        
        // execute the quer
        
        if ( count($userNewRegistrations) )
        {  
            if ( !$this->database->exec("
                INSERT INTO
                    `{$this->tableNames['rel_course_user']}`
                        (user_id, code_cours, profile_id, isCourseManager, isPending, tutor, count_user_enrol, count_class_enrol, enrollment_date)
                VALUES\n" . implode( ",\n\t", $userNewRegistrations ) ) )
            {
                $this->_setStatus( self::STATUS_ERROR_INSERT_FAIL);
                $this->errLog[] = "Cannot insert userlist " . implode( ",", $userListToInsert ) . " in  course  {$courseCode}";
                Console::error( "Cannot insert userlist " . implode( ",", $userListToInsert ) . " in  course  {$courseCode}" );
                
                array_merge( $this->failedUserList, $userListToInsert );
            }
            
            $this->insertedUserList = $userListToInsert;
            
        }
        
        return $this->hasError();
    }
    
    /**
     * Remove a list of users given their user id from the cours
     * @param array $userIdList list of user ids to add
     * @param bool $classMode execute class registration instead of individual registration if set to true (default:false)
     * @param bool $keepTrackingData tracking data will be deleted if set to false (default:true, i.e. keep data)
     * @param array $moduleDataToPurge list of module_label => (purgeTracking => bool, purgeData => bool)
     * @return boolean
     */
    public function removeUserIdListFromCourse( $userIdList, $classMode = false, $keepTrackingData = true, $moduleDataToPurge = array() )
    {
        if ( ! count( $userIdList ) )
        {
            return false;
        }
        
        $courseCode = $this->course->courseId;
        $sqlCourseCode = $this->database->quote( $courseCode );
        
        // update user registration counts
        $cntToChange = $classMode ? 'count_class_enrol' : 'count_user_enrol';
        
        $this->database->exec("
            UPDATE
                `{$this->tableNames['rel_course_user']}`
            SET
                `{$cntToChange}` = `{$cntToChange}` - 1
            WHERE
                `code_cours` = {$sqlCourseCode}
            AND
                `{$cntToChange}` > 0
            AND
                `user_id` IN (".implode( ',', $userIdList ).")
        ");
                
                
        // get the user ids to remove
        
        $userListToRemove = $this->database->query("
            SELECT 
                `user_id`
            FROM
                `{$this->tableNames['rel_course_user']}`
            WHERE
                `count_class_enrol` <= 0
            AND
                `count_user_enrol` <= 0
            AND
                `code_cours` = {$sqlCourseCode}
        ");
                
        // var_dump($userIdList);die();
        
        if ( $userListToRemove->numRows() )
        {
            $userIdListToRemove = array();
            
            foreach ( $userListToRemove as $user )
            {
                $userIdListToRemove[] = $user['user_id'];
            }
            
            $sqlList = array();
            
            $sqlList[] = "DELETE FROM `{$this->tableNames['bb_rel_topic_userstonotify']}` WHERE user_id IN (".implode( ',', $userIdListToRemove ).")";
            $sqlList[] = "DELETE FROM `{$this->tableNames['userinfo_content']}` WHERE user_id IN (".implode( ',', $userIdListToRemove ).")";
            $sqlList[] = "UPDATE `{$this->tableNames['group_team']}` SET `tutor` = NULL WHERE `tutor` IN (".implode( ',', $userIdListToRemove ).")";
            $sqlList[] = "DELETE FROM `{$this->tableNames['group_rel_team_user']}` WHERE user IN (".implode( ',', $userIdListToRemove ).")";
            
            if ( !$keepTrackingData )
            {
                $sqlList[] = "DELETE FROM `{$this->tableNames['tracking_event']}` WHERE user_id IN (".implode( ',', $userIdListToRemove ).")";
            }
            
            $sqlList[] = "DELETE FROM `{$this->tableNames['rel_course_user']}` WHERE user_id IN (".implode( ',', $userIdListToRemove ).")";
            
            foreach ( $sqlList as $sql )
            {
                $this->database->exec( $sql );
            }
            
            if ( !empty( $moduleDataToPurge ) )
            {
                foreach ( $moduleDataToPurge as $moduleData )
                {
                    $connectorPath = get_module_path( $moduleData['label'] ) . '/connector/adminuser.cnr.php';
                    
                    if ( file_exists( $connectorPath ) )
                    {
                        require_once $connectorPath;
                        
                        $connectorClass = $moduleData['label'] . '_AdminUser';
                        
                        if ( class_exist ( $connectorClass ) )
                        {
                            $connector = new $connectorClass( $this->database );

                            if ( $moduleData['purgeTracking'] )
                            {
                                $connector->purgeUserListCourseTrackingData( $userIdListToRemove, $this->course->courseId );
                            }

                            if ( $moduleData['purgeResources'] )
                            {
                                $connector->purgeUserListCourseResources( $userIdListToRemove, $this->course->courseId );
                            }
                        }
                        else
                        {
                            Console::warning("Class {$connectorClass} not found");
                        }
                    }
                    else
                    {
                        Console::warning("No user delete connector found for module {$moduleData['label']}");
                    }
                }
            }
            
            $this->deletedUserList = $userListToRemove;
        }
        else
        {
            return false;
        }
    }
}

/**
 * Add a list of users to the platform
 * @since Claroline 1.11.6
 */
class Claro_PlatformUserList
{

    protected $database;
    
    protected 
        $userSuccessList = array(), 
        $userFailureList = array(), 
        $userInsertedList = array(),
        $userConvertedList = array(),
        $userDisabledList = array(),
        $userAlreadyThere = array();
    
    /**
     * 
     * @param mixed $database Database_Connection instance or null, if null, the default database connection will be used
     */
    public function __construct ( $database = null )
    {
        $this->database = is_null ( $database ) ? Claroline::getDatabase () : $database;
    }
    
    /**
     * Get the list of valid users i.e. users registered to the platform
     * @return array of username => user_id
     */
    public function getValidUserIdList()
    {
        return $this->userSuccessList;
    }
    
    /**
     * Get the list of newly inserted users
     * @return array of username => user_id
     */
    public function getInsertedUserIdList()
    {
        return $this->userInsertedList;
    }
    
    public function getFailedUserInsertionList()
    {
        return $this->userFailureList;
    }
    
    /**
     * Get the list of converted users i.e. users for which the authSource/password has been changed
     * @return array of username => user_id
     */
    public function getConvertedUserIdList()
    {
        return $this->userConvertedList;
    }
    
    /**
     * Get the list of valid users for which the account has been disabled
     * @return array of username => user_id
     */
    public function getDisabledUserIdList()
    {
        return $this->userConvertedList;
    }
    
    /**
     * Get the list of users that were alredy in the platform
     * @return array of username => user_id
     */
    public function getAlreadyThereUserIdList()
    {
        return $this->userAlreadyThere;
    }
    
    /**
     * 
     * @param Iterator $userList
     * @param string $overwriteAuthSourceWith change the auth source for existing users with the given one, set to null if you want to keep the original auth source (default:null)
     * @param bool $emptyPasswordForOverWrittenAuthSource empty (i.e. set to string value 'empty') users for which the auth source is changed
     * @return boolean false if empty list given
     */
    public function registerUserList ( $userList, $overwriteAuthSourceWith = null, $emptyPasswordForOverWrittenAuthSource = false )
    {
        if ( ! count( $userList ) )
        {
            return false;
        }
        
        $tbl_mdb_names = claro_sql_get_main_tbl ();
        $tbl_user = $tbl_mdb_names[ 'user' ];
        
        foreach ( $userList as $user )
        {
            try
            {
                $userFound = $this->getUserIfAlreadyExists ( $user );

                if ( false !== $userFound )
                {
                    if ( $userFound['email'] == $user->email )
                    {
                        if ( $overwriteAuthSourceWith 
                            && ( $userFound['authSource']  !== $overwriteAuthSourceWith ) )
                        {
                            if ( $emptyPasswordForOverWrittenAuthSource )
                            {
                                $emptyPassword = ",
                                    `password` = 'empty'
                                ";
                            }
                            else
                            {
                                $emptyPassword = '';
                            }
                            
                            $this->database->exec( "
                            UPDATE
                                `{$tbl_user}`
                            SET
                                `authSource` = ".$this->database->quote( $overwriteAuthSourceWith )."
                                {$emptyPassword}
                            WHERE
                                user_id = " . Claroline::getDatabase ()->escape ( $userFound['user_id'] )
                            );
                            
                            $this->userConvertedList[$userFound['username']] = $userFound['user_id'];
                            Console::info ( "Change authSource to {$overwriteAuthSourceWith} for user ".var_export($userFound,true) );
                        }
                        else
                        {
                            // user already there, nothing to be done
                            $this->userAlreadyThere[$userFound['username']] = $userFound['user_id'];
                        }

                        $this->userSuccessList[$userFound['username']] = $userFound['user_id'];
                    }
                    else
                    {
                        // disable old account by changing the username
                        $this->database->exec( "
                        UPDATE
                            `{$tbl_user}`
                        SET
                            `authSource` = 'disabled',
                            `username` = CONCAT('*EPC*', username )
                        WHERE
                            user_id = " . Claroline::getDatabase ()->escape ( $userFound['user_id'] )
                        );
                        
                        $this->userDisabledList[$userFound['username']] = $userFound['user_id'];
                        
                        Console::info ( "Disable account for user ".var_export($userFound,true)." : conflict with ldap account " .var_export($user,true) );
                        
                        $this->insertUserAsNew($user);
                    }
                }
                else
                {
                    $this->insertUserAsNew($user);
                }
            }
            catch ( Exception $e )
            {
                $this->userFailureList[] = $user;
                Console::error ( "Cannot add user {$user->username} : EXCEPTION '{$e->getMessage()}' with stack {$e->getTraceAsString ()}" );
            }
        }
        
        Console::info( "Add user to platform from EPC : converted=" . count( $this->userConvertedList )
                . " disabled=" .count( $this->userDisabledList )
                . " inserted=" . count( $this->userInsertedList )
                . " alreadythere=" . count( $this->userAlreadyThere )
                . " failed=" . count( $this->userFailureList ) );
        
        if ( count( $this->userInsertedList ) )
        {
            Console::info( "Add user to platform from EPC : userid created " . implode(',', $this->userInsertedList ) );
        }
        
        return true;
        
    }
    
    /**
     * Insert a user record as new user in the platform user list
     * @param stdClass $user (lastname,firstname,username,email,officialCode)
     */
    protected function insertUserAsNew( $user )
    {
        $tbl_mdb_names = claro_sql_get_main_tbl ();
        $tbl_user = $tbl_mdb_names[ 'user' ];
        
        $this->database->exec( "
        INSERT INTO `{$tbl_user}`
        SET nom             = ". $this->database->quote($user->lastname) .",
            prenom          = ". $this->database->quote($user->firstname) .",
            username        = ". $this->database->quote($user->username) .",
            language        = '',
            email           = ". $this->database->quote($user->email) .",
            officialCode    = ". $this->database->quote($user->officialCode) .",
            officialEmail   = ". $this->database->quote($user->email) .",
            authSource      = 'ldap', 
            phoneNumber     = '',
            password        = 'empty',
            isCourseCreator = 0,
            isPlatformAdmin = 0,
            creatorId    = " . claro_get_current_user_id() );
        
        $key = (string) $user->username;

        $this->userInsertedList[$key] = $this->userSuccessList[$key] = $this->database->insertId();
    }
    
    /**
     * Returns a user if it is found, false otherwise
     * @param stdClass $user (username,...)
     * @return mixed false or the found user
     */
    public function getUserIfAlreadyExists ( $user )
    {
        $tbl_mdb_names = claro_sql_get_main_tbl ();
        $tbl_user = $tbl_mdb_names[ 'user' ];

        $foundUser = $this->database->query ( "
            SELECT 
                u.username,
                u.user_id,
                u.authSource,
                u.email
            FROM 
                `{$tbl_user}` AS u 
            WHERE 
                u.`username` = " . $this->database->quote ( $user->username )
        );

        if ( !$foundUser->numRows () )
        {
            return false;
        }
        else
        {
            return $foundUser->fetch();
        }
    }

}

/**
 * USer list of a course
 * @since Claroline 1.11.6
 */
class Claro_CourseUserList
{

    protected $cid, $course, $database;
    protected $courseUserList, $courseUserIdList;
    
    /**
     * 
     * @param string $cid id(code) of the course
     * @param mixed $database Database_Connection instance or null, if null, the default database connection will be used
     */
    public function __construct ( $cid = null, $database = null )
    {
        $this->cid = is_null ( $cid ) ? claro_get_current_course_id () : $cid;
        $this->database = is_null ( $database ) ? Claroline::getDatabase () : $database;

        $this->course = new Claro_course ( $cid );
        $this->course->load ();
    }
    
    /**
     * Get the list of users registered in the course
     * @param bool $forceRefresh
     * @return array of user_id => user (username, user_id, count_user_enrol, count_class_enrol)
     */
    public function getUserList ( $forceRefresh = false )
    {
        if ( !is_array ( $this->courseUserList ) || $forceRefresh )
        {
            $tbl_mdb_names = claro_sql_get_main_tbl ();
            $tbl_user = $tbl_mdb_names[ 'user' ];
            $tbl_rel_course_user = $tbl_mdb_names[ 'rel_course_user' ];

            $cid = $this->database->quote ( $this->cid );

            $resultSet = $this->database->query ( "
            SELECT 
                u.username, 
                cu.user_id, 
                cu.count_user_enrol, 
                cu.count_class_enrol
            FROM
                `{$tbl_rel_course_user}` AS cu
            JOIN
                `{$tbl_user}` AS u
            ON
                cu.user_id = u.user_id
            WHERE
                cu.code_cours = {$cid}
         " );

            $resultSet->useId ( 'user_id' );

            $this->courseUserList = array ( );

            foreach ( $resultSet as $userId => $user )
            {
                $this->courseUserList[ $userId ] = $user;
            }
        }

        return $this->courseUserList;
    }
    
    /**
     * Get the list of the ids of the users registered in the course
     * @param bool $forceRefresh
     * @return array of user_id => user_id so it can be used a a list or a set
     */
    public function getUserIdList ( $forceRefresh = false )
    {
        if ( !is_array ( $this->courseUserIdList ) || $forceRefresh )
        {
            $tbl_mdb_names = claro_sql_get_main_tbl ();
            $tbl_rel_course_user = $tbl_mdb_names[ 'rel_course_user' ];

            $cid = $this->database->quote ( $this->cid );

            $resultSet = $this->database->query ( "
            SELECT
                cu.user_id
            FROM
                `{$tbl_rel_course_user}` AS cu

            WHERE
                cu.code_cours = {$cid}
                
         " );

            $resultSet->useId ( 'user_id' );

            $this->courseUserIdList = array ( );

            foreach ( $resultSet as $user_id => $user_id )
            {
                $this->courseUserIdList[ $user_id ] = $user;
            }
        }

        return $this->courseUserIdList;
    }

}