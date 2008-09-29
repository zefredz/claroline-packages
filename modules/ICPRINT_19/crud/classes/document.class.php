<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * Description
     *
     * @version     1.9 $Revision$
     * @copyright   2001-2008 Universite catholique de Louvain (UCL)
     * @author      Claroline Team <info@claroline.net>
     * @author      Frederic Minne <zefredz@claroline.net>
     * @license     http://www.gnu.org/copyleft/gpl.html
     *              GNU GENERAL PUBLIC LICENSE version 2 or later
     * @package     PACKAGE_NAME
     */

    if ( count( get_included_files() ) == 1 )
    {
        die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
    }
    
    class PrintServiceDocument
    {
        public $id;
        public $title;
        public $localPath;
        public $globalPath;
        public $length;
        public $hash;
        public $publisher;
        public $courseId;
        public $action;
        
        public static function fromLocalPath( $localPath )
        {
            $courseDir = realpath( 
                  get_path('coursesRepositorySys') 
                . claro_get_course_path()
                .'/document' );
            
            $globalPath = $courseDir . $localPath;
            
            if ( file_exists( $globalPath ) )
            {
                $doc = new PrintServiceDocument;
                
                $doc->title = basename( $localPath );
                $doc->localPath = $localPath;
                $doc->globalPath = $globalPath;
                $doc->length = filesize( $globalPath );
                $doc->hash = md5_file( $globalPath );
                $doc->publisher = claro_get_current_user_id();
                $doc->courseId = claro_get_current_course_id();
                
                return $doc;
            }
            else
            {
                throw new Exception( 'File not found : ' . htmlspecialchars($localPath) );
            }
        }
    }
?>