<?php // $Id$
/**
 * CLAROLINE
 *
 * @version 0.1 $Revision$
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
 
$tlabelReq = 'CLLP';

require_once dirname( __FILE__ ) . '/../../../claroline/inc/claro_init_global.inc.php';

/*
 * Shared libraries
 */
include_once get_path('incRepositorySys') . '/lib/embed.lib.php';


/*
 * Output
 */

$interbredcrump[] = array ('url' => '../index.php', 'name' => get_lang('Learning path list'));

$nameTools = get_lang('Learning path');


$display = new ClarolineScriptEmbed();
$display->hideClaroBody();
$display->hideFooter();
$display->setContent(''); // no content
$display->output();
?>
