<?php

// inclusion du noyeux de claroline
require_once dirname(__FILE__) . "/../../claroline/inc/claro_init_global.inc.php";
require_once get_path('includePath').'/lib/user.lib.php';

// Vérification que l'utilisateur soit enregistré
if(!claro_is_user_authenticated()) 
{
	claro_die(get_lang("Not allowed"));
}

// lib
require_once "lib/netquiz.class.php";
include_once("netquiz/langr.inc.php");
include_once("netquiz/settings.inc.php");
include_once("netquiz/functions.inc.php");

// recupération des données utilisateurs
$current_user_data = user_get_properties(claro_get_current_user_id());
$is_allowedToAdmin = claro_is_allowed_to_edit();

    //Variables
    $statCurrentUser = $_REQUEST['statCurrentUser'];
    
    $iIDQuiz = $_GET["idquiz"];
    $iIDQuestion = $_GET["idquestion"];
    
	// Declaration de la Class netquiz		
	$netquiz = new netquiz();

if( $statCurrentUser == false && $is_allowedToAdmin == true ) 
{    	
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
    
    if($iNbRepondants > 0)
    {
        $sMoyenneHTML = getFormatedScore($selectNumberParticipantAndMoyenne['Moyenne'],$fPonderation);
    }
    else
    {
        $sMoyenneHTML = getFormatedScore(0,$fPonderation);
    }
	
	// Participations list
	$netquiz->setIdQuestion( $iIDQuestion );
	$selectQuestionDetailParticipationsList = $netquiz->selectQuestionDetailParticipationsList();
}
else
{	
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
    $netquiz->setCurrentUserId( claro_get_current_user_id() );
	$selectNumberParticipantAndMoyenne = $netquiz->selectNumberParticipantAndMoyenneCurrentUser();

    $iNbRepondants = $selectNumberParticipantAndMoyenne['NbRepondants'];
    
    if($iNbRepondants > 0)
    {
        $sMoyenneHTML = getFormatedScore($selectNumberParticipantAndMoyenne['Moyenne'],$fPonderation);
    }
    else
    {
        $sMoyenneHTML = getFormatedScore(0,$fPonderation);
    }
	
	// Participations list
	$netquiz->setIdQuestion( $iIDQuestion );
	$selectQuestionDetailParticipationsList = $netquiz->selectQuestionDetailParticipationsListCurrentUser();
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="css/main.css" />
        <script src="js/fct_js.js" language="javascript"></script>
        <title><?php echo $sLR["title"]; ?></title>
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
                        <?php if($is_allowedToAdmin == true) { ?>
                        <strong><?php echo $sLR["qq_sta_lbl"]; ?> : </strong><?php echo $sStatus; ?>&nbsp;&nbsp;<strong>><font class="blue">></font></strong> <a href="javascript:editStatusQuestion('<?php echo $iIDQuestion ?>','<?php echo $iActive ?>');"><?php echo $sLR["qq_edit_link"]; ?></a><br/ ><br/ >
                        <?php } ?>
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
                if(intval($iFinal) == 1)
                {
                    $sParticipationDate = date($sDefaultDateHourFormat,$rows['ParticipationDateUT']);
                }
                else
                {
                    $sParticipationDate = "-";
                }
                
                $iActif = intval($rows['Actif']);
                $sNomPrefix = ($iActif == 0 ? $sCanceledSPrefix : "");
                
                echo "<div class=\"DetailList\" style=\"background-color:$sCurrentColor\"><br />";
                ?>
                    <table width="710" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <!-- <td width="100"><strong><?php echo $sLR["qq_matc_lbl"]; ?></strong></td> -->
                            <td width="195"><strong><?php echo $sLR["qq_namc_lbl"]; ?></strong></td>
                             <!-- <td width="75"><strong><?php echo $sLR["qq_grc_lbl"]; ?></strong></td> -->
                            <td width="140"><strong><?php echo $sLR["qq_ds_lbl"]; ?></strong></td>
                            <td width="65"><strong><?php echo $sLR["qq_scoc_lbl"]; ?></strong></td>
                            <?php if($is_allowedToAdmin == true) { ?>
                            <td width="135"><strong>><font class="blue">></font> <a href="javascript:editPointageQuestion('<?php echo $iIDParticipant ?>','<?php echo $sPointage ?>','<?php echo $iIDQuestion ?>');"><?php echo $sLR["qq_scoec_link"]; ?></a></strong></td>
                            <?php } ?>
                        </tr>
                        <tr>
                             <!-- <td><?php echo $sMatricule; ?></td> -->
                            <td><?php echo "$sNomPrefix$sPrenom $sNom"; ?></td>
                             <!-- <td><?php echo $sGroupe; ?></td> -->
                            <td><?php echo $sParticipationDate; ?></td>
                            <td><?php echo $sPointageAutoHTML; ?></td>
                            <?php if($is_allowedToAdmin == true) { ?>
                            <td><?php echo $sPointageHTML; ?></td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td colspan="6" height="20">&nbsp;</td>
                        </tr>
                    </table>
                <?php
                echo "</div>";
                
                $sCurrentColor = ($sCurrentColor == $sColor1 ? $sColor2 : $sColor1);
            }
        ?>
    </body>
</html>