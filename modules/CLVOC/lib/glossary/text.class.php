<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    if ( count( get_included_files() ) == 1 ) die( '---' );
    
    /**
     * Text class
     * @author  Frederic Minne <zefredz@claroline.net>
     * @copyright Copyright &copy; 2006, Frederic Minne
     * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @version 1.0
     * @package Glossary
     */
    
    class Glossary_Text
    {
        var $connection;
        var $tableList;
        var $title;
        var $content;
        var $id;
        var $wordList;
        var $dictionaryId = null;
        
        /**
         * Constructor
         * @param   DatabaseConnection connection
         * @param   array tableList database table list
         */
        function Glossary_Text( &$connection, $tableList )
        {
            $this->connection =& $connection;
            $this->tableList = $tableList;
            $this->title = NULL;
            $this->content = NULL;
            $this->id = 0;
            $this->wordList = array();
        }
        
        /**
         * Set text title
         * @param   string title
         */
        function setTitle( $title )
        {
            $this->title = $title;
        }
        
        /**
         * Get text title
         * @return  string title
         */
        function getTitle()
        {
            return $this->title;
        }
        
        /**
         * Set text content
         * @param   string content
         */
        function setContent( $content )
        {
            $this->content = $content;
        }
        
        /**
         * Get text content
         * @return  string content
         */
        function getContent()
        {
            return $this->content;
        }
        
        /**
         * Set text id
         * @param   int id
         */
        function setId( $id )
        {
            $this->id = (int) $id;
        }
        
        /**
         * Get text id
         * @return  int text id
         */
        function getId()
        {
            return $this->id;
        }
        
        /**
         * Set text dictionary
         * @param   int dictionaryId
         */
        function setDictionaryId( $dictionaryId )
        {
            $this->dictionaryId = $dictionaryId;
        }
        
        /**
         * Get text dictionary
         * @return  int dictionary id
         */
        function getDictionaryId()
        {
            return (int) $this->dictionaryId;
        }
        
        /**
         * Set word list
         * @param   array arrWordList list of words
         */
        function setWordList( $arrWordList )
        {
            $this->wordList = $arrWordList;
        }
        
        /**
         * Get word list
         * @return  array wordlist
         */
        function getWordList()
        {
            return $this->wordList;
        }
        
        /**
         * Add a word to word list
         * @param   string word
         * @return  boolean
         */
        function addWord( $word )
        {
            if ( in_array( $word, $this->wordList ) )
            {
                return false;
            }
            else
            {
                $this->wordList[] = $word;
                return true;
            }
        }
        
        /**
         * Delete a word from word list
         * @param   string word
         * @return  boolean
         */
        function deleteWord( $word )
        {
            if ( in_array( $word, $this->wordList ) )
            {
                foreach ( $this->wordList as $key => $theWord )
                {
                    if ( $word == $theWord )
                    {
                        unset( $this->wordList[$key] );
                        break;
                    }
                }
                return true;
            }
            else
            {
                return false;
            }
        }
        
        /**
         * Load text from database
         * @return  boolean
         */
        function load()
        {
            $this->connection->connect();
            
            $sql = "SELECT id, title, content, wordList " ."\n"
                . "FROM `".$this->tableList['glossary_texts']."` \n"
                . "WHERE id = " . (int) $this->getId() ."\n"
                ; 
                
            $text = $this->connection->getRowFromQuery( $sql );
            
            if ( $this->connection->hasError() )
            {
                return false;
            }
            
            if ( ! is_null( $text ) )
            {
                $this->setId( $text['id'] );
                $title = stripslashes( $text['title'] );
                $this->setTitle( $title );
                $content = stripslashes( $text['content'] );
                $this->setContent( $content );
                $wordList = trim(stripslashes( $text['wordList'] ));
                if ( !empty( $wordList ) )
                {
                    $this->setWordList( explode( '|', $wordList ) );
                }
                else
                {
                    $this->setWordList( array() );
                }
                
                $sql = "SELECT dictionaryId " ."\n"
                    . "FROM `".$this->tableList['glossary_text_dictionaries']."` \n"
                    . "WHERE textId = " . (int) $this->getId() ."\n"
                    ;
                
                // change to multiple dictionaries    
                $dictionaryId = $this->connection->getSingleValueFromQuery( $sql );
                
                $this->setDictionaryId( $dictionaryId );
                
                return true;
            }
            else
            {
                return false;
            }
        }
        
        /**
         * Save text to database
         * @return  boolean
         */
        function save()
        {
            $this->connection->connect();
            
            if ( 0 == $this->id )
            {
                // insert
                $sql = "INSERT INTO `" . $this->tableList['glossary_texts'] ."`\n"
                    . " SET title = '". addslashes( $this->title ) . "', " ."\n"
                    . "content = '". addslashes( $this->content ) . "'" ."\n"
                    ;
                    
                $this->connection->executeQuery( $sql );
            
                if ( $this->connection->hasError() )
                {
                    return false;
                }
            
                $textId = $this->connection->getLastInsertId();
                
                $this->setId( $textId );
                
                if ( ! is_null( $this->dictionaryId ) )
                {
                    $sql = "INSERT INTO `" . $this->tableList['glossary_text_dictionaries'] ."` \n"
                        . " SET textId = ". (int) $textId . ", " ."\n"
                        . "dictionaryId = ". (int) $this->dictionaryId . "\n"
                        ;
                    $this->connection->executeQuery( $sql );
                    
                    if ( $this->connection->hasError() )
                    {
                        return false;
                    }
                }
            }
            else
            {
                // update
                $sql = "UPDATE `" . $this->tableList['glossary_texts'] ."`\n"
                    . " SET title = '". addslashes( $this->title ) . "', " ."\n"
                    . "wordList = '". addslashes( implode( '|', $this->wordList ) ) . "', " ."\n"
                    . "content = '". addslashes( $this->content ) . "' " ."\n"
                    . "WHERE id = " . (int) $this->id ."\n"
                    ;
                    
                $this->connection->executeQuery( $sql );
            
                if ( $this->connection->hasError() )
                {
                    return false;
                }
                
                if ( ! is_null( $this->dictionaryId ) )
                {
                    $sql = "SELECT textId "
                        . "FROM `" . $this->tableList['glossary_text_dictionaries'] ."` \n"
                        . "WHERE textId = ". (int) $this->getId() . "\n"
                        ;
                        
                    if ( $this->connection->queryReturnsResult( $sql ) )
                    {  
                        $sql = "UPDATE `" . $this->tableList['glossary_text_dictionaries'] ."`\n"
                            . " SET dictionaryId = ". (int) $this->getDictionaryId() . " \n"
                            . "WHERE textId = ". (int) $this->getId() . "\n"
                            ;
                    }
                    else
                    {
                        $sql = "INSERT INTO `" . $this->tableList['glossary_text_dictionaries'] ."`\n"
                            . " SET textId = ". (int) $this->getId() . ", " ."\n"
                            . "dictionaryId = ". (int) $this->getDictionaryId() . "\n"
                            ;
                    }
                    
                    $this->connection->executeQuery( $sql );
                    
                    if ( $this->connection->hasError() )
                    {
                        return false;
                    }
                }
            }
            
            return true;
        }
        
        /**
         * Delete the text from database
         * @return  boolean
         */
        function delete()
        {
            $this->connection->connect();
            
            $sql = "DELETE " ."\n"
                . "FROM `".$this->tableList['glossary_texts']."`\n"
                . "WHERE id = " . (int) $this->id ."\n"
                ;
            
            $this->connection->executeQuery( $sql );
            
            if ( $this->connection->hasError() )
            {
                return false;
            }
            else
            {
                if ( ! is_null( $this->dictionaryId ) )
                {
                    $sql = "DELETE " . "\n"
                        . "FROM `" . $this->tableList['glossary_text_dictionariess'] ."`\n"
                        . "WHERE textId = ". (int) $this->getId() . "\n"
                        ;
                        
                    $this->connection->executeQuery( $sql );
                    
                    if ( $this->connection->hasError() )
                    {
                        return false;
                    }
                }
                
                return true;
            }
        }
        
        /**
         * Get list of texts
         * @return  array (id, title, content)
         */
        function getList()
        {
            $this->connection->connect();
            
            $sql = "SELECT id, title, content " ."\n"
                . "FROM `".$this->tableList['glossary_texts'] ."`\n"
                ; 
                
            $textList = $this->connection->getAllRowsFromQuery( $sql );
            
            if ( $this->connection->hasError() )
            {
                return false;
            }
            
            return $textList;
        }
        
        /**
         * Get dictionary list
         * @param   int textId
         * @return  array dictionary ids
         */
        function getDictionaryList( $textId )
        {            
            $this->connection->connect();
            
            $sql = "SELECT dictionaryId "
                . "FROM `" .$this->tableList['glossary_text_dictionaries'] ."` \n"
                . "WHERE textId = " . (int) $textId
                ;
                
            $dictionaryList = $this->connection->getColumnFromQuery( $sql );
            
            return $dictionaryList;
        }
        
    	function getGlossary()
    	{
    		
            $wordList = $this->getWordList();
            $wordList = array_map("addslashes",$wordList);

            $sql = "SELECT " . "\n"
            . "W.id AS wordId, " . "\n"
            . "W.name , " . "\n"
            . "WD.id AS entryId, " . "\n"
            . "WD.dictionaryId, " . "\n"
            . "D.id AS definitionId, " . "\n"
            . "D.definition " . "\n"
            . "FROM " . "\n"
            . "`".$this->tableList['glossary_words']."` AS W " . "\n"
            . "LEFT JOIN " . "\n"
            . "`".$this->tableList['glossary_word_definitions']."` AS WD " . "\n"
            . "ON W.id = WD.wordId " . "\n"
            . "LEFT JOIN " . "\n"
            . "`".$this->tableList['glossary_definitions']."` AS D " . "\n"
            . "ON WD.definitionId = D.id " . "\n"
            . "WHERE WD.dictionaryId = '1' " . "\n"
            . "AND  " . "\n"
            . "W.name IN ('" . implode("','",$wordList) . "') " . "\n"
            . "ORDER BY wordId ASC " . "\n"
            ;
            
    		if ( false !== ($result = claro_sql_query_fetch_all_rows($sql)) )
    		{
    			return $result;
    		}
    		else
    		{				
    			return claro_failure::set_failure('SEARCH_FAILED');
    		}
    		
    	}			
        
    }
?>