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
 * A class that represents a resource collection
 * belonging to libraries, bibliography or bookmarks
 * @const LIBRARY_COLLECTION
 * @const COURSE_COLLECTION
 * @const USER_COLLECTION
 * @property $type
 * @property $refId;
 * @property $resourceList;
 */
class Collection
{
    const LIBRARY_COLLECTION = 'catalogue';
    const COURSE_COLLECTION = 'bibliography';
    const USER_COLLECTION = 'bookmark';
    
    protected $type;
    protected $refId;
    protected $resourceList = array();
    
    protected $database;
    
    /**
     * Constructor
     * @param string $type
     * @param string $refId
     */
    public function __construct( $database , $type , $refId )
    {
        $this->database = $database;
        $this->tbl = get_module_main_tbl( array( 'library_collection' ) );
        
        if ( $type != self::LIBRARY_COLLECTION
          && $type != self::COURSE_COLLECTION
          && $type != self::USER_COLLECTION )
        {
            throw new Exception( 'Invalid type' );
        }
        
        $this->type = $type;
        $this->refId = $refId;
        
        $this->load();
    }
    
    /**
     * Loads datas
     * This method is called by the constructor when it received an ID
     */
    protected function load()
    {
        $this->resourceList = array();
        
        $resultSet = $this->database->query( "
            SELECT
                resource_id
            FROM
                `{$this->tbl['library_collection']}`
            WHERE
                type = " . $this->database->quote( $this->type ) . "
            AND
                ref_id = " . $this->database->quote( $this->refId )
        );
        
        foreach( $resultSet as $line )
        {
            $this->resourceList[ $line[ 'resource_id' ] ] = new Metadata( $this->database , $line[ 'resource_id' ] ); // TODO Try to remove this dependency
            //$this->resourceList[ $line[ 'resource_id' ] ] = $line[ 'resource_id' ];
        }
    }
    
    /**
     * Getter for $refId
     * @return int $refId
     */
    public function getRefId()
    {
        return $this->refId;
    }
    
    /**
     * Getter for resource list
     * @return array $resourceList
     */
    public function getResourceList( $force = false )
    {
        if ( $force )
        {
            $this->load();
        }
        
        return $this->resourceList;
    }
    
    /**
     * Getter for static value $_type
     * @return string $type
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Verifies if specified resource is in the resource set
     * @param $ResourceId
     * @return boolean true if exists
     */
    public function resourceExists( $resourceId )
    {
        return array_key_exists( $resourceId , $this->resourceList );
    }
    
    /**
     * Add a resource in the resource set
     * @param Resource $resource
     * @retrn boolean true on success
     */
    public function add( $resourceId )
    {
        if ( $this->database->exec( "
            INSERT INTO
                `{$this->tbl['library_collection']}`
            SET
                type = " . $this->database->quote( $this->type ) . ",
                ref_id = " . $this->database->quote( $this->refId ) . ",
                resource_id = " . $this->database->escape( $resourceId ) ) )
        {
            return $this->resourceList[ $resourceId ] = new Metadata( $this->database , $resourceId ); // TODO Try to remove this dependency
            //return $this->resourceList[ $resourceId ] = $resourceId;
        }
    }
    
    /**
     * Removes a resource from the resource collection
     * @param int $resourceId
     * @return boolean true on success
     */
    public function remove( $resourceId )
    {
        unset( $this->resourceList[ $resourceId ] );
        
        $this->database->exec( "
            DELETE FROM
                `{$this->tbl['library_collection']}`
            WHERE
                type = " . $this->database->quote( $this->type ) . "
            AND
                ref_id = " . $this->database->quote( $this->refId ) . "
            AND
                resource_id = " . $this->database->escape( $resourceId ) );
        
        return $this->database->affectedRows();
    }
    
    /**
     * Remove all the resources from the resource collection
     * @return boolean true on success
     */
    public function removeAll()
    {
        $this->database->exec( "
            DELETE FROM
                `{$this->tbl['library_collection']}`
            WHERE
                type = " . $this->database->quote( $this->type ) . "
            AND
                ref_id = " . $this->database->quote( $this->refId ) );
        
        return $this->database->affectedRows();
    }
}