<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 2.0.1 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

$tlabelReq = 'UCREPORT';

require_once dirname(__FILE__) . '/../../../claroline/inc/claro_init_global.inc.php';

$hide_banner = true;
$hide_footer = true;

Claroline::getInstance()->setDisplayType(Claroline::POPUP);
Claroline::getInstance()->display->body->appendContent( get_block( 'blockReportHelp' ) );

echo Claroline::getInstance()->display->render();