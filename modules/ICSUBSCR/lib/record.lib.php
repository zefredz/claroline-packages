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

class Record
{
    protected $userId;
    protected $groupId;
    protected $slotList;
    protected $subscriptionList = array();
    
    public function __construct( $slotList , $userId , $groupId = null )
    {
        $this->slotList = $slotList;
        $this->userId = $userId;
        $this->groupId = $groupId;
    }
    
    public function subscribe( $slotId )
    {
        if( array_key_exists( $slotId , $this->slotList ) && $this->isAvailable( $slotId ) )
        {
            return Claroline::getDatabase()->exec( "
                INSERT INTO `{$this->tbl}` SET
                userId = " . Claroline::getDatabase()->escape( $this->userId ) . ",
                groupId = " . Claroline::getDatabase()->escape( $this->groupId ) . ",
                slotId = " . Claroline::getDatabase()->escape( $slotId ) );
        }
    }
    
    public function getSubscrCount( $slotId )
    {
        return Claroline::getDatabase()->query( "
            SELECT
                count( slotId )
            FROM
                `{$this->tbl}`" )->fetch( Database_ResultSet::FETCH_VALUE );
    }
    
    public function getAvailableSpace( $slotId )
    {
        return $this->slotList[ $slotId ]->getAvailableSpace();
    }
    
    public function getRemainingSpace( $slotId )
    {
        return $this->getAvailableSpace( $slotId ) - $this->getSubscrCount( $slotId );
    }
    
    public function isSubscribed( $slotId )
    {
        $subscriber = $this->groupId ? 'groupId' : 'userId';
        
        return Claroline::getDatabase()->query( "
            SELECT " . $subscriber . "
            FROM `{$this->tbl}`
            WHERE slotId = " . Claroline::getDatabase()->escape( $slotId ) )->numRows();
    }
    
    public function isAvailable( $slotId )
    {
        return $this->getRemainingSpace( $slotId ) && ! $this->isSubscribed( $slotId );
    }
}