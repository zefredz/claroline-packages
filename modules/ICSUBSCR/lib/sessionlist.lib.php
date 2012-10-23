<?php

class SessionList
{
    const CONTEXT_USER = 'user';
    const CONTEXT_GROUP = 'group';
    
    protected $tbl;
    protected $context;
    protected $allowedToEdit;
    protected $sessionList;
    
    public function __construct( $context = self::USER )
    {
        $tbl = get_module_course_tbl( array( 'icsubscr_session' ) );
        $this->tbl = $tbl[ 'icsubscr_session' ];
        
        $this->context = $context;
        $this->allowedToEdit = $allowedToEdit;
        
        $this->load();
    }
    
    public function load()
    {
        $sessionList = Claroline::getDatabase()->query( "
            SELECT id, rank
            FROM `{$this->tbl}`
            WHERE context = " . Claroline::getDatabase()->quote( $this->context ) . "
            SORT BY rank ASC" );
        
        foreach( $sessionList as $session )
        {
            $sessionId = $session['id'];
            
            $this->sessionList[ $sessionId ] = new Session( $sessionId );
        }
    }
    
    public function get( $sessionId )
    {
        if( array_key_exists( $sessionId , $this->sessionList ) )
        {
            return $this->sessionList[ $sessionId ];
        }
    }
}