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

if($is_allowedToAdmin == false) 
{
    claro_die( get_lang('Not allowed action !') );
}

// Breadcrumps
$interbredcrump[]= array ( 'url' => 'index.php', 'name' => get_lang('Netquiz'));
$interbredcrump[]= array ( 'url' => NULL, 'name' => get_lang('Install a new quiz'));

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

$output->append( '<h3>' . get_lang("Install a new quiz") . '</h3>' );

$output->append('
<form enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'?fuseaction='.$form_xfa_post.'" method="post">
'.claro_form_relay_context().'
<input type="hidden" name="claroFormId" value="'.uniqid('').'" />
<input type="hidden" name="cmd" value="submitImage" />
');
        
                    
$output->append('
<table>
    <tr>
        <td>'.get_lang("Upload file").' : </td>
        <td><input type="file" name="frm_file"  /></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>
            <a href="'.dirname($_SERVER['PHP_SELF']).'/'.$form_xfa_cancel.'"><input class="buttom" type="button" value="'.get_lang("Cancel").'" onclick="document.location=\''.dirname($_SERVER['PHP_SELF']).'/'.$form_xfa_cancel.''.'\'" /></a>
            <input class="buttom" type="submit" value="'.get_lang("Save").'" />
        </td>
    </tr>
</table> 
</form>
');

// print display
echo $output->getContents();
	
// ------------ Claroline footer ---------------
require_once get_path('incRepositorySys') . '/claro_init_footer.inc.php';	

?>