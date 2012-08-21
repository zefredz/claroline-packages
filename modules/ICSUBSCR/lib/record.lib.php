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
    protected $session;
    protected $selectedSlotList = array();
    
    public function __construct( $session , $userId = null , $groupId = null )
    {
        if ( ! $userId && ! $groupId )
        {
            throw new Exception( 'User id and goup id cannot be null together' );
        }
        
        if( (int)$userId && (int)$groupId )
        {
            throw new Exception( 'User id and goup id cannot be defined together' );
        }
        
        $this->session = $session;
        $this->userId = $userId;
        $this->groupId = $groupId;
        
        $this->tbl = get_module_course_tbl( array( 'icsubscr_record' ) );
    }
    
    public function choose( $slotId )
    {
        if( ! in_array( $slotId , $this->selectedSlotList )
           && array_key_exists( $slotId , $this->session->slotList )
           && count( $this->selectedSlotList )
                < $this->session->slotList[ $slotId ][ Slot::AVAILABLE_SPACE ] )
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
}