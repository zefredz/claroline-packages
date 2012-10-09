<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.4 $Revision$ - Claroline 1.11
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICSUBSCR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class SessionController extends ICSUBSCR_Controller
{
    public function __construct( $record , $slotId = null , $allowedToEdit = false )
    {
        parent::__construct( $record , $slotId , $allowedToEdit );
        
        $this->record = &$this->model;
        $this->session = &$this->model->session;
        
        $this->dateUtil = new DateUtil( get_lang( '_date' ) );
        $this->defaultCmd = 'rqView';
    }
    
    public function rqShowSession()
    {
        $this->view->selectedView = 0;
    }
    
    public function exSubcribe( $slotList )
    {
        if( ! $this->record->subscribe( $slotList ) )
        {
            $this->output[] = array( 'error' => 'Cannot save subscription' );
        }
        
        $this->view->selectedView = 0;
    }
    
    public function rqView()
    {
        $this->view->selectedView = 0;
    }
    
    public function rqCreateSlot()
    {
        if( $this->allowedToEdit )
        {
            $this->view->selectedView = 1;
        }
        else
        {
            $this->addMsg( self::ERROR , 'Not allowed' );
            $this->view->selectedView = 0;
        }
    }
    
    public function rqEditSlot()
    {
        if( $this->allowedToEdit )
        {
            $this->view->selectedView = 1;
        }
        else
        {
            $this->addMsg( self::ERROR , 'Not allowed' );
            $this->view->selectedView = 0;
        }
    }
    
    public function rqDeleteSlot()
    {
        $question = $this->view->question( get_lang( 'delete this session?' )
                                        , 'exDeleteSlot'
                                        , array( 'slotId' => $this->id ) ) ;
        $this->addMsg( self::QUESTION , $question );
        $this->view->selectedView = 0;
    }
    
    public function exDeleteSlot()
    {
        if( ! $this->session->deleteSlot( $this->id ) )
        {
            $this->output[] = array( 'error' => 'Cannot delete slot' );
        }
        
        $this->view->selectedView = 0;
    }
    
    public function exCreateSlot( $data )
    {
        $this->view->selectedView = 1;
        
        $data = $this->validate( $data );
        
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
        
        $data = $this->validate( $data );
        
        if( $this->session->modifySlot( $data ) )
        {
            $this->addMsg( self::SUCCESS , 'Slot successfully modified' );
        }
        else
        {
            $this->addMsg( self::ERROR , 'Slot changes failed' );
        }
    }
    
    private function validate( $date )
    {
        if( empty( $data['title'] ) )
        {
            $this->addMsg( self::ERROR , 'Missing Title' );
            return;
        }
        
        if( $this->session->getType() == Session::ENUM_TYPE_UNDATED )
            return $this->flush( $data );
        
        $date = $data['date'];
        $startHour = $data['startHour'];
        $startDate = $this->dateUtil->in( $date , $startHour );
        
        if( ! $startDate )
        {
            $this->addMsg( self::ERROR , 'Invalid start date' );
            return;
        }
        
        $data['startDate'] = $startDate;
        
        if( $this->session->getType == Session::ENUM_TYPE_DATED )
            return $this->flush( $data );
        
        $endTHour = $data['endHour'];
        $endDate = $this->dateUtil->in( $date , $endHour );
        
        if( ! $endDate )
        {
            $this->addMsg( self::ERROR , 'Invalid start date' );
            return;
        }
        
        $data['endDate'] = $endDate;
        
        return $this->flush( $data );
    }
    
    private function flush( $data )
    {
        unset( $data['date'] , $data['startHour'] , $data['endHour'] );
        return $data;
    }
}