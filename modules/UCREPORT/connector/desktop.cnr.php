<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 2.4.3 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class UCREPORT_Portlet extends UserDesktopPortlet
{
    public function __construct()
    {
        $this->name = 'My report';
        $this->label = 'UCREPORT_Portlet';
    }
    
    public function renderContent()
    {
        $output = '<div id="portletMyReport">' . "\n"
                . '<img src="' . get_icon_url( 'loading' ) . '" alt="loading" />' . "\n"
                . '</div>' . "\n"
                ;
        
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