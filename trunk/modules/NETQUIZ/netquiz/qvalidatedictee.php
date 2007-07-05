<?php
include_once("functions.inc.php");

function validateMultiple($iIDQ,$iIDP,$sXML,$dP){
	include("settings.inc.php");
	
	echoComment("Validating Multiple");
	echoComment(htmlentities($sXML));
	
	//Extract Information from XML
	$oXML = simplexml_load_string($sXML);
	
	//$iTypeEti = $oXML->etiquette;
	$bRepMultiple = true;
	if($oXML->isrepmultiple == "false"){
		$bRepMultiple = false;
	}
	$bTousBRepO = true;
	if($oXML->istousbrepo == "false"){
		$bTousBRepO = false;
	}
	
	$oListeChoix = $oXML->liste_choix->choix;
	$sTextes = array();
	$bBRep = array();
	$iNbBRep = 0;
	$iNbMRep = 0;
	foreach($oListeChoix as $oChoix){
		$sAtt = $oChoix->attributes();
		$sCID = $sAtt["cid"];
		$sTextes["$sCID"] = $oChoix->texte;
		$bBRep["$sCID"] = $oChoix->isbrep;
		if($bBRep["$sCID"] == "true"){
			$iNbBRep++;
		}else{
			$iNbMRep++;
		}
	}
	
	//Begin Validation
	$iPointage = 0;
	$sReponseHTML = "";
	
	$iNbGivenBRep = 0;
	$iNbGivenMRep = 0;
	
	echoComment("Reponse : (" . trim($_POST["Reponse"],",") . ")");
	if(strlen(trim($_POST["Reponse"],",")) == 0){
		$sReponseHTML = "<font style=\"color:$sNoRepMsgCol\">$sNoRepMsg</font><br /><br />";
		$iPointage = 0;
	}else{
		$sGivenCID = explode(",",trim($_POST["Reponse"],","));
		for($i = 0;$i < count($sGivenCID);$i++){
			//Validate
			$sCID = $sGivenCID[$i];
			if($bBRep["$sCID"] == "true"){
				$iNbGivenBRep++;
				$sBulletName = $sBRepBullet;
			}else{
				$iNbGivenMRep++;
				$sBulletName = $sMRepBullet;
			}
			
			$sTexte = $sTextes["$sCID"];
			
			//Generate ReponseHTML
			$sReponseHTML .= "<img src=\"images/$sBulletName\" /><img src=\"images/spacer.gif\" width=\"10\" height=\"10\" />";
			$sReponseHTML .= "$sTexte<br /><br />";
			
		}
		
		if(($iNbGivenMRep == 0) && ($iNbGivenBRep == 0)){
			$sMsg = $sRepIncMsg;
			$sMsgColor = $sRepIncMsgCol;
		}else if($iNbGivenMRep == 0){
			if((!$bRepMultiple) || (!$bTousBRepO)){
				$sMsg = $sBRepMsg;
				$sMsgColor = $sBRepMsgCol;
			}else{
				if($iNbGivenBRep == $iNbBRep){
					$sMsg = $sBRepMsg;
					$sMsgColor = $sBRepMsgCol;
				}else{
					$sMsg = $sRepIncMsg;
					$sMsgColor = $sRepIncMsgCol;
				}
			}
		}else{
			$sMsg = $sMRepMsg;
			$sMsgColor = $sMRepMsgCol;
		}
		
		
		
		$sReponseHTML = "<font style=\"color:$sMsgColor\">$sMsg</font><br /><br />" . $sReponseHTML;
		
		echoComment("iNbGivenBRep : $iNbGivenBRep <br><br>iNbGivenMRep : $iNbGivenMRep<br><br>iNbBRep : $iNbBRep");
				
		if($bTousBRepO){
			$iNbBRepRequis = $iNbBRep;
		}else{
			$iNbBRepRequis = 1;
		}
		
		if($bRepMultiple && !$bTousBRepO){
			$dPondBRep = (($iNbGivenBRep > 0) ? $dP : 0);
			$dPondMRep = $dP / $iNbMRep;
			
			echoComment("\$dPondBRep = $dPondBRep");
			echoComment("\$dPondMRep = $dPondMRep");
			
			$iPointage = max(($dPondBRep - ($dPondMRep * $iNbGivenMRep)),0);
		}else{
			echoComment("iNbBRepRequis = $iNbBRepRequis");
			echoComment((min(max(($iNbGivenBRep - $iNbGivenMRep),0),$iNbBRepRequis) / $iNbBRepRequis));
			
			$iPointage = (min(max(($iNbGivenBRep - $iNbGivenMRep),0),$iNbBRepRequis) / $iNbBRepRequis) * $dP;  
		}
	}
	
	saveValidation($sReponseHTML, $iPointage, $iIDP,$iIDQ);
}

function validateLacune($iIDQ,$iIDP,$sXML,$dP){
	include("settings.inc.php");
	
	echoComment("Validating Lacune");
	echoComment(htmlentities($sXML));
	
	//Extract Information from XML
	$oXML = simplexml_load_string($sXML);
	
	$sReponse = cleanForValid($oXML->breponse);
	$sGivenReponse = cleanForValid(fromGPC($_POST["Reponse"]));
	//Begin Validation
	$iPointage = 0;
	$sReponseHTML = "";
	
	echoComment("sReponse = " . $sReponse);
	
	//Validate
	if(strlen($sGivenReponse) == 0){
		$sReponseHTML = "<font style=\"color:$sNoRepMsgCol\">$sNoRepMsg</font><br /><br />";
		$iPointage = 0;
	}else{
		if(strtolower($sReponse) == strtolower($sGivenReponse)){
			$iPointage = $dP;
			$sBulletName = $sBRepBullet;
			$sMsg = $sBRepMsg;
			$sMsgColor = $sBRepMsgCol;
			$sRepToDisplay = $sReponse;
		}else{
			$iPointage = 0;
			$sBulletName = $sMRepBullet;
			$sMsg = $sMRepMsg;
			$sMsgColor = $sMRepMsgCol;
			$sRepToDisplay = $sGivenReponse;
		}
		
		
		$sRepToDisplay = htmlentities($sRepToDisplay);
		
		//Generate ReponseHTML
		$sReponseHTML = "<font style=\"color:$sMsgColor\">$sMsg</font><br /><br />";
		$sReponseHTML .= "<img src=\"images/$sBulletName\" /><img src=\"images/spacer.gif\" width=\"10\" height=\"10\" />";
		$sReponseHTML .= "$sRepToDisplay<br><br>";
	}
	
	saveValidation($sReponseHTML, $iPointage, $iIDP,$iIDQ);
}

