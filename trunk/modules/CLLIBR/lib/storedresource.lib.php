<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.6.2 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class that represents the stored resource
 * @const DOWNLOAD_ACCESS
 * @const RAW_ACCESS
 * @property string $location
 * @property Resource object $resource
 * @property string $keyword
 */
class StoredResource
{
    const DOWNLOAD_ACCESS = 'download';
    const RAW_ACCESS = 'direct';
    
    protected $location;
    protected $resource;
    protected $keyword;
    
    /**
     * Constructor
     */
    public function __construct( $location , $resource , $keyword = '' )
    {
        $this->location = $location;
        $this->resource = $resource;
        $this->keyword = $keyword;
    }
    
    /**
     * Generates a stored name
     * @param string $fileName
     * @return string $storedName
     */
    public function generateStoredName()
    {
        /*return UUID::generate( UUID::UUID_NAME_MD5
                             , UUID::FMT_STRING
                             , $this->resource->getName() . $this->resource->getDate() );
        the line above is for use with uuid.lib.php */
        return md5( $this->resource->getName()
                  . $this->resource->getDate()
                  . $this->keyword )
             . '-'
             . md5( $this->resource->getDate() );
    }
    
    /**
     * Stores a file
     * @param $_FILES[ 'uploadedFile' ] $file
     * @return boolean true on success
     */
    public function store( $file )
    {
        $target_path = $this->location . $this->generateStoredName();
        
        return move_uploaded_file( $file[ 'tmp_name' ] , $target_path );
    }
    
    /**
     * Gets the stored file (download)
     */
    public function getFile( $access = self::DOWNLOAD_ACCESS )
    {
        $filePath = $this->location . $this->generateStoredName();
        
        if ( $access == self::DOWNLOAD_ACCESS )
        {
            header('Content-type: ' . self::getMimeType( $this->resource->getName() ) );
            header('Content-Disposition: attachment; filename="' . $this->resource->getName() . '"');
            readfile( $filePath );
        }
        elseif( $access == self::RAW_ACCESS )
        {
            return file_get_contents( $filePath );
        }
        else
        {
            throw new Exception( 'Invalid argument' );
        }
    }
    
    /**
     * Gets file name
     * @return string $fileName
     */
    public function getFileName()
    {
        return $this->resource->getName();
    }
    
    /**
     * Deletes the file
     * @return boolean true on success
     */
    public function delete()
    {
        return file_exists( $this->location . $this->generateStoredName() )
            && unlink( $this->location . $this->generateStoredName() );
    }
    
    /**
     * Static method: gets file extension from file name
     * @param string $fileName
     * @return string $extension
     */
    public static function getFileExtension( $fileName )
    {
        return strtolower( pathinfo( $fileName, PATHINFO_EXTENSION ) );
    }
    
