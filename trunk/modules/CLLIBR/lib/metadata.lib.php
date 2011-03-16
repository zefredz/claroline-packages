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
 * A class that represents the metadatas
 * related to a specified resource
 * @static array $defaultMetadataList
 * @property string $resourceUid
 * @property array $metaDataList
 */
class Metadata
{
    protected static $defaultMetadataList = array( 'title',
                                                   'author',
                                                   'description',
                                                   'publication date',
                                                   'publisher' );
    
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
                resource_id = " . $this->database->escape( $this->resourceId )
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
        
        foreach( $this->metadataList as $id => $metadata )
        {
            $metadatas[ $metadata[ 'name' ] ][ $id ] = $metadata[ 'value' ];
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
                resource_id = " . $this->database->quote( $id ) ) )
        {
            unset( $this->metadataList[ $id ] );
            
            return $this->database->affectedRows();
        }
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
     * @static Getter for self::$defaultMetadataList
     */
    public static function getDefaultMetadataList()
    {
        return self::$defaultMetadataList;
    }
}