function validateClosure($iIDQ,$iIDP,$sXML,$dP){
	include("settings.inc.php");
	
	echoComment("Validating Closure");
	echoComment(htmlentities($sXML));
	
	//Extract Information from XML
	$oXML = simplexml_load_string($sXML);
	
	$sType = $oXML->type;

	$oTrous = array();
	$oListeTrous = $oXML->liste_trous->trou;
	$iNbTrous = count($oListeTrous);
	echoComment("Begining parsing trous (" . $iNbTrous . " trous)");
	for($i = 0;$i < $iNbTrous;$i++){
		$oTrou = $oListeTrous[$i];
		
		if($sType == "simple"){
			$oTrous[$i] = $oTrou->reponse;
		}else{
			$oTrous[$i] = array();
			
			$oListeChoix = $oTrou->liste_choix->choix;
			echoComment("&nbsp;&nbsp;Trou no $i (" . count($oListeChoix) . " choix)");
			foreach($oListeChoix as $oChoix){
				$sAtt = $oChoix->attributes();
				$sCID = $sAtt["cid"];
				$oTrous[$i]["$sCID"] = array();
				
				$oTrous[$i]["$sCID"]["texte"] = $oChoix->texte;
				$oTrous[$i]["$sCID"]["isbrep"] = $oChoix->isbrep;
				
				echoComment("&nbsp;&nbsp;&nbsp;&nbsp;\$oTrous[$i][\"$sCID\"][\"texte\"] = " . $oTrous[$i]["$sCID"]["texte"]);
				echoComment("&nbsp;&nbsp;&nbsp;&nbsp;\$oTrous[$i][\"$sCID\"][\"isbrep\"] = " . $oTrous[$i]["$sCID"]["isbrep"]);
			}
		}
	}
	
	//Begin Validation
	$iPointage = 0;
	$sReponseHTML = "";
	
	$iNbGivenBRep = 0;
	$bRepInc = false;
	
	echoComment("\$iNbTrous = $iNbTrous");
	
	for($i = 0;$i < $iNbTrous;$i++){
		//Validate
		$sInputName = "input" . completeNumber($i,5);
		$sGivenRep = fromGPC($_POST["$sInputName"]);
		
		echoComment("\$sInputName = $sInputName");
		
		if($sType == "simple"){
			$sGivenRep = cleanForValid($sGivenRep);
			$sReponse = cleanForValid($oTrous[$i]);
			if(strlen($sGivenRep) < 1){
				$sBulletName = $sRepIncBullet;
				$sTextToDisplay = " -";
				$bRepInc = true;
			}elseif(strtolower($sGivenRep) == strtolower($sReponse)){
				$iNbGivenBRep++;
				$sBulletName = $sBRepBullet;
				$sTextToDisplay = $sReponse;
			}else{
				$sBulletName = $sMRepBullet;
				$sTextToDisplay = $sGivenRep;
			}
		}else{
			echoComment("\$sGivenRep for [$i] = $sGivenRep");
			if(strlen($sGivenRep) < 1){
				$sBulletName = $sRepIncBullet;
				$sTextToDisplay = " -";
			}elseif($oTrous[$i]["$sGivenRep"]["isbrep"] == "true"){
				$iNbGivenBRep++;
				$sBulletName = $sBRepBullet;
				$sTextToDisplay = $oTrous[$i]["$sGivenRep"]["texte"];
			}else{
				$sBulletName = $sMRepBullet;
				$sTextToDisplay = $oTrous[$i]["$sGivenRep"]["texte"];
			}
		}
		
		$sTextToDisplay = htmlentities($sTextToDisplay);
		
		//Generate ReponseHTML
		$sReponseHTML .= "<img src=\"images/$sBulletName\" /><img src=\"images/spacer.gif\" width=\"10\" height=\"10\" />";
		$sReponseHTML .= "$sTextToDisplay<br><br>";
		
	}
	
   echoComment("iNbGivenBRep : $iNbGivenBRep");
	
	if($bRepInc){
		$sMsg = $sRepIncMsg;
		$sMsgColor = $sRepIncMsgCol;
	}else if($iNbGivenBRep < $iNbTrous){
		$sMsg = $sMRepMsg;
		$sMsgColor = $sMRepMsgCol;
	}else{
		$sMsg = $sBRepMsg;
		$sMsgColor = $sBRepMsgCol;
	}
	
	
	
	$sReponseHTML = "<font style=\"color:$sMsgColor\">$sMsg</font><br /><br />" . $sReponseHTML;
	
	$iPointage = round(($iNbGivenBRep / $iNbTrous) * $dP,2);
	
	saveValidation($sReponseHTML, $iPointage, $iIDP,$iIDQ);
}

function validateOuverte($iIDQ,$iIDP,$sXML,$dP){
	include("settings.inc.php");
	
	echoComment("Validating Ouverte");
	echoComment(htmlentities($sXML));
	
	$sGivenReponse = cleanForValid(fromGPC($_POST["Reponse"]));
	//Begin Validation
	if(strlen($sGivenReponse) > 0){
		$iPointage = $dP;
	}else{
		$iPointage = 0;
	}   
	$sReponseHTML = htmlentities($sGivenReponse);
	
	//Generate ReponseHTML
	$sReponseHTML .= "$sReponse<br><br>";
	
	saveValidation($sReponseHTML, $iPointage, $iIDP,$iIDQ);
}

