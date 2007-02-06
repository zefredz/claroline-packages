<?php  // $Id$
    
    // vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:
    
    if ( count( get_included_files() ) == 1 ) die( '---' );
	
	/**
     * Tag Management and Display Classes
     * @author  Frederic Minne <zefredz@claroline.net>
     * @copyright Copyright &copy; 2006, Frederic Minne
     * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @version 1.0
     * @package Glossary
     */
    
    class Glossary_Tag
    {
        var $connection;
        var $tableList;
        
        /**
         * Constructor
         * @param   DatabaseConnection connection
         * @param   array tableList database table list
         */
        function Glossary_Tag( &$connection, $tableList )
        {
            $this->connection =& $connection;
            $this->tableList = $tableList;
        }
        
        /**
         * Add a new tag to tag collection
         * @param   string tag
         * @param   string description (optional)
         * @return  int tag id, false on failure
         */
        function addTag( $tag, $description = '' )
        {
            $this->connection->connect();
            
            // protect against duplicate tag !!!!
            if ( $this->tagExists( $tag ) )
            {
                return false;
            }
            
            $sql = "INSERT INTO " . $this->tableList['glossary_tags'] . " \n"
                . "SET name = '" .addslashes($tag). "', \n"
                . "description = '" .addslashes($description). "'"
                ;
                
            $this->connection->executeQuery( $sql );
            
            if ( ! $this->connection->hasError() )
            {
                return $this->connection->getLastInsertId();
            }
            else
            {
                return false;
            }
        }
        
        /**
         * Delete the given tag from the collection
         * @param int tagId
         * @return boolean
         */
        function deleteTag( $tagId )
        {
            $this->connection->connect();
            
            $sql = "DELETE FROM " . $this->tableList['glossary_tags'] . " \n"
                . "WHERE id = " . (int) $tagId
                ;
                
            $this->connection->executeQuery( $sql );
            
            if ( $this->connection->hasError() )
            {
                return false;
            }
            else
            {
                $sql = "DELETE FROM " . $this->tableList['glossary_tags_entries'] . " \n"
                    . "WHERE id = " . (int) $tagId
                    ;
                
                $this->connection->executeQuery( $sql );
                
                return ( ! $this->connection->hasError() );
            }
        }
        
        /**
         * Update the given tag in the collection
         * @param   int tagId
         * @param   string tag
         * @param   string description (optional)
         * @return  boolean
         */
        function updateTag( $tagId, $tag, $description = '' )
        {
            $this->connection->connect();
            
            $sql = "UPDATE " . $this->tableList['glossary_tags'] . " \n"
                . "SET name = '" .addslashes($tag). "', \n"
                . "description = '" .addslashes($description). "' \n"
                . "WHERE id = " . (int) $tagId
                ;
                
            $this->connection->executeQuery( $sql );
            
            return ( ! $this->connection->hasError() );
        }
        
        /**
         * Add the given tag to the given item
         * @param   int itemId
         * @param   int tagId
         * @return  boolean
         */
        function addTagToItem( $itemId, $tagId )
        { 
            $this->connection->connect();
            
            $sql = "INSERT INTO " . $this->tableList['glossary_tags_entries'] . " \n"
                . "SET tagId = " .(int) $tagId. ", \n"
                . "entryId = " . (int) $itemId
                ;
                
            $this->connection->executeQuery( $sql );
            
            return ( ! $this->connection->hasError() );
        }
        
        /**
         * Remove the given tag from the given item
         * @param   int itemId
         * @param   int tagId
         * @return  boolean
         */
        function deleteTagForItem( $itemId, $tagId )
        {
            $this->connection->connect();
            
            $sql = "DELETE FROM " . $this->tableList['glossary_tags_entries'] . " \n"
                . "WHERE tagId = " .(int) $tagId. " \n"
                . "AND entryId = " . (int) $itemId
                ;
                
            $this->connection->executeQuery( $sql );
            
            return ( ! $this->connection->hasError() );
        }
        
        /**
         * Get item list matching a given tag
         * @param   string tag
         * @return  array item ids (or names ?)
         * @todo    TODO implement
         */
        function getItemListByTag( $tag )
        {
            // not implemented yet
            
            return array();
        }
        
        /**
         * Get item list matching a given tag
         * @param   string tag
         * @return  array item ids (or names ?)
         * @todo    TODO implement
         */
        function getEntryList( $tagId )
        {
            $sql = "SELECT W.name AS word, D.definition AS def, T.tagId AS tagId, \n"
                . "WD.id AS entryId, DC.name as dictName, T.id AS relId "
                . "FROM " . $this->tableList['glossary_tags_entries'] . " AS T\n"
                . "LEFT JOIN " . $this->tableList['glossary_word_definitions'] . " AS WD\n"
                . "ON T.entryId = WD.id "
                . "LEFT JOIN " . $this->tableList['glossary_definitions'] . " AS D\n"
                . "ON D.id = WD.definitionId "
                . "LEFT JOIN " . $this->tableList['glossary_words'] . " AS W\n"
                . "ON W.id = WD.wordId "
                . "LEFT JOIN " . $this->tableList['glossary_dictionaries'] . " AS DC\n"
                . "ON DC.id = WD.dictionaryId "
                . "WHERE T.tagId = " . (int) $tagId
                ;
            
            return $this->connection->getAllRowsFromQuery( $sql );
        }
        
        // TODO move to dictionary.class ?
        function getEntry( $entryId )
        {
            $this->connection->connect();
            
            $sql = "SELECT W.name AS wordName, D.definition as definition \n"
                . "FROM " .$this->tableList['glossary_word_definitions']." AS WD\n"
                . "LEFT JOIN " .$this->tableList['glossary_words']." AS W " . "\n"
                . "ON W.id = WD.wordId \n"
                . "LEFT JOIN " .$this->tableList['glossary_definitions']." AS W " . "\n"
                . "ON D.id = WD.definitionId \n"
                . "WHERE WD.id = ".(int)$entryId
                ;
                
            return $this->connection->getRowFromQuery( $sql );
        }
        
        /**
         * Get the list of tags of the given item
         * @param   int itemId
         * @return  array tag list (id, name)
         */
        function getTagListForItem( $itemId )
        {
            $this->connection->connect();
            
            $sql = "SELECT T.id, T.name "
                . "FROM " . $this->tableList['glossary_tags_entries'] . " AS TE \n"
                . "INNER JOIN " . $this->tableList['glossary_tags'] . " AS T \n"
                . "ON TE.tagId = T.id \n"
                . "WHERE TE.entryId = " . (int) $itemId
                ;
                
            return $this->connection->getAllRowsFromQuery( $sql );
        }
        
        /**
         * Get all difined tags
         * @return array tag list
         */
        function getAllTags()
        {
            $this->connection->connect();
            
            $sql = "SELECT id, name, description "
                . "FROM " . $this->tableList['glossary_tags'] . " \n"
                ;
                
            return $this->connection->getAllRowsFromQuery( $sql );
        }
        
        /**
         * Get id of the given tag
         * @param   string tag
         * @return  int tag id, false if not found
         */
        function getTagId( $tag )
        {
            $this->connection->connect();
            
            $sql = "SELECT id "
                . "FROM " . $this->tableList['glossary_tags'] . " \n"
                . "WHERE name = '" . addslashes($tag ) . "'"
                ;
                    
            return $this->connection->getSingleValueFromQuery( $sql );
        }
        
        /**
         * Check if a tag exists
         * @param   string tag
         * @return  boolean
         */
        function tagExists( $tag )
        {
            $this->connection->connect();
            
            $sql = "SELECT id "
                . "FROM " . $this->tableList['glossary_tags'] . " \n"
                . "WHERE name = '" . addslashes($tag ) . "'"
                ;
                
            return $this->connection->queryReturnsResult( $sql );
        }
        
        /**
         * Check if a tag exists
         * @param   string tag
         * @return  boolean
         */
        function tagIdExists( $tagId )
        {
            $this->connection->connect();
            
            $sql = "SELECT id "
                . "FROM " . $this->tableList['glossary_tags'] . " \n"
                . "WHERE id = " . (int)$tagId
                ;
                
            return $this->connection->queryReturnsResult( $sql );
        }
        
        function getTagsCount()
        {
            $this->connection->connect();
            
            $sql = "SELECT T.name, count(TE.tagId) AS nbr "
                . "FROM " . $this->tableList['glossary_tags_entries'] . " AS TE \n"
                . "LEFT JOIN " . $this->tableList['glossary_tags'] . " AS T \n"
                . "ON TE.tagId = T.id \n"
                . "GROUP BY TE.tagId\n"
                ;
            
            $result = $this->connection->getAllRowsFromQuery( $sql );
            $ret = array();
            
            foreach ( $result as $row )
            {
                $ret[$row['name']] = $row['nbr'];
            }
            
            return $ret;
        }
        
        function getTag( $tagId )
        {
            $this->connection->connect();
            
            $sql = "SELECT id, name, description "
                . "FROM " . $this->tableList['glossary_tags'] . " \n"
                . "WHERE id = " . (int)$tagId
                ;
                
            return $this->connection->getRowFromQuery( $sql );
        }
    }
    
    class Glossary_Tag_Cloud
    {
        var $wordCounts;
        var $ratio;
        var $baseFontSize;
        
        function Glossary_Tag_Cloud( $wordCountList, $baseFontSize = 9 )
        {
            $this->wordcounts = $wordCountList;
            $this->ratio = null;
            $this->baseFontSize = (int) $baseFontSize;
        }
        
        # computing
        
        function computeRatio()
        {
            $min = 1000000;
            $max = -1000000;
            foreach( array_keys( $this->wordcounts ) as $word )
            {
                if ( $this->wordcounts[ $word ] > $max )
                {
                    $max = $this->wordcounts[ $word ];
                }
                if ( $this->wordcounts[ $word ] < $min )
                {
                    $min = $this->wordcounts[ $word ];
                }
            }
            
            $this->ratio = ( $max - $min );
        }
        
        function getWordCount( $word )
        {
            if ( array_key_exists( $word, $this->wordCounts ) )
            {
                return $this->wordCounts[ $word ];
            }
            else
            {
                return 0;
            }
        }
        
        function getWordList()
        {
            return array_keys( $this->wordCounts );
        }
        
        function getRatio( $base )
        {
            if ( is_null( $this->ratio ) )
            {
                $this->computeRatio();
            }
            
            return ( $base / $this->ratio );
        }
        
        function getDefaultCSS()
        {
            $css = ".tagCloudEntry { line-height: ".( ( 2 * $this->baseFontSize ) + 2 )."pt;}"
                ;
            return $css;
        }
        
        function getFontSize( $count, $ratio )
        {
            return (int)( $this->baseFontSize + ( $count * $ratio ) );
        }
        
        function toHTML( $urlGeneratorCallback )
        {
            $this->countWords();
            $ratio = $this->getRatio( (float) ( 2 * $this->baseFontSize ) );
            
            $wc = $this->getWordList();
            sort( $wc );
            $out = '';
            foreach( $wc as $word )
            {
                $url = call_user_func( $urlGeneratorCallback, $word );
                $fs = $this->getFontSize( $this->getWordCount($word), $ratio );
                $out .= '<a class="tagCloudEntry" href="' . $url . '" style="font-size:'. $fs . 'pt;">'
                    . $word . '</a> &nbsp;' . "\n"
                    ;
            }
            
            return $out;
        }
    }
?>