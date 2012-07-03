<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.0.1 $Revision$ - Claroline 1.9
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICSUBSCR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

$tlabelReq = 'ICSUBSCR';

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

if ( ! claro_is_in_a_course() || ! claro_is_course_allowed() ) claro_disp_auth_form( true );

FromKernel::uses(
    'utils/input.lib',
    'utils/validator.lib',
    'display/layout.lib' );

From::Module( 'CLMEETNG' )->uses(
    'subscription.lib' );

$dialogBox = new DialogBox();

try
{
    
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
    
    $dialogBox->error( '<strong>' . get_lang( 'Error' ) . ' : </strong>' . $errorMsg );
    Claroline::getInstance()->display->body->appendContent( $dialogBox->render() );
}

echo Claroline::getInstance()->display->render();