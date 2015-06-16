<?php // $Id$

/**
 * Moodle Resource Exporter
 *
 * @version     MOODLEEX 2.0 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2015 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     MOODLEEX
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class MBZExporter
{
    protected $courseId;
    protected $tmpFilePath;
    protected $mbzFileName;
    protected $fileList;
    protected $resourceList;
    protected $output;
    
    public function __construct( $courseId )
    {
        $this->courseId = $courseId;
        
        $this->tmpFilePath = get_path( 'coursesRepositorySys' )
            . claro_get_course_path( $this->courseId )
            . '/tmp/' . uniqid( '' ) . '/';
        
        $this->mbzFileName = 'export_' . $this->courseId . '_' . date( 'Y-m-d' ) . '.mbz';
    }
    
    public function select( $itemList )
    {
        $this->toExport = $itemList;
    }
    
    public function export()
    {
        $mbzArchive = new PclZip( $this->mbzFileName );
        
        $mbzArchive->add(
            $this->fileList,
            PCLZIP_OPT_REMOVE_PATH, $this->tmpFilePath );
        
        if ( file_exists( $this->mbzFileName ) )
        {
            claro_send_file( $downloadArchiveFile, $downloadArchiveName );
            unlink( $downloadArchiveFile );
            exit();
        }
        else
        {
            $this->output[ 'error' ][] = get_lang( 'Unable to create zip file' );
        }
    }
    
    private function buildFileList()
    {
        foreach( $this->toExport as $item )
        {
            $this->{ '_' . $item[ 'type' ] };
        }
    }
    
    public function output( $type = null )
    {
        if( ! is_null( $type ) )
        {
            return $this->output[ $type ];
        }
        else
        {
            return $this->output;
        }
    }
}
