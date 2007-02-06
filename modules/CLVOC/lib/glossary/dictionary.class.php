<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    if ( count( get_included_files() ) == 1 ) die( '---' );
    
    /**
     * Dictionary Class
     * 
     * @author  Frederic Minne <zefredz@claroline.net>
     * @copyright Copyright &copy; 2006, Frederic Minne
     * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @version 1.0
     * @package Glossary
     */
    
    class Glossary_Dictionary
    {
        var $connection;
        var $tableList;
        var $dictionary = null; 
        var $dictionaryId = null;
        
        /**
         * Constructor
         * @param   DatabaseConnection connection
         * @param   array tableList database table list
         */
        function Glossary_Dictionary( &$connection, $tableList )
        {
            $this->connection =& $connection;
            $this->tableList = $tableList;
        }
        
        /**
         * Set dictionary id
         * @param   int id dictionary id
         */
        function setId( $dictionaryId )
        {
            $this->dictionaryId = $dictionaryId;
        }
        
        /**
         * Load dictionary object from database
         */
        function load()
        {
            $this->connection->connect();
            
            $where = ( is_null( $this->dictionaryId ) ) 
                ? ''
                : "WHERE WD.dictionaryId = ". (int) $this->dictionaryId
                ;
                
            $sql = "SELECT W.name, D.definition, W.id AS wordId, D.id AS definitionId " . "\n"
                . "FROM ". $this->tableList['glossary_word_definitions']." AS WD " . "\n"
                . "INNER JOIN ". $this->tableList['glossary_words']." AS W " . "\n"
                . "ON W.id = WD.wordId " . "\n"
                . "INNER JOIN ". $this->tableList['glossary_definitions']." AS D " . "\n"
                . "ON D.id = WD.definitionId " . "\n"
                . $where . " " . "\n"
                . "ORDER BY UPPER(W.name) ASC" . "\n"
                ;
    
            $result = $this->connection->getAllRowsFromQuery( $sql );
            
            $this->dictionary = $result;
        }
        
        /**
         * Reload dictionary from database
         * @see load
         */
        function reload()
        {
            $this->load();
        }
        
        /**
         * Load virtual dictionary from several dictionaries in database
         * @param   array (name, definition, wordId, definitionId)
         */
        function loadFromMultipleDictionaries( $dictIdList )
        {
            $this->connection->connect();
            
            $where = "WHERE WD.dictionaryId IN (". implode(',', $dictIdList ) .")";
                
            $sql = "SELECT W.name, D.definition, W.id AS wordId, D.id AS definitionId " . "\n"
                . "FROM ". $this->tableList['glossary_word_definitions']." AS WD " . "\n"
                . "INNER JOIN ". $this->tableList['glossary_words']." AS W " . "\n"
                . "ON W.id = WD.wordId " . "\n"
                . "INNER JOIN ". $this->tableList['glossary_definitions']." AS D " . "\n"
                . "ON D.id = WD.definitionId " . "\n"
                . $where . " " . "\n"
                . "ORDER BY UPPER(W.name) ASC" . "\n"
                ;
    
            $result = $this->connection->getAllRowsFromQuery( $sql );
            
            $this->dictionary = $result;
        }
        
        /**
         * Get dictionary as an array
         * @return  array dictionary
         */
        function getDictionary()
        {
            if ( is_null( $this->dictionary ) )
            {
                $this->load();
            }
            
            return $this->dictionary;
        }
        
        /**
         * Get word from id in dictionary
         * @param   int wordId
         * @return  string word, null if no result found
         */
        function getWord( $wordId )
        {
            $this->connection->connect();
                 
            $sql = "SELECT name " . "\n"
                . "FROM ".$this->tableList['glossary_words']." " . "\n"
                . "WHERE id = ". (int) $wordId . "\n"
                ;
                
            $result = $this->connection->getRowFromQuery( $sql );
            
            if ( ! empty( $result ) )
            {
                return $result['name'];
            }
            else
            {
                return NULL;
            }
        }
        
        /**
         * Check if a word exists in the given dictionary
         * @param   int wordId
         * @param   in dictionaryId
         */
        function wordExistsInDictionary( $wordId, $dictionaryId )
        {
            $this->connection->connect();
                 
            $sql = "SELECT id " . "\n"
                . "FROM ".$this->tableList['glossary_word_definitions']." " . "\n"
                . "WHERE wordId = ". (int) $wordId . " \n"
                . "AND dictionaryId = " . (int) $dictionaryId
                ;
                
            return $this->connection->queryReturnsResult( $sql );
        }
        
        /**
         * Check if a word exists in the current dictionary
         * @param   int wordId
         * @param   in dictionaryId
         */
        function wordExists( $wordId )
        {
            $this->connection->connect();
                 
            $sql = "SELECT id " . "\n"
                . "FROM ".$this->tableList['glossary_word_definitions']." " . "\n"
                . "WHERE wordId = ". (int) $wordId . " \n"
                . "AND dictionaryId = " . (int) $this->dictionaryId
                ;
                
            return $this->connection->queryReturnsResult( $sql );
        }
        
        /**
         * Add word and definition to dictionary
         * @param   string word
         * @param   string definition
         * @return  int wordId on success, boolean false on failure
         */
        function addWord( $word, $definition )
        {
            $this->connection->connect();
            
            
            
            // check if the word is in the dictionary            
            $sql = "SELECT id " . "\n"
                . "FROM ".$this->tableList['glossary_words']." " . "\n"
                . "WHERE name = '".addslashes( $word )."'" . "\n"
                ; 
                
            $result = $this->connection->getRowFromQuery( $sql );
            
            if ( $this->connection->hasError() )
            {
                return false;
            }
            
            $wordId = isset( $result['id'] )
                ? (int) $result['id']
                : null
                ;
            
            // word not in dictionary
            if ( is_null( $wordId ) )
            {
                $sql = "INSERT INTO " . "\n"
                    .$this->tableList['glossary_words'] . "\n"
                    ." SET name = '". addslashes( $word ) . "'" . "\n"
                    ;
                $this->connection->executeQuery( $sql );
            
                if ( $this->connection->hasError() )
                {
                    return false;
                }
            
                $wordId = $this->connection->getLastInsertId();
            }
            
            if ( $this->connection->hasError() )
            {
                return false;
            }
            
            // check if the definition is in the dictionary            
            $sql = "SELECT id " . "\n"
                . "FROM ".$this->tableList['glossary_definitions']." " . "\n"
                . "WHERE definition = '".addslashes( $definition )."'" . "\n"
                ; 
                
            $result = $this->connection->getRowFromQuery( $sql );
            
            if ( $this->connection->hasError() )
            {
                return false;
            }
            
            $defId = isset( $result['id'] )
                ? (int) $result['id']
                : null
                ;
                
            if ( is_null( $defId ) )
            {
                $sql = "INSERT INTO " . "\n"
                    . $this->tableList['glossary_definitions'] . "\n"
                    . " SET definition = '" . addslashes( $definition ) . "'" . "\n"
                    ;
                $this->connection->executeQuery( $sql );
                
                if ( $this->connection->hasError() )
                {
                    echo $this->connection->getError();
                    return false;
                }
                
                $defId = $this->connection->getLastInsertId();
            }
            
            $sql = "SELECT id " . "\n"
                . "FROM ".$this->tableList['glossary_word_definitions']." " . "\n"
                . "WHERE definitionId = " . (int) $defId . " " . "\n"
                . "AND wordId = " . (int) $wordId . "\n"
                ;
            
            $sql .= ( is_null( $this->dictionaryId ) ) 
                ? ''
                : " AND dictionaryId = ". (int) $this->dictionaryId
                ; 
                
            $result = $this->connection->getRowFromQuery( $sql );
            
            if ( $this->connection->hasError() )
            {
                return false;
            }
            
            $wdId = isset( $result['id'] )
                ? (int) $result['id']
                : null
                ;
                
            if ( is_null( $wdId ) )
            {
                $sql = "INSERT INTO ".$this->tableList['glossary_word_definitions']." " . "\n"
                    . "SET wordId = ". (int) $wordId . ", " . "\n"
                    . "definitionId = ". (int) $defId . "\n"
                    ;
                    
                $sql .= ( is_null( $this->dictionaryId ) ) 
                    ? ''
                    : ", dictionaryId = ". (int) $this->dictionaryId  . "\n"
                    ;
                    
                $this->connection->executeQuery( $sql );
                
                if ( $this->connection->hasError() )
                {
                    return false;
                }
            }
            
            return $wordId;
        }
        
        /**
         * Update word in dictionary
         * @param   int wordId
         * @param   string newWord
         * @return  boolean
         */
        function modifyWord( $wordId, $newWord )
        {
            $this->connection->connect();
            
            $sql = "UPDATE ".$this->tableList['glossary_words']." " . "\n"
                . "SET name = '". addslashes( $newWord ) . "' " . "\n"
                . "WHERE id = " . (int) $wordId . "\n"
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
        
        /**
         * Delete a word in the dictionary
         * @param   int wordId
         * @return  boolean
         */
        function deleteWord( $wordId )
        {
            $this->connection->connect();
            
            $sql = "DELETE " . "\n"
                . "FROM ".$this->tableList['glossary_word_definitions']." " . "\n"
                . "WHERE wordId = " . (int) $wordId . "\n"
                ;
                
            $sql .= ( is_null( $this->dictionaryId ) ) 
                ? ''
                : " AND dictionaryId = ". (int) $this->dictionaryId . "\n"
                ;
            
            $this->connection->executeQuery( $sql );
            
            if ( $this->connection->hasError() )
            {
                return false;
            }
            
            return $this->garbageCollect();
        }
        
        function garbageCollect()
        {
        	$this->connection->connect();
        	
        	// delete words without definitions
        	$sql = "SELECT W.id "
        		. "FROM " . $this->tableList['glossary_words']." AS W " . "\n"
        		. "LEFT JOIN " . $this->tableList['glossary_word_definitions']." AS WD " . "\n"
        		. "ON W.id = WD.wordId" . "\n"
        		. "WHERE WD.wordId IS NULL" . "\n"
				;
				
			$results = $this->connection->getColumnFromQuery( $sql );
			
			if ( count( $results ) > 0 )
			{
			
				$sql = "DELETE FROM " . $this->tableList['glossary_words']." " . "\n"
					. "WHERE id IN(" . implode(',', $results ) . ") " . "\n"
					;
				
				$this->connection->executeQuery( $sql );
			}
            
            if ( $this->connection->hasError() )
            {
                return false;
            }
            
            // delete definitions without words
            $sql = "SELECT D.id "
        		. "FROM " . $this->tableList['glossary_definitions']." AS D " . "\n"
        		. "LEFT JOIN " . $this->tableList['glossary_word_definitions']." AS WD " . "\n"
        		. "ON D.id = WD.definitionId" . "\n"
        		. "WHERE WD.definitionId IS NULL" . "\n"
				;
				
			$results = $this->connection->getColumnFromQuery( $sql );
			
			if ( count( $results ) > 0 )
			{
			
				$sql = "DELETE FROM " . $this->tableList['glossary_definitions']." " . "\n"
					. "WHERE id IN(" . implode(',', $results ) . ") " . "\n"
					;
				
				$this->connection->executeQuery( $sql );
			}
            
            if ( $this->connection->hasError() )
            {
                return false;
            }
            
            // delete orphan entries : missing definition
            $sql = "SELECT WD.id "
        		. "FROM " . $this->tableList['glossary_word_definitions']." AS WD " . "\n"
        		. "LEFT JOIN " . $this->tableList['glossary_definitions']." AS D " . "\n"
        		. "ON D.id = WD.definitionId" . "\n"
        		. "WHERE D.id IS NULL" . "\n"
				;
				
			$results = $this->connection->getColumnFromQuery( $sql );
			
			if ( count( $results ) > 0 )
			{
			
				$sql = "DELETE FROM " . $this->tableList['glossary_word_definitions']." " . "\n"
					. "WHERE id IN(" . implode(',', $results ) . ") " . "\n"
					;
				
				$this->connection->executeQuery( $sql );
			}
            
            if ( $this->connection->hasError() )
            {
                return false;
            }
            
            // delete orphan entries : missing word
            $sql = "SELECT WD.id "
        		. "FROM " . $this->tableList['glossary_word_definitions']." AS WD " . "\n"
        		. "LEFT JOIN " . $this->tableList['glossary_words']." AS W " . "\n"
        		. "ON W.id = WD.wordId" . "\n"
        		. "WHERE W.id IS NULL" . "\n"
				;
				
			$results = $this->connection->getColumnFromQuery( $sql );
			
			if ( count( $results ) > 0 )
			{
			
				$sql = "DELETE FROM " . $this->tableList['glossary_word_definitions']." " . "\n"
					. "WHERE id IN(" . implode(',', $results ) . ") " . "\n"
					;
				
				$this->connection->executeQuery( $sql );
			}
            
            if ( $this->connection->hasError() )
            {
                return false;
            }
            
            return true;
        }
        
        /**
         * Get definition from id
         * @param   int defId
         * @return  string definition if found, null if not found
         */
        function getDefinition( $defId )
        {
            $this->connection->connect();

            $sql = "SELECT definition " . "\n"
                . "FROM ".$this->tableList['glossary_definitions']." " . "\n"
                . "WHERE id = ". (int) $defId . "\n"
                ;

            $result = $this->connection->getRowFromQuery( $sql );

            if ( ! is_null( $result ) )
            {
                return $result['definition'];
            }
            else
            {
                return NULL;
            }
        }
        
        /**
         * Get definition id from definition text
         * @param   string def definition
         * @return  int definitionId, null if not found
         */
        function getDefinitionId( $def )
        {
            $this->connection->connect();

            $sql = "SELECT id " . "\n"
                . "FROM ".$this->tableList['glossary_definitions']." " . "\n"
                . "WHERE definition = '" . addslashes( $def ) . "'" . "\n"
                ;

            $result = $this->connection->getRowFromQuery( $sql );

            if ( ! is_null( $result ) )
            {
                return (int) $result['id'];
            }
            else
            {
                return NULL;
            }
        }
        
        /**
         * Modify definition in dictionary
         * @param   int defId definition id
         * @param   string newDefinition
         * @return  boolean
         */
        function modifyDefinition( $defId, $newDefinition )
        {
            $this->connection->connect();
            
            $sql = "UPDATE ".$this->tableList['glossary_definitions']." " . "\n"
                . "SET definition = '". addslashes( $newDefinition ) . "' " . "\n"
                . "WHERE id = " . (int) $defId . "\n"
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
        
        /**
         * Delete definition from dictionary
         * @param   int defId definition id
         * @return  boolean
         */
        function deleteDefinition( $defId )
        {
            $this->connection->connect();
            
            $sql = "DELETE " . "\n"
                . "FROM ".$this->tableList['glossary_word_definitions']." " . "\n"
                . "WHERE definitionId = " . (int) $defId . "\n"
                ;
                
            $sql .= ( is_null( $this->dictionaryId ) ) 
                ? ''
                : " AND dictionaryId = ". (int) $this->dictionaryId . "\n"
                ;
            
            $this->connection->executeQuery( $sql );           
            
            if ( $this->connection->hasError() )
            {
                return false;
            }
            
            return $this->garbageCollect();
        }
        
        /**
         * Delete an entry from the dictionary
         * @param   int wordId
         * @param   int defId
         * @return  boolean
         */
        function deleteWordDefinition( $wordId, $defId )
        {
            $this->connection->connect();
            
            // delete the relation line in ditionary
            $sql = "DELETE " . "\n"
                . "FROM ".$this->tableList['glossary_word_definitions']." " . "\n"
                . "WHERE definitionId = " . (int) $defId . " " . "\n"
                . "AND wordId = " . (int) $wordId . "\n"
                ;
                
            $sql .= ( is_null( $this->dictionaryId ) ) 
                ? ''
                : " AND dictionaryId = ". (int) $this->dictionaryId . "\n"
                ;
                
            $this->connection->executeQuery( $sql );           
            
            if ( $this->connection->hasError() )
            {
                return false;
            }
            
            return $this->garbageCollect();
        }
        
        /**
         * Get id list of the given word
         * @param   string wordName
         * @return  array
         */
        function getWordIdList( $wordName )
        {
            $this->connection->connect();
            
            $sql = "SELECT id " . "\n"
                . "FROM " . $this->tableList['glossary_words'] . " " . "\n"
                . "WHERE name = '" . $wordName . "'" . "\n"
                ;
                
            $result = $this->connection->getAllRowsFromQuery( $sql );
            
            return $result;
        }
        
        /**
         * Get word id
         * @param string wordName
         * @return int word id, null if not found
         */
        function getWordId( $wordName )
        {
            $this->connection->connect();
            
            $sql = "SELECT id " . "\n"
                . "FROM " . $this->tableList['glossary_words'] . " " . "\n"
                . "WHERE name = '" . $wordName . "'" . "\n"
                ;
                
            $result = $this->connection->getRowFromQuery( $sql );
            
            if ( ! empty( $result ) )
            {
                return (int) $result['id'];
            }
            else
            {
                return null;
            }
        }
        
        /**
         * Get definition list for the given word id
         * @param   int wordId
         * @return array (name, definition, wordId, definitionId)
         */
        function getDefinitionList( $wordId )
        {
            $this->connection->connect();
                
            $and = ( is_null( $this->dictionaryId ) ) 
                ? ''
                : " AND dictionaryId = ". (int) $this->dictionaryId . " " . "\n"
                ;
                
            $sql = "SELECT W.name, D.definition, W.id AS wordId, D.id AS definitionId " . "\n"
                . "FROM ". $this->tableList['glossary_word_definitions']." AS WD " . "\n"
                . "INNER JOIN ". $this->tableList['glossary_words']." AS W " . "\n"
                . "ON W.id = WD.wordId " . "\n"
                . "INNER JOIN ". $this->tableList['glossary_definitions']." AS D " . "\n"
                . "ON D.id = WD.definitionId " . "\n"
                . "WHERE WD.wordId = " . (int) $wordId . " " . "\n"
                . $and . "\n"
                . "ORDER BY UPPER(D.definition) ASC" . "\n"
                ;
    
            $result = $this->connection->getAllRowsFromQuery( $sql );
            
            return $result;
        }
        
        /**
         * Get list of all words
         * @return  array id name
         */
        function getWordList()
        {
            $this->connection->connect();
            
            $sql = "SELECT id, name " . "\n"
                . "FROM " . $this->tableList['glossary_words']." " . "\n"
                . "ORDER BY LENGTH(name) DESC" . "\n"
                ;
                
            $result = $this->connection->getAllRowsFromQuery( $sql );
            
            return $result;
        }
        
        /**
         * Add a list of synonyms to a definition
         * @param   int defId
         * @param   array synonymList
         * @param   string definition
         * @return  boolean
         */
        function addSynonymList( $defId, $synonymList, $definition )
        {
            if ( is_array( $synonymList ) && count( $synonymList ) > 0 )
            {
                $this->connection->connect();
                
                $tmp = $this->getSynonymList( $defId );
                $actualSynList = array();
                
                foreach ( $tmp as $entry )
                {
                    $actualSynList[] = $entry;
                }
                
                $synList = array();
                
                foreach ( $synonymList as $id => $syn )
                {
                    $syn = trim( $syn );
                    
                    if ( !empty( $syn ) )
                    {
                        $synList[$id] = $syn;
                    }
                }
                
                $synonymList = array_unique( $synList );
                
                $toAdd = array_diff( $synonymList, $actualSynList );
                $toDelete = array_diff( $actualSynList, $synonymList );
                
                if ( !empty( $toAdd ) )
                {
                    foreach ( $toAdd as $word )
                    {
                        $this->addWord( $word, $definition );
                    }
                }
                
                if ( !empty( $toDelete ) )
                {
                    foreach ( $toDelete as $word )
                    {
                        $wordId = $this->getWordId( $word );
                        $this->deleteWordDefinition( $wordId, $defId );
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
         * Get list of synonyms for the given definition id
         * @param   int defId
         * @return  array synonymList, null on error
         */
        function getSynonymList( $defId )
        {
            if ( $defId > 0 )
            {
                $this->connection->connect();
                
                $sql = "SELECT WD.wordId " . "\n"
                    . "FROM ".$this->tableList['glossary_word_definitions']." AS WD " . "\n"
                    . "WHERE WD.definitionId = " . (int) $defId . "\n"
                    ;
                    
                $sql .= ( is_null( $this->dictionaryId ) ) 
                    ? ''
                    : " AND dictionaryId = ". (int) $this->dictionaryId . "\n"
                    ;
                    
                $result = $this->connection->getAllRowsFromQuery( $sql );
                
                if ( is_array( $result ) )
                {
                    $wordList = array();
                    
                    foreach ( $result as $row )
                    {
                        $wordList[] = $row['wordId'];
                    }
                    
                    $result = array();
                    
                    if ( is_array( $wordList ) && !empty( $wordList ) )
                    {
                        $sql = "SELECT W.name " . "\n"
                            . "FROM ".$this->tableList['glossary_words']." AS W " . "\n"
                            . "WHERE W.id " . "\n"
                            . "IN " . '(' . implode(',', $wordList ) . ')' . "\n"
                            . " ORDER BY UPPER(W.name) ASC" . "\n"
                            ;               
                        
                        $result = $this->connection->getColumnFromQuery( $sql );
                    }
                    
                    return $result;
                }
                else
                {
                    return null;
                }
            }
            else
            {
                return null;
            }
        }
        
        /**
         * Export dictionary as an array
         * @return  array
         */
        function export()
        {
            $dict = $this->getDictionary();
            $export = array();
            
            foreach ( $dict as $word )
            {
                $export[] = array( $word['name'], $word['definition'] );
            }
            
            return $export;
        }
        
        /**
         * Import dictionary as an array
         * @param   array dictionary contents
         */
        function import( $arrayDict )
        {
            foreach ( $arrayDict as $word )
            {
                $this->addWord( $word[0], $word[1] );
            }
        }
    }
?>