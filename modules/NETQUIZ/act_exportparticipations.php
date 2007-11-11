<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:

    /**
     * CLAROLINE
     *
     * @version 1.9 $Revision: 159 $
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
    $i = 1;
    
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

    // Class netquiz : recuperation du nombre Total du quiz
    $netquiz->setIdQuiz( $iIDQuiz );
    $iPonderationTotal = $netquiz->selectPonderationTotal();

    //Liste des participations
    $sCols = array("Matricule","Prenom","Groupe","ParticipationDate","PointageTotal");
    $sOrderByField = "Nom";
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
        // Class netquiz : recuperation de la ponderation
        $netquiz->setIdQuiz( $iIDQuiz );
        $netquiz->setOrderByField( $sOrderByField );
        $netquiz->setOrderByDirection( $sOrderByDirection );
        $selectParticipations = $netquiz->selectParticipations();

        output( "No" . ';' . "Nom" . ';' . "Courriel" . ';' . "Soumission" . ';' . html_entity_decode("Résultats ( / ") . toLangFloat($iPonderationTotal) . " )" . ';' . html_entity_decode("Résultats ( % )") );
        output( "\n\n" );

        foreach($selectParticipations as $row)
        {
            $iIDParticipant = $row['IDParticipant'];
            $sNom = $row['Nom'];
            $sPrenom = $row['Prenom'];
            $sCourriel = !empty( $row['Courriel']) ? $row['Courriel'] : '-';
            $sPrenomNom = clipString($sPrenom . " " . $sNom,30,$sDefaultClipString);
            $iActif = intval($row['Actif']);
            $sNomPrefix = ($iActif == 0 ? $sCanceledSPrefix : "");
            $iFinal = $row['Final'];
            if(intval($iFinal) == 1)
            {
                $sDate = date($sDefaultDateHourFormat,$row['ParticipationDate']);
            }
            else
            {
                $sDate = "-";
            }
            $iPointageTotal = $row['PointageTotal'];
            $sScore = toLangFloat($iPointageTotal);
            $sScorePC = round( $iPointageTotal/$iPonderationTotal*100 );
            
            output( "$i" . ';' . "$sPrenomNom" . ';' . "$sCourriel" . ';' . "$sDate" . ';' . "$sScore" . ';' . "$sScorePC" . "\n" );
            $i++;
        }
    }
    else
    {
        die;
        // Class netquiz : recuperation de la ponderation
        $netquiz->setIdQuiz( $iIDQuiz );
        $netquiz->setOrderByField( $sOrderByField );
        $netquiz->setOrderByDirection( $sOrderByDirection );
        $netquiz->setCurrentUserId( claro_get_current_user_id() );
        $selectParticipations = $netquiz->selectParticipationsCurrentUser();

        output("No" . ';' . "Nom" . ';' . "Courriel" . ';' . "Soumission" . ';' . html_entity_decode("R&eacute;sultats ( / ") . toLangFloat($iPonderationTotal) . " )" . ';' . html_entity_decode("R&eacute;sultats ( % )"));
        output("\n\n");

        foreach($selectParticipations as $row)
        {
            $iIDParticipant = $row['IDParticipant'];
            $sNom = $row['Nom'];
            $sPrenom = $row['Prenom'];
            $sCourriel = !empty( $row['Courriel']) ? $row['Courriel'] : '-';
            $sPrenomNom = clipString($sPrenom . " " . $sNom,30,$sDefaultClipString);
            $iActif = intval($row['Actif']);
            $sNomPrefix = ($iActif == 0 ? $sCanceledSPrefix : "");
            $iFinal = $row['Final'];
            if(intval($iFinal) == 1)
            {
                $sDate = date($sDefaultDateHourFormat,$row['ParticipationDate']);
            }
            else
            {
                $sDate = "-";
            }
            $iPointageTotal = $row['PointageTotal'];
            $sScore = toLangFloat($iPointageTotal);
            $sScorePC = round( $iPointageTotal/$iPonderationTotal*100 );
            
            output( "$i" . ';' . "$sPrenomNom" . ';' . "$sCourriel" . ';' . "$sDate" . ';' . "$sScore" . ';' . "$sScorePC" . "\n");
            $i++;
        }
    }
}
?>