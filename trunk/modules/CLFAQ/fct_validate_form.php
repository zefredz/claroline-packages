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

// 
/**
 * Fonction qui détermine si la valeur passée est oui ou non une chaîne vide
 * @ return boolean true si la chaîne est vide
 */ 
function fct_isempty( $str )
{
    if (! strlen ( trim ( $str ) ))
    {
        return true ;
    } 
    else
    {
        return false ;
    }
}

/**
 * Fonction qui détermine si la valeur passée $str correspond à un chaîne ne dépassant pas $nbr caractères
 * 
 * @return boolean true si la chaîne contient plus de $nbr caractères
 * 
 */
function fct_check_length( $str, $nbr )
{
    if (strlen ( trim ( $str ) ) >= $nbr)
    {
        return true ;
    } 
    else
    {
        return false ;
    }
}

/**
 * Fonction qui détermine si la valeur passée $str correspond à une chaîne ne dépassant pas $nbr caractères
 *
 * @return boolean true si la chaîne contient plus de $nbr caractères
 */
function fct_check_length_min( $str, $nbr )
{
    if (strlen ( trim ( $str ) ) <= $nbr)
    {
        return true ;
    } 
    else
    {
        return false ;
    }
}

/**
 * Fonction qui détermine si la valeur passée correspond ou non à une date valide
 * 
 * @return boolean true si la valeur correspond à une date valide
 * 
 */
function fct_isdate( $str )
{
    if (strlen ( $str ) == 10)
    {
        # On isole les différentes composantes de la date
        $day = substr ( $str, 0, 2 ) ;
        $month = substr ( $str, 3, 2 ) ;
        $year = substr ( $str, 6, 4 ) ;
        # On teste si ces différentes composantes permettent de composer une date valide
        if (! @checkdate ( $month, $day, $year ))
        {
            return false ;
        }
    } 
    else
    {
        return false ;
    }
    return true ;
}

/**
 * Fonction qui détermine si la date $date1 est inférieur ou non à la date $date2
 * 
 * @param $date1 string date au format dd/mm/yyyy
 * @param $date2 string date au format dd/mm/yyyy
 * @return boolean true si $date1 est strictement inférieure à $date2
 *  
 */
function fct_checkdates( $date1, $date2 )
{
    # Conversion de $date1 en timestamp
    $day1 = substr ( $date1, 0, 2 ) ;
    $month1 = substr ( $date1, 3, 2 ) ;
    $year1 = substr ( $date1, 6, 4 ) ;
    
    $date1 = mktime ( 0, 0, 0, $month1, $day1, $year1 ) ;
    
    # Conversion de $date2 en timestamp
    $day2 = substr ( $date2, 0, 2 ) ;
    $month2 = substr ( $date2, 3, 2 ) ;
    $year2 = substr ( $date2, 6, 4 ) ;
    
    $date2 = mktime ( 0, 0, 0, $month2, $day2, $year2 ) ;
    
    if ($date1 < $date2)
    {
        return true ;
    } 
    else
    {
        return false ;
    }
}

/**
 * Fonction qui prépare une date valide au format dd/mm/yyyy pour son insertion dans la DB
 * @return string une date au format yyyymmdd
 */
function fct_reversedate( $date )
{
    $day = substr ( $date, 0, 2 ) ;
    $month = substr ( $date, 3, 2 ) ;
    $year = substr ( $date, 6, 4 ) ;
    
    return $year . $month . $day ;
}

/**
 * Fonction qui détermine si la valeur passée correspond ou non à une adresse email valide
 * 
 * @param string email to check
 * 
 * @return boolean true si la valeur est reconnue comme une adresse email valide
 */
function fct_isemail( $str )
{
    if (! fct_isempty ( $str ))
    { # On vérifie tout d'abord s'il ne s'agit pas d'une chaîne vide
      # L'adresse doit contenir un @ et un . suivit de 2 à 3 caractères
        if (! eregi ( "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $str ))
        {
            return false ;
        }
    } 
    else
    {
        return false ;
    }
    return true ;
}

/**
 * Fonction qui détermine si la valeur passée correspond ou non à une adresse url valide
 * 
 * @param string url to check
 * 
 * @return si la valeur est reconnue comme une adresse url valide
 */
function fct_isurl( $str )
{
    if (! fct_isempty ( $str ))
    { # On vérifie tout d'abord s'il ne s'agit pas d'une chaîne vide
        # L'adresse doit contenir un http:// ou https:// et un . suivit de 2 à 3 caractères
        if (! eregi ( "^(http://|https://|http://www|https://www){0,1}[A-Za-z0-9][A-Za-z0-9\-\.]+[A-Za-z0-9]\.[A-Za-z]{2,}[\43-\176]*$", $str ))
        {
            return false ;
        }
    } 
    else
    {
        return false ;
    }
    return true ;
}

/**
 * Fonction qui détermine si la valeur passée correspond à un nombre valide
 * 
 * @param mixed value to check
 * 
 * @return 0 si la valeur ne correspond pas à un nombre valide
 * @return 1 si la valeur correspond à un nombre valide
 * @return 2 si la valeur correspond à un nombre entier
 */
function fct_isnumber( $nbre )
{
    if (is_numeric ( $nbre ))
    {
        if (round ( $nbre ) == $nbre)
        {
            return 2 ; // Il s'agit d'un nombre entier
        } 
        else
        {
            return 1 ; // Il s'agit d'un nombre valide
        }
    } 
    else
    {
        return 0 ; // Il ne s'agit pas d'un nombre valide
    }
}

?>