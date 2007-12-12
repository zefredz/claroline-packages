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

    // lib
	//require_once "lib/netquiz.class.php";
	//require_once get_path('incRepositorySys') . '/lib/fileManage.lib.php';
    
	//Variables
	$error_message = '';
	$iIDQuiz = $_GET["id"];
	$repQuizId = $_GET["repQuizId"];
   
   // Declaration de la Class netquiz		
	$netquiz = new netquiz();
	
	// Class netquiz : recuperation de IdQuestion
	$netquiz->setIdQuiz( $iIDQuiz );
	$selectIdQuestion = $netquiz->selectIdQuestion();

	foreach( $selectIdQuestion as $IdQuestion )
	{
	
		$iIDQuestion = $IdQuestion['IDQuestion'];
		
		// Class netquiz : Select participations
		$netquiz->setIdQuestion( $iIDQuestion );
		$selectAllParticipations = $netquiz->selectAllParticipations();
		
		foreach( $selectAllParticipations as $AllParticipations )
		{
			$iIDParticipant = $AllParticipations['IDParticipant'];
			
			$netquiz->setIdQuestion( $iIDQuestion );
			$netquiz->setIdParticipant( $iIDParticipant );
			if ( !$netquiz->deleteAllParticipations() )
			{
				$error_message .= '<li>'.get_lang("Participations is not deleted").'</li>';
			}
		}
		
		$netquiz->setIdQuestion( $iIDQuestion );
		if ( !$netquiz->deleteAllQuestions() )
		{
			$error_message .= '<li>'.get_lang("Questions is not deleted").'</li>';
		}
	}
	
	$netquiz->setIdQuiz( $iIDQuiz );
	if ( !$netquiz->deleteQuizs() )
	{
		$error_message .= '<li>'.get_lang("Quizs is not deleted").'</li>';
	}
	
	$netquiz->setIdQuiz( $iIDQuiz );
	if ( !$netquiz->deleteParticipants() )
	{
		$error_message .= '<li>'.get_lang("Participants is not deleted").'</li>';
	}

	// supression du répertoire
	$dataDirectory = get_path('rootSys') . 'courses/' . claro_get_course_path() .'/modules/' . get_current_module_label() . '/data/' . $repQuizId;
	if ( !claro_delete_file( $dataDirectory ) )
	{
		$error_message .= '<li>'.get_lang("Reportory is not deleted").'</li>';
	}
	
	$confirm = '<li>'.get_lang("L'exercice a correctement été supprimé !").'</li>';

//}	
?>