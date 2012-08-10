<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.0.2 $Revision$ - Claroline 1.9
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICSUBSCR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

abstract class PluginController extends ICSUBSCR_Controller
{
    public function rqShowSession()
    {
        $this->selectedView = 0;
    }
    
    public function exSubcribe( $slotList , $userId = null , $groupId = null )
    {
        $record = new Record( $this->model , $userId , $groupId );
        
        if( ! $record->subscribe( $slotList ) )
        {
            $this->output[] = array( 'error' => 'Cannot save subscription' );
        }
        
        $this->selectedView = 0;
    }
}