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
$interbredcrump[]= array ( 'url' => NULL, 'name' => get_lang('Statistics') );

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
        : false
        ;
    
    $iIDQuiz = $_GET["id"];
    $sGetParam = "&amp;id=$iIDQuiz";
	
    // Declaration de la Class netquiz		
	$netquiz = new netquiz();
    
if( $statCurrentUser == false && $is_allowedToAdmin == true ) 
{
    // Class netquiz : select quiz info
    $netquiz->setIdQuiz( $iIDQuiz );
    $QuizInfo = $netquiz->selectQuizInfo();

    $sQuizName = $QuizInfo['QuizName'];
    $dVersionDate = date($sDefaultDateFormat,$QuizInfo['VersionDate']);
    //$sPassword = $QuizInfo['Password'];
    //$iActif = $QuizInfo['Actif'];
    $fPonderationTotal = $QuizInfo['PonderationTotal'];

    // Nombre de participations et premiere date
    $netquiz->setIdQuiz( $iIDQuiz );

    $NumberParticipationsAndDate = $netquiz->selectNumberParticipationsAndDate();
    $NumberParticipationsAndDate->setPonderationTotal( $fPonderationTotal );
    $NumberParticipationsAndDate->compute();
    
    $sTitle = $sQuizName . "&nbsp;(&nbsp;$dVersionDate&nbsp;)";
    $sTabs = array($sLR["q_stats_link"],$sLR["q_quest_link"],$sLR["q_part_link"]);
    $sTabsLinks = array("index.php?fuseaction=viewAllQuizsStats","index.php?fuseaction=viewAllQuizsQuestions","index.php?fuseaction=viewAllQuizParticipations");
    $iSelectedTab = 0;
}
else
{
    // Class netquiz : select quiz info
    $netquiz->setIdQuiz( $iIDQuiz );
    $QuizInfo = $netquiz->selectQuizInfo();

    $sQuizName = $QuizInfo['QuizName'];
    $dVersionDate = date($sDefaultDateFormat,$QuizInfo['VersionDate']);
    //$sPassword = $QuizInfo['Password'];
    //$iActif = $QuizInfo['Actif'];
    $fPonderationTotal = $QuizInfo['PonderationTotal'];

    // Nombre de participations et premiere date
    $netquiz->setIdQuiz( $iIDQuiz );
    $netquiz->setCurrentUserId( claro_get_current_user_id() );

    $NumberParticipationsAndDate = $netquiz->selectNumberParticipationsAndDateCurrentUser();
    $NumberParticipationsAndDate->setPonderationTotal( $fPonderationTotal );
    $NumberParticipationsAndDate->compute();
    
    $sTitle = $sQuizName . "&nbsp;(&nbsp;$dVersionDate&nbsp;)";
    $sTabs = array($sLR["q_stats_link"],$sLR["q_quest_link"],$sLR["q_part_link"]);
    $sTabsLinks = array("index.php?fuseaction=viewAllQuizsStats&amp;statCurrentUser=1","index.php?fuseaction=viewAllQuizsQuestions&amp;statCurrentUser=1","index.php?fuseaction=viewAllQuizParticipations&amp;statCurrentUser=1");
    $iSelectedTab = 0;
}

$output->append( '<div id="netquiz">' );

    $output->append( '<h3>' . $sTitle . '</h3>');

    $output->append('    
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
    <table width="750" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td width="100%" class="ContentCell" align="left" style="padding:5px;">
            <br />
            <strong>' . $sLR["q_nbp_lbl"] . ' :</strong> ' . $NumberParticipationsAndDate->getNbParticipations() . '<br />
            <strong>' . $sLR["q_ls_lbl"] . ' :</strong> ' . $NumberParticipationsAndDate->getDateLastParticipation() . '<br />
            <strong>' . $sLR["q_avg_lbl"] . ' :</strong> ' . $NumberParticipationsAndDate->getAverageHTML() . '<br />
            <strong>' . $sLR["q_med_lbl"] . ' :</strong> ' . $NumberParticipationsAndDate->getMedianeHTML() . '<br />
            <strong>' . $sLR["q_gt60_lbl"] . ' :</strong> ' . $NumberParticipationsAndDate->getNbPartGT60() . '<br />
            <br />
        </td>
    </tr>
    </table>
    ');                                

$output->append( '</div>' );

// print display
echo $output->getContents();
		
// ------------ Claroline footer ---------------
require_once get_path('incRepositorySys') . '/claro_init_footer.inc.php';	

?>