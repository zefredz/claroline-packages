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

class ICSUBSCR_List
{
    const ITEM_TYPE_SESSION = 'session';
    const ITEM_TYPE_SLOT = 'slot';
    
    const UP = -1;
    const DOWN = 1;
    
    protected $itemType;
    protected $parentId;
    protected $itemClassName;
    protected $itemList;
    protected $maxRank;
    protected $tbl;
    
    public function __construct( $item , $parentId = null )
    {
        $itemType = strtolower( get_class( $item ) );
        
        if( $itemType != self::ITEM_TYPE_SESSION
            && $itemType != self::ITEM_TYPE_SLOT )
        {
            throw new Exception( 'Invalid item type' );
        }
        
        if( $itemType == self::ITEM_TYPE_SLOT && ! $parentId )
        {
            throw new Exception( 'Missing parent id' );
        }
        
        $this->itemType = $itemType;
        $this->parentId = $parentId;
        $this->itemClassName = ucwords( $itemType );
        
        $tbl = get_module_course_tbl( array( 'icsubscr_list' ) );
        $this->tbl = $tbl[ 'icsubscr_list' ];
        
        $this->load();
    }
    
    /**
     * Loads item list from database
     * This method is called by the constructor
     * @return void
     */
    public function load()
    {
        $sql = "SELECT itemId FROM `{$this->tbl}`"
            . "\nWHERE itemType = "
            . Claroline::getDatabase()->quote( $this->itemType );
        
        if( $this->parentId )
        {
            $sql .= "\nAND sessionId = "
                . Claroline::getDatabase()->escape( $this->parentId );
        }
        
        $sql .= "\nORDER BY rank ASC";
        
        $itemList = Claroline::getDatabase()->query( $sql );
        $this->itemList = array();
        
        if( $itemList->numRows() )
        {
            foreach( $itemList as $itemData )
            {
                $itemId = $itemData[ 'id' ];
                $this->itemList[ $itemId ] = new $this->itemClassName( $itemId );
            }
        }
    }
    
    /**
     * Getter for item list
     * @param boolean $force : to force reload
     * @return array : the item list
     */
    public function getItemList( $force = false )
    {
        if( $force )
        {
            $this->load();
        }
        
        return $this->itemList;
    }
    
    public function getItem( $itemId )
    {
        if( array_key_exists( $itemId , $this->itemList ) )
        {
            return $this->itemList[ $itemId ];
        }
    }
    
    /**
     * Verifies id item list is not empty
     * @return boolean : true if not
     */
    public function notEmpty()
    {
        return ! empty( $this->itemList );
    }
    
    public function getRank( $itemId )
    {
        return Claroline::getDatabase()->query( "
            SELECT
                rank
            FROM
                `{$this->tbl}`
            WHERE
                id = " . Claroline::getDatabase()->escape( $itemId )
        )->fetch( Database_ResultSet::FETCH_VALUE );
    }
    
    public function getMaxRank( $force = false )
    {
        if( is_null( $this->maxRank ) || $force )
        {
            $sql = "SELECT MAX( rank ) FROM `{$this->tbl}`"
                . "\nWHERE itemType = "
                . Claroline::getDatabase()->quote( $this->itemType );
            
            if( $this->parentId )
            {
                $sql .= "\nAND sessionId = "
                    . Claroline::getDatabase()->escape( $this->parentId );
            }
            
            $this->maxRank = Claroline::getDatabase()->query( $sql )->fetch( Database_ResultSet::FETCH_VALUE );
        }
        
        if( $this->maxRank != count( $this->itemList ) )
        {
            $this->repairRank();
        }
        
        return count( $this->itemList );
    }
    
    public function up( $itemId )
    {
        return $this->move( $itemId , self::UP );
    }
    
    public function down( $itemId )
    {
        return $this->move( $itemId , self::DOWN );
    }
    
    /**
     * Moves an item (private function)
     * @param int $itemId : the id of the item
     * @param int $direction : 1 for up, -1 for down
     * @return : boolean
     */
    private function move( $itemId , $direction )
    {
        if( $direction != self::UP && $direction != self::DOWN )
        {
            throw new Exception( 'Invalid value for direction' );
        }
        
        if( array_key_exists( $itemId , $this->itemList ) )
        {
            $oldRank = $this->getRank( $itemId );
            $newRank = $oldRank + $direction;
            
            if( $newRank > 0 && $newRank <= $this->getMaxRank() )
            {
                $sql = "SELECT id FROM `{$this->tbl}`"
                    . "\nWHERE rank = " . Claroline::getDatabase()->escape( $newRank )
                    . "\nAND itemType = " . Claroline::getDatabase()->quote( $this->itemType )
                    . "\nAND parentId = " . Claroline::getDatabase()->escape( $this->parentId );
                
                $swapId = Claroline::getDatabase()->query( $sql )->fetch( Database_ResultSet::FETCH_VALUE );
                
                return $this->setRank( $itemId , $newRank )
                    && $this->setRank( $swapId , $oldRank );
            }
        }
    }
    
    public function add( $item )
    {
        if( ! is_a( $item , $this->itemClassName ) )
        {
            throw new Exception( 'Invalid object' );
        }
        
        if( $item->save() )
        {
            $sql = "UPDATE `{$this->tbl}`\nSET rank = "
                . Claroline::getDatabase()->escape( $this->getMaxRank() +1 )
                . "\nWHERE id = " . Claroline::getDatabase()->escape( $item->getId() );
                
            return Claroline::getDatabase()->exec( $sql );
        }
    }
    
    private function setRank( $itemId , $rank )
    {
        $sql = "UPDATE `{$this->tbl}`"
                . "\nSET rank = " . Claroline::getDatabase()->escape( $rank )
                . "\nWHERE itemType = " . Claroline::getDatabase()->quote( $this->itemType )
                . "\nAND parentId = " . Claroline::getDatabase()->escape( $this->parentId )
                . "\nAND id = " . Claroline::getDatabase()->escape( $itemId );
                
        return Claroline::getDatabase()->exec( $sql );
    }
    
    private function repairRank()
    {
        $rank = 1;
        
        foreach( array_keys( $this->itemList ) as $itemId )
        {
            $this->setRank( $itemId , $rank++ );
        }
    }
}