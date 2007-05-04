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

    include_once("settings.inc.php");
    include_once("functions.inc.php");
    //MAIN
    
    $iIDQuiz = $_GET["id"];
    
    $sGetParam = "?id=$iIDQuiz";
    
    $oServerConn = mysql_connect($sMySQLLocation,$sMySQLUser,$sMySQLPassword);
    if(!$oServerConn){
        die(mysql_error());
    }
    
    mysql_select_db($sMySQLDatabase);
    
    //Nom du quiz et date de version
    $oRS = executeQuery("select QuizName, VersionDate from nq_quizs where IDQuiz = $iIDQuiz");
    
    $sQuizName = mysql_result($oRS,0,"QuizName");
    $dVersionDate = mysql_result($oRS,0,"VersionDate");
    
   
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="includes/gestion.css" />
        <script src="includes/functions.js" language="javascript"></script>
        <title>Gestion des r&eacute;sultats</title>
        <script>
            function showDetail(iIDParticipant){
                openWindowsAndCenter("viewParticipantDetail.php?idquiz=<?php echo $iIDQuiz ?>&idparticipant=" + iIDParticipant,600,600)
            }
        </script>
    </head>
    <body>
        <div align="center">
            <table cellpadding="0" cellspacing="0" border="0" width="700">
                <?php generateHeader($sQuizName,$dVersionDate,$sGetParam,3)?>
                <tr>
                    <td width="100%" class="ContentCell">
                        <br />
                        <br />
                        <br />
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>

<?php
    //mysql_close();
}
?>