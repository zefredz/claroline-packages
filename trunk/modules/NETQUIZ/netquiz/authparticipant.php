<?php

// inclusion du noyeux de claroline
include "../../../claroline/inc/claro_init_global.inc.php";
require_once get_path('includePath').'/lib/user.lib.php';

// lib
require_once "../lib/netquiz.class.php";

// recupération des données utilisateurs
$current_user_data = user_get_properties(claro_get_current_user_id());

	// inclusion des fichiers Netquiz
	include_once("langr.inc.php");
	include_once("settings.inc.php");
	include_once("functions.inc.php");
	//include_once("fct_validate_form.php");
	
	// Récupération des paramettres URL
	$sNextPage = $_GET["np"];
	$sQuizIdent = $_GET["qi"];
	$sQuizVersion = $_GET["qv"];

	// Declaration de la Class netquiz		
	$netquiz = new netquiz();
	
	// Class netquiz : recuperation de IdQuiz
	$netquiz->setQuizVersion( $sQuizVersion );
	$netquiz->setQuizIdent( $sQuizIdent );
	$iIDQuiz = $netquiz->fetchIdQuiz();
	
	if ( is_null($iIDQuiz) )
	{
		claro_die(get_lang("Quiz not found"));
	}
		
	// Insert participant information
	$current_user_id = claro_get_current_user_id();
    $sPrenom = $current_user_data['firstname'];
	$sNom = $current_user_data['lastname'];
	$sGroupe = "";
	$sMatricule = $current_user_data['officialCode'];
	$sCourriel = $current_user_data['email'];

	// Class netquiz : insertion du participant
	$netquiz->setCurrentUserId( $current_user_id );
    $netquiz->setPrenom( $sPrenom );
	$netquiz->setNom( $sNom );
	$netquiz->setGroupe( $sGroupe );
	$netquiz->setMatricule( $sMatricule );
	$netquiz->setCourriel( $sCourriel );
	$netquiz->setIdQuiz( $iIDQuiz );
	
	if ( !$netquiz->insertUser() )
	{
		claro_die(get_lang("Participant is not insert"));
	}
	

	$iIDParticipant = netquiz::lastIdParticipant();

	//Generate javascript (set iIDParticipant and redirect to NextPage)
	$sReferer = $_SERVER["HTTP_REFERER"];
	
	$sRefererPath = substr($sReferer,0,strrpos($sReferer,"/"));
	
	$sNextPageFull = $sRefererPath . "/" . $sNextPage;
?>
	
<html>
	<head>
		<script>
			function pageInit(){
				parent.iIDParticipant = '<?php echo $iIDParticipant; ?>';
				parent.sNomUsager = '<?php echo $sNom; ?>';
				parent.sPrenomUsager = '<?php echo $sPrenom; ?>';
				parent.sMatriculeUsager = '<?php echo $sMatricule; ?>';
				parent.sGroupeUsager = '<?php echo $sGroupe; ?>';
				parent.sCourrielUsager = '<?php echo $sCourriel; ?>';
				parent.window.onbeforeunload = parent.confirmClose;
				parent.moveFirst();
			}
		</script>
	</head>
	<body onload="pageInit()">
		&nbsp;
	</body>
</html>