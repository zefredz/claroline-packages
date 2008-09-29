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
    
    class PDOMapperForm
    {
        protected $schema;
        
        public function __construct( $schema )
        {
            $this->schema = $schema;
        }
        
        public function render()
        {
            // foreach editable attributes, create input with the good values
            // add one links for any relations
            // edit link for any relations
        }
        
        public function renderHasManyList( $name )
        {
        }
    }  
?>