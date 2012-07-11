<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.0.1 $Revision$ - Claroline 1.9
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICSUBSCR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class SessionList
{
    const PARAM_CONTEXT = 'context';
    const PARAM_START_DATE = 'startDate';
    const PARAM_END_DATE = 'endDate';
    const PARAM_STATUS = 'status';
    const PARAM_RANK = 'rank';
    const PARAM_VISIBILITY = 'visibility';
    const ENUM_CONTEXT_USER = 'user';
    const ENUM_CONTEXT_GROUP = 'group';
    const ENUM_STATUS_OPEN = 'open';
    const ENUM_STATUS_CLOSED = 'closed';
    const ENUM_VISIBILITY_VISIBLE = 'visible';
    const ENUM_VISIBILITY_INVISIBLE = 'invisible';
    
    protected $context;
    protected $sessionList;
    
    /**
     * Constructor
     * @param string $context : the actual context
     */
    public function __construct( $context = self::ENUM_CONTEXT_USER )
    {
        $this->context = $context;
        
        $this->tbl = get_module_course_tbl( array( 'icsubscr_session' ) );
        $this->load();
    }
    
    public function load()
    {
        $sessionList = Claroline::getDatabase()->query( "
            SELECT
                id,
                title,
                description,
                type,
                startDate,
                status,
                rank,
                visibility
            FROM
                `{$this->tbl['icsubscr_session']}`
            WHERE
                context = " . Claroline::getDatabase()->quote( $this->context ) . "
            ORDER BY rank"
        );
        
        $this->sessionList = array();
        
        foreach( $sessionList as $sessionData )
        {
            $sessionId = $sessionData[ 'id' ];
            $this->sessionList[ $sessionId ] = $sessionData;
        }
    }
    
    public function getSessionList( $force = false )
    {
        if( $force )
        {
            $this->load();
        }
        
        return $this->sessionList;
    }
    
    public function get( $sessionId , $param )
    {
        if( array_key_exists( $sessionId , $this->sessionList )
           && isset( $this->sessionList[ $sessionId ][ $param ] ) )
        {
            return $this->sessionList[ $sessionId ][ $param ];
        }
    }
    
    public function getStartDate( $sessionId )
    {
        return $this->get( $sessionId , self::PARAM_START_DATE );
    }
    
    public function getEndDate( $sessionId )
    {
        return $this->get( $sessionId , self::PARAM_END_DATE );
    }
    
    public function isOpen( $sessionId )
    {
        return $this->get( $sessionId , self::PARAM_STATUS ) == self::ENUM_STATUS_OPEN;
    }
    
    public function isVisible( $sessionId )
    {
        return $this->get( $sessionId , self::PARAM_VISIBILITY ) == self::ENUM_VISIBILITY_VISIBLE;
    }
    
    public function isAvailable( $sessionId )
    {
        $now = new date( 'Y-m-d H:i:s' );
        
        return $this->isOpen( $sessionId )
            && $this->isVisible( $sessionId )
            && ( ! $this->getStartDate() || $this->getStartDate( $sessionId ) < $now )
            && ( ! $this->getEndDate() || $this->getEndDate( $sessionId ) > $now );
    }
    
    public function set( $sessionId , $param , $value )
    {
        if( array_key_exists( $sessionId , $this->sessionList ) )
        {
            return $this->sessionList[ $sessionId ][ $param ] = $value;
        }
    }
    
    public function setVisible( $sessionId )
    {
        return $this->set( $sessionId
                        , self::PARAM_VISIBILITY
                        , self::ENUM_VISIBILITY_VISIBLE );
    }
    
    public function setInvisible( $sessionId )
    {
        return $this->set( $sessionId
                        , self::PARAM_VISIBILITY
                        , self::ENUM_VISIBILITY_INVISIBLE );
    }
    
    public function setOpen( $sessionId )
    {
        return $this->set( $sessionId
                        , self::PARAM_STATUS
                        , self::ENUM_STATUS_OPEN );
    }
    
    public function setClosed( $sessionId )
    {
        return $this->set( $sessionId
                        , self::PARAM_STATUS
                        , self::ENUM_STATUS_CLOSED );
    }
    
    public function setStartDate( $sessionId , $date )
    {
        return $this->set( $sessionId
                        , self::PARAM_START_DATE
                        , $date );
    }
    
    public function setEndDate( $sessionId , $date )
    {
        return $this->set( $sessionId
                        , self::PARAM_END_DATE
                        , $date );
    }
    
    public function unsetDate( $sessionId )
    {
        return $this->set( $sessionId
                            , self::PARAM_START_DATE
                            , null )
            &&  $this->set( $sessionId
                            , self::PARAM_END_DATE
                            , null );
    }
    
    public function save( $sessionId )
    {
        if( ! array_key_exists( $sessionId , $this->sessionList ) )
        {
            throw new Exception( 'Invalid session id' );
        }
        
        $sessionData = array();
        
        foreach( $this->sessionList[ $sessionId ] as $data => $value )
        {
            $sessionData[] = $data . " = " . claroline::getDatabase()->quote( $value );
        }
        
        $sqlString = implode( "AND\n" , $sessionData );
        
        return Claroline::getDatabase()->exec( "
            UPDATE
                `{$this->tbl['icsubscr_session']}`
            SET
                " . $sessionData . "
            WHERE
                sessionId = " . Claroline::getDatabase()->escape( $sessionId ) );
    }
    
    public function create( $title , $description = null , $type = null , $startDate = null , $endDate = null )
    {
        $sql = "INSERT INTO
            `{$this->tbl['icsubscr_session']}`
        SET
            title = " . Claroline::getDatabase()->quote( $title ) . "
            context = " . Claroline::getDatabase()->quote( $this->context ) . "
            rank = " . Claroline::getDatabase()->escape( count( $this->sessionRank ) + 1 );
        
        if( $description )
        {
            $sql .= ",\ndescription = " . Claroline::getDatabase()->quote( $description );
        }
        
        if( $type )
        {
            $sql .= ",\ntype = " . Claroline::getDatabase()->quote( $type );
        }
        
        if( $startDate)
        {
            $sql .= ",\nstartDate = " . Claroline::getDatabase()->quote( $startDate );
        }
        
        if( $description )
        {
            $sql .= ",\nenddate = " . Claroline::getDatabase()->quote( $description );
        }
        
        Claroline::getDatabase()->exec( $sql );
        
        return Claroline::getDatabase()->insertId();
    }
    
    public function delete( $sessionId )
    {
        return Claroline::getDatabase()->exec( "
            DELETE FROM
                `{$this->tbl['icsubscr_session']}`
            WHERE
                id = " . Claroline::getDatabase()->escape( $sessionId ) );
    }
    
    public function getSession( $sessionId )
    {
        return new Session( $sessionId );
    }
}