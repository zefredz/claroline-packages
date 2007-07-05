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

if( count( get_included_files() ) == 1 )
{
    die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead');
}

// Breadcrumps

$interbredcrump[]= array ( 'url' => 'index.php', 'name' => get_lang('Netquiz') );
$interbredcrump[]= array ( 'url' => 'index.php?fuseaction=stats', 'name' => get_lang('View the statistics') );
$interbredcrump[]= array ( 'url' => NULL, 'name' => get_lang('Participants') );

// --------- Claroline header and banner ----------------    
require_once get_path('incRepositorySys') . "/claro_init_header.inc.php";

// --------- Claroline body ----------------    
// toolTitle
$output->append(claro_html_tool_title( get_lang('Netquiz') ) . "\n");	

// display
if($is_allowedToAdmin == true) 
{
    // Affichage quand on est administrateur
    $output->append('<p>');
        $output->append('<a class="claroCmd" href="index.php"><img src="'.get_icon("info").'" alt="'.get_lang("List of quizs").'" title="'.get_lang("List of quizs").'" /> '.get_lang('List of quizs').'</a>');
        $output->append(' | ');
        $output->append('<a class="claroCmd" href="index.php?fuseaction=stats"><img src="'.get_icon("statistics").'" alt="'.get_lang("View the statistics").'" title="'.get_lang("View the statistics").'" /> '.get_lang('View the statistics').'</a>');
        $output->append(' | ');
        $output->append('<a class="claroCmd" href="index.php?fuseaction=install_quiz"><img src="'.get_icon("download").'" alt="'.get_lang("Install a new quiz").'" title="'.get_lang("Install a new quiz").'" /> '.get_lang('Install a new quiz').'</a>');
    $output->append('</p>');
}
else
{
    // Affichage quand on n'est pas administrateur
    $output->append('<p>');
        $output->append('<a class="claroCmd" href="index.php"><img src="'.get_icon("info").'" alt="'.get_lang("List of quizs").'" title="'.get_lang("List of quizs").'" /> '.get_lang('List of quizs').'</a>');
    $output->append('</p>');
}

    //Variables
    $statCurrentUser = ( isset( $_REQUEST['statCurrentUser'] ) )
        ? true
        : 0
        ;
    
    $iIDQuiz = $_GET["id"];
    $sGetParam = "&amp;id=$iIDQuiz";
  
	//OrderBy
    $sCols = array("Matricule","Nom","Groupe","ParticipationDate","PointageTotal");
    $sLabels = array($sLR["q_nc_lbl"],$sLR["q_namc_lbl"],$sLR["q_grc_lbl"],$sLR["q_sdc_lbl"],$sLR["q_scoc_lbl"]);
    $sLinks = array();
    $sOrderByField = $sCols[1];
    $sOrderByDirection = "ASC";
    $sOrderByID = -1;
    
    if(isset($_GET["ob"])){
        $sOrderByField = $sCols[$_GET["ob"]];
        $sOrderByID = $_GET["ob"];
    }
    
    if(isset($_GET["obd"])){
        $sOrderByDirection = $_GET["obd"];
    }
    
    for($i = 0;$i < count($sCols);$i++)
    {
        $sOBD = "ASC";
        
        if($i == $sOrderByID)
        {
            $sOBD = ($sOrderByDirection == "ASC" ? "DESC" : "ASC");
        }
        
        if( $statCurrentUser == false ) 
        {
            $sLinks[$i] = "<a href=\"index.php?fuseaction=viewAllQuizParticipations$sGetParam&ob=$i&obd=$sOBD\">$sLabels[$i]</a>";
        }
        else
        {
            $sLinks[$i] = "<a href=\"index.php?fuseaction=viewAllQuizParticipations$sGetParam&ob=$i&obd=$sOBD&statCurrentUser=1\">$sLabels[$i]</a>";
        }
    }
    	
	// Declaration de la Class netquiz		
	$netquiz = new netquiz();

if( $statCurrentUser == false && $is_allowedToAdmin == true ) 
{
    // Class netquiz : select Info du quiz Participations
	$netquiz->setIdQuiz( $iIDQuiz );
	$ViewQuizInfo = $netquiz->selectViewQuizInfo();

    $sQuizName = $ViewQuizInfo['QuizName'];
    $dVersionDate = date($sDefaultDateFormat,$ViewQuizInfo['VersionDate']);
    //$sPassword = $ViewQuizInfo['Password'];
    //$iActif = $ViewQuizInfo['Actif'];
    
	//Quiz total
	$netquiz->setIdQuiz( $iIDQuiz );
	$fPonderationTotal = $netquiz->selectTotalQuiz();

    //Participations list    
	$netquiz->setOrderByField( $sOrderByField );
	$netquiz->setOrderByDirection( $sOrderByDirection );
	$Participations = $netquiz->selectParticipations();
    
    $sTitle = $sQuizName . "&nbsp;(&nbsp;$dVersionDate&nbsp;)";
    $sTabs = array($sLR["q_stats_link"],$sLR["q_quest_link"],$sLR["q_part_link"]);
    $sTabsLinks = array("index.php?fuseaction=viewAllQuizsStats","index.php?fuseaction=viewAllQuizsQuestions","index.php?fuseaction=viewAllQuizParticipations");
    $iSelectedTab = 2;
}
else
{
    // Class netquiz : select Info du quiz Participations
	$netquiz->setIdQuiz( $iIDQuiz );
	$ViewQuizInfo = $netquiz->selectViewQuizInfo();

    $sQuizName = $ViewQuizInfo['QuizName'];
    $dVersionDate = date($sDefaultDateFormat,$ViewQuizInfo['VersionDate']);
    //$sPassword = $ViewQuizInfo['Password'];
    //$iActif = $ViewQuizInfo['Actif'];
    
	//Quiz total
	$netquiz->setIdQuiz( $iIDQuiz );
	$fPonderationTotal = $netquiz->selectTotalQuiz();

    //Participations list    
	$netquiz->setOrderByField( $sOrderByField );
	$netquiz->setOrderByDirection( $sOrderByDirection );
    $netquiz->setCurrentUserId( claro_get_current_user_id() );
	$Participations = $netquiz->selectParticipationsCurrentUser();
    
    $sTitle = $sQuizName . "&nbsp;(&nbsp;$dVersionDate&nbsp;)";
    $sTabs = array($sLR["q_stats_link"],$sLR["q_quest_link"],$sLR["q_part_link"]);
    $sTabsLinks = array("index.php?fuseaction=viewAllQuizsStats&amp;statCurrentUser=1","index.php?fuseaction=viewAllQuizsQuestions&amp;statCurrentUser=1","index.php?fuseaction=viewAllQuizParticipations&amp;statCurrentUser=1");
    $iSelectedTab = 2;
}

