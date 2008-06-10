<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * Blog Comment class
 *
 * @version     1.9 $Revision$
 * @copyright   2001-2007 Universite catholique de Louvain (UCL)
 * @author      Frederic Minne <zefredz@claroline.net>
 * @license     http://www.gnu.org/copyleft/gpl.html 
 *              GNU GENERAL PUBLIC LICENSE
 * @package     CLBLOG
 */

class Blog_Comment
{
    protected $config;
    protected $connection;
    
    public function __construct( $connection, $config = null )
    {
        $this->config = $config ;
        $this->connection = $connection;
    }
    
    public function addComment( $postId, $userId, $contents )
    {
        $sql = "INSERT INTO `" . $this->config['blog_comments'] . "`\n"
            . "SET userId = ".(int) $userId.",\n"
            . "contents = '".addslashes($contents)."',\n"
            . "ctime = '".date( "Y-m-d H:i:s", claro_time() )."',\n"
            . "postId = ".(int) $postId
            ;
            
        $this->connection->exec( $sql );
        
        return $this->connection->insertId();
    }
    
    public function editComment( $id, $postId, $userId, $contents )
    {
        $sql = "UPDATE `" . $this->config['blog_comments'] . "`\n"
            . "SET userId = ".(int) $userId.",\n"
            . "contents = '".addslashes($contents)."', \n"
            . "ctime = '".date( "Y-m-d H:i:s", claro_time() )."',\n"
            . "postId = ".(int) $postId . "\n"
            . "WHERE id = " . (int) $id
            ;
            
        $this->connection->exec( $sql );
        
        return $this->connection->insertId();
    }
    
    public function getPostComment( $postId )
    {
        $sql = "SELECT id, userId, postId, contents, ctime \n"
            . "FROM `" . $this->config['blog_comments'] . "`\n"
            . "WHERE postId = " . (int) $postId . "\n"
            . "ORDER BY id ASC"
            ;

        return $this->connection->query( $sql );
    }
    
    public function getComment( $id )
    {
        $sql = "SELECT id, userId, postId, contents, ctime \n"
            . "FROM `" . $this->config['blog_comments'] . "` \n"
            . "WHERE id = " . (int) $id . " \n"
            . "ORDER BY id ASC"
            ;

        $result = $this->connection->query( $sql );
        
        return $result->fetch();
    }
    
    public function getAll()
    {
        $sql = "SELECT id, userId, postId, contents, ctime \n"
            . "FROM `" . $this->config['blog_comments'] . "`\n"
            . "ORDER BY id ASC"
            ;

        return $this->connection->query( $sql );
    }
    
    public function deleteComment( $id )
    {
        $sql = "DELETE FROM `" . $this->config['blog_comments'] . "`\n"
            . "WHERE id = " . (int) $id
            ;
            
        $this->connection->exec( $sql );

        return $this->connection->affectedRows();
    }
    
    public function deletePostComment( $postId )
    {
        $sql = "DELETE FROM `" . $this->config['blog_comments'] . "` \n"
            . "WHERE postId = " . (int) $postId
            ;
            
        $this->connection->exec( $sql );

        return $this->connection->affectedRows();
    }
    
    public function getCommentNumber( $postId )
    {
        $sql = "SELECT count(*) FROM `" . $this->config['blog_comments'] . "` \n"
            . "WHERE postId = " . (int) $postId
            ;
        
        $result = $this->connection->query( $sql );
        
        return $result->fetch(MysqlResultSet::FETCH_VALUE);
    }
    
    public function commentExists( $id )
    {
        $sql = "SELECT id "
            . "FROM `" . $this->config['blog_comments'] . "` \n"
            . "WHERE id = " . (int) $id
            ;

        $result = $this->connection->query( $sql );
        
        return $result->numRows() > 0;
    }
}
