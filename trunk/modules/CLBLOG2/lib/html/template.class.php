<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * @author  Frederic Minne <zefredz@claroline.net>
     * @copyright Copyright &copy; 2006-2007, Frederic Minne
     * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @version 1.0
     * @package HTML
     */
    
    
    class HTML_Template
    {
        protected $_tpl = '';
        protected $_allowCallback = false;
        protected $_callBack = array();
        
        function __construct( $tpl )
        {
            $this->_tpl = $tpl;
        }
        
        function allowCallback()
        {
            $this->_allowCallback = true;
        }
        
        function registerCallback( $key, $callback )
        {
            $this->_callBack[$key] = $callback;
        }
        
        function render( $data )
        {
            $output = $this->_tpl;
            
            foreach ( $data as $key => $value )
            {
                $output = str_replace( "%$key%", $value, $output );
                $output = str_replace( "%html($key)%", claro_htmlspecialchars( $value ), $output );
                $output = str_replace( "%uu($key)%", rawurlencode( $value ), $output );
                $output = str_replace( "%int($key)%", (int) $value, $output );
                
                if ( $this->_allowCallback && array_key_exists( $key, $this->_callBack ) )
                {
                    $matches = array();
                    
                    if ( preg_match( "/%apply\(\s*([\w_]+)\s*,\s*(".$key.")\s*\)%/", $output, $matches ) )
                    {
                        if ( $this->_callBack[$key] == $matches[1] )
                        {
                            $replacement = call_user_func( $matches[1], $value, $matches[2] );
                            $output = preg_replace( "/%apply\(\s*([\w_]+)\s*,\s*(".$key.")\s*\)%/"
                                , $replacement, $output );
                        }
                    }
                }
            }
            
            return $output;
        }
    }

