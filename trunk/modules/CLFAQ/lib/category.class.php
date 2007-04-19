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
     * @package CLFAQ
     */

// class category
class Category 
{
	
	var $id = null;
	var $category = '';
	var $description = '';
	
	function getId()
	{
    	return $this->id;
	}
	
	function setId($id)
	{
		$this->id = $id;
	}
	
	function getCategory()
	{
		return $this->category;
	}
	
	function setCategory ($category)
	{
		$this->category = $category;
	}
	
	function getDescription()
	{
		return $this->description;
	}
	
	function setDescription ($description)
	{
		$this->description = $description;
	}

	function _setProperties( $data )
	{
		
		if((array_key_exists('clfaq_category_id',$data)) && (is_numeric($data['clfaq_category_id'])))
		{
			$this->setId( $data['clfaq_category_id'] );
		}
		else
		{
			$data['clfaq_category_id'] = null;
		}
		
		if((array_key_exists('clfaq_category',$data)) && (is_string($data['clfaq_category'])))
		{
			$this->setCategory( $data['clfaq_category'] );
		}
		else
		{
			$data['clfaq_category'] = '';
		}
		
		if((array_key_exists('clfaq_category_description',$data)) && (is_string($data['clfaq_category_description'])))
		{
		$this->setDescription( $data['clfaq_category_description'] );
		}
		else
		{
			$data['clfaq_category_description'] = '';
		}
		
		return true;
		
	}	
	
	function create()
	{
		if ( is_null( $this->getId()) )
		{
			// Nom de/des DB
			$tblNameList = array(
				'clfaq_category'
			);
			
			$faqTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
			
			$sql = "INSERT INTO `".$faqTables['clfaq_category']."` (clfaq_category, clfaq_category_description) VALUES ('".$this->getCategory()."','".$this->getDescription()."');";
			if ( claro_sql_query($sql) )
			{
				// last insert id
				$id = claro_sql_insert_id();
				$this->setId($id);
				
				return true;
			}
			else
			{
				return claro_failure::set_failure('CATEGORY_CREATION_FAILED');
			}
		}
		else
		{
			return claro_failure::set_failure('CATEGORY_CREATION_FAILED');
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
					'clfaq_category'
				);
				
				$faqTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
					
				$sql = "SELECT clfaq_category_id, clfaq_category, clfaq_category_description FROM `".$faqTables['clfaq_category']."` WHERE clfaq_category_id = '".$this->getId()."'";
				if ( false !== ($result = claro_sql_query_get_single_row($sql)) )
				{
					return $this->_setProperties($result);
				}
				else
				{				
					return claro_failure::set_failure('CATEGORY_LOADED_FAILED');
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
				'clfaq_category'
			);
			
			$faqTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
			
			$sql = "UPDATE `".$faqTables['clfaq_category']."` SET clfaq_category = '".$this->getCategory()."', clfaq_category_description = '".$this->getDescription()."' WHERE clfaq_category_id = '".$this->getId()."'";			
			
			if ( claro_sql_query($sql) )
			{
				return true;
			}
			else
			{
				
				return claro_failure::set_failure('CATEGORY_UPDATED_FAILED');
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
				'clfaq_category'
			);
			
			$faqTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
			
			$sql = "DELETE FROM `".$faqTables['clfaq_category']."` WHERE clfaq_category_id=".$this->getId()."";

			if ( claro_sql_query($sql) )
			{
				return true;
			}
			else
			{
				
				return claro_failure::set_failure('CATEGORY_REMOVED_FAILED');
			}
		}
	}
	
	function getCategoryCountList()
	{

		// Nom de/des DB
		$tblNameList = array(
			'clfaq',
			'clfaq_category'
		);
		
		$faqTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
	
		$sql = "SELECT clfaq_category_id, clfaq_category, count(clfaq_id_category) AS totalrows FROM `".$faqTables['clfaq']."` INNER JOIN `".$faqTables['clfaq_category']."` ON clfaq_id_category = clfaq_category_id GROUP BY clfaq_id_category ORDER BY clfaq_category ASC";
		if ( false !== ($result = claro_sql_query_fetch_all($sql)) )
		{
			return $result;
		}
		else
		{			
			return claro_failure::set_failure('CATEGORYCOUNTLIST_LOADED_FAILED');
		}
		
	}
	
	function getCategoryList($showEmptyCategories = false)
	{
		
		// Nom de/des DB
		$tblNameList = array(
			'clfaq',
			'clfaq_category'
		);
		
		$faqTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		$join = true === $showEmptyCategories ? 'LEFT' : 'INNER';
			
		$sql = "SELECT clfaq_category_id, clfaq_category, clfaq_category_description, count(clfaq_id_category) AS totalrows FROM `".$faqTables['clfaq_category']."` ".$join." JOIN `".$faqTables['clfaq']."` ON clfaq_category_id = clfaq_id_category GROUP BY clfaq_id_category ORDER BY clfaq_category ASC";
		if ( false !== ($result = claro_sql_query_fetch_all($sql)) )
		{
			return $result;
		}
		else
		{			
			return claro_failure::set_failure('CATEGORYLIST_LOADED_FAILED');
		}
		
	}
	
	function getQuestionList()
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
			
			$sql = "SELECT clfaq_id, clfaq_question FROM `".$faqTables['clfaq']."` WHERE clfaq_id_category = '".$this->getId()."'";
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
	
	function getCategoryCount()
	{
		
		// Nom de/des DB
		$tblNameList = array(
			'clfaq_category'
		);
		
		$faqTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
			
		$sql = "SELECT count(clfaq_category_id) AS totalrows FROM `".$faqTables['clfaq_category']."`";
		if ( false !== ($result = claro_sql_query_get_single_value($sql)) )
		{
			return $result;
		}
		else
		{			
			return claro_failure::set_failure('CATEGORYLIST_LOADED_FAILED');
		}
		
	}
	
}

?>