function validateMiseEnOrdre($iIDQ,$iIDP,$sXML,$dP){
	include("settings.inc.php");
	
	echoComment("Validating Mise en ordre");
	echoComment(htmlentities($sXML));
	
	//Extract Information from XML
	$oXML = simplexml_load_string($sXML);
	
	$oListeChoix = $oXML->liste_choix->choix;
	$sTextes = array();
	$sCIDs = array();
	$i = 0;
	foreach($oListeChoix as $oChoix){
		$sAtt = $oChoix->attributes();
		$sCID = $sAtt["cid"];
		$sTextes["$sCID"] = $oChoix->texte;
		$sCIDs[$i] = $sCID;
		$i++;
	}
	$iNbChoix = count($sCIDs);
	
	//Begin validation
	$iPointage = 0;
	$sReponseHTML = "";
	
	$iNbGivenBRep = 0;
	
	$sGivenCID = explode(",",trim($_POST["Reponse"],","));
	
	for($i = 0;$i < $iNbChoix;$i++){
		//Validate
		$sCurrentCID = $sGivenCID[$i];
		
		if($sCurrentCID == $sCIDs[$i]){
			$iNbGivenBRep++;
			$sBulletName = $sBRepBullet;
		}else{
			$sBulletName = $sMRepBullet;
		}
		$sTexte = htmlentities($sTextes["$sCurrentCID"]);
		
		//Generate ReponseHTML
		$sReponseHTML .= "<img src=\"images/$sBulletName\" /><img src=\"images/spacer.gif\" width=\"10\" height=\"10\" />";
		$sReponseHTML .= "$sTexte<br /><br />";
	}
	
	if($iNbGivenBRep == $iNbChoix){
		$sMsg = $sBRepMsg;
		$sMsgColor = $sBRepMsgCol;
	}else{
		$sMsg = $sMRepMsg;
		$sMsgColor = $sMRepMsgCol;
	}
	
	
	
	$sReponseHTML = "<font style=\"color:$sMsgColor\">$sMsg</font><br /><br />" . $sReponseHTML;
	
	$iPointage = round(($iNbGivenBRep / $iNbChoix) * $dP,2);
	
	saveValidation($sReponseHTML, $iPointage, $iIDP,$iIDQ);
}

function validateAssociation($iIDQ,$iIDP,$sXML,$dP){
	include("settings.inc.php");
	
	echoComment("Validating Association");
	echoComment(htmlentities($sXML));
	
	//Extract Information from XML
	$oXML = simplexml_load_string($sXML);
	
	$oListeChoix = $oXML->liste_choix->choix;
	$sTextesF = array();
	$sTextes = array();
	$sCIDs = array();
	$i = 0;
	foreach($oListeChoix as $oChoix){
		$sAtt = $oChoix->attributes();
		$sCID = $sAtt["cid"];
		$sTextes["$sCID"] = $oChoix->texte2;
		$sCIDs[$i] = $sCID;
		$sTextesF[$i] = $oChoix->texte;
		$i++;
	}
	$iNbChoix = count($sCIDs);
	
	//Begin validation
	$iPointage = 0;
	$sReponseHTML = "";
	
	$iNbGivenBRep = 0;
	
	$sGivenCID = explode(",",trim($_POST["Reponse"],","));
	
	for($i = 0;$i < $iNbChoix;$i++){
		//Validate
		$sCurrentCID = $sGivenCID[$i];
		
		if($sCurrentCID == $sCIDs[$i]){
			$iNbGivenBRep++;
			$sBulletName = $sBRepBullet;
		}else{
			$sBulletName = $sMRepBullet;
		}
		$sTexteF = htmlentities($sTextesF[$i]);
		$sTexte = htmlentities($sTextes["$sCurrentCID"]);
		
		//Generate ReponseHTML
		$sReponseHTML .= "<img src=\"images/$sBulletName\" /><img src=\"images/spacer.gif\" width=\"10\" height=\"10\" />";
		$sReponseHTML .= "$sTexteF<img src=\"images/spacer.gif\" width=\"30\" height=\"10\" />";
		$sReponseHTML .= "$sTexte<br /><br />";
		
	}
	
	if($iNbGivenBRep == $iNbChoix){
		$sMsg = $sBRepMsg;
		$sMsgColor = $sBRepMsgCol;
	}else{
		$sMsg = $sMRepMsg;
		$sMsgColor = $sMRepMsgCol;
	}
	
	
	
	$sReponseHTML = "<font style=\"color:$sMsgColor\">$sMsg</font><br /><br />" . $sReponseHTML;
	
	$iPointage = round(($iNbGivenBRep / $iNbChoix) * $dP,2);
	
	saveValidation($sReponseHTML, $iPointage, $iIDP,$iIDQ);
}

function validateDamier($iIDQ,$iIDP,$sXML,$dP){
	include("settings.inc.php");
	
	echoComment("Validating Damier");
	echoComment(htmlentities($sXML));
	
	//Extract Information from XML
	$oXML = simplexml_load_string($sXML);

	$oListeChoix = $oXML->liste_choix->choix;
	$sTextes = array();
	$sTextes2 = array();
	$sCIDs = array();
	$i = 0;
	foreach($oListeChoix as $oChoix){
		$sAtt = $oChoix->attributes();
		$sCID = $sAtt["cid"];
		$sTextes["$sCID"] = $oChoix->texte;
		$sTextes2["$sCID"] = $oChoix->texte2;
		$sCIDs[$i] = $sCID;
		$i++;
	}
	$iNbChoix = count($sCIDs);
	
	//Begin Validation
	$iPointage = 0;
	$sReponseHTML = "";
	
	$iNbGivenBRep = 0;
	
	for($i = 0;$i < $iNbChoix;$i++){
		//Validate
		$sCurrentCID = $sCIDs[$i];
		$sBFound = $_POST["$sCurrentCID"];
		
		if($sBFound == "true"){
			$iNbGivenBRep++;
		}
		
	}
	
	echoComment("iNbGivenBRep : $iNbGivenBRep");
	
	if($iNbGivenBRep < $iNbChoix){
		$sMsg = $sMRepMsg;
		$sMsgColor = $sMRepMsgCol;
	}else{
		$sMsg = $sBRepMsg;
		$sMsgColor = $sBRepMsgCol;
	}
	
	
	
	$sReponseHTML = "<font style=\"color:$sMsgColor\">$sMsg</font><br /><br />" . $sReponseHTML;
	
	$iPointage = round(($iNbGivenBRep / $iNbChoix) * $dP,2);
	
	saveValidation($sReponseHTML, $iPointage, $iIDP,$iIDQ);
}

