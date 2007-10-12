<?php  // $Id$
    
    // vim: expandtab sw=4 ts=4 sts=4:
    
    if ( count( get_included_files() ) == 1 ) die( '---' );
    
    /**
     * Dictionary List Class
     * 
     * @author  Frederic Minne <zefredz@claroline.net>
     * @copyright Copyright &copy; 2006, Frederic Minne
     * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @version 1.0
     * @package Glossary
     */
    
    require_once dirname(__FILE__) . "/dictionary.class.php";
	
	class Glossary_Dictionary_List
    {
        var $connection;
        var $tableList;
        var $dictionaryList = null;
        var $rootId = 0; 
        
        /**
         * Constructor
         * @param   DatabaseConnection connection
         * @param   array tableList database table list
         */
        function Glossary_Dictionary_List( &$connection, $tableList )
        {
            $this->connection =& $connection;
            $this->tableList = $tableList;
        }
        
        /**
         * Set id of the root dictionary
         * @param   int rootId
         */
        function setRootId( $rootId )
        {
            $this->rootId = $rootId;
        }
        
        /**
         * Load dictionary list
         */
        function load()
        {
            $this->connection->connect();
            
            $sql = "SELECT D.id, D.name, D.description, T.parentId " . "\n"
                . "FROM `" . $this->tableList['glossary_dictionary_tree']. "` AS T \n"
                . "INNER JOIN `" . $this->tableList['glossary_dictionaries'] . "` AS D \n"
                . "ON D.id = T.itemId \n"
                . "WHERE T.parentId = " . (int) $this->rootId
                ;
                
            $result = $this->connection->getAllRowsFromQuery( $sql );
            
            $this->dictionaryList = $result;
        }
        
        /**
         * Reload dictionary list, alias of load
         * @see     load
         */
        function reload()
        {
            $this->load();
        }
        
        /**
         * Create a new dictionary or edit an existing one
         * @param   string name
         * @param   string description (optional)
         * @param   int dictId (optional)
         * @param   int parentId (optional)
         * @return  int dictionary id, false on error
         */
        function createDictionary( $name, $description = '', $dictId = null, $parentId = null )
        {
            $this->connection->connect();
            
            // cannot edit default dictionary
            
            if ( 0 === $dictId )
            {
                return false;
            }
            
            if (is_null( $parentId ) )
            {
                $parentId = 0;
            }
            
            $sql = "SELECT id " . "\n"
                . "FROM `" . $this->tableList['glossary_dictionaries'] . "` AS D " . "\n"
                . "WHERE id = " . (int) $dictId  . "\n"
                ;
                
            if ( is_null( $dictId ) || ! $this->connection->queryReturnsResult( $sql ) )
            {
                $sql = "INSERT INTO `" . $this->tableList['glossary_dictionaries'] . "`\n"
                    . "SET name = '" . addslashes( $name ) . "', \n"
                    . "description = '" . addslashes( $description ) . "'\n"
                    ;
                    
                $this->connection->executeQuery( $sql );
                
                if ( $this->connection->hasError() )
                {
                    return false;
                } 
                
                $dictId = $this->connection->getLastInsertId();
                
                if ( ! is_null( $parentId ) )
                {
                    $sql = "INSERT INTO `" . $this->tableList['glossary_dictionary_tree'] . "`\n"
                        . "SET parentId = " . (int) $parentId . ", " . "\n"
                        . "itemId = " . (int) $dictId . "\n"
                        ;
                        
                    $this->connection->executeQuery( $sql );
                    
                    if ( $this->connection->hasError() )
                    {
                        return false;
                    }  
                }
                
                return $dictId;
            }
            else
            {
                $sql = "UPDATE `" . $this->tableList['glossary_dictionaries'] . "`\n"
                    . "SET name = '" . addslashes( $name ) . "', " . "\n"
                    . "description = '" . addslashes( $description ) . "' " . "\n"
                    . "WHERE id = " . (int) $dictId  . "\n"
                    ;
                    
                $this->connection->executeQuery( $sql );
                
                if ( $this->connection->hasError() )
                {
                    return false;
                }
                
                return $dictId;                
            }
        }
        
        /**
         * Create a new dictionary or edit an existing one
         * @param   int dictId
         * @param   string name
         * @param   string description (optional)
         * @return  int dictionary id, false on error
         */
        function updateDictionary( $dictId, $name, $description = '' )
        {
            return $this->createDictionary( $name, $description, $dictId );
        }
        
        function getDictionaryInfo( $dictId )
        {
            $this->connection->connect();
            
            $sql = "SELECT D.id, D.name, D.description, T.parentId " . "\n"
                . "FROM `" . $this->tableList['glossary_dictionary_tree']. "` AS T \n"
                . "INNER JOIN `" . $this->tableList['glossary_dictionaries'] . "` AS D \n"
                . "ON D.id = T.itemId \n"
                . "WHERE D.id = " . (int) $dictId . "\n"
                ;
                
            $result = $this->connection->getRowFromQuery( $sql );
            
            return $result;
        }
        
        /**
         * Get dictionary list
         * @return  array
         */
        function getDictionaryList()
        {
            if ( is_null( $this->dictionaryList ) )
            {
                $this->load();
            }
            
            return $this->dictionaryList;
        }
        
        function getAllDictionaries()
        {
            $this->connection->connect();
            
            $sql = "SELECT D.id, D.name, D.description, T.parentId " . "\n"
                . "FROM `" . $this->tableList['glossary_dictionary_tree']. "` AS T \n"
                . "INNER JOIN `" . $this->tableList['glossary_dictionaries'] . "` AS D \n"
                . "ON D.id = T.itemId \n"
                . "ORDER BY D.id ASC"
                ;
                
            $result = $this->connection->getAllRowsFromQuery( $sql );
            
            return $result;
        }
        
        /**
         * Delete the given dictionary
         * @param   int dictId
         * @return  boolean
         * @todo    FIXME garbage collector
         */
        function deleteDictionary( $dictId )
        {
            // 1. delete all dictionary entries from word_defs
            
            #$dict = new GlossaryDictionary( $this->connection, $this->tableList )
            #$dict->setId( (int) $dictId );
            #$dict->delete();
            
            // 2. run garbage collector on words and definitions table
            
            #$dict->gc();
            
            // 3. delete dictionary info from dict
            
            $this->connection->connect();
            
            $sql = "DELETE FROM `" . $this->tableList['glossary_dictionaries'] . "`\n"
                . "WHERE id = "  . (int) $dictId
                ;
                
            $this->connection->executeQuery( $sql );
            
            $sql = "DELETE FROM `" . $this->tableList['glossary_dictionary_tree'] . "`\n"
                . "WHERE itemId = "  . (int) $dictId
                ;
                
            return $this->connection->executeQuery( $sql );
        }
        
        /**
         * Check if a given dictionary exists
         * @param   int dictionaryId
         * @return  boolean
         */
        function dictionaryExists( $dictionaryId )
        {
            $this->connection->connect();
            
            $sql = "SELECT id " . "\n"
                . "FROM `" . $this->tableList['glossary_dictionaries'] . "` AS D \n"
                . "WHERE id = " . (int) $dictionaryId  . "\n"
                ;
                
            return $this->connection->queryReturnsResult( $sql );
        }
    }
?>