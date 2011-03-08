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
 * A Class that represents a resource
 * @const TYPE_FILE
 * @const TYPE_URL
 * @property $authorizedFileType
 * @property $uid
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
    
    protected $uid;
    protected $type;
    
    protected $creationDate;
    protected $resourceName;
    protected $is_stored;
    
    protected $database;
    
    /**
     * Constructor
     * @param int $resourceId
     */
    public function __construct( $database , $uid = null )
    {
        $this->database = $database;
        $this->tbl = get_module_main_tbl( array( 'library_resource' ) );
        
        if ( $uid )
        {
            $this->load( $uid );
        }
    }
    
    /**
     * Loads datas
     * This method is called by the constructor when it received an ID
     */
    protected function load( $uid )
    {
        $resultSet = $this->database->query( "
            SELECT
                creation_date,
                mime_type,
                resource_name
            FROM
                `{$this->tbl['library_resource']}`
            WHERE
                uid = " . $this->database->quote( $uid )
            )->fetch( Database_ResultSet::FETCH_ASSOC );
        
        if ( count( $resultSet ) )
        {
            $this->creationDate = $resultSet[ 'creation_date' ];
            $this->type = $resultSet[ 'mime_type' ];
            $this->resourceName = $resultSet[ 'resource_name' ];
            $this->uid = $uid;
            
            return $this->is_stored = true;
        }
        else
        {
            throw new Exception( 'resource does not exists' );
        }
    }
    
    /**
     * Generate an Unique ID
     * @param string $fileName
     * @return string $uid
     */
    protected function generateUid( $fileName )
    {
        // TODO generate a bestUID
        return $this->uid = md5( $fileName . time() );
    }
    
    /**
     * Getter for UID
     * @return string $uid
     */
    public function getUid()
    {
        return $this->uid;
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
     * Setter for the name of the resource
     * @param string $name
     * @return boolean true on success
     */
    public function setName( $name )
    {
        return $this->resourceName = $name;
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
                uid = " . $this->database->quote( $this->uid ) );
    }
    
    /**
     * Saves the datas in DB
     * @return boolean true on success
     */
    public function save()
    {
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
                    uid = " . $this->database->quote( $this->uid )
            );
        }
        else
        {
            $this->database->exec( "
                INSERT INTO " . $sql . ",
                    uid = " . $this->database->quote( $this->uid )
            );
        }
        
        return $this->database->affectedRows();
    }
    
    /**
     * Verifies the validity on the file name,
     * generates an uid and sets the value in object
     * @return boolean true on success
     */
    public function validate( $fileName )
    {
        return in_array( strtolower( pathinfo( $fileName, PATHINFO_EXTENSION ) )
                        , $this->authorizedFileType )
                && $this->generateUid( $fileName )
                && $this->setName( $fileName );
    }
}