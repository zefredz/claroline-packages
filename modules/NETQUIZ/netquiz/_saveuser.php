<?php
    include_once("settings.inc.php");
    include_once("functions.inc.php");
    //include "isauth.php";
    
    //Variables
    $sGivenOldPassword = toSQLString(fromGPC($_POST["txtOldPassword"]),false);
    $sPassword = toSQLString(fromGPC($_POST["txtPassword1"]),false);
    $sPassword2 = toSQLString(fromGPC($_POST["txtPassword2"]),false);
    
    
    $iIDUser = $_POST["IDUser"];
    $sRefPage = $_GET["rp"];
    
    //Connection
    $oServerConn = mysql_connect($sMySQLLocation,$sMySQLUser,$sMySQLPassword);
    if(!$oServerConn){
        die(mysql_error());
    }
    
    mysql_select_db($sMySQLDatabase);
    
    $sQuery = "select * from nq_users where IDUser = $iIDUser and LoginPassword = password($sGivenOldPassword)";

    $oRs = executeQuery($sQuery);
    
    if(mysql_num_rows($oRs) > 0){
        if($sPassword == $sPassword2){
            $sQuery =   "update nq_users set LoginPassword = password($sPassword) where IDUser = $iIDUser";
    
            executeQuery($sQuery);
            
            urlRedirect("quizlist.php");
        }else{
            urlRedirect("edituser.php?msg=1&rp=$sRefPage");
        }
    }else{
        urlRedirect("edituser.php?msg=2&rp=$sRefPage");
    }
    
    mysql_close();
?>