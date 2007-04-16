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
     * @package CLFAQ
     */

$tlabelReq = 'CLFAQ';

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
require_once "lib/faq.class.php";
require_once "lib/category.class.php";

$output = new Output_Buffer;

$moduleImageRepositorySys = dirname(__FILE__).'/img';
$moduleImageRepositoryWeb = dirname($_SERVER['PHP_SELF']).'/img';

// set style

$htmlHeadXtra[] = '<link rel="stylesheet" type="text/css" href="./css/faq.css" media="screen, projection, tv" />' . "\n";
$htmlHeadXtra[] = '<script type="text/javascript" src="./js/fct_js.js" ></script>' . "\n";

// switch

$fuseaction = isset($_GET['fuseaction']) ? $_GET['fuseaction'] : 'default';

switch($fuseaction) {

	// Gestion des faq
	case "create_faq":
		include dirname(__FILE__).'/act_prep_create_faq.php';
		include dirname(__FILE__).'/dsp_create_faq.php';
		break;
		
	case "edit_faq":
		include dirname(__FILE__).'/act_prep_create_faq.php';
		include dirname(__FILE__).'/dsp_create_faq.php';
		break;
	
	case "validate_faq":
		include dirname(__FILE__).'/fct_validate_form.php';
		include dirname(__FILE__).'/act_validate_faq.php';
		if($error == 1) 
		{
			include dirname(__FILE__).'/act_prep_create_faq.php';
			include dirname(__FILE__).'/dsp_create_faq.php';
		}
		else
		{
			include dirname(__FILE__).'/act_prep_search.php';
			include dirname(__FILE__).'/dsp_list_category.php';
		}
		break;
		
	case "delete_faq":
		if((array_key_exists('confirm',$_GET)) && ('true' == $_GET['confirm']))
		{
			include dirname(__FILE__).'/act_delete.php';
			include dirname(__FILE__).'/act_prep_search.php';
			include dirname(__FILE__).'/dsp_list_category.php';
		}
		else
		{
			include dirname(__FILE__).'/act_prep_delete.php';
			include dirname(__FILE__).'/dsp_confirm_delete.php';
		}
		break;

	case "faq":
		include dirname(__FILE__).'/act_prep_create_faq.php';
		include dirname(__FILE__).'/act_prep_search.php';
		include dirname(__FILE__).'/dsp_faq.php';
		break;
	
	case "faq_content":
		include dirname(__FILE__).'/act_prep_create_faq.php';
		include dirname(__FILE__).'/act_prep_search.php';
		include dirname(__FILE__).'/dsp_faq_content.php';
		break;
		
	// Gestion des catégories
	case "create_category":
		include dirname(__FILE__).'/act_prep_create_category.php';
		include dirname(__FILE__).'/dsp_create_category.php';
		break;
		
	case "management_category":
		include dirname(__FILE__).'/act_prep_create_category.php';
		include dirname(__FILE__).'/dsp_management_category.php';
		break;
	
	case "edit_category":
		include dirname(__FILE__).'/act_prep_create_category.php';
		include dirname(__FILE__).'/dsp_create_category.php';
		break;
	
	case "validate_category":
		include dirname(__FILE__).'/fct_validate_form.php';
		include dirname(__FILE__).'/act_validate_category.php';
		if($error == 1) 
		{
			include dirname(__FILE__).'/act_prep_create_category.php';
			include dirname(__FILE__).'/dsp_create_category.php';
		}
		else
		{
			include dirname(__FILE__).'/dsp_management_category.php';
		}
		break;
		
	case "delete_category":
		if((array_key_exists('confirm',$_GET)) && ('true' == $_GET['confirm']))
		{
			include dirname(__FILE__).'/act_delete.php';
			include dirname(__FILE__).'/dsp_management_category.php';
		}
		else
		{
			include dirname(__FILE__).'/act_prep_delete.php';
			include dirname(__FILE__).'/dsp_confirm_delete.php';
		}
		break;
		
	// Search
	case "validate_search":
		include dirname(__FILE__).'/fct_validate_form.php';
		include dirname(__FILE__).'/act_validate_search.php';
		
		if($error == 1) 
		{
			
			include dirname(__FILE__).'/act_prep_create_faq.php';
			include dirname(__FILE__).'/act_prep_search.php';
			
			if('default' == $form_dest)
			{
				include dirname(__FILE__).'/dsp_list_category.php';
			}
			elseif('faq' == $form_dest)
			{
				include dirname(__FILE__).'/dsp_faq.php';
			}
			elseif('faq_content' == $form_dest)
			{
				include dirname(__FILE__).'/dsp_faq_content.php';
			}
			else
			{
				claro_die( get_lang('Not allowed action !') );
			}
			
		}	
		else
		{
			
			include dirname(__FILE__).'/act_prep_create_faq.php';
			include dirname(__FILE__).'/act_prep_search.php';
			include dirname(__FILE__).'/dsp_search.php';
			
		}
		break;
	
	default:		
		include dirname(__FILE__).'/act_prep_search.php';
		include dirname(__FILE__).'/dsp_list_category.php';
		break;

}
?>