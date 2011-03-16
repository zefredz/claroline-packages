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
 * A class that represents the stored resource
 * @property $resourceId
 * @property $fileName
 * @property $fileExtension
 * @property $secretId
 * @property $location
 */
class StoredResource
{
    protected $location;
    protected $secretId;
    protected $fileName;
    
    protected $database;
    
    /**
     * Constructor
     */
    public function __construct( $database , $location , $secretId = null , $fileName = null )
    {
        $this->database = $database;
        $this->location = $location;
        
        if ( $secretId )
        {
            $this->secretId = $secretId;
        }
        
        if ( $fileName && $secretId )
        {
            $this->fileName = $fileName;
        }
    }
    
    /**
     * Generate a secret ID
     * @param string $fileName
     * @return string $secretId
     */
    protected function generateSecretId( $fileName )
    {
        do
        {
            $this->secretId = md5( $fileName . time() );
        }
        while( file_exists( $this->location . $this->secretId ) );
    }
    
    /**
     * Stores a file
     * @param $_FILES[ 'uploadedFile' ] $file
     * @return string $secretId
     */
    public function store( $file )
    {
        $this->fileName = $file[ 'name' ];
        $this->generateSecretId( $this->fileName );
        
        $target_path = $this->location . $this->secretId;
        
        if ( move_uploaded_file( $file[ 'tmp_name' ] , $target_path ) )
        {
            return $this->secretId;
        }
    }
    
    /**
     * Gets file extension
     * @return string $this->fileExtension
     */
    public function getFileExtension()
    {
        //return strtolower( pathinfo( $this->fileName, PATHINFO_EXTENSION ) );
        $parts = explode( '.' , $this->fileName );
        return $parts[ count( $parts ) - 1 ];
    }
    
    /**
     * Gets the stored file (download)
     */
    public function getFile()
    {
        header('Content-type: application/' . $this->getFileExtension( $this->fileName ) );
        header('Content-Disposition: attachment; filename="' . $this->fileName . '"');
        readfile( $this->location . $this->secretId );
    }
    
    /**
     * Deletes the file
     * @return boolean true on success
     */
    public function delete()
    {
        //return claro_delete_file( $this->location . $this->secretId );
        return unlink( $this->location . $this->secretId );
    }
}