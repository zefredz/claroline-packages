<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    if ( count( get_included_files() ) == 1 )
    {
        die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
    }
    
    /**
     * Blog Post class
     *
     * @version     1.9 $Revision: 1.4 $
     * @copyright   2001-2007 Universite catholique de Louvain (UCL)
     * @author      Frederic Minne <zefredz@claroline.net>
     * @license     http://www.gnu.org/copyleft/gpl.html 
     *              GNU GENERAL PUBLIC LICENSE
     * @package     CLBLOG
     */
    
    class Blog_Post
    {
        var $config;
        
        var $connection;
        
        function Blog_Post( &$connection, $config )
        {
            $this->config = $config ;
            $this->connection =& $connection;
        }
        
        function addPost( $userId, $title, $contents, $chapo = '', $groupId = 0 )
        {
            $this->connection->connect();
            
            $sql = "INSERT INTO `" . $this->config['blog_posts'] . "` "
                . "SET userId = ".(int) $userId.", "
                . "groupId = ".(int) $groupId.", "
                . "title = '".addslashes($title)."', "
                . "chapo = '".addslashes($chapo)."', "
                . "contents = '".addslashes($contents)."', "
                . "ctime = '".date( "Y-m-d H:i:s", claro_time() )."' "
                ;
                
            $this->connection->executeQuery( $sql );
            
            $id = $this->connection->getLastInsertId();
            
            return $id;
        }
        
        function updatePost( $postId, $userId, $title, $contents, $chapo = '', $groupId )
        {
            $this->connection->connect();
            
            $sql = "UPDATE `" . $this->config['blog_posts'] . "` "
                . "SET userId = ".(int) $userId.", "
                . "groupId = ".(int) $groupId.", "
                . "title = '".addslashes($title)."', "
                . "chapo = '".addslashes($chapo)."', "
                . "contents = '".addslashes($contents)."' "
                . "WHERE id = ".(int) $postId
                ;
                
            $this->connection->executeQuery( $sql );
            
            $id = $this->connection->getLastInsertId();
            
            return $id;
        }
        
        function postExists( $postId )
        {
            $this->connection->connect();
            
            $sql = "SELECT id "
                . "FROM `" . $this->config['blog_posts'] . "` "
                . "WHERE id = " . (int) $postId
                ;

            return $this->connection->queryReturnsResult( $sql );
        }
        
        function getPost( $postId )
        {
            $this->connection->connect();
            
            $sql = "SELECT id, userId, groupId, title, chapo, contents, ctime "
                . "FROM `" . $this->config['blog_posts'] . "` "
                . "WHERE id = " . (int) $postId
                ;

            return $this->connection->getRowFromQuery( $sql );
        }
        
        function getAll()
        {
            $this->connection->connect();
            
            $sql = "SELECT id, userId, groupId, title, chapo, contents, ctime "
                . "FROM `" . $this->config['blog_posts'] . "` "
                . "ORDER BY id DESC"
                ;

            return $this->connection->getAllRowsFromQuery( $sql );
        }
        
        function deletePost( $id )
        {
            $this->connection->connect();
            
            $sql = "DELETE FROM `" . $this->config['blog_posts'] . "` "
                . "WHERE id = " . (int) $id
                ;
                
            $this->connection->executeQuery( $sql );

            return true;
        }
    }
?>