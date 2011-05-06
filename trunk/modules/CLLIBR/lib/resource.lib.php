<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.4.0 $Revision$ - Claroline 1.9
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
 */
class Resource
{
    const TYPE_FILE = 'file';
    const TYPE_URL = 'url';
    
    protected $authorizedFileType;
    
    protected $id;
    protected $type;
    protected $title;
    protected $description;
    
    protected $creationDate;
    protected $resourceName;
    
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
                creation_date,
                resource_type,
                resource_name,
                title,
                description
            FROM
                `{$this->tbl['library_resource']}`
            WHERE
                id = " . $this->database->escape( $this->id )
            )->fetch( Database_ResultSet::FETCH_ASSOC );
        
        if ( count( $result ) )
        {
            $this->creationDate = $result[ 'creation_date' ];
            $this->type = $result[ 'resource_type' ];
            $this->resourceName = $result[ 'resource_name' ];
            $this->title = $result[ 'title' ];
            $this->description = $result[ 'description' ];
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
     * Getter for Title
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Getter for Description
     * @return string description
     */
    public function getDescription()
    {
        return $this->description;
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
     * Setter for resource's name
     * @param string $name
     * @return boolean true on success
     */
    public function setName( $name )
    {
        return $this->resourceName = $name;
    }
    
    /**
     * Setter for Title
     * @param string $title
     * @return boolean true on success
     */
    public function setTitle( $title )
    {
        return $this->title = $title;
    }
    
    /**
     * Setter for Description
     * @param string $description
     * @return boolena true on success
     */
    public function setDescription( $description )
    {
        return $this->description = $description;
    }
    
    /**
     * Setter for creation date
     * @param string date
     * @return boolean true on success
     */
    public function setDate( $date = null )
    {
        return $this->creationDate = $date ? $date : date( 'Y-m-d H:i:s' );
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
                    resource_type = " . $this->database->quote( $this->type ) . ",
                    resource_name = " . $this->database->quote( $this->resourceName ) . ",
                    creation_date = " . $this->database->quote( $this->creationDate ) . ",
                    title = " . $this->database->quote( $this->title ) . ",
                    description = " . $this->database->quote( $this->description );
    }
    
    /**
     * Inserts a new resource
     * @param string $sql
     * @return boolean true on success
     */
    private function insert()
    {
        if ( ! $this->type
          || ! $this->resourceName
          || ! $this->creationDate
          || ! $this->title )
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