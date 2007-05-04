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
    
	function removeEMTags($s){
        $sToReplace = array("<em>","</em>");
        $sBy = array("","");
        
        return str_replace($sToReplace,$sBy,$s);
    }
    function output($s){
        print $s;
    }
	
    //MAIN
    
    $iIDQuiz = $_GET["id"];
    $sTab = "\t";
    /*
    //Connection
    $oServerConn = mysql_connect($sMySQLLocation,$sMySQLUser,$sMySQLPassword);
    if(!$oServerConn){
        die(mysql_error());
    }
    
    mysql_select_db($sMySQLDatabase);
    
	
	
    //Nom du quiz et date de version
    $oRS = executeQuery("select QuizName, UNIX_TIMESTAMP(VersionDate) as VersionDate from nq_quizs where IDQuiz = $iIDQuiz");
   */ 
    
	// Declaration de la Class netquiz		
	$netquiz = new netquiz();
	
	// Class netquiz : recuperation du Nom du quiz et date de version
	$netquiz->setIdQuiz( $iIDQuiz );
	$selectNameQuizAndDate = $netquiz->selectNameQuizAndDate();
	
	foreach( $selectNameQuizAndDate as $row )
	{
		$sQuizName = str_replace(" ","_",html_entity_decode($row['QuizName']));
	    $dVersionDate = date($sExportDateFormat,$row['VersionDate']);
	}
    
    $sCharset = "ISO-8859-1";

    //Header
    header("Content-type: application/force-download; charset=$sCharset");
    header("Content-disposition: attachment; filename=$sQuizName$dVersionDate.txt");
    
	
	// Class netquiz : recuperation du nombre Total du quiz
	$netquiz->setIdQuiz( $iIDQuiz );
	$iPonderationTotal = $netquiz->selectPonderationTotal();
	
	/*
    //Total du quiz
	$oRS = executeQuery("select sum(Ponderation) as PonderationTotal from nq_questions where IDQuiz = $iIDQuiz");
    $iPonderationTotal = mysql_result($oRS,0,"PonderationTotal");
    */

    //Liste des participations
    //OrderBy
    $sCols = array("Matricule","Prenom","Groupe","ParticipationDate","PointageTotal");
    $sLabels = array("Matricule","Nom","Groupe","Soumission","R&eacute;sultats");
    $sLinks = array();
    $sOrderByField = "Nom";
    $sOrderByDirection = "ASC";
    $sOrderByID = -1;
    
    if(isset($_GET["ob"]) && $_GET["ob"] > -1){
        $sOrderByField = $sCols[$_GET["ob"]];
    }
    
    if(isset($_GET["obd"])){
        $sOrderByDirection = $_GET["obd"];
    }
	
	// Class netquiz : recuperation de la ponderation
	$netquiz->setIdQuiz( $iIDQuiz );
	$netquiz->setOrderByField( $sOrderByField );
	$netquiz->setOrderByDirection( $sOrderByDirection );
	$selectParticipations = $netquiz->selectParticipations();
	
	output("Statut" . $sTab . "Matricule" . $sTab . "Nom" . $sTab . "Courriel" . $sTab . "Groupe" . $sTab . "Soumission" . $sTab . html_entity_decode("R&eacute;sultats ( / ") . toLangFloat($iPonderationTotal) . " )" . $sTab . html_entity_decode("R&eacute;sultats ( % )"));
    
	/*
	for($i = 0;$i < count($sCols);$i++){
        output(html_entity_decode($sLabels[$i]) . $sTab);
    }
	*/
	output("\n\n");

	foreach($selectParticipations as $row)
	{
		$iIDParticipant = $row['IDParticipant'];
        $sNom = $row['Nom'];
        $sPrenom = $row['Prenom'];
        $sCourriel = $row['Courriel'];
        $sMatricule = $row['Matricule'];
        $sPrenomNom = clipString($sPrenom . " " . $sNom,30,$sDefaultClipString);
        $sGroupe = $row['Groupe'];
        $iActif = intval($row['Actif']);
        $sNomPrefix = ($iActif == 0 ? $sCanceledSPrefix : "");
        $iFinal = $row['Final'];
        if(intval($iFinal) == 1){
            $sDate = date($sDefaultDateHourFormat,$row['ParticipationDate']);
        }else{
            $sDate = "-";
        }
        $iPointageTotal = $row['PointageTotal'];
        $sScore = toLangFloat($iPointageTotal);
        $sScorePC = round($iPointageTotal/$iPonderationTotal*100);
        
        output(removeEMTags(html_entity_decode($sNomPrefix)) . $sTab . $sMatricule . $sTab . $sPrenomNom . $sTab . $sCourriel . $sTab . $sGroupe . $sTab . $sDate . $sTab . $sScore . $sTab . $sScorePC . "\n");
	}
	
	/*
    $sQuery =   "select nq_participants.IDParticipant, nq_participants.Prenom, nq_participants.Nom, nq_participants.Groupe, nq_participants.Final,  nq_participants.Courriel, " .
                "UNIX_TIMESTAMP(nq_participants.ParticipationDate) as ParticipationDate, sum(nq_participations.Pointage) as PointageTotal, nq_participants.Matricule, nq_participants.Actif " .
                "from nq_participants " .
                "left join nq_participations using (IDParticipant) " .
                "right join nq_questions on nq_participations.IDQuestion = nq_questions.IDQuestion " .
                "where nq_questions.Active = 1 and " .
                "nq_participants.IDQuiz = $iIDQuiz " .
                "group by nq_participants.IDParticipant " .
                "order by $sOrderByField $sOrderByDirection";
    
    $oRS = executeQuery($sQuery);

    output("Statut" . $sTab . "Matricule" . $sTab . "Nom" . $sTab . "Courriel" . $sTab . "Groupe" . $sTab . "Soumission" . $sTab . html_entity_decode("R&eacute;sultats ( / ") . toLangFloat($iPonderationTotal) . " )" . $sTab . html_entity_decode("R&eacute;sultats ( % )"));
    output("\n\n");
        
    for($i = 0;$i < mysql_num_rows($oRS);$i++){
        $iIDParticipant = mysql_result($oRS,$i,"IDParticipant");
        $sNom = mysql_result($oRS,$i,"Nom");
        $sPrenom = mysql_result($oRS,$i,"Prenom");
        $sCourriel = mysql_result($oRS,$i,"Courriel");
        $sMatricule = mysql_result($oRS,$i,"Matricule");
        $sPrenomNom = clipString($sPrenom . " " . $sNom,30,$sDefaultClipString);
        $sGroupe = mysql_result($oRS,$i,"Groupe");
        $iActif = intval(mysql_result($oRS,$i,"Actif"));
        $sNomPrefix = ($iActif == 0 ? $sCanceledSPrefix : "");
        $iFinal = mysql_result($oRS,$i,"Final");
        if(intval($iFinal) == 1){
            $sDate = date($sDefaultDateHourFormat,mysql_result($oRS,$i,"ParticipationDate"));
        }else{
            $sDate = "-";
        }
        $iPointageTotal = mysql_result($oRS,$i,"PointageTotal");
        $sScore = toLangFloat($iPointageTotal);
        $sScorePC = round($iPointageTotal/$iPonderationTotal*100);
        
        output(removeEMTags(html_entity_decode($sNomPrefix)) . $sTab . $sMatricule . $sTab . $sPrenomNom . $sTab . $sCourriel . $sTab . $sGroupe . $sTab . $sDate . $sTab . $sScore . $sTab . $sScorePC . "\n");
    }

    mysql_close();
    */
    
}
?>