function validateZone($iIDQ,$iIDP,$sXML,$dP){

	include("settings.inc.php");
	
	echoComment("Validating Zone");
	echoComment(htmlentities($sXML));
	
	//Extract Information from XML
	$oXML = simplexml_load_string($sXML);

	$sCoords = array();
	$sTextes = array();
	$sCIDs = array();
	
	$oListeChoix = $oXML->liste_choix->choix;
	$iNbChoix = count($oListeChoix);
	for($i = 0;$i < $iNbChoix;$i++){
		$oChoix = $oListeChoix[$i];
		
		$sAtt = $oChoix->attributes();
		$sCID = $sAtt["cid"];
		
		$sTextes["$sCID"] = $oChoix->texte;
		$sCoords["$sCID"] = $oChoix->coord;
		$sCIDs[$i] = $sCID;
	}
	
	//Begin Validation
	$iPointage = 0;
	$sReponseHTML = "";
	
	$iNbGivenBRep = 0;
	$bRepInc = false;
	
	for($i = 0;$i < $iNbChoix;$i++){
		//Validate
		$sCurrentCID = $sCIDs[$i];
		$sGivenCoord = $_POST["$sCurrentCID"];
		
		if(strlen($sGivenCoord) < 1){
			$sBulletName = $sRepIncBullet;
			$sTextToDisplay = " -";
			$bRepInc = true;
		}elseif($sGivenCoord == $sCoords["$sCurrentCID"]){
			$iNbGivenBRep++;
			$sBulletName = $sBRepBullet;
			$sTextToDisplay = $sTextes["$sCurrentCID"];
		}else{
			$sBulletName = $sMRepBullet;
			$sTextToDisplay = $sTextes["$sCurrentCID"];
		}
	
		$sTextToDisplay = htmlentities($sTextToDisplay);
		
		//Generate ReponseHTML
		$sReponseHTML .= "<img src=\"images/$sBulletName\" /><img src=\"images/spacer.gif\" width=\"10\" height=\"10\" />";
		$sReponseHTML .= "$sTextToDisplay<br><br>";
		
	}
	
	echoComment("iNbGivenBRep : $iNbGivenBRep");
	
	if($bRepInc){
		$sMsg = $sRepIncMsg;
		$sMsgColor = $sRepIncMsgCol;
	}else if($iNbGivenBRep < $iNbChoix){
		$sMsg = $sMRepMsg;
		$sMsgColor = $sMRepMsgCol;
	}else{
		$sMsg = $sBRepMsg;
		$sMsgColor = $sBRepMsgCol;
	}
		
	
	$sReponseHTML = "<font style=\"color:$sMsgColor\">$sMsg</font><br /><br />" . $sReponseHTML;
	
	$iPointage = round(($iNbGivenBRep / $iNbChoix) * $dP,2);
	
	saveValidation($sReponseHTML, $iPointage, $iIDP,$iIDQ);
}

function saveValidation($sReponseHTML,$iPointage,$iIDP, $iIDQ){
	echoComment("\$iPointage = $iPointage");
	echoComment($sReponseHTML);
	
	$sReponseHTMLClean = toSQLString($sReponseHTML);
	
	### Debug ###
    
    $tblNameList = array(
		'nq_participations'
	);
		
	$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
	
    ### Debug ###	
    
    
    $sQuery = "select * from `".$nameTables['nq_participations']."` where IDParticipant = $iIDP and IDQuestion = $iIDQ";
	echoComment($sQuery);
	$oRS = executeQuery($sQuery);
	
	if(mysql_num_rows($oRS) == 0){
		//Insert
		$sQuery =   "insert into `".$nameTables['nq_participations']."` " . 
					"(IDParticipant,IDQuestion,Pointage,PointageAuto,ReponseHTML) " . 
					"values ($iIDP,$iIDQ,$iPointage,$iPointage,$sReponseHTMLClean)";
		
		echoComment(htmlentities($sQuery));
		
		executeQuery($sQuery);
		
		echoComment("insert");
	}else{
		//Update
		$sQuery =   "update `".$nameTables['nq_participations']."` " . 
					"set Pointage = $iPointage, PointageAuto = $iPointage, ReponseHTML = $sReponseHTMLClean " . 
					"where IDParticipant = $iIDP and IDQuestion = $iIDQ";
		
		echoComment($sQuery);
		
		executeQuery($sQuery);            
		
		echoComment("update");
	}
	
}

function echoComment($s){
	global $bVerbose;
	
	if($bVerbose){
		echo "$s<br><br>";
	}
}


