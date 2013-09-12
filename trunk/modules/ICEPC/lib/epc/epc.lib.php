<?php

// $Id$
// vim: expandtab sw=4 ts=4 sts=4:

/**
 * EPC connector library
 *
 * @version     $Revision$
 * @copyright   (c) 2001-2012, Universite catholique de Louvain (UCL)
 * @author      Claroline Team <info@claroline.net>
 * @author      Frederic Minne <zefredz@claroline.net>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE version 2 or later
 * @package     ucl.epc
 */

define ( 'EPC_TYPE_COURSE', 'course' );
define ( 'EPC_TYPE_PROGRAM', 'program' );

/**
 * Convert course or program code to EPC query
 */
class EpcCodeToQuery
{
    /**
     * Convert UCL course code to EPC query
     * @param string $courseCode format SSSSSNNNND (ex.: LBIO1111A, LMAPR2016)
     * @param string $year optional format YYYY
     * @return string
     * @throws Exception
     */
    public static function getCourseQuery ( $courseCode, $year = null )
    {
        $matches = array ( );

        if ( preg_match ( '/([A-Z]{1,5})(\d{4})([A-Z]?)/', $courseCode, $matches ) )
        {
            // return var_export( $matches, true );

            if ( !count ( $matches ) == 4 )
            {
                throw new Exception ( "Wrong course code {$courseCode}" );
            }

            $courseUrlFragmentArray = array ( );
            $courseUrlFragmentArray[ ] = empty ( $year ) ? '-' : $year;
            $courseUrlFragmentArray[ ] = empty ( $matches[ 1 ] ) ? '-' : $matches[ 1 ];
            $courseUrlFragmentArray[ ] = empty ( $matches[ 2 ] ) ? '-' : $matches[ 2 ];
            $courseUrlFragmentArray[ ] = empty ( $matches[ 3 ] ) ? '-' : $matches[ 3 ];

            return implode ( '/', $courseUrlFragmentArray );
        }
        else
        {
            throw new Exception ( "Wrong course code {$courseCode}" );
        }
    }
    
    /**
     * Convert UCL course code to EPC query
     * @param string $programCode format SSSSCNLF/OR (ex.: BIRA21MS/G, BIR13BA)
     * @param string $year optional YYYY
     * @return string
     * @throws Exception
     */
    public static function getProgramQuery ( $programCode, $year = null )
    {
        $matches = array ( );

        if ( preg_match ( '/([A-Z]{1,5})(\d{1})(\d{1})([A-Z]{2})\/?([A-Z]{0,2})/', $programCode, $matches ) )
        {
            // return var_export( $matches, true );

            if ( !count ( $matches ) == 6 )
            {
                throw new Exception ( "Wrong program code {$programCode}" );
            }

            $programUrlFragmentArray = array ( );
            $programUrlFragmentArray[ ] = empty ( $year ) ? '-' : $year;
            $programUrlFragmentArray[ ] = empty ( $matches[ 1 ] ) ? '-' : $matches[ 1 ];
            $programUrlFragmentArray[ ] = empty ( $matches[ 2 ] ) ? '-' : $matches[ 2 ];
            $programUrlFragmentArray[ ] = empty ( $matches[ 3 ] ) ? '-' : $matches[ 3 ];
            $programUrlFragmentArray[ ] = empty ( $matches[ 4 ] ) ? '-' : $matches[ 4 ];
            $programUrlFragmentArray[ ] = empty ( $matches[ 5 ] ) ? '-' : $matches[ 5 ];

            return implode ( '/', $programUrlFragmentArray );
        }
        else
        {
            throw new Exception ( "Wrong program code {$programCode}" );
        }
    }

}

/**
 * Basic class to execute queries against the EPC REST web service 
 */
class EpcServiceQuery
{

    protected
        $baseUrl,
        $username = '',
        $password = '';
    protected
        $info,
        $response;

