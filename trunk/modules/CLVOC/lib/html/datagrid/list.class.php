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
     
    require_once dirname(__FILE__) . '/table.class.php';
    
    class HTML_Datagrid_List extends HTML_Datagrid_table
    {
        var $ordered = false;
        
        function render()
        {
            // table head
            
            $list = '';
            
            if ( count( $this->data ) > 0 )
            {
                $list = ( $this->ordered ? '<ol>' : '<ul>' ) . "\n";
                
                foreach ( $this->data as $row )
                {
                    $list .= '<li><ul>';
                    
                    foreach ( $this->dataFields as $key => $value )
                    {
                        if ( $key != $this->actionField || $this->displayActionField )
                        {
                            if ( array_key_exists( $key, $this->dataUrls ) )
                            {
                                $list .= '<li>'. htmlspecialchars( $value ) . ' : ';
                                
                                $list .= str_replace( '%'.$key.'%', htmlspecialchars($row[$key]),
                                    str_replace( '%ACTION_FIELD%', $row[$this->actionField], $this->dataUrls[$key] ));
                                
                                $list .= '</li>';
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
                    }
                    
                    foreach ( $this->actionFields as $key => $value )
                    {
                        $list .= '<li>' 
                            . htmlspecialchars( $value ) . ' : '
                            . str_replace( '%ACTION_FIELD%', $row[$this->actionField]
                                , $this->actionUrls[$key] )  
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