    /**
     * Static mehod : Gets Mime type from file name
     * @param string $fileName
     * @return string $mimeType
     */
    public static function getMimeType( $fileName )
    {
        $fileExtension = self::getFileExtension( $fileName);
        $defaultMimeType = 'document/unknown';
        
        if( $fileExtension )
        {
            $mimeTypeList = array(
                # Structured text
                'htm'   => 'text/html',
                'html'  => 'text/html',
                'url'   => 'text/html',
                'xml'   => 'application/xml',
                'css'   => 'text/css',
                'js'    => 'text/javascript',
                # Syndication
                'ics'   => 'text/Calendar',
                'xcs'   => 'text/Calendar',
                'rdf'   => 'text/xml',
                'rss'   => 'application/rss+xml',
                'opml'  => 'text/x-opml',
                # Editable text
                'txt'   => 'text/plain',
                'rtf'   => 'application/rtf',
                'tex'   => 'application/x-tex',
                # MS Office
                'doc'   => 'application/msword',
                'ppt'   => 'application/vnd.ms-powerpoint',
                'pps'   => 'application/vnd.ms-powerpoint',
                'xls'   => 'application/vnd.ms-excel',
                'xsl'   => 'text/xml',
                # Open Document Formats
                'odt'   => 'application/vnd.oasis.opendocument.text',
                'ott'   => 'application/vnd.oasis.opendocument.text-template',
                'oth'   => 'application/vnd.oasis.opendocument.text-web',
                'odm'   => 'application/vnd.oasis.opendocument.text-master',
                'odg'   => 'application/vnd.oasis.opendocument.graphics',
                'otg'   => 'application/vnd.oasis.opendocument.graphics-template',
                'odp'   => 'application/vnd.oasis.opendocument.presentation',
                'otp'   => 'application/vnd.oasis.opendocument.presentation-template',
                'ods'   => 'application/vnd.oasis.opendocument.spreadsheet',
                'ots'   => 'application/vnd.oasis.opendocument.spreadsheet-template',
                'odc'   => 'application/vnd.oasis.opendocument.chart',
                'odf'   => 'application/vnd.oasis.opendocument.formula',
                'odb'   => 'application/vnd.oasis.opendocument.database',
                'odi'   => 'application/vnd.oasis.opendocument.image',
                # Star Office Documents
                'sxw'   => 'application/vnd.sun.xml.writer',
                'stw'   => 'application/vnd.sun.xml.writer.template',
                'sxc'   => 'application/vnd.sun.xml.calc',
                'stc'   => 'application/vnd.sun.xml.calc.template',
                'sxd'   => 'application/vnd.sun.xml.draw',
                'std'   => 'application/vnd.sun.xml.draw.template',
                'sxi'   => 'application/vnd.sun.xml.impress',
                'sti'   => 'application/vnd.sun.xml.impress.template',
                'sxg'   => 'application/vnd.sun.xml.writer.global',
                'sxm'   => 'application/vnd.sun.xml.math',
                # Microsoft Office 2007 (sucks)
                'docm'  => 'application/vnd.ms-word.document.macroEnabled.12',
                'docx'  => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'dotm'  => 'application/vnd.ms-word.template.macroEnabled.12',
                'dotx'  => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
                'potm'  => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
                'potx'  => 'application/vnd.openxmlformats-officedocument.presentationml.template',
                'ppam'  => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
                'ppsm'  => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
                'ppsx'  => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
                'pptm'  => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
                'pptx'  => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'xlam'  => 'application/vnd.ms-excel.addin.macroEnabled.12',
                'xlsb'  => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
                'xlsm'  => 'application/vnd.ms-excel.sheet.macroEnabled.12',
                'xlsx'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'xltm'  => 'application/vnd.ms-excel.template.macroEnabled.12',
                'xltx'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
                # Print
                'pdf'   => 'application/pdf',
                'ps'    => 'application/postscript',
                # Image
                'png'   => 'image/png',
                'jpg'   => 'image/jpeg',
                'jpeg'  => 'image/jpeg',
                'gif'   => 'image/gif',
                'tif'   => 'image/tiff',
                'tiff'  => 'image/tiff',
                'bmp'   => 'image/bmp',
                # Vector graphics
                'svg'   => 'image/svg+xml',
                'swf'   => 'application/x-shockwave-flash',
                # Sound
                'wav'   => 'audio/x-wav',
                'aif'   => 'audio/x-aiff',
                'ogg'   => 'audio/ogg',
                'flac'  => 'audio/flac',
                'ape'   => 'audio/x-monkeys-audio',
                'mka'   => 'audio/x-matroska',
                'mid'   => 'audio/midi',
                'mp3'   => 'audio/mpeg',
                # Streaming
                'm3u'   => 'audio/x-mpegurl',
                'ra'    => 'audio/x-realaudio',
                'ram'   => 'audio/x-pn-realaudio',
                'rm'    => 'audio/x-pn-realaudio',
                # Video
                'ogv'   => 'video/ogg',
                'avi'   => 'video/x-msvideo',
                'wmv'   => 'video/x-ms-wmv',
                'mov'   => 'video/quicktime',
                'qt'    => 'video/quicktime',
                'mp4'   => 'video/mp4',
                'mpg'   => 'video/mpeg',
                'mpeg'  => 'video/mpeg',
                'mkv'   => 'video/x-matroska',
                'vp8'   => 'video/webm',
                # Archive
                'tar'   => 'application/x-tar',
                'tgz'   => 'application/x-gzip',
                'gz'    => 'application/x-gzip',
                'zip'   => 'application/zip',
                'rar'   => 'application/x-rar-compressed',
                'sit'   => 'application/x-stuffit',
                'hqx'   => 'application/mac-binhex40'
            );
            
            return array_key_exists( $fileExtension, $mimeTypeList )
                ? $mimeTypeList[$fileExtension]
                : $defaultMimeType;
        }
        else
        {
            return $defaultMimeType;
        }
    }
}