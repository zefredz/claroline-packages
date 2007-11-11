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

if( count( get_included_files() ) == 1 )
{
    die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead');
}

// Breadcrumps
$interbredcrump[]= array ( 'url' => 'index.php', 'name' => get_lang('Netquiz'));
$interbredcrump[]= array ( 'url' => NULL, 'name' => get_lang('View the statistics'));

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

// info et erreur
if(isset($error)) 
{
	if($error != 0) 
	{
		 $output->append('<ul class="error">');
			$output->append($error_message);
		 $output->append('</ul>');
	}
}

if(isset($confirm)) 
{
	 $output->append('<ul class="info">');
		$output->append($confirm);
	 $output->append('</ul>');
}
  
// Declaration de la Class netquiz	
$netquiz = new netquiz();
    
// Class netquiz : recuperation toutes les infos de la table quizs
$selectQuizsList = netquiz::selectQuizsListDate();

$output->append( '<h3>' . get_lang("View the statistics") . '</h3>');

if($is_allowedToAdmin == true) 
{
    // Affichage quand on est administrateur
    $output->append( '<table class="claroTable emphaseLine widthTable" summary="' . get_lang("List of quizs") . '">' );
    	$output->append( '<thead>' );
    		$output->append( '<tr class="headerX">' );
    			$output->append( '<th>' . get_lang("List of quizs") . '</th>' );
    			$output->append( '<th>' . get_lang("Date") . '</th>' );
    		$output->append( '</tr>' );
    	$output->append( '</thead>' );
    	
    	$output->append( '<tbody>' );
    	
    	if ($selectQuizsList)
    	{        
        	foreach ( $selectQuizsList as $quizsList )	
        	{
        		$sQuizName = htmlentities( $quizsList['QuizName'] );
        		//$repQuizId = $quizsList['RepQuizId'];
        		$iIDQuiz = $quizsList['IDQuiz'];
        		$sVersionDate = date( $sDefaultDateHourFormat,$quizsList['TS_VersionDate'] );
        		//$sStatut = ( $quizsList['Actif'] == '0' ? $sLR['ql_naq_lbl'] : $sLR['ql_aq_lbl'] );
        		
        		$output->append('<tr>');
        		$output->append('  <td><a href="index.php?fuseaction=viewAllQuizsStats&amp;id='.$iIDQuiz.'">'.$sQuizName.'</a></td>');
        		$output->append('  <td class="center">'.$sVersionDate.'</td>');
        		$output->append('</tr>');
        	}
    	}
        else
        {
    		$output->append( '<tr>' );
    		$output->append( '<td>' . get_lang("Aucun exercice de trouvé !") . '</td>' );
    		$output->append( '<td>&nbsp;</td>' );
    		$output->append( '</tr>' );
        }
        
    	$output->append( '</tbody>' );
    $output->append( '</table>	' );		
}

// print display
echo $output->getContents();
		
// ------------ Claroline footer ---------------
require_once get_path('incRepositorySys') . '/claro_init_footer.inc.php';	

?>