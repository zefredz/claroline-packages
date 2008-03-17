<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:

    /**
     * Flat text datagrid template
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
    
    require_once dirname(__FILE__) . '/../template.class.php';
    
    class DatagridFlat extends SimpleTemplate
    {
        protected $template;
        protected $data;
        protected $footer = '';
        protected $header = '';
        protected $emptyMessage = '<!-- empty -->';
        
        public function __construct()
        {
            parent::__construct();
        }
        
        public function setEmptyMessage( $str )
        {
            $this->emptyMessage = $str;
        }
        
        public function setData( $data )
        {
            $this->data = $data;
        }
        
        public function setTemplate( $template )
        {
            $this->template = $template;
        }
        
        public function setFooter( $footer )
        {
            $this->footer = $footer;
        }
        
        public function setHeader( $header )
        {
            $this->header = $header;
        }
        
        public function render()
        {
            $output = '';
            
            if ( !empty( $this->header ) )
            {
                $output .= $this->header  . "\n";
            }
            
            if ( count( $this->data ) > 0 )
            {
                foreach ( $this->data as $row )
                {
                    if ( is_object( $row ) )
                    {
                        $row = (array) $row;
                    }
                    
                    $output .= parent::render( $this->template, $row );
                }
            }
            else
            {
                $output .= $this->emptyMessage;
            }
            
            if ( !empty( $this->footer ) )
            {
                $output .= $this->footer . "\n";
            }
            
            return $output;
        }
    }
?>