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

class Record
{
    protected $session;
    protected $selectedSlotList = array();
    
    public function __construct( $session )
    {
        $this->session = $session;
        
        $this->tbl = get_module_course_tbl( array( 'ICSUBSCR_record' ) );
    }
    
    public function choose( $slotId )
    {
        if( ! in_array( $slotId , $this->selectedSlotList )
           && array_key_exists( $slotId , $this->session->slotList )
           && count( $this->selectedSlotList ) < $this->session->slotList[ $slotId ][ Slot::AVAILABLE_SPACE ] )
        {
            $this->selectedSlotList[] = $slotId;
        }
    }
    
    public function save()
    {
        $sql1 = "INSERT INTO
                `{$this->tbl['ICSUBSCR_record']}` (userId, groupId, sessionId, slotId, rank))
            VALUES";
        
        foreach( $this->selectedSlotList as $rank => $slotId )
        {
            
            $sqlArray[] = "\n("
                . Claroline::getDatabase()->escape( claro_get_current_user_id() ) . ", "
                . Claroline::getDatabase()->escape( claro_get_current_group_id() ) . ", "
                . Claroline::getDatabase()->escape( $this->session->getId() ) .", "
                . Claroline::getDatabase()->escape( $slotId ) . ", "
                . Claroline::getDatabase()->escape( $rank )
                . ")";
        }
        
        return Claroline::getDatabase()->exec( $sql . implode( ',' , $sqlArray ) );
    }
}