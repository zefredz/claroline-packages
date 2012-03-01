<?php // $Id$
/**
 * Restore profile list
 *
 * @version     PRESTORE $Revision$ - Claroline 1.11
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     PRESTORE
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

$tlabelReq = 'PRESTORE';

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

From::Module( 'PRESTORE' )->uses('init_profile_right.lib');

$tbl = claro_sql_get_main_tbl( 'right_profile' );

Claroline::getDatabase()->exec( "TRUNCATE `{$tbl['right_profile']}`;");

try
{
    create_required_profile();
}
catch( Exception $e )
{
    if ( claro_debug_mode() )
    {
        $errorMsg = '<pre>' . $e->__toString() . '</pre>';
    }
    else
    {
        $errorMsg = $e->getMessage();
    }
}

Claroline::getInstance()->display->body->appendContent( '<h3>Success!</h3>' );
echo Claroline::getInstance()->display->render();