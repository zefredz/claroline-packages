<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.1 $Revision$ - Claroline 1.11
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICSUBSCR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class GenericView extends PluginView
{
    public function __construct()
    {
        $additionalCmdList = array();
        
        parent::__construct( $additionalCmdList );
    }
    
    public function setCmdList()
    {
        return;
    }
}