<?php // $Id$

/** Online Help Form
 *
 * @version     ICHELP 0.5 $Revision$ - Claroline 1.11.5
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

$pageTitle = array( 'mainTitle' => get_lang( 'Online Help Form' ) );
$cmdList = array();

$dialogBox = new DialogBox();
JavascriptLoader::getInstance()->load('ichelp_form');

try
{
    include dirname(__FILE__) . '/locale.inc.php';
    
    $ticket = new TicketManager();
    
    $view = null;
    $autoAnswer = null;
    
    $defaultUserData = array(
        'userId' => null,
        'firstName' => null,
        'lastName' => null,
        'mail' => null,
        'username' => null,
        'officialCode' => null,
        'jsEnabled' => null,
        'courseId' => null,
        'UCLMember' => null,
        'isManager' => null );
    
    $userData = claro_get_current_user_data();
    
    if( $userData )
    {
        $userData = array_merge( $defaultUserData , $userData );
    }
    else
    {
        $userData = $defaultUserData;
    }
    
    if ( $courseId )
    {
        $userData[ 'courseId' ] = $courseId;
    }
    
    $userInput = Claro_UserInput::getInstance();
    $formData = $userInput->get( 'data' );
    
    if( $formData )
    {
        $error = false;
        $userData = array_merge( $userData , $formData );
        
        if( empty( $userData[ 'lastName' ] ) || empty( $userData[ 'firstName' ] ) || empty( $userData[ 'mail' ] ) )
        {
            $error = get_lang( 'Required information missing' );
        }
        elseif( ! is_mail( $userData[ 'mail' ] ) )
        {
            $error = get_lang( 'Invalid mail' );
        }
        else
        {
            $userData[ 'cookieEnabled' ] = isset( $_SERVER['HTTP_COOKIE'] );
            
            $mailFrom = $userData[ 'mail' ];
            $nameFrom = $userData[ 'firstName' ] . ' ' . $userData[ 'lastName' ];
            
            if( array_key_exists( 'issueType' , $userData ) )
            {
                $issueType = $userData[ 'issueType' ];
                $subject = get_lang( $checkList[ $issueType ][ 'description' ] );
                $mailTpl = $checkList[ $issueType ][ 'mailTpl' ];
            }
            else
            {
                $subject = get_lang( 'Unknown issue' );
                $mailTpl = false;
            }
            
            $mailBody = new ModuleTemplate( 'ICHELP' , 'mail.tpl.php' );
            $mailBody->assign( 'userData' , $userData );
            $mailBody->assign( 'ticket' , $ticket );
            $mailBody->assign( 'autoMail' , (boolean)$mailTpl );
            
            $mailTo = 'icampus@uclouvain.be';   // <- l'adresse iCampus
            $nameTo = 'Support iCampus';
            
            // REDIRECTION VERS LE SERVICE DESK ===>
            if( (int)$userData['UCLMember']
                && ( $userData[ 'issueType' ] == 'firstAccessProblem'
                    || $userData[ 'issueType' ] == 'accessProblem' ) )
            {
                $mailTo = 'icampus@uclouvain.be'; // <- l'adresse du service desk
                $nameTo = 'Service Desk UCL';
            }
            // <=== REDIRECTION VERS LE SERVICE DESK
            
            if( claro_mail( $subject , $mailBody->render() , $mailTo , $nameTo , $mailFrom , $nameFrom ) )
            {
                $ticket->set( 'mailSent' , 1 );
                
                if( $mailTpl )
                {
                    $autoMail = new ModuleTemplate( 'ICHELP' , 'auto/' . $mailTpl . '.tpl.php' );
                    $autoMail->assign( 'userData' , $userData );
                    
                    
                    /* à décommenter si on décide d'afficher la réponse automatique directement dans la page (en plus du mail)
                    $autoAnswer = $autoMail->render();
                    $content = $header . strip_tags( str_replace( '<br />' , "\n" , $autoAnswer ) ) . $footer;
                    */
                    $content = $header . $autoMail->render() . $footer;
                    
                    $mailSent = claro_mail( 'Re:' . $subject , $content , $mailFrom , $nameFrom , $mailTo , $nameTo );
                    $ticket->set( 'autoMailSent' , $mailSent );
                }
                
                $ticket->set( 'userInfos' , json_encode( $userData ) );
                $ticket->save();
            }
            else
            {
                $error = get_lang( 'Mail sending failed' );
            }
        }
        
        if( ! $error )
        {
            $view = new ModuleTemplate( 'ICHELP' , 'ok.tpl.php' );
            $view->assign( 'backUrl' , $ticket->get( 'httpReferer' ) );
            
            $dialogBox->success( get_lang( 'Your request has been succesfully sent' ) );
        }
        else
        {
            $dialogBox->error( '<span style="color: red; font-weight: bold;">' . $error . '</span>' );
        }
        
        /* Affiche également le contenu du mail envoyé directement dans la page
        if( $autoAnswer )
        {
            $dialogBox->info( $autoAnswer );
        }
        */
    }
    else
    {
        $dialogBox->info( '<span style="color: green; font-weight: bold;">' . get_lang( 'welcome_message' ) . '</span>' );
    }
    
    if( ! $view )
    {
        $view = new ModuleTemplate( 'ICHELP' , 'form.tpl.php' );
        $view->assign( 'userData' , $userData );
        $view->assign( 'ticket' , $ticket );
        $view->assign( 'checkList' , $checkList );
    }
    
    $cmdList[] = array(
        'img'  => 'back',
        'name' => get_lang( 'back' ),
        'url'  => claro_htmlspecialchars( $ticket->get( 'httpReferer' ) ) );
    
    Claroline::getInstance()->display->body->appendContent(
        claro_html_tool_title( $pageTitle , null , $cmdList ) .
        $dialogBox->render() .
        $view->render() );
    
    if( $ticket->get( 'mailSent' ) )
    {
        $ticket->flush();
    }
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