function validateDictee($iIDQ,$iIDP,$sXML,$dP){
        include("settings.inc.php"); 
        
        echoComment("Validating Dictee");
        echoComment(htmlentities($sXML));
        
        //Extract Information from XML
        $oXML = simplexml_load_string($sXML);

        $sBReponse = $oXML->breponse;
        $bPoncCompte = $oXML->isponccompte;
        $bPoncCompte = (($bPoncCompte == "true") ? true : false);
        $bCaseSens = $oXML->ismajcompte;
        $bCaseSens = (($bCaseSens == "true") ? true : false);
        $iFautePond = $oXML->fautepond;

    $sRepAct = fromGPC($_POST["Reponse"]);
    
    $iPointage = 0;
    $sRetroAAfficher = "";
    $u1 = "<U><FONT COLOR=\"#b22222\">";
    $u2 = "</FONT></U>";
    
    echoComment("sBReponse = ($sBReponse)");
    echoComment("sRepAct = $sRepAct");
    
    
    $compar = cleanForValidDictee($sBReponse);
    $lireentree = "";
    
    if(trim($sRepAct) == ""){
        $sRetroAAfficher = "<font style=\"color:$sRepIncMsgCol\">$sRepIncMsg</font><br><br>";
        $iPointage = 0;
        saveValidation($sRetroAAfficher,$iPointage,$iIDP, $iIDQ);
        return;
    }
    
    
    $lireentree = cleanForValidDictee($sRepAct);
    $lireentree = str_replace("  "," ",$lireentree);
    $compar = str_replace("  "," ",$compar);
    if ($bPoncCompte == false) {
            
            $sToReplace = array(",",".",";",":","!","?","«","»");
            $sBy = array("","","","","","","","");
            
            $lireentreenoponc = str_replace($sToReplace,$sBy,$lireentree);
            
            $compar = str_replace($sToReplace,$sBy,$compar);
            
            $compar = trim($compar) . " ";
            $lireentree = $lireentreenoponc;
            $lireentree = trim($lireentree) . " ";
    } else {
            $compar = trim($compar) . " ";
            $lireentree = trim($lireentree) . " ";
    }
    //$compar = htmlentities($compar);
    //$compar = convertir($compar);
    //$compar = html_entity_decode($compar);
    
    if ($lireentree == $compar) {
        $sRetroAAfficher = "<font style=\"color:$sBRepMsgCol\">$sBRepMsg</font><br><br>";
        
        saveValidation($sRetroAAfficher,$dP,$iIDP, $iIDQ);
        return;
    } else {
            if ($sRepAct != "") {
            $comparNew = $compar . " ---fin---";
            $lireentree .= " ---fin---";
            $nbMots1 = 0; // $trouver le nombre de mots dans le texte de l'usager
            $trouve = 0;
            for ($i = 0; $i < strlen($lireentree); $i++) {
                    if ($trouve == 1) {
                            if ($lireentree{$i} == " " || $lireentree{$i} == "\r" || $lireentree{$i} == "\n") {
                                    $trouve = 0;
                            }
                    } else {
                            if ($lireentree{$i} != " " && $lireentree{$i} != "\r" && $lireentree{$i} != "\n" && $trouve == 0) {
                                    $nbMots1++;
                                    $trouve = 1;
                            }
                    }
            }
            if ($nbMots1 == 0) {
                $sRetroAAfficher = "<font style=\"color:$sRepIncMsgCol\">$sRepIncMsg</font><br><br>";
                $iPointage = 0;
                saveValidation($sRetroAAfficher,$iPointage,$iIDP, $iIDQ);
                return;
            }
            $nbMots2 = 0;  // $trouver le nombre de mots dans la reponse
            $trouve = 0;
            for ($i = 0; $i < strlen($comparNew); $i++) {
                    if ($trouve == 1) {
                            if ($comparNew{$i} == ' ' || $comparNew{$i} == '\r' || $comparNew{$i} == '\n') {
                                    $trouve = 0;
                            }
                    } else {
                            if ($comparNew{$i} != ' ' && $comparNew{$i} != '\r' && $comparNew{$i} != '\n' && $trouve == 0) {
                                    $nbMots2++;
                                    $trouve = 1;
                            }
                    }
            }
            if ($nbMots1 > 0) {
                    $tab1 = makeArray4($nbMots1);  // $tab1 : texte de l'usager
                    $posReturn0 = makeArray4($nbMots1);
                    $indiceTab = 0;
                    $trouve = 0;
                    for ($j = 0; $j < strlen($lireentree)-1; $j++) {
                            if ($lireentree{$j} == ' ' || $lireentree{$j} == '\r' || $lireentree{$j} == '\n') {
                                    
                                    if (($lireentree{$j} == '\n') && $posReturn0[$indiceTab] == "+") $posReturn0[$indiceTab] = "-";
                                    if (($lireentree{$j} == '\n') && $posReturn0[$indiceTab] != "+" && $posReturn0[$indiceTab] != "-") $posReturn0[$indiceTab] = "+";
                                    
                                    if ($trouve == 1) {
                                            if ($indiceTab < $nbMots1)
                                                    $indiceTab++;
                                            else
                                                    $j = strlen($lireentree)+10;  // Pour arreter
                                            $trouve = 0;
                                    }
                            } else {
                                    
                                    $tab1[$indiceTab] .= $lireentree{$j};
                                    $trouve = 1;
                            }
                    }
            }
            if ($nbMots2 > 0) {
                    $tab2 = makeArray4($nbMots2);   // $tab2 : reponse
                    $indiceTab = 0;
                    $trouve = 0;
                    for ($j = 0; $j < strlen($comparNew)-1; $j++) {
                    if ($comparNew{$j} == ' ' || $comparNew{$j} == '\r' || $comparNew{$j} == '\n') {
                            if ($trouve == 1) {
                            if ($indiceTab < $nbMots2)
                                    $indiceTab++;
                            else
                                    $j = strlen($lireentree)+10;  // Pour arreter
                            $trouve = 0;
                            }
                    } else {
                            $tab2[$indiceTab] .= $comparNew{$j};
                            $trouve = 1;
                    }
                    }
            }
            $motsIncorrects = 0;
            $motsManquants = 0;
            $motsEnTrop = 0;
            $messa = "";
            $positions = makeArray3($nbMots1);
            for ($i = 0; $i < $nbMots1; $i++) $positions[$i] = -1;
            $pos = makeArray1($nbMots2);
            for ($i = 0; $i < $nbMots2-1; $i++) {  // $trouver les mots identiques dans $tab2
                    for ($ij = $i+1; $ij < $nbMots2; $ij++) {
                    if (strlen($tab2[$i]) == strlen($tab2[$ij])) {
                            $trouve = false;
                            for ($j = 0; $j < strlen($tab2[$i]); $j++) {
                            $car1 = $tab2[$i]{$j};
                            $car2 = $tab2[$ij]{$j};
                            if ($bCaseSens == false) {
                                    $car1 = strtolower($tab2[$i]{$j});
                                    $car2 = strtolower($tab2[$ij]{$j});
                            }
                            if ($car1 != $car2) $trouve = true;
                            }
                            if ($trouve == false) {
                            $pos[$i] = true;
                            $pos[$ij] = true;
                            }
                    }
                    }
            }
            for ($i = 0; $i < $nbMots1; $i++) {
                    for ($ii = 0; $ii < $nbMots2; $ii++) {
                    if (strlen($tab1[$i]) == strlen($tab2[$ii])) {
                            $trouve = false;
                            for ($j = 0; $j < strlen($tab1[$i]); $j++) {
                            $car1 = $tab1[$i]{$j};
                            $car2 = $tab2[$ii]{$j};
                            if ($bCaseSens == false) {
                                    $car1 = strtolower($tab1[$i]{$j});
                                    $car2 = strtolower($tab2[$ii]{$j});
                            }
                            if ($car1 != $car2) $trouve = true;
                            }
                            if ($trouve == false && $pos[$ii] == false) {
                            $positions[$i] = $ii;
                            $pos[$ii] = true;
                            $ii = $nbMots2 + 1;
                            }
                    }
                    }
            }
            for ($i = 0; $i < $nbMots1-1; $i++) {  // $trouver les mots identiques dans $tab1
                    for ($ij = $i+1; $ij < $nbMots1; $ij++) {
                    if (strlen($tab1[$i]) == strlen($tab1[$ij])) {
                            $trouve = false;
                            for ($j = 0; $j < strlen($tab1[$i]); $j++) {
                            $car1 = $tab1[$i]{$j};
                            $car2 = $tab1[$ij]{$j};
                            if ($bCaseSens == false) {
                                    $car1 = strtolower($tab1[$i]{$j});
                                    $car2 = strtolower($tab1[$ij]{$j});
                            }
                            if ($car1 != $car2) $trouve = true;
                            }
                            if ($trouve == false) {
                            $positions[$i] = -1;
                            $positions[$ij] = -1;
                            }
                    }
                    }
            }
            for ($i = 0; $i < $nbMots1-1; $i++) {
                    for ($j = $i+1; $j < $nbMots1; $j++) {
                    if ($positions[$i] > $positions[$j] && $positions[$j] > -1)
                            $positions[$i] = -1;
                    }
            }
            for ($i = 1; $i < $nbMots1; $i++) {
                    if ($positions[$i] > -1 && $positions[$i-1] == -1) {
                    $k = $positions[$i];
                    for ($j = $i-1; $j >= 0; $j--) {
                            $k--;
                            if ($k < 0) {
                            $j = -1; // arreter
                            } else {
                            $mot1 = $tab1[$j];
                            $mot2 = $tab2[$k];
                            if ($bCaseSens == false) {
                                    $mot1 = strtolower($tab1[$j]);
                                    $mot2 = strtolower($tab2[$k]);
                            }
                            if ($mot1 == $mot2 && $positions[$j] == -1)
                                    $positions[$j] = $k;
                            else
                                    $j = -1;  // arreter
                            }
                    }
                    }
            }
            
            //bug en haut de ca ya des undefined dans $tab1
            
            $compar1 = " ";
            $compar2 = " ";
            $chaineReturn = "";
            $depassement = false;
            $indice = 0;
            for ($i = 0; $i < $nbMots1; $i++) { // Le dernier mot est ---fin---
                    if ($depassement == false) {
                    if ($positions[$i] > -1) {
                            if ($positions[$i] == $indice) {
                            $compar1 .= $tab1[$i] . " ";
                            
                            if ($indice < $nbMots2-1) $compar2 .= $tab2[$indice] . " ";
                            $chaineReturn .= $posReturn0[$i];
                            if ($indice < $nbMots2-1)
                                    $indice++;
                            else
                                    $depassement = true;
                            } else {
                            if (($positions[$i] - $indice) > 0) {
                                    while ($indice < $positions[$i] && $depassement == false) {
                                    $motsManquants++;
                                    $compar1 .= "--------" . " ";
                                    if ($indice < $nbMots2-1) $compar2 .= $tab2[$indice] . " ";
                                    $chaineReturn .= " ";
                                    if ($indice < $nbMots2-1)
                                            $indice++;
                                    else
                                            $depassement = true;
                                    }
                            } else {
                                    $ij = $indice;
                                    while ($ij > $positions[$i]) {
                                    $temp = strrpos($compar2," ");
                                    $compar2 = substr($compar2,0, $temp);
                                    $temp = strrpos($compar2," ");
                                    $compar2 = substr($compar2,0, $temp);
                                    $ij--;
                                    }
                                    while ($indice > $positions[$i]) {
                                    $motsEnTrop++;
                                    if ($motsIncorrects > 0) $motsIncorrects--;
                                    $compar2 .= "--------" . " ";
                                    $indice--;
                                    }
                            }
                            $compar1 .= $tab1[$i] . " ";
                            if ($indice < $nbMots2-1) $compar2 .= $tab2[$indice] + " ";
                            $chaineReturn .= $posReturn0[$i];
                            if ($indice < $nbMots2-1)
                                    $indice++;
                            else
                                    $depassement = true;
                            }
                    } else {
                            $compar1 .= $tab1[$i] . " ";
                            if ($indice < $nbMots2-1) $compar2 .= $tab2[$indice] . " ";
                            $chaineReturn .= $posReturn0[$i];
                            $mot1 = $tab1[$i];
                            $mot2 = $tab2[$indice];
                            if ($bCaseSens == false) {
                            $mot1 = strtolower($tab1[$i]);
                            $mot2 = strtolower($tab2[$indice]);
                            }
                            if ($mot1 == $mot2) {
                            $messa .= $tab1[$i] . " ";
                            } else {
                            if ($indice < $nbMots2-1) $motsIncorrects++;
                            }
                            if ($indice < $nbMots2-1)
                            $indice++;
                            else
                            $depassement = true;
                    }
                    } else {
                    $motsEnTrop++;
                    $compar1 .= $tab1[$i] . " ";
                    $compar2 .= "--------" . " ";
                    $chaineReturn .= $posReturn0[$i];
                    }
            }
            $nbMots1 = 0;
            $trouve = 0;
            for ($i = 0; $i < strlen($compar1); $i++) {
                    if ($trouve == 1) {
                    if ($compar1{$i} == ' ') {
                            $trouve = 0;
                    }
                    } else {
                    if ($compar1{$i} != ' ' && $trouve == 0) {
                            $nbMots1++;
                            $trouve = 1;
                    }
                    }
            }
            $nbMots2 = 0;
            $trouve = 0;
            for ($i = 0; $i < strlen($compar2); $i++) {
                    if ($trouve == 1) {
                    if ($compar2{$i} == ' ') {
                            $trouve = 0;
                    }
                    } else {
                    if ($compar2{$i} != ' ' && $trouve == 0) {
                            $nbMots2++;
                            $trouve = 1;
                    }
                    }
            }
            $nbMots = $nbMots1;
            if ($nbMots < $nbMots1) $nbMots = $nbMots2;
            $tab1_new = makeArray4($nbMots);
            $tab2_new = makeArray4($nbMots);
            $posReturn = makeArray4($nbMots);
            for ($j = 0; $j < strlen($chaineReturn); $j++) {
                    $posReturn[$j] = $chaineReturn{$j};
                    if ($posReturn[$j] == '+') $posReturn[$j] = "<BR>";
                    if ($posReturn[$j] == '-') $posReturn[$j] = "<BR><BR>";
            }
            for ($j = strlen($chaineReturn); $j < $nbMots; $j++) {
                    $posReturn[$j] = " ";
            }
            $indiceTab = 0;
            $trouve = 0;
            
            
            
            for ($j = 0; $j < strlen($compar1)-1; $j++) {
                    if ($compar1{$j} == ' ') {
                        if ($trouve == 1) {
                                if ($indiceTab < $nbMots)
                                $indiceTab++;
                                else
                                $j = strlen($compar1)+10;  // Pour arreter
                                $trouve = 0;
                        }
                    } else {
                    
                    $tab1_new[$indiceTab] .= $compar1{$j};
                    $trouve = 1;
                    }
            }
            $indiceTab = 0;
            $trouve = 0;
            for ($j = 0; $j < strlen($compar2)-1; $j++) {
                    if ($compar2{$j} == ' ') {
                    if ($trouve == 1) {
                            if ($indiceTab < $nbMots)
                            $indiceTab++;
                            else
                            $j = strlen($compar2)+10;  // Pour arreter
                            $trouve = 0;
                    }
                    } else {
                    $tab2_new[$indiceTab] .= $compar2{$j};
                    $trouve = 1;
                    }
            }
            
            /*print_r($tab1_new);
            echo "<br><br>";
            print_r($tab2_new);
            echo "<br><br>";*/
            
            echoComment("nbMots1 = $nbMots1");
            echoComment("nbMots2 = $nbMots2");
            
            if ($nbMots1 != 0 && $nbMots2 != 0) {
                    
                    $messa = messageDictee($tab1_new, $tab2_new, $u1, $u2, $posReturn,$bCaseSens);
                    echoComment("messa = $messa");
                    $sReponse = $messa;
                    if (intval($messa) == 999) {
                        $sRetroAAfficher = "<font style=\"color:$sBRepMsgCol\">$sBRepMsg</font><br><br>";
        
                        saveValidation($sRetroAAfficher,$dP,$iIDP, $iIDQ);
                        return;
                    }
                    
                    $messa = "$messa";
                    $messa .= "<BR><BR><TABLE WIDTH=\"350\" BORDER=\"0\">";
                    $messa .= "<TR><TD>$sMsgMotsMOrtho&nbsp;:&nbsp;</TD><TD>$motsIncorrects</TD></TR>";
                    $messa .= "<TR><TD>$sMsgMotsManq&nbsp;:&nbsp;</TD><TD>$motsManquants</TD></TR>";
                    $messa .= "<TR><TD>$sMsgMotsTrop&nbsp;:&nbsp;</TD><TD>$motsEnTrop</TD></TR></TABLE>";
                    
                    $messa = "<font style=\"color:$sMRepMsgCol\">$sMRepMsg</font><br><br>$messa";
                    
                    $motsIncorrects += $motsManquants + $motsEnTrop;
                    
                    $iPointsPerdus = floatval($motsIncorrects) * floatval($iFautePond);
                    
                    echoComment("\$iPointsPerdus = $iPointsPerdus");
                    
                    $iPointage = max($dP - $iPointsPerdus,0);
                    
                    saveValidation($messa,$iPointage,$iIDP, $iIDQ);
                    
                    //parent.exercice[$indicedelapage].diag = "&Agrave; reprendre"
                    //if (parent.exercice[$indicedelapage].nbpt < 0.0) parent.exercice[$indicedelapage].nbpt = 0.0
            } else {
                //retro.showHTML("Aucune correction n'a pu &ecirc;tre effectu&eacute;e car aucun mot n'a &eacute;t&eacute; d&eacute;tect&eacute;. Essayez &agrave; nouveau.")
            }
            } else {
            //parent.documentWrite(infoclic)
            }
    }
}
function messageDictee($tab1, $tab2, $u1, $u2, $posReturn,$bCaseSens) {
    $messa = "";
    $mot1 = "";
    $mot2 = "";
    $detect = 0;
    $i = 0;
    
    while ($i < count($tab1)) {
            $mot1 = $tab1[$i];
            $mot2 = $tab2[$i];
            
            if ($mot1{1} == '-' && $mot1{2} == '-' && $mot1{3} == '-' && $mot1{4} == '-') {
                    $detect = 1;
                    $messa .= "$u1&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$u2" . $posReturn[$i];
                    $i++;
                    continue;
            }
            echoComment("mot2 = ($mot2)");
            if(strlen(trim($mot2)) == 0){
                echoComment("word skipped");
                $i++;
                continue;
            }
            if ($mot2{1} == '-' && $mot2{2} == '-' && $mot2{3} == '-' && $mot2{4} == '-') {
                    $detect = 1;
                    $messa .= $u1 . "[";
                    for ($jj = 1; $jj < strlen($mot1); $jj++) $messa .= $mot1{$jj};
                    $messa .= "]" . $u2 . $posReturn[$i];
                    $i++;
                    
                    continue;
            }
            if ($mot1{1} == '-' && $mot1{2} == '-' && $mot1{3} == '-' && $mot1{4} == 'f' && $mot1{5} == 'i') {
                    $i++;
                    
                    continue;
            }
            if (strlen($mot1) == strlen($mot2)) {
                    for ($j = 0; $j < strlen($mot1); $j++) {
                            $car1 = $mot1{$j};
                            $car2 = $mot2{$j};
                            if ($bCaseSens == false) {
                                    $car1 = strtolower($mot1{$j});
                                    $car2 = strtolower($mot2{$j});
                            }
                            if ($car1 == $car2) {
                                $messa .= $mot1{$j};
                            } else {
                                $messa .= $u1 . $mot1{$j} . $u2;
                                $detect = 1;
                            }
                            
                    }
                    $i++;
                    
            } else {
                $detect = 1;
                if (strlen($mot1) > strlen($mot2)) {
                        $j1 = 0;
                        $lettresAjoutees = 0;
                        $tabl1 = makeArray3(strlen($mot1));
                        $tabl2 = makeArray3(strlen($mot1));
                        for ($j = 0; $j < strlen($mot1); $j++) {
                        $car1 = $mot2{$j1};
                        $car2 = $mot1{$j};
                        if ($bCaseSens == false) {
                                $car1 = strtolower($mot2{$j1});
                                $car2 = strtolower($mot1{$j});
                        }
                        if ($car1 == $car2 || $lettresAjoutees == (strlen($mot1) - strlen($mot2))) {
                                $tabl2[$j] = $mot2{$j1};
                                $j1++;
                        } else {
                                $tabl2[$j] = "&nbsp;";
                                $lettresAjoutees++;
                        }
                        $tabl1[$j] = $mot1{$j};
                        }
                        for ($j = 0; $j < strlen($mot1); $j++) {
                                $car1 = $tabl1[$j];
                                $car2 = $tabl2[$j];
                                if ($bCaseSens == false) {
                                        $car1 = strtolower($tabl1[$j]);
                                        $car2 = strtolower($tabl2[$j]);
                                }
                                if ($car1 == $car2) {
                                        $messa .= $tabl1[$j];
                                } else {
                                        $messa .= $u1 . $tabl1[$j] . $u2;
                                }
                        }
                        
                } else {
                        $j1 = 0;
                        $lettresAjoutees = 0;
                        $tabl1 = makeArray3(strlen($mot2));
                        $tabl2 = makeArray3(strlen($mot2));
                        for ($j = 0; $j < strlen($mot2); $j++) {
                        $car1 = $mot1{$j1};
                        $car2 = $mot2{$j};
                        if ($bCaseSens == false) {
                                $car1 = strtolower($mot1{$j1});
                                $car2 = strtolower($mot2{$j});
                        }
                        if ($car1 == $car2 || $lettresAjoutees == (strlen($mot2) - strlen($mot1))) {
                                $tabl1[$j] = $mot1{$j1};
                                $j1++;
                        } else {
                                $tabl1[$j] = "&nbsp;";
                                $lettresAjoutees++;
                        }
                        $tabl2[$j] = $mot2{$j};
                        }
                        for ($j = 0; $j < strlen($mot2); $j++) {
                        $car1 = $tabl1[$j];
                        $car2 = $tabl2[$j];
                        if ($bCaseSens == false) {
                                $car1 = strtolower($tabl1[$j]);
                                $car2 = strtolower($tabl2[$j]);
                        }
                        if ($car1 == $car2) {
                                $messa .= $tabl1[$j];
                        } else {
                                $messa .= $u1 . $tabl1[$j] . $u2;
                        }
                        }
                }
                $i++;
            }
            $messa .= $posReturn[$i];
    }
    if ($detect == 0) return "999";
    $y = stripos($messa,"$u1&nbsp;");
    while ($y > -1) {
            $messa = substr($messa,0, $y + strlen($u1)) . "#" . substr($messa,$y + strlen($u1));
            $y = stripos($messa,"$u1&nbsp;");
    }
    $y = stripos($messa,"$u1#");
    while ($y > -1) {
            $messa = substr($messa,0, $y + strlen($u1)) . "&nbsp;" . substr($messa,$y + strlen($u1) + 1);
            $y = stripos($messa,"$u1#");
    }
    return $messa;
    }
