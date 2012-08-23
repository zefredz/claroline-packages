<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 1.1.0 $Revision$ - Claroline 1.11
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A Class that represents a resource
 * @const TYPE_FILE
 * @const TYPE_URL
 * @property int $id
 * @property boolean $is_deleted
 * @property string $storageType
 * @property string $resourceType
 * @property string $creationDate
 * @property string $resourceName
 */
class Resource
{
    const TYPE_FILE = 'file';
    const TYPE_URL = 'url';
    
    protected $id;
    protected $submitterId;
    protected $storageType;
    protected $resourceType;
    
    protected $creationDate;
    protected $resourceName;
    
    protected $is_deleted = false;
    protected $database;
    
    /**
     * Constructor
     * @param int $resourceId
     */
    public function __construct( $database , $id = null )
    {
        $this->database = $database;
        $this->tbl = get_module_main_tbl( array( 'library_resource' ) );
        
        if ( $id )
        {
            $this->id = $id;
            $this->load();
        }
    }
    
    /**
     * Loads datas
     * This method is called by the constructor when it received an ID
     */
    protected function load()
    {
        $result = $this->database->query( "
            SELECT
                submitter_id,
                creation_date,
                storage_type,
                resource_type,
                resource_name
            FROM
                `{$this->tbl['library_resource']}`
            WHERE
                id = " . $this->database->escape( $this->id ) );
        
        if ( $result->numRows() )
        {
            $data = $result->fetch( Database_ResultSet::FETCH_ASSOC );
            $this->creationDate = $data[ 'creation_date' ];
            $this->storageType = $data[ 'storage_type' ];
            $this->resourceType = $data[ 'resource_type' ];
            $this->resourceName = $data[ 'resource_name' ];
            $this->submitterId = $data[ 'submitter_id' ];
        }
        else
        {
            $this->is_deleted = true;
        }
    }
    
    /**
     * Controls if the resource is deleted
     * @return boolean true if deleted
     */
    public function isDeleted()
    {
        return $this->is_deleted;
    }
    
    /**
     * Getter for ID
     * @return string $id
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Getter for the "file name" of the resource
     * for an file: the name of the submitted (and downloaded) file
     * for an url : the complete url of the distant resource
     * @return string $reourceName
     */
    public function getName()
    {
        return $this->resourceName;
    }
    
    /**
     * Getter for resource type
     * self::TYPE_FILE for file
     * self::TYPE_URL for url
     * @return string type
     */
    public function getType()
    {
        return $this->resourceType;
    }
    
    /**
     * Getter for creation date
     * @return string $date
     */
    public function getDate()
    {
        return $this->creationDate;
    }
    
    /**
     * Getter for Storage type
     * @return string $storageType
     */
    public function getStorageType()
    {
        return $this->storageType;
    }
    
    /**
     * Setter for the resource's type
     * @param string $type
     * @return boolean true on success
     */
    public function setType( $type )
    {
        return $this->resourceType = $type;
    }
    
    /**
     * Setter for the resource's storage type
     * self::TYPE_FILE for file
     * self::TYPE_URL for url
     * @param string $type
     * @return boolean true on success
     */
    public function setStorageType( $type )
    {
        if ( $type != self::TYPE_FILE && $type != self::TYPE_URL )
        {
            throw new Exception( 'invalid type' );
        }
        
        return $this->storageType = $type;
    }
    
    /**
     * Setter for resource's name
     * @param string $name
     * @return boolean true on success
     */
    public function setName( $name )
    {
        return $this->resourceName = $name;
    }
    
    /**
     * Setter for creation date
     * @param string $date
     * @return boolean true on success
     */
    public function setDate( $date = null )
    {
        return $this->creationDate = $date ? $date : date( 'Y-m-d H:i:s' );
    }
    
    /**
     * setter for submitter id
     * @param int : $userId
     * @return boolean true on succes
     */
    public function setSubmitterId( $userId )
    {
        if( ! $this->submitterId )
        {
            return $this->submitterId = $userId;
        }
    }
    
    /** 
     * Sets a new name and update the date
     * @param string $fileName
     * @return boolean true on success
     */
    public function updateResource( $name )
    {
            return $this->setName( $name )
                && $this->setDate();
    }
    
    /**
     * Deletes the resource from platform
     * @return boolean true on success
     */
    public function delete()
    {
        return $this->database->exec( "
            DELETE FROM
                `{$this->tbl['library_resource']}`
            WHERE
                id = " . $this->database->escape( $this->id ) );
    }
    
    /**
     * Saves the datas in DB
     * This method just calls insert() or update()
     */
    public function save()
    {
        if ( $this->id )
        {
            return $this->update();
        }
        else
        {
            return $this->insert();
        }
    }
    
    /**
     * Generate string for insert() and update() methods
     * @return string $sqlString
     */
    private function generateSqlString()
    {
        return "\n   `{$this->tbl['library_resource']}`
                SET
                    submitter_id = " . $this->database->quote( $this->submitterId ) . ",
                    storage_type = " . $this->database->quote( $this->storageType ) . ",
                    resource_type = " . $this->database->quote( $this->resourceType ) . ",
                    resource_name = " . $this->database->quote( $this->resourceName ) . ",
                    creation_date = " . $this->database->quote( $this->creationDate );
    }
    
    /**
     * Inserts a new resource
     * @param string $sql
     * @return boolean true on success
     */
    private function insert()
    {
        if ( ! $this->storageType
          || ! $this->resourceType
          || ! $this->resourceName )
        {
            throw new Exception( 'Missing atributes' );
        }
        
        if ( $this->database->exec( "
            INSERT INTO" . $this->generateSqlString() ) )
        {
            return $this->id = $this->database->insertId();
        }
    }
    
    /**
     * Updates the resource
     * @param string $sql
     * @return boolena true on success
     */
    private function update()
    {
        return $this->database->exec( "
            UPDATE" . $this->generateSqlString() . "
            WHERE
                id = " . $this->database->escape( $this->id ) );
    }
}