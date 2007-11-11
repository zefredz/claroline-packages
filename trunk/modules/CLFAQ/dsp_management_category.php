<?php // $Id$

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

$showEmptyCategories = true === $is_allowedToAdmin ? true : false ;

$category = new category ( ) ;

if (! ($categoryList = $category->getCategoryList ( $showEmptyCategories )))
{
    $errorMsg = (get_lang ( 'Invalid data !' )) ;
}

// Breadcrumps

$interbredcrump [] = array ( 'url' => 'index.php' , 'name' => get_lang ( 'F.A.Q' ) ) ;
$interbredcrump [] = array ( 'url' => NULL , 'name' => get_lang ( 'Management category' ) ) ;

// --------- Claroline header and banner ----------------    

require_once get_path ( 'incRepositorySys' ) . "/claro_init_header.inc.php" ;

// --------- Claroline body ----------------    

// toolTitle
$output->append ( claro_html_tool_title ( get_lang ( 'F.A.Q' ) ) . "\n" ) ;

if (isset ( $errorMsg ))
{
    $output->append ( '<ul class="error">' ) ;
    $output->append ( $errorMsg ) ;
    $output->append ( '</ul>' ) ;
}

if (isset ( $confirm ))
{
    $output->append ( '<ul class="confirm">' ) ;
    $output->append ( $confirm ) ;
    $output->append ( '</ul>' ) ;
}

$output->append ( '<p><a class="claroCmd" href="' . $_SERVER [ 'PHP_SELF' ] . '?fuseaction=create_category"><img src="' . get_icon ( "info" ) . '" alt="' . get_lang ( "info" ) . '" title="' . get_lang ( "info" ) . '" /> ' . get_lang ( 'Create category' ) . '</a></p>' ) ;

$output->append ( '
	<table class="claroTable emphaseLine management" summary="' . get_lang ( 'Management category' ) . '">
		<thead>
			<tr class="headerX">
				<th>' . get_lang ( "Category" ) . '</th>
				<th>' . get_lang ( "Description" ) . '</th>
				<th class="col_static">' . get_lang ( "Edit" ) . '</th>
				<th class="col_static">' . get_lang ( "Delete" ) . '</th>
				<th class="col_static">' . get_lang ( "Visibility" ) . '</th>
			</tr>
		</thead>	
		<tbody>
	' ) ;

foreach ( $categoryList as $key => $val )
{
    
    $output->append ( '
		<tr>
			<td class="category">' . $val [ 'clfaq_category' ] . ' (' . $val [ 'totalrows' ] . ')</td>
			<td>' . $val [ 'clfaq_category_description' ] . '</td>
			<td class="center">
				<a href="' . $_SERVER [ 'PHP_SELF' ] . '?fuseaction=edit_category&amp;clfaq_category_id=' . $val [ 'clfaq_category_id' ] . '"><img src="' . get_icon ( "edit" ) . '" alt="' . get_lang ( "edit" ) . '" title="' . get_lang ( "edit" ) . '" /></a>
			</td>
			<td class="center">
				<a href="' . $_SERVER [ 'PHP_SELF' ] . '?fuseaction=delete_category&amp;id=' . $val [ 'clfaq_category_id' ] . '" onclick="js_fct_confirm_delete_category(' . $val [ 'clfaq_category_id' ] . '); return false;"><img src="' . get_icon ( "delete" ) . '" alt="' . get_lang ( "delete" ) . '" title="' . get_lang ( "delete" ) . '" /></a>
			</td>
			<td class="center">
				<a href=""><img src="' . get_icon ( "visible" ) . '" alt="' . get_lang ( "Visibility" ) . '" title="' . get_lang ( "Visibility" ) . '" /></a>
			</td>
		</tr>
		 ' ) ;

}

$output->append ( '
		<tbody>
	</table>
	
	' ) ;

// print display

echo $output->getContents () ;

// ------------ Claroline footer ---------------

require_once get_path ( 'incRepositorySys' ) . '/claro_init_footer.inc.php' ;
// vim: expandtab sw=4 ts=4 sts=4:

?>