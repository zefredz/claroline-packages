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
    
	function output($s){
        print $s;
    }
	
    //MAIN
    
    $iIDQuiz = $_GET["id"];
    $sTab = "\t";
    
	/*
    $oServerConn = mysql_connect($sMySQLLocation,$sMySQLUser,$sMySQLPassword);
    if(!$oServerConn){
        die(mysql_error());
    }
    
    mysql_select_db($sMySQLDatabase);
    
	
    //Nom du quiz et date de version
    $oRS = executeQuery("select QuizName, UNIX_TIMESTAMP(VersionDate) as VersionDate from nq_quizs where IDQuiz = $iIDQuiz");
    
    $sQuizName = str_replace(" ","_",html_entity_decode(mysql_result($oRS,0,"QuizName")));
    $dVersionDate = mysql_result($oRS,0,"VersionDate");
    $dVersionDate = date($sExportDateFormat,$dVersionDate);
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
    //header("Content-type: text");
    //header("Content-type: text/plain; charset=$sCharset");
    header("Content-disposition: attachment; filename=$sQuizName$dVersionDate.txt");
    
    //Liste des questions
    //OrderBy
    $sCols = array("NoQuestion","QuestionName","QuestionTypeTD","Average","Ponderation");
    $sLabels = array("No","Titre","Type","Moyenne","Pond&eacute;ration");
    $sLinks = array();
    $sOrderByField = "NoQuestion";
    $sOrderByDirection = "ASC";
    $sOrderByID = -1;
    
    if(isset($_GET["ob"]) && $_GET["ob"] > -1){
        $sOrderByField = $sCols[$_GET["ob"]];
    }
    
    if(isset($_GET["obd"])){
        $sOrderByDirection = $_GET["obd"];
    }
    
	// Class netquiz : recuperation de la liste des questions
	$netquiz->setIdQuiz( $iIDQuiz );
	$netquiz->setOrderByField( $sOrderByField );
	$netquiz->setOrderByDirection( $sOrderByDirection );
	$selectQuestions = $netquiz->selectQuestions();
	
	for($i = 0;$i < count($sCols);$i++){
        output(html_entity_decode($sLabels[$i]) . $sTab);
    }
	
	output("\n\n");
	
	foreach($selectQuestions as $row)
	{
		$iIDQuestion = $row['IDQuestion'];
        $sNo = $row['NoQuestion'];
        $iActive = intval($row['Active']);
        $sNomPrefix = ($iActive == 0 ? $sCanceledQPrefix : "");
        $sNom = clipString($row['QuestionName'],$iQNameMaxNbChar,$sDefaultClipString);
        $sType = html_entity_decode($row['QuestionTypeTD']);
        $sAverage = $row['Average'];
        $sPond = toLangFloat($row['Ponderation']);

        output($sNo . $sTab . $sNom . $sTab . $sType . $sTab . $sAverage . $sTab . $sPond . "\n");
	}


	/*
    $sQuery =   "select nq_questions.NoQuestion, nq_questions.Ponderation, AVG(nq_participations.Pointage) as Average, " .
                "nq_questions.QuestionName , nq_questions.QuestionTypeTD, nq_questions.IDQuestion, nq_questions.Active " .
                "from nq_questions " .
                "left join nq_participations using (IDQuestion) " .
                "left join (select * from nq_participants where Actif = 1) nq_participants_actif on nq_participations.IDParticipant = nq_participants_actif.IDParticipant " .
                "where nq_questions.IDQuiz = $iIDQuiz " .
                "group by nq_questions.IDQuestion " .
                "order by $sOrderByField $sOrderByDirection";
    
    
    $oRS = executeQuery($sQuery);
    
    for($i = 0;$i < count($sCols);$i++){
        output(html_entity_decode($sLabels[$i]) . $sTab);
    }
    output("\n\n");
    
    for($i = 0;$i < mysql_num_rows($oRS);$i++){
        $iIDQuestion = mysql_result($oRS,$i,"IDQuestion");
        $sNo = mysql_result($oRS,$i,"NoQuestion");
        $iActive = intval(mysql_result($oRS,$i,"Active"));
        $sNomPrefix = ($iActive == 0 ? $sCanceledQPrefix : "");
        $sNom = clipString(mysql_result($oRS,$i,"QuestionName"),$iQNameMaxNbChar,$sDefaultClipString);
        $sType = html_entity_decode(mysql_result($oRS,$i,"QuestionTypeTD"));
        $sAverage = mysql_result($oRS,$i,"Average");
        $sPond = toLangFloat(mysql_result($oRS,$i,"Ponderation"));

        output($sNo . $sTab . $sNom . $sTab . $sType . $sTab . $sAverage . $sTab . $sPond . "\n");
    }
    mysql_close();
    */
}
?>