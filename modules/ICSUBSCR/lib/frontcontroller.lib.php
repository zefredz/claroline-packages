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

class FrontController extends ICSUBSCR_Controller
{
    public function __construct( $model , $id = null , $allowedToEdit = false )
    {
        parent::__construct( $model , $sessionId , $allowedToEdit );
        
        $this->sessionList = &$this->model;
        $this->defaultCmd = 'rqShowSessionList';
        $this->dateUtil = new DateUtil( get_lang( '_date' ) );
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
    
    public function rqDeleteSession()
    {
        $question = $this->view->question( get_lang( 'delete this session?' )
                                        , 'exDeleteSession'
                                        , array( 'sessionId' => $this->id ) ) ;
        $this->addMsg( self::QUESTION , $question );
        $this->view->selectedView = 0;
    }
    
    public function exCreateSession( $data )
    {
        if( ! $data['title'] || ! $data['description'] || ! $data['type'] )
        {
            $this->addMsg( self::ERROR , 'Missing fields' );
            return;
        }
        
        if( $data['startDate'] )
        {
           $data['startDate'] = $this->dateUtil->in( $data['startDate'] );
        }
        
        if( $data['endDate'] )
        {
            $data['endDate'] = $this->dateUtil->in( $data['endDate'] );
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
    
    public function exEditSession( $data )
    {
        if( $data['startDate'] )
        {
           $data['startDate'] = $this->dateUtil->in( $data['startDate'] );
        }
        
        if( $data['endDate'] )
        {
            $data['endDate'] = $this->dateUtil->in( $data['endDate'] );
        }
        
        $this->model->getItem( $this->id )->setData( $data );
        
        if( $this->sessionList->getItem( $this->id )->save() )
        {
            $this->addMsg( self::SUCCESS , 'Session successfully modified' );
        }
        else
        {
            $this->addMsg( self::ERROR , 'Session cannot be modified' );
        }
        
        $this->view->selectedView = 0;
    }
    
    public function exDeleteSession()
    {
        if( $this->sessionList->delete( $this->id) )
        {
            $this->addMsg( self::SUCCESS , 'Session successfully deleted' );
        }
        else
        {
            $this->addMsg( self::ERROR , 'An error occur while deleting session' );
        }
        
        $this->view->selectedView = 0;
    }
    
    public function exHide()
    {
        if( ! $this->sessionList->getItem( $this->id )->hide() )
        {
            $this->addMsg( self::ERROR , 'An error occured' );
        }
        
        $this->resetView = true;
        $this->view->selectedView = 0;
    }
    
    public function exShow()
    {
        if( ! $this->sessionList->getItem( $this->id )->show() )
        {
            $this->addMsg( self::ERROR , 'An error occured' );
        }
        
        $this->resetView = true;
        $this->view->selectedView = 0;
    }
    
    public function exLock()
    {
        if( ! $this->sessionList->getItem( $this->id )->close() )
        {
            $this->addMsg( self::ERROR , 'An error occured' );
        }
        
        $this->resetView = true;
        $this->view->selectedView = 0;
    }
    
    public function exUnlock()
    {
        if( ! $this->sessionList->getItem( $this->id )->open() )
        {
            $this->addMsg( self::ERROR , 'An error occured' );
        }
        
        $this->resetView = true;
        $this->view->selectedView = 0;
    }
    
    public function exMoveUp()
    {
        if( ! $this->sessionList->up( $this->id ) )
        {
            $this->addMsg( self::ERROR , 'An error occured' );
        }
        
        $this->resetView = true;
        $this->view->selectedView = 0;
    }
    
    public function exMoveDown()
    {
        if( ! $this->sessionList->down( $this->id ) )
        {
            $this->addMsg( self::ERROR , 'An error occured' );
        }
        
        $this->resetView = true;
        $this->view->selectedView = 0;
    }
}