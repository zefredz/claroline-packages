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

class EpcCodeToQuery
{

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
 * BAsic class to execute queries against the EPC REST web service 
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

class EpcQueryHelper extends EpcServiceQuery
{

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

abstract class EpcServiceXmlResponse
{

    protected $xml;

    public function __construct ( $queryResponse )
    {
        $this->xml = simplexml_load_string ( $queryResponse );
    }

    public function getStudents ()
    {
        return $this->xml->xpath ( 'etudiant' );
    }

    public function getNumberOfRecords ()
    {
        return count ( $this->getStudents () );
    }

    public function getIterator ()
    {
        return new EpcServiceStudentsIterator ( $this->getStudents () );
    }

    abstract public function getInfo ();
}

class EpcServiceStudentsInCourse extends EpcServiceXmlResponse
{

    public function __construct ( $queryResponse )
    {
        parent::__construct ( $queryResponse );
    }

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

    public function __construct ( $queryResponse )
    {
        parent::__construct ( $queryResponse );
    }

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

class EpcServiceStudentRecord
{

    protected $xmlRecord;

    public function __construct ( $xmlRecord )
    {
        $this->xmlRecord = $xmlRecord;
    }

    // Let's do some magic here :)
    public function __get ( $name )
    {
        if ( $name == 'username' )
        {
            return $this->xmlRecord->uidLDAP;
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
            return isset ( $this->xmlRecord->noma ) ? $this->xmlRecord->noma : null;
        }
        elseif ( $name == 'siglAnet' )
        {
            return isset ( $this->xmlRecord->siglAnet ) ? $this->xmlRecord->siglAnet : null;
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

    protected function getOfficialCode ()
    {
        return ltrim ( '0', (string) $this->xmlRecord->matriculeFgs );
    }

}

class EpcServiceStudentsIterator extends RowToObjectArrayIterator
{

    public function current ()
    {
        return new EpcServiceStudentRecord ( $this->collection[ $this->key () ] );
    }

}

class EpcStudentListService
{

    protected
        $baseUrl,
        $username = '',
        $password = '',
        $epcQuery;

    public function __construct ( $baseUrl, $username = '', $password = '' )
    {
        $this->baseUrl  = $baseUrl;
        $this->username = $username;
        $this->password = $password;

        $this->epcQuery = new EpcQueryHelper ( $baseUrl, $username, $password );
    }

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

    public function getInfo ()
    {
        return $this->epcQuery->getInfo ();
    }

    public function getRawResponse ()
    {
        return $this->epcQuery->getResponse ();
    }

}
