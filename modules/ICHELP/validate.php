<?php // $Id$

/** Online Help Form
 *
 * @version     ICHELP 0.8 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICHELP
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

$tlabelReq = 'ICHELP';

$courseId = isset( $_REQUEST[ 'cidReq' ] ) ? $_REQUEST['cidReq'] : null;
unset( $_REQUEST[ 'cidReq' ] );

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

FromKernel::uses(
    'utils/input.lib',
    'utils/validator.lib',
    'display/layout.lib',
    'sendmail.lib' );

From::Module( 'ICHELP' )->uses(
    'ticketmanager.lib',
    'utils.lib' );

$dialogBox = new DialogBox();

try
{
    $userInput = Claro_UserInput::getInstance();
    $ticketId = $userInput->get( 'ticketId' );
    
    $ticket = new TicketManager( $ticketId );
    $ticket->update( 'status' , 'solved' );
    
    $userInfos = json_decode( $ticket->get( 'userInfos' ) , true );
    $from = $userInfos[ 'firstName' ] . ' ' . $userInfos[ 'lastName' ];
    $mail = $userInfos[ 'mail' ];
    $subject = $ticket->get( 'shortDescription' );
    
    $content = get_lang( 'confirmation_message' , array( '%name' => $from , '%ticket' => $ticketId ) );
    
    claro_mail( get_lang( 'Solved : ' ) . $subject , $content , 'icampus@uclouvain.be' , 'Support iCampus' , $mail , $from );
    
    $dialogBox->success( get_lang( 'Thanks' ) );
    $pageTitle = array( 'mainTitle' => get_lang( 'Online Help Form' ) );
    
    Claroline::getInstance()->display->body->appendContent(
        claro_html_tool_title( $pageTitle ) . $dialogBox->render() );
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