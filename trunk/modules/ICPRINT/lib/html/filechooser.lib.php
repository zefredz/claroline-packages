<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * Description
     *
     * @version     1.9 $Revision$
     * @copyright   2001-2007 Universite catholique de Louvain (UCL)
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
    
    require_once dirname(__FILE__) . '/form.lib.php';
    
    class FormFileChooser extends SelectBox
    {
        protected $fileList;
        
        public function __construct( $name, $fileList, $extra = null )
        {
            if ( ! is_array( $extra ) ) $extra = array();
            $extra['multiple'] = 'multiple';
            $extra['size'] = count( $fileList ) > 10 ? 10 : count( $fileList );
            
            parent::__construct( $name, $extra );
            $this->fileList = $fileList;
            
            foreach ( $fileList as $file )
            {
                $this->addOption( new Option( $file['name'],  md5($file['path']) ) );
            }
        }
    }
    
?>