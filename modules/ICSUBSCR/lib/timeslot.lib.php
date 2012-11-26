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

class Timeslot extends SlotList
{
    public function createSlot( $startDate , $endDate , $sliceNb = 1 , $availableSpace = 0 )
    {
        if( $startStamp = strtotime( $startDate ) !== false
            && $endStamp = strtotime( $endDate ) !== false )
        {
            $slotList = array();
            $slotTimeLapse = ( (int)$endStamp - (int)$startStamp ) / (int)$sliceNb;
            
            for( $i = $startStamp; $i += $slotTimeLapse; $i < $endStamp )
            {
                $startDate = date( 'Y-m-d h:i:s' , $i );
                $endDate = date( 'Y-m-d h:i:s' , $i + $slotTimeLapse );
                $label = get_lang(
                    'From %startDate to %endDate',
                    array( '%startDate' => $startDate , '%endDate' => $endDate ) );
                
                $slot = new Slot( $this->listId );
                $slot->setLabel( $label );
                $slot->setStartDate( $startDate );
                $slot->setEndDate( $enDdate );
                
                if( ! $this->add( $slot->save() ) )
                {
                    return false;
                }
            }
            
            return true;
        }
        else
        {
            return false;
        }
    }
}