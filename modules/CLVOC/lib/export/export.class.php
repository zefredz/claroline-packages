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
	 
	
	 

class export 
{
		
	
	function getSearch()
	{
    	return $this->search;
	}
	
	function setSearch($search)
	{
		$this->search = $search;
	}	


	
	
	
	function searchText()
	{

		$tblNameList = array(
			'glossary_words','glossary_word_definitions','glossary_definitions'
		);
				
		$faqTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		        
		$sql = "SELECT " . "\n"
        . "W.id, " . "\n"
        . "W.name, " . "\n"
        . "WD.id, " . "\n"
        . "WD.dictionaryId, " . "\n"
        . "WD.definitionId, " . "\n"
        . "WD.wordId, " . "\n"
        . "D.id, " . "\n"
        . "D.definition " . "\n"
        . "FROM " . "\n"
        . "`".$faqTables['glossary_words']."` AS W " . "\n"
        . "INNER JOIN " . "\n"
        . "`".$faqTables['glossary_word_definitions']."` AS WD " . "\n"
        . "ON W.id = WD.wordId " . "\n"
        . "INNER JOIN " . "\n"
        . "`".$faqTables['glossary_definitions']."` AS D " . "\n"
        . "ON WD.definitionId = D.id " . "\n"
        . "WHERE W.name  " . "\n"
        . "LIKE '%".$this->getSearch()."%' " . "\n"
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