//FONCTIONS DE NETQUIZ3
$car = array(50);
$car0 = array(50);

$car0 [1] = "%26agrave%3B";
$car0 [2] = "%26aacute%3B";
$car0 [3] = "%26acirc%3B";
$car0 [4] = "%26auml%3B";
$car0 [5] = "%26ccedil%3B";
$car0 [6] = "%26egrave%3B";
$car0 [7] = "%26eacute%3B";
$car0 [8] = "%26ecirc%3B";
$car0 [9] = "%26euml%3B";
$car0 [10] = "%26igrave%3B";
$car0 [11] = "%26iacute%3B";
$car0 [12] = "%26icirc%3B";
$car0 [13] = "%26iuml%3B";
$car0 [14] = "%26ntilde%3B";
$car0 [15] = "%26ograve%3B";
$car0 [16] = "%26oacute%3B";
$car0 [17] = "%26ocirc%3B";
$car0 [18] = "%26ouml%3B";
$car0 [19] = "%26ugrave%3B";
$car0 [20] = "%26uacute%3B";
$car0 [21] = "%26ucirc%3B";
$car0 [22] = "%26uuml%3B";
$car0 [23] = "%26Agrave%3B";
$car0 [24] = "%26Aacute%3B";
$car0 [25] = "%26Acirc%3B";
$car0 [26] = "%26Auml%3B";
$car0 [27] = "%26Ccedil%3B";
$car0 [28] = "%26Egrave%3B";
$car0 [29] = "%26Eacute%3B";
$car0 [30] = "%26Ecirc%3B";
$car0 [31] = "%26Euml%3B";
$car0 [32] = "%26Igrave%3B";
$car0 [33] = "%26Iacute%3B";
$car0 [34] = "%26Icirc%3B";
$car0 [35] = "%26Iuml%3B";
$car0 [36] = "%26Ntilde%3B";
$car0 [37] = "%26Ograve%3B";
$car0 [38] = "%26Oacute%3B";
$car0 [39] = "%26Ocirc%3B";
$car0 [40] = "%26Ouml%3B";
$car0 [41] = "%26Ugrave%3B";
$car0 [42] = "%26Uacute%3B";
$car0 [43] = "%26Ucirc%3B";
$car0 [44] = "%26Uuml%3B";
$car0 [45] = "%26szlig%3B";
$car0 [46] = "%26#171%3B";
$car0 [47] = "%26#187%3B";
$car0 [48] = "%26quot%3B";
  
