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
     
$fuseaction = isset($_GET['fuseaction']) ? $_GET['fuseaction'] : 'default';

$id = $_GET['id'];

switch($fuseaction) {

	case "delete_faq":
		
		$deleteFaq = new Faq();
		$deleteFaq->setId($id);
		if ( $deleteFaq->remove() )
		{
		
			$confirm = "<li>".get_lang("La Faq a été supprimée avec succès !")."</li>";
		}
		else
		{
			$confirm = "<li>".get_lang("La Faq n'a pas été supprimée avec succès !")."</li>";
		}

		break;
		
	case "delete_category":
		
		$deleteCategoty = new Category();
		$deleteCategoty->setId($id);
		if ( $deleteCategoty->remove() )
		{
		
			$confirm = "<li>".get_lang("La Catégorie a été supprimée avec succès !")."</li>";
		}
		else
		{
			$confirm = "<li>".get_lang("La Catégorie n'a pas été supprimée avec succès !")."</li>";
		}

		break;

	default:
		// Protection : Si $fuseaction est different de 'delete_faq' ou de 'delete_category'
		claro_die( get_lang('Not allowed action !') );
		break;
		
}
?>