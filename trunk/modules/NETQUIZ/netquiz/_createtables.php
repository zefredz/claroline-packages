<?php
    include_once("settings.inc.php");
    include_once("functions.inc.php");
    include_once("table.creation.inc.php");
    
    //Variable
    $sUser = "";
    $sPassword = "";
    
    if(isset($_POST["txtUsername"])){
	$sUser = fromGPC($_POST["txtUsername"]);
    }
    
    if(isset($_POST["txtPassword1"])){
	$sPassword = fromGPC($_POST["txtPassword1"]);
    }
    
    //Connection
    $oServerConn = mysql_connect($sMySQLLocation,$sMySQLUser,$sMySQLPassword);
    if(!$oServerConn){
	die(mysql_error());
    }
    
    mysql_select_db($sMySQLDatabase);
    
    createTablesStruct($sRequiredTables,$sCreationQuerys,$sUser,$sPassword);
    
    $oRS = mysql_query("select max(IDUser) as LastID from nq_users");
    
    //Starting session
    session_start();
    $_SESSION["IDUser"] = mysql_result($oRS,0,"LastID");
    
    mysql_close();
    
    urlRedirect("quizlist.php");
?>