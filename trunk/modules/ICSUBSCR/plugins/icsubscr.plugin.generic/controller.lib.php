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

class GenericController extends PluginController
{
    public function exCreateSlot( $data )
    {
        $this->view->selectedView = 1;
        
        if( empty( $data['title'] ) )
        {
            $this->addMsg( self::ERROR , 'Missing Title' );
            return;
        }
        
        if( $this->session->addSlot( $data ) )
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
        $this->view->selectedView = 1;
        
        if( empty( $data['title'] ) )
        {
            $this->addMsg( self::ERROR , 'Missing Title' );
            return;
        }
        
        if( $this->session->mofifySlot( $this->id , $data ) )
        {
            $this->addMsg( self::SUCCESS , 'Slot successfully modified' );
        }
        else
        {
            $this->addMsg( self::ERROR , 'Slot changes failed' );
        }
    }
}