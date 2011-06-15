<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.6.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class that represents a library
 * @const LIB_PUBLIC
 * @const LIB_RESTRICTED
 * @const LIB_PRIVATE
 * @property int $id
 * @property string $title
 * @property string $status
 */
class Library
{
    const LIB_PUBLIC = 'public';
    const LIB_RESTRICTED = 'restricted';
    const LIB_PRIVATE = 'private';
    
    protected $id;
    protected $title;
    protected $status;
    
    protected $database;
    
    /**
     * Contructor
     * @param int $userId
     * @param int $id
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
                status
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
        $this->status = $resultSet[ 'status' ];
    }
    
    /**
     * Getter for library id
     * @return int $libraryId
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Getter for library title
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Getter for status
     * @return string $status
     */
    public function getStatus()
    {
        return $this->status;
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
     * @param string $status
     * @return boolean true on success
     */
    public function setStatus( $status = self::LIB_PRIVATE )
    {
        if ( $status != self::LIB_PUBLIC
            && $status != self::LIB_RESTRICTED
            && $status != self::LIB_PRIVATE )
        {
            throw new Exception( 'Invalid status' );
        }
        
        return $this->status = $status;
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
                    status = " . $this->database->quote( $this->status );
        
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