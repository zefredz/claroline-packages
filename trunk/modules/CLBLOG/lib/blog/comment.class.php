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
    
    if ( count( get_included_files() ) == 1 ) die( '---' );
    
    require_once dirname(__FILE__) . "/../database/connection.class.php";
    
    class Blog_Comment
    {
        var $config;
        
        var $connection;
        
        function Blog_Comment( &$connection, $config = null )
        {
            $this->config = $config ;
            $this->connection =& $connection;
        }
        
        function addComment( $postId, $userId, $contents )
        {
            $this->connection->connect();
            
            $sql = "INSERT INTO `" . $this->config['blog_comments'] . "`\n"
                . "SET userId = ".(int) $userId.",\n"
                . "contents = '".addslashes($contents)."',\n"
                . "ctime = '".date( "Y-m-d H:i:s" )."',\n"
                . "postId = ".(int) $postId
                ;
                
            $this->connection->executeQuery( $sql );
            
            $id = $this->connection->getLastInsertId();
            
            if ( $this->connection->hasError() )
            {
                return false;
            }
            else
            {
                return $id;
            }
        }
        
        function editComment( $id, $postId, $userId, $contents )
        {
            $this->connection->connect();
            
            $sql = "UPDATE `" . $this->config['blog_comments'] . "`\n"
                . "SET userId = ".(int) $userId.",\n"
                . "contents = '".addslashes($contents)."', \n"
                . "ctime = '".date( "Y-m-d H:i:s" )."',\n"
                . "postId = ".(int) $postId . "\n"
                . "WHERE id = " . (int) $id
                ;
                
            $this->connection->executeQuery( $sql );
            
            $id = $this->connection->getLastInsertId();
            
            if ( $this->connection->hasError() )
            {
                return false;
            }
            else
            {
                return $id;
            }
        }
        
        function getPostComment( $postId )
        {
            $this->connection->connect();
            
            $sql = "SELECT id, userId, postId, contents, ctime \n"
                . "FROM `" . $this->config['blog_comments'] . "`\n"
                . "WHERE postId = " . (int) $postId . "\n"
                . "ORDER BY id ASC"
                ;

            $result = $this->connection->getAllRowsFromQuery( $sql );
            
            if ( $this->connection->hasError() )
            {
                return false;
            }
            else
            {
                return $result;
            }
        }
        
        function getComment( $id )
        {
            $this->connection->connect();
            
            $sql = "SELECT id, userId, postId, contents, ctime \n"
                . "FROM `" . $this->config['blog_comments'] . "` \n"
                . "WHERE id = " . (int) $id . " \n"
                . "ORDER BY id ASC"
                ;

            $result = $this->connection->getRowFromQuery( $sql );
            
            if ( $this->connection->hasError() )
            {
                return false;
            }
            else
            {
                return $result;
            }
        }
        
        function getAll()
        {
            $this->connection->connect();
            
            $sql = "SELECT id, userId, postId, contents, ctime \n"
                . "FROM `" . $this->config['blog_comments'] . "`\n"
                . "ORDER BY id ASC"
                ;

            $result = $this->connection->getAllRowsFromQuery( $sql );
            
            if ( $this->connection->hasError() )
            {
                return null;
            }
            else
            {
                $ret = array();
                
                foreach ( $result as $row )
                {
                    $ret[] = $row;
                }
                
                return $ret;
            }
        }
        
        function deleteComment( $id )
        {
            $this->connection->connect();
            
            $sql = "DELETE FROM `" . $this->config['blog_comments'] . "`\n"
                . "WHERE id = " . (int) $id
                ;
                
            $this->connection->executeQuery( $sql );

            if ( $this->connection->hasError() )
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        
        function deletePostComment( $postId )
        {
            $this->connection->connect();
            
            $sql = "DELETE FROM `" . $this->config['blog_comments'] . "` \n"
                . "WHERE postId = " . (int) $postId
                ;
                
            $this->connection->executeQuery( $sql );

            if ( $this->connection->hasError() )
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        
        function getCommentNumber( $postId )
        {
            $this->connection->connect();
            
            $sql = "SELECT count(*) FROM `" . $this->config['blog_comments'] . "` \n"
                . "WHERE postId = " . (int) $postId
                ;
            
            return $this->connection->getSingleValueFromQuery( $sql );
        }
        
        function commentExists( $id )
        {
            $this->connection->connect();
            
            $sql = "SELECT id "
                . "FROM `" . $this->config['blog_comments'] . "` \n"
                . "WHERE id = " . (int) $id
                ;

            return $this->connection->queryReturnsResult( $sql );
        }
    }
?>