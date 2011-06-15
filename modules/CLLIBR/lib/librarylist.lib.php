<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.6.2 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class for library managment
 * @property int $userId
 * @property boolean $is_admin
 * @property array $userLibraryList  the list of libraries which the user is librarian
 * @property array $allowedLibraryList  the list of public libraries
 */
class LibraryList
{
    protected $userId;
    protected $is_admin;
    protected $userLibraryList;
    protected $allowedLibraryList;
    
    protected $database;
    
    /**
     * Constructor
     * @param $userId
     * @param boolean $is_admin
     */
    public function __construct( $database , $userId , $is_admin = false )
    {
        $this->database = $database;
        $this->tbl = get_module_main_tbl( array( 'library_library' , 'library_librarian' , 'user' ) );
        
        $this->userId = $userId;
        $this->is_admin = $is_admin;
        $this->load();
    }
    
    /**
     * Loads datas
     * This method is called by the constructor when it received an ID
     * @param int $userId
     */
    protected function load()
    {
        $userLibraryList = $this->database->query( "
            SELECT
                LY.id,
                LY.title,
                LY.status
            FROM
                `{$this->tbl['library_library']}` AS LY
            LEFT JOIN
                `{$this->tbl['library_librarian']}` AS LN
            ON
                LY.id = LN.library_id
            WHERE
                LN.user_id = " . $this->database->escape( $this->userId )
        );
        
        foreach( $userLibraryList as $library )
        {
            $id = $library[ 'id' ];
            $this->userLibraryList[ $id ][ 'title' ] = $library[ 'title' ];
            $this->userLibraryList[ $id ][ 'status' ] = $library[ 'status' ];
            $this->userLibraryList[ $id ][ 'librarianList' ] = $this->getLibrarianList( $id );
        }
        
        $where = $this->is_admin
               ? ""
               : "\nWHERE LY.status != " . $this->database->quote( Library::LIB_PRIVATE );
        
        $allowedLibraryList = $this->database->query( "
            SELECT
                LY.id,
                LY.title,
                LY.status
            FROM
                `{$this->tbl['library_library']}` AS LY
            LEFT JOIN
                `{$this->tbl['library_librarian']}` AS LN
            ON
                LY.id = LN.library_id" . $where
        );
        
        foreach( $allowedLibraryList as $library )
        {
            $id = $library[ 'id' ];
            $this->allowedLibraryList[ $id ][ 'title' ] = $library[ 'title' ];
            $this->allowedLibraryList[ $id ][ 'librarianList' ] = $this->getLibrarianList( $id );
            $this->allowedLibraryList[ $id ][ 'status' ] = $library[ 'status' ];
        }
    }
    
    /**
     * Getter for the two library list in an array
     * @param boolean $force
     * @return array $resourceList
     */
    public function getResourceList( $force = false )
    {
        if( $force )
        {
            $this->load();
        }
        
        return array( 'user' => $this->userLibraryList
                    , $this->is_admin ? 'other' : 'allowed'  => $this->allowedLibraryList );
    }
    
    /**
     * Gets librarian list
     * @param int $libraryId
     * @return array $librarianList
     */
    public function getLibrarianList( $libraryId )
    {
        $result =  $this->database->query( "
            SELECT
                U.user_id,
                U.nom,
                U.prenom
            FROM
                `{$this->tbl['user']}` AS U
            LEFT JOIN
                `{$this->tbl['library_librarian']}` AS L
            ON
                U.user_id = L.user_id
            WHERE
                L.library_id = " . $this->database->escape( $libraryId ) );
        
        $librarianList = array();
        
        foreach( $result as $line )
        {
            $librarianList[ $line[ 'user_id' ] ] = $line[ 'prenom' ] . ' ' . $line[ 'nom' ];
        }
        
        return $librarianList;
    }
}