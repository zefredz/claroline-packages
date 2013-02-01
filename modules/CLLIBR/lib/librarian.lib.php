<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 1.1.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class that represents a librarian
 * @property int $libraryId
 * @property array $librarianList
 */
class Librarian
{
    protected $libraryId;
    protected $librarianList;
    
    protected $database;
    
    /**
     * Constructor
     * @param int $userId
     * @param int $libraryId
     */
    public function __construct( $database , $libraryId )
    {
        $this->database = $database;
        $this->tbl = get_module_main_tbl( array( 'library_librarian' ) );
        
        $this->libraryId = $libraryId;
    }
    
    /**
     * Getter for librarianList
     * @return array $librarianList
     */
    public function getLibrarianList()
    {
        return $this->librarianList;
    }
    
    /**
     * Verifies if the user is librarian of the specified library
     * @param int $userId
     * @return boolean true if is librarian
     */
    public function isLibrarian( $userId )
    {
        return $this->database->query( "
            SELECT
                user_id
            FROM
                `{$this->tbl['library_librarian']}`
            WHERE
                library_id = " . $this->database->escape( $this->libraryId ) . "
            AND
                user_id = " . $this->database->escape( $userId )
        )->numRows();
    }
    
    /**
     * Registers the specified user as librarian
     * @param int $userId
     * @return boolean true on success
     */
    public function register( $userId )
    {
        return $this->database->exec( "
                INSERT INTO
                    `{$this->tbl['library_librarian']}`
                SET
                    user_id = " . $this->database->escape( $userId ) .",
                    library_id = " . $this->database->escape( $this->libraryId ) )
            && $this->librarianList[ $userId ] = $userId;
        
    }
    
    /**
     * Unregisters the specified user as librarian
     * @param int $userId
     * @return boolean true on success
     */
    public function unregister( $userId )
    {
        if ( $this->database->exec( "
            DELETE FROM
                `{$this->tbl['library_librarian']}`
            WHERE
                user_id = " . $this->database->escape( $userId ) ."
            AND
                library_id = " . $this->database->escape( $this->libraryId ) ) )
        {
            unset( $this->librarianList[ $userId ] );
        }
        
        return $this->database->affectedRows();
    }
    
    /**
     * Unsubscribe all the librarians
     * @return int $affectedRows
     */
    public function wipe()
    {
        if ( $this->database->exec( "
            DELETE FROM
                `{$this->tbl['library_librarian']}`
            WHERE
                library_id = " . $this->database->escape( $this->libraryId ) ) )
        {
            $this->librarianList = array();
        }
        
        return $this->database->affectedRows();
    }
}