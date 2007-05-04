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
    
    $iActive = $_GET["a"];
    $iIDQuestion = $_GET["id"];
    
    if(isset($_GET["cw"])){
        $bCloseWindow = true;
    }
    
	// Declaration de la Class netquiz		
	$netquiz = new netquiz();
	
    // Class netquiz : update du status des questions
	$netquiz->setIdQuestion( $iIDQuestion );
	$netquiz->setQuestionsActif( $iActive );

	if ( !$netquiz->updateQuestionsStatus() )
	{
		claro_die(get_lang("Status is not updated"));
	}

	/*
    //Connection
    $oServerConn = mysql_connect($sMySQLLocation,$sMySQLUser,$sMySQLPassword);
    if(!$oServerConn){
        die(mysql_error());
    }
    
    mysql_select_db($sMySQLDatabase);
    
    $sQuery = "update nq_questions set Active = $iActive where IDQuestion = $iIDQuestion";
    
    executeQuery($sQuery);
    */
	
    if($bCloseWindow){
        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html>
            <head>
                <script>
                    opener.opener.refresh();
                    opener.refresh();
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
                <title><?php echo $sLR["qq_sta_lbl"]; ?></title>
                <script>
                    function cancel(){
                        window.close();
                    }
                </script>
            </head>
            <body>
                <form method="get" action="editqstatus.php">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td align="left"><strong><?php echo $sLR["qq_sta_lbl"]; ?></strong></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td align="left">
                                <input type="radio" name="a" value="1" id="a1" <?php echo ($iActive == 0 ? "" : " checked");?> /><label for="a1">&nbsp;&nbsp;<?php echo $sLR["qq_staa_lbl"]; ?></label><br />
                                <input type="radio" name="a" value="0" id="a0" <?php echo ($iActive == 1 ? "" : " checked");?> /><label for="a0">&nbsp;&nbsp;<?php echo $sLR["qq_staia_lbl"]; ?></label>
                                <input type="hidden" name="id" value="<?php echo $iIDQuestion; ?>" />
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