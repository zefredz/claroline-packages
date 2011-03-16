<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.3.1 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class for library managment
 * @property $userId
 * @property $userLibraryList  the list of libraries which the user is librarian
 * @property $publicLibraryList  the list of public libraries
 */
class LibraryList
{
    protected $userId;
    protected $userLibraryList;
    protected $publicLibraryList;
    
    protected $database;
    
    /**
     * Constructor
     * @param $userId
     */
    public function __construct( $database , $userId )
    {
        $this->database = $database;
        $this->tbl = get_module_main_tbl( array( 'library_library' , 'library_librarian' , 'user' ) );
        
        if ( $userId )
        {
            $this->userId = $userId;
            $this->load();
        }
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
                LY.is_public
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
            $this->userLibraryList[ $id ][ 'is_public' ] = $library[ 'is_public' ];
            $this->userLibraryList[ $id ][ 'librarianList' ] = $this->getLibrarianList( $id );
        }
        
        $publicLibraryList = $this->database->query( "
            SELECT
                LY.id,
                LY.title
            FROM
                `{$this->tbl['library_library']}` AS LY
            LEFT JOIN
                `{$this->tbl['library_librarian']}` AS LN
            ON
                LY.id = LN.library_id
            WHERE
                LY.is_public = TRUE
            AND
                LN.user_id != " . $this->database->escape( $this->userId )
        );
        
        foreach( $publicLibraryList as $library )
        {
            $id = $library[ 'id' ];
            $this->publicLibraryList[ $id ][ 'title' ] = $library[ 'title' ];
            $this->publicLibraryList[ $id ][ 'librarianList' ] = $this->getLibrarianList( $id );
        }
    }
    
    /**
     * Getter for the two library list in an array
     * @return array $resourceList
     */
    public function getResourceList( $force = false )
    {
        if( $force )
        {
            $this->load();
        }
        
        return array( 'user' => $this->userLibraryList
                    , 'public'  => $this->publicLibraryList );
    }
    
    /**
     * Gets librarian list
     * @return array $librarianList
     */
    public function getLibrarianList( $libraryId )
    {
        $result =  $this->database->query( "
            SELECT
                L.user_id,
                U.nom,
                U.prenom
            FROM
                `{$this->tbl['library_librarian']}` AS L,
                `{$this->tbl['user']}` AS U
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