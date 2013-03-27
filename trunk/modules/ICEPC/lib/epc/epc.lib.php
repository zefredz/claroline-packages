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
        elseif ( $name == 'siglAnet' )
        {
            return isset ( $this->xmlRecord->siglAnet ) ? (string) $this->xmlRecord->siglAnet : null;
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
    protected function getOfficialCode ()
    {
        return ltrim ( '0', (string) $this->xmlRecord->matriculeFgs );
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
}

class EpcCourseUserListInfo
{
    private $database, $courseId;
    
    public function __construct ( $courseId, $database = null)
    {
        $this->courseId = $courseId;
        $this->database = $database ? $database : Claroline::getDatabase();
    }
    
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