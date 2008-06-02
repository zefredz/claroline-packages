<?php

// FIXME : move to inc/lib/database/database.lib.php
interface CrudResource
{
    public function create();
    public function delete();
    public function update();
    public function toArray();
    
    public static function load( $id );
    public static function fromArray( $data );
    public static function loadAll();
}

interface UserCrudResource extends CrudResource
{
    public static function loadAllForUSer( $userId );
}

class MysqlResultSetCountableIterator implements Iterator, Countable
{
    protected $resultSet;
    protected $mode;
    protected $idx;
    protected $numrows;
    protected $valid;
    
    const FETCH_ASSOC = MYSQL_ASSOC;
    const FETCH_NUM = MYSQL_NUM;
    const FETCH_BOTH = MYSQL_BOTH;
    const FETCH_OBJECT = 'FETCH_OBJECT';
    
    public function __construct( $result, $mode = self::FETCH_ASSOC )
    {
        $this->resultSet = $result;
        $this->mode = $mode;
        $this->numrows = mysql_num_rows( $this->resultSet );
        $this->idx = 0;
    }
    
    public function __destruct()
    {
        @mysql_free_result($this->resultSet);
        
        unset( $this->numrows );
        unset( $this->mode );
        unset( $this->valid );
    }
    
    public function count()
    {
        return $this->numrows;
    }
    
    public function valid()
    {
        return $this->valid;
    }
    
    public function current()
    {
        // Go to the correct data
        @mysql_data_seek( $this->resultSet, $this->idx );
        
        if ( $this->mode == self::FETCH_OBJECT )
        {
            return mysql_fetch_object( $this->resultSet );
        }
        else
        {
            return mysql_fetch_array( $this->resultSet, $this->mode );
        }
    }
    
    public function next()
    {
        $this->idx++;
        $this->valid = $this->idx < $this->numrows;
    }
    
    public function rewind()
    {
        $this->idx = 0;
        $this->valid = @mysql_data_seek( $this->resultSet, 0 );
    }
    
    public function key()
    {
        return $this->idx;
    }
}
