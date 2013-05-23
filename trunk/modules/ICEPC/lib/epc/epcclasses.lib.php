<?php 

// $Id$

/**
 * CLAROLINE
 *
 * @version 0.1 $Revision$
 * @copyright (c) 2013 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package ICEPC
 * @author Frederic Minne <zefredz@claroline.net>
 *
 */

/**
 * EPC class name to query translator helper
 */
class EpcClassNameToQuery extends EpcCodeToQuery
{
    /**
     * Get EPC query from class name
     * @param EpcClassName $className
     * @return string
     */
    public static function getQueryFromName( EpcClassName $className )
    {
        if ( $className->getEpcClassType () == EPC_TYPE_COURSE )
        {
            return self::getCourseQuery( $className->getEpcCourseOrProgramCode(), $className->getEpcAcademicYear() );
        }
        else
        {
            return self::getProgramQuery( $className->getEpcCourseOrProgramCode(), $className->getEpcAcademicYear() );
        }
    }
}

/**
 * EPC class name
 */
class EpcClassName
{
    private 
        $acadYear, 
        $code, 
        $type;
    
    /**
     * An EPC class is given by a type ('course' or 'program'), a start of academic year year and a code
     * @param string $epcType type of class, accepted values are 'course' or 'program'
     * @param string $epcAcadYear year on which the wanted academic year started
     * @param string $epcCode code of the EPC course or program corresponding to the EPC class
     */
    public function __construct( $epcType, $epcAcadYear, $epcCode )
    {
        $this->acadYear = $epcAcadYear;
        $this->type = $epcType;
        $this->code = $epcCode;
    }
    
    /**
     * Get EPC class type
     * @return string
     */
    public function getEpcClassType()
    {
        return $this->type;
    }
    
    /**
     * Get year of the start of the academic year
     * @return string
     */
    public function getEpcAcademicYear()
    {
        return $this->acadYear;
    }
    
    /**
     * Get course or program code
     * @return string
     */
    public function getEpcCourseOrProgramCode()
    {
        return $this->code;
    }
    
    /**
     * Get EPC class name as a string of format epc_course|program:year:code
     * @return string
     */
    public function __toString ()
    {
        return "epc_{$this->type}:{$this->acadYear}:{$this->code}";
    }
    
    /**
     * Parse a string of format epc_course|program:year:code
     * @param string $string
     * @return \self
     * @throws Exception if not a valid class name
     */
    public static function parse( $string )
    {
        $matches = array();
        
        if ( preg_match( '/epc_(course|program)\:(.+?)\:(.+?)$/', $string, $matches ) )
        {
            $class = new self( $matches[1], $matches[2], $matches[3] );
            return $class;
        }
        else
        {
            throw new Exception ( "Not a valid EPC class name {$string}" );
        }
    }
}

/**
 * Represents an EPC class
 */
class EpcClass
{
    protected $database, $epcName, $classId, $associatedClass;
    
    /**
     * 
     * @param EpcClassName $epcName
     * @param Database_Connection $database database connection or null
     */
    public function __construct( $epcName, $database = null )
    {
        $this->database = $database ? $database : Claroline::getDatabase();      
        $this->epcName = $epcName;
        $this->classId = null;
        $this->associatedClass = null;
    }
    
    /**
     * Get EPC class name
     * @return EpcClassName
     */
    public function getName()
    {
        return $this->epcName;
    }
    
    /**
     * Get the id of the associated Claroline class
     * @return int
     */
    protected function getAssociatedClassId()
    {
        if ( empty( $this->classId ) )
        {
            $tbl = claro_sql_get_main_tbl();

            $this->classId = $this->database->query("
                SELECT 
                    id
                FROM 
                    `" . $tbl['class'] . "`
                WHERE 
                    `name` = ". $this->database->quote( $this->epcName->__toString() ) . "
            ")->fetch( Database_ResultSet::FETCH_VALUE );
        }
        
        return $this->classId;
    }
    
    /**
     * Check if associated Claroline class already exists
     * @return boolean
     */
    public function associatedClassExists()
    {
        $this->getAssociatedClassId(); 
        
        if ( !empty( $this->classId ) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    /**
     * Create associated Claroline class if missing
     * @throws Exception
     */
    public function createAssociatedClass()
    {
        if ( !empty($this->associatedClass) || $this->associatedClassExists () )
        {
            throw new Exception("Cannot create ssociated class : already exists");
        }
        
        $this->associatedClass = new Claro_Class( $this->database );
        $this->associatedClass->setName($this->epcName);
        
        $this->associatedClass->create();
    }
    
    /**
     * Get the associated Claroline class
     * @return Claro_Class
     */
    public function getAssociatedClass()
    {
        if ( !$this->associatedClass )
        {
            $this->associatedClass = new Claro_Class( $this->database );
            $this->associatedClass->load( $this->getAssociatedClassId () );
        }
        
        return $this->associatedClass;
    }
}

/**
 * Represents the list of EPC classes associated with a course
 */
class EpcClassList
{
    private
        $database;
    
    /**
     * 
     * @param string $courseId course code
     * @param Database_Connection $database database connection or null
     */
    public function __construct ( $database = null )
    {
        $this->database = $database ? $database : Claroline::getDatabase();
    }
    
    /**
     * Get the list of EPC classes associated with a course
     * @param string $courseId
     * @return Database_ResultSet
     */
    public function getEpcCourseClassList( $courseId = null )
    {
        $courseId = $courseId ? $courseId : claro_get_current_course_id();
        
        $tbl  = claro_sql_get_main_tbl();
    
        return $this->database->query("
            SELECT
                c.id,
                c.name,
                c.class_parent_id,
                c.class_level
            FROM 
                `{$tbl['rel_course_class']}` AS cc
            LEFT JOIN 
                `{$tbl['class']}` AS c
            ON
                c.id = cc.classId
            AND
                c.name LIKE 'epc_%:%:%'
            WHERE
                cc.courseId = ".$this->database->quote($courseId)."
        ");
    }
    
    /**
     * Get the list of all EPC classes in the platform
     * @return Database_ResultSet
     */
    public function getEpcClassList()
    {
        $tbl  = claro_sql_get_main_tbl();
    
        return $this->database->query("
            SELECT
                c.id,
                c.name,
                c.class_parent_id,
                c.class_level,
                COUNT(*) AS numberOfCourses,
                GROUP_CONCAT(cc.courseId) AS courseIdList
            FROM 
                `{$tbl['rel_course_class']}` AS cc
            LEFT JOIN 
                `{$tbl['class']}` AS c
            ON
                c.id = cc.classId
            AND
                c.name LIKE 'epc_%:%:%'
            WHERE
                1 = 1
            GROUP BY cc.classId
        ");
    }
}
