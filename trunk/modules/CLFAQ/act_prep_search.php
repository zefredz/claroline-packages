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
     
$form_xfa_post = 'validate_search';
$form_xfa_cancel = 'index.php';
$frm_search = isset($_POST['frm_search']) ? $_POST['frm_search'] : '';

$fuse = isset($_GET['fuseaction']) ? $_GET['fuseaction'] : 'default';
$form_dest = isset($_POST['form_dest']) ? $_POST['form_dest'] : $fuse;

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';

switch($form_dest) {

	case "faq_content":
		$form_dest = $form_dest;
		break;
		
	case "faq":
		$form_dest = $form_dest;
		break;
		
	default:
		$form_dest = 'default';
		break;
		
}

?>