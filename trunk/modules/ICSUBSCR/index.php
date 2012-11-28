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
    'sessionlist.lib',
    'hidable.lib',
    'session.lib',
    'slot.lib',
    'subscription.lib',
    'result.lib',
    'dateutil.lib',
    'message.lib' );

$message = new Message();

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
            'exDeleteSession',
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
    
    //$userGroupList = get_user_group_list( claro_get_current_user_id() );
    $userInput = Claro_UserInput::getInstance();
    $userInput->setValidator( 'cmd' , new Claro_Validator_AllowedList( $actionList ) );
    $cmd = $userInput->get( 'cmd' , 'rqShowSessionList' );
    $sessionId = $userInput->get( 'sessionId' , null );
    $data = $userInput->get( 'data' );
    
    $dateUtil = new DateUtil( get_lang( '_date' ) );
    $session = new Session( $sessionId );
    $sessionList = new SessionList();
    
    $template = 'sessionlist';
    
    switch( $cmd )
    {
        case 'rqShowSessionList':
            break;
        
        case 'rqShowSession':
        case 'rqUnsubscribe':
        case 'rqCreateSession':
        case 'rqModifySession':
        case 'rqModifySlot':
        case 'rqDeleteSlot':
        case 'rqShowSessionResult':
            $template = 'sessionedit';
            break;
        
        case 'rqDeleteSession':
            $xid = array( 'sessionId' => $sessionId );
            $message->addMsg( 'question' , 'Delete this session?' , 'exDeleteSession' , $xid );
            break;
        
        case 'exCreateSession':
        case 'exModifySession':
            if( $data['openingDate'] )
            {
               $data['openingDate'] = $dateUtil->in( $data['openingDate'] );
            }
            
            if( $data['closingDate'] )
            {
                $data['closingDate'] = $dateUtil->in( $data['closingDate'] );
            }
            
            if( ! $data['title'] || ! $data['description'] )
            {
                $message->addMsg( 'error' , 'Missing fields' );
            }
            
            if( $session->getId() )
            {
                $data['type'] = $session->getType();
            }
            
            $session->setData( $data );
            
            if( ! $message->hasError() )
            {
                if( $session->getId() )
                {
                    $action = 'modified';
                    $ok = $session->save();
                }
                else
                {
                    $action = 'created';
                    $ok = $session->save() && $sessionList->add( $session->getId() );
                }
                
                if( $ok )
                {
                    $message->addMsg( 'success' , 'Session successfully ' . $action );
                }
                else
                {
                    $message->addMsg( 'error' , 'Session cannot be ' . $action );
                }
            }
            
            $template = 'sessionedit';
            break;
        
        case 'exDeleteSession':
            if( $sessionList->remove( $session->getId() ) && $session->delete() )
                {
                    $message->addMsg( 'success' , 'Session successfully deleted' );
                }
                else
                {
                    $message->addMsg( 'error' , 'Session cannot be deleted' );
                }
            break;
        
        case 'exCloseSession':
            if( ! $session->close() )
                {
                    $message->addMsg( 'error' , 'Session cannot be closed' );
                }
            break;
        
        case 'exOpenSession':
            if( ! $session->open() )
                {
                    $message->addMsg( 'error' , 'Session cannot be opened' );
                }
            break;
        
        case 'exShowSession':
            if( ! $session->show() )
                {
                    $message->addMsg( 'error' , 'Cannot change the visibility' );
                }
            break;
        
        case 'exHideSession':
            if( ! $session->hide() )
                {
                    $message->addMsg( 'error' , 'Cannot change the visibility' );
                }
            break;
        
        case 'exMoveSessionUp':
            if( ! $sessionList->up( $sessionId ) )
                {
                    $message->addMsg( 'error' , 'Cannot change the rank' );
                }
            break;
        
        case 'exMoveSessionDown':
            if( ! $sessionList->down( $sessionId ) )
                {
                    $message->addMsg( 'error' , 'Cannot change the rank' );
                }
            break;
        
        case 'exCreateSlot':
        case 'exModifySlot':
            $ok = true;
            
            foreach( $data as $slotData )
            {
                if( $slotData['startDate'] )
                {
                   $slotData['startDate'] = $dateUtil->in( $slotdata['startDate'] );
                }
                
                if( $slotData['endDate'] )
                {
                    $slotData['endDate'] = $dateUtil->in( $slotData['endDate'] );
                }
                
                if( $slotData['label'] )
                {
                    $message->addMsg( 'error' , 'Missing slot label' );
                }
                
                if( $session->getType() != Session::TYPE_UNDATED && ! $slotData['startDate'] )
                {
                    $message->addMsg( 'error' , 'missing start date' );
                }
                
                if( $session->getType() == Session::TYPE_TIMESLOT && ! $slotData['endDate' ] )
                {
                    $message->addMsg( 'error' , 'missing end date' );
                }
                
                $slot = new Slot( $sessionId );
                $slot->setData( $slotData );
                
                if( ! $message->hasError() )
                {
                    if( $session->getId() )
                    {
                        $action = 'modified';
                        $ok = $slot->save( $slotData );
                    }
                    else
                    {
                        $action = 'created';
                        $ok = $slot->save() && $session->addSlot( $slot->getId() ) && $ok;
                    }
                }
            }
            
            if( $ok )
            {
                $message->addMsg( 'success' , 'Slot successfully ' . $action );
            }
            else
            {
                $message->addMsg( 'error' , 'Slot cannot be ' . $action );
            }
            break;
        
        case 'rqCreateSlot':
            $template = 'createslot';
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
    
    $pageTitle = array( 'mainTitle' => get_lang( 'Subscriptions' ) );
    $cmdList = array();
    $advancedCmdList = array();
    $assignList = array();
    
    switch( $template )
    {
        case 'sessionlist':
            $assignList[ 'sessionList' ] = $sessionList;
            
            if( claro_is_allowed_to_edit() )
            {
                $cmdList[] = array( 'img'  => 'new',
                        'name' => get_lang( 'create a new session' ),
                        'url'  => htmlspecialchars( Url::Contextualize( get_module_url( $tlabelReq )
                                .'/index.php?cmd=rqCreateSession' ) ) );
            }
            break;
        
        case 'sessionedit':
            $assignList[ 'session' ] = $session;
            
            if( $session->getId() )
            {
                $cmdList[] = array( 'img'  => 'new',
                        'name' => get_lang( 'create new slots' ),
                        'url'  => htmlspecialchars( Url::Contextualize( get_module_url( $tlabelReq )
                                .'/' . $session->getType() . '.php?sessionId=' . $session->getId() ) ) );
            }
            break;
        
        case 'createslot':
            $assignList[ 'sessionId' ] = $session->getId();
            $assignList[ 'sessionType' ] = $session->getType();
            
            $cmdList[] = array( 'img'  => 'back',
                    'name' => get_lang( 'Back' ),
                    'url'  => htmlspecialchars( Url::Contextualize( get_module_url( $tlabelReq )
                            .'/index.php?cmd=rqModifySession&sessionId=' . $session->getId() ) ) );
            break;
        
        default:
        {
            throw new Exception( 'bad template name or template not defined' );
        }
    }
    
    $view = new ModuleTemplate( $tlabelReq , $template . '.tpl.php' );
    
    foreach( $assignList as $assignedName => $assign )
    {
        $view->assign( $assignedName , $assign );
    }
    
    Claroline::getInstance()->display->body->appendContent(
        claro_html_tool_title( $pageTitle , null , $cmdList , $advancedCmdList )
        . $message->render()
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
    
    $message->error( '<strong>' . get_lang( 'Error' ) . ' : </strong>' . $errorMsg );
    Claroline::getInstance()->display->body->appendContent( $message->render() );
}

echo Claroline::getInstance()->display->render();