<?php

class grappleListener extends EventDriven
{
    var $grapple;
    var $grapple_idAssignedEvent;
    
    public function __construct()
    {
        $this->grapple = new grapple();
        $this->grapple_idAssignedEvent = ( isset( $_SESSION[ 'grapple' ][ 'previousGEBId' ] ) ? (int) $_SESSION[ 'grapple' ][ 'previousGEBId' ] : 0 );
    }
    
    public function userLogin()
    {
        $data = $this->grapple->userLogin( claro_get_current_user_id(), $this->grapple_idAssignedEvent );
        $this->grapple_idAssignedEvent = $data->idAssignedEvent;
        $_SESSION[ 'grapple' ][ 'previousGEBId' ] = $this->grapple_idAssignedEvent;        
    }
    
    public function studentEnrollment( $event )
    {
        $eventArgs = $event->getArgs();
        
        $data = $this->grapple->studentEnrollment( $eventArgs[ 'uid' ], $this->grapple_idAssignedEvent );
        $this->grapple_idAssignedEvent = $data->idAssignedEvent;
        $_SESSION[ 'grapple' ][ 'previousGEBId' ] = $this->grapple_idAssignedEvent;
    }
    
    public function userRegistration( $event )
    {
        $eventArgs = $event->getArgs();
        
        $data = $this->grapple->userRegistration( $eventArgs[ 'uid' ], $this->grapple_idAssignedEvent );
        $this->grapple_idAssignedEvent = $data->idAssignedEvent;
        $_SESSION[ 'grapple' ][ 'previousGEBId' ] = $this->grapple_idAssignedEvent;
    }
}

?>