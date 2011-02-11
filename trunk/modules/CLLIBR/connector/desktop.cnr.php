<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.2.4 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class CLLIBR_Portlet extends UserDesktopPortlet
{
    public function renderContent()
    {
        $output = '<div id="portletMyBookmark">' . "\n"
                . '<img src="' . get_icon_url( 'loading' ) . '" alt="loading" />' . "\n"
                . '</div>' . "\n"
                . '<div style="clear: both;" ></div>' . "\n";
        
        $output .= '<script type="text/javascript">' . "\n"
                .  '    $(document).ready(function(){' . "\n"
                .  '        $("#portletMyBookmark").load("' . get_module_url( 'CLLIBR' ) . '/mybookmark.php" );' . "\n"
                .  '    })'
                .  '</script>';
        
        return $output;
    }
    
    public function renderTitle()
    {
        return get_lang( 'My Bookmark' );
    }
}