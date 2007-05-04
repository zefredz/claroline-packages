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

    $interbredcrump[]= array ( 'url' => 'index.php', 'name' => get_lang('Netquiz'));

	// --------- Claroline header and banner ----------------    
	
    require_once get_path('incRepositorySys') . "/claro_init_header.inc.php";
	
	// --------- Claroline body ----------------    
		
	// toolTitle
    $output->append(claro_html_tool_title( get_lang('Netquiz') ) . "\n");	

	// display

if($is_allowedToAdmin == true) 
{
	

	$output->append('<p><a class="claroCmd" href="javascript:openNetquiz(\''.dirname($_SERVER['PHP_SELF']).'/netquiz\')"><img src="'.get_icon("info").'" alt="'.get_lang("info").'" title="'.get_lang("info").'" /> '.get_lang('Netquiz').'</a></p>');
	
	//$output->append('<p><a class="claroCmd" href="index.php?fuseaction=netquiz"><img src="'.get_icon("info").'" alt="'.get_lang("info").'" title="'.get_lang("info").'" /> '.get_lang('Netquiz').'</a></p>');

}

$output->append('<ul>');

$repName = "/exercices";
$path = dirname(__FILE__) . $repName;

$rep=opendir( $path );
$noFolder = false;
while ( false !== ($file = readdir($rep)) )
{
	
	if( $file != ".." && $file != "." && $file != "" )
	{ 
		
		if ( is_dir($path.'/'.$file) ) 
		{
			//'.dirname($_SERVER['PHP_SELF']).$repName.'/'.$file.'
			$noFolder = true;
			$output->append('<li>');
			$output->append('<img src="'.get_icon('folder').'" alt="'.get_lang('folder').'" title="'.get_lang('folder').'">&nbsp;');
			
			
			//\''.dirname($_SERVER['PHP_SELF']).'/netquiz\', \''.claro_get_current_user_id().'\'
			//\''.dirname($_SERVER['PHP_SELF']).$repName.'/'..$file.'\', \''.claro_get_current_user_id().'\'
			$output->append('<a href="javascript:openQuiz(\''.dirname($_SERVER['PHP_SELF']).$repName.'/'.$file.'\')">'.$file.'</a>');


			//$output->append('<a href="javascript:openQuiz(\''.dirname($_SERVER['PHP_SELF']).$repName.'/'.$file.'\')">'.$file.'</a>');
			$output->append('</li>');
				
		}
		
	}
	
}

if ( $noFolder == false ) 
{
	
	$output->append('<li>'.get_lang('No exercise').'</li>');
	
}

closedir($rep);

$output->append('</ul>');

// print display
echo $output->getContents();
	
	
			
			/*
			$connexion = @mysql_connect("localhost", "root", "");
			@mysql_select_db("db_netquiz", $connexion);
	
			$sql = "SELECT * FROM `nq_quizs`";
			$result = claro_sql_query_fetch_all($sql);
			
			foreach($result as $val)
			{
				
				print ('<p>');
				print ($val['IDQuiz'].', '.$val['QuizIdent'].', '.$val['QuizVersion'].', '.$val['QuizName'].'<br />');
				print ('<a href="/claroline/v1/module/NETQUIZ/netquiz/authparticipant.php?id='.claro_get_current_user_id().'&auth=1&qi='.$val['QuizIdent'].'&qv='.$val['QuizVersion'].'">Lancer le formulaire</a>');
				print ('</p>');
			
			}
			*/	
	
	
	// ------------ Claroline footer ---------------
	
	require_once get_path('incRepositorySys') . '/claro_init_footer.inc.php';	

?>