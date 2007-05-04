<?php
####################################

// inclusion du noyeux de claroline
include("../../../claroline/inc/claro_init_global.inc.php");

// recupération des données utilisateurs
$current_user_data = claro_get_current_user_data();

/*
print("<pre>Claro ID => ");
print_r ($current_user_data);
print(" <= </pre>");
*/

####################################

// inclusion des fichiers Netquiz
include_once("langr.inc.php");
include_once("settings.inc.php");
include_once("functions.inc.php");

//Variables

/*
$bAuth = false;
$sNextPage = "";
$sQuizIdent = "";
$sQuizVersion = "";
if(isset($_GET["auth"])){
$bAuth = true;
*/

// Récupération des paramettres URL
$sNextPage = $_GET["np"];
$sQuizIdent = $_GET["qi"];
$sQuizVersion = $_GET["qv"];

/* 
}
*/

/*	
//Connection  base de données
$oServerConn = mysql_connect($sMySQLLocation,$sMySQLUser,$sMySQLPassword);
if(!$oServerConn){
	claro_die(mysql_error());
}

mysql_select_db($sMySQLDatabase);
*/

// Vérification que l'utilisateur soit enregistré
if(!claro_is_user_authenticated()) 
{
	claro_die(get_lang("Not allowed"));
	//claro_disp_auth_form();
}
else
{	
/*
if($bAuth){
	$sQuery = "select * from nq_quizs where QuizIdent = '$sQuizIdent' and QuizVersion = '$sQuizVersion'";
	
	$oRS = executeQuery($sQuery);
	
	if(mysql_num_rows($oRS) == 0){
		showError(0);
	}elseif(intval(mysql_result($oRS,0,"Actif")) == 0){
		showError(1);
	}
	
	$sMsgs = array( "",
				$sLR["a_ipw_msg"]);

	if(isset($_GET["msg"])){
		$sMsg = $sMsgs[$_GET["msg"]];
	}else{
		$sMsg = "&nbsp;";
	}
	
	?>
	<html>
		<head>
			<link rel="stylesheet" href="includes/main.css" type="text/css" />
			<script src="includes/functions.js" language="javascript"></script>
			
			<script>
				function checkForm(){
					var sMsg = "<?php echo html_entity_decode($sLR["a_if_msg"]); ?>";
					var sTextbox = new Array("txtNom","txtPrenom");
					
					for(var i = 0;i < sTextbox.length;i++){
						
						if(getObj(sTextbox[i]).value == ""){
							alert(sMsg);
							return true;
						}
					}
					
					return true;
				}
				
				function pageInit(){
					getObj("txtNom").focus();
					parent.window.onbeforeunload = null;
				}
				
			</script>
			
		</head>
		<body onload="pageInit()">
			
								
			<p>Claro ID => <?php echo claro_get_current_user_id(); ?> <= </p>
			<p>Claro ID => <?php print_r (claro_get_current_user_data()); ?> <= </p>
			
			<div align="center">
				<table width="775" cellpadding="0" cellspacing="0" border="0">
					<!--
					<tr>
						<td height="100" colspan="4"><img src="images/spacer.gif" height="100" width="775" /></td>
					</tr>
					-->
					<tr>
						<td height="25" width="90" valign="top"><img src="images/spacer.gif" height="11" width="90" /></td>
						<td height="25" colspan="3" valign="middle" width="685">
							<table width="685" cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td width="568" valign="middle" class="Avertissement">&nbsp;
									</td>
									<td width="108" valign="middle" align="right"><img src="images/logo_netquiz.gif" width="73" height="13"></td>
								</tr>
								<tr>
									<td colspan="2" width="685" height="5"><img src="images/spacer.gif" width="685" height="5" /></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td height="10" width="90" class="LineA"><img src="images/spacer.gif" height="10" width="90" /></td>
						<td height="10" width="685" colspan="3" class="LineB"><img src="images/spacer.gif" height="10" width="685" /></td>
					</tr>
					<tr>
						<td width="90">&nbsp;</td>
						<td width="685" colspan="3"><font class="Avertissement"><?php echo $sLR["a_note_msg"]; ?></font></td>
					</tr>
					<tr>
						<td height="50" colspan="4"><img src="images/spacer.gif" height="50" width="775" /></td>
					</tr>
					<tr>
						<td colspan="4" align="center">
							<form action="authparticipant.php" method="post" onsubmit="return checkForm();" id="frmAuth">
								<table cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td align="right"><font><?php echo $sLR["a_lnam_lbl"]; ?></font>&nbsp;&nbsp;</td>
										<td align="left">&nbsp;&nbsp;<input type="text" name="txtNom" id="txtNom" /></td>
									</tr>
									<tr>
										<td height="10" colspan="2"><img src="images/spacer.gif" width="30" height="10" /></td>
									</tr>
									<tr>
										<td align="right"><font><?php echo $sLR["a_fnam_lbl"]; ?></font>&nbsp;&nbsp;</td>
										<td align="left">&nbsp;&nbsp;<input type="text" name="txtPrenom" id="txtPrenom" /></td>
									</tr>
									<tr>
										<td height="10" colspan="2"><img src="images/spacer.gif" width="30" height="10" /></td>
									</tr>
									<tr>
										<td align="right"><font><?php echo $sLR["a_mat_lbl"]; ?></font>&nbsp;&nbsp;</td>
										<td align="left">&nbsp;&nbsp;<input type="text" name="txtMatricule" id="txtMatricule" /></td>
									</tr>
									<tr>
										<td height="10" colspan="2"><img src="images/spacer.gif" width="30" height="10" /></td>
									</tr>
									<tr>
										<td align="right"><font><?php echo $sLR["a_gr_lbl"]; ?></font>&nbsp;&nbsp;</td>
										<td align="left">&nbsp;&nbsp;<input type="text" name="txtGroupe" id="txtGroupe" /></td>
									</tr>
									<tr>
										<td height="10" colspan="2"><img src="images/spacer.gif" width="30" height="10" /></td>
									</tr>
									<tr>
										<td align="right"><font><?php echo $sLR["a_email_lbl"]; ?></font>&nbsp;&nbsp;</td>
										<td align="left">&nbsp;&nbsp;<input type="text" name="txtCourriel" id="txtCourriel" /></td>
									</tr>
									<tr>
										<td height="10" colspan="2"><img src="images/spacer.gif" width="30" height="10" /></td>
									</tr>
									<tr>
										<td align="right"><font><?php echo $sLR["a_pw_lbl"]; ?></font>&nbsp;&nbsp;</td>
										<td align="left">&nbsp;&nbsp;<input type="password" name="txtPassword" id="txtPassword" /></td>
									</tr>
									<tr>
										<td height="40" colspan="2"><img src="images/spacer.gif" width="30" height="40" /></td>
									</tr>
									<tr>
										<td colspan="2" align="center"><input type="submit" value="<?php echo $sLR["a_go_btn"]; ?>" />
										<input type="hidden" name="NextPage" value="<?php echo $sNextPage ?>" />
										<input type="hidden" name="QuizIdent" value="<?php echo $sQuizIdent ?>" />
										<input type="hidden" name="QuizVersion" value="<?php echo $sQuizVersion ?>" />
										
										</td>
									</tr>
									<tr>
										<td colspan="2" align="center"><font class="Red"><?php echo $sMsg ?></font></td>
									</tr>
								</table>
							</form>
						</td>
					</tr>
				</table>
			</div>
		</body>
	</html>

	<?php
}else{
   
//Check if password is good
		
$sGivenPassword = fromGPC(toSQLString($_POST["txtPassword"],false));
$sQuizIdent = $_POST["QuizIdent"];
$sQuizVersion = $_POST["QuizVersion"];
$sNextPage = $_POST["NextPage"];

$sQuery = "select * from nq_quizs where QuizIdent = '$sQuizIdent' and QuizVersion = '$sQuizVersion' and Password = $sGivenPassword";

*/
	

	/*
	
	Le $sQuery n'est pas obligatoire ... si on le desactive il ne faut pas oublier de supprimer les 2 variables $iIDQuiz dans le query suivant ...
	
	*/
	
	/*
	$sQuery = "select * from nq_quizs where QuizIdent = '$sQuizIdent' and QuizVersion = '$sQuizVersion'";

	$oRS = executeQuery($sQuery);
	
	if(mysql_num_rows($oRS) == 0){
		
		urlRedirect("authparticipant.php?auth=1&np=$sNextPage&qi=$sQuizIdent&qv=$sQuizVersion&msg=1");
	}
	
	$iIDQuiz = mysql_result($oRS,0,"IDQuiz");
	*/
	
	###############################################
	
	/*
	$tblNameList = array(
					'nq_quizs'
				);
	*/
				
	//$faqTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
					
	$sql = "select IDQuiz from nq_quizs where QuizIdent = '$sQuizIdent' and QuizVersion = '$sQuizVersion'";
	$iIDQuiz = claro_sql_query_get_single_value ($sql);
	
	/*
	$result = claro_sql_query($sql);
	
	if(mysql_num_rows($result) == 0){
		
		urlRedirect("authparticipant.php?auth=1&np=$sNextPage&qi=$sQuizIdent&qv=$sQuizVersion&msg=1");
	}
	
	$iIDQuiz = mysql_result($result,0,"IDQuiz");
	
	*/
	
	//$result = claro_sql_query_get_single_row($sql);
	
	
	###############################################
	
	//Insert participant information

	/*
	$sNom = toSQLString(fromGPC($_POST["txtNom"]),false);
	$sPrenom = toSQLString(fromGPC($_POST["txtPrenom"]),false);
	$sMatricule = toSQLString(fromGPC($_POST["txtMatricule"]),false);
	$sGroupe = toSQLString(fromGPC($_POST["txtGroupe"]),false);
	$sCourriel = toSQLString(fromGPC($_POST["txtCourriel"]),false);
	*/

	###############################################
	$sNom = toSQLString(fromGPC($current_user_data['lastName']),false);
	$sPrenom = toSQLString(fromGPC($current_user_data['firstName']),false);
	$sMatricule = toSQLString(fromGPC(""/*$_POST["txtMatricule"]*/),false);
	$sGroupe = toSQLString(fromGPC(""/*$_POST["txtGroupe"]*/),false);
	$sCourriel = toSQLString(fromGPC($current_user_data['mail']),false);
	###############################################
	
	/*
	
	Supprimer les 2 variables $iIDQuiz
	
	*/
	
	$sQuery =   <<<IQUERY
				insert into nq_participants 
				(Prenom,Nom,Groupe,Matricule,Courriel,IDQuiz) 
				values ($sPrenom,$sNom,$sGroupe,$sMatricule,$sCourriel,$iIDQuiz)
IQUERY;
	
	executeQuery($sQuery);
	
	
	//Get participant id
	$sQuery =   "select max(IDParticipant) as last_id from nq_participants";
	$oRS = executeQuery($sQuery);
	$iIDParticipant = mysql_result($oRS,0,"last_id");
	
	
	//Generate javascript (set iIDParticipant and redirect to NextPage)
	$sReferer = $_SERVER["HTTP_REFERER"];
	
	$sRefererPath = substr($sReferer,0,strrpos($sReferer,"/"));
	
	$sNextPageFull = $sRefererPath . "/" . $sNextPage;
}
	
	?>
		<html>
			<head>
				<script>
					function pageInit(){
						parent.iIDParticipant = <?php echo $iIDParticipant; ?>;
						parent.sNomUsager = <?php echo $sNom; ?>;
						parent.sPrenomUsager = <?php echo $sPrenom; ?>;
						parent.sMatriculeUsager = <?php echo $sMatricule; ?>;
						parent.sGroupeUsager = <?php echo $sGroupe; ?>;
						parent.sCourrielUsager = <?php echo $sCourriel; ?>;
						parent.window.onbeforeunload = parent.confirmClose;
						parent.moveFirst();
					}
				</script>
			</head>
			<body onload="pageInit()">
				&nbsp;
			</body>
		</html>
	<?php
/*

}

    mysql_close();
    
    function showError($i){
        global $sLR;
        
        $sMsgs = array($sLR["a_nfqerr_msg"], $sLR["a_iaqerr_msg"]);
        $sMsg = $sMsgs[$i];
        
        ?>
            <html style="height:100%;">
                <head>
                    <link rel="stylesheet" href="includes/main.css" type="text/css" />
                    <script>
                        parent.window.onbeforeunload = null;
                    </script>
                </head>
                <body style="margin:0px;height:100%;">
                    <table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td height="66%" width="100%" align="center" valign="middle">
                                <strong><?php echo $sMsg; ?></strong>
                           
							<?php
							##############################
							print ("<br />1 : ".$_GET["np"]."<br />");
					        print ("2 : ".$_GET["qi"]."<br />");
					        print ("3 : ".$_GET["qv"]."<br />");
							##############################
							?>
							
							</td>
                        </tr>
                        <tr>
                            <td height="34%" width="100%" align="center" valign="middle">
                                &nbsp;
                            </td>
                        </tr>
                    </table>
                </body>
            </html>
        <?php
        exit();
    }
*/
?>