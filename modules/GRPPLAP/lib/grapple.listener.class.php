<?php

class grappleListener extends EventDriven
{
    public $grapple;
    public $grapple_idAssignedEvent;
    
    public function __construct()
    {
        $this->grapple = new grapple();
        $this->grapple_idAssignedEvent = ( isset( $_SESSION[ 'grapple' ][ 'previousGEBId' ] ) ? (int) $_SESSION[ 'grapple' ][ 'previousGEBId' ] : 0 );
    }
    
    public function userLogin()
    {
        if( isset( $_SESSION[ 'grapple' ][ 'logged' ] ) && $_SESSION[ 'grapple' ][ 'logged' ] === true )
        {
            return true;
        }
        $data = $this->grapple->userLogin( claro_get_current_user_id(), $this->grapple_idAssignedEvent );
        $this->grapple_idAssignedEvent = $data->idAssignedEvent;
        $_SESSION[ 'grapple' ][ 'previousGEBId' ] = $this->grapple_idAssignedEvent;
        $_SESSION[ 'grapple' ][ 'logged' ] = true;
        
        return true;
    }
    
    public function studentEnrollment( $event )
    {
        $eventArgs = $event->getArgs();
        $userData = claro_get_course_user_properties( $eventArgs['cid'], $eventArgs['uid']);
        
        if( $userData['privilege']['is_courseAdmin'] )
        {
            $userType = 'TEACHER';
        }
        elseif( $userData['privilege']['is_courseTutor'] )
        {
            $userType = 'TUTOR';
        }
        else
        {
            $userType = 'LEARNER';
        }
        
        $data = $this->grapple->studentEnrollment( $eventArgs[ 'uid' ], $eventArgs['cid'], $userType, $this->grapple_idAssignedEvent );
        $this->grapple_idAssignedEvent = $data->idAssignedEvent;
        $_SESSION[ 'grapple' ][ 'previousGEBId' ] = $this->grapple_idAssignedEvent;
    }
    
    public function roleChange( $event )
    {
        $eventArgs = $event->getArgs();
        $userData = claro_get_course_user_properties( $eventArgs['cid'], $eventArgs['uid']);
        
        if( $userData['privilege']['is_courseAdmin'] )
        {
            $userType = 'TEACHER';
        }
        elseif( $userData['privilege']['is_courseTutor'] )
        {
            $userType = 'TUTOR';
        }
        else
        {
            $userType = 'LEARNER';
        }
        
        $data = $this->grapple->userRoleChange( $eventArgs[ 'uid' ], $eventArgs['cid'], $userType, $this->grapple_idAssignedEvent );
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