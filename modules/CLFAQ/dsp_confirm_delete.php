<?php // $Id$
// vim: expandtab sw=4 ts=4 sts=4:

// protect file
if (count ( get_included_files () ) == 1)
{
    die ( 'The file ' . basename ( __FILE__ ) . ' cannot be accessed directly, use include instead' ) ;
}

if ($is_allowedToAdmin == false)
{
    claro_die ( get_lang ( 'Not allowed action !' ) ) ;
}

/**
 * CLAROLINE
 *
 * @version 1.9 $Revision: 344 $
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

// Breadcrumps
$interbredcrump [] = array ( 'url' => 'index.php' , 'name' => get_lang ( 'F.A.Q' ) ) ;
$interbredcrump [] = array ( 'url' => 'index.php?fuseaction=management_category' , 'name' => get_lang ( 'Management category' ) ) ;
$interbredcrump [] = array ( 'url' => NULL , 'name' => get_lang ( 'Delete' ) ) ;

// --------- Claroline header and banner ----------------    
require_once get_path ( 'incRepositorySys' ) . '/claro_init_header.inc.php' ;

// --------- Claroline body ----------------    

// toolTitle
$output->append ( claro_html_tool_title ( get_lang ( 'F.A.Q' ) ) . "\n" ) ;

$output->append ( '
	<form action="' . $_SERVER [ 'PHP_SELF' ] . '?fuseaction=' . $form_xfa_post . '" method="post">
    <input type="hidden" name="claroFormId" value="' . uniqid ( '' ) . '" />
	' ) ;

$output->append ( '<ul class="confirm">' ) ;
$output->append ( $msg ) ;
$output->append ( '</ul>' ) ;

$output->append ( '
		<a href="' . dirname ( $_SERVER [ 'PHP_SELF' ] ) . '/' . $form_xfa_cancel . '"><input class="buttom" type="button" value="' . get_lang ( "Cancel" ) . '" onclick="document.location=\'' . dirname ( $_SERVER [ 'PHP_SELF' ] ) . '/' . $form_xfa_cancel . '' . '\'" /></a>
		<input class="buttom" type="submit" value="' . get_lang ( "Yes" ) . '" />
	</form>
	' ) ;

// print display

echo $output->getContents () ;

// ------------ Claroline footer ---------------
require_once get_path ( 'incRepositorySys' ) . '/claro_init_footer.inc.php' ;
?>