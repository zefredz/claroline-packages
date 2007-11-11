<?php // $Id$
// protect file
if (count ( get_included_files () ) == 1)
{
    die ( 'The file ' . basename ( __FILE__ ) . ' cannot be accessed directly, use include instead' ) ;
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

$categoryCount = category::getCategoryCount () ;

// Breadcrumps
$interbredcrump [] = array ( 'url' => 'index.php' , 'name' => get_lang ( 'F.A.Q' ) ) ;

// --------- Claroline header and banner ----------------    
require_once get_path ( 'incRepositorySys' ) . '/claro_init_header.inc.php' ;

// --------- Claroline body ----------------    
// toolTitle
$output->append ( claro_html_tool_title ( get_lang ( 'F.A.Q' ) ) . "\n" ) ;

// display
$output->append ( '
    
    <form action="' . $_SERVER [ 'PHP_SELF' ] . '?fuseaction=' . $form_xfa_post . '" method="post">
        <p>
            <img src="' . get_icon ( "Search" ) . '" alt="' . get_lang ( "Search" ) . '" title="' . get_lang ( "search" ) . '" />
            <input type="hidden" name="form_dest" value="' . $form_dest . '" />
            <input type="hidden" name="id" value="' . $id . '" />
            <input class="test" name="frm_search" value="' . $frm_search . '" type="text" size="20" />
            <input type="submit" value="' . get_lang ( "Search" ) . '" />
    ' ) ;

if ($is_allowedToAdmin == true)
{
    
    if (0 != $categoryCount)
    {
        $output->append ( '| <a class="claroCmd" href="' . $_SERVER [ 'PHP_SELF' ] . '?fuseaction=create_faq"><img src="' . get_icon ( "new" ) . '" alt="' . get_lang ( "new" ) . '" title="' . get_lang ( "new" ) . '" /> ' . get_lang ( 'Create F.A.Q.' ) . '</a> |' ) ;
    } else
    {
        $output->append ( '| <span class="claroCmdDisabled"><img src="' . get_icon ( "new" ) . '" alt="' . get_lang ( "new" ) . '" title="' . get_lang ( "new" ) . '" /> ' . get_lang ( 'Create F.A.Q.' ) . '</span> |' ) ;
    }
    
    $output->append ( ' <a class="claroCmd" href="' . $_SERVER [ 'PHP_SELF' ] . '?fuseaction=management_category"><img src="' . get_icon ( "info" ) . '" alt="' . get_lang ( "info" ) . '" title="' . get_lang ( "info" ) . '" /> ' . get_lang ( 'Management category' ) . '</a>' ) ;
    $output->append ( '</p></form>' ) ;

} else
{
    
    $output->append ( '</p></form>' ) ;

}

if (isset ( $confirm ))
{
    $output->append ( '<ul class="confirm">' ) ;
    $output->append ( $confirm ) ;
    $output->append ( '</ul>' ) ;
}

if (isset ( $error ))
{
    if ($error != 0)
    {
        $output->append ( '<ul class="error">' ) ;
        $output->append ( $error_message ) ;
        $output->append ( '</ul>' ) ;
    }
}

$output->append ( '<h3>' . get_lang ( 'List category ' ) . '</h3>' ) ;

$output->append ( '<dl>' ) ;

foreach ( $categoryList as $key => $val )
{
    
    if (0 != $val [ 'totalrows' ])
    {
        $output->append ( '<dt><a href="' . $_SERVER [ 'PHP_SELF' ] . '?fuseaction=faq&amp;id=' . $val [ 'clfaq_category_id' ] . '" title="' . $val [ 'clfaq_category' ] . '">' . $val [ 'clfaq_category' ] . ' (' . $val [ 'totalrows' ] . ')</a></dt>' ) ;
    } else
    {
        $output->append ( '<dt><span>' . $val [ 'clfaq_category' ] . ' (' . $val [ 'totalrows' ] . ')</span></dt>' ) ;
    }

}

$output->append ( '</dl>' ) ;

// print display
echo $output->getContents () ;
// ------------ Claroline footer ---------------

require_once get_path ( 'incRepositorySys' ) . '/claro_init_footer.inc.php' ;
// vim: expandtab sw=4 ts=4 sts=4:
?>