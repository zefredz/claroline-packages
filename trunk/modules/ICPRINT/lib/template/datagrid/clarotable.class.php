<?php  // $Id$
    
    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * HTML tablet datagrid template
     *
     * @version     $Revision$
     * @copyright   2001-2007 Universite catholique de Louvain (UCL)
     * @author      Frederic Minne <zefredz@claroline.net>
     * @license     http://www.gnu.org/copyleft/gpl.html
     *              GNU GENERAL PUBLIC LICENSE version 2 or later
     * @package     template
     */
     
    if ( count( get_included_files() ) == 1 )
    {
        die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
    }
     
    require_once dirname(__FILE__) . '/table.class.php';
    
    class DatagridClaroTable extends DatagridTable
    {
        protected $superHdr = '';
        
        public function __construct()
        {
            parent::__construct();
            $this->addStyleClass( 'claroTable' );
        }
        
        public function emphaseLine()
        {
            $this->addStyleClass( 'emphaseLine' );
        }
        
        public function setSuperHeader( $superHdr )
        {
            $this->superHdr = $superHdr;
        }
        
        protected function _renderTableHead()
        {
            $table = '<thead>' . "\n";
            
            $table .= !empty($this->superHdr) 
                ? '<tr class="superHeader">'.$this->superHdr.'</tr>' . "\n"
                : ''
                ;
            
            $table.= '<tr class="headerX">';
            
            foreach ( $this->dataFields as $field )
            {
                $table .= '<th>'
                    . $field
                    . '</th>'
                    ;
            }
            
            foreach ( $this->actionFields as $field )
            {
                $table .= '<th>'
                    . $field
                    . '</th>'
                    ;
            }
                    
            $table .= '</tr>' . "\n"
                . '</thead>' . "\n"
                ;
                
            return $table;       
        }
    }
?>