    /**
     * Constructor
     * @param string $baseUrl URL of the web service
     * @param string $username HTTP username to access the web service
     * @param string $password HTTP password to access the web service
     */
    public function __construct ( $baseUrl, $username = '', $password = '' )
    {
        $this->baseUrl  = $baseUrl;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Execute a query against the EPC web service
     * @param string $queryUrl EPC queries are given using a URL path
     * @return boolean 
     */
    public function execQuery ( $queryUrl )
    {
        // var_dump($this->baseUrl.'/'.$queryUrl);

        $process = curl_init ( $this->baseUrl . '/' . $queryUrl );

        curl_setopt ( $process, CURLOPT_HEADER, 0 );
        curl_setopt ( $process, CURLOPT_USERPWD, $this->username . ":" . $this->password );
        curl_setopt ( $process, CURLOPT_TIMEOUT, 180 );
        curl_setopt ( $process, CURLOPT_RETURNTRANSFER, TRUE );

        $this->response = curl_exec ( $process );
        $this->info     = curl_getinfo ( $process );

        curl_close ( $process );

        if ( $this->info[ 'http_code' ] != '200' )
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Get the response from the service
     * @return mixed 
     */
    public function getResponse ()
    {
        return $this->response;
    }

    /**
     * Get the info about the query
     * @return array
     * @see curl library documentation 
     */
    public function getInfo ()
    {
        return $this->info;
    }
    
    /**
     * Returns true if an error has occured on the remote service (i.e. if http 
     * return code is not 200)
     * @return bool
     */
    public function hasError()
    {
        return $this->info['http_code'] != '200';
    }

}

/**
 * Helper to easily call EPC service
 */
class EpcQueryHelper extends EpcServiceQuery
{
    /**
     * Get the list of students in a course
     * @param string $year YYYY
     * @param string $courseCode format SSSSSNNNND (ex.: LBIO1111A, LMAPR2016)
     * @return mixed query response
     */
    public function getStudentsInCourse ( $year, $courseCode )
    {

        if ( $this->execQuery ( 'cours/' . EpcCodeToQuery::getCourseQuery ( $courseCode, $year ) ) )
        {
            return $this->getResponse ();
        }
        else
        {
            return $this->getInfo ();
        }
    }
    
    /**
     * Get the list of students in a program
     * @param string $year YYYY
     * @param string $programCode format SSSSCNLF/OR (ex.: BIRA21MS/G, BIR13BA)
     * @return mixed query response
     */
    public function getStudentsInProgram ( $year, $programCode )
    {

        if ( $this->execQuery ( 'anneeEtude/' . EpcCodeToQuery::getProgramQuery ( $programCode, $year ) ) )
        {
            return $this->getResponse ();
        }
        else
        {
            return $this->getInfo ();
        }
    }
    
    /**
     * Returns true if no error occured on the remote service (i.e. http return
     * code is 200)
     * @return bool
     */
    public function isSuccess ()
    {
        return $this->info[ 'http_code' ] == '200';
    }

}

/**
 * EPC XML Response wrapper
 */
abstract class EpcServiceXmlResponse
{

    protected $xml, $students = null, $studentsCount = null;
    
    /**
     * Wrap the given xml response
     * @param string $queryResponse
     */
    public function __construct ( $queryResponse )
    { 
        if ( !preg_match( '/\<[^<]*?\>/', $queryResponse ) )
        {
            throw new Exception ( get_lang( "Invalid query, please check your query parameters and/or contact the administrator, returned response : %message", array( '%message' => $queryResponse ) ) );
        }
        
        $this->xml = simplexml_load_string ( $queryResponse );
    }
    
    /**
     * Get students
     * @return array of SimpleXmlElement
     */
    public function getStudents ()
    {
        // hydrate
        if ( is_null( $this->students ) )
        {
            $this->students = $this->xml->xpath ( 'etudiant' );
        }
        
        return $this->students;
    }
    
    /**
     * Get the number of returned students
     * @return int
     */
    public function getNumberOfRecords ()
    {
        // hydrate
        if ( is_null ( $this->studentsCount ) )
        {
            $this->studentsCount = count ( $this->getStudents () );
        }
        
        return $this->studentsCount;
    }
    
    /**
     * Get an iterator for the returned student list
     * @return \EpcServiceStudentsIterator
     */
    public function getIterator ()
    {
        return new EpcServiceStudentsIterator ( $this->getStudents () );
    }

    abstract public function getInfo ();
}

class EpcServiceStudentsInCourse extends EpcServiceXmlResponse
{
    /**
     * Get informations about the response
     * @return array ( courseNumber, courseInitials, courseSubdivision, courseValidity, numberOfStudents )
     */
    public function getInfo ()
    {
        return array (
            'courseNumber'      => (string) $this->xml->coursCnum,
            'courseInitials'    => (string) $this->xml->coursSigle,
            'courseSubdivision' => (string) $this->xml->coursSubdivision,
            'courseValidity'    => (string) $this->xml->coursValidite,
            'numberOfStudents'  => (string) $this->xml->nombreEtudiantsInscrits
        );
    }

}

class EpcServiceStudentsInProgram extends EpcServiceXmlResponse
{
    /**
     * Get informations about the response
     * @return array ( programCycle, programLevel, programOrientation, programInitials, programValidity, programSuffix, numberOfStudents )
     */
    public function getInfo ()
    {
        return array (
            'numberOfStudents'   => (string) $this->xml->nombreEtudiantsInscrits,
            'programCycle'       => (string) $this->xml->offreCycle,
            'programLevel'       => (string) $this->xml->offreNiveau,
            'programOrientation' => (string) $this->xml->offreOrientation,
            'programInitials'    => (string) $this->xml->offreSigle,
            'programValidity'    => (string) $this->xml->offreValidite,
            'programSuffix'      => (string) $this->xml->offresLettresFinales
        );
    }

}

/**
 * Student record wrapper
 */
class EpcServiceStudentRecord
{

    protected $xmlRecord;
    
    /**
     * Wrap a user
     * @param SimpleXmlElement $xmlRecord
     */
    public function __construct ( $xmlRecord )
    {
        $this->xmlRecord = $xmlRecord;
    }

    // Let's do some magic here :)
    /**
     * Translate EPC student record property to Claroline user property by using some black magic
     * @param string $name
     * @return mixed
     */
    public function __get ( $name )
    {
        if ( $name == 'username' )
        {
            return (string) $this->xmlRecord->uidLDAP;
        }
        elseif ( $name == 'firstname' )
        {
            return iconv ( 'utf-8', get_conf ( 'charset' ), (string) $this->xmlRecord->prenom );
        }
        elseif ( $name == 'lastname' )
        {
            return iconv ( 'utf-8', get_conf ( 'charset' ), (string) $this->xmlRecord->nom );
        }
        elseif ( $name == 'email' )
        {
            return (string) $this->xmlRecord->email;
        }
        elseif ( $name == 'officialCode' || $name == 'employeeNumber' )
        {
            return $this->getOfficialCode ();
        }
        elseif ( $name == 'noma' )
        {
            return isset ( $this->xmlRecord->noma ) ? (string) $this->xmlRecord->noma : null;
        }
        elseif ( $name == 'sigleAnet' )
        {
            return isset ( $this->xmlRecord->sigleAnet ) ? (string) $this->xmlRecord->sigleAnet : null;
        }
        else
        {
            if ( isset ( $this->xmlRecord->$name ) )
            {
                return iconv ( 'utf-8', get_conf ( 'charset' ), (string) $this->xmlRecord->$name );
            }
            else
            {
                return null;
            }
        }
    }
    
    /**
     * Get the FGS matricule as officialCode
     * @return string
     */
    public function getOfficialCode ()
    {
        return ltrim ( (string) $this->xmlRecord->matriculeFgs, '0' );
    }

}

/**
 * Iterator to convert array of EPC students (SimpleXmlElement) to EpcServiceStudentRecord instances
 */
class EpcServiceStudentsIterator extends RowToObjectArrayIterator
{
    /**
     * Get current item as EpcServiceStudentRecord instance
     * @return \EpcServiceStudentRecord
     */
    public function current ()
    {
        return new EpcServiceStudentRecord ( $this->collection[ $this->key () ] );
    }

}

/**
 * EPC service client for course and program student lists
 */
class EpcStudentListService
{

    protected
        $baseUrl,
        $username = '',
        $password = '',
        $epcQuery;
    
    /**
     * 
     * @param string $baseUrl url of the service
     * @param string $username service user login
     * @param string $password service user password
     */
    public function __construct ( $baseUrl, $username = '', $password = '' )
    {
        $this->baseUrl  = $baseUrl;
        $this->username = $username;
        $this->password = $password;

        $this->epcQuery = new EpcQueryHelper ( $baseUrl, $username, $password );
    }
    
    /**
     * Get list of students in a course  for a given academic year
     * @param string $year start year of academic year format YYYY (ex.: 2012 for 2012-2013)
     * @param string $courseCode format SSSSSNNNND (ex.: LBIO1111A, LMAPR2016)
     * @return \EpcServiceStudentsInCourse
     * @throws Exception
     */
    public function getStudentsInCourse ( $year, $courseCode )
    {
        if ( !$this->epcQuery->getStudentsInCourse ( $year, $courseCode ) )
        {
            throw new Exception (
                "Error while retrieving students in course {$courseCode} for year {$year} : "
                . var_export ( $this->epcQuery->getInfo (), true )
            );
        }

        return new EpcServiceStudentsInCourse ( $this->epcQuery->getResponse () );
    }
    
    /**
     * Get the list of students in a program for a given academic year
     * @param string $year start year of academic year format YYYY (ex.: 2012 for 2012-2013)
     * @param string $programCode  format SSSSCNLF/OR (ex.: BIRA21MS/G, BIR13BA)
     * @return \EpcServiceStudentsInProgram
     * @throws Exception
     */
    public function getStudentsInProgram ( $year, $programCode )
    {
        if ( !$this->epcQuery->getStudentsInProgram ( $year, $programCode ) )
        {
            throw new Exception (
                "Error while retrieving students in program {$programCode} for year {$year} : "
                . var_export ( $this->epcQuery->getInfo (), true )
            );
        }

        return new EpcServiceStudentsInProgram ( $this->epcQuery->getResponse () );
    }
    
    /**
     * Information about the response
     * @return array
     */
    public function getInfo ()
    {
        return $this->epcQuery->getInfo ();
    }
    
    /**
     * Get the XML response for debug purpose or for other usage
     * @return string
     */
    public function getRawResponse ()
    {
        return $this->epcQuery->getResponse ();
    }
    
    public function hasError()
    {
        return $this->epcQuery->hasError();
    }
}

/**
 * Utility class to get info about user list in a course
 */
class EpcCourseUserListInfo
{
    private $database, $courseId;
    
    /**
     * 
     * @param string $courseId
     * @param Database_Connection $database
     */
    public function __construct ( $courseId, $database = null)
    {
        $this->courseId = $courseId;
        $this->database = $database ? $database : Claroline::getDatabase();
    }
    
    /**
     * Get the list of user to update
     * @param EpcServiceStudentIterator $userList
     * @param bool $askForClassRegistrationToForce retrieve user for which the class registration will be forced
     * @param bool $askForPendingEnrollment retrieve user with pending enrolment to validate
     * @return array username => user(username, user_id, count_user_enrol, count_class_enrol, isPending)
     */
    public function getUsernameListToUpdate( $userList, $askForClassRegistrationToForce = true, $askForPendingEnrollment = true )
    {
        if ( !count( $userList ) )
        {
            return array();
        }
        
        $usernameList = array();
        
        foreach ( $userList as $user )
        {
            $usernameList[] = $this->database->quote( $user->username );
        }
        
        $tbl_mdb_names = claro_sql_get_main_tbl ();
        $tbl_user = $tbl_mdb_names[ 'user' ];
        $tbl_rel_course_user = $tbl_mdb_names[ 'rel_course_user' ];

        $cid = $this->database->quote ( $this->courseId );
        
        if ( $askForClassRegistrationToForce && $askForPendingEnrollment )
        {
            $condition = " AND 
                (cu.count_class_enrol < 1  OR cu.isPending = 1 )";
        }
        elseif ( $askForClassRegistrationToForce )
        {
            $condition = "AND
                cu.count_class_enrol < 1";
        }
        elseif ( $askForPendingEnrollment )
        {
            $condition = "AND
                cu.isPending = 1";
        }
        else
        {
            $condition = "";
        }

        $resultSet = $this->database->query ( "
            SELECT 
                u.username, 
                cu.user_id, 
                cu.count_user_enrol, 
                cu.count_class_enrol,
                cu.isPending
            FROM
                `{$tbl_rel_course_user}` AS cu
            JOIN
                `{$tbl_user}` AS u
            ON
                cu.user_id = u.user_id
            AND
                u.username IN (".implode(',',$usernameList).")
            WHERE
                cu.code_cours = {$cid}
            {$condition}
        " );
                
        $resultSet->useId ( 'username' );
        
        $usernameListToUpdate = array();
        
        foreach ( $resultSet as $username => $userToUpdate )
        {
            $usernameListToUpdate[$username] = $userToUpdate;
        }
        
        return $usernameListToUpdate;
    }
}

class EpcUserListInfo
{
    private $database;
    
    /**
     * 
     * @param string $courseId
     * @param Database_Connection $database
     */
    public function __construct ( $database = null)
    {
        $this->database = $database ? $database : Claroline::getDatabase();
    }
    
    public function getUserListToUpdate( $userList, $reEnableDisabledAccounts )
    {
        if ( !count( $userList ) )
        {
            return array();
        }
        
        $usernameList = array();
        
        foreach ( $userList as $user )
        {
            $usernameList[] = $this->database->quote( $user->username );
        }
        
        if ( ! $reEnableDisabledAccounts )
        {
            $condition = "
            AND
                u.authSource != 'disabled'
            ";
        }
        else
        {
            $condition = "
            AND
                ( u.authSource != 'disabled' OR u.email LIKE '%uclouvain.be' )
            ";
        }
        
        $tbl_mdb_names = claro_sql_get_main_tbl ();
        $tbl_user = $tbl_mdb_names[ 'user' ];
        
        $resultSet = $this->database->query ( "
            SELECT 
                u.username
            FROM
                `{$tbl_user}` AS u
            WHERE
                u.username IN (".implode(',',$usernameList).")
            {$condition}
        " );
                
        $resultSet->useId ( 'username' );
        
        $usernameListToUpdate = array();
        
        foreach ( $resultSet as $username => $userToUpdate )
        {
            $usernameListToUpdate[$username] = $userToUpdate;
        }
        
        return $usernameListToUpdate;
    }
}

/**
 * Cache of some user data retreived from EPC (mainly NOMA and Year of study
 */
class EpcUserDataCache
{
    private $database, $tbl;
    
    /**
     * Constructor
     * @param Database_Connection $database database connection or null to use 
     * the default Claroline database connexion
     */
    public function __construct ( $database = null )
    {
        $this->database = $database ? $database : Claroline::getDatabase ();
        $this->tbl = get_module_main_tbl(array('epc_user_data'));
    }
    
    /**
     * Cache data from user list
     * @param EpcServiceStudentsIterator $userIterator
     * @param array $usernameToIdTranslationTable of username => user_id
     * @return boolean
     */
    public function registerUserData( $userIterator, $usernameToIdTranslationTable )
    {
        if ( ! count( $userIterator ) )
        {
            return false;
        }
        
        if ( ! count( $usernameToIdTranslationTable ) )
        {
            return false;
        }
        
        $userDataSqlInsertArray = array();
        $userDataSqlUpdateArray = array();
        $userList = array();
        
        foreach ( $userIterator as $user )
        {
            if ( isset ( $usernameToIdTranslationTable[$user->username] ) )
            {
                $userList[$usernameToIdTranslationTable[$user->username]] = $user;
            }
        }
        
        $userIdList = array_keys( $userList );
        $userAlreadyThereIdList = $this->getUserAlreadyCached($userIdList);
        
        foreach ( $userList as $userId => $userToProcess )
        {
            $sqlUserId = (int) $userId;
            $sqlNoma = $this->database->quote( $userToProcess->noma );
            $sqlAnet = $this->database->quote( $userToProcess->sigleAnet );
                
            if ( isset($userAlreadyThereIdList[$userId]) )
            {
                $userDataSqlUpdateArray[] = "SET noma = {$sqlNoma}, sigle_anet = {$sqlAnet}, last_sync = NOW() WHERE user_id = {$sqlUserId}";
            }
            else
            {
                
                $userDataSqlInsertArray[] = "({$sqlUserId}, {$sqlNoma}, {$sqlAnet}, '', NOW())";
            }
        }
        
        if ( count( $userDataSqlInsertArray ) )
        {
            $this->database->exec("
                INSERT INTO
                    `{$this->tbl['epc_user_data']}` (user_id, noma, sigle_anet, other_data, last_sync)
                VALUES
                    ".implode(",\n",$userDataSqlInsertArray));
        }
        
        if ( count( $userDataSqlUpdateArray) )
        {
            foreach ( $userDataSqlUpdateArray as $userUpdate )
            {
                $this->database->exec("
                    UPDATE
                        `{$this->tbl['epc_user_data']}`
                    {$userUpdate}");
            }
        }
        
        return true;
    }
    
    /**
     * Get cached data about all users in the given list
     * @param array $userIdList
     * @return array of user_id => userdata
     */
    public function getAllUsersCachedData( $userIdList )
    {
        if ( ! count( $userIdList ) )
        {
            return array();
        }
        
        $userIds = implode( ',', $userIdList );
        
        $result = $this->database->query("SELECT user_id, noma, sigle_anet, other_data, last_sync FROM `{$this->tbl['epc_user_data']}` WHERE user_id IN ($userIds);");
        
        $userIdListToReturn = array();
        
        foreach ( $result as $user )
        {
            $userIdListToReturn[$user['user_id']] = $user;
        }
        
        return $userIdListToReturn;
    }
    
    private function getUserAlreadyCached( $userIdList )
    {
        if ( ! count( $userIdList ) )
        {
            return array();
        }
        
        $userIds = implode( ',', $userIdList );
        
        $result = $this->database->query("SELECT user_id FROM `{$this->tbl['epc_user_data']}` WHERE user_id IN ($userIds);");
        
        $userIdListToReturn = array();
        
        foreach ( $result as $user )
        {
            $userIdListToReturn[$user['user_id']] = $user['user_id'];
        }
        
        return $userIdListToReturn;
    }
    
    /**
     * Get list of users whose cached data must be updated (i.e. missing or changed) 
     * @param EpcServiceStudentsIterator $userIterator
     * @param array $usernameToIdTranslationTable array of username => user_id
     * @return array
     */
    public function getUserListToUpdate( $userIterator, $usernameToIdTranslationTable )
    {
        if ( ! count( $userIterator ) )
        {
            return array();
        }
        
        if ( ! count( $usernameToIdTranslationTable ) )
        {
            return array();
        }
        
        $userList = array();
        
        foreach ( $userIterator as $user )
        {
            if ( isset ( $usernameToIdTranslationTable[$user->username] ) )
            {
                $userList[$usernameToIdTranslationTable[$user->username]] = $user;
            }
        }
        
        $userIdList = array_keys( $userList );
        $userAlreadyThereIdList = $this->getAllUsersCachedData($userIdList);
        
        $userListToUpdate = array();
        
        foreach ( $userList as $userId => $userMissing )
        {
            if ( !isset( $userAlreadyThereIdList[$userId] ) )
            {
                $userListToUpdate[$userMissing->username] = true;
            }
        }
        
        foreach ( $userAlreadyThereIdList as $userId => $userCached )
        {
            if ( isset( $userList[$userId] ) )
            {
                $userAlreadyThere = $userList[$userId];

                if ( $userCached['sigle_anet'] != $userAlreadyThere->sigleAnet 
                    || $userCached['noma'] != $userAlreadyThere->noma )
                {
                    $userListToUpdate[$userAlreadyThere->username] = true;
                }
            }
        }
        
        return $userListToUpdate;
    }
}

class EpcClassQueryProperties
{
    private $database, $tbl, $epcClassName, $properties;
    
    public function __construct ( $epcClassName, $courseId = null, $database = null )
    {
        $this->database = $database ? $database : Claroline::getDatabase();
        
        $this->tbl = get_module_course_tbl( array('course_properties'), $courseId );
        
        $this->epcClassName = $epcClassName->__toString();
        
        $this->properties = array(
            'epcLinkExistingStudentsToClass' => null,
            'epcValidatePendingUsers' => null
        );
        
        $this->load();
    }
    
    public function setOptions ( $epcLinkExistingStudentsToClass, $epcValidatePendingUsers )
    {
        if ( is_null( $this->properties['epcLinkExistingStudentsToClass'] ) )
        {
            $this->insert("{$this->epcClassName}.epcLinkExistingStudentsToClass", $epcLinkExistingStudentsToClass ? 1 : 0 );
        }
        else
        {
            $this->update("{$this->epcClassName}.epcLinkExistingStudentsToClass", $epcLinkExistingStudentsToClass ? 1 : 0 );
        }
        
        $this->properties[$this->properties['epcLinkExistingStudentsToClass']] = $epcLinkExistingStudentsToClass;
        
        if ( is_null( $this->properties['epcValidatePendingUsers'] ) )
        {
            $this->insert("{$this->epcClassName}.epcValidatePendingUsers", $epcValidatePendingUsers ? 1 : 0 );
        }
        else
        {
            $this->update("{$this->epcClassName}.epcValidatePendingUsers", $epcValidatePendingUsers ? 1 : 0 );
        }
        
        $this->properties[$this->properties['epcValidatePendingUsers']] = $epcValidatePendingUsers;
    }
    
    public function getOptions()
    {
        return $this->properties;
    }
    
    private function load()
    {
        $properties = $this->database->query("
            SELECT 
                `id`,
                `name`, 
                `category`, 
                `value` 
            FROM 
                `{$this->tbl['course_properties']}` 
            WHERE 
                `category` = 'ICEPC'
            AND
                `name` LIKE '".$this->database->escape($this->epcClassName).".%'
        ");
        
        foreach ( $properties as $property )
        {
            list ( $epcClass, $name ) = explode('.', $property['name'] );
            
            $this->properties[$name] = $property['value'] == '1' ? true : false;
        }
    }
    
    private function insert( $name, $value )
    {
        $sqlName = $this->database->quote( $name );
        $sqlValue = $this->database->escape( $value );
        
        return $this->database->exec("
            INSERT INTO
                `{$this->tbl['course_properties']}`
            SET
                `name`= {$sqlName},
                `category` = 'ICEPC',
                `value` = {$sqlValue}
        ");
    }
    
    private function update( $name, $value )
    {
        $sqlName = $this->database->quote( $name );
        $sqlValue = $this->database->escape( $value );
        
        return $this->database->exec("
            UPDATE
                `{$this->tbl['course_properties']}`
            SET
                `value` = {$sqlValue}
            WHERE
                `name` = {$sqlName}
            AND
                `category` = 'ICEPC'
        ");
    }
}
