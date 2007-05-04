<?php
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

    
    //Connection
    $oServerConn = mysql_connect($sMySQLLocation,$sMySQLUser,$sMySQLPassword);
    if(!$oServerConn){
        die(mysql_error());
    }
    
    mysql_select_db($sMySQLDatabase);
    
    //Update Participant info if final soumission
    if(isset($_POST["Final"])){
        $sQuery =   "update nq_participants " . 
                    "set ParticipationDate = now(), Final = 1 " . 
                    "where IDParticipant = $iIDParticipant";
            
        echoComment("Final submit<br><br>$sQuery");
        
        executeQuery($sQuery);
    }
    if($iNoQuestion == -1){
        urlRedirect($sNextPageFull,false);
    }
    
    //Get the IDQuestion
    $sQuery =   "select IDQuestion from nq_questions, nq_quizs where nq_quizs.QuizIdent = '$sQuizIdent' and
                nq_quizs.QuizVersion = '$sQuizVersion' and nq_quizs.IDQuiz = nq_questions.IDQuiz and
                nq_questions.NoQuestion = $iNoQuestion";
    
    $oRS = executeQuery($sQuery);
    
    $iIDQuestion = mysql_result($oRS,0,"IDQuestion");
   
    //Get ReponseXML and Ponderation
    $sQuery = "select ReponseXML, Ponderation from nq_questions where IDQuestion = $iIDQuestion";
    
    $oRS = executeQuery($sQuery);
    
    $sReponseXML = "<?xml version='1.0' encoding=\"ISO-8859-1\"?>" . mysql_result($oRS,0,"ReponseXML");
    $dPonderation = mysql_result($oRS,0,"Ponderation");
    
    //Validate the question
    switch($iTypeQuestion){
        case 0: //Multiple
            validateMultiple($iIDQuestion,$iIDParticipant,$sReponseXML,$dPonderation);
            break;
        case 1: //Lacune
            validateLacune($iIDQuestion,$iIDParticipant,$sReponseXML,$dPonderation);
            break;
        case 2: //Closure
            validateClosure($iIDQuestion,$iIDParticipant,$sReponseXML,$dPonderation);
            break;
        case 3: //Dictee
            validateDictee($iIDQuestion,$iIDParticipant,$sReponseXML,$dPonderation);
            break;
        case 4: //Ouverte
            validateOuverte($iIDQuestion,$iIDParticipant,$sReponseXML,$dPonderation);
            break;
        case 5: //Mise en ordre
            validateMiseEnOrdre($iIDQuestion,$iIDParticipant,$sReponseXML,$dPonderation);
            break;
        case 6: //Association
            validateAssociation($iIDQuestion,$iIDParticipant,$sReponseXML,$dPonderation);
            break;
        case 7: //Damier
            validateDamier($iIDQuestion,$iIDParticipant,$sReponseXML,$dPonderation);
            break;
        case 8: //Zone à identifier
            validateZone($iIDQuestion,$iIDParticipant,$sReponseXML,$dPonderation);
            break;
    }
    
    //Redirect to NextPage
    echoComment("The next page is <a href=\"$sNextPageFull\">$sNextPageFull</a>");
    if(!$bVerbose){
        urlRedirect($sNextPageFull,false);
    }
    
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
            $sTextes["$sCID"] = XMLStrtoStr($oChoix->texte);
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
        
        $sReponse = cleanForValid(XMLStrtoStr($oXML->breponse));
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
                $oTrous[$i] = XMLStrtoStr($oTrou->reponse);
            }else{
                $oTrous[$i] = array();
                
                $oListeChoix = $oTrou->liste_choix->choix;
                echoComment("&nbsp;&nbsp;Trou no $i (" . count($oListeChoix) . " choix)");
                foreach($oListeChoix as $oChoix){
                    $sAtt = $oChoix->attributes();
                    $sCID = $sAtt["cid"];
                    $oTrous[$i]["$sCID"] = array();
                    
                    $oTrous[$i]["$sCID"]["texte"] = XMLStrtoStr($oChoix->texte);
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
            $sTextes["$sCID"] = XMLStrtoStr($oChoix->texte);
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
            $sTextes["$sCID"] = XMLStrtoStr($oChoix->texte2);
            $sCIDs[$i] = $sCID;
            $sTextesF[$i] = XMLStrtoStr($oChoix->texte);
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
            $sTextes["$sCID"] = XMLStrtoStr($oChoix->texte);
            $sTextes2["$sCID"] = XMLStrtoStr($oChoix->texte2);
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
            
            $sTextes["$sCID"] = XMLStrtoStr($oChoix->texte);
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
        
        $sQuery = "select * from nq_participations where IDParticipant = $iIDP and IDQuestion = $iIDQ";
        echoComment($sQuery);
        $oRS = executeQuery($sQuery);
        
        if(mysql_num_rows($oRS) == 0){
            //Insert
            $sQuery =   "insert into nq_participations " . 
                        "(IDParticipant,IDQuestion,Pointage,PointageAuto,ReponseHTML) " . 
                        "values ($iIDP,$iIDQ,$iPointage,$iPointage,$sReponseHTMLClean)";
            
            echoComment(htmlentities($sQuery));
            
            executeQuery($sQuery);
            
            echoComment("insert");
        }else{
            //Update
            $sQuery =   "update nq_participations " . 
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