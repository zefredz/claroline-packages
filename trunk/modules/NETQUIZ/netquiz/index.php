<?php
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

	include_once("langr.inc.php");
	include_once("settings.inc.php");
	include_once("functions.inc.php");
	include_once("table.creation.inc.php");
	include_once("db.file.creation.inc.php");
	
	//Variables
	$sMsgs = array($sLR["log_wunps_msg"], $sLR["log_mi_msg"]);

	if(isset($_GET["msg"])){
		$sMsg = $sMsgs[$_GET["msg"]];
	}else{
		$sMsg = "&nbsp;";
	}
	
	urlRedirect("quizlist.php");
	
	/*
	if( !isset($_GET["msg"]) ) {
		urlRedirect("checklogin.php");
	}
	else
	{
		echo($sMsg); 
	}
	*/
}

/*
?> 



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="includes/main.css" />
                <script src="includes/functions.js" language="javascript"></script>
		<title><?php echo $sLR["title"]; ?></title>
                <script>
                    function pageInit(){
                        getObj("txtUsername").focus();
                    }
		    function checkIdentForm(){
			var sMsgA = "<?php echo html_entity_decode($sLR["log_faferr_msg"]) ?>";
			var sMsgB = "<?php echo html_entity_decode($sLR["log_rpwerr_msg"]) ?>";
			
			if(getObj("txtUsername").value == ""){
				alert(sMsgA);
				return false;
			}
			if(getObj("txtPassword1").value == ""){
				alert(sMsgA);
				return false;
			}
			if(getObj("txtPassword1").value != getObj("txtPassword2").value){
				alert(sMsgB);
				return false;
			}
			
			return true;
		    }
                </script>
	</head>
	<body onLoad="pageInit();" style="margin:0px;">
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
                             
							 <?php
								
                                    $oServerConn = null;
    
                                    if(checkDBFile()){
                                        $oServerConn = @mysql_connect($sMySQLLocation,$sMySQLUser,$sMySQLPassword);
                                        
                                        if($oServerConn){
                                            if(!mysql_select_db($sMySQLDatabase)){
                                                showDBForm(1);
                                            }else{
                                                if(checkTablesStruct($sRequiredTables)){
                                                    showLoginForm($sMsg);
                                                }else{
                                                    showIdentForm(true,"");
                                                }
                                            }
                                        }else{
                                            die(showDBForm(mysql_errno()));
                                        }
                                    }else{
                                        showDBForm(-1);
                                    }
									
                                ?>
								
                            </td>
			</tr>
		</table>
	</body>
</html>


<?php



   if($oServerConn){
        mysql_close();
    }
    
    function showLoginForm($sMsg){
	include("langr.inc.php");
        ?>
            
            <form action="checklogin.php" method="post">
		<div align="center">
			<table cellpadding="0" cellspacing="0" border="0" width="450">
				<tr>
					<td width="100%" height="125"><img src="images/spacer.gif" width="100%" height="125" /></td>
				</tr>
				<tr>
					<td width="100%" align="center">
						<font class="section_header"><?php echo $sLR["log_title"]; ?></font>
					</td>
				</tr>
                                <tr>
					<td width="100%" height="30"><img src="images/spacer.gif" width="100%" height="30" /></td>
				</tr>
				<tr>
                                    <td width="100%" class="InputCell"> 
                                        <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                <tr>
                                                        <td width="100%" colspan="2" height="20">
                                                                <img src="images/spacer.gif" width="100%" height="20" />
                                                        </td>
                                                </tr>
                                                <tr>
                                                        <td width="50%" align="right" valign="middle">
                                                                <strong><?php echo $sLR["log_un_lbl"]; ?> :&nbsp;&nbsp;</strong>
                                                        </td>
                                                        <td width="50%" align="left" valign="middle">
                                                                <input type="text" name="txtUsername" id="txtUsername" />
                                                        </td>
                                                </tr>
                                                <tr>
                                                        <td width="100%" colspan="2" height="5"><img src="images/spacer.gif" width="100%" height="5" /></td>
                                                </tr>
                                                <tr>
                                                        <td width="50%" align="right" valign="middle">
                                                                <strong><?php echo $sLR["log_pw_lbl"]; ?> :&nbsp;&nbsp;</strong>
                                                        </td>
                                                        <td width="50%" align="left" valign="middle">
                                                                <input type="password" name="txtPassword" id="txtPassword" />
                                                        </td>
                                                </tr>
                                                <tr>
                                                        <td width="100%" colspan="2" height="10">
                                                                <img src="images/spacer.gif" width="100%" height="10" />
                                                        </td>
                                                </tr>
                                                <tr>
                                                        <td width="100%" colspan="2" align="center">
                                                                <p class="ErrorMsg"><?php echo($sMsg); ?></p>
                                                        </td>
                                                </tr>
                                                <tr>
                                                        <td width="100%" colspan="2" height="10">
                                                                <img src="images/spacer.gif" width="100%" height="10" />
                                                        </td>
                                                </tr>
                                                <tr>
                                                        <td width="100%" colspan="2" align="center">
                                                                <input type="submit" value="<?php echo $sLR["log_go_btn"]; ?>" />
                                                        </td>
                                                </tr>
                                                <tr>
                                                        <td width="100%" colspan="2" height="5"><img src="images/spacer.gif" width="100%" height="5" /></td>
                                                </tr>
                                        </table>
                                    </td>
				</tr>
			</table>
		</div>
            </form>
        <?php
    }
	*/
?>