function convertir($chaine) {
	$caraca = "";
	$caracb = "";
	for ($i = 1; $i < 49; $i++)  {
		$caraca = $car0[$i];
		if (stripos($chaine,$caraca) >= 0) {
			$caracb = $car[$i];
			$chaine = caractere($chaine, $caraca, $caracb);
		}
	}
	return($chaine);
}

function caractere($chaine, $caraca, $caracb) {
        $y = -1;
        $n = strlen($chaine);
        $chaineNew = $chaine;
        $longueur = strlen($caraca);
	
	while (stripos($chaine,$caraca) >= 0) {
		$y = stripos($chaine,$caraca);
		if ($y > 0) {
			$chaineNew = substr($chaine,0,$y) . $caracb . substr($chaine,$y+$longueur);
			$n = strlen($chaineNew);
			$chaine = $chaineNew;
		} else if ($y == 0) {
			$chaineNew = $caracb + substr($chaine,$y+$longueur);
			$n = strlen($chaineNew);
			$chaine = $chaineNew;
		}
	}
	return($chaine);
}
function makeArray1($n) {
  $a = array($n);
  for ( $i = 0; $i < $n; $i++) $a[$i] = false;
  return $a;
}
function makeArray2($n) {
  $a = array($n);
  for ( $i = 0; $i < $n; $i++) $a[$i] = "";
  return $a;
}
function makeArray3($n) {
  $a = array($n);
  for ( $i = 0; $i < $n; $i++) $a[$i] = 0;
  return $a;
}
function makeArray4($n) {
  $a = array($n);
  for ( $i = 0; $i < $n; $i++) $a[$i] = " ";
  return $a;
}

?>
