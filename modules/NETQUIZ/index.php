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

$tlabelReq = 'NETQUIZ';

require_once dirname(__FILE__) . "/../../claroline/inc/claro_init_global.inc.php";

if ( ! claro_is_tool_allowed() )
{
	if ( ! claro_is_in_a_course() )
    {
		claro_disp_auth_form( true );
	}
	else
	{
		claro_die(get_lang("Not allowed"));
	}
}

// display mode

claro_set_display_mode_available(TRUE);

$is_allowedToAdmin = claro_is_allowed_to_edit();

install_module_in_course( $tlabelReq, claro_get_current_course_id() );

// lib
require_once get_path('includePath') . '/lib/file.lib.php';
require_once get_path('includePath') . '/lib/fileManage.lib.php';
require_once get_path('includePath') . '/lib/fileDisplay.lib.php';
require_once get_path('includePath') . '/lib/fileUpload.lib.php';
require_once get_path('includePath') . '/lib/pclzip/pclzip.lib.php';
require_once "lib/magic.lib.php";
require_once "lib/fileuploader.lib.php";
require_once "lib/buffer.class.php";
require_once "lib/netquizinstaller.class.php";
require_once "lib/netquiz.class.php";

// files
include_once("netquiz/langr.inc.php");
include_once("netquiz/settings.inc.php");
include_once("netquiz/functions.inc.php");

$output = new Output_Buffer;

$moduleImageRepositorySys = dirname(__FILE__).'/img';
$moduleImageRepositoryWeb = dirname($_SERVER['PHP_SELF']).'/img';

// set style
$htmlHeadXtra[] = '<link rel="stylesheet" type="text/css" href="css/styles.css" media="screen, projection, tv" />' . "\n";
$htmlHeadXtra[] = '<link rel="stylesheet" type="text/css" href="css/styles_netquiz.css" media="screen, projection, tv" />' . "\n";
$htmlHeadXtra[] = '<script type="text/javascript" src="./js/fct_js.js" ></script>' . "\n";

// switch

$fuseaction = isset($_GET['fuseaction']) ? $_GET['fuseaction'] : 'default';

switch($fuseaction) {

	case "install_quiz":
		include dirname(__FILE__).'/act_prep_upload_quiz.php';
		include dirname(__FILE__).'/dsp_upload_quiz.php';
		break;
		
	case "validate_install_quiz":
		//include dirname(__FILE__).'/fct_validate_form.php';
		include dirname(__FILE__).'/act_prep_upload_quiz.php';
		include dirname(__FILE__).'/act_validate_upload.php';
		if($error == 1) 
		{
			include dirname(__FILE__).'/act_prep_upload_quiz.php';
			include dirname(__FILE__).'/dsp_upload_quiz.php';
		}
		else
		{
			//include dirname(__FILE__).'/dsp_list_exercice.php';
            include dirname(__FILE__).'/act_prep_upload_quiz.php';
            include dirname(__FILE__).'/dsp_upload_quiz.php';
		}
		break;
		
	case "stats":
		include dirname(__FILE__).'/dsp_statistics.php';
		break;
        
    case "viewAllQuizsStats":
		include dirname(__FILE__).'/dsp_view_all_quizs_stats.php';
		break;
	
	case "viewAllQuizParticipations":
		include dirname(__FILE__).'/dsp_view_all_quizs_participations.php';
		break;
	
    case "viewAllQuizsQuestions":
		include dirname(__FILE__).'/dsp_view_all_quizs_questions.php';
		break;
    
    case "deleteQuiz":
		include dirname(__FILE__).'/act_delete_quiz.php';
		include dirname(__FILE__).'/dsp_list_exercice.php';
		break;
        
    case "editStatus":
		include dirname(__FILE__).'/act_edit_visibility_quiz.php';
		include dirname(__FILE__).'/dsp_list_exercice.php';
		break;
	
    default:		
		include dirname(__FILE__).'/dsp_list_exercice.php';
		break;
}
?>