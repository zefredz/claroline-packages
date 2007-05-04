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
    
    //MAIN
    /*
    $oServerConn = mysql_connect($sMySQLLocation,$sMySQLUser,$sMySQLPassword);
    if(!$oServerConn){
        die(mysql_error());
    }
    
    mysql_select_db($sMySQLDatabase);
  
    $oRS = executeQuery("select *,UNIX_TIMESTAMP(VersionDate) AS TS_VersionDate from nq_quizs");
  */
  
	// Declaration de la Class netquiz	
	$netquiz = new netquiz();
		
	// Class netquiz : recuperation toutes les infos de la table quizs
	$selectQuizsList = netquiz::selectQuizsList();

}    
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="includes/main.css" />
		<title><?php echo $sLR["title"]; ?></title>
		<script language="javascript">
		    function deleteQuiz(){
			var bConfDelete = confirm("<?php echo html_entity_decode($sLR["ql_dq_msg"]); ?>");
			
			if(bConfDelete){
			    return true;
			}else{
			    return false;
			}
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
										<td height="30" width="50%" valign="middle" align="left"><font class="section_header"><?php echo $sLR["ql_title"]; ?></font></td>
										<td height="30" width="50%" valign="middle" align="right"><!-- ### debug ### <a class="small" href="edituser.php?rp=quizlist.php"><?php echo $sLR["menu_cpw_link"]; ?></a>--></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td width="100%" height="5" valign="middle"><img src="images/ligne.gif" width="100%" height="1" alt="" border="0" /></td>
						</tr>
						<tr>
							<td width="100%" height="30" valign="middle" align="right">
								<a class="small" href="addquizlist.php"><?php echo $sLR["menu_aq_link"]; ?></a>
							</td>
						</tr>
						<tr>
							<td width="100%" height="20"><img src="images/spacer.gif" width="100%" height="50"></td>
						</tr>
						<tr>
							<td width="100%" height="30" align="left" valign="middle"><font class="list_header"><?php echo $sLR["ql_lhead_lbl"]; ?></font></td>
						</tr>
						<tr>
							<td width="100%" height="20" valign="middle"><img src="images/ligne.gif" width="100%" height="1" /></td>
						</tr>
						<tr>
							<td width="100%">
								<table width="100%" cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td width="425" height="25" align="left"><font class="list_header"><?php echo $sLR["ql_tc_lbl"]; ?></font></td>
			<td width="150" height="25" align="center"><font class="list_header"><?php echo $sLR["ql_dc_lbl"]; ?></font></td>
										<td width="15" height="25" align="left"><img src="images/spacer.gif" width="100%" height="25"></td>
										<td width="70" height="25" align="center"><font class="list_header"><?php echo $sLR["ql_sc_lbl"]; ?></font></td>
										<td width="85" height="25" align="center"><font class="list_header"><?php echo $sLR["ql_ac_lbl"]; ?></font></td>
									</tr>
									<tr>
										<td width="100%" height="10" colspan="5"><img src="images/spacer.gif" width="100%" height="10"></td>
									</tr>
									<?php
										foreach ( $selectQuizsList as $quizsList )	
										{
									        $sQuizName = htmlentities( $quizsList['QuizName'] );
											$iIDQuiz = $quizsList['IDQuiz'];
											$sVersionDate = date( $sDefaultDateHourFormat,$quizsList['TS_VersionDate'] );
											$sStatut = ( $quizsList['Actif'] == '0' ? $sLR['ql_naq_lbl'] : $sLR['ql_aq_lbl'] );
											
											echo '<tr>';
											echo '  <td width="425" height="25" class="listContent" align="left"><a href="viewquizstats.php?id='.$iIDQuiz.'">'.$sQuizName.'</a></td>';
											echo '  <td width="150" height="25" class="listContent" align="center">'.$sVersionDate.'</td>';
											echo '  <td width="15" height="25"><img src="images/spacer.gif" width="100%" height="2" /></td>';
											echo '  <td width="70" height="25" class="listContent" align="center">'.$sStatut.'</td>';
											echo '  <td width="85" height="25" align="center">';
											echo '    <a href="quizdelete.php?id='.$iIDQuiz.'" onClick="return deleteQuiz();">'.$sLR["ql_dq_link"].'</a>';
											echo '  </td>';
											echo '</tr>';
										}
										
										//mysql_close();
									?>
								</table>
							</td>
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>