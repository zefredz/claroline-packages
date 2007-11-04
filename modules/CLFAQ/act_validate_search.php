<?php // $Id$
// vim: expandtab sw=4 ts=4 sts=4:
// protect file
if (count ( get_included_files () ) == 1)
{
    die ( 'The file ' . basename ( __FILE__ ) . ' cannot be accessed directly, use include instead' ) ;
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

foreach ( $_POST as $key => $val )
{
    $_POST [ $key ] = trim ( $val ) ;
    if (! get_magic_quotes_gpc ())
    {
        $_POST [ $key ] = addslashes ( $val ) ;
    }
}

$error = 0 ;
$error_message = '' ;
$form_dest = $_REQUEST [ 'form_dest' ] ;

// Validation du champ Categorie : le champ doit être rempli
if (fct_isempty ( $_POST [ 'frm_search' ] ))
{
    $error = 1 ;
    $error_message .= "<li>" . get_lang ( "Vous n'avez pas mentionné la Recherche !" ) . "</li>" ;
} else
{
    $frm_search = $_POST [ 'frm_search' ] ;
}

// Validation du champ Categorie : le champ ne doit pas contenir plus de 100 caractères
if (fct_check_length ( $_POST [ 'frm_search' ], 100 ))
{
    $error = 1 ;
    $error_message .= "<li>" . get_lang ( "La Recherche contient plus de 100 caractères !" ) . "</li>" ;
} else
{
    $frm_search = $_POST [ 'frm_search' ] ;
}

if ($error == 1)
{
    
    # En cas d'échec de la validation, on déprotège les valeurs en vue de les réafficher dans le formulaire
    foreach ( $_POST as $key => $val )
    {
        $_POST [ $key ] = stripslashes ( $val ) ;
    }
}
?>