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
    $aExistingQuizs = array();
    $aFileNameExists = array();
    $aFileNameNews = array();
    $aFileNameErrors = array();
    
    $sNewStatut = "-";
    $sExistsStatut = $sLR["aq_exi_lbl"];
    $sErrorStatut = $sLR["aq_if_lbl"];
    
	### debug ###
	/*
    //Connection
	$oServerConn = mysql_connect($sMySQLLocation,$sMySQLUser,$sMySQLPassword);
    if(!$oServerConn){
        die(mysql_error());
    }
    
    mysql_select_db($sMySQLDatabase);
*/
	
	
	 
    //Get existing quizs
   /*
   $sQuery = "select * from nq_quizs";
   
    $oRS = executeQuery($sQuery);

    for($i = 0;$i < mysql_num_rows($oRS);$i++){
        $aExistingQuizs[$i] = mysql_result($oRS,$i,"QuizIdent") . "-" . mysql_result($oRS,$i,"QuizVersion");
    }
    */
	
	// Declaration de la Class netquiz	
	$netquiz = new netquiz();
	
	// Class netquiz : recuperation toutes les infos de la table quizs
	$selectAllQuizs = netquiz::selectAllQuizs();
	
	foreach ( $selectAllQuizs as $quizs )	
	{
        $aExistingQuizs[] = $quizs['QuizIdent'] . "-" . $quizs['QuizVersion'];
	}
	
	### debug ###
	
    // Open the XML Source file directory
    $oDir = opendir($sXMLFileFolder);
    if ($oDir) {
	while (($sFile = readdir($oDir)) !== false) {
	     $sFilePath = $sXMLFileFolder . $sFile;
	     if(is_file($sFilePath)){
		$xml = @simplexml_load_file($sFilePath);
		 if(!$xml){
		    //Add file in the error list
		    $aFileNameErrors[count($aFileNameErrors)] = $sFile;
		 }else{
		    $sQuizIdent = $xml->quiz->quizident;
		    $sQuizVersion = $xml->quiz->quizversion;
		    
		    if(isInArray($sQuizIdent ."-" . $sQuizVersion,$aExistingQuizs)){
			//Add file in the existing list
			$aFileNameExists[count($aFileNameExists)] = $sFile;
		    }else{
			//Add file in the new list
			$aFileNameNews[count($aFileNameNews)] = $sFile;
		    }
		 }
	    }
	}
	closedir($oDir);
    }
	
	function writeRow($sFileName,$sStatut,$bShowAction){
        global $sLR;
        
        echo "<tr>";
        echo "  <td width=\"425\" height=\"25\" class=\"listContent\" align=\"left\">" . htmlentities($sFileName) . "</td>";
        echo "  <td width=\"15\" height=\"25\"><img src=\"images/spacer.gif\" width=\"100%\" height=\"2\" /></td>";
        echo "  <td width=\"120\" height=\"25\" class=\"listContent\" align=\"center\">$sStatut</td>";
        echo "  <td width=\"85\" height=\"25\" align=\"center\">";
        if($bShowAction){
            echo "    <a href=\"addquiz.php?fn=$sFileName\">" . $sLR["aq_add_link"] . "</a>";
        }else{
            echo "    &nbsp;";
        }
        echo "  </td>";
        echo "</tr>";
    }

    //mysql_close();

}    
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
            <link rel="stylesheet" type="text/css" href="includes/main.css" />
            <title><?php echo $sLR["title"]; ?></title>
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
										<td height="30" width="50%" valign="middle" align="left"><font class="section_header"><?php echo $sLR["aq_titre"]; ?></font></td>
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
							<td width="100%" height="20"><img src="images/spacer.gif" width="100%" height="50"></td>
						</tr>
						<tr>
							<td width="100%" height="30" align="left" valign="middle"><font class="list_header"><?php echo $sLR["aq_lhead_lbl"]; ?></font></td>
						</tr>
						<tr>
							<td width="100%" height="20" valign="middle"><img src="images/ligne.gif" width="100%" height="1" /></td>
						</tr>
						<tr>
							<td width="100%">
								<table width="100%" cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td width="425" height="25" align="left"><font class="list_header"><?php echo $sLR["aq_nc_lbl"]; ?></font></td>
										<td width="15" height="25" align="left"><img src="images/spacer.gif" width="100%" height="25"></td>
										<td width="120" height="25" align="center"><font class="list_header"><?php echo $sLR["aq_sc_lbl"]; ?></font></td>
										<td width="85" height="25" align="center"><font class="list_header"><?php echo $sLR["aq_ac_lbl"]; ?></font></td>
									</tr>
									<tr>
										<td width="100%" height="10" colspan="5"><img src="images/spacer.gif" width="100%" height="10"></td>
									</tr>
									<?php
										for($i = 0;$i < count($aFileNameNews);$i++){
											writeRow($aFileNameNews[$i],$sNewStatut,true);
										}
										for($i = 0;$i < count($aFileNameExists);$i++){
											writeRow($aFileNameExists[$i],$sExistsStatut,false);
										}
										for($i = 0;$i < count($aFileNameErrors);$i++){
											writeRow($aFileNameErrors[$i],$sErrorStatut,false);
										}
									?>
								</table>
							</td>
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>