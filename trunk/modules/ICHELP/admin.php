<?php // $Id$

/** Online Help Form
 *
 * @version     ICHELP 0.4 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICHELP
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

echo Claroline::getInstance()->display->render();