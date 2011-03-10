<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.2.8 $Revision$ - Claroline 1.9
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
        $this->tbl = get_module_main_tbl( array( 'library_library' , 'library_librarian' ) );
        
        if ( $userId )
        {
            $this->load( $userId );
            $this->userId = $userId;
        }
    }
    
    /**
     * Loads datas
     * This method is called by the constructor when it received an ID
     * @param int $userId
     */
    protected function load( $userId )
    {
        $this->userLibraryList = $this->database->query( "
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
                LN.user_id = " . $this->database->escape( $userId )
        );
        
        $this->publicLibraryList = $this->database->query( "
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
                LN.user_id != " . $this->database->escape( $userId )
        );
    }
    
    /**
     * Getter for the two library list in an array
     * @return array $resourceList
     */
    public function getResourceList( $force = false )
    {
        if( $force )
        {
            $this->load( $this->userId );
        }
        
        return array( 'user' => $this->userLibraryList
                    , 'public'  => $this->publicLibraryList );
    }
}