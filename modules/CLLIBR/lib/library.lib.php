<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.3.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class that represents a library
 * @property int $id
 * @property int userId
 * @property string $title

 * @property boolean $is_public
 */
class Library
{
    protected $id;
    protected $title;
    protected $is_public;
    
    protected $database;
    
    /**
     * Contructor
     * @param int $userId
     * @param int id
     */
    public function __construct( $database , $id = null )
    {
        $this->database = $database;
        $this->tbl = get_module_main_tbl( array( 'library_library' ) );
        
        if ( $id )
        {
            $this->id = $id;
            $this->load();
        }
    }
    
    /**
     * Loads datas
     * This method is called by the constructor when it received an ID
     * @param int $id
     */
    public function load()
    {
        $resultSet = $this->database->query( "
            SELECT
                title,
                is_public
            FROM
                `{$this->tbl['library_library']}`
            WHERE
                id = " . $this->database->escape( $this->id )
        )->fetch( Database_ResultSet::FETCH_ASSOC );
        
        if ( empty( $resultSet ) )
        {
            throw new Exception( 'These library does not exist' );
        }
        
        $this->title = $resultSet[ 'title' ];
        $this->is_public = (boolean)$resultSet[ 'is_public' ];
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
     * Deletes the library
     * @return boolean true on success
     */
    public function delete()
    {
        return $this->database->exec( "
            DELETE FROM
                `{$this->tbl['library_library']}`
            WHERE
                id = " . $this->database->quote( $this->id ) );
    }
    
    /**
     * Save the library datas in DB
     * @return boolean true on success
     */
    public function save()
    {
        $sql = "\n    `{$this->tbl['library_library']}`
                SET
                    title = " . $this->database->quote( $this->title ) . ",
                    is_public = " . $this->database->escape( (int)$this->is_public );
        
        if ( $this->id )
        {
            $this->database->exec( "
                UPDATE" . $sql . "
                WHERE
                    id = " . $this->database->escape( $this->id )
            );
            
            return $this->database->affectedRows();
        }
        else
        {
            $this->database->exec( "
                INSERT INTO " . $sql
            );
            
            return $this->id = $this->database->insertId();
        }
    }
}