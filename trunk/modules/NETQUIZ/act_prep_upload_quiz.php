<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:

    /**
     * CLAROLINE
     *
     * @version 1.9 $Revision: 159 $
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
     * @package NETQUIZ
     */

if( count( get_included_files() ) == 1 )
{
    die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead');
}

if($is_allowedToAdmin == false) 
{
    claro_die( get_lang('Not allowed action !') );
}

$sTypeLabel = array();
$sTypeLabel[0]["choix"] = "Choix multiples";
$sTypeLabel[0]["reponses"] = "R&eacute;ponses multiples";
$sTypeLabel[0]["vraifaux"] = "Vrai ou faux";
$sTypeLabel[1] = "R&eacute;ponse br&egrave;ve";
$sTypeLabel[2] = "Texte lacunaire";
$sTypeLabel[3] = "Dict&eacute;e";
$sTypeLabel[4] = "D&eacute;veloppement";
$sTypeLabel[5] = "Mise en ordre";
$sTypeLabel[6] = "Association";
$sTypeLabel[7] = "Damier";
$sTypeLabel[8] = "Zone &agrave; identifier";				

$form_xfa_post = 'validate_install_quiz';
$form_xfa_cancel = 'index.php';

?>