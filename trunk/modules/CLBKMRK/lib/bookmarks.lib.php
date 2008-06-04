<?php

From::module('CLBKMRK')->uses('database.lib');

function cut_long_url_for_display( $url )
{
    if ( strlen($url) > 50 )
    {
        return substr( $url, 0, 45 ) . '[...]';
    }
    else
    {
        return $url;
    }
}

class Bookmark implements UserCrudResource
{
    protected $id;
    protected $name;
    protected $url;
    protected $owner;
    
    protected static $db;
    protected static $databaseTables;
    
    public function __construct()
    {
        self::init();
    }
    
    protected static function init()
    {
        self::$databaseTables = get_module_main_tbl(array('clbkmrk_bookmarks'));
        self::$db = new ClarolineQuery;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    protected function setId( $id )
    {
        $this->id = (int) $id;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName( $name )
    {
        $this->name = $name;
    }
    
    public function getUrl()
    {
        return $this->url;
    }
    
    public function setUrl( $url )
    {
        $this->url = $url;
    }
    
    public function getOwner()
    {
        return $this->owner;
    }
    
    public function setOwner( $owner )
    {
        $this->owner = (int) $owner;
    }
    
    public function create()
    {
        $sql = "INSERT INTO `".self::$databaseTables['clbkmrk_bookmarks']."`\n"
            . "SET\n"
            . "`name` = '".claro_sql_escape($this->name)."',\n"
            . "`url` = '".claro_sql_escape($this->url)."',\n"
            . "`owner_id` = " . (int) $this->owner
            ;
        
        return self::$db->exec( $sql );
    }
    
    public function delete()
    {
        $sql = "DELETE\n"
            . "FROM `".self::$databaseTables['clbkmrk_bookmarks']."`\n"
            . "WHERE id = " . (int) $this->id
            ;
        
        return self::$db->exec( $sql );
    }
    
    public function update()
    {
        $sql = "UPDATE `".self::$databaseTables['clbkmrk_bookmarks']."`\n"
            . "SET\n"
            . "`name` = '".claro_sql_escape($this->name)."',\n"
            . "`url` = '".claro_sql_escape($this->url)."',\n"
            . "`owner_id` = " . (int) $this->owner ."\n"
            . "WHERE id = " . (int) $this->id
            ;
        
        return self::$db->exec( $sql );
    }
    
    public static function __set_state( $properties )
    {
        $bk = new self;
        $bk->setId((int) $properties['id']);
        $bk->setName($properties['name']);
        $bk->setUrl($properties['url']);
        $bk->setOwner($properties['owner_id']);
        
        return $bk;
    }
    
    public static function load( $id )
    {
        self::init();
        
        $sql = "SELECT `id`, `name`, `url`, `owner_id`\n"
            . "FROM `".self::$databaseTables['clbkmrk_bookmarks']."`\n"
            . "WHERE id = " . (int) $id
            ;
        
        $res = self::$db->query( $sql )->fetch(MysqlResultSet::FETCH_ASSOC);
        
        if ( $res )
        {
            return self::__set_state( $res );
        }
        else
        {
            throw new Exception("Cannot load bookmark from the database");
        }
    }
    
    public static function fromArray( $data )
    {
        return self::__set_state( $data );
    }
    
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'url' => $this-getUrl(),
            'owner' => $this->getOwner()
        );
    }
    
    public static function loadAllForUSer( $userId )
    {
        self::init();
        
        $sql = "SELECT `id`, `name`, `url`, `owner_id`\n"
            . "FROM `".self::$databaseTables['clbkmrk_bookmarks']."`\n"
            . "WHERE `owner_id` = " . (int) $userId
            ;
            
        return self::$db->query( $sql );
    }
    
    public static function loadAll()
    {
        self::init();
        
        $sql = "SELECT `id`, `name`, `url`, `owner_id`\n"
            . "FROM `".self::$databaseTables['clbkmrk_bookmarks']."`\n"
            ;
        
        return self::$db->query( $sql );
    }
}
