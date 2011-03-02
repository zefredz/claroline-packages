<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.2.5 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
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
    protected static $defaultMetadataList = array( 'author',
                                                   'description',
                                                   'date',
                                                   'publisher' );
    
    protected $resourceUid;
    protected $metadataList;
    
    /**
     * Constructor
     * @param string $resourceUid
     */
    public function __construct( $resourceUid )
    {
        $this->tbl = get_module_main_tbl( array( 'library_metadata' ) );
        
        $this->resourceUid = $resourceUid;
        
        $this->load();
    }
    
    /**
     * Loads metadatas
     * This method is called by the constructor
     */
    public function load()
    {
        $this->metadataList = array();
        
        $result = Claroline::getDatabase()->query( "
            SELECT
                id,
                name,
                value
            FROM
                `{$this->tbl['library_metadata']}`
            WHERE
                resource_uid = " . Claroline::getDatabase()->quote( $this->resourceUid )
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
        if ( Claroline::getDatabase()->exec( "
                INSERT INTO
                    `{$this->tbl['library_metadata']}`
                SET
                    resource_uid = " . Claroline::getDatabase()->quote( $this->resourceUid ) . ",
                    name = " . Claroline::getDatabase()->quote( $name ) . ",
                    value = " . Claroline::getDatabase()->quote( $value ) ) )
        {
            
            return $this->metadataList[ Claroline::getDatabase()->insertId() ] = array( 'name' => $name
                                                                                     , 'value' => $value );
        }
    }
    
    /**
     * Removes a metadata
     * @param int $id
     * @return boolean true on success
     */
    public function remove( $id )
    {
        if ( Claroline::getDatabase()->exec( "
            DELETE FROM
                `{$this->tbl['library_metadata']}`
            WHERE
                resource_uid = " . Claroline::getDatabase()->quote( $id ) ) )
        {
            unset( $this->metadataList[ $id ] );
            
            return Claroline::getDatabase()->affectedRows();
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
        if ( Claroline::getDatabase()->exec( "
            UPDATE
                `{$this->tbl['library_metadata']}`
            SET
                value = " . Claroline::getDatabase()->quote( $value ) . "
            WHERE
                id = " . Claroline::getDatabase()->escape( $id ) ) )
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