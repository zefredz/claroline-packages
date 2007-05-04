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
    $bCloseWindow = false;
    
    $fPointage = ($_GET["p"]);
    $iIDQuestion = $_GET["idq"];
    $iIDParticipant = $_GET["idp"];
    
    if(isset($_GET["cw"])){
        $bCloseWindow = true;
    }
    
	/*
    //Connection
    $oServerConn = mysql_connect($sMySQLLocation,$sMySQLUser,$sMySQLPassword);
    if(!$oServerConn){
        die(mysql_error());
    }
    
    mysql_select_db($sMySQLDatabase);
    
    $sQuery = "select Ponderation from nq_questions where IDQuestion = $iIDQuestion";
    
    $oRS = executeQuery($sQuery);
    
    $fPonderation = mysql_result($oRS,0,"Ponderation");
  */  
    
	// Declaration de la Class netquiz		
	$netquiz = new netquiz();
	
	// Class netquiz : recuperation de la ponderation
	$netquiz->setIdQuestion( $iIDQuestion );
	$selectPonderation = $netquiz->selectPonderation();
	
	$fPointage = max(min(floatval(fromLangFloat($fPointage)),$selectPonderation),0);
    
	// Class netquiz : update des points/scores
	$netquiz->setIdQuestion( $iIDQuestion );
	$netquiz->setPointage( $fPointage );
	$netquiz->setIdParticipant( $iIDParticipant );

	if ( !$netquiz->updateScore() )
	{
		claro_die(get_lang("Score is not updated"));
	}
	
	/*
    $sQuery = "update nq_participations set Pointage = $fPointage where IDQuestion = $iIDQuestion and IDParticipant = $iIDParticipant";
    
    executeQuery($sQuery);
    */
	
    if($bCloseWindow){
        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html>
            <head>
                <script src="includes/functions.js" language="javascript"></script>
                <script>
                    createCookie("scrollYPos",opener.getScrollPos());
                    opener.opener.location.reload(false);
                    opener.location.reload(false);
                    
                    window.close();
                </script>
            </head>
        </html>
        <?php
    }else{
        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html>
            <head>
                <link rel="stylesheet" type="text/css" href="includes/main.css" />
                <script src="includes/functions.js" language="javascript"></script>
                <title><?php echo $sLR["qp_scoec_link"]; ?></title>
                <script>
                    function cancel(){
                        window.close();
                    }
                    function pageInit(){
                        getObj("p").focus();
                    }
                </script>
            </head>
            <body onload="pageInit()">
                <form method="get" action="editpointage.php">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td align="left"><strong><?php echo $sLR["qp_scoec_link"]; ?></strong></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td align="left">
                                <input type="text" name="p" id="p" value="<?php echo $fPointage; ?>" size="10" maxlength="10" />
                                <input type="hidden" name="idq" value="<?php echo $iIDQuestion; ?>" />
                                <input type="hidden" name="idp" value="<?php echo $iIDParticipant; ?>" />
                                <input type="hidden" name="cw" value="1" />
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td align="right"><input type="submit" value="<?php echo $sLR["ok_btn"]; ?>" />&nbsp;<input type="button" value="<?php echo $sLR["cancel_btn"]; ?>" onclick="cancel()" /></td>
                        </tr>
                    </table>
                </form>
            </body>
        </html>
        <?php
    }
}
?>