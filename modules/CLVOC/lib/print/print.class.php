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