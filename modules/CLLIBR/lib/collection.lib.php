<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 1.1.5 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class that represents a resource collection
 * belonging to libraries, bibliography or bookmarks
 * @const SORT_TITLE
 * @const SORT_AUTHOR
 * @const LIBRARY_COLLECTION
 * @const COURSE_COLLECTION
 * @const USER_COLLECTION
 * @property string $type
 * @property string $refId;
 * @property array $resourceList;
 */
class Collection
{
    const SORT_TITLE = 'title';
    const SORT_AUTHOR = 'author';
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
        
        if ( $refId )
        {
            $this->load();
        }
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
                resource_id,
                is_visible
            FROM
                `{$this->tbl['library_collection']}`
            WHERE
                collection_type = " . $this->database->quote( $this->type ) . "
            AND
                ref_id = " . $this->database->quote( $this->refId )
        );
        
        foreach( $resultSet as $line )
        {
            $resourceId = $line[ 'resource_id' ];
            $metadata = new Metadata( $this->database , $resourceId );
            $this->resourceList[ $resourceId ] = array( 'id' => $resourceId
                                                      , 'title' => $metadata->get( 'title' )
                                                      , 'author' => $metadata->get( 'author' )
                                                      , 'is_visible' => (boolean)$line[ 'is_visible' ] );
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
     * @param boolean $force
     * @return array $resourceList
     */
    public function getResourceList( $force = false , $sort = null )
    {
        if ( $force )
        {
            $this->load();
        }
        
        if ( $sort )
        {
            $this->sort( $sort );
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
     * @param int $resourceId
     * @return boolean true if exists
     */
    public function resourceExists( $resourceId )
    {
        return $this->database->query( "
            SELECT
                resource_id
            FROM
                `{$this->tbl['library_collection']}`
            WHERE
                resource_id = " . $this->database->escape( $resourceId ) ."
            AND
                collection_type = " . $this->database->quote( $this->type ) ."
            AND
                ref_id = " . $this->database->quote( $this->refId )
        )->numRows();
    }
    
    /**
     * Sets the visibility of the resource
     * @param int $resourceId
     * @param boolean $is_visible
     * @return boolean true on success
     */
    public function setVisibility( $resourceId , $is_visible = true )
    {
        return $this->database->exec( "
            UPDATE
                `{$this->tbl['library_collection']}`
            SET
                is_visible = " . $this->database->escape( $is_visible ? 'TRUE' : 'FALSE' ) . "
            WHERE
                resource_id = " . $this->database->escape( $resourceId ) . "
            AND
                ref_id = " . $this->database->quote( $this->refId ) . "
            AND
                collection_type = " . $this->database->quote( $this->type ) );
    }
    
    /**
     * Gets the collection list in wich the specified resource is included
     * @param int $resourceId
     * @return array $collectionList
     */
    public function getCollectionList( $resourceId )
    {
        $collectionList = array();
        
        $result = $this->database->query( "
            SELECT
                collection_type AS type,
                ref_id
            FROM
                `{$this->tbl['library_collection']}`
            WHERE
                resource_id =" . $this->database->escape( $resourceId ) );
        
        foreach( $result as $line )
        {
            $collectionList[ $line[ 'type' ] ][] = $line[ 'ref_id' ];
        }
        
        return $collectionList;
    }
    
    /**
     * Add a resource in the resource set
     * @param int $resourceId
     * @return boolean true on success
     */
    public function add( $resourceId )
    {
        if ( $this->database->exec( "
            INSERT INTO
                `{$this->tbl['library_collection']}`
            SET
                collection_type = " . $this->database->quote( $this->type ) . ",
                ref_id = " . $this->database->quote( $this->refId ) . ",
                resource_id = " . $this->database->escape( $resourceId ) ) . "
                is_visible = TRUE" )
        {
            return $this->resourceList[ $resourceId ] = array( new Resource( $this->database , $resourceId )
                                                             , new Metadata( $this->database , $resourceId ) );
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
                collection_type = " . $this->database->quote( $this->type ) . "
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
    public function wipe()
    {
        $this->database->exec( "
            DELETE FROM
                `{$this->tbl['library_collection']}`
            WHERE
                collection_type = " . $this->database->quote( $this->type ) . "
            AND
                ref_id = " . $this->database->quote( $this->refId ) );
        
        return $this->database->affectedRows();
    }
    
    /**
     * Removes a specified resource from all the collections
     * @param int $resourceId
     * @return int $affectedRows
     */
    public function removeResource( $resourceId )
    {
        $this->database->exec( "
            DELETE FROM
                `{$this->tbl['library_collection']}`
            WHERE
                resource_id = " . $this->database->escape( $resourceId ) . "
            AND
                collection_type != " . $this->database->quote( self::USER_COLLECTION ) );
        
        return $this->database->affectedRows();
    }
    
    /**
     * Moves a specified resource from a collection to another
     * @param int $resourceId
     * @param string $refId
     * @param string $type
     * @return boolean true on success
     */
    public function moveResource( $resourceId , $refId , $type = self::LIBRARY_COLLECTION )
    {
        return $this->database->exec( "
            UPDATE
                `{$this->tbl['library_collection']}`
            SET
                ref_id = " . $this->database->quote( $refId ) . "
            WHERE
                collection_type = " . $this->database->quote( $type ) . "
            AND
                resource_id = " . $this->database->escape( $resourceId ) );
    }
    
    /**
     * Sorts the resource list
     */
    private function sort( $sort )
    {
        if ( $sort != self::SORT_TITLE
          && $sort != self::SORT_AUTHOR )
        {
            throw new Exception( 'Invalid argument' );
        }
        
        $sortedList = array();
        
        foreach( $this->resourceList as $datas )
        {
            $sortedList[ $datas[ $sort ] ] = $datas;
        }
        
        ksort( $sortedList );
        
        $this->resourceList = $sortedList;
    }
}