<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 1.1.5 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class to manage the library entries within a course's bibliography
 * @property string $courseId
 * @property array $libraryList
 */
class CourseLibrary
{
    protected $courseId;
    protected $libraryList;
    
    protected $database;
    
    /**
     * Constructor
     * @param string courseId
     */
    public function __construct( $database , $courseId )
    {
        $this->database = $database;
        $this->tbl = get_module_main_tbl( array( 'library_course_library' ) );
        
        $this->courseId = $courseId;
        $this->load();
    }
    
    /**
     * Loads the library list
     * This method is called by the constructor
     */
    public function load()
    {
        $this->libraryList = array();
        
        $result = $this->database->query( "
            SELECT
                library_id,
                title
            FROM
                `{$this->tbl['library_course_library']}`
            WHERE
                course_id = " . $this->database->quote( $this->courseId )
        );
        
        foreach( $result as $line )
        {
            $this->libraryList[ $line[ 'library_id' ] ] = $line[ 'title' ];
        }
    }
    
    /**
     * Gets the library list
     * @return array $libraryList
     */
    public function getLibraryList( $force = false )
    {
        if ( $force )
        {
            $this->load();
        }
        
        return $this->libraryList;
    }
    
    /**
     * Controls if the specified entry exists
     * @param int $libraryId
     * @return boolean true if exists
     */
    public function libraryExists( $libraryId )
    {
        return $this->database->query( "
            SELECT
                library_id
            FROM
                `{$this->tbl['library_course_library']}`
            WHERE
                library_id = " . $this->database->escape( $libraryId ) . "
            AND
                course_id = " . $this->database->quote( $this->courseId )
        )->numRows();
    }
    
    /**
     * Adds a new entry
     * @param int $libraryId
     * @param string $title
     * @return boolean true on success
     */
    public function add( $libraryId , $title )
    {
        if ( ! $this->libraryExists( $libraryId ) )
        {
            return $this->database->exec( "
                INSERT INTO
                    `{$this->tbl['library_course_library']}`
                SET
                    library_id = " . $this->database->escape( $libraryId ) . ",
                    course_id = " . $this->database->quote( $this->courseId ) . ",
                    title = " . $this->database->quote( $title ) );
        }
    }
    
    /**
     * Removes an existing entry
     * @param int $libraryId
     * @return boolean true on success
     */
    public function remove( $libraryId )
    {
        return $this->database->exec( "
            DELETE FROM
                `{$this->tbl['library_course_library']}`
            WHERE
                library_id = " . $this->database->escape( $libraryId ) . "
            AND
                course_id = " . $this->database->quote( $this->courseId ) );
    }
}