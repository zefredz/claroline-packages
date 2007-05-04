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
	 
	include_once("settings.inc.php");
    include_once("functions.inc.php");
   
    //Variables
    $sGivenUsername = toSQLString(fromGPC($_POST["txtUsername"]));
    $sGivenPassword = toSQLString(fromGPC($_POST["txtPassword"]),false);
    
    //Connection
    $oServerConn = mysql_connect($sMySQLLocation,$sMySQLUser,$sMySQLPassword);
    if(!$oServerConn){
        die(mysql_error());
    }
    
    mysql_select_db($sMySQLDatabase);
    
    $sQuery = "select * from nq_users where Username = $sGivenUsername and LoginPassword = password($sGivenPassword)";
    
    $oRS = executeQuery($sQuery);
   
    if(mysql_num_rows($oRS) > 0){
        //Valid login; Starting session
        //session_start();
        $_SESSION["IDUser"] = mysql_result($oRS,0,"IDUser");
        urlRedirect("quizlist.php");
    }else{
        //Invalid login; Redirecting onto an error page
        urlRedirect("index.php?msg=0");
    }
    
    mysql_close();
	 
	 
/*
	if(mysql_num_rows($oRS) > 0){
        //Valid login; Starting session
        $_SESSION["IDUser"] = claro_get_current_user_id();
        urlRedirect("quizlist.php");
    }else{
        //Invalid login; Redirecting onto an error page
        urlRedirect("index.php?msg=0");
    }
	s*/
}
?> 