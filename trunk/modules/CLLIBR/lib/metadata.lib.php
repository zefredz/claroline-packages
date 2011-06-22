<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.7.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class that represents the metadatas
 * related to a specified resource
 * @const KEYWORD
 * @const COLLECTION
 * @const TITLE
 * @const DESCRIPTION
 * @property string $resourceId
 * @property array $metadataList
 */
class Metadata
{
    const KEYWORD = 'keyword';
    const COLLECTION = 'collection';
    const TITLE = 'title';
    const DESCRIPTION = 'description';
    
    protected $resourceId;
    protected $metadataList = array();
    
    protected $database;
    
    /**
     * Constructor
     * @param int $resourceId
     */
    public function __construct( $database , $resourceId )
    {
        $this->database = $database;
        $this->tbl = get_module_main_tbl( array( 'library_metadata' ) );
        
        if ( $resourceId )
        {
            $this->resourceId = $resourceId;
            $this->load();
        }
    }
    
    /**
     * Loads metadatas
     * This method is called by the constructor
     */
    public function load()
    {
        $this->metadataList = array();
        
        $result = $this->database->query( "
            SELECT
                metadata_name AS name,
                metadata_value AS value
            FROM
                `{$this->tbl['library_metadata']}`
            WHERE
                resource_id = " . $this->database->escape( $this->resourceId ) . "
            ORDER BY
                id ASC"
        );
        
        foreach( $result as $line )
        {
            $name = $line[ 'name' ];
            $value = $line[ 'value' ];
            
            if ( $name == self::COLLECTION || $name == self::KEYWORD )
            {
                $this->metadataList[ $name ][] = $value;
            }
            else
            {
                $this->metadataList[ $name ] = $value;
            }
        }
    }
    
    /**
     * Getter for metadataList
     * @return array $metadataList
     */
    public function getMetadataList( $force = false )
    {
        if ( $force )
        {
            $this->load();
        }
        
        return $this->metadataList;
    }
    
    /**
     * Getter for resource id
     * @return int $resourceid
     */
    public function getresourceId()
    {
        return $this->resourceId;
    }
    
    /**
     * Setter for resource id
     * @param $int $resourceId
     * @return int $resourceId if did not exist
     */
    public function setResourceId( $resourceId )
    {
        if ( ! $this->resourceId )
        {
            $this->resourceId = $resourceId;
            $this->load();
            
            return $resourceId;
        }
    }
    
    /**
     * Adds a new metadata
     * @param string $name
     * @param string $value
     * @return boolean true on success
     */
    public function add( $name , $value )
    {
        if ( $this->database->exec( "
                INSERT INTO
                    `{$this->tbl['library_metadata']}`
                SET
                    resource_id = " . $this->database->quote( $this->resourceId ) . ",
                    metadata_name = " . $this->database->quote( $name ) . ",
                    metadata_value = " . $this->database->quote( $value ) ) )
        {
            if ( $name == self::COLLECTION || $name == self::KEYWORD )
            {
                return $this->metadataList[ $name ][] = $value;
            }
            else
            {
                return $this->metadataList[ $name ] = $value;
            }
        }
    }
    
    /**
     * Helper to add a keyword
     * @param string $keyword
     */
    public function addKeyword( $keyword )
    {
        return $this->add( self::KEYWORD , $keyword );
    }
    
    /**
     * Helper to set a metadata
     * @param string $name
     * @param string $value
     * @return boolean true on success
     */
    public function set( $name , $value )
    {
        if ( $this->get( $name ) )
        {
            return $this->modify( $name , $value );
        }
        else
        {
            return $this->add( $name , $value );
        }
    }
    
    /**
     * Helper to set title
     * @param string title
     * @return boolean true on success
     */
    public function setTitle( $title )
    {
        return $this->set( self::TITLE , $title );
    }
    
    /**
     * Helper to set description
     * @param string description
     * @return boolean true on success
     */
    public function setDescription( $description )
    {
        return $this->set( self::DESCRIPTION , $description );
    }
    
    /**
     * Get the values for a specified metadata
     * @param string $name
     * @return array $values
     */
    public function get( $name )
    {
        if ( array_key_exists( $name , $this->metadataList ) )
        {
            return $this->metadataList[ $name ];;
        }
    }
    
    /**
     * Removes a metadata
     * @param string $name
     * @return boolean true on success
     */
    public function remove( $name )
    {
        if ( $this->database->exec( "
            DELETE FROM
                `{$this->tbl['library_metadata']}`
            WHERE
                resource_id = " . $this->database->quote( $this->resourceId ) ) )
        {
            unset( $this->metadataList[ $name ] );
        }
        
        return $this->database->affectedRows();
    }
    
    /**
     * Removes all metadatas
     * @return boolean true on success
     */
    public function removeAll()
    {
        if ( $this->database->exec( "
            DELETE FROM
                `{$this->tbl['library_metadata']}`
            WHERE
                resource_id = " . $this->database->quote( $this->resourceId ) ) )
        {
            $this->metadataList = array();
        }
        
        return $this->database->affectedRows();
    }
    
    /**
     * Modifies a metadata
     * @param string $name
     * @param string $value
     * @return boolean true on success
     */
    public function modify( $name , $value )
    {
        if ( $name == self::COLLECTION || $name == self::KEYWORD )
        {
            throw new Exception( 'Not modifiable property');
        }
        
        if ( $this->database->exec( "
            UPDATE
                `{$this->tbl['library_metadata']}`
            SET
                metadata_value = " . $this->database->quote( $value ) . "
            WHERE
                metadata_name = " . $this->database->quote( $name ) . "
            AND
                resource_id = " . $this->database->escape( $this->resourceId ) ) )
        {
            return $this->metadataList[ $name ] = $value;
        }
    }
    
    /**
     * Gets all property names
     */
    public function getAllProperties()
    {
        return $this->database->query( "
            SELECT
                DISTINCT metadata_name AS name
            FROM
                `{$this->tbl['library_metadata']}`"
        );
    }
    
    /**
     * Gets all the values associated with the specified metadata
     * @param string $name
     * @return Resultset
     */
    public function getValue( $name )
    {
        return $this->database->query( "
            SELECT
                DISTINCT metadata_value AS value
            FROM
                `{$this->tbl['library_metadata']}`
            WHERE
                metadata_name = " . $this->database->quote( $name )
        );
    }
    
    /**
     * Gets all existing keywords
     * Helper for getValues( self::KEYWORD )
     */
    public function getAllKeywords()
    {
        return $this->getValue( self::KEYWORD );
    }
    
    /**
     * Gets the collection list the resource belongs to
     * Helper for getValues( self::COLLECTION )
     */
    public function getAllCollections()
    {
        return $this->getValue( self::COLLECTION );
    }
}