<?php  // $Id$
    
    // vim: expandtab sw=4 ts=4 sts=4:
    
    if ( count( get_included_files() ) == 1 ) die( '---' );
    
    /**
     * @author  Frederic Minne <zefredz@claroline.net>
     * @copyright Copyright &copy; 2006-2007, Frederic Minne
     * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @version 1.0
     * @package HTML.Datagrid
     */
    
    class HTML_Datagrid_Table2
    {
        var $data = array();
        var $dataFields = array();
        var $dataUrls = array();
        var $actionFields = array();
        var $actionUrls = array();
        var $footer = '';
        var $displayIfEmpty = true;
        var $emptyMessage = null;
        var $title = '';
        
        function setTitle( $title )
        {
            $this->title = $title;
        }
        
        function setEmptyMessage( $str )
        {
            $this->emptyMessage = $str;
        }
        
        function setDataFields( $dataFields )
        {
            $this->dataFields = $dataFields;
        }
        
        function setData( $data )
        {
            $this->data = $data;
        }
        
        function setDataUrls( $dataUrls )
        {
            $this->dataUrls = $dataUrls;
        }
        
        function setActionFields( $actionFields )
        {
            $this->actionFields = $actionFields;
        }
        
        function setActionUrls( $actionUrls )
        {
            $this->actionUrls = $actionUrls;
        }
        
        function setFooter( $footer )
        {
            $this->footer = $footer;
        }
        
        function disableDisplayIfEmpty()
        {
            $this->displayIfEmpty = false;
        }
        
        function enableDisplayIfEmpty()
        {
            $this->displayIfEmpty = true;
        }
        
        function render()
        {
            if ( false === $this->displayIfEmpty && empty( $this->data ) )
            {
                return '';
            }
            
            $colspan = count( $this->dataFields ) + count( $this->actionFields );
            
            $colspan = ' colspan="'.$colspan.'"';
            
            // table head
            
            $table = '<table class="claroTable emphaseLine" style="width: 100%">' . "\n"
                . (!empty($this->title) ? '<caption>' . $this->title . '</caption>' . "\n" : '' )
                . '<thead>' . "\n"
                . '<tr class="superHeader">' . "\n"
                ;
                    
            foreach ( $this->dataFields as $field )
            {
                $table .= '<th>'
                    . htmlspecialchars( $field )
                    . '</th>'
                    ;
            }
            
            foreach ( $this->actionFields as $field )
            {
                $table .= '<th>'
                    . htmlspecialchars( $field )
                    . '</th>'
                    ;
            }
                    
            $table .= '</tr>' . "\n"
                . '</thead>' . "\n"
                ;
                
            // table body
            
            $table .= '<tbody>' . "\n";
            
            if ( count( $this->data ) > 0 )
            {
                foreach ( $this->data as $row )
                {
                    $table .= '<tr>';
                    
                    foreach ( array_keys($this->dataFields) as $key ) // => $value )
                    {
                        if ( array_key_exists( $key, $this->dataUrls ) )
                        {
                            $table .= '<td>';
                            
                            if ( !is_null( $row[$key] ) )
                            {
                            
                                $table .= str_replace( '%'.$key.'%', htmlspecialchars($row[$key]),
                                    str_replace( '%uu('.$key.')%', rawurlencode($row[$key]), $this->dataUrls[$key] ));
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
                        foreach ( array_keys( $row ) as $key )
                        {
                            $table .= str_replace( '%'.$key.'%', htmlspecialchars($row[$key]),
                                str_replace( '%uu('.$key.')%', rawurlencode($row[$key]), $url ));
                        }
                    }
                    
                    $table .= '</tr>' . "\n";
                }
            }
            else
            {
                if ( is_null( $this->emptyMessage ) )
                {
                    $this->emptyMessage = 'Empty';
                }
                
                $table .= '<tr><td' . $colspan . '>' . $this->emptyMessage . '</td></tr>' . "\n";
            }
            
            $table .= '</tbody>' . "\n";
            
            // table foot
            
            if ( !empty( $this->footer ) )
            {
                $table .= '<tfoot>' . "\n"
                    . '<tr>'
                    . '<td'.$colspan.'>'
                    . $this->footer
                    . '</td>'
                    . '</tr>' . "\n"
                    . '</tfoot>'
                    . "\n"
                    ;
            }
            
            // end of table
            
            $table .= '</table>' . "\n";
            
            return $table;
        }
    }
?>