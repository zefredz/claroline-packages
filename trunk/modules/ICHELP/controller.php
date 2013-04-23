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
    $userId = claro_get_current_user_id();
    
    JavascriptLoader::getInstance()->load( 'ichelp_form' );
    
    if( ! $userId )
    {
        JavascriptLoader::getInstance()->load( 'antibot' );
    }
    
    $ticket = new TicketManager();
    
    include dirname(__FILE__) . '/locale.inc.php';
    
    $userInput = Claro_UserInput::getInstance();
    $formData = $userInput->get( 'data' );
    
    $view = null;
    $autoMailContent = null;
    $autoMailSent = null;
    
    $defaultUserData = array(
        'userId' => null,
        'firstName' => null,
        'lastName' => null,
        'mail' => null,
        'username' => null,
        'officialCode' => null,
        'jsEnabled' => null,
        'courseId' => null,
        'courseCode' => null,
        'UCLMember' => null,
        'isManager' => null,
        'urlOrigin' => null,
        'issueDescription' => null );
    
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
        $courseData = claro_get_course_data( $courseId );
        
        $userData[ 'courseId' ] = $courseId;
        $userData[ 'courseCode' ] = $courseData[ 'officialCode' ];
    }
    
    if( $formData )
    {
        $error = false;
        $userData = array_merge( $userData , $formData );
        
        if( ! $userId && ! $userInput->get( 'antibot' ) )
        {
            $error = get_lang( 'javascript not activated' );
        }
        elseif( empty( $userData[ 'lastName' ] )
           || empty( $userData[ 'firstName' ] )
           || empty( $userData[ 'mail' ] )
           || empty( $userData[ 'issueType' ] ) )
        {
            $error = get_lang( 'Required information missing' );
        }
        elseif( ! is_mail( $userData[ 'mail' ] ) )
        {
            $error = get_lang( 'Invalid mail' );
        }
        else
        {
            $userData[ 'issueDescription' ] = str_replace( "'" , "&acute;" , $userData[ 'issueDescription' ] );
            $ticket->set( 'issueDescription' , $userData[ 'issueDescription' ] );
            
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
            
            $ticket->set( 'shortDescription' , $subject );
            
            $mailTo = 'icampus@uclouvain.be';   // <- l'adresse iCampus
            $nameTo = 'Support iCampus';
            
            /* REDIRECTION VERS LE SERVICE DESK ===>
            if( (int)$userData['UCLMember']
                && ( $userData[ 'issueType' ] == 'firstAccessProblem'
                    || $userData[ 'issueType' ] == 'accessProblem' ) )
            {
                $mailTo = 'service-desk@uclouvain.be'; // <- l'adresse du service desk
                $nameTo = 'Service Desk UCL';
            }
            // <=== REDIRECTION VERS LE SERVICE DESK */
            
            if( $mailTpl )
            {
                $autoMail = new ModuleTemplate( 'ICHELP' , 'auto/' . $mailTpl . '.tpl.php' );
                $autoMail->assign( 'userData' , $userData );
                
                
                /* à décommenter si on décide d'afficher la réponse automatique directement dans la page (en plus du mail)
                $autoMail = $autoMail->render();
                $autoMailContent = $header . strip_tags( str_replace( '<br />' , "\n" , $autoMail ) ) . $footer;
                */
                $autoMailContent = $header . $autoMail->render() . $validator . $footer;
                
                $mailSent = claro_mail( 'Re:' . $subject , $autoMailContent , $mailFrom , $nameFrom , $mailTo , $nameTo );
                $ticket->set( 'autoMailSent' , $mailSent );
            }
            
            $mailBody = new ModuleTemplate( 'ICHELP' , 'mail.tpl.php' );
            $mailBody->assign( 'userData' , $userData );
            $mailBody->assign( 'ticket' , $ticket );
            $mailBody->assign( 'autoMailContent' , $autoMailContent ? $autoMail->render() : false );
            $mailBody->assign( 'mailSent' , $mailSent );
            
            if( claro_mail( 'ICHELP: ' . $subject , $mailBody->render() , $mailTo , $nameTo , $mailFrom , $nameFrom ) )
            {
                $ticket->set( 'mailSent' , 1 );
                $ticket->set( 'userInfos' , serialize( $userData ) );
                $ticket->save();
                $ticket->flush();
            }
            else
            {
                $error = get_lang( 'Mail sending failed' );
            }
        }
        
        if( ! $error )
        {
            $view = new ModuleTemplate( 'ICHELP' , 'ok.tpl.php' );
            $view->assign( 'backUrl' , $ticket->get( 'urlOrigin' ) );
            
            $dialogBox->success( get_lang( 'Your request has been succesfully sent' ) );
        }
        else
        {
            $dialogBox->error( '<span style="color: red; font-weight: bold;">' . $error . '</span>' );
        }
        
        /* Affiche également le contenu du mail envoyé directement dans la page
        if( $autoMailContent )
        {
            $dialogBox->info( $autoMail );
        }
        */
    }
    else
    {
        $encodedFrom = $userInput->get( 'from' );
        $urlOrigin = $encodedFrom
            ? base64_decode( $encodedFrom )
            : get_path( 'rootWeb' );
        $ticket->set( 'urlOrigin' , $urlOrigin );
        
        $dialogBox->info( '<span style="color: green; font-weight: bold;">' . get_lang( 'welcome_message' ) . '</span>' );
    }
    
    if( ! $view )
    {
        $view = new ModuleTemplate( 'ICHELP' , 'form.tpl.php' );
        $view->assign( 'userData' , $userData );
        $view->assign( 'ticket' , $ticket );
        $view->assign( 'checkList' , $checkList );
    }
    
    $pageTitle = array( 'mainTitle' => get_lang( 'Online Help Form' ) );
    
    $cmdList[] = array(
        'img'  => 'back',
        'name' => get_lang( 'Back' ),
        'url'  => claro_htmlspecialchars( $ticket->get( 'urlOrigin' ) ) );
    
    Claroline::getInstance()->display->body->appendContent(
        claro_html_tool_title( $pageTitle , null , $cmdList ) .
        $dialogBox->render() .
        $view->render() );
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