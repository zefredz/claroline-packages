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
 * A Class that represents a resource
 * @const TYPE_FILE
 * @const TYPE_URL
 * @property $authorizedFileType
 * @property $id
 * @property $secretId
 * @property $title
 * @property $type
 * @property $metaDataList
 * @property $creationDate
 * @property $is_stored
 */
class Resource
{
    const TYPE_FILE = 'file';
    const TYPE_URL = 'url';
    
    protected $authorizedFileType;
    
    protected $id;
    protected $secretId;
    protected $type;
    
    protected $creationDate;
    protected $resourceName;
    protected $is_stored;
    
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
                secret_id,
                creation_date,
                mime_type,
                resource_name
            FROM
                `{$this->tbl['library_resource']}`
            WHERE
                id = " . $this->database->escape( $this->id )
            )->fetch( Database_ResultSet::FETCH_ASSOC );
        
        if ( count( $result ) )
        {
            $this->secretId = $result[ 'secret_id' ];
            $this->creationDate = $result[ 'creation_date' ];
            $this->type = $result[ 'mime_type' ];
            $this->resourceName = $result[ 'resource_name' ];
            
            return $this->is_stored = true;
        }
        else
        {
            throw new Exception( 'resource does not exists' );
        }
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
     * Getter for secret ID
     * @return string $secretId
     */
    public function getSecretId()
    {
        return $this->secretId;
    }
    
    /**
     * Setter for secret ID
     * @param string $secretId
     */
    public function setSecretId( $secretId )
    {
        if ( strlen( $secretId ) != 32 )
        {
            throw new Exception( 'invalid ID :' . $secretId );
        }
        
        return $this->secretId = $secretId;
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
        return $this->type;
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
     * Setter for the resource's type
     * self::TYPE_FILE for file
     * self::TYPE_URL for url
     * @param string $type
     * @return boolean true on success
     */
    public function setType( $type )
    {
        if ( $type != self::TYPE_FILE && $type != self::TYPE_URL )
        {
            throw new Exception( 'invalid type' );
        }
        
        return $this->type = $type;
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
     * @return boolean true on success
     */
    public function save()
    {
        if ( ! $this->secretId )
        {
            throw new Exception( 'Secret ID missing' );
        }
        
        $sql = "\n    `{$this->tbl['library_resource']}`
                SET
                    mime_type = " . $this->database->quote( $this->type ) . ",
                    resource_name = " . $this->database->quote( $this->resourceName ) . ",
                    creation_date = NOW()";
        
        if ( $this->is_stored )
        {
            $this->database->exec( "
                UPDATE" . $sql . "
                WHERE
                    secret_id = " . $this->database->quote( $this->secretId )
            );
        }
        else
        {
            $this->database->exec( "
                INSERT INTO " . $sql . ",
                    secret_id = " . $this->database->quote( $this->secretId )
            );
            
            $this->id = $this->database->insertId();
        }
        
        return $this->database->affectedRows();
    }
    
    /**
     * Verifies the validity on the file name,
     * and if valid, sets the resource name
     * @return boolean true on success
     */
    public function validate( $fileName )
    {
        return in_array( strtolower( pathinfo( $fileName, PATHINFO_EXTENSION ) )
                        , $this->authorizedFileType )
            && $this->resourceName = $fileName;
    }
}