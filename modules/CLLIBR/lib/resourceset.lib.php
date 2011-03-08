<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.2.8 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class that represents a resource collection
 * belonging to libraries, bibliography or bookmarks
 * @static $_type
 * @property $refId;
 * @property $resourceList;
 */
abstract class ResourceSet
{
    protected static $_type;
    
    protected $refId;
    protected $resourceList = array();
    
    protected $database;
    
    /**
     * Constructor
     */
    public function __construct( $database , $refId = null )
    {
        $this->database = $database;
        $this->tbl = get_module_main_tbl( array( 'library_resource'
                                                   , 'library_resource_set' ) );
        
        self::$_type = strtolower( get_class( $this ) );
        
        if ( $refId )
        {
            $this->load( $refId );
            $this->refId = $refId;
            $this->validate();
        }
    }
    
    /**
     * Loads datas
     * This method is called by the constructor when it received an ID
     */
    protected function load( $refId )
    {
        $this->resourceList = array();
        
        $resultSet = $this->database->query( "
            SELECT
                R.uid
            FROM
                `{$this->tbl['library_resource']}`     AS R
            LEFT JOIN
                `{$this->tbl['library_resource_set']}` AS S
            ON
                S.resource_uid = R.uid
            WHERE
                S.type = " . $this->database->quote( self::$_type ) . "
            AND
                S.ref_id = " . $this->database->quote( $refId )
        );
        
        foreach( $resultSet as $line )
        {
            $this->resourceList[ $line[ 'uid' ] ] = new Metadata( $this->database , $line[ 'uid' ] );
        }
    }
    
    /**
     * Getter for id
     * @return int id
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
            $this->load( $this->refId );
        }
        
        return $this->resourceList;
    }
    
    /**
     * Getter for static value $_type
     * @return string $_type
     */
    public function getType()
    {
        return self::$_type;
    }
    
    /**
     * Verifies if specified resource is in the resource set
     * @param $ResourceUid
     * @return boolean true if exists
     */
    public function resourceExists( $resourceUid )
    {
        return array_key_exists( $resourceUid , $this->resourceList );
    }
    
    /**
     * Add a resource in the resource set
     * @param Resource $resource
     */
    public function addResource( $resourceUid )
    { 
        if ( $this->resourceExists( $resourceUid ) )
        {
            throw new Exception( 'Resource already exists' );
        }
        
        if ( $this->database->exec( "
            INSERT INTO
                `{$this->tbl['library_resource_set']}`
            SET
                type = " . $this->database->quote( self::$_type ) . ",
                ref_id = " . $this->database->quote( $this->refId ) . ",
                resource_uid = " . $this->database->quote( $resourceUid ) ) )
        {
            return $this->resourceList[ $resourceUid ] = new Metadata( $resourceUid );
        }
    }
    
    /**
     * Removes a resource from the resource collection
     * @param int $resourceId
     */
    public function removeResource( $resourceUid )
    {
        if ( ! array_key_exists( $resourceUid , $this->resourceList ) )
        {
            throw new Exception( 'Resource does not exist' );
        }
        
        unset( $this->resourceList[ $resourceUid ] );
        
        return $this->database->exec( "
            DELETE FROM
                `{$this->tbl['library_resource_set']}`
            WHERE
                type = " . $this->database->quote( self::$_type ) . "
            AND
                ref_id = " . $this->database->quote( $this->refId ) . "
            AND
                resource_uid = " . $this->database->quote( $resourceUid ) );
    }
    
    /**
     * Remove all the resources from the resource collection
     * @return boolean true on success
     */
    public function deleteAll()
    {
        return $this->database->exec( "
            DELETE FROM
                `{$this->tbl['library_resource_set']}`
            WHERE
                type = " . $this->database->quote( self::$_type ) . "
            AND
                ref_id = " . $this->database->quote( $this->refId ) );
    }
    
    /**
     * Static function to verify if the argument passed to the constructor
     * is consistent with the declared type
     */
    abstract public function validate();
}