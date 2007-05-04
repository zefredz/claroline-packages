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
    //include("isauth.php");
    
    //Variables
    $iIDQuiz = $_GET["idquiz"];
    $iIDQuestion = $_GET["idquestion"];
    
	// Declaration de la Class netquiz		
	$netquiz = new netquiz();
	
	// Class netquiz : select Question détail
	$netquiz->setIdQuestion( $iIDQuestion );
	$selectDetailsQuestion = $netquiz->selectDetailsQuestion();
	
    $sQuizName = $selectDetailsQuestion['QuizName'];
    $sNomQuestion = $selectDetailsQuestion['QuestionName'];
    $sTypeQuestion = $selectDetailsQuestion['QuestionTypeTD'];
    $sEnonceHTML = $selectDetailsQuestion['EnonceHTML'];
    $sBReponseHTML = $selectDetailsQuestion['ReponseHTML'];
    $fPonderation = $selectDetailsQuestion['Ponderation'];
    $sPonderation = toLangFloat($fPonderation);
    $sNoQuestion = intval($selectDetailsQuestion['NoQuestion']) + 1;
    $iActive = intval($selectDetailsQuestion['Active']);
    $sStatus = (($iActive == 1) ? $sLR["qq_staa_lbl"] : $sLR["qq_staia_lbl"]);

	
	// Class netquiz : select Nombre de participant et moyenne
	$netquiz->setIdQuestion( $iIDQuestion );
	$selectNumberParticipantAndMoyenne = $netquiz->selectNumberParticipantAndMoyenne();

    $iNbRepondants = $selectNumberParticipantAndMoyenne['NbRepondants'];
    
    if($iNbRepondants > 0){
        $sMoyenneHTML = getFormatedScore($selectNumberParticipantAndMoyenne['Moyenne'],$fPonderation);
    }else{
        $sMoyenneHTML = getFormatedScore(0,$fPonderation);
    }
	
	// Participations list
	$netquiz->setIdQuestion( $iIDQuestion );
	$selectQuestionDetailParticipationsList = $netquiz->selectQuestionDetailParticipationsList();
	
