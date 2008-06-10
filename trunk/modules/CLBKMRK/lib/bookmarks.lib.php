<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * Bookmark management class and utilities
 *
 * @version     1.9 $Revision$
 * @copyright   2001-2008 Universite catholique de Louvain (UCL)
 * @author      Claroline Team <info@claroline.net>
 * @author      Frederic Minne <zefredz@claroline.net>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE version 2 or later
 * @package     CLBKMRK
 */

// load required libraries
From::module('CLBKMRK')->uses('crud.lib');

/**
 * Cut a URL after a given number of characters
 * @param   string $url
 * @param   int $size
 * @return  string
 */
function cut_long_url_for_display( $url, $size = 50 )
{
    if ( strlen($url) > $size )
    {
        return substr( $url, 0, $size - 5 ) . '[...]';
    }
    else
    {
        return $url;
    }
}

/**
 * Bookmark management class
 */
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
        self::$db = Claroline::getDatabase();
    }
    
    /**
     * Return the id of the bookmark
     * @return  int
     */
    public function getId()
    {
        return $this->id;
    }
    
    protected function setId( $id )
    {
        $this->id = (int) $id;
    }
    
    /**
     * Return the name of the bookmark
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Set the name of the bookmark
     * @param   string $name
     */
    public function setName( $name )
    {
        $this->name = $name;
    }
    
    /**
     * Return the URL of the bookmark
     * @return  string
     */
    public function getUrl()
    {
        return $this->url;
    }
    
    /**
     * Set the URL of the bookmark
     * @param   string $url
     */
    public function setUrl( $url )
    {
        $this->url = $url;
    }
    
    /**
     * Return the id of the owner of the bookmark
     * @return  int
     */
    public function getOwner()
    {
        return $this->owner;
    }
    
    /**
     * Set the id of the owner of the bookmark
     * @param   int $owner id of the owner
     */
    public function setOwner( $owner )
    {
        $this->owner = (int) $owner;
    }
    
    /**
     * @see UserCrudResource
     */
    public function create()
    {
        $sql = "INSERT INTO `".self::$databaseTables['clbkmrk_bookmarks']."`\n"
            . "SET\n"
            . "`name` = '".self::$db->escape($this->name)."',\n"
            . "`url` = '".self::$db->escape($this->url)."',\n"
            . "`owner_id` = " . (int) $this->owner
            ;
        
        return self::$db->exec( $sql );
    }
    
    /**
     * @see UserCrudResource
     */
    public function delete()
    {
        $sql = "DELETE\n"
            . "FROM `".self::$databaseTables['clbkmrk_bookmarks']."`\n"
            . "WHERE id = " . (int) $this->id
            ;
        
        return self::$db->exec( $sql );
    }
    
    /**
     * @see UserCrudResource
     */
    public function update()
    {
        $sql = "UPDATE `".self::$databaseTables['clbkmrk_bookmarks']."`\n"
            . "SET\n"
            . "`name` = '".self::$db->escape($this->name)."',\n"
            . "`url` = '".self::$db->escape($this->url)."',\n"
            . "`owner_id` = " . (int) $this->owner ."\n"
            . "WHERE id = " . (int) $this->id
            ;
        
        return self::$db->exec( $sql );
    }
    
    /**
     * Set the internal state of the object
     * PHP Magic method, see PHP object model documentation for details
     */
    public static function __set_state( $properties )
    {
        $bk = new self;
        $bk->setId((int) $properties['id']);
        $bk->setName($properties['name']);
        $bk->setUrl($properties['url']);
        $bk->setOwner($properties['owner_id']);
        
        return $bk;
    }
    
    /**
     * @see UserCrudResource
     */
    public static function load( $id )
    {
        self::init();
        
        $sql = "SELECT `id`, `name`, `url`, `owner_id`\n"
            . "FROM `".self::$databaseTables['clbkmrk_bookmarks']."`\n"
            . "WHERE id = " . (int) $id
            ;
        
        $res = self::$db->query( $sql )->fetch(Mysql_ResultSet::FETCH_ASSOC);
        
        if ( $res )
        {
            return self::__set_state( $res );
        }
        else
        {
            throw new Exception("Cannot load bookmark from the database");
        }
    }
    
    /**
     * @see UserCrudResource
     */
    public static function fromArray( $data )
    {
        return self::__set_state( $data );
    }
    
    /**
     * @see UserCrudResource
     */
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'url' => $this-getUrl(),
            'owner' => $this->getOwner()
        );
    }
    
    /**
     * @see UserCrudResource
     */
    public static function loadAllForUSer( $userId )
    {
        self::init();
        
        $sql = "SELECT `id`, `name`, `url`, `owner_id`\n"
            . "FROM `".self::$databaseTables['clbkmrk_bookmarks']."`\n"
            . "WHERE `owner_id` = " . (int) $userId
            ;
            
        return self::$db->query( $sql );
    }
    
    /**
     * @see UserCrudResource
     */
    public static function loadAll()
    {
        self::init();
        
        $sql = "SELECT `id`, `name`, `url`, `owner_id`\n"
            . "FROM `".self::$databaseTables['clbkmrk_bookmarks']."`\n"
            ;
        
        return self::$db->query( $sql );
    }
}
