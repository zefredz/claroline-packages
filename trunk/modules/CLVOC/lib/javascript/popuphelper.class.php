<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    if ( count( get_included_files() ) == 1 ) die( '---' );
    
    require_once dirname(__FILE__) . '/javascripthelper.class.php';
    
    /**
     * Popup helper class
     * 
     * @see popup.js
     * @author  Frederic Minne <zefredz@claroline.net>
     * @copyright Copyright &copy; 2006-2007, Frederic Minne
     * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @version 1.0
     * @package Javascript::Popup
     */
    class PopupHelper
    {        
        /**
         * Create popup call html code
         * @return  string
         */
        function popup( $url, $title = '', $width = 300, $height = 300 )
        {
            $popup = "popup( '" . $url . "'" . ", '" . $title 
                    . "', " . $width . "," . $height . ");"
                    ;
                    
            return $popup;
        }
        
        function popupLink( $url, $text, $title = '', $width = 300, $height = 300, $class = '', $img = '' )
        {
            $class = empty( $class ) ? '' : ' class="' . $class . '"';
            $img = empty( $img ) ? '' : '<img src="'.$img.'" alt="" style="border:0px" />&nbsp;';
            if ( JavascriptHelper::javascriptEnabled() )
            {
                return '<a href="#" onclick="'
                    . PopupHelper::popup( $url, $title, $width, $height )
                    . 'return false;"' . $class
                    . '>'
                    . $img
                    . $text
                    . '</a>'
                    ;
            }
            else
            {
                return '<a href="'.$url.'" target="_blank"'.$class.'>'
                    . $text
                    . '</a>'
                    ;
            }
        }
    }
?>
