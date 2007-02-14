<?php // $Id$
/**
 * CLAROLINE
 *
 *
 * @version 1.0 $Revision$
 *
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLSTAT
 *
 * @author Christophe Gesché <moosh@claroline.net>
 *
 */
define('FORMAT_DISP_HTML','FORMAT_DISP_HTML'.__LINE__);
define('FORMAT_DISP_IMG','FORMAT_DISP_IMG'.__LINE__);
// this is script  is  write  for  internal work of IPM

$langFile = "tracking";
$langToolName = "Calcul des stats cumulées des cours";


$displayFormat = FORMAT_DISP_HTML;

include '../../claroline/inc/claro_init_global.inc.php';
include get_path('includePath') . '/lib/statsUtils.lib.inc.php';
include './lib/platform.stat.lib.php';
$nameTools = $langToolName;



// INSTALL Tables
// this code would be removed when module can install admin modules
include './connector/IPMSTAT.install.php';
$is_allowedToTrack = claro_is_platform_admin();
$passe = toolStat::get_current_scan_id();

$courseQty = claro_get_course_quantity();
$courseLeft = claro_get_course_not_scanned_count($passe);

// display title
$titleTab['mainTitle'] = $nameTools;
$menu[]=claro_html_cmd_link('scan.php',get_lang('Run scan'));
$menu[]=claro_html_cmd_link('results.php',get_lang('display results'));

include( get_path('incRepositorySys') . '/claro_init_header.inc.php');
echo claro_html_tool_title( $nameTools)
.    claro_html_menu_vertical($menu)
.    get_lang('There is %courseQty courses on this platform.', array('%courseQty'=>$courseQty))
.    get_lang('There still %courseLeft courses to scan in scanning session %scanId.', array('%courseLeft'=>$courseLeft,'%scanId'=>$passe))
;


include  get_path('includePath') . '/claro_init_footer.inc.php';


?>