<?php

// inclusion du noyeux de claroline
include "../../../claroline/inc/claro_init_global.inc.php";
require_once get_path('includePath').'/lib/user.lib.php';

// lib
require_once "../lib/netquiz.class.php";
require_once get_path('incRepositorySys') . '/lib/fileManage.lib.php';

// recupération des données utilisateurs
$current_user_data = user_get_properties(claro_get_current_user_id());

    include_once("langr.inc.php");
    include_once("settings.inc.php");
    include_once("functions.inc.php");
    include_once("qvalidatedictee.php");
    
    //Settings
    $bVerbose = false;
    
    //Variables
    $sQuizIdent = $_POST["QuizIdent"];
    $sQuizVersion = $_POST["QuizVersion"];
    $iNoQuestion = intval($_POST["NoQuestion"]);
    $iTypeQuestion = intval($_POST["TypeQuestion"]);
    $sNextPage = $_POST["NextPage"];
    $iIDParticipant = $_POST["IDParticipant"];
    
    //Get the source URL
    $sReferer = $_SERVER["HTTP_REFERER"];
    
    $sRefererPath = substr($sReferer,0,strrpos($sReferer,"/"));
    
    $sNextPageFull = $sRefererPath . "/" . $sNextPage;
	
	// Declaration de la Class netquiz		
	$netquiz = new netquiz();
	
    if(isset($_POST["Final"])){
	    // Class netquiz : Update Participant info if final soumission
		$netquiz->setIdParticipant( $iIDParticipant );

		if ( !$netquiz->updateParticipantsDate() )
		{
			claro_die( get_lang( "Participant info is not updated" ) );
		}
	}
	
    if($iNoQuestion == -1){
        urlRedirect($sNextPageFull,false);
    }
    
    //Get the IDQuestion
	$netquiz->setQuizIdent( $sQuizIdent );
	$netquiz->setQuizVersion( $sQuizVersion );
	$netquiz->setNoQuestion( $iNoQuestion );
	$iIDQuestion = $netquiz->selectOneIdQuestion();
	
	//Get ReponseXML and Ponderation
	$netquiz->setIdQuestion( $iIDQuestion );
	$reponseXMLandPonderation = $netquiz->selectReponseXMLandPonderation();
	
    $sReponseXML = "<?xml version='1.0' encoding=\"ISO-8859-1\"?>" . $reponseXMLandPonderation['ReponseXML'];
    $dPonderation = $reponseXMLandPonderation['Ponderation'];
	
    //Validate the question
    switch($iTypeQuestion){
        case 0: //Multiple
            
			//var_dump($iIDQuestion . ' , ' . $iIDParticipant . ' , ' . $sReponseXML . ' , ' . $dPonderation . '<br />');
			
			validateMultiple($iIDQuestion,$iIDParticipant,$sReponseXML,$dPonderation);
            break;
        case 1: //Lacune
            
			//var_dump($iIDQuestion . ' , ' . $iIDParticipant . ' , ' . $sReponseXML . ' , ' . $dPonderation . '<br />');
			
			validateLacune($iIDQuestion,$iIDParticipant,$sReponseXML,$dPonderation);
            break;
        case 2: //Closure
            
			//var_dump($iIDQuestion . ' , ' . $iIDParticipant . ' , ' . $sReponseXML . ' , ' . $dPonderation . '<br />');
			
			validateClosure($iIDQuestion,$iIDParticipant,$sReponseXML,$dPonderation);
            break;
        case 3: //Dictee
            
			//var_dump($iIDQuestion . ' , ' . $iIDParticipant . ' , ' . $sReponseXML . ' , ' . $dPonderation . '<br />');
			
			validateDictee($iIDQuestion,$iIDParticipant,$sReponseXML,$dPonderation);
            break;
        case 4: //Ouverte
            
			//var_dump($iIDQuestion . ' , ' . $iIDParticipant . ' , ' . $sReponseXML . ' , ' . $dPonderation . '<br />');
			
			validateOuverte($iIDQuestion,$iIDParticipant,$sReponseXML,$dPonderation);
            break;
        case 5: //Mise en ordre
            
			//var_dump($iIDQuestion . ' , ' . $iIDParticipant . ' , ' . $sReponseXML . ' , ' . $dPonderation . '<br />');
			
			validateMiseEnOrdre($iIDQuestion,$iIDParticipant,$sReponseXML,$dPonderation);
            break;
        case 6: //Association
            
			//var_dump($iIDQuestion . ' , ' . $iIDParticipant . ' , ' . $sReponseXML . ' , ' . $dPonderation . '<br />');
			
			validateAssociation($iIDQuestion,$iIDParticipant,$sReponseXML,$dPonderation);
            break;
        case 7: //Damier
            
			//var_dump($iIDQuestion . ' , ' . $iIDParticipant . ' , ' . $sReponseXML . ' , ' . $dPonderation . '<br />');
			
			validateDamier($iIDQuestion,$iIDParticipant,$sReponseXML,$dPonderation);
            break;
        case 8: //Zone à identifier
            
			//var_dump($iIDQuestion . ' , ' . $iIDParticipant . ' , ' . $sReponseXML . ' , ' . $dPonderation . '<br />');
			
			validateZone($iIDQuestion,$iIDParticipant,$sReponseXML,$dPonderation);
            break;
    }
    
    //Redirect to NextPage
    echoComment("The next page is <a href=\"$sNextPageFull\">$sNextPageFull</a>");
    if(!$bVerbose){
        urlRedirect($sNextPageFull,false);
    }
?> 