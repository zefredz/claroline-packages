<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    if ( count( get_included_files() ) == 1 ) die( '---' );
    
    /**
     * Javascript helper class
     * 
     * @author  Frederic Minne <zefredz@claroline.net>
     * @copyright Copyright &copy; 2006-2007, Frederic Minne
     * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @version 1.0
     * @package Javascript
     */
    class JavascriptHelper
    {
        /**
         * Include a remote javascript lib in html
         * @param   string  $libUrl url of the library
         * @return  string  html inclusion code
         */
        function includeJavascript( $libUrl )
        {
            return "\n" 
                . '<script type="text/javascript" src="'.$libUrl.'"></script>' 
                . "\n"
                ;
        }
        
        /**
         * Embed javascript code in HTML flux
         * @param   string  $code   javsacript code to embed
         * @return  string  html embed code
         */
        function embedJavascript( $code )
        {
            return "\n<script type=\"text/javascript\">\n"
                . $code
                . "\n</script>\n"
                ;
        }
        
        /**
         * Check if javascript is enabled on the client by using a cookie
         */
        function javascriptEnabled()
        {
            return ( isset( $_COOKIE['javascriptEnabled'] )
                && $_COOKIE['javascriptEnabled'] );
        }
    }
?>
