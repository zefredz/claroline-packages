<?php // $Id$
/**
 * Student Monitoring Tool
 *
 * @version     ICMONIT 1.0.0 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICMONIT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class UCREPORT_Portlet extends UserDesktopPortlet
{
    public function __construct()
    {
        $this->name = 'My report';
        $this->label = 'ICMONIT_Portlet';
    }
    
    public function renderContent()
    {
        $output = '<div id="portletMyReport">' . "\n"
                . '<img src="' . get_icon_url( 'loading' ) . '" alt="loading" />' . "\n"
                . '</div>' . "\n"
                . '<div style="clear: both;" ></div>' . "\n";
        
        $output .= '<script type="text/javascript">' . "\n"
                .  '    $(document).ready(function(){' . "\n"
                .  '        $("#portletMyReport").load("' . get_module_url( 'ICMONIT' ) . '/myreport.php" );' . "\n"
                .  '    })'
                .  '</script>';
        
        return $output;
    }
    
    public function renderTitle()
    {
        return get_lang( 'My report' );
    }
}
