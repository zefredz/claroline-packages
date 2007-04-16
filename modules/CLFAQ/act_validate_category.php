<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:

	// protect file
	if( count( get_included_files() ) == 1 )
	{
		die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead');
	}

	if($is_allowedToAdmin == false) 
	{
		claro_die( get_lang('Not allowed action !') );
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

foreach($_POST as $key=>$val) {
	$_POST[$key] = trim($val);
	if(!get_magic_quotes_gpc()) {
		$_POST[$key] = addslashes($val);
	}
}

$error = 0;
$error_message = "";
$frm_id = $_POST['frm_id'];
$form_action = $_POST['form_action'];

// Validation du champ Categorie : le champ doit �tre rempli
if(fct_isempty($_POST['frm_category'])) {
	$error = 1;
	$error_message .= "<li>".get_lang("Vous n'avez pas mentionn� la Cat�gorie !")."</li>";
}
else
{
	$frm_category = $_POST['frm_category'];
}

// Validation du champ Categorie : le champ ne doit pas contenir plus de 100 caract�res
if(fct_check_length($_POST['frm_category'],100)) {
	$error = 1;
	$error_message .= "<li>".get_lang("La Cat�gorie contient plus de 100 caract�res !")."</li>";
}
else
{
	$frm_category = $_POST['frm_category'];
}

// Validation du champ Description : le champ doit �tre rempli
if(fct_isempty($_POST['frm_description'])) {
	$error = 1;
	$error_message .= "<li>".get_lang("Vous n'avez pas mentionn� la Description !")."</li>";
}
else
{
	$frm_description = $_POST['frm_description'];
}

// Validation du champ Description : le champ ne doit pas contenir plus de 100 caract�res
if(fct_check_length($_POST['frm_description'],500)) {
	$error = 1;
	$error_message .= "<li>".get_lang("La R�ponse contient plus de 500 Description !")."</li>";
}
else
{
	$frm_description = $_POST['frm_description'];
}

if($error != 1) {
		
	if($form_action == "add") {	
		
		$createCategoty = new Category();
		$createCategoty->setCategory($frm_category);
		$createCategoty->setDescription($frm_description);
		if ( $createCategoty->create() )
		{
		
			$confirm = "<li>".get_lang("La Cat�gorie a correctement �t� ajout�e !")."</li>";
		}
		else
		{
			$confirm = "<li>".get_lang("La Cat�gorie n'a pas correctement �t� ajout�e !")."</li>";
		}
		
	} 
	elseif($form_action == "edit") 
	{
		
		$updateCategoty = new Category();
		$updateCategoty->setId($frm_id);
		$updateCategoty->setCategory($frm_category);
		$updateCategoty->setDescription($frm_description);
		if ( $updateCategoty->update() )
		{
		
			$confirm = "<li>".get_lang("La Cat�gorie a correctement �t� �dit�e !")."</li>";
		}
		else
		{
			$confirm = "<li>".get_lang("La Cat�gorie n'a pas correctement �t� �dit�e !")."</li>";
		}
		
	}
	else
	{
		claro_die( get_lang('Not allowed action !') );
	}
	
} else {
	# En cas d'�chec de la validation, on d�prot�ge les valeurs en vue de les r�afficher dans le formulaire
	foreach($_POST as $key=>$val) {
		$_POST[$key] = stripslashes($val);
	}
}
?>