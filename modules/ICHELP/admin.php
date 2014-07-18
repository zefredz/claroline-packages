<?php // $Id$

/** Online Help Form
 *
 * @version     ICHELP 0.9 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICHELP
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

$tlabelReq = 'ICHELP';

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

FromKernel::uses(
    'utils/input.lib',
    'utils/validator.lib',
    'display/layout.lib',
    'sendmail.lib' );

From::Module( 'ICHELP' )->uses(
    'ticketmanager.lib',
    'ticketlist.lib',
    'utils.lib' );

$dialogBox = new DialogBox();

if( claro_is_platform_admin() )
{
    try
    {
        $ticketList = new TicketList();
        
        $userInput = Claro_UserInput::getInstance();
        $cmd = $userInput->get( 'cmd' );
        $ticketId = $userInput->get( 'ticketId' );
        $chronologicOrder = (boolean)$userInput->get( 'order' );
        $failedOnly = (boolean)$userInput->get( 'failed' );
        
        $ticketList->load( $failedOnly , $chronologicOrder );
        
        if( $ticketId && $ticketList->ticketExists( $ticketId ) )
        {
            $ticket = new TicketManager( $ticketId );
            $ticketData = unserialize( $ticket->get( 'userInfos' ) );
            
            switch( $cmd )
            {
                case 'readDescription':
                    $info = new ModuleTemplate( 'ICHELP' , 'info.tpl.php' );
                    $info->assign( 'ticket' , $ticket );
                    $info->assign( 'ticketData' , $ticketData );
                    $dialogBox->info( $info->render() );
                    break;
                
                case 'resendMail':
                    $mailBody = new ModuleTemplate( 'ICHELP' , 'mail.tpl.php' );
                    $mailBody->assign( 'ticket' , $ticket );
                    $mailBody->assign( 'userData' , $ticketData );
                    $mailBody->assign( 'autoMail' , false );
                    
                    $mailTo = 'icampus@uclouvain.be';   // <- l'adresse iCampus
                    $nameTo = 'Support iCampus';
                    
                    $mailFrom = $ticketData[ 'mail' ];
                    $nameFrom = $ticketData[ 'firstName' ] . ' ' . $ticketData[ 'lastName' ];
                    
                    $subject = $ticket->get( 'shortDescription' );
                    
                    if( claro_mail( 'ICHELP: ' . $subject , $mailBody->render() , $mailTo , $nameTo , $mailFrom , $nameFrom ) )
                    {
                        $ticket->update( 'mailSent' , 1 );
                        
                        $dialogBox->success( get_lang( 'Mail successfully sent' ) );
                    }
                    else
                    {
                        $dialogBox->error( get_lang( 'Mail sending failed' ) );
                    }
                    break;
                
                case 'closeTicket':
                    if( $ticket->update( 'status' , 'closed' ) )
                    {
                        $dialogBox->success( get_lang( 'Ticket successfully closed' ) );
                    }
                    else
                    {
                        $dialogBox->error( get_lang( 'This ticket cannot be closed' ) );
                    }
                    break;
            }
        }
        
        $view = new ModuleTemplate( 'ICHELP' , 'ticketlist.tpl.php' );
        $view->assign( 'ticketList' , $ticketList->getTicketList( true ) );
        
        $pageTitle = array( 'mainTitle' => get_lang( 'Raiders of the Lost Tickets' ) );
        
        $cmdList[] = array(
            'img'  => 'back',
            'name' => get_lang( 'Back' ),
            'url'  => get_path( 'rootWeb' ) . '/claroline/admin/module/module_list.php?typeReq=applet' );
        
        $cmdList[] = array(
            'img' => '',
            'name' => get_lang( 'Toggle chronology' ),
            'url'  => claro_htmlspecialchars( Url::Contextualize( get_module_url( 'ICHELP' )
                                              .'/admin.php?order=' . (int)(! $ticketList->chronologicOrder ) ) ) );
        
        Claroline::getInstance()->display->body->appendContent(
            claro_html_tool_title( $pageTitle , null , $cmdList )
            . $dialogBox->render()
            . $view->render() );
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
}
else
{
    $dialogBox->error( 'Not allowed' );
    Claroline::getInstance()->display->body->appendContent( $dialogBox->render() );
}

echo Claroline::getInstance()->display->render();