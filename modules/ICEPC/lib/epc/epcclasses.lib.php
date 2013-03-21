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

class EpcClassNameToQuery extends EpcCodeToQuery
{
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

class EpcClassName
{
    private 
        $acadYear, 
        $code, 
        $type;
    
    public function __construct( $epcType, $epcAcadYear, $epcCode )
    {
        $this->acadYear = $epcAcadYear;
        $this->type = $epcType;
        $this->code = $epcCode;
    }
    
    public function getEpcClassType()
    {
        return $this->type;
    }
    
    public function getEpcAcademicYear()
    {
        return $this->acadYear;
    }
    
    public function getEpcCourseOrProgramCode()
    {
        return $this->code;
    }
    
    public function __toString ()
    {
        return "epc_{$this->type}:{$this->acadYear}:{$this->code}";
    }
    
    /*public function toEpcQuery()
    {
        if ( $this->type == 'course' )
        {
            return EpcCodeToQuery::getCourseQuery( $this->code, $this->acadYear );
        }
        else
        {
            return EpcCodeToQuery::getProgramQuery( $this->code, $this->acadYear );
        }
    }*/
    
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

class EpcClass
{
    protected $database, $epcName, $classId, $associatedClass;
    
    public function __construct( $epcName, $database = null )
    {
        $this->database = $database ? $database : Claroline::getDatabase();      
        $this->epcName = $epcName;
        $this->classId = null;
        $this->associatedClass = null;
    }
    
    public function getName()
    {
        return $this->epcName;
    }
    
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

class EpcCourseClassList
{
    private
        $database,
        $courseId;
    
    public function __construct ( $courseId, $database = null )
    {
        $this->courseId = $courseId;
        $this->database = $database ? $database : Claroline::getDatabase();
    }
    
    public function getEpcClassList()
    {
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
                cc.courseId = ".$this->database->quote($this->courseId)."
        ");
    }
}
