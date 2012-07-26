<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.0.2 $Revision$ - Claroline 1.9
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

From::Module( 'ICSUBSCR' )->uses(
    'controller.lib',
    'defaultcontroller.lib',
    'lister.lib',
    'filteredlister.lib',
    'pluginloader.lib',
    'plugincontroller.lib',
    'record.lib',
    'session.lib',
    'sessionlist.lib' );

$dialogBox = new DialogBox();

try
{
    $courseData = claro_get_current_course_data();
    $lang = $courseData[ 'language' ];
    
    $userId = claro_get_current_user_id();
    $courseId = claro_get_current_course_id();
    $groupId = claro_get_current_group_id();
    $is_courseAllowed = claro_is_course_allowed();
    $is_groupAllowed = claro_is_group_allowed();
    $is_allowed_to_edit = claro_is_allowed_to_edit();
    
    $pluginRepository = get_module_path( 'ICSUBSCR' ) . '/plugins/';
    $pluginLoader = new PluginLoader( $pluginRepository , $lang );
    
    $sessionList = new SessionList( $groupId ? 'group' : 'user' );
    
    $actionList = array( 'rqShowSessionList' );
    
    if( $is_allowed_to_edit )
    {
        $restrictedActionList = array(
            'rqCreateSession',
            'exCreateSession',
            'rqEditSession',
            'exEditsession',
            'rqDeleteSession',
            'exDeleteSession',
            'exHide',
            'exShow',
            'exLock',
            'exUnlock' );
        
        $actionList = array_merge( $actionList , $restrictedActionList );
    }
    
    $userInput = Claro_UserInput::getInstance();
    
    $cmd = $userInput->get( 'cmd' , 'rqShowSessionList' );
    $sessionId = $userInput->get( 'sessionId' );
    $data = $userINput->get( 'data' );
    $msg = array();
    
    if( in_array( $cmd , $actionList ) )
    {
        switch( $cmd )
        {
            // CONTROLLER
            case 'rqShowSessionList':
            case 'rqCreateSession' :
            case 'rqEditSession' :
            case 'rqDeleteSession' :
                break;
            
            case 'rqViewSession':
            {
                $sessionType = $sessionList->get( $sessionId , 'type' );
                $session = $pluginLoader->get( $sessionType );
                break;
            }
            
            case 'exCreateSession':
            {
                $data = $userInput->get( 'data' );
                
                if( $sessionId = $sessionList->add( $data ) )
                {
                    $msg['success'] = 'Session successfully created';
                }
                else
                {
                    $msg['error'] = 'Session cannot be created';
                }
                
                break;
            }
            
            case 'exEditSession' :
            {
                $data = $userInput->get( 'data' );
                
                $sessionList->modify( $sessionId , $data );
                
                if( $sessionList->save( $sessionId ) )
                {
                    $msg['success'] = 'Session successfully modified';
                }
                else
                {
                    $msg['error'] = 'Session cannot be modified';
                }
                
                break;
            }
            
            case 'exDeleteSession' :
            {
                if( $sessionList->delete( $sessionId ) )
                {
                    $msg['success'] = 'Session successfully deleted';
                }
                else
                {
                    $msg['error'] = 'An error occur while deleting session';
                }
                
                break;
            }
            
            case 'exHide' :
            {
                if( ! $sessionList->setInvisible( $sessionId ) )
                {
                    $msg['error'] = 'An error occur';
                }
                
                break;
            }
            
            case 'exShow' :
            {
                if( ! $sessionList->setVisible( $sessionId ) )
                {
                    $msg['error'] = 'An error occur';
                }
                
                break;
            }
            
            case 'exLock' :
            {
                if( ! $sessionList->setClosed( $sessionId ) )
                {
                    $msg['error'] = 'An error occur';
                }
                
                break;
            }
            
            case 'exUnlock' :
            {
                if( ! $sessionList->setOpen( $sessionId ) )
                {
                    $msg['error'] = 'An error occur';
                }
                
                break;
            }
        }
        
        //VIEW
        $pageTitle = array( 'mainTitle' => get_lang( 'Subscriptions' ) );
        $cmdList = array();
        
        CssLoader::getInstance()->load( 'main' , 'screen' );
        
        
        switch( $cmd )
        {
            case 'rqShowSessionList':
            case 'exCreateSession':
            {
                $template = new ModuleTemplate( 'ICSUBSCR' , 'sessionlist.tpl.php' );
                $template->assign( 'sessionList' , $sessionList->getItemList() );
                
                if ( $is_allowed_to_edit )
                {
                    $cmdList[] = array(
                        'img'  => 'new',
                        'name' => get_lang( 'Create a new session' ),
                        'url'  => htmlspecialchars( Url::Contextualize( get_module_url( 'ICSUBSCR' )
                                .'/index.php?cmd=rqCreateSession' ) ) );
                }
                
                break;
            }
            
            case 'rqCreateSession':
            {
                $template = new ModuleTemplate( 'ICSUBSCR' , 'createsession.tpl.php' );
                $template->assign( 'pluginList' , $pluginLoader->getPluginList() );
                
                $cmdList[] = array(
                    'img'  => 'back',
                    'name' => get_lang( 'Back to the session list' ),
                    'url'  => htmlspecialchars( Url::Contextualize( get_module_url( 'ICSUBSCR' )
                            .'/index.php?cmd=rqShowSessionList' ) ) );
                
                break;
            }
        }
        
        foreach( $msg as $type => $content )
        {
            $dialogBox->{$type}($content);
        }
        
        $output = $dialogBox->render() . $template->render();
    }
    elseif( $sessionId )
    {
        $type = $sessionList->getType( $sessionId );
        $session = $pluginList->get( $type );
        $session->execute( $cmd );
        $pageTitle['subTitle'] = $session->getTitle();
        $cmdList = $session->getCmdList();
        $output = $session->output();
    }
    else
    {
        throw new Exception( 'bad command :' . $cmd );
    }
    
    Claroline::getInstance()->display->body->appendContent( claro_html_tool_title( $pageTitle , null , $cmdList ) . $output );
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