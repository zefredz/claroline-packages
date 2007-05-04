<?php

### debut debug #################################################################################################

// inclusion du noyeux de claroline
include "../../../claroline/inc/claro_init_global.inc.php";
require_once get_path('includePath').'/lib/user.lib.php';

// lib
require_once "../lib/netquiz.class.php";

// recupération des données utilisateurs
$current_user_data = user_get_properties(claro_get_current_user_id());

// Vérification que l'utilisateur soit enregistré
if(!claro_is_user_authenticated()) 
{
	claro_die(get_lang("Not allowed"));
	//claro_disp_auth_form();
}
else
{

### fin debug #################################################################################################

	include_once("settings.inc.php");
    include_once("functions.inc.php");
	//include "isauth.php";
    
    //Variables
    $iIDQuiz = $_GET["id"];
    /*
    //Connection
    $oServerConn = mysql_connect($sMySQLLocation,$sMySQLUser,$sMySQLPassword);
    if(!$oServerConn){
        die(mysql_error());
    }
    
    mysql_select_db($sMySQLDatabase);
    */
	
    /*//Get every IDQuestion
    $sQuery = "select IDQuestion from nq_questions where IDQuiz = $iIDQuiz";
    $oRS = executeQuery($sQuery);
    */
   
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
				claro_die(get_lang("Participations is not deleted"));
			}
			
		}
		
		$netquiz->setIdQuestion( $iIDQuestion );
		if ( !$netquiz->deleteAllQuestions() )
		{
			claro_die(get_lang("Questions is not deleted"));
		}

	
	}
	
	$netquiz->setIdQuiz( $iIDQuiz );
	if ( !$netquiz->deleteQuizs() )
	{
		claro_die(get_lang("Quizs is not deleted"));
	}
	
	$netquiz->setIdQuiz( $iIDQuiz );
	if ( !$netquiz->deleteParticipants() )
	{
		claro_die(get_lang("Participants is not deleted"));
	}

	urlRedirect("quizlist.php");
	
/*
   //Loop with IDQuestion and delete every Participations and Questions
    for($i = 0;$i < mysql_num_rows($oRS);$i++){
        $iIDQuestion = mysql_result($oRS,$i,"IDQuestion");
        
        $sQuery = "select * from nq_participations where IDQuestion = $iIDQuestion";
        //echo "$sQuery<br><br>";
        $oRSAux = executeQuery($sQuery);
        
        for($j = 0;$j < mysql_num_rows($oRSAux);$j++){
            $iIDParticipant = mysql_result($oRSAux,$j,"IDParticipant");
            
            $sQuery = "delete from nq_participations where IDParticipant = $iIDParticipant and IDQuestion = $iIDQuestion";
            //echo "$sQuery<br><br>";
            executeQuery($sQuery);
        }
        $sQuery = "delete from nq_questions where IDQuestion = $iIDQuestion";
        //echo "$sQuery<br><br>";
        executeQuery($sQuery);
        
    }

    $sQuery = "delete from nq_quizs where IDQuiz = $iIDQuiz";
    executeQuery($sQuery);
    
    $sQuery = "delete from nq_participants where IDQuiz = $iIDQuiz";
    executeQuery($sQuery);

    urlRedirect("quizlist.php");
    
    mysql_close();
	*/
}	
?> 