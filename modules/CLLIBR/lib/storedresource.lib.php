<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.2.7 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class that represents the stored resource
 * @property $resourceId
 * @property $fileName
 * @property $fileExtension
 * @property $uid
 * @property $location
 */
class StoredResource
{
    protected $fileName;
    protected $fileExtension;
    protected $uid;
    protected $location;
    
    /**
     * Constructor
     */
    public function __construct( $location , $uid = null )
    {
        $this->tbl = get_module_main_tbl( array( 'library_resource' ) );
        
        $this->location = $location;
        
        if ( $uid )
        {
            $this->resourceId = $uid;
            $this->load();
        }
    }
    
    /**
     * Loads datas
     * This method is called by the constructor when it received an ID
     */
    public function load()
    {
        $resultSet = Claroline::getDatabase()->query( "
            SELECT
                uid,
                resource_name
            FROM
                `{$this->tbl['library_resource']}`
            WHERE
                uid = " . Claroline::getDatabase()->quote( $this->uid )
        )->fetch( Database_ResultSet::FETCH_ASSOC );
        
        if ( count( $resultSet ) )
        {
            $this->uid = $resultSet[ 'uid' ];
            $this->fileName = $resultSet[ 'resource_name' ];
        }
        else
        {
            throw new Exception( 'resource does not exist' );
        }
    }
    
    /**
     * Stores a file
     * @param $_FILES[ 'uploadedFile' ] $file
     * @param string $uid
     * @return boolean true on success
     */
    public function store( $file , $uid )
    {
        $fileName = $file[ 'name' ];
        
        if ( strlen( $uid ) != 32 )
        {
            throw new Exception( 'invalid UID :' . $uid );
        }
        
        $target_path = $this->location . $uid;
        
        return move_uploaded_file( $file[ 'tmp_name' ] , $target_path )
            && $this->fileName = $fileName
            && $this->uid = $uid;
    }
    
    /**
     * Gets file extension
     * @return string $this->fileExtension
     */
    public function getFileExtension()
    {
        if ( $this->fileExtension )
        {
            //$this->fileExtension = strtolower( pathinfo( $this->fileName, PATHINFO_EXTENSION ) );
            $parts == explode( '.' , $this->filename );
            $this->fileExtension = $parts[ count( $parts ) - 1 ];
        }
        
        return $this->fileExtension;
    }
    
    /**
     * Gets the stored file (download)
     */
    public function getFile()
    {
        header('Content-type: application/' . $this->fileExtension );
        header('Content-Disposition: attachment; filename="' . $this->fileName . '"');
        readfile( $this->location . $this->uid );
    }
    
    /**
     * Deletes the file
     * @return boolean true on success
     */
    public function delete()
    {
        var_dump( $this->uid );
        exit();
        if ( Claroline::getDatabase()->exec( "
                DELETE FROM
                    `{$this->tbl['library_resource']}`
                WHERE
                    uid = " . Claroline::getDatabase()->quote( $this->uid )
            ) )
        {
            return claro_delete_file( $this->location . $this->uid );
        }
        else
        {
            throw new Exception( 'Cannot delete file ' . $this->uid );
        }
    }
}
