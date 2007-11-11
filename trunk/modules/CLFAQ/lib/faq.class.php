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
     * @version 1.9 $Revision: 87 $
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
     * @package CLFAQ
     */

// class faq
class Faq
{
	
	var $id = null;
	var $categoryId = null;
	var $question = '';
	var $answer = '';
	var $search ='';
	
	function getId()
	{
    	return $this->id;
	}
	
	function setId($id)
	{
		$this->id = $id;
	}
	
	function getCategoryId()
	{
		return $this->categoryId;
	}
	
	function setCategoryId($categoryId)
	{
		$this->categoryId = $categoryId;
	}
	
	function getQuestion()
	{
		return $this->question;
	}
	
	function setQuestion ($question)
	{
		$this->question = $question;
	}
	
	function getAnswer()
	{
		return $this->answer;
	}
	
	function setAnswer ($answer)
	{
		$this->answer = $answer;
	}
	
	function _setProperties( $data )
	{
		
		if((array_key_exists('clfaq_id',$data)) && (is_numeric($data['clfaq_id'])))
		{
			$this->setId( $data['clfaq_id'] );
		}
		else
		{
			$data['clfaq_id'] = null;
		}
		
		if((array_key_exists('clfaq_id_category',$data)) && (is_numeric($data['clfaq_id_category'])))
		{
		$this->setCategoryId( $data['clfaq_id_category'] );
		}
		else
		{
			$data['clfaq_id_category'] = null;
		}
		
		if((array_key_exists('clfaq_question',$data)) && (is_string($data['clfaq_question'])))
		{
		$this->setQuestion( $data['clfaq_question'] );
		}
		else
		{
			$data['clfaq_question'] = '';
		}
		
		if((array_key_exists('clfaq_answer',$data)) && (is_string($data['clfaq_answer'])))
		{
		$this->setAnswer( $data['clfaq_answer'] );
		}
		else
		{
			$data['clfaq_answer'] = '';
		}
		
		return true;
	}

	function create()
	{
		if ( is_null( $this->getId()) )
		{
			// Nom de/des DB
			$tblNameList = array(
				'clfaq'
			);
			
			$faqTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
			
			$sql = "INSERT INTO `".$faqTables['clfaq']."` (clfaq_id_category, clfaq_question, clfaq_answer) VALUES ('".$this->getCategoryId()."','".$this->getQuestion()."','".$this->getAnswer()."');";
			if ( claro_sql_query($sql) )
			{
				// last insert id
				$id = claro_sql_insert_id();
				$this->setId($id);
				
				return true;
			}
			else
			{
				return claro_failure::set_failure('FAQ_CREATION_FAILED');
			}
		}
		else
		{
			return claro_failure::set_failure('FAQ_CREATION_FAILED');
		}
		
	}
	
	function load()
	{
		if( is_null($this->getId()) )
		{
			// error
			return false;
		}
		else
		{
			// load from db
			// properties
			// Nom de/des DB
			$tblNameList = array(
				'clfaq'
			);
				
			$faqTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
					
			$sql = "SELECT clfaq_id, clfaq_id_category, clfaq_question, clfaq_answer FROM `".$faqTables['clfaq']."` WHERE clfaq_id = '".$this->getId()."'";
			if ( false !== ($result = claro_sql_query_get_single_row($sql)) )
			{
				return $this->_setProperties($result);
			}
			else
			{				
				return claro_failure::set_failure('FAQ_LOADED_FAILED');
			}
		}
	}
	
	function update()
	{

		if( is_null($this->getId()) )
		{
			// error
			return false;
		}
		else
		{
			//update en db
			// Nom de/des DB
			$tblNameList = array(
				'clfaq'
			);
			
			$faqTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
			
			$sql = "UPDATE `".$faqTables['clfaq']."` SET clfaq_id_category = '".$this->getCategoryId()."', clfaq_question = '".$this->getQuestion()."', clfaq_answer = '".$this->getAnswer()."' WHERE clfaq_id = '".$this->getId()."'";			
			
			if ( claro_sql_query($sql) )
			{
				return true;
			}
			else
			{
				
				return claro_failure::set_failure('FAQ_UPDATED_FAILED');
			}
			
		}
	}
	
	function remove()
	{
		if( is_null($this->getId()) )
		{
			// error
			return false;
		}
		else
		{
			// delete from db
			// update properties (set all properties to default value)
			
			// Nom de/des DB
			$tblNameList = array(
				'clfaq'
			);
			
			$faqTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
			
			$sql = "DELETE FROM `".$faqTables['clfaq']."` WHERE clfaq_id=".$this->getId()."";

			if ( claro_sql_query($sql) )
			{
				return true;
			}
			else
			{
				
				return claro_failure::set_failure('FAQ_REMOVED_FAILED');
			}
		}
	}
	
	function Search($data)
	{

		$tblNameList = array(
			'clfaq'
		);
				
		$faqTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
			
		$sql = "SELECT clfaq_id, clfaq_id_category, clfaq_question, clfaq_answer FROM `".$faqTables['clfaq']."` WHERE clfaq_question LIKE '%".$data."%' OR clfaq_answer LIKE '%".$data."%'";
		if ( false !== ($result = claro_sql_query_fetch_all($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('FAQLIST_LOADED_FAILED');
		}
		
	}			
	
}

?>