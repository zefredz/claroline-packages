<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.4.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 * @author      Frederic Minne <frederic.minne@uclouvain.be>
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
    protected $resource;
    
    /**
     * Constructor
     */
    public function __construct( $location , $resource )
    {
        $this->location = $location;
        $this->resource = $resource;
    }
    
    /**
     * Generates a stored name
     * @param string $fileName
     * @return string $storedName
     */
    public function generateStoredName( $fileName )
    {
        return md5( $this->resource->getName() ) . '-' . $this->resource->getId();
    }
    
    /**
     * Stores a file
     * @param $_FILES[ 'uploadedFile' ] $file
     * @return boolean true on success
     */
    public function store( $file )
    {
        $target_path = $this->location . $this->generateStoredName( $this->resource->getName() );
        
        return move_uploaded_file( $file[ 'tmp_name' ] , $target_path );
    }
    
    /**
     * Gets the stored file (download)
     */
    public function getFile()
    {
        header('Content-type: ' . self::getMimeType( $this->resource->getName() ) );
        header('Content-Disposition: attachment; filename="' . $this->resource->getName() . '"');
        readfile( $this->location . $this->generateStoredName( $this->resource->getName() ) );
    }
    
    /**
     * Deletes the file
     * @return boolean true on success
     */
    public function delete()
    {
        return unlink( $this->location . $this->generateStoredName( $this->resource->getName() ) );
    }
    
    /**
     * Static mehod : Gets Mime type from file name
     * @param string $fileName
     * @return string $mimeType
     */
    public static function getMimeType( $fileName )
    {
        $mimeType = null;
        $fileExtension = strtolower( pathinfo( $fileName, PATHINFO_EXTENSION ) );
        $defaultMimeType = 'document/unknown';
        
        if( $fileExtension )
        {
            $mimeTypeList = array(
                'aif'   => 'audio/x-aiff',
                'avi'   => 'video/x-msvideo',
                'bmp'   => 'image/bmp',
                'css'   => 'text/css',
                'doc'   => 'application/msword',
                'fla'   => 'application/octet-stream',
                'gif'   => 'image/gif',
                'gz'    => 'application/x-gzip',
                'htm'   => 'text/html',
                'html'  => 'text/html',
                'hqx'   => 'application/mac-binhex40',
                'jpg'   => 'image/jpeg',
                'jpeg'  => 'image/jpeg',
                'js'    => 'text/javascript',
                'm3u'   => 'audio/x-mpegurl',
                'mid'   => 'audio/midi',
                'mov'   => 'video/quicktime',
                'mp3'   => 'audio/mpeg',
                'mp4'   => 'video/mp4',
                'mpg'   => 'video/mpeg',
                'mpeg'  => 'video/mpeg',
                'ogg'   => 'application/x-ogg',
                
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
                
                'pdf'   => 'application/pdf',
                'png'   => 'image/png',
                'ppt'   => 'application/vnd.ms-powerpoint',
                'pps'   => 'application/vnd.ms-powerpoint',
                'ps'    => 'application/postscript',
                'ra'    => 'audio/x-realaudio',
                'ram'   => 'audio/x-pn-realaudio',
                'rm'    => 'audio/x-pn-realaudio',
                'rpm'   => 'audio/x-pn-realaudio-plugin',
                'rtf'   => 'application/rtf',
                'sit'   => 'application/x-stuffit',
                'svg'   => 'image/svg+xml',
                'swf'   => 'application/x-shockwave-flash',
                
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
                
                # Misc
                'tar'   => 'application/x-tar',
                'tex'   => 'application/x-tex',
                'tgz'   => 'application/x-gzip',
                'tif'   => 'image/tiff',
                'tiff'  => 'image/tiff',
                'txt'   => 'text/plain',
                'url'   => 'text/html',
                'wav'   => 'audio/x-wav',
                'wmv'   => 'video/x-ms-wmv',
                'xml'   => 'application/xml',
                'xls'   => 'application/vnd.ms-excel',
                'xsl'   => 'text/xml',
                'zip'   => 'application/zip',
                
                # Syndication
                'ics'   => 'text/Calendar',
                'xcs'   => 'text/Calendar',
                'rdf'   => 'text/xml',
                'rss'   => 'application/rss+xml',
                'opml'  => 'text/x-opml',
                
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
                'xltx'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template'
            );
            
            $mimeType = array_key_exists( $fileExtension, $mimeTypeList )
                ? $mimeTypeList[$fileExtension]
                : $defaultMimeType
                ;
        }
        else
        {
            $mimeType = $defaultMimeType;
        }
        
        return $mimeType;
    }
}