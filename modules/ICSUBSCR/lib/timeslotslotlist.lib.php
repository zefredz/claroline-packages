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

class TimeslotSlotList extends ICSUBSCR_List
{
    public function __contruct( $sessionId )
    {
        parent::__construct( 'slot' , $sessionId );
    }
    
    public function exCreateSlot( $startDate , $endDate , $sliceNb , $availableSpace = 0 )
    {
        if( $sliceNb > 1 )
        {
            if( $startStamp = strtotime( $startDate ) !== false
                && $endStamp = strtotime( $endDate ) !== false )
            {
                $slotList = array();
                $slotTimeLapse = ( (int)$endStamp - (int)$startStamp ) / (int)$sliceNb;
                $slotIndex = 1;
                $errorList = array();
                
                for( $i = $startStamp; $i += $slotTimeLapse; $i < $endStamp )
                {
                    $slotTitle = $data['title'] . ' ' . $slotIndex++;
                    $startDate = date( 'Y-m-d h:i:s' , $i );
                    $endDate = date( 'Y-m-d h:i:s' , $i + $slotTimeLapse );
                    $label = get_lang(
                        'From %startDate to %endDate',
                        array( '%startDate' => $startDate , '%endDate' => $endDate ) );
                    
                    if( ! $this->session->addSlot(
                        $label,
                        $startDate,
                        $endDate,
                        $availableSpace ) )
                    {
                        $this->addMsg(
                            self::ERROR,
                            'Error while creating the following slots : ' . $description );
                        return;
                    }
                }
                
                $this->addMsg( self::SUCCESS , 'Slots successfully created' );
            }
        }
        elseif( $this->session->addSlot(
            $slotTitle,
            $description,
            $startDate,
            $endDate,
            $availableSpace ) )
        {
            $this->addMsg( self::SUCCESS , 'Slot successfully created' );
        }
        else
        {
            $this->addMsg( self::ERROR , 'Slot cannot be created' );
        }
    }
}