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
        $this->view->selectedView = 1;
        
        $data = $this->_validate( $data );
        
        $sliceNb = (int)$data['sliceNb'];
        
        if( $sliceNb > 1 )
        {
            if( $startStamp = strtotime( $data['startDate'] ) !== false
                && $endStamp = strtotime( $data['endDate'] ) !== false )
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
                    $description = get_lang(
                        'From %startDate to %endDate',
                        array( '%startDate' => $startDate , '%endDate' => $endDate ) );
                    
                    if( ! $this->session->addSlot(
                        $slotTitle,
                        $description,
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
    
    public function exEditSlot( $data )
    {
        $this->selectedView = 1;
        
        $data = $this->_validate( $data );
        
        if( $this->session->modifySlot( $data ) )
        {
            $this->addMsg( self::SUCCESS , 'Slot successfully modified' );
        }
        else
        {
            $this->addMsg( self::ERROR , 'Slot changes failed' );
        }
    }
    
    private function _validate( $date )
    {
        if( empty( $data['title'] ) )
        {
            $this->addMsg( self::ERROR , 'Missing Title' );
            return;
        }
        
        $date = $data['date'];
        $startHour = $data['startHour'];
        $endTHour = $data['endHour'];
        
        $startDate = $this->dateUtil->in( $date , $startHour );
        $endDate = $this->dateUtil->in( $date , $endDate );
        
        if( ! $startDate || ! $endDate )
        {
            $this->addMsg( self::ERROR , 'Invalid date' );
            return;
        }
        else
        {
            $data['startDate'] = $startDate;
            $data['endDate'] = $endDate;
            unset( $data['date'] , $data['startHour'] , $data['endHour'] );
        }
        
        return $data;
    }
}