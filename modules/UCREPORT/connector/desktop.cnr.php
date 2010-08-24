<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 0.7.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class UCREPORT_Portlet extends UserDesktopPortlet
{
    public function renderContent()
    {
        $output = '<div id="portletMyReport">' . "\n"
                . '<img src="' . get_icon_url( 'loading' ) . '" alt="loading" />' . "\n"
                . '</div>' . "\n"
                . '<div style="clear: both;" ></div>' . "\n";
        
        $output .= '<script type="text/javascript">' . "\n"
                .  '    $(document).ready(function(){' . "\n"
                .  '        $("#portletMyReport").load("' . get_module_url( 'UCREPORT' ) . '/myreport.php" );' . "\n"
                .  '    })'
                .  '</script>';
        
        return $output;
    }
    
    public function renderTitle()
    {
        return get_lang( 'My report' );
    }
}