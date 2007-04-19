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

//

install_module_in_course( $tlabelReq, claro_get_current_course_id() );

// lib
require_once "lib/buffer.class.php";
//require_once "lib/faq.class.php";
//require_once "lib/category.class.php";
$output = new Output_Buffer;

$moduleImageRepositorySys = dirname(__FILE__).'/img';
$moduleImageRepositoryWeb = dirname($_SERVER['PHP_SELF']).'/img';

// set style

//$htmlHeadXtra[] = '<link rel="stylesheet" type="text/css" href="./css/faq.css" media="screen, projection, tv" />' . "\n";
$htmlHeadXtra[] = '<script type="text/javascript" src="./js/fct_js.js" ></script>' . "\n";

// switch

$fuseaction = isset($_GET['fuseaction']) ? $_GET['fuseaction'] : 'default';

switch($fuseaction) {

	case "netquiz":
		include dirname(__FILE__).'/netquiz/index.php';
		break;
	
	default:		
		include dirname(__FILE__).'/dsp_list_exercice.php';
		break;

}

?>