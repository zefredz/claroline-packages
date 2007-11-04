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
     * @copyright 2001-2007 Universite catholique de Louvain (UCL)
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

$form_xfa_post = 'validate_category';
$form_xfa_cancel = 'index.php?fuseaction=management_category';

# id
$frm_id = isset($_POST['frm_id']) ? $_POST['frm_id'] : '';
		
# Champ categorie
$frm_category = isset($_POST['frm_category']) ? $_POST['frm_category'] : '';

# Champ categorie
$frm_description = isset($_POST['frm_description']) ? $_POST['frm_description'] : '';

# Action
$form_action = isset($_POST['form_action']) ? $_POST['form_action'] : '';


if($fuseaction == 'validate_category')
{

	if($form_action == 'add')
	{
					
		# action
		$form_action = 'add';
			
	}
	elseif($form_action == 'edit')
	{
							
		# action
		$form_action = 'edit';
			
	}
	else
	{
		
		// Protection : Si $form_action est different de 'add' ou de 'edit'
		claro_die( get_lang('Not allowed action !') );
		
	}

}
else
{

	if($fuseaction == 'create_category')
	{
	
		# action
		$form_action = 'add';
		
	}
	elseif($fuseaction == 'edit_category')
	{
		
		$categoryId = $_GET['clfaq_category_id'];
		
		$loadCategory = new Category();
		$loadCategory->setId($categoryId);
		if( !$loadCategory->load() )
		{
			$errorMsg( get_lang('Invalid data !') );
		}
		
		$frm_id = $loadCategory->getId();
		
		$frm_category = htmlentities( $loadCategory->getCategory() );
		
		$frm_description = htmlentities( $loadCategory->getDescription() );
		
		$form_action = 'edit';
		
	}
	
}
?>