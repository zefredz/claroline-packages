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
 * A class that represents the metadatas
 * related to a specified resource
 * @const KEYWORD
 * @const COLLECTION
 * @property string $resourceId
 * @property array $metadataList
 */
class Metadata
{
    const KEYWORD = 'keyword';
    const COLLECTION = 'collection';
    
    protected $resourceId;
    protected $metadataList;
    
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
                id,
                name,
                value
            FROM
                `{$this->tbl['library_metadata']}`
            WHERE
                resource_id = " . $this->database->escape( $this->resourceId ) . "
            ORDER BY
                id ASC"
        );
        
        foreach( $result as $line )
        {
            $this->metadataList[ $line[ 'id' ] ] = array( 'name' => $line[ 'name' ]
                                                       , 'value' => $line[ 'value' ] );
        }
    }
    
    /**
     * Export metadatas
     * @return array $metadatas
     */
    public function export()
    {
        $metadatas = array();
        
        if ( ! empty( $this->metadataList ) )
        {
            foreach( $this->metadataList as $id => $metadata )
            {
                $metadatas[ $metadata[ 'name' ] ][ $id ] = $metadata[ 'value' ];
            }
        }
        
        return $metadatas;
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
                    name = " . $this->database->quote( $name ) . ",
                    value = " . $this->database->quote( $value ) ) )
        {
            
            return $this->metadataList[ $this->database->insertId() ] = array( 'name' => $name
                                                                             , 'value' => $value );
        }
    }
    
    /**
     * Get the values for a specified metadata
     * @param string $name
     * @return array $values
     */
    public function get( $name )
    {
        $values = array();
        
        foreach( $this->metadataList as $metadata )
        {
            if ( $metadata[ 'name' ] == $name )
            {
                $values[] = $metadata[ 'value' ];
            }
        }
        
        return $values;
    }
    
    /**
     * Removes a metadata
     * @param int $id
     * @return boolean true on success
     */
    public function remove( $id )
    {
        if ( $this->database->exec( "
            DELETE FROM
                `{$this->tbl['library_metadata']}`
            WHERE
                resource_id = " . $this->database->quote( $this->resourceId ) . "
            AND
                id = " . $this->database->escape( $id ) ) )
        {
            unset( $this->metadataList[ $id ] );
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
            unset( $this->metadataList );
        }
        
        return $this->database->affectedRows();
    }
    
    /**
     * Modifies a specified metadata
     * @param int $id
     * @param string $value
     * @return boolean true on success
     */
    public function modify( $id , $value )
    {
        if ( $this->database->exec( "
            UPDATE
                `{$this->tbl['library_metadata']}`
            SET
                value = " . $this->database->quote( $value ) . "
            WHERE
                id = " . $this->database->escape( $id ) ) )
        {
            return $this->metadataList[ $id ][ 'value' ] = $value;
        }
    }
    
    /**
     * Gets all property names
     */
    public function getAllProperties()
    {
        return $this->database->query( "
            SELECT
                DISTINCT name
            FROM
                `{$this->tbl['library_metadata']}`"
        );
    }
    
    /**
     * Gets all the values associated with the specified metadata
     * @param string $name
     * @return Resultset
     */
    public function getValues( $name )
    {
        return $this->database->query( "
            SELECT
                DISTINCT value
            FROM
                `{$this->tbl['library_metadata']}`
            WHERE
                name = " . $this->database->quote( $name )
        );
    }
    
    /**
     * Gets all existing keywords
     * Helper for getValues( self::KEYWORD )
     */
    public function getAllKeywords()
    {
        return $this->getValues( self::KEYWORD );
    }
    
    /**
     * Gets the collection list the resource belongs to
     * Helper for getValues( self::COLLECTION )
     */
    public function getAllCollections()
    {
        return $this->getValues( self::COLLECTION );
    }
    
    /**
     * Verifies if a metadate already exists
     * @param string $name
     * @param string $value
     * @return boolean true if exists
     */
    public function metadataExists( $name , $value )
    {
        return in_array( array( 'name' => $name , 'value' => $value )
                       , $this->metadataList );
    }
}