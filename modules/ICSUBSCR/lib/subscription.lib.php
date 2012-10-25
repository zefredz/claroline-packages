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

class Subscription
{
    protected $session;
    protected $userId;
    protected $groupId;
    protected $recordList;
    
    public function __construct( $session , $userId , $groupId = null )
    {
        $this->session = $session;
        $this->userId = $userId;
        $this->groupId = $groupId;
        
        $this->load();
    }
    
    public function load()
    {
        $sql = "SELECT slotId FROM `{$this->tbl}`\nWHERE sessionId = "
        . Claroline::getDatabase()->escape( $this->session->getId() );
        
        if( $groupId )
        {
            $sql .= "\nAND groupId = " . Claroline::getDatabase()->escape( $this->groupId );
        }
        else
        {
            $sql .= "\nAND userId = " . Claroline::getDatabase()->escape( $this->userId );
        }
        
        $result = Claroline::getDatabase()->query( $sql );
        
        $slotList = array_keys( $this->session->getSlotList() );
        $this->recordList = array_fill_keys( $slotList , false );
        
        foreach( $result as $record )
        {
            $slotId = $record[ 'slotId' ];
            $this->recordList[ $slotId ] = true;
        }
    }
    
    public function getSubscrCount( $slotId )
    {
        $sql = "SELECT COUNT(*) FROM `{$this->tbl}`\nWHERE slotId = "
            . Claroline::getDatabase()->escape( $slotId );
        
        return Claroline::getDatabase()->query( $sql )->fetch( Database_ResultSet::FETCH_VALUE );
    }
    
    public function getAvailableSpace( $slotId )
    {
        return $this->session->getSlot( $slotId )->getAvailableSpace();
    }
    
    public function getRemainingSpace( $slotId )
    {
        return $this->getAvailableSpace( $slotId ) - $this->getSubscrCount( $slotId );
    }
    
    public function isSubscribed( $slotId )
    {
        return $this->recordList[ $slotId ];
    }
    
    public function isAvailable( $slotId )
    {
        return $this->getRemainingSpace( $slotId ) && ! $this->isSubscribed( $slotId );
    }
    
    public function subscribe( $slotId )
    {
        $sql = "INSERT INTO `{$this->tbl}` SET\nslotId = "
            . Claroline::getDatabase()->escape( $slotId )
            . ",\nuserId = "
            . Claroline::getDatabase()->escape( $this->userId )
            . ",\nsessionId = "
            . Claroline::getDatabase()->escape( $this->session->getId() );
        
        if( $this->groupId )
        {
            $sql .= ",\ngroupId = " . Claroline::getDatabase()->escape( $this->groupId );
        }
        
        if( ! $this->isSubscribed( $slotId ) && Claroline::getDatabase()->exec( $sql ) )
        {
            return $this->recordList[ $slotId ] = true;
        }
    }
    
    public function unsubscribe( $slotId )
    {
        $sql = "DELETE FROM `{$this->tbl}` \nWHERE slotId = "
            . Claroline::getDatabase()->escape( $slotId );
        
        if( $this->groupId )
        {
            $sql .= "\nAND groupId = " . Claroline::getDatabase()->escape( $this->groupId );
        }
        else
        {
            $sql .= "\nAND userId = " . Claroline::getDatabase()->escape( $this->userId );
        }
        
        if( $this->isSubscribed( $slotId ) && Claroline::getDatabase()->exec( $sql ) )
        {
            $this->recordList[ $slotId ] = false;
            
            return true;
        }
    }
    
    public function flush()
    {
        $sql = "DELETE FROM `{$this->tbl}` \nWHERE sessionId = "
            . Claroline::getDatabase()->escape( $this->session->getId() );
        
        if( $this->groupId )
        {
            $sql .= "\nAND groupId = " . Claroline::getDatabase()->escape( $this->groupId );
        }
        else
        {
            $sql .= "\nAND userId = " . Claroline::getDatabase()->escape( $this->userId );
        }
        
        if( Claroline::getDatabase()->exec( $sql ) )
        {
            $slotList = array_keys( $this->session->getSlotList() );
            $this->recordList = array_fill_keys( $slotList , false );
            
            return true;
        }
    }
}