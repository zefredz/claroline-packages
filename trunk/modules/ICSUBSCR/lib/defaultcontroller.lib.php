<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.0.2 $Revision$ - Claroline 1.11
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICSUBSCR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class DefaultController extends ICSUBSCR_Controller
{
    public function __construct( $model , $sessionId = null , $allowedToEdit = false )
    {
        parent::__construct( $model , $sessionId , $allowedToEdit );
        
        $this->view = new DefaultView();
    }
    
    public function rqShowSessionList()
    {
        $this->view->selectedView = 0;
    }
    
    public function rqCreateSession()
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
    
    public function exCreateSession( $data )
    {
        if( ! $data['title'] || ! $data['description'] || ! $data['type'] )
        {
            $this->addMsg( self::ERROR , 'Missing fields' );
            return;
        }
        
        if( $this->id = $this->model->add( $data ) )
        {
            $this->addMsg( self::SUCCESS , 'Session successfully created' );
        }
        else
        {
            $this->addMsg( self::ERROR , 'Session cannot be created' );
        }
        
        $this->view->selectedView = 1;
    }
    
    public function rqEditSession()
    {
        $this->view->selectedView = 1;
    }
    
    public function exEditSession( $data )
    {
        $this->model->modify( $this->id , $data );
        
        if( $this->model->save( $this->id ) )
        {
            $this->addMsg( self::SUCCESS , 'Session successfully modified' );
        }
        else
        {
            $this->addMsg( self::ERROR , 'Session cannot be modified' );
        }
        
        $this->view->selectedView = 0;
    }
    
    public function exDeleteSession( $sessionId )
    {
        if( $this->model->delete( $sessionId ) )
        {
            $this->addMsg( self::SUCCESS , 'Session successfully deleted' );
        }
        else
        {
            $this->addMsg( self::ERROR , 'An error occur while deleting session' );
        }
        
        $this->view->selectedView = 0;
    }
    
    public function exHide( $sessionId )
    {
        if( ! $this->model->setInvisible( $sessionId ) )
        {
            $this->addMsg( self::ERROR , 'An error occur' );
        }
        
        $this->view->selectedView = 0;
    }
    
    public function exShow( $sessionId )
    {
        if( ! $sessionList->setVisible( $sessionId ) )
        {
            $this->addMsg( self::ERROR , 'An error occur' );
        }
        
        $this->view->selectedView = 0;
    }
    
    public function exLock( $sessionId )
    {
        if( ! $sessionList->setClosed( $sessionId ) )
        {
            $this->addMsg( self::ERROR , 'An error occur' );
        }
        
        $this->view->selectedView = 0;
    }
    
    public function exUnlock( $sessionId )
    {
        if( ! $sessionList->setOpen( $sessionId ) )
        {
            $this->addMsg( self::ERROR , 'An error occur' );
        }
        
        $this->view->selectedView = 0;
    }
}