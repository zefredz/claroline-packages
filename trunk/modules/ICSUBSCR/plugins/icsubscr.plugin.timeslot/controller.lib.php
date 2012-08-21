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

class TimeSlotController extends PluginController
{
    public function exCreateSlot( $data )
    {
        $startTime = $data['startTime'];
        $endTime = $data['endTime'];
        $sliceNb = (int)$data['sliceNb'];
        
        if( ! empty( $data['startTime'] ) && ! empty( $data['endTime'] ) && $sliceNb > 0 )
        {
            if( $startStamp = strtotime( $startTime ) !== false
               && $endStamp = strtotime( $endTime ) !== false )
            {
                $slotList = array();
                $slotTimeLapse = ( (int)$endStamp - (int)$startStamp ) / (int)$sliceNb;
                
                for( $i = $startStamp; $i += $slotTimeLapse; $i < $endStamp )
                {
                    $slot = new Slot();
                    $slot->setDate( date( 'Y-m-d h:i:s' , $i ) );
                    $slotList[] = $slot;
                }
            }
        }
    }
    
    public function exEditSlot( $data )
    {
        return;
    }
}