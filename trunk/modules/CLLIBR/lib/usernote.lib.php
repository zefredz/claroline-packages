<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.8.8 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class that represents a user's note on a resource
 * @property int $userId
 * @property int $resourceId
 * @property string $content
 */
class UserNote
{
    protected $userId;
    protected $resourceId;
    protected $content;
    
    protected $database;
    
    /**
     * Contructor
     * @param Database $database
     * @param int $userId
     * @param int $resourceId
     */
    public function __construct( $database , $userId , $resourceId )
    {
        $this->database = $database;
        $this->tbl = get_module_main_tbl( array( 'library_user_note' ) );
        
        $this->userId = $userId;
        $this->resourceId = $resourceId;
        $this->load();
    }
    
    /**
     * Loads the content of the note
     * This method is called by the constructor
     */
    public function load()
    {
        $resultSet = $this->database->query( "
            SELECT
                content
            FROM
                `{$this->tbl['library_user_note']}`
            WHERE
                user_id = " . $this->database->escape( $this->userId ) . "
            AND
                resource_id = " . $this->database->escape( $this->resourceId )
        );
        
        if ( $resultSet->numRows() )
        {
            $this->content = $resultSet->fetch( Database_ResultSet::FETCH_VALUE );
        }
    }
    
    /**
     * Getter for the note's content
     * @param boolean $force
     * @return string $content
     */
    public function getContent( $force = false )
    {
        if ( $force )
        {
            $this->load();
        }
        
        return $this->content;
    }
    
    /**
     * Setter for the note's content
     * @param string $content
     * @return boolean true on success
     */
    public function set( $content )
    {
        if ( ! $this->noteExists() )
        {
            $this->create();
        }
        
        $this->content = $content;
        $this->save();
    }
    
    /**
     * Controls if user's note exist
     * @return boolean true if exists
     */
    public function noteExists()
    {
        return ! is_null( $this->content );
    }
    
    /**
     * Create a new note
     * @return boolean true on success
     */
    public function create()
    {
        return $this->database->exec( "
            INSERT INTO
                `{$this->tbl['library_user_note']}`
            SET
                user_id = " . $this->database->escape( $this->userId ) . ",
                resource_id = " . $this->database->escape( $this->resourceId ) . ",
                content = " . $this->database->quote( $this->content ) );
    }
    
    /**
     * Deletes the note
     * @return boolean true on success
     */
    public function delete()
    {
        return $this->database->exec( "
            DELETE FROM
                `{$this->tbl['library_user_note']}`
            WHERE
                user_id = " . $this->database->escape( $this->userId ) . "
            AND
                resource_id = " . $this->database->escape( $this->resourceId ) );
    }
    
    /**
     * Saves the note
     * @return boolean true on success
     */
    public function save()
    {
        return $this->database->exec( "
            UPDATE
                `{$this->tbl['library_user_note']}`
            SET
                content = " . $this->database->quote( $this->content ) . "
            WHERE
                user_id = " . $this->database->escape( $this->userId ) . "
            AND
                resource_id = " . $this->database->escape( $this->resourceId ) );
    }
}