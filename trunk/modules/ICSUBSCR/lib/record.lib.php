<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.1 $Revision$ - Claroline 1.9
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICSUBSCR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class Record
{
    const CONTEXT_USER = 'user_id';
    const CONTEXT_GROUP = 'group_id';
    
    public $session;
    
    protected $result = array();
    protected $selectedSlotList = array();
    protected $context;
    protected $subscriberId = null;
    
    public function __construct( $session , $userId = null , $groupId = null )
    {
        if( $groupId )
        {
            $this->context = self::CONTEXT_GROUP;
            $this->subscriberId = $groupId;
        }
        elseif( $userId )
        {
            $this->context = self::CONTEXT_USER;
            $this->subscriberId = $userId;
        }
        
        $this->session = $session;
        
        $this->tbl = get_module_course_tbl( array( 'icsubscr_record' ) );
    }
    
    public function load()
    {
        $result = Claroline::getDatabase()->query( "
                SELECT
                    userId,
                    groupId,
                    slotId,
                    rank
                FROM
                    `{$this->tbl['icsubscr_record']}`
                WHERE
                    sessionId = " . Claroline::getDatabase()->escape( $this->session->getId() ) );
        
        foreach( $result as $record )
        {
            $subscriberId = $this->context == self::CONTEXT_GROUP ? $record[ 'groupId' ] : $record[ 'userId' ];
            $this->result[ $record[ 'slotId' ] ][ $subscriberId ] = $record[ 'rank' ];
        }
    }
    
    public function getResult()
    {
        return $this->result;
    }
    
    public function choose( $slotId )
    {
        if( ! in_array( $slotId , $this->selectedSlotList )
           && array_key_exists( $slotId , $this->session->slotList )
           && $this->spaceAvailable( $slotId ) )
        {
            $this->selectedSlotList[] = $slotId;
        }
    }
    
    public function unchoose( $slotId )
    {
        unset( $this->selectedSlotList[ $slotId ] );
    }
    
    public function wipe()
    {
        if( Claroline::getDatabase()->exec( "
            DELETE FROM
                `{$this->tbl['icsubscr_record']}`
            WHERE
                sessionId = " . Claroline::getDatabase()->escape( $this->session->getId() ) . "
            AND
                userId = " . Claroline::getDatabase()->escape( $this->userId ) . "
            AND
                groupId = " . Claroline::getDatabase()->escape( $this->groupId ) ) )
        {
            return $this->selectedSlotList = array();
        }
    }
    
    public function save()
    {
        $sql1 = "INSERT INTO
                `{$this->tbl['icsubscr_record']}` (userId, groupId, sessionId, slotId, rank))
            VALUES";
        
        foreach( $this->selectedSlotList as $rank => $slotId )
        {
            $sqlArray[] = "\n("
                . Claroline::getDatabase()->escape( $this->userId ) . ", "
                . Claroline::getDatabase()->escape( $this->groupId ) . ", "
                . Claroline::getDatabase()->escape( $this->session->getId() ) .", "
                . Claroline::getDatabase()->escape( $slotId ) . ", "
                . Claroline::getDatabase()->escape( $rank )
                . ")";
        }
        
        return Claroline::getDatabase()->exec( $sql . implode( ',' , $sqlArray ) );
    }
    
    public function subscribe( $slotList )
    {
        foreach( $slotList as $slotId )
        {
            $this->choose( $slotId );
        }
        
        return $this->save();
    }
    
    public function spaceAvailable( $slotId )
    {
        return count( $this->result[ $slotId ] ) < $this->session->getOption( Session::OPTION_AVAILABLE_SPACE );
    }
}