<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:

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
     * @package NETQUIZ
     */

if( count( get_included_files() ) == 1 )
{
    die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead');
}

// Vérification que l'utilisateur soit enregistré
if($is_allowedToAdmin == true) 
{

    $iIDQuiz = $_GET["id"];
    $sStatut = ( $_GET["status"] == '0' ? '1' : '0' );

	// Declaration de la Class netquiz		
	$netquiz = new netquiz();
	
    // Class netquiz : update du status des questions
	$netquiz->setIdQuiz( $iIDQuiz );
    $netquiz->setActif( $sStatut );

	if ( !$netquiz->updateQuizsStatus() )
	{
        $error_message .= '<li>'.get_lang("Visibility is not updated").'</li>';
	}
	else
    {
        $confirm = '<li>'.get_lang("Visibility is updated !").'</li>';
    }

}
?>