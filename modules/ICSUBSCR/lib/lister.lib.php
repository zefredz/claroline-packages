<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.0.2 $Revision$ - Claroline 1.9
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICSUBSCR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class Lister
{
    protected $tbl;
    protected $cond = array();
    protected $allowedFields = array();
    protected $maxRank = 0;
    
    protected $itemList = array();
    
    /**
     * Constructor
     * @param string $tbl : the name of the database table
     * @param array $cond : the condition that must be added to loading quiery
     * @return void
     */
    public function __construct( $tbl , $cond = null , $allowedFields = null )
    {
        $this->tbl = $tbl;
        
        if( is_array( $cond ) )
        {
            $this->cond = $cond;
        }
        
        if( is_array( $allowedFields ) )
        {
            $this->allowedFields = $allowedFields;
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
    
    /**
     * Verifies id item list is not empty
     * @return boolean : true if not
     */
    public function notEmpty()
    {
        return ! empty( $this->itemList );
    }
    
    /**
     * Gets the value of an item's parameter
     * @param int $itemId : the id of the item
     * @param string $name : the name of the parameter
     * @return string : the value of the parameter
     */
    public function get( $itemId , $name )
    {
        $itemData = $this->getData( $itemId );
        
        if( $itemData && array_key_exists( $name , $itemData ) )
        {
            return $itemData[ $name ];
        }
    }
    
    /**
     * Gets all the data of an item
     * @param int $itemId : the id of the item
     * @return : array : the datas of the item
     */
    public function getData( $itemId )
    {
        if( array_key_exists( 'item_' . $itemId , $this->itemList ) )
        {
            return $this->itemList[ 'item_' . $itemId ];
        }
    }
    
    /**
     * Sets the value of an item's parameter
     * @param int $itemId : the id of the item
     * @param string $name : the name of the parameter
     * @param string : the value of the parameter
     * @return void
     */
    public function set( $itemId , $name , $value )
    {
        if( array_key_exists( 'item_' . $itemId , $this->itemList )
        &&  array_key_exists( $name , $this->itemList[ 'item_' . $itemId ] ) )
        {
            return $this->itemList[ 'item_' . $itemId ][ $name ] = $value;
        }
    }
    
    /**
     * Sets a bunch of parameter
     * @param int $itemId : the item's id
     * @param array $data : the parameter's data
     * @return void
     */
    public function modify( $itemId , $data )
    {
        foreach( $data as $name => $value )
        {
            $this->set( $itemId , $name , $value );
        }
    }
    
    /**
     * Loads item list from database
     * This method is called by the constructor
     * @return void
     */
    public function load()
    {
        $fieldList = ! empty( $this->allowedFields )
            ? 'id, rank, ' . implode( ',' , array_keys( $this->allowedFields ) )
            : "*";
        
        $sql = "SELECT {$fieldList} FROM `{$this->tbl}`";
        
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
        
        $sql .= "\nORDER BY rank ASC";
        
        $itemList = Claroline::getDatabase()->query( $sql );
        
        if( $itemList->numRows() )
        {
            $this->itemList = array();
            
            foreach( $itemList as $itemData )
            {
                $itemId = $itemData[ 'id' ];
                $this->itemList[ 'item_' . $itemId ] = $itemData;
            }
            
            $this->maxRank = $itemData[ 'rank' ];
        }
    }
    
    /**
     * Saves datas in database
     * @param int $itemId : the item's id - if null, saves all the list
     * @return boolean
     */
    public function save( $itemId = null )
    {
        $nbRows = 0;
        
        $itemList = $itemId && array_key_exists( 'item_' . $itemId , $this->itemList )
                ? array( $this->itemList[ 'item_' . $itemId ] )
                : $this->itemList;
        
        foreach( $itemList as $item )
        {
            $sql = "UPDATE `{$this->tbl}` SET ";
            
            $sqlData = array();
            
            foreach( $item as $name => $value )
            {
                if( $name != 'id' )
                {
                    $sqlData[] = $name . " = '" . $value . "'";
                }
            }
            
            $sql .= implode( ",\n" , $sqlData );
            
            $id = $itemId ? $itemId : $item['id'];
            $sql .= "\n WHERE id = " . $id;
            
            
            if( Claroline::getDatabase()->exec( $sql ) )
            {
                $nbRows++;
            }
        }
        
        $this->load();
        
        return $nbRows;
    }
    
    /**
     * Adds an item
     * @param array $data : the data of the item
     * @return int : the new item's id
     */
    public function add( $data )
    {
        if( ! empty( $this->allowedFields ) )
        {
            $data = array_merge( $this->allowedFields , $data ); // fills missing fields with default values
            $data = array_intersect_key( $data , $this->allowedFields ); // removes unwanted fields
        }
        
        $data[ 'rank' ] = ++$this->maxRank;
        
        $sql = "INSERT INTO `{$this->tbl}` SET\n";
        
        $sqlArray = array();
        
        foreach( $data as $name => $value )
        {
            $sqlArray[] = $name . " = '" . $value . "'";
        }
        
        $sql .= implode( "\n," , $sqlArray );
        
        if( Claroline::getDatabase()->exec( $sql ) )
        {
            $itemId = Claroline::getDatabase()->insertId();
            $this->itemList[ 'item_' . $itemId ] = $data;
            
            return $itemId;
        }
    }
    
    /**
     * Deletes an item
     * @param int $itemId : the id of the item
     * @return : boolean
     */
    public function delete( $itemId )
    {
        if( array_key_exists( 'item_' . $itemId , $this->itemList ) )
        {
            unset( $this->itemList[ 'item_' . $itemId ] );
            
            return Claroline::getDatabase()->exec( "
                DELETE FROM
                    `{$this->tbl}`
                WHERE
                    id = " . (int)$itemId );
        }
    }
    
    /**
     * Moves item up in the list (helper)
     * @param int $itemId : the id of the item
     * @return : boolean
     */
    public function up( $itemId )
    {
        return $this->move( $itemId , 1 );
    }
    
    /**
     * Moves item down in the list (helper)
     * @param int $itemId : the id of the item
     * @return : boolean
     */
    public function down( $itemId )
    {
        return $this->move( $itemId , -1 );
    }
    
    /**
     * Gets max rank
     * @return int
     */
    public function getMaxRank()
    {
        return $this->maxRank;
    }
    
    /**
     * Moves an item (private function)
     * @param int $itemId : the id of the item
     * @param int $direction : 1 for up, -1 for down
     * @return : boolean
     */
    private function move( $itemId , $direction = 1 )
    {
        if( abs( $direction ) != 1 )
        {
            throw new Exception( 'Invalid value for direction: must be +1 for up, -1 for down' );
        }
        
        if( array_key_exists( 'item_' . $itemId , $this->itemList ) )
        {
            $oldRank = $this->itemList[ 'item_' . $itemId ][ 'rank' ];
            $newRank = $oldRank - $direction;
            
            $this->itemList[ 'item_' . $itemId ][ 'rank' ] = $newRank;
            
            foreach( $this->itemList as $item )
            {
                if( $item[ 'id' ] != $itemId && $item[ 'rank' ] == $newRank )
                {
                    $this->itemList[ 'item_' . $item[ 'id' ] ][ 'rank' ] = $oldRank;
                }
            }
        }
        
        return $this->save();
    }
}