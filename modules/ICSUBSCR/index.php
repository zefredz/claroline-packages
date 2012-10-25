<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.1 $Revision$ - Claroline 1.11
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICSUBSCR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

$tlabelReq = 'ICSUBSCR';

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

FromKernel::uses(
    'utils/input.lib',
    'utils/validator.lib',
    'display/layout.lib' );

From::Module( 'ICSUBSCR' )->uses(
    'list.lib',
    'hidable.lib',
    'session.lib',
    'slot.lib',
    'subscription.lib',
    'result.lib',
    //'layout.lib',
    'dateutil.lib' );

try
{
    ////////////////
    // CONTROLLER //
    ////////////////
    
    $actionList = array(
        'rqShowSessionList',
        'rqShowSession',
        'exSubscribe',
        'rqUnsubscribe',
        'exUnsubscribe' );
    
    if( claro_is_allowed_to_edit() )
    {
        $restrictedActionList = array(
            'rqCreateSession',
            'exCreateSession',
            'rqModifySession',
            'exModifySession',
            'rqDeleteSession',
            'exdeleteSession',
            'exMoveSessionUp',
            'exMoveSessionDown',
            'exOpenSession',
            'exCloseSession',
            'exShowSession',
            'exHideSession',
            'rqCreateSlot',
            'exCreateSlot',
            'rqModifySlot',
            'exModifySlot',
            'rqDeleteSlot',
            'exDeleteSlot',
            'exMoveSlotUp',
            'exMoveSlotDown',
            'rqShowSessionResult' );
        
        $actionList = array_merge( $restrictedActionList , $actionList );
    }
    
    $userInput = Claro_UserInput::getInstance();
    $userInput->setValidator( 'cmd' , new Claro_Validator_AllowedList( $actionList ) );
    $cmd = $userInput->get( 'cmd' , 'rqShowSessionList' );
    $sessionId = $userInput->get( 'sessionId' , null );
    
    $sessionList = new ICSUBSCR_List();
    $session = new Session( $sessionId );
    
    switch( $cmd )
    {
        case 'rqShowSessionList':
        case 'rqShowSession':
        case 'rqUnsubscribe':
        case 'rqCreateSession':
        case 'rqModifySession':
        case 'rqDeleteSession':
        case 'rqCreateSlot':
        case 'rqModifySlot':
        case 'rqDeleteSlot':
        case 'rqShowSessionResult':
            break;
        
        default:
        {
            throw new Exception( 'bad command' );
        }
    }
    
    //////////
    // VIEW //
    //////////
    
    CssLoader::getInstance()->load( 'kalendae' , 'screen' );
    CssLoader::getInstance()->load( 'main' , 'screen' );
    
    JavascriptLoader::getInstance()->load('kalendae');
    JavascriptLoader::getInstance()->load('dateutil');
    
    $dialogBox = new DialogBox();
    
    $pageTitle = array( 'mainTitle' => get_lang( 'Subscriptions' ) );
    $cmdList = array();
    $advancedCmdList = array();
    $template = 'sessionlist';
    $assignList = array( 'sessionList' => $sessionList );
    
    switch( $cmd )
    {
        case 'rqShowSessionList':
            $template = 'sessionlist';
            
            if( claro_is_allowed_to_edit() )
            {
                $cmdList[] = array( 'img'  => 'new',
                        'name' => get_lang( 'create a new session' ),
                        'url'  => htmlspecialchars( Url::Contextualize( get_module_url( $tlabelReq )
                                  .'/index.php?cmd=rqCreateSession' ) ) );
            }
            break;
        
        case 'rqCreateSession':
            $template = 'sessionedit';
            $assignList = array( 'session' => $session );
            break;
        
        default:
        {
            throw new Exception( 'bad command' );
        }
    }
    
    $view = new ModuleTemplate( $tlabelReq , $template . '.tpl.php' );
    
    foreach( $assignList as $assignedName => $assign )
    {
        $view->assign( $assignedName , $assign );
    }
    
    Claroline::getInstance()->display->body->appendContent(
        claro_html_tool_title( $pageTitle , null , $cmdList , $advancedCmdList )
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
    
    $errorReport = new DialogBox();
    $errorReport->error( '<strong>' . get_lang( 'Error' ) . ' : </strong>' . $errorMsg );
    Claroline::getInstance()->display->body->appendContent( $errorReport->render() );
}

echo Claroline::getInstance()->display->render();