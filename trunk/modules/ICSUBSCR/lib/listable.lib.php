<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.4 $Revision$ - Claroline 1.11
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICSUBSCR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class Listable
{
    const NOT_NULL = '_not_null_';
    const PARAM_VISIBILITY = 'visibility';
    const ENUM_VISIBILITY_VISIBLE = 'visible';
    const ENUM_VISIBILITY_INVISIBLE = 'invisible';
    
    protected $id;
    protected $tbl;
    protected $propertyList;
    protected $data = array();
    
    public function __construct( $id = null )
    {
        if( $id )
        {
            $this->load( $id );
        }
    }
    
    public function load( $id = null )
    {
        if( ! $this->id && ! (int)$id )
            return false;
        
        $fieldList = ! empty( $this->propertyList )
            ? self::PARAM_VISIBILITY . ", " . implode( ',' , array_keys( $this->propertyList ) )
            : "*";
        
        $sql = "SELECT {$fieldList} FROM `{$this->tbl}` WHERE id = " . $id;
        
        if( ! empty( $this->cond ) )
        {
            $sql .= "\nAND ";
            
            $sqlCond = array();
            
            foreach( $this->cond as $name => $value )
            {
                $sqlCond[] = $name . " = '" . $value . "'";
            }
            
            $sql .= implode( "\nAND " , $sqlCond );
        }
        
        $this->data = Claroline::getDatabase()->query( $sql )->fetch( Database_ResultSet::FETCH_ASSOC );
        
        if( ! empty( $this->data ) )
        {
            $this->id = $id;
            return $this->id;
        }
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Adds an item
     * @param array $data : the data of the item
     * @return int : the new item's id
     */
    public function save( $data = null )
    {
        if( ! empty( $data ) )
        {
            $this->data = $this->validate( $data , $this->propertyList );
        }
        
        $sqlArray = array();
        
        foreach( $this->data as $name => $value )
        {
            if( $value == self::NOT_NULL )
            {
                throw new Exception( 'Missing value ' );
            }
            
            $sqlArray[] = $name . " = '" . $value . "'";
        }
        
        $sqlValues = implode( "\n," , $sqlArray );
        
        if( $this->id )
        {
            $sql = "UPDATE `{$this->tbl}` SET\n"
                . $sqlValues
                . "\nWHERE id = "
                . Claroline::getDatabase()->escape( $this->id );
        }
        else
        {
            $sql = "INSERT INTO `{$this->tbl}` SET\n" . $sqlValues;
        }
        
        if( Claroline::getDatabase()->exec( $sql ) )
        {
            if( ! $this->id )
            {
                $this->id = Claroline::getDatabase()->insertId();
            }
            
            $this->data = $data;
            
            return $this->id;
        }
    }
    
    public function getTbl()
    {
        return $this->tbl;
    }
    
    public function isVisible()
    {
        return $this->get( self::PARAM_VISIBILITY ) == self::ENUM_VISIBILITY_VISIBLE;
    }
    
    public function setVisibility( $visibility = self::ENUM_VISIBILITY_VISIBLE )
    {
        if( $visibility == self::ENUM_VISIBILITY_VISIBLE
            || $visibility == self::ENUM_VISIBILITY_INVISIBLE )
        {
            return Claroline::getDatabase()->exec( "
                UPDATE
                    `{$this->tbl}`
                SET
                    " . self::PARAM_VISIBILITY . " = " . Claroline::getDatabase()->quote( $visibility ) . "
                WHERE
                    id = " . Claroline::getDatabase()->escape( $this->id ) );
        }
    }
    
    public function show()
    {
        return $this->setVisibility( self::ENUM_VISIBILITY_VISIBLE );
    }
    
    public function hide()
    {
        return $this->setVisibility( self::ENUM_VISIBILITY_INVISIBLE );
    }
    
    /**
     * Deletes the item
     * @return boolean
     */
    public function delete()
    {
        if( $this->id )
        {
            return Claroline::getDatabase()->exec( "
                DELETE FROM
                    `{$this->tbl}`
                WHERE
                    id = " . Claroline::getDatabase()->escape( $this->id ) );
        }
    }
    
    /**
     * Gets a property from $this->data
     * @param string $propertyName
     * @return mixed : the property value
     */
    public function get( $name )
    {
        if( array_key_exists( $name , $this->data ) )
        {
            return $this->data[ $name ];
        }
    }
    
    /**
     * Sets property
     * @param string $name
     * @param mixed $value
     */
    public function set( $name , $value )
    {
        if( array_key_exists( $name , $this->propertyList ) )
        {
            $this->data[ $name ] = $value;
        }
        else
        {
            return false;
        }
    }
    
    public function getdata()
    {
        return $this->data;
    }
    
    public function setData( $data )
    {
        $this->data = $this->validate( $data , $this->propertyList );
    }
    
    public function validate( $data , $validation )
    {
        $data = array_merge( $validation , $data ); // fills missing fields with default values
        $data = array_intersect_key( $data , $validation ); // removes unwanted fields
        
        return $data;
    }
    
    public function getInstance( $itemId )
    {
        $className = get_class( $this );
        return new $className( $itemId );
    }
}