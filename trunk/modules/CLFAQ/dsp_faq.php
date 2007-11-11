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

$category = new Category ( ) ;
$categoryCount = category::getCategoryCount () ;
$category->setId ( $id ) ;

if (! $category->load ())
{
    $errorMsg = (get_lang ( 'Invalid data !' )) ;
}

if (! ($questionList = $category->getQuestionList ()))
{
    $errorMsg = (get_lang ( 'Invalid data !' )) ;
}

// Breadcrumps
$interbredcrump [] = array ( 'url' => 'index.php' , 'name' => get_lang ( 'F.A.Q' ) ) ;
$interbredcrump [] = array ( 'url' => null , 'name' => $category->getCategory () ) ;

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
    } 
    else
    {
        $output->append ( '| <span class="claroCmdDisabled"><img src="' . get_icon ( "new" ) . '" alt="' . get_lang ( "new" ) . '" title="' . get_lang ( "new" ) . '" /> ' . get_lang ( 'Create F.A.Q.' ) . '</span> |' ) ;
    }
    
    $output->append ( ' <a class="claroCmd" href="' . $_SERVER [ 'PHP_SELF' ] . '?fuseaction=management_category"><img src="' . get_icon ( "info" ) . '" alt="' . get_lang ( "info" ) . '" title="' . get_lang ( "info" ) . '" /> ' . get_lang ( 'Management category' ) . '</a>' ) ;
    $output->append ( '</p></form>' ) ;

} else
{
    
    $output->append ( '</p></form>' ) ;

}

if (isset ( $errorMsg ))
{
    $output->append ( '<ul class="error">' ) ;
    $output->append ( $errorMsg ) ;
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

$output->append ( '<h3>' . $category->getCategory () . '</h3>' ) ;
$output->append ( '<p class="description">' . $category->getDescription () . '</p>' ) ;
$output->append ( '<dl>' ) ;

foreach ( $questionList as $key => $val )
{
    
    $output->append ( '<dt class="question">' ) ;
    
    if ($is_allowedToAdmin == true)
    {
        
        $output->append ( '
                <span class="align_ico">
                <a href="' . $_SERVER [ 'PHP_SELF' ] . '?fuseaction=edit_faq&amp;clfaq_id=' . $val [ 'clfaq_id' ] . '"><img src="' . get_icon ( "edit" ) . '" alt="' . get_lang ( "edit" ) . '" title="' . get_lang ( "edit" ) . '" /></a>
                <a href="' . $_SERVER [ 'PHP_SELF' ] . '?fuseaction=delete_faq&amp;id=' . $val [ 'clfaq_id' ] . '" onclick="js_fct_confirm_delete_faq(' . $val [ 'clfaq_id' ] . '); return false;"><img src="' . get_icon ( "delete" ) . '" alt="' . get_lang ( "delete" ) . '" title="' . get_lang ( "delete" ) . '" /></a>
                </span>
                ' ) ;
    
    }
    ;
    
    $output->append ( '<a href="' . $_SERVER [ 'PHP_SELF' ] . '?fuseaction=faq_content&amp;id=' . $val [ 'clfaq_id' ] . '">' . htmlentities ( $val [ 'clfaq_question' ] ) . '</a>' ) ;
    
    $output->append ( '</dt>' ) ;

}

$output->append ( '</dl>' ) ;

$output->append ( '<p class="align_ico return" ><a href="' . dirname ( $_SERVER [ 'PHP_SELF' ] ) . '/' . $form_xfa_cancel . '"><input type="button" value="' . get_lang ( "Back" ) . '" onclick="document.location=\'' . dirname ( $_SERVER [ 'PHP_SELF' ] ) . '/' . $form_xfa_cancel . '\'" /></a></p>' ) ;
// print display
echo $output->getContents () ;

// ------------ Claroline footer ---------------
require_once get_path ( 'incRepositorySys' ) . '/claro_init_footer.inc.php' ;
// vim: expandtab sw=4 ts=4 sts=4:
?>