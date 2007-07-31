<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:

	// protect file
	if( count( get_included_files() ) == 1 )
	{
		die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead');
	}

    /**
     * CLAROLINE
     *
     * @version 1.9 $Revision$
     *
     * @copyright 2001-2006 Universite catholique de Louvain (UCL)
     *
     * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
     * This program is under the terms of the GENERAL PUBLIC LICENSE (GPL)
     * as published by the FREE SOFTWARE FOUNDATION. The GPL is available
     * through the world-wide-web at http://www.gnu.org/copyleft/gpl.html
     *
     * @author KOCH Gregory <gregk84@gate71.be>
     *
     * @package CLVOC
     */

	 
	 
	 
	//claro_sql_query_get_single_value => selectionne 1 valeur
	//claro_sql_query_get_single_row => selectionne 1 ligne
	//claro_sql_query_fetch_all_rows => selectionne toutes les lignes
	 
	
	 

class PrintTextGlossary
{
		
	
	function getDictionaryId()
	{
    	return $this->dictionaryId;
	}
	
	function setDictionaryId($dictionaryId)
	{
		$this->dictionaryId = $dictionaryId;
	}	


	function getTextId()
	{
    	return $this->textId;
	}
	
	function setTextId($textId)
	{
		$this->textId = $textId;
	}

	function getWordList()
	{
        return $this->wordList;
	}
	
	function setWordList($wordList)
	{
		$this->wordList = $wordList;
	}	
	
	
	function printWords()
	{

		$tblNameList = array(
			'glossary_words','glossary_word_definitions','glossary_definitions'
		);
				
		$faqTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
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
        . "`".$faqTables['glossary_words']."` AS W " . "\n"
        . "LEFT JOIN " . "\n"
        . "`".$faqTables['glossary_word_definitions']."` AS WD " . "\n"
        . "ON W.id = WD.wordId " . "\n"
        . "LEFT JOIN " . "\n"
        . "`".$faqTables['glossary_definitions']."` AS D " . "\n"
        . "ON WD.definitionId = D.id " . "\n"
        . "WHERE WD.dictionaryId = '1' " . "\n"
        . "AND  " . "\n"
        . "W.name IN ('" . implode("','",$wordList) . "') " . "\n"
        . "ORDER BY wordId ASC " . "\n"
        ;
        
       // = '".$this->getDictionaryId()."' " . "\n"
        
        /*        
		$sql = "SELECT " . "\n"
        . "W.id, " . "\n"
        . "W.name, " . "\n"
        . "WD.id, " . "\n"
        . "WD.dictionaryId, " . "\n"
        . "WD.definitionId, " . "\n"
        . "WD.wordId, " . "\n"
        . "D.id, " . "\n"
        . "D.definition, " . "\n"
        . "Dict.id, " . "\n"
        . "TDict.dictionaryId, " . "\n"
        . "TDict.textId, " . "\n"
        . "T.id, " . "\n"
        . "T.title, " . "\n"
        . "T.content " . "\n"        
        . "FROM " . "\n"
        . "`".$faqTables['glossary_words']."` AS W " . "\n"
        . "INNER JOIN " . "\n"
        . "`".$faqTables['glossary_word_definitions']."` AS WD " . "\n"
        . "ON W.id = WD.wordId " . "\n"
        . "INNER JOIN " . "\n"
        . "`".$faqTables['glossary_definitions']."` AS D " . "\n"
        . "ON WD.definitionId = D.id " . "\n"
        . "INNER JOIN " . "\n"
        . "`".$faqTables['glossary_dictionaries']."` AS Dict " . "\n"
        . "ON WD.dictionaryId = Dict.id " . "\n"
        . "INNER JOIN " . "\n"
        . "`".$faqTables['glossary_text_dictionaries']."` AS TDict " . "\n"
        . "ON Dict.id = TDict.dictionaryId " . "\n"
        . "INNER JOIN " . "\n"
        . "`".$faqTables['glossary_texts']."` AS T " . "\n"
        . "ON TDict.textId = T.id " . "\n"
        . "WHERE WD.dictionaryId = '%".$this->getDictionaryId()."%' " . "\n"
        . "AND  " . "\n"
        ."T.id = '%".$this->getTextId()."%' " . "\n"
        ;
*/
        /*
        $sql = "SELECT id, name, MATCH (name) AGAINST ('".$this->getSearch()."*' IN BOOLEAN MODE) AS score " . "\n"
        . "FROM `".$faqTables['glossary_words']."` " . "\n"
        . "WHERE MATCH (name) " . "\n"
        . "AGAINST ('*".$this->getSearch()."*' IN BOOLEAN MODE) " . "\n"
        ;
        */
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