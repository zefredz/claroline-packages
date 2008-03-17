<?php  // $Id$
    
    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * HTML list datagrid template
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
    
    class DatagridList extends DatagridTable
    {
        protected $ordered;
        
        public function __construct( $ordered = false )
        {
            $this->ordered = $ordered;
            parent::__construct();
        }
        
        public function render()
        {
            // table head
            
            $list = '';
            
            if ( count( $this->data ) > 0 )
            {
                $list = ( $this->ordered ? '<ol>' : '<ul>' ) . "\n";
                
                foreach ( $this->data as $row )
                {
                    if ( is_object( $row ) )
                    {
                        $row = (array) $row;
                    }
                    
                    $list .= '<li><ul>';
                    
                    foreach ( $this->dataFields as $key => $value )
                    {
                        if ( array_key_exists( $key, $this->dataUrls ) )
                        {
                            $list .= '<li>'. htmlspecialchars( $value ) . ' : '
                                . $this->replaceKey(  $this->dataUrls[$key], $key, $row[$key], $this->_allowCallback, $this->_callBack )
                                . '</li>'
                                ;
                        }
                        else
                        {
                            $list .= '<li>' 
                                . htmlspecialchars( $value ) . ' : '
                                . htmlspecialchars( $row[$key] ) 
                                . '</li>' . "\n"
                                ;
                        }
                    }
                    
                    // whoops : this will surely not work...
                    foreach ( $this->actionFields as $key => $value )
                    {
                        $list .= '<li>' 
                            . $this->replaceKey(  $this->actionUrls[$key], $key, $value, $this->_allowCallback, $this->_callBack ) 
                            . '</li>' . "\n"
                            ; 
                    }
                    
                    $list .= '</ul></li>' . "\n";
                }
                
                $list = ( $this->ordered ? '</ol>' : '</ul>' ) . "\n";
            }
            else
            {
                $list .= '<p>' . get_lang('Empty') . '</p>' . "\n";
            }
            
            
            if ( !empty( $this->footer ) )
            {
                $table .= '<p>' . "\n"
                    . $this->footer
                    . '</p>'
                    . "\n"
                    ;
            }
            
            return $list;
        }
    }
?>