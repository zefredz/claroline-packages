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

abstract class PluginController extends ICSUBSCR_Controller
{
    public function __construct( $model , $slotId = null , $allowedToEdit = false )
    {
        parent::__construct( $model , $slotId , $allowedToEdit );
        
        $this->record = &$this->model;
        $this->session = &$this->model->session;
        
        $this->defaultCmd = 'rqView';
    }
    
    public function rqShowSession()
    {
        $this->selectedView = 0;
    }
    
    public function exSubcribe( $slotList )
    {
        if( ! $this->record->subscribe( $slotList ) )
        {
            $this->output[] = array( 'error' => 'Cannot save subscription' );
        }
        
        $this->selectedView = 0;
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
        
        $this->selectedView = 0;
    }
    
    abstract public function exCreateSlot( $data );
    abstract public function exEditSlot( $data );
}