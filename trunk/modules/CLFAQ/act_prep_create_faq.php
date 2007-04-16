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
     
$form_xfa_post = 'validate_faq';
$form_xfa_cancel = 'index.php';

$categoryId = isset($_GET['categoryId']) ? $_GET['categoryId'] : "";
$fuse = isset($_GET['fuse']) ? $_GET['fuse'] : "";

# id
$frm_id = isset($_POST['frm_id']) ? $_POST['frm_id'] : "";

# Champ catégorie
$frm_category = isset($_POST['frm_category']) ? $_POST['frm_category'] : "";

# Champ question
$frm_question = isset($_POST['frm_question']) ? $_POST['frm_question'] : "";
		
# Champ reponse
$frm_answer = isset($_POST['frm_answer']) ? $_POST['frm_answer'] : '';

# Action
$form_action = isset($_POST['form_action']) ? $_POST['form_action'] : '';


if($fuseaction == 'validate_faq')
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

	if($fuseaction == 'create_faq')
	{
	
		# action
		$form_action = 'add';
		
	}
	elseif($fuseaction == 'edit_faq')
	{
		
		$Id = $_GET['clfaq_id'];
		
		$loadFaq = new Faq();
		$loadFaq->setId($Id);
		if( !$loadFaq->load() )
		{
			$errorMsg( get_lang('Invalid data !') );
		}
		
		# id
		$frm_id = $loadFaq->getId();
		
		# id de la categorie
		$frm_category = $loadFaq->getCategoryId();
		
		# Champ question
		$frm_question = htmlentities( $loadFaq->getQuestion() );
		
		# Champ reponse
		$frm_answer = htmlentities( $loadFaq->getAnswer() );
		
		# Action
		$form_action = 'edit';
		
	}
	
}
?>