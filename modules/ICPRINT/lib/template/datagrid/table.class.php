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
     
    require_once dirname(__FILE__) . '/../template.class.php';
    
    class DatagridTable extends SimpleTemplate
    {
        protected $lineNumber = 0;
        protected $data = array();
        protected $dataFields = array();
        protected $dataUrls = array();
        protected $actionFields = array();
        protected $actionUrls = array();
        protected $footer = '';
        protected $emptyMessage = '<!-- empty -->';
        protected $title = '';
        protected $_styleClasses = array();
        protected $_style = array();
        
        protected $tableAttributes = '';
        
        public function __construct()
        {
            parent::__construct();
        }
        
        public function fullWidth()
        {
            $this->_style[] = 'width:100%';
        }
        
        public function addStyleClass( $class )
        {
            $this->_styleClasses[] = $class;
        }
        
        public function addStyle( $cssStyle )
        {
            $this->_style[] = $cssStyle;
        }
        
        public function setTitle( $title )
        {
            $this->title = $title;
        }
        
        public function setEmptyMessage( $str )
        {
            $this->emptyMessage = $str;
        }
        
        public function setDataFields( $dataFields )
        {
            $this->dataFields = $dataFields;
        }
        
        public function setData( $data )
        {
            $this->data = $data;
        }
        
        public function setDataUrls( $dataUrls )
        {
            $this->dataUrls = $dataUrls;
        }
        
        public function setActionFields( $actionFields )
        {
            $this->actionFields = $actionFields;
        }
        
        public function setActionUrls( $actionUrls )
        {
            $this->actionUrls = $actionUrls;
        }
        
        public function setFooter( $footer )
        {
            $this->footer = $footer;
        }
        
        public function setTableAttributes( $attr )
        {
            $this->tableAttributes = $attr;
        }
        
        public function getColsCount()
        {
            return count( $this->dataFields ) + count( $this->actionFields );
        }
        
        public function getRowsCount()
        {
            return count( $this->data );
        }
        
        protected function _getStyleClasses()
        {
            return (!empty( $this->_styleClasses ) 
                ?' class="'.implode( ' ', $this->_styleClasses ).'"'
                : ''
            );
        }
        
        protected function _getStyle()
        {
            return ( !empty( $this->_style )
                ? ' style="'.implode( ';', $this->_style ).'"'
                : ''
            );
        }
        
        protected function _renderTableStart()
        {
            return '<table'
                . $this->_getStyleClasses()
                . $this->_getStyle()
                . $this->tableAttributes.'>' . "\n"
                ;
        }
        
        protected function _renderTableCaption()
        {
            return (!empty($this->title) ? '<caption>' . $this->title . '</caption>' . "\n" : '' );
        }
        
        protected function _renderTableHead()
        {
            $table = '<thead>' . "\n";
            
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
        
        protected function _renderTableBody()
        {
            $colspan = ' colspan="'.$this->getColsCount().'"';
            
            $table = '<tbody>' . "\n";
            
            if ( count( $this->data ) > 0 )
            {
                foreach ( $this->data as $row )
                {
                    if ( is_object( $row ) )
                    {
                        $row = (array) $row;
                    }
                    
                    $table .= '<tr>';
                    
                    foreach ( array_keys($this->dataFields) as $key ) // => $value )
                    {
                        if ( array_key_exists( $key, $this->dataUrls ) )
                        {
                            $table .= '<td>';
                            
                            if ( !is_null( $row[$key] ) )
                            {
                                $table .= $this->replaceKey( $this->dataUrls[$key], $key, $row[$key], $this->_allowCallback, $this->_callBack );
                            }
                            else
                            {
                                $table .= '&nbsp;';
                            }
                            
                            $table .= '</td>';
                        }
                        else
                        {
                            $table .= '<td>' . htmlspecialchars( $row[$key] ) . '</td>';
                        }
                    }
                    
                    foreach ( $this->actionUrls as $url )
                    {
                        $cell = $url;
                        
                        foreach ( array_keys( $row ) as $key )
                        {
                            $cell = $this->replaceKey( str_replace( '%_lineNumber%', $this->lineNumber, $cell ), $key, $row[$key], $this->_allowCallback, $this->_callBack );
                        }
                        
                        $table .= '<td>' . $cell .'</td>';
                    }
                    
                    $table .= '</tr>' . "\n";
                    
                    $this->lineNumber++;
                }
            }
            else
            {
                $table .= '<tr><td' . $colspan . '>' . $this->emptyMessage . '</td></tr>' . "\n";
            }
            
            $table .= '</tbody>' . "\n";
            
            return $table;
        }
        
        protected function _renderTableFooter()
        {
            if ( !empty( $this->footer ) )
            {
                $colspan = ' colspan="'.$this->getColsCount().'"';
                
                return '<tfoot>' . "\n"
                    . '<tr>'
                    . '<td'.$colspan.'>'
                    . $this->footer
                    . '</td>'
                    . '</tr>' . "\n"
                    . '</tfoot>'
                    . "\n"
                    ;
            }
            else
            {
                return '';
            }
        }
        
        protected function _renderTableEnd()
        {
            return '</table>' . "\n";
        }
        
        public function render()
        {
            $table = $this->_renderTableStart()
                . $this->_renderTableCaption()
                . $this->_renderTableHead()
                . $this->_renderTableBody()
                . $this->_renderTableFooter()
                . $this->_renderTableEnd()
                ;
            
            return $table;
        }
    }
?>