<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * Simple template system
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
    
    class SimpleTemplate
    {
        protected $_allowCallback;
        protected $_callBack;
        
        public function __construct()
        {
            $this->_allowCallback = false;
            $this->_callBack = array();
        }
        
        public function allowCallback()
        {
            $this->_allowCallback = true;
        }
        
        public function registerCallback( $key, $callback )
        {
            $this->_callBack[$key] = $callback;
        }
        
        public function render( $tpl, $data )
        {
            if ( is_object( $data ) )
            {
                $data = (array) $data;
            }
            
            $output = $tpl;
            
            foreach ( $data as $key => $value )
            {
                $output = $this->replaceKey( $output, $key, $value, $this->_allowCallback, $this->_callBack );
            }
            
            return $output;
        }
        
        protected function replaceKey( $output, $key, $value, $allowCallback = false, $callbackList = null )
        {
            $output = str_replace( "%$key%", $value, $output );
            $output = str_replace( "%html($key)%", htmlspecialchars( $value ), $output );
            $output = str_replace( "%uu($key)%", rawurlencode( $value ), $output );
            $output = str_replace( "%int($key)%", (int) $value, $output );
            
            if ( $allowCallback 
                && is_array( $callbackList ) 
                && array_key_exists( $key, $callbackList ) )
            {
                $matches = array();
                
                if ( preg_match( "/%apply\(\s*([\w_]+)\s*,\s*(".$key.")\s*\)%/", $output, $matches ) )
                {
                    if ( $callbackList[$key] == $matches[1] )
                    {
                        $replacement = call_user_func( $matches[1], $value, $matches[2] );
                        $output = preg_replace( "/%apply\(\s*([\w_]+)\s*,\s*(".$key.")\s*\)%/"
                            , $replacement, $output );
                    }
                }
            }
            
            return $output;
        }
    }
?>