/*	
   //Connection
    $oServerConn = mysql_connect($sMySQLLocation,$sMySQLUser,$sMySQLPassword);
    if(!$oServerConn){
        die(mysql_error());
    }
    
    mysql_select_db($sMySQLDatabase);
    
    //Question detail
    $sQuery =   "select * from nq_questions, nq_quizs where IDQuestion = $iIDQuestion and nq_questions.IDQuiz = nq_quizs.IDQuiz";
    
    $oRS = executeQuery($sQuery);
    
    $sQuizName = htmlentities(mysql_result($oRS,0,"QuizName"));
    $sNomQuestion = htmlentities(mysql_result($oRS,0,"QuestionName"));
    $sTypeQuestion = mysql_result($oRS,0,"QuestionTypeTD");
    $sEnonceHTML = mysql_result($oRS,0,"EnonceHTML");
    $sBReponseHTML = mysql_result($oRS,0,"ReponseHTML");
    $fPonderation = mysql_result($oRS,0,"Ponderation");
    $sPonderation = toLangFloat($fPonderation);
    $sNoQuestion = intval(mysql_result($oRS,0,"NoQuestion")) + 1;
    $iActive = intval(mysql_result($oRS,0,"Active"));
    $sStatus = (($iActive == 1) ? $sLR["qq_staa_lbl"] : $sLR["qq_staia_lbl"]);
        //Average and number of participant
    $sQuery =   "select count(nq_participations.IDParticipant) as NbRepondants, avg(Pointage) as Moyenne from nq_participations, nq_participants where nq_participations.IDQuestion = $iIDQuestion " .
                "and nq_participations.IDParticipant = nq_participants.IDParticipant and nq_participants.Actif = 1";
    
    $oRS = executeQuery($sQuery);
    
    $iNbRepondants = mysql_result($oRS,0,"NbRepondants");
    
    if($iNbRepondants > 0){
        $sMoyenneHTML = getFormatedScore(mysql_result($oRS,0,"Moyenne"),$fPonderation);
    }else{
        $sMoyenneHTML = getFormatedScore(0,$fPonderation);
    }
   
 
	//Participations list
    $sQuery =   "select *, UNIX_TIMESTAMP(nq_participants.ParticipationDate) as ParticipationDateUT from nq_participations, nq_participants where nq_participations.IDQuestion = $iIDQuestion " .
                "and nq_participations.IDParticipant = nq_participants.IDParticipant";
    
    $oRS = executeQuery($sQuery);
 */	
	
	
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="includes/main.css" />
        <script src="includes/functions.js" language="javascript"></script>
        <title><?php echo $sLR["title"]; ?></title>
        <script>
            function editStatus(){
                openWindowsAndCenterNSB("editqstatus.php?id=<?php echo $iIDQuestion ?>&a=<?php echo $iActive ?>",300,150,"editstatus");
            }
            function editPointage(iIDP,iP){
                iScrollPos = getScrollPos();
            
                openWindowsAndCenterNSB("editpointage.php?idq=<?php echo $iIDQuestion ?>&idp=" + iIDP + "&p=" + iP,300,150,"editpointage");
            }
            function pageInit(){
                var iScrollPos = readCookie("scrollYPos");
                if(iScrollPos){
                    window.scrollTo(0,iScrollPos);
                    eraseCookie("scrollYPos");
                }
            }
        </script>
    </head>
    <body class="Detail" onload="pageInit()">
        <div class="DetailHeader">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td width="17" class="DetailBand">
                        <img src="images/spacer.gif" width="17" height="20" />
                    </td>
                    <td style="padding:20px;">
                        <strong><?php echo $sNoQuestion; ?></strong><br/ >
                        <strong><?php echo $sLR["qq_title_lbl"]; ?> : </strong><?php echo $sNomQuestion; ?><br/ >
                        <strong><?php echo $sLR["qq_type_lbl"]; ?> : </strong><?php echo $sTypeQuestion; ?><br/ >
                        <strong><?php echo $sLR["qq_txt_lbl"]; ?> : </strong><?php echo $sEnonceHTML; ?><br/ ><br/ >
                        <strong><?php echo $sLR["qq_nbp_lbl"]; ?> : </strong><?php echo $iNbRepondants; ?><br/ >
                        <strong><?php echo $sLR["qq_val_lbl"]; ?> : </strong><?php echo $sPonderation; ?><br/ >
                        <strong><?php echo $sLR["qq_avg_lbl"]; ?> : </strong><?php echo $sMoyenneHTML; ?><br/ ><br/ >
                        <strong><?php echo $sLR["qq_sta_lbl"]; ?> : </strong><?php echo $sStatus; ?>&nbsp;&nbsp;<strong>><font class="blue">></font></strong> <a href="javascript:editStatus();"><?php echo $sLR["qq_edit_link"]; ?></a><br/ ><br/ >
                    </td>
                </tr>
            </table>

        </div>
        <?php
            
            $sColor1 = "#FFFFFF";
            $sColor2 = "#F5F5F5";
            $sCurrentColor = $sColor1;
        
            foreach($selectQuestionDetailParticipationsList as $rows)
			{
                
				$iIDParticipant = $rows['IDParticipant'];
                $sNom = $rows['Nom'];
                $sPrenom = $rows['Prenom'];
                $sGroupe = $rows['Groupe'];
                $sMatricule = $rows['Matricule'];
                $sReponse = $rows['ReponseHTML'];
                
                $fPointage = $rows['Pointage'];
                $sPointage = toLangFloat($fPointage);
                $sPointageHTML = getFormatedScore($fPointage,$fPonderation,false);
                
                $sPointageAutoHTML = getFormatedScore($rows['PointageAuto'],$fPonderation,false);
                
                $iFinal = $rows['Final'];
                if(intval($iFinal) == 1){
                    $sParticipationDate = date($sDefaultDateHourFormat,$rows['ParticipationDateUT']);
                }else{
                    $sParticipationDate = "-";
                }
                
                $iActif = intval($rows['Actif']);
                $sNomPrefix = ($iActif == 0 ? $sCanceledSPrefix : "");
                
                echo "<div class=\"DetailList\" style=\"background-color:$sCurrentColor\"><br />";
                ?>
                <table width="710" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="100"><strong><?php echo $sLR["qq_matc_lbl"]; ?></strong></td>
                        <td width="195"><strong><?php echo $sLR["qq_namc_lbl"]; ?></strong></td>
                        <td width="75"><strong><?php echo $sLR["qq_grc_lbl"]; ?></strong></td>
                        <td width="140"><strong><?php echo $sLR["qq_ds_lbl"]; ?></strong></td>
                        <td width="65"><strong><?php echo $sLR["qq_scoc_lbl"]; ?></strong></td>
                        <td width="135"><strong>><font class="blue">></font> <a href="javascript:editPointage(<?php echo $iIDParticipant ?>,'<?php echo $sPointage ?>');"><?php echo $sLR["qq_scoec_link"]; ?></a></strong></td>
                    </tr>
                    <tr>
                        <td><?php echo $sMatricule; ?></td>
                        <td><?php echo "$sNomPrefix$sPrenom $sNom"; ?></td>
                        <td><?php echo $sGroupe; ?></td>
                        <td><?php echo $sParticipationDate; ?></td>
                        <td><?php echo $sPointageAutoHTML; ?></td>
                        <td><?php echo $sPointageHTML; ?></td>
                    </tr>
                    <tr>
                        <td colspan="6" height="20">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="6"><strong><?php echo $sLR["qq_gac_lbl"]; ?></strong></td>
                    </tr>
                    <tr>
                        <td colspan="6"><?php echo $sReponse; ?></td>
                    </tr>
                    <tr>
                        <td colspan="6" height="20">&nbsp;</td>
                    </tr>
                </table>
                
                <?php
                echo "</div>";
                
                $sCurrentColor = ($sCurrentColor == $sColor1 ? $sColor2 : $sColor1);
            }
			
			
			/*
			$sColor1 = "#FFFFFF";
            $sColor2 = "#F5F5F5";
            $sCurrentColor = $sColor1;
        
            for($i = 0;$i < mysql_num_rows($oRS);$i++){
                $iIDParticipant = mysql_result($oRS,$i,"IDParticipant");
                $sNom = htmlentities(mysql_result($oRS,$i,"Nom"));
                $sPrenom = htmlentities(mysql_result($oRS,$i,"Prenom"));
                $sGroupe = htmlentities(mysql_result($oRS,$i,"Groupe"));
                $sMatricule = htmlentities(mysql_result($oRS,$i,"Matricule"));
                $sReponse = mysql_result($oRS,$i,"ReponseHTML");
                
                $fPointage = mysql_result($oRS,$i,"Pointage");
                $sPointage = toLangFloat($fPointage);
                $sPointageHTML = getFormatedScore($fPointage,$fPonderation,false);
                
                $sPointageAutoHTML = getFormatedScore(mysql_result($oRS,$i,"PointageAuto"),$fPonderation,false);
                
                $iFinal = mysql_result($oRS,$i,"Final");
                if(intval($iFinal) == 1){
                    $sParticipationDate = date($sDefaultDateHourFormat,mysql_result($oRS,$i,"ParticipationDateUT"));
                }else{
                    $sParticipationDate = "-";
                }
                
                $iActif = intval(mysql_result($oRS,$i,"Actif"));
                $sNomPrefix = ($iActif == 0 ? $sCanceledSPrefix : "");
                
                echo "<div class=\"DetailList\" style=\"background-color:$sCurrentColor\"><br />";
                ?>
                <table width="710" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="100"><strong><?php echo $sLR["qq_matc_lbl"]; ?></strong></td>
                        <td width="195"><strong><?php echo $sLR["qq_namc_lbl"]; ?></strong></td>
                        <td width="75"><strong><?php echo $sLR["qq_grc_lbl"]; ?></strong></td>
                        <td width="140"><strong><?php echo $sLR["qq_ds_lbl"]; ?></strong></td>
                        <td width="65"><strong><?php echo $sLR["qq_scoc_lbl"]; ?></strong></td>
                        <td width="135"><strong>><font class="blue">></font> <a href="javascript:editPointage(<?php echo $iIDParticipant ?>,'<?php echo $sPointage ?>');"><?php echo $sLR["qq_scoec_link"]; ?></a></strong></td>
                    </tr>
                    <tr>
                        <td><?php echo $sMatricule; ?></td>
                        <td><?php echo "$sNomPrefix$sPrenom $sNom"; ?></td>
                        <td><?php echo $sGroupe; ?></td>
                        <td><?php echo $sParticipationDate; ?></td>
                        <td><?php echo $sPointageAutoHTML; ?></td>
                        <td><?php echo $sPointageHTML; ?></td>
                    </tr>
                    <tr>
                        <td colspan="6" height="20">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="6"><strong><?php echo $sLR["qq_gac_lbl"]; ?></strong></td>
                    </tr>
                    <tr>
                        <td colspan="6"><?php echo $sReponse; ?></td>
                    </tr>
                    <tr>
                        <td colspan="6" height="20">&nbsp;</td>
                    </tr>
                </table>
                
                <?php
                echo "</div>";
                
                $sCurrentColor = ($sCurrentColor == $sColor1 ? $sColor2 : $sColor1);
            }
			*/
        ?>
    </body>
</html>

<?php
    //mysql_close();
}
?>