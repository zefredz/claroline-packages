<?php // $Id$
    
    // vim: expandtab sw=4 ts=4 sts=4:
    
    if ( count( get_included_files() ) == 1 ) die( '---' );
    
    /**
     * @author  Frederic Minne <zefredz@claroline.net>
     * @copyright Copyright &copy; 2006-2007, Frederic Minne
     * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @version 1.0
     * @package HTML
     */
    
    class HTML_Popup_Helper
    {
        function windowClose()
        {
            return '<p style="text-align:center;"><a href="#" onclick="window.close()">
Close window</a></p>' . "\n";
        }
        
        function popupEmbed( $content )
        {
            $out = HTML_Popup_Helper::windowClose();
            $out .= $content;
            $out .= HTML_Popup_Helper::windowClose();
            
            return $out;
        }
    }
?>
