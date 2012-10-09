<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.4 $Revision$ - Claroline 1.9
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICSUBSCR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class Lister
{
    const PARAM_RANK = 'rank';
    const UP = -1;
    const DOWN = 1;
    
    protected $ListedObject;
    protected $cond = array();
    protected $itemList = array();
    protected $maxRank;
    
    /**
     * Constructor
     * @param string $tbl : the name of the database table
     * @param array $cond : the condition that must be added to loading quiery
     * @return void
     */
    public function __construct( $listedObject , $cond = null )
    {
        $this->listedObject = $listedObject;
        
        if( is_array( $cond ) )
        {
            $this->cond = $cond;
        }
        
        $this->load();
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
    
    /**
     * Loads item list from database
     * This method is called by the constructor
     * @return void
     */
    public function load()
    {
        $sql = "SELECT id FROM `{$this->listedObject->getTbl()}`";
        
        if( ! empty( $this->cond ) )
        {
            $sql .= "\nWHERE ";
            
            $sqlCond = array();
            
            foreach( $this->cond as $name => $value )
            {
                $sqlCond[] = $name . " = '" . $value . "'";
            }
            
            $sql .= implode( "\nAND " , $sqlCond );
        }
        
        $sql .= "\nORDER BY " . self::PARAM_RANK . " ASC";
        
        $itemList = Claroline::getDatabase()->query( $sql );
        
        if( $itemList->numRows() )
        {
            $this->itemList = array();
            
            foreach( $itemList as $itemData )
            {
                $itemId = $itemData[ 'id' ];
                $this->itemList[ $itemId ] = $this->listedObject->getInstance( $itemId );
            }
        }
    }
    
    public function delete( $itemId )
    {
        $itemRank = $this->getRank( $itemId );
        
        if( $this->itemList[$itemId]->delete() )
        {
            return Claroline::getDatabase()->exec( "
                UPDATE
                    `{$this->listedObject->getTbl()}`
                SET
                    rank = rank - 1
                WHERE
                    rank >" . Claroline::getDatabase()->escape( $itemRank ) );
        }
    }
    
    public function getRank( $itemId )
    {
        return Claroline::getDatabase()->query( "
            SELECT rank FROM
                `{$this->listedObject->getTbl()}`
            WHERE
                id = " . Claroline::getDatabase()->escape( $itemId )
        )->fetch( Database_ResultSet::FETCH_VALUE );
    }
    
    public function getMaxRank( $force = false )
    {
        if( is_null( $this->maxRank ) || $force )
        {
            $this->maxRank = Claroline::getDatabase()->query( "
                SELECT MAX( rank ) FROM `{$this->listedObject->getTbl()}`"
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
                    SELECT id FROM `{$this->listedObject->getTbl()}`
                    WHERE rank = " . Claroline::getDatabase()->escape( $newRank )
                )->fetch( Database_ResultSet::FETCH_VALUE );
                
                return $this->setRank( $itemId , $newRank )
                    && $this->setRank( $swapId , $oldRank );
            }
        }
    }
    
    public function create( $data )
    {
        $this->listedObject->set( $data );
        $this->listedObject->set( self::PARAM_RANK , $this->getMaxRank() );
        $itemId = $this->listedObject->save();
        
        if( $itemId )
        {
            $this->setRank( $itemId , $this->getMaxRank() + 1 );
        }
    }
    
    private function setRank( $itemId , $rank )
    {
        return Claroline::getDatabase()->exec( "
            UPDATE
                `{$this->listedObject->getTbl()}`
            SET
                rank = " . Claroline::getDatabase()->escape( $rank ) . "
            WHERE
                id = " . Claroline::getDatabase()->escape( $itemId ) );
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