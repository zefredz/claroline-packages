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
    $iIDQuiz = $_GET["id"];
    $sGetParam = "?id=$iIDQuiz";
    	
	/*
    //Connection
    $oServerConn = mysql_connect($sMySQLLocation,$sMySQLUser,$sMySQLPassword);
    if(!$oServerConn){
        die(mysql_error());
    }
    
    mysql_select_db($sMySQLDatabase);
    
    //Status or password change
    $bUpdateStatPass = false;
    if(isset($_GET["stat"])){
        $bUpdateStatPass = true;
        $iActif = $_GET["stat"];
        
        $sQuery = "update nq_quizs set Actif = $iActif where IDQuiz = $iIDQuiz";
    
        executeQuery($sQuery);
		
    }
   
    if(isset($_GET["pass"])){
        $bUpdateStatPass = true;
        $sPassword = toSQLString(fromGPC($_GET["pass"]),false);
        
        $sQuery = "update nq_quizs set Password = $sPassword where IDQuiz = $iIDQuiz";
    
        executeQuery($sQuery);
    }
    */
    
	// Declaration de la Class netquiz		
	$netquiz = new netquiz();

	//Status change
    $bUpdateStatPass = false;
    if(isset($_GET["stat"])){
        $bUpdateStatPass = true;
        $iActif = $_GET["stat"];
        
		$netquiz->setIdQuiz( $iIDQuiz );
		$netquiz->setActif( $iActif );
		$netquiz->updateQuizsStatus();
		
    }
	
	//OrderBy
    $sCols = array("Matricule","Nom","Groupe","ParticipationDate","PointageTotal");
    $sLabels = array($sLR["q_matc_lbl"],$sLR["q_namc_lbl"],$sLR["q_grc_lbl"],$sLR["q_sdc_lbl"],$sLR["q_scoc_lbl"]);
    $sLinks = array();
    $sOrderByField = $sCols[1];
    $sOrderByDirection = "ASC";
    $sOrderByID = -1;
    
    if(isset($_GET["ob"])){
        $sOrderByField = $sCols[$_GET["ob"]];
        $sOrderByID = $_GET["ob"];
    }
    
    if(isset($_GET["obd"])){
        $sOrderByDirection = $_GET["obd"];
    }
    
    for($i = 0;$i < count($sCols);$i++){
        $sOBD = "ASC";
        
        if($i == $sOrderByID){
            $sOBD = ($sOrderByDirection == "ASC" ? "DESC" : "ASC");
        }
        
        $sLinks[$i] = "<a href=\"viewquizparticipations.php$sGetParam&ob=$i&obd=$sOBD\">$sLabels[$i]</a>";
    }
    	
	// Class netquiz : select Info du quiz Participations
	$netquiz->setIdQuiz( $iIDQuiz );
	$ViewQuizInfo = $netquiz->selectViewQuizInfo();

    $sQuizName = $ViewQuizInfo['QuizName'];
    $dVersionDate = date($sDefaultDateFormat,$ViewQuizInfo['VersionDate']);
    $sPassword = $ViewQuizInfo['Password'];
    $iActif = $ViewQuizInfo['Actif'];
    
	if($iActif == 0){
        $sCheckedActif = "";
        $sCheckedInactif = " checked";
    }else{
        $sCheckedActif = " checked";
        $sCheckedInactif = "";
    }

	/*
    //Quiz information
    $oRS = executeQuery("select QuizName, UNIX_TIMESTAMP(VersionDate) as VersionDate, Password, Actif from nq_quizs where IDQuiz = $iIDQuiz");
    
    $sQuizName = htmlentities(mysql_result($oRS,0,"QuizName"));
    $dVersionDate = date($sDefaultDateFormat,mysql_result($oRS,0,"VersionDate"));
    $sPassword = htmlentities(mysql_result($oRS,0,"Password"));
    $iActif = mysql_result($oRS,0,"Actif");
    
    if($iActif == 0){
        $sCheckedActif = "";
        $sCheckedInactif = " checked";
    }else{
        $sCheckedActif = " checked";
        $sCheckedInactif = "";
    }
	*/
    
    $sTitle = $sQuizName . "&nbsp;(&nbsp;$dVersionDate&nbsp;)";
    
	//Quiz total
	$netquiz->setIdQuiz( $iIDQuiz );
	$fPonderationTotal = $netquiz->selectTotalQuiz();

	/*
    //Quiz total
    $oRS = executeQuery("select sum(Ponderation) as PonderationTotal from nq_questions where IDQuiz = $iIDQuiz and nq_questions.active = 1");
    
    $fPonderationTotal = mysql_result($oRS,0,"PonderationTotal");
    */
    //Participations list    
    
	$netquiz->setOrderByField( $sOrderByField );
	$netquiz->setOrderByDirection( $sOrderByDirection );
	$Participations = $netquiz->selectParticipations();
	
	/*
	$sQuery =   "select nq_participants.IDParticipant, nq_participants.Prenom, nq_participants.Nom, nq_participants.Groupe, nq_participants.Final, " .
                "UNIX_TIMESTAMP(nq_participants.ParticipationDate) as ParticipationDate, sum(nq_participations.Pointage) as PointageTotal, nq_participants.Matricule, nq_participants.Actif " .
                "from nq_participants " .
                "left join nq_participations using (IDParticipant) " .
                "right join nq_questions on nq_participations.IDQuestion = nq_questions.IDQuestion " .
                "where nq_questions.Active = 1 and " .
                "nq_participants.IDQuiz = $iIDQuiz " .
                "group by nq_participants.IDParticipant " .
                "order by $sOrderByField $sOrderByDirection";
    
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
            var sRolloverColor = "#BAB6DA";
            var sNormalColor = "#F5F5F5";
        
            function showDetail(iIDParticipant){
                openWindowsAndCenter("viewparticipantdetail.php?idquiz=<?php echo $iIDQuiz ?>&idparticipant=" + iIDParticipant,770,570)
            }
            function rowRollover(oRow){
                oRow.bgColor = sRolloverColor;
            }
            function rowRollout(oRow){
                oRow.bgColor = sNormalColor;
            }
        </script>
    </head>
    <body style="margin:0px;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                            <td align="center" style="background:url(images/bandetitre_pat.jpg) repeat-x;" height="56">
                                    <img src="images/bandetitre.jpg" />
                            </td>
                    </tr>
                    <tr>
                            <td width="100%" height="100">
                                    &nbsp;
                            </td>
                    </tr>
                    <tr>
                            <td align="center">
                                <table width="745" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td width="100%" height="30">
                                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                <tr>
                                                    <td height="30" width="50%" valign="middle" align="left"><font class="section_header"><?php echo $sLR["q_title"]; ?></font></td>
                                                    <td height="30" width="50%" valign="middle" align="right"><a class="small" href="quizlist.php"><?php echo $sLR["menu_bql_link"]; ?></a></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="100%" height="5" valign="middle"><img src="images/ligne.gif" width="100%" height="1" alt="" border="0" /></td>
                                    </tr>
                                    <tr>
                                        <td width="100%" height="30" valign="middle" align="right">
                                            &nbsp;
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="100%" height="50"><img src="images/spacer.gif" width="100%" height="50"></td>
                                    </tr>
                                    <tr>
                                        <td width="100%" valign="middle" align="left">
                                            <font class="list_header"><?php echo($sTitle); ?></font>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="100%" height="30"><img src="images/spacer.gif" width="100%" height="30"></td>
                                    </tr>
                                    <tr>
                                        <td width="100%" valign="middle" align="left">
                                            <form method="get" action="viewquizparticipations.php<?php echo $sGetParam ?>" style="display:inline;" id="frmStatPass">
                                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                <tr>
                                                    <td width="40%" align="left">
                                                        <strong><?php echo $sLR["q_sta_lbl"]; ?> : </strong><input type="radio" onchange="postForm('frmStatPass')" name="stat" id="stat1" value="1"<?php echo $sCheckedActif ?> /> <label for="stat1"><?php echo $sLR["q_staa_lbl"]; ?></label> <input type="radio" onchange="postForm('frmStatPass')" name="stat" id="stat0" value="0"<?php echo $sCheckedInactif ?> /> <label for="stat0"><?php echo $sLR["q_staia_lbl"]; ?></label>
                                                    </td>
                                                    <td width="60%" align="right">
                                                        <!-- <strong><?php echo $sLR["q_pw_lbl"]; ?> : </strong><input type="text" name="pass" size="20" maxlength="45" value="<?php echo $sPassword ?>" /> <font class="blue">></font> <a href="javascript:postForm('frmStatPass');"><?php echo $sLR["q_acc_lbl"]; ?></a> -->
                                                    </td>
                                                </tr>
                                            </table>
                                            <input type="hidden" value="<?php echo $iIDQuiz ?>" name="id" />
                                            </form>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="100%" height="50"><img src="images/spacer.gif" width="100%" height="50"></td>
                                    </tr>
                                    <tr>
                                        <td width="100%">
                                            <table width="750" cellpadding="0" cellspacing="0" border="0">
                                            <?php generateHeader($sQuizName,$dVersionDate,$sGetParam,2)?>
                                            </table>
                                            <table width="750" cellpadding="0" cellspacing="0" border="0" class="ContentCell">
                                                <tr>
                                                    <td width="750" height="20" colspan="5">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td width="125" align="left" class="ContentColWB" style="background-color:#CACACA;"><strong><?php echo $sLinks[0]; ?></strong></td>
                                                    <td width="235" align="center" class="ContentColWB" style="background-color:#CACACA;"><strong><?php echo $sLinks[1]; ?></strong></td>
                                                    <td width="110" align="center" class="ContentColWB" style="background-color:#CACACA;"><strong><?php echo $sLinks[2]; ?></strong></td>
                                                    <td width="165" align="center" class="ContentColWB" style="background-color:#CACACA;"><strong><?php echo $sLinks[3]; ?></strong></td>
                                                    <td width="115" align="center" class="ContentColWoB" style="background-color:#CACACA;"><strong><?php echo $sLinks[4]; ?></strong></td>
                                                </tr>
                                                <?php
													
													$sCurrentColor = $sRowColorA;
                                                    foreach($Participations as $rows)
													{
													
                                                        $iIDParticipant = $rows['IDParticipant'];
                                                        $sNom = $rows['Nom'];
                                                        $sPrenom = $rows['Prenom'];
                                                        $sMatricule = $rows['Matricule'];
                                                        $sNomPrenom = htmlentities(clipString($sNom . " " . $sPrenom,30,$sDefaultClipString));
                                                        $sGroupe = $rows['Groupe'];
                                                        $iActif = intval($rows['Actif']);
                                                        $sNomPrefix = ($iActif == 0 ? $sCanceledSPrefix : "");
                                                        $iFinal = $rows['Final'];
                                                        if(intval($iFinal) == 1){
                                                            $sDate = date($sDefaultDateHourFormat,$rows['ParticipationDate']);
                                                        }else{
                                                            $sDate = "-";
                                                        }
                                                        
                                                        
                                                        $fPointageTotal = $rows['PointageTotal'];
                                                        $sScoreHTML = getFormatedScore($fPointageTotal,$fPonderationTotal);
                    
                                                        echo "<tr class=\"ContentRow\" onMouseOver=\"rowRollover(this);\" onMouseOut=\"rowRollout(this);\"  onClick=\"showDetail($iIDParticipant);\">";
                                                        echo "    <td align=\"left\" class=\"ContentColWB\" style=\"background-color:$sCurrentColor;\">$sMatricule</td>";
                                                        echo "    <td align=\"left\" class=\"ContentColWB\" style=\"background-color:$sCurrentColor;\">$sNomPrefix$sNomPrenom</td>";
                                                        echo "    <td align=\"left\" class=\"ContentColWB\" style=\"background-color:$sCurrentColor;\">$sGroupe</td>";
                                                        echo "    <td align=\"center\" class=\"ContentColWB\" style=\"background-color:$sCurrentColor;\">$sDate</td>";
                                                        echo "    <td align=\"center\" class=\"ContentColWoB\" style=\"background-color:$sCurrentColor;\">$sScoreHTML</td>";
                                                        echo "</tr>";
                                                        
                                                        $sCurrentColor = ($sCurrentColor == $sRowColorA ? $sRowColorB : $sRowColorA);
                                                    
													}

													
													/*
													$sCurrentColor = $sRowColorA;
                                                    for($i = 0;$i < mysql_num_rows($oRS);$i++){
                                                        $iIDParticipant = mysql_result($oRS,$i,"IDParticipant");
                                                        $sNom = mysql_result($oRS,$i,"Nom");
                                                        $sPrenom = mysql_result($oRS,$i,"Prenom");
                                                        $sMatricule = htmlentities(mysql_result($oRS,$i,"Matricule"));
                                                        $sNomPrenom = htmlentities(clipString($sNom . " " . $sPrenom,30,$sDefaultClipString));
                                                        $sGroupe = htmlentities(mysql_result($oRS,$i,"Groupe"));
                                                        $iActif = intval(mysql_result($oRS,$i,"Actif"));
                                                        $sNomPrefix = ($iActif == 0 ? $sCanceledSPrefix : "");
                                                        $iFinal = mysql_result($oRS,$i,"Final");
                                                        if(intval($iFinal) == 1){
                                                            $sDate = date($sDefaultDateHourFormat,mysql_result($oRS,$i,"ParticipationDate"));
                                                        }else{
                                                            $sDate = "-";
                                                        }
                                                        
                                                        
                                                        $fPointageTotal = mysql_result($oRS,$i,"PointageTotal");
                                                        $sScoreHTML = getFormatedScore($fPointageTotal,$fPonderationTotal);
                    
                                                        echo "<tr class=\"ContentRow\" onMouseOver=\"rowRollover(this);\" onMouseOut=\"rowRollout(this);\"  onClick=\"showDetail($iIDParticipant);\">";
                                                        echo "    <td align=\"left\" class=\"ContentColWB\" style=\"background-color:$sCurrentColor;\">$sMatricule</td>";
                                                        echo "    <td align=\"left\" class=\"ContentColWB\" style=\"background-color:$sCurrentColor;\">$sNomPrefix$sNomPrenom</td>";
                                                        echo "    <td align=\"left\" class=\"ContentColWB\" style=\"background-color:$sCurrentColor;\">$sGroupe</td>";
                                                        echo "    <td align=\"center\" class=\"ContentColWB\" style=\"background-color:$sCurrentColor;\">$sDate</td>";
                                                        echo "    <td align=\"center\" class=\"ContentColWoB\" style=\"background-color:$sCurrentColor;\">$sScoreHTML</td>";
                                                        echo "</tr>";
                                                        
                                                        $sCurrentColor = ($sCurrentColor == $sRowColorA ? $sRowColorB : $sRowColorA);
                                                    }
													*/
                                                ?>
                                            </table>
                                            <table width="750" cellpadding="0" cellspacing="0" border="0">
                                                <tr>
                                                    <?php
                                                        echo "<td width=\"100%\" height=\"50\"colspan=\"5\" align=\"right\" valign=\"bottom\"><a href=\"exportparticipations.php$sGetParam&ob=$sOrderByID&obd=$sOrderByDirection\">" . $sLR["q_exp_link"] . "</a></td>";
                                                            
                                                    ?>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                    </tr>
            </table>
    </body>
</html>

<?php
    //mysql_close();
}
?>