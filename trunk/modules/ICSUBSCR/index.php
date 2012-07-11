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

From::Module( 'ICSUBSCR' )->uses(
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
    $pluginList = new PluginLoader( $pluginRepository , $lang );
    
    $sessionList = new SessionList( $groupId ? 'group' : 'user' );
    
    $actionList = array( 'rqShowSessionList' );
    
    if( $is_allowed_to_edit )
    {
        $restrictedActionList = array( 'excreateSession' );
        
        $actionList = array_merge( $actionList , $restrictedActionList );
    }
    
    $userInput = Claro_UserInput::getInstance();
    $userInput->setValidator( 'cmd' , new Claro_Validator_AllowedList( $actionList ) );
    
    $cmd = $userInput->get( 'cmd' , 'rqShowSessionList' );
    $sessionId = $userInput->get( 'sessionId' );
    
    switch( $cmd )
    {
        case 'rqShowSessionList':
            break;
        
        case 'rqViewSession':
        {
            $sessionType = $sessionList->get( $sessionId , 'type' );
            $session = $pluginLoader->get( $sessionType );
            break;
        }
        
        case 'exCreateSession':
        {
            $startTime = $userInput->get( 'startTime' );
            $endTime = $userInput->get( 'endTime' );
            $sliceNb = $userInput->get( 'sliceNb' , '1' );
            
            if( ! empty( $startTime ) && ! empty( $endTime ) && (int)$sliceNb != 0 )
            {
                if( $startStamp = strtotime( $startTime ) !== false
                   && $endStamp = strtotime( $endTime ) !== false )
                {
                    $slotList = array();
                    $slotTimeLapse = ( (int)$endStamp - (int)$startStamp ) / (int)$sliceNb;
                    
                    for( $i = $startStamp; $i += $slotTimeLapse; $i < $endStamp )
                    {
                        $slot = new Slot();
                        $slot->setDate( date( 'Y-m-d h:i:s' , $i ) );
                        $slotList[] = $slot;
                    }
                }
            }
            break;
        }
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