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
    $iIDParticipant = $_GET["idparticipant"];
    
	// Declaration de la Class netquiz		
	$netquiz = new netquiz();
	
	// Class netquiz : select QuizName
	$netquiz->setIdQuiz( $iIDQuiz );
	$sQuizName = $netquiz->selectQuizName();
	
	//Participant detail
	$netquiz->setIdParticipant( $iIDParticipant );
	$selectDetailsParticipant = $netquiz->selectDetailsParticipant();

	$sNom = $selectDetailsParticipant['Nom'];
    $sPrenom = $selectDetailsParticipant['Prenom'];
    $sGroupe = $selectDetailsParticipant['Groupe'];
    $sMatricule = $selectDetailsParticipant['Matricule'];
    $sCourriel = $selectDetailsParticipant['Courriel'];
    $sCoordonnees = $selectDetailsParticipant['Coordonnees'];
    $iFinal = $selectDetailsParticipant['Final'];
    if(intval($iFinal) == 1){
        $sParticipationDate = date($sDefaultDateHourFormat,$selectDetailsParticipant['ParticipationDate']);
    }else{
        $sParticipationDate = "-";
    }
    $iActif = intval($selectDetailsParticipant['Actif']);
    $sStatus = (($iActif == 1) ? $sLR["qp_staa_lbl"] : $sLR["qp_staia_lbl"]);

	//Quiz total
	$netquiz->setIdQuiz( $iIDQuiz );
	$fPonderationTotal = $netquiz->selectTotalQuiz();
	
	//Participant's total score
	$netquiz->setIdParticipant( $iIDParticipant );
	$fPointageTotal = $netquiz->selectTotalScore();

	$sPointageTotalHTML = getFormatedScore($fPointageTotal,$fPonderationTotal);

	//Participations list
	$netquiz->setIdParticipant( $iIDParticipant );
	$selectParticipationsList = $netquiz->selectParticipationsList();

	
	/* 
    //Connection
    $oServerConn = mysql_connect($sMySQLLocation,$sMySQLUser,$sMySQLPassword);
    if(!$oServerConn){
        die(mysql_error());
    }
    
    mysql_select_db($sMySQLDatabase);
   
    //Quiz name
    $sQuery =   "select * from nq_quizs where IDQuiz = $iIDQuiz";
    
    $oRS = executeQuery($sQuery);
    
    $sQuizName = htmlentities(mysql_result($oRS,0,"QuizName"));

    //Participant detail
    $sQuery =   "select Nom,Prenom,Groupe,Matricule,Courriel,Coordonnees, Final, UNIX_TIMESTAMP(ParticipationDate) as ParticipationDate, Actif from nq_participants where IDParticipant = $iIDParticipant";
    
    $oRS = executeQuery($sQuery);
    
    $sNom = htmlentities(mysql_result($oRS,0,"Nom"));
    $sPrenom = htmlentities(mysql_result($oRS,0,"Prenom"));
    $sGroupe = htmlentities(mysql_result($oRS,0,"Groupe"));
    $sMatricule = htmlentities(mysql_result($oRS,0,"Matricule"));
    $sCourriel = htmlentities(mysql_result($oRS,0,"Courriel"));
    $sCoordonnees = htmlentities(mysql_result($oRS,0,"Coordonnees"));
    $iFinal = mysql_result($oRS,0,"Final");
    if(intval($iFinal) == 1){
        $sParticipationDate = date($sDefaultDateHourFormat,mysql_result($oRS,0,"ParticipationDate"));
    }else{
        $sParticipationDate = "-";
    }
    $iActif = intval(mysql_result($oRS,0,"Actif"));
    $sStatus = (($iActif == 1) ? $sLR["qp_staa_lbl"] : $sLR["qp_staia_lbl"]);
	 
	//Quiz total
    $sQuery = "select sum(Ponderation) as PonderationTotal from nq_questions where IDQuiz = $iIDQuiz and Active = 1";
    
    $oRS = executeQuery($sQuery);
    
    $fPonderationTotal = mysql_result($oRS,0,"PonderationTotal");
	
    //Participant's total score
    $sQuery =   "select sum(Pointage) as PointageTotal from nq_participations, nq_questions where IDParticipant = $iIDParticipant and nq_participations.IDQuestion = nq_questions.IDQuestion and nq_questions.Active = 1";
    
    $oRS = executeQuery($sQuery);
    
    $fPointageTotal = mysql_result($oRS,0,"PointageTotal");
    
    $sPointageTotalHTML = getFormatedScore($fPointageTotal,$fPonderationTotal);
   
    
	//Participations list
    $sQuery =   "select * from nq_participations, nq_questions where nq_participations.IDParticipant = $iIDParticipant " .
                "and nq_participations.IDQuestion = nq_questions.IDQuestion";
    
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
            var iScrollPos = 0;
            function editStatus(){
                openWindowsAndCenterNSB("editsstatus.php?id=<?php echo $iIDParticipant ?>&a=<?php echo $iActif ?>",300,150,"editstatus");
            }
            function editPointage(iIDQ,iP){
                openWindowsAndCenterNSB("editpointage.php?idq=" + iIDQ + "&idp=<?php echo $iIDParticipant ?>&p=" + iP,300,150,"editpointage");
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
    <body class="Detail" onload="pageInit();">
        <div class="DetailHeader">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td width="17" class="DetailBand">
                        <img src="images/spacer.gif" width="17" height="20" />
                    </td>
                    <td style="padding:20px;" align="left">
                        <table cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td width="245"><strong><?php echo $sLR["qp_mat_lbl"]; ?> : </strong><?php echo $sMatricule; ?></td>
                                <td><strong><?php echo $sLR["qp_email_lbl"]; ?> : </strong><?php echo $sCourriel; ?></td>
                            </tr>
                            <tr>
                                <td width="245"><strong><?php echo $sLR["qp_lnam_lbl"]; ?> : </strong><?php echo $sNom; ?></td>
                                <td><strong><?php echo $sLR["qp_gr_lbl"]; ?> : </strong><?php echo $sGroupe; ?></td>
                            </tr>
                            <tr>
                                <td width="245"><strong><?php echo $sLR["qp_fnam_lbl"]; ?> : </strong><?php echo $sPrenom; ?></td>
                                <td><strong><?php echo $sLR["qp_sd_lbl"]; ?> : </strong><?php echo $sParticipationDate; ?></td>
                            </tr>
                            <tr>
                                <td width="100%" colspan="2" height="30">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="245"><strong><?php echo $sLR["qp_tsco_lbl"]; ?> : </strong><?php echo $sPointageTotalHTML; ?></td>
                            </tr>
                            <tr>
                                <td width="100%" colspan="2" height="30">
                                    <strong><?php echo $sLR["qp_sta_lbl"]; ?> : </strong><?php echo $sStatus; ?>&nbsp;&nbsp;<strong>><font class="blue">></font></strong> <a href="javascript:editStatus();"><?php echo $sLR["qp_edit_link"]; ?></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        <?php
            
			$sColor1 = "#FFFFFF";
            $sColor2 = "#F5F5F5";
            $sCurrentColor = $sColor1;
        
            foreach( $selectParticipationsList as $rows )
			{
                
				$iIDQuestion = $rows['IDQuestion'];
                $sNoQuestion = $rows['NoQuestion'] + 1;
                $sNomQuestion = $rows['QuestionName'];
                $sTypeQuestion = $rows['QuestionTypeTD'];
                
                $sEnonceHTML = $rows['EnonceHTML'];
                $sBReponseHTML = $rows['ReponseHTML'];
                
                $fPonderation = $rows['Ponderation'];
                $sPonderation = toLangFloat($fPonderation);
                
                $fPointage = $rows['Pointage'];
                $sPointage = toLangFloat($fPointage);
                $sPointageHTML = getFormatedScore($fPointage,$fPonderation,false);
                
                $sPointageAutoHTML = getFormatedScore($rows['PointageAuto'],$fPonderation,false);
                
                
                $sReponseHTML = $rows['ReponseHTML'];
                
                $iActive = intval($rows['Active']);
                $sNomPrefix = ($iActive == 0 ? $sCanceledQPrefix : "");
                
                echo "<div class=\"DetailList\" style=\"background-color:$sCurrentColor\"><br />";
                ?>
                    <table width="710" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td width="50"><strong><?php echo $sLR["qp_noc_lbl"]; ?></strong></td>
                            <td width="350"><strong><?php echo $sLR["qp_titc_lbl"]; ?></strong></td>
                            <td width="110"><strong><?php echo $sLR["qp_typc_lbl"]; ?></strong></td>
                            <td width="65"><strong><?php echo $sLR["qp_scoc_lbl"]; ?></strong></td>
                            <td width="135"><strong>><font class="blue">></font> <a id="link<?php echo "$iIDParticipant-$iIDQuestion"; ?>" href="javascript:editPointage(<?php echo $iIDQuestion ?>,'<?php echo $sPointage ?>');"><?php echo $sLR["qp_scoec_link"]; ?></a></strong></td>
                            
                        </tr>
                        <tr>
                            <td><?php echo $sNoQuestion; ?></td>
                            <td><?php echo $sNomPrefix . $sNomQuestion; ?></td>
                            <td><?php echo $sTypeQuestion; ?></td>
                            <td><?php echo $sPointageAutoHTML; ?></td>
                            <td><?php echo $sPointageHTML; ?></td>
                        </tr>
                        <tr>
                            <td colspan="5" height="20">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="5"><strong><?php echo $sLR["qp_txtc_lbl"]; ?></strong></td>
                        </tr>
                        <tr>
                            <td colspan="5"><?php echo $sEnonceHTML; ?></td>
                        </tr>
                        <tr>
                            <td colspan="5" height="20">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="5"><strong><?php echo $sLR["qp_gac_lbl"]; ?></strong></td>
                        </tr>
                        <tr>
                            <td colspan="5"><?php echo $sReponseHTML; ?></td>
                        </tr>
                        <tr>
                            <td colspan="5" height="20">&nbsp;</td>
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
                $iIDQuestion = intval(mysql_result($oRS,$i,"IDQuestion"));
                $sNoQuestion = intval(mysql_result($oRS,$i,"NoQuestion")) + 1;
                $sNomQuestion = htmlentities(mysql_result($oRS,$i,"QuestionName"));
                $sTypeQuestion = mysql_result($oRS,$i,"QuestionTypeTD");
                
                $sEnonceHTML = mysql_result($oRS,$i,"EnonceHTML");
                $sBReponseHTML = mysql_result($oRS,$i,"ReponseHTML");
                
                $fPonderation = mysql_result($oRS,$i,"Ponderation");
                $sPonderation = toLangFloat($fPonderation);
                
                $fPointage = mysql_result($oRS,$i,"Pointage");
                $sPointage = toLangFloat($fPointage);
                $sPointageHTML = getFormatedScore($fPointage,$fPonderation,false);
                
                $sPointageAutoHTML = getFormatedScore(mysql_result($oRS,$i,"PointageAuto"),$fPonderation,false);
                
                
                $sReponseHTML = mysql_result($oRS,$i,"ReponseHTML");
                
                $iActive = intval(mysql_result($oRS,$i,"Active"));
                $sNomPrefix = ($iActive == 0 ? $sCanceledQPrefix : "");
                
                echo "<div class=\"DetailList\" style=\"background-color:$sCurrentColor\"><br />";
                ?>
                    <table width="710" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td width="50"><strong><?php echo $sLR["qp_noc_lbl"]; ?></strong></td>
                            <td width="350"><strong><?php echo $sLR["qp_titc_lbl"]; ?></strong></td>
                            <td width="110"><strong><?php echo $sLR["qp_typc_lbl"]; ?></strong></td>
                            <td width="65"><strong><?php echo $sLR["qp_scoc_lbl"]; ?></strong></td>
                            <td width="135"><strong>><font class="blue">></font> <a id="link<?php echo "$iIDParticipant-$iIDQuestion"; ?>" href="javascript:editPointage(<?php echo $iIDQuestion ?>,'<?php echo $sPointage ?>');"><?php echo $sLR["qp_scoec_link"]; ?></a></strong></td>
                            
                        </tr>
                        <tr>
                            <td><?php echo $sNoQuestion; ?></td>
                            <td><?php echo $sNomPrefix . $sNomQuestion; ?></td>
                            <td><?php echo $sTypeQuestion; ?></td>
                            <td><?php echo $sPointageAutoHTML; ?></td>
                            <td><?php echo $sPointageHTML; ?></td>
                        </tr>
                        <tr>
                            <td colspan="5" height="20">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="5"><strong><?php echo $sLR["qp_txtc_lbl"]; ?></strong></td>
                        </tr>
                        <tr>
                            <td colspan="5"><?php echo $sEnonceHTML; ?></td>
                        </tr>
                        <tr>
                            <td colspan="5" height="20">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="5"><strong><?php echo $sLR["qp_gac_lbl"]; ?></strong></td>
                        </tr>
                        <tr>
                            <td colspan="5"><?php echo $sReponseHTML; ?></td>
                        </tr>
                        <tr>
                            <td colspan="5" height="20">&nbsp;</td>
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