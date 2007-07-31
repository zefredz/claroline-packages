<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * CLAROLINE
 *
 * $Revision$
 *
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLLP
 *
 * @author Sebastien Piraux
 *
 */

function getViewerHtmlHeaders($pathId)
{
	$headers = "\n"
	.     '<script type="text/javascript">' . "\n"
	.    '  var pathId = "'.(int) $pathId.'";' . "\n"
	.    '  var moduleUrl = "'.get_module_url('CLLP').'/";' . "\n"
	.    '  var cidReq = "'.claro_get_current_course_id().'";' . "\n"
	.    '  var debug_mode = '.get_conf('scorm_api_debug').';' . "\n"
	.    '  var jQueryPath = "'.get_module_url('CLLP').'/js/jquery.js";' . "\n"
	.    '</script>' . "\n\n"
	.    '<script type="text/javascript" src="'.get_module_url('CLLP').'/js/jquery.js"></script>' . "\n"
	.    '<script type="text/javascript" src="'.get_module_url('CLLP').'/js/jquery.frameready.js"></script>' . "\n"
	.    '<script type="text/javascript" src="'.get_module_url('CLLP').'/js/json.jquery.js"></script>' . "\n"
	.    '<script type="text/javascript" src="'.get_module_url('CLLP').'/js/CLLP.js"></script>' . "\n"
	.    '<script type="text/javascript" src="'.get_module_url('CLLP').'/viewer/scormAPI.php?pathId='.$pathId.'"></script>' . "\n\n";

	return $headers;
}

/**
 * this function should be used in tool to generate API calls required to handle
 * progression when tool is used as a learning path item using javascript
 *
 */
function reportProgression()
{

}

function checkStatusOfItem()
{

}

function hasAccessTo()
{

}

?>