$output->append( '<div id="netquiz">' );

$output->append( '<h3>' . $sTitle . '</h3>' );

$output->append( '
    <table width="750" cellpadding="0" cellspacing="0" border="0">
        <tr>');
           
                for($i = 0;$i < count($sTabs);$i++){
                    if($i == $iSelectedTab){
                        $output->append( '<td class="SelectedTab" height="25">&nbsp;&nbsp;' . $sTabs[$i] . '&nbsp;&nbsp;</td>');
                    }else{
                        $output->append( '<td class="Tab" height="25">&nbsp;&nbsp;<a href="' . $sTabsLinks[$i].$sGetParam . '">' . $sTabs[$i] . '</a>&nbsp;&nbsp;</td>');
                    }
                }
            $output->append('  
            <td class="SpacerTab" width="100%">&nbsp;</td>
        </tr>
    
    </table>
    <table width="750" cellpadding="0" cellspacing="0" border="0" class="ContentCell">
        <tr>
            <td width="750" height="20" colspan="5">&nbsp;</td>
        </tr>
        <tr>
            <td width="45" align="left" class="ContentColWB" style="background-color:#CACACA;"><strong>' . $sLinks[0] . '</strong></td>
            <td width="405" align="center" class="ContentColWB" style="background-color:#CACACA;"><strong>' . $sLinks[1] . '</strong></td>
            <!-- <td width="110" align="center" class="ContentColWB" style="background-color:#CACACA;"><strong>' . $sLinks[2] . '</strong></td> -->
            <td width="150" align="center" class="ContentColWB" style="background-color:#CACACA;"><strong>' . $sLinks[3] . '</strong></td>
            <td width="150" align="center" class="ContentColWoB" style="background-color:#CACACA;"><strong>' . $sLinks[4] . '</strong></td>
        </tr>
        ' );
     
            $sCurrentColor = $sRowColorA;
            $i = 1;
            foreach($Participations as $rows)
            {
            
                $iIDParticipant = $rows['IDParticipant'];
                $sNom = $rows['Nom'];
                $sPrenom = $rows['Prenom'];
                $sMatricule = $rows['Matricule'];
                $sNomPrenom = htmlentities(clipString($sNom . " " . $sPrenom,30,$sDefaultClipString));
                $sGroupe = $rows['Groupe'];
                $iActif = intval($rows['Actif']);
                $sNomPrefix = ($iActif == 0 ? $sCanceledSPrefix : "");
                $iFinal = $rows['Final'];
                if(intval($iFinal) == 1){
                    $sDate = date($sDefaultDateHourFormat,$rows['ParticipationDate']);
                }else{
                    $sDate = "-";
                }
                
                $fPointageTotal = $rows['PointageTotal'];
                $sScoreHTML = getFormatedScore($fPointageTotal,$fPonderationTotal);

                $output->append( '<tr class="ContentRow" onClick="showDetailParticipant(' . $iIDParticipant . ', ' . $iIDQuiz . ');"> ');
                    //$output->append( '<td align="left" class="ContentColWB" style="background-color:' . $sCurrentColor . ';">' . $sMatricule . '</td> ');
                    $output->append( '<td align="left" class="ContentColWB" style="background-color:' . $sCurrentColor . ';">' . $i . '</td> ');
                    $output->append( '<td align="left" class="ContentColWB" style="background-color:' . $sCurrentColor . ';">' .$sNomPrefix . $sNomPrenom . '</td> ');
                    //$output->append( '<td align="left" class="ContentColWB" style="background-color:' . $sCurrentColor . ';">' .$sGroupe . '</td> ');
                    $output->append( '<td align="center" class="ContentColWB" style="background-color:' . $sCurrentColor . ';">' . $sDate . '</td> ');
                    $output->append( '<td align="center" class="ContentColWoB" style="background-color:' . $sCurrentColor . ';">' . $sScoreHTML . '</td> ');
                $output->append( '</tr>' );
                
                $sCurrentColor = ($sCurrentColor == $sRowColorA ? $sRowColorB : $sRowColorA);
                
                $i++;
            }
    
    $output->append( '
    </table>
    <table width="750" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td width="100%" height="50"colspan="5" align="right" valign="bottom"><a href="javascript:exportParticipations(\'' . $iIDQuiz . '\',\'' . $sOrderByID . '\',\'' . $sOrderByDirection . '\',\'' . $statCurrentUser .'\');">' .$sLR["q_exp_link"]. '</a></td>

        </tr>
    </table>
    ' );

$output->append( '</div>' );

// print display
echo $output->getContents();
		
// ------------ Claroline footer ---------------
require_once get_path('incRepositorySys') . '/claro_init_footer.inc.php';	

?>