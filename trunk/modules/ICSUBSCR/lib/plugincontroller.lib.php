<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.0.1 $Revision$ - Claroline 1.9
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICSUBSCR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

abstract class PluginController
{
    public function __construct( $session )
    {
        $this->session = $session;
    }
    
    /**
     * Executes command
     */
    public function execute( $cmd )
    {
        if( method_exists( $this , '_' . $cmd ) )
        {
            $this->{'_' . $cmd}();
            $this->_output();
        }
        else
        {
            $this->message[] = array( 'type' => 'error'
                                    , 'text' => 'invalid_command' );
        }
    }
    
    /**
     * Output
     */
    private function _output()
    {
        $view = new PhpTemplate( get_module_path($moduleLabel)
                                . '/plugins/icsubscr.' . $label
                                . '.plugin/templates/'. $template );
    }
}