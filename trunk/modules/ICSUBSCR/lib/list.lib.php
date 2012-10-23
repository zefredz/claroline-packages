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
    const ITEM_TYPE_RECORD = 'record';
    const UP = -1;
    const DOWN = 1;
    
    protected $itemType;
    protected $parentId;
    protected $itemClassName;
    protected $itemList;
    protected $tbl;
    
    public function __construct( $itemType = self::ITEM_TYPE_SESSION , $parentId = null )
    {
        if( $itemType != self::ITEM_TYPE_SESSION
            && $itemType != self::ITEM_TYPE_SLOT
            && $itemType != self::ITEM_TYPE_RECORD )
        {
            throw new Exception( 'Invalid item type' );
        }
        
        if( $itemType == self::ITEM_TYPE_SESSION )
        {
            $parentId = 0;
        }
        elseif( is_null( $parentId ) || (int)$parentId == 0 )
        {
            throw new Exception( 'Missing parent id' );
        }
        
        $this->itemType = $itemType;
        $this->parentId = $parentId;
        $this->itemClassName = ucwords( $itemType );
        
        $tbl = get_module_course_tbl( array( 'icsubscr_list' ) );
        $this->tbl = $tbl[ 'icsubscr_session' ];
        
        $this->load();
    }
    
    /**
     * Loads item list from database
     * This method is called by the constructor
     * @return void
     */
    public function load()
    {
        $itemList = Claroline::getDatabase()->query( "
            SELECT
                itemId
            FROM
                `{$this->tbl}`
            WHERE
                itemType = " . Claroline::getDatabase()->quote( $this->itemType ) . "
            AND
                parentId = " . Claroline::getDatabase()->escape( $this->parentId ) . "
            ORDER BY
                rank ASC" );
        
        if( $itemList->numRows() )
        {
            $this->itemList = array();
            
            foreach( $itemList as $itemData )
            {
                $itemId = $itemData[ 'itemId' ];
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
                itemId = " . Claroline::getDatabase()->escape( $itemId ) . "
            AND
                itemType = " . Claroline::getDatabase()->escape( $this->itemType )
        )->fetch( Database_ResultSet::FETCH_VALUE );
    }
    
    public function getMaxRank( $force = false )
    {
        if( is_null( $this->maxRank ) || $force )
        {
            $this->maxRank = Claroline::getDatabase()->query( "
                SELECT
                    MAX( rank )
                FROM
                    `{$this->tbl}`
                WHERE
                    itemType = " . Claroline::getDatabase()->quote( $this->itemType ) . "
                AND
                    parentId = " . Claroline::getDatabase()->escape( $this->parentId )
            )->fetch( Database_ResultSet::FETCH_VALUE );
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
                $swapId = Claroline::getDatabase()->query( "
                    SELECT
                        itemId
                    FROM
                        `{$this->tbl}`
                    WHERE
                        itemType = " . Claroline::getDatabase()->quote( $this->itemType ) . "
                    AND
                        parentId = " . Claroline::getDatabase()->escape( $this->parentId ) . "
                    AND
                        rank = " . Claroline::getDatabase()->escape( $newRank )
                )->fetch( Database_ResultSet::FETCH_VALUE );
                
                return $this->setRank( $itemId , $newRank )
                    && $this->setRank( $swapId , $oldRank );
            }
        }
    }
    
    public function add( $item )
    {
        if( is_a( $item , $this->itemClassName ) && $item->getId() )
        {
            return Claroline::getDatabase()->exec( "
                INSERT INTO `{$this->tbl}` SET
                itemId = " . Claroline::getDatabase()->escape( $item->getId() ) . "
                parentId = " . Claroline::getDatabase()->escape( $this->parentId ) . "
                itemType = " . Claroline::getDatabase()->quote( $this->itemType ) . "
                rank = " . Claroline::getDatabase()->escape( $this->getMaxRank() +1 ) );
        }
        else
        {
            throw new Exception( 'Invalid object' );
        }
    }
    
    public function remove( $item )
    {
        if( is_a( $item , $this->itemClassName ) && $item->getId() )
        {
            return Claroline::getDatabase()->exec( "
                DELETE FROM `{$this->tbl}`
                WHERE
                    itemId = " . Claroline::getDatabase()->escape( $item->getId() ) . "
                AND
                    itemType = " . Claroline::getDatabase()->quote( $this->itemType ) );
        }
        else
        {
            throw new Exception( 'Invalid object' );
        }
    }
    
    private function setRank( $itemId , $rank )
    {
        return Claroline::getDatabase()->exec( "
            UPDATE
                `{$this->tbl}`
            SET
                rank = " . Claroline::getDatabase()->escape( $rank ) . "
            WHERE
                itemType = " . Claroline::getDatabase()->quote( $this->itemType ) . "
            AND
                itemId = " . Claroline::getDatabase()->escape( $itemId ) );
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