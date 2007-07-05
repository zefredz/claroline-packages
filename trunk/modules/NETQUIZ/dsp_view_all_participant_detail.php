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
require_once("lib/netquiz.class.php");
include_once("netquiz/langr.inc.php");
include_once("netquiz/settings.inc.php");
include_once("netquiz/functions.inc.php");

// recupération des données utilisateurs
$current_user_data = user_get_properties(claro_get_current_user_id());
$is_allowedToAdmin = claro_is_allowed_to_edit();

    //Variables
    $statCurrentUser = ( isset( $_REQUEST['statCurrentUser'] ) )
        ? true
        : false
        ;
        
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
    if(intval($iFinal) == 1)
    {
        $sParticipationDate = date($sDefaultDateHourFormat,$selectDetailsParticipant['ParticipationDate']);
    }
    else
    {
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

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="css/main.css" />
        <script src="js/fct_js.js" language="javascript"></script>
        <title><?php echo $sLR["title"]; ?></title>
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
                                <td width="245"><strong><?php echo $sLR["qp_lnam_lbl"]; ?> : </strong><?php echo $sNom; ?></td>
                                <td width="245"><strong><?php echo $sLR["qp_fnam_lbl"]; ?> : </strong><?php echo $sPrenom; ?></td>
                            </tr>
                            <tr>
                                <td><strong><?php echo $sLR["qp_email_lbl"]; ?> : </strong><?php echo $sCourriel; ?></td>
                                <td width="245"><strong><?php echo $sLR["qp_sd_lbl"]; ?> : </strong><?php echo $sParticipationDate; ?></td>
                            </tr>
                            <tr>
                                <td width="100%" colspan="2" height="30">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="245"><strong><?php echo $sLR["qp_tsco_lbl"]; ?> : </strong><?php echo $sPointageTotalHTML; ?></td>
                            </tr>
                            <?php if($is_allowedToAdmin == true) { ?>
                            <tr>
                                <td width="100%" colspan="2" height="30">
                                    <strong><?php echo $sLR["qp_sta_lbl"]; ?> : </strong><?php echo $sStatus; ?>&nbsp;&nbsp;<strong>><font class="blue">></font></strong> <a href="javascript:editStatusParticipant('<?php echo $iIDParticipant ?>','<?php echo $iActif ?>');"><?php echo $sLR["qp_edit_link"]; ?></a>
                                </td>
                            </tr>
                            <?php } ?>
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
                            <?php if($is_allowedToAdmin == true) { ?>
                            <td width="135"><strong>><font class="blue">></font> <a id="link<?php echo "$iIDParticipant-$iIDQuestion"; ?>" href="javascript:editPointageParticipant('<?php echo $iIDQuestion ?>','<?php echo $sPointage ?>','<?php echo $iIDParticipant ?>');"><?php echo $sLR["qp_scoec_link"]; ?></a></strong></td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td><?php echo $sNoQuestion; ?></td>
                            <td><?php echo $sNomPrefix . $sNomQuestion; ?></td>
                            <td><?php echo $sTypeQuestion; ?></td>
                            <td><?php echo $sPointageAutoHTML; ?></td>
                            <?php if($is_allowedToAdmin == true) { ?>
                            <td><?php echo $sPointageHTML; ?></td>
                            <?php } ?>
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
                    </table>
                <?php
                echo "</div>";
                
                $sCurrentColor = ($sCurrentColor == $sColor1 ? $sColor2 : $sColor1);
			}
        ?>
    </body>
</html>