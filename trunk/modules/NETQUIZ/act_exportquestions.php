<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:

    /**
     * CLAROLINE
     *
     * @version 1.9 $Revision$
     *
     * @copyright 2001-2006 Universite catholique de Louvain (UCL)
     *
     * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
     * This program is under the terms of the GENERAL PUBLIC LICENSE (GPL)
     * as published by the FREE SOFTWARE FOUNDATION. The GPL is available
     * through the world-wide-web at http://www.gnu.org/copyleft/gpl.html
     *
     * @author KOCH Gregory <gregk84@gate71.be>
     *
     * @package NETQUIZ
     */

// inclusion du noyeux de claroline
require_once dirname(__FILE__) . "/../../claroline/inc/claro_init_global.inc.php";
require_once get_path('includePath').'/lib/user.lib.php';

// lib
require_once( "lib/netquiz.class.php" );
include_once( "netquiz/settings.inc.php" );
include_once( "netquiz/functions.inc.php" );

// Sécurité
if( count( get_included_files() ) == 1 )
{
    die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead');
}

// recupération des données utilisateurs
$current_user_data = user_get_properties(claro_get_current_user_id());

// Vérification que l'utilisateur soit enregistré
if(!claro_is_user_authenticated()) 
{
	claro_die(get_lang("Not allowed"));
}
else
{

    //Variables
    $is_allowedToAdmin = claro_is_allowed_to_edit();
    $statCurrentUser = $_REQUEST['statCurrentUser'];
    $iIDQuiz = $_REQUEST["id"];
    $sCharset = "ISO-8859-1";

    // Declaration de la Class netquiz		
    $netquiz = new netquiz();

    // Class netquiz : recuperation du Nom du quiz et date de version
    $netquiz->setIdQuiz( $iIDQuiz );
    $selectNameQuizAndDate = $netquiz->selectNameQuizAndDate();

    foreach( $selectNameQuizAndDate as $row )
    {
        $sQuizName = str_replace(" ","_",html_entity_decode($row['QuizName']));
        $dVersionDate = date($sExportDateFormat,$row['VersionDate']);
    }

    //Declaration du Header
    header("Content-type: application/force-download; charset=$sCharset");
    header("Content-disposition: attachment; filename=$sQuizName$dVersionDate.txt");

    //Liste des questions
    $sCols = array("NoQuestion","QuestionName","QuestionTypeTD","Average","Ponderation");
    $sOrderByField = "NoQuestion";
    $sOrderByDirection = "ASC";
    $sOrderByID = -1;

    //OrderBy
    if(isset($_REQUEST["ob"]) && $_REQUEST["ob"] > -1)
    {
        $sOrderByField = $sCols[$_REQUEST["ob"]];
    }

    if(isset($_REQUEST["obd"]))
    {
        $sOrderByDirection = $_REQUEST["obd"];
    }
    
    //export
    if( $statCurrentUser == false && $is_allowedToAdmin == true ) 
    {
        // Class netquiz : recuperation de la liste des questions
        $netquiz->setIdQuiz( $iIDQuiz );
        $netquiz->setOrderByField( $sOrderByField );
        $netquiz->setOrderByDirection( $sOrderByDirection );
        $selectQuestions = $netquiz->selectQuestions();
            
        output( "No" . ';' . "Titre" . ';' . "Type" . ';' . "Moyenne" . ';' . "Pondération" );
        output( "\n\n" );

        foreach($selectQuestions as $row)
        {
            $iIDQuestion = $row['IDQuestion'];
            $sNo = $row['NoQuestion'];
            $iActive = intval($row['Active']);
            $sNomPrefix = ($iActive == 0 ? $sCanceledQPrefix : "");
            $sNom = clipString( $row['QuestionName'],$iQNameMaxNbChar,$sDefaultClipString );
            $sType = html_entity_decode( $row['QuestionTypeTD'] );
            $sAverage = $row['Average'];
            $sPond = toLangFloat( $row['Ponderation'] );
            
            $regexp = '/\./';
            $replace = ',';
            $sAverage = preg_replace($regexp, $replace, $sAverage);

            output( "$sNo" . ';' . "$sNom" . ';' . "$sType" . ';' . "$sAverage" . ';' . "$sPond" . "\n" );
        }
    }
    else
    {    
        // Class netquiz : recuperation de la liste des questions
        $netquiz->setIdQuiz( $iIDQuiz );
        $netquiz->setOrderByField( $sOrderByField );
        $netquiz->setOrderByDirection( $sOrderByDirection );
        $netquiz->setCurrentUserId( claro_get_current_user_id() );
        $selectQuestions = $netquiz->selectQuestionsCurrentUser();
            
        output( "No" . ';' . "Titre" . ';' . "Type" . ';' . "Moyenne" . ';' . "Pondération" );
        output( "\n\n" );
        
        foreach($selectQuestions as $row)
        {
            $iIDQuestion = $row['IDQuestion'];
            $sNo = $row['NoQuestion'];
            $iActive = intval($row['Active']);
            $sNomPrefix = ($iActive == 0 ? $sCanceledQPrefix : "");
            $sNom = clipString( $row['QuestionName'],$iQNameMaxNbChar,$sDefaultClipString );
            $sType = html_entity_decode( $row['QuestionTypeTD'] );
            $sAverage = $row['Average'];
            $sPond = toLangFloat( $row['Ponderation'] );
            
            $regexp = '/\./';
            $replace = ',';
            $sAverage = preg_replace($regexp, $replace, $sAverage);

            output( "$sNo" . ';' . "$sNom" . ';' . "$sType" . ';' . "$sAverage" . ';' . "$sPond" . "\n" );
        }
    }
    
}
?>