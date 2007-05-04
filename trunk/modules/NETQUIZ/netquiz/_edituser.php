<?php
    include_once("langr.inc.php");
    include_once("settings.inc.php");
    include_once("functions.inc.php");
    include_once("table.creation.inc.php");
    //include "isauth.php";
    
    //Msg
    $sMsgs = array(	"Vos modifications ont &eacute;t&eacute; sauvegard&eacute;es avec succ&egrave;s.",
			"Les mots de passe ne correspondent pas",
                        "L'ancien mot de passe ne correspond pas" );

    if(isset($_GET["msg"])){
        $sMsg = $sMsgs[$_GET["msg"]];
    }else{
        $sMsg = "&nbsp;";
    }
    
    $sRefPage = $_GET["rp"];
    
    $oServerConn = mysql_connect($sMySQLLocation,$sMySQLUser,$sMySQLPassword);
    if(!$oServerConn){
        die(mysql_error());
    }
    
    mysql_select_db($sMySQLDatabase);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="includes/main.css" />
                <script src="includes/functions.js" language="javascript"></script>
		<title><?php echo $sLR["title"]; ?></title>
                <script>
                    function pageInit(){
                        getObj("txtOldPassword").focus();
                    }
		    function cancel(){
			location.href = "quizlist.php";
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
                                    showIdentForm(false,$sMsg);
                                ?>
                            </td>
			</tr>
		</table>
	</body>
</html>