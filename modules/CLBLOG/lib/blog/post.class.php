<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * Blog Post class
 *
 * @version     2.0 $Revision$
 * @copyright   2001-2014 Universite catholique de Louvain (UCL)
 * @author      Frederic Minne <zefredz@claroline.net>
 * @license     http://www.gnu.org/copyleft/gpl.html 
 *              GNU GENERAL PUBLIC LICENSE
 * @package     CLBLOG
 */

class Blog_Post
{
    protected $config;
    protected $connection;
    
    public function __construct( $connection, $config )
    {
        $this->config = $config ;
        $this->connection = $connection;
    }
    
    public function addPost( $userId, $title, $contents, $chapo = '', $groupId = 0 )
    {
        $sql = "INSERT INTO `" . $this->config['blog_posts'] . "` "
            . "SET userId = ".(int) $userId.", "
            . "groupId = ".(int) $groupId.", "
            . "title = '".addslashes($title)."', "
            . "chapo = '".addslashes($chapo)."', "
            . "contents = '".addslashes($contents)."', "
            . "ctime = '".date( "Y-m-d H:i:s", claro_time() )."' "
            ;
            
        $this->connection->exec( $sql );
        
        return $this->connection->insertId();
    }
    
    public function updatePost( $postId, $userId, $title, $contents, $chapo = '', $groupId = 0 )
    {
        $sql = "UPDATE `" . $this->config['blog_posts'] . "` "
            . "SET userId = ".(int) $userId.", "
            . "groupId = ".(int) $groupId.", "
            . "title = '".addslashes($title)."', "
            . "chapo = '".addslashes($chapo)."', "
            . "contents = '".addslashes($contents)."' "
            . "WHERE id = ".(int) $postId
            ;
            
        $this->connection->exec( $sql );
        
        return $this->connection->insertId();
    }
    
    public function postExists( $postId )
    {
        $sql = "SELECT id "
            . "FROM `" . $this->config['blog_posts'] . "` "
            . "WHERE id = " . (int) $postId
            ;

        $res = $this->connection->query( $sql );
        
        return $res->numRows() > 0;
    }
    
    public function getPost( $postId )
    {
        $sql = "SELECT id, userId, groupId, title, chapo, contents, ctime "
            . "FROM `" . $this->config['blog_posts'] . "` "
            . "WHERE id = " . (int) $postId
            ;

        $res = $this->connection->query( $sql );
        
        return $res->fetch();
    }
    
    public function getAll( $groupId = 0 )
    {
        $sql = "SELECT id, userId, groupId, title, chapo, contents, ctime "
            . "FROM `" . $this->config['blog_posts'] . "` "
            . "WHERE groupId = " . (int) $groupId . " "
            . "ORDER BY id DESC"
            ;

        return $this->connection->query( $sql );
    }
    
    public function deletePost( $id )
    {
        $sql = "DELETE FROM `" . $this->config['blog_posts'] . "` "
            . "WHERE id = " . (int) $id
            ;
            
        $this->connection->exec( $sql );

        return $this->connection->affectedRows();
    }
}
