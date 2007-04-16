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

$fuseaction = isset($_GET['fuseaction']) ? $_GET['fuseaction'] : 'default';

$id = isset($_GET['id']) ? $_GET['id'] : '';

switch($fuseaction) {

	case "delete_faq":
		$form_xfa_post = 'delete_faq&amp;id='.$id.'&amp;confirm=true';
		$form_xfa_cancel = 'index.php';
		$msg = "<li>".get_lang("Etes-vous certain de vouloir supprimer définitivement cette F.A.Q ?")."</li>";
		break;
		
	case "delete_category":
		$form_xfa_post = 'delete_category&amp;id='.$id.'&amp;confirm=true';
		$form_xfa_cancel = 'index.php?fuseaction=management_category';
		$msg = "<li>".get_lang("Etes-vous certain de vouloir supprimer définitivement cette catégorie ?")."</li>";
		break;

	default:
		// Protection : Si $fuseaction est different de 'delete_faq' ou de 'delete_category'
		claro_die( get_lang('Not allowed action !') );
		break;
		
}
?>