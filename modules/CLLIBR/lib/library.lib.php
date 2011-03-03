<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.2.7 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class the represents an library
 * @property int $id
 * @property int userId
 * @property string $title
 * @property array $librarianList
 * @property boolean $is_public
 */
class Library
{
    protected $id;
    protected $title;
    protected $is_public;
    protected $librarianList = array();
    
    /**
     * Contructor
     * @param int $userId
     * @param int id
     */
    public function __construct( $id = null )
    {
        $this->tbl = get_module_main_tbl( array( 'library_library'
                                               , 'library_librarian' ) );
        
        if ( $id )
        {
            $this->load( $id );
            $this->id = $id;
        }
    }
    
    /**
     * Loads datas
     * This method is called by the constructor when it received an ID
     * @param int $id
     */
    public function load( $id )
    {
        $resultSet = Claroline::getDatabase()->query( "
            SELECT
                title,
                is_public
            FROM
                `{$this->tbl['library_library']}`
            WHERE
                id = " . Claroline::getDatabase()->escape( $id )
        )->fetch( Database_ResultSet::FETCH_ASSOC );
        
        if ( empty( $resultSet ) )
        {
            throw new Exception( 'These library does not exist' );
        }
        
        $this->title = $resultSet[ 'title' ];
        $this->is_public = (boolean)$resultSet[ 'is_public' ];
        
        $this->librarianList = array();
        
        $resultSet = Claroline::getDatabase()->query( "
            SELECT
                user_id
            FROM
                `{$this->tbl['library_librarian']}`
            WHERE
                library_id = " . Claroline::getDatabase()->escape( $id )
        );
        
        foreach( $resultSet as $line )
        {
            $this->librarianList[] = $line[ 'user_id' ];
        }
    }
    
    /**
     * Getter for library id
     * @return $libraryId
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Verifies if specified user is librarian of this library
     * @param $int $userId
     * @return boolean true if is librarian
     */
    public function isLibrarian( $userId )
    {
        return in_array( $userId , $this->librarianList );
    }
    
    /**
     * Verifes if specified user is allowed to access to this library
     * @param int $userId
     * @return boolean true if is allowed
     */
    public function isAllowed( $userId )
    {
        return $this->isLibrarian( $userId ) || $this->isPublic();
    }
    
    /**
     * Getter for library title
     * @return string $this->Title
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Setter for library title
     * @param string $title
     * @return boolean true on success
     */
    public function setTitle( $title )
    {
        return $this->title = $title;
    }
    
    /**
     * Setter for library status
     * true -> public library
     * false -> private library
     * @param boolean $is_public
     * @return boolean true on success
     */
    public function setPublic( $isPublic = false )
    {
        return $this->is_public = (boolean)$isPublic;
    }
    
    /**
     * Verifies if the library is public
     * @retrun boolean true if public
     */
    public function isPublic()
    {
        return $this->is_public;
    }
    
    /**
     * Getter for librarian list
     * @return array $librarianList
     */
    public function getLibrarianList( $force = false )
    {
        if ( $force )
        {
            $this->load();
        }
        
        return $this->librarianList;
    }
    
    /**
     * Adds a librarian to the library
     * @param int $userId
     * @param int $userId
     */
    public function addLibrarian( $userId )
    {
        if ( in_array( $userId , $this->librarianList ) )
        {
            throw new Exception( 'librarian already exists' );
        }
        
        if ( Claroline::getDatabase()->exec( "
                INSERT INTO
                    `{$this->tbl['library_librarian']}`
                set
                    user_id = " . Claroline::getDatabase()->escape( $userId ) . ",
                    library_id = " . Claroline::getDatabase()->escape( $this->id ) ) )
        {
            return $this->librarianList[] = $userId;
        }
    }
    
    /**
     * Removes a librarian
     * @param int $userId
     * @return boolean $is_removed
     */
    public function removeLibrarian( $userId )
    {
        if ( in_array( $userId , $this->librarianList ) )
        {
            throw new Exception( 'librarian does not exist' );
        }
        
        if ( Claroline::getDatabase()->exec( "
                DELETE FROM
                    `{$this->tbl['library_librarian']}`
                WHERE
                    user_id = " . Claroline::getDatabase()->escape( $userId ) . "
                AND
                    library_id = " . Claroline::getDatabase()->escape( $this->id ) ) )
        {
            unset( $this->librarianList[ array_search( $userId , $this->librarianList ) ] );
            return Claroline::getDatabase()->affectedRows();
        }
    }
    
    /**
     * Deletes the library
     * @return boolean true on success
     */
    public function delete()
    {
        return Claroline::getDatabase()->exec( "
            DELETE FROM
                `{$this->tbl['library_library']}`
            WHERE
                id = " . Claroline::getDatabase()->quote( $this->id ) )
        && Claroline::getDatabase()->exec( "
            DELETE FROM
                `{$this->tbl['library_librarian']}`
            WHERE
                library_id = " . Claroline::getDatabase()->quote( $this->id ) );
    }
    
    /**
     * Save the library datas in DB
     * @return boolean true on success
     */
    public function save()
    {
        $sql = "\n    `{$this->tbl['library_library']}`
                SET
                    title = " . Claroline::getDatabase()->quote( $this->title ) . ",
                    is_public = " . Claroline::getDatabase()->escape( (int)$this->is_public );
        
        if ( $this->id )
        {
            Claroline::getDatabase()->exec( "
                UPDATE" . $sql . "
                WHERE
                    id = " . Claroline::getDatabase()->escape( $this->id )
            );
            
            return Claroline::getDatabase()->affectedRows();
        }
        else
        {
            Claroline::getDatabase()->exec( "
                INSERT INTO " . $sql
            );
            
            return $this->id = Claroline::getDatabase()->insertId();
        }
    }
}