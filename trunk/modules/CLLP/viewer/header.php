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
 * Output
 */
$claroline->setDisplayType( CL_PAGE );

$interbredcrump[] = array ('url' => '../index.php', 'name' => get_lang('Learning path list'));

$nameTools = get_lang('Learning path');

$claroline->display->body->hideClaroBody();
$claroline->display->footer->hide();
$claroline->display->body->setContent(''); // no content

echo $claroline->display->render();
?>