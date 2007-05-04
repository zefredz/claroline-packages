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
	
	include_once("langr.inc.php");
	include_once("settings.inc.php");
	include_once("functions.inc.php");

	//Get file name
	if(!isset($_GET["fn"])){
		urlRedirect("addquizlist.php");
	}
	$sFileName = $_GET["fn"];
	$sFilePath = $sXMLFileFolder . $sFileName;

	//Try to open it using xmldom
	//If it fails, return error
	if(!$xml = simplexml_load_file($sFilePath)){
		urlRedirect("addquizform.php?msg=0");
		//urlRedirect("index.php?msg=0");
		exit();
	}

	//Get quiz info
	$oQuestions = $xml->quiz->questions->question;
	$sQuizIdent = $xml->quiz->quizident;
	$sQuizVersion = $xml->quiz->quizversion;	
	$sQuizTitre = html_entity_decode(XMLStrtoStr($xml->quiz->quiztitre));
	$iNbQuestions = count($oQuestions);
	//$sVersionDate = now();
	$sPassword = '';
	$sQuizAuteur = html_entity_decode(XMLStrtoStr($xml->quiz->quizauteur));
	$sActif = 1;

	/*
	//Check if quiz already exist
	$oServerConn = mysql_connect($sMySQLLocation,$sMySQLUser,$sMySQLPassword);
	if(!$oServerConn){
	die(mysql_error());
	}

	mysql_select_db($sMySQLDatabase);

	$oRS = executeQuery("select * from nq_quizs where QuizIdent = $sQuizIdent and QuizVersion = $sQuizVersion");

	
	
	

	
	
	
	
	
	if(mysql_num_rows($oRS) > 0){
		unlink($sFilePath);
		urlRedirect("addquizform.php?msg=1");
		exit();
	}
	
	*/
	
	### debut debug #################################################################################################
	
	// Declaration de la Class netquiz		
	$netquiz = new netquiz();
	
	// Class netquiz : recuperation de IdQuiz	
	$netquiz->setQuizVersion( $sQuizVersion );
	$netquiz->setQuizIdent( $sQuizIdent );
	$iIDQuiz = $netquiz->fetchIdQuiz();
	
	### debug ###
	// attention la redirection ne fonctionne pas ...
	
	#################################################################################################
	# debut gros problème ....
	#################################################################################################
	
	/*
	
	// code original 
	if(mysql_num_rows($oRS) > 0){
		unlink($sFilePath);
		urlRedirect("addquizform.php?msg=1");
		exit();
	}
	*/
	// mon code
	if( !is_null($iIDQuiz) ) {
		//unlink($sFilePath);
		urlRedirect("addquizform.php?msg=1");
		//urlRedirect("index.php?msg=1");
		exit();
	}

	#################################################################################################
	# fin gros problème ....
	#################################################################################################

	
	/*
	if ( is_null($iIDQuiz) )
	{
		claro_die(get_lang("Quiz not found"));
	}
	*/
	
	/*
	//Insert un quiz en DB
	$sQuery =   "insert into nq_quizs (QuizIdent, QuizVersion, QuizName, NbQuestions, VersionDate, Password, Auteur, Actif) 
				values ($sQuizIdent,$sQuizVersion,$sQuizTitre,$iNbQuestions,now(),'',$sQuizAuteur,1)";

	executeQuery($sQuery);
	*/
	
	// Class netquiz : insertion du quiz
	$netquiz->setQuizIdent( $sQuizIdent );
	$netquiz->setQuizVersion( $sQuizVersion );
	$netquiz->setQuizName( $sQuizTitre );
	$netquiz->setNbQuestions( $iNbQuestions );
	//$netquiz->setVersionDate( $sVersionDate );
	$netquiz->setPassword( $sPassword );	
	$netquiz->setAuteur( $sQuizAuteur );
	$netquiz->setActif( $sActif );
	
	if ( !$netquiz->insertQuiz() )
	{
		claro_die(get_lang("Quiz is not insert"));
	}
		
	//Get the quiz index
	
	/*
	$oRS = executeQuery("select max(IDQuiz) as last_id from nq_quizs");
	$iIDQuiz = mysql_result($oRS,0,"last_id");
	*/
	
	$iIDQuiz = netquiz::lastIdQuiz();
	
	### fin debug #################################################################################################
	
	//Loop for each questions
	$iNoQuestion = 0;
	foreach($oQuestions as $oQuestion){
		//Insert question information in DB
	$sQAtt = $oQuestion->attributes();
	$iType = intval($sQAtt["type"]);
		if($iType > 0){
			$sType = $sTypeLabel[$iType];
			
		}else{
			if($oQuestion->reponse->isrepmultiple == "true"){
				$sType = $sTypeLabel[0]["reponses"];
				
			}else{
				if(count($oQuestion->reponse->liste_choix->choix) == 2){
					$sType = $sTypeLabel[0]["vraifaux"];
				}else{
					$sType = $sTypeLabel[0]["choix"];
				}
			}
		}
		
		$sTitre = XMLStrtoStr($oQuestion->titre);
		$sEnonce = XMLStrtoStr($oQuestion->enonce);
		$iPonderation = $oQuestion->ponderation;
		$sReponseXML = XMLStrtoStr($oQuestion->reponse->asXML());
		
		/*
		$sQuery =   "insert into nq_questions (QuestionName, QuestionType, QuestionTypeTD, Ponderation, EnonceHTML, ReponseXML, IDQuiz, NoQuestion) 
					values ($sTitre,$iType,$sType,$iPonderation,$sEnonce,$sReponseXML,$iIDQuiz,$iNoQuestion)";
					
		executeQuery($sQuery);
		*/
		
		// Class netquiz : insertion du quiz
		$netquiz->setTitre( $sTitre );
		$netquiz->setType( $iType );
		$netquiz->setTypeTd( $sType );
		$netquiz->setPonderation( $iPonderation );
		$netquiz->setEnonce( $sEnonce );
		$netquiz->setReponseXML( $sReponseXML );	
		$netquiz->setIdQuiz( $iIDQuiz );
		$netquiz->setNoQuestion( $iNoQuestion );
		
		if ( !$netquiz->insertQuestions() )
		{
			claro_die(get_lang("Questions is not insert"));
		}

		$iNoQuestion++;
	}
	urlRedirect("quizlist.php");

}
    
?>