<?php
    include("settings.inc.php");
    include_once("functions.inc.php");
    
    session_start();
    
    if(!isset($_SESSION["IDUser"])){
        urlRedirect("index.php?msg=1");
    }
?>