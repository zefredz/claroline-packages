<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    if ( count( get_included_files() ) == 1 )
    {
        die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
    }
    
    /**
     * File Uploader
     *
     * @version     1.9 $Revision: 159 $
     * @copyright   2001-2007 Universite catholique de Louvain (UCL)
     * @author      Frederic Minne <zefredz@claroline.net>
     * @license     http://www.gnu.org/copyleft/gpl.html
     *              GNU GENERAL PUBLIC LICENSE
     * @package     KERNEl
     */

    // TODO handle expand for zipped files
    class FileUploader
    {
        var $_file;
        var $_allowPhpFiles = false;
        var $_allowHtaccessFiles = false;
        
        /**
            Usage :
                $uploader = new FileUploader( $_FILES['myFile'] );
                if ( ! $uploader->uploadFailed() )
                {
                    $uploader->moveToDestination( $destinaTionDirectory
                        , $destinationFileName );
                }
                else
                {
                    echo $uploader->getErrorMessage();
                }
        */
        function FileUploader ( $_file )
        {
            $this->_file = $_file;
        }
        
        function allowPhpFiles()
        {
            $this->_allowPhpFiles = true;
        }
        
        function allowHtaccess()
        {
            $this->_allowHtaccessFiles = true;
        }
        
        function uploadFailed()
        {
            return $this->getFileUploadErrno( $this->_file ) > 0;
        }
        
        function moveToDestination( $destinationDirectory, $destinationName = '' )
        {
            if ( $this->uploadFailed() )
            {
                return claro_failure::set_failure($this->getFileUploadErrorMessage());
            }
            else
            {
                if ( ! file_exists( $destinationDirectory ) )
                {
                    claro_mkdir( $destinationDirectory, true );
                }
                
                if ( empty ( $destinationName ) )
                {
                    $destinationName = trim( $this->_file['name'] );
                }
                
                $destinationPath = FileUtils::appendPath($destinationDirectory
                    , FileUtils::getSecureFileName( $destinationName
                        , $this->_allowPhpFiles
                        , $this->_allowHtaccessFiles ) );
                
                if ( move_uploaded_file($this->_file['tmp_name'], $destinationPath ) )
                {
                    chmod( $destinationPath, CLARO_FILE_PERMISSIONS );
                    return $destinationName;
                }
                else
                {
                    return claro_failure::set_failure(get_lang('File upload failed'));
                }
            }
        }

        function getFileUploadErrno()
        {
            return $this->_file['error'];
        }

        function getFileUploadErrorMessage()
        {
            return $this->getFileUploadErrorStringFromErrno( $this->getFileUploadErrno() );
        }

        function getFileUploadErrorStringFromErrno( $errorLevel )
        {
            if ( !defined( 'UPLOAD_ERR_CANT_WRITE' ) )
            {
                // Introduced in PHP 5.1.0
                define( 'UPLOAD_ERR_CANT_WRITE', 5 );
            }

            switch( $errorLevel )
            {
                case UPLOAD_ERR_OK:
                {
                    $details = get_lang('No error');
                }
                case UPLOAD_ERR_INI_SIZE:
                {
                    $details = get_lang('File too large. Notice : Max file size %size'
                        , array ( '%size' => get_cfg_var('upload_max_filesize') ) );
                }   break;
                case UPLOAD_ERR_FORM_SIZE:
                {
                    $details = get_lang('File size exceeds');
                }   break;
                case UPLOAD_ERR_PARTIAL:
                {
                    $details = get_lang('File upload incomplete');
                }   break;
                case UPLOAD_ERR_NO_FILE:
                {
                    $details = get_lang('No file uploaded');
                }   break;
                case UPLOAD_ERR_NO_TMP_DIR:
                {
                    $details = get_lang('Temporary folder missing');
                }   break;
                case UPLOAD_ERR_CANT_WRITE:
                {
                    $details = get_lang('Failed to write file to disk');
                }   break;
                default:
                {
                    $details = get_lang('Unknown error code %errCode%'
                        , array('%errCode%' => $errorLevel ));
                }   break;
            }

            return $details;
        }
    }
    
    class FileUtils
    {
        function getSecureFileName($fileName, $allowPhp = false, $allowHtaccess = false)
        {
            if (!$allowPhp) $fileName = FileUtils::php2phps($fileName);
            if (!$allowHtaccess) $fileName = FileUtils::htaccess2txt( $fileName );
            $fileName = FileUtils::secureFilePath( $fileName );
            return $fileName;
        }
        
        function php2phps( $fileName )
        {
            $fileName = preg_replace("/\.(php.?|phtml)$/", ".phps", $fileName);
            return $fileName;
        }
        
        function htaccess2txt( $fileName )
        {
            $fileName = str_ireplace('.htaccess', 'htaccess.txt', $fileName);
            return $fileName;
        }
        
        function secureFilePath( $path )
        {
            while ( strpos( $path, '..' ) !== false )
            {
                $path = preg_replace( '~^(\.\.)$|(/\.\.)|(\.\./)~', '', $path );
            }

            $path = str_replace( '://', '', $path );

            return $path;
        }
        
        function appendPath( $headPath, $tailPath )
        {
            $headPath = str_replace( '\\', '/', $headPath );
            $tailPath = str_replace( '\\', '/', $tailPath );
            
            if ( substr( $headPath, -1, 1 ) == '/' && substr( $tailPath, 0, 1 ) == '/' )
            {
                return $headPath . substr($tailPath, 1);
            }
            elseif ( substr( $headPath, -1, 1 ) != '/' && substr( $tailPath, 0, 1 ) != '/' )
            {
                return $headPath . '/' . $tailPath;
            }
            else
            {
                return $headPath . $tailPath;
            }
        }
    }
?>