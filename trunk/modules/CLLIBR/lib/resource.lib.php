<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.2.2 $Revision$ - Claroline 1.9
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
    protected $title;
    protected $type;
    
    protected $creationDate;
    protected $resourceName;
    protected $is_stored;
    
    /**
     * Constructor
     * @param int $resourceId
     */
    public function __construct( $resourceUid = null )
    {
        $this->tbl = get_module_main_tbl( array( 'library_resource' ) );
        
        if ( $resourceUid )
        {
            $this->load( $resourceUid );
        }
    }
    
    /**
     * Loads datas
     * This method is called by the constructor when it received an ID
     */
    protected function load( $uid )
    {
        $resultSet = Claroline::getDatabase()->query( "
            SELECT
                title,
                creation_date,
                mime_type,
                resource_name
            FROM
                `{$this->tbl['library_resource']}`
            WHERE
                uid = " . Claroline::getDatabase()->quote( $uid )
            )->fetch( Database_ResultSet::FETCH_ASSOC );
        
        if ( count( $resultSet ) )
        {
            $this->title = $resultSet[ 'title' ];
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
     * Getter for the title of the resource
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
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
     * Setter for the title orf the resource
     * @param string $title
     * @return bollean true on success
     */
    public function setTitle( $title )
    {
        return $this->title = $title;
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
        return Claroline::getDatabase()->exec( "
            DELETE FROM
                `{$this->tbl['library_resource']}`
            WHERE
                uid = " . Claroline::getDatabase()->quote( $this->uid ) );
    }
    
    /**
     * Saves the datas in DB
     * @return boolean true on success
     */
    public function save()
    {
        $sql = "\n    `{$this->tbl['library_resource']}`
                SET
                    title = " . Claroline::getDatabase()->quote( $this->title ) . ",
                    mime_type = " . Claroline::getDatabase()->quote( $this->type ) . ",
                    resource_name = " . Claroline::getDatabase()->quote( $this->resourceName ) . ",
                    creation_date = NOW()";
        
        if ( $this->is_stored )
        {
            Claroline::getDatabase()->exec( "
                UPDATE" . $sql . "
                WHERE
                    uid = " . Claroline::getDatabase()->quote( $this->uid )
            );
        }
        else
        {
            Claroline::getDatabase()->exec( "
                INSERT INTO " . $sql . ",
                    uid = " . Claroline::getDatabase()->quote( $this->uid )
            );
        }
        
        return Claroline::getDatabase()->affectedRows();
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