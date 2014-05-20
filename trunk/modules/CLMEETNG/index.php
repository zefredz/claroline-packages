<?php // $Id$
/**
 * Online Meetings for Claroline
 *
 * @version     CLMEETNG 0.1.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLMEETNG
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

$tlabelReq = 'CLMEETNG';

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

if ( ! claro_is_in_a_course() || ! claro_is_course_allowed() ) claro_disp_auth_form( true );

FromKernel::uses(
    'utils/input.lib',
    'utils/validator.lib',
    'display/layout.lib',
    'fileUpload.lib' );

From::Module( 'CLMEETNG' )->uses(
    'client.lib',
    'decorator.lib',
    'meeting.lib',
    'meetinglist.lib',
    'dateconverter.lib' );

$dialogBox = new DialogBox();

try
{
    $serviceName = get_conf( 'CLMEETNG_service_name');
    $openMeetingsServerUrl = get_conf( 'CLMEETNG_server_url');
    $openMeetingsServerPort = get_conf( 'CLMEETNG_server_port' );
    
    $userId = claro_get_current_user_id();
    $courseId = claro_get_current_course_id();
    $groupId = claro_get_current_group_id();
    $is_courseAllowed = claro_is_course_allowed();
    $is_groupAllowed = claro_is_group_allowed();
    
    $is_allowed_to_edit = claro_is_allowed_to_edit();
    
    $actionList = array(
        'rqShowMeetingList',
        'rqJoinMeeting' );
    
    if ( $is_allowed_to_edit )
    {
        $restrictedActionList = array(
            'rqCreateMeeting',
            'exCreateMeeting',
            'rqDeleteMeeting',
            'exDeleteMeeting',
            'rqEditMeeting',
            'exEditMeeting',
            'exOpenMeeting',
            'exCloseMeeting',
            'exMkVisible',
            'exMkInvisible' );
        
        $actionList = array_merge( $actionList , $restrictedActionList );
    }
    
    $userInput = Claro_UserInput::getInstance();
    $userInput->setValidator( 'cmd' , new Claro_Validator_AllowedList( $actionList ) );
    
    $cmd = $userInput->get( 'cmd' , 'rqShowMeetingList' );
    $meetingId = $userInput->get( 'meetingId' );
    
    // CONTROLLER
    $client = new CLMEETNG_OpenMeetingsClient(
        $openMeetingsServerUrl,
        $openMeetingsServerPort,
        $serviceName,
        $meetingId );
    
    if( $meetingId )
    {
        $meeting = new CLMEETNG_Meeting(
            $client,
            $courseId,
            $userId,
            $groupId );
    }
    else
    {
        $meetingList = new CLMEETNG_MeetingList(
            $courseId,
            $groupId,
            $is_allowed_to_edit );
    }
    
    switch( $cmd )
    {
        case 'rqShowMeetingList':
        case 'rqCreateMeeting':
        case 'rqEditMeeting':
        case 'rqDeleteMeeting':
        case 'exEditMeeting':
        case 'exDeleteMeeting':
        case 'exOpenMeeting':
        case 'exCloseMeeting':
        case 'exMkVisible':
        case 'exMkInvisible':
            break;
        
        case 'rqJoinMeeting':
        {
            $access_allowed = ! empty( $sessionId );
            break;
        }
        
        case 'exCreateMeeting':
        {
            $data = $userInput->get( 'data' );
            
            $meeting = new CLMEETNG_Meeting( $client , $userId , $courseId , $groupId , $is_allowed_to_edit );
            
            /*$datePart = explode( '/' , $data['date'] );
            $dateArray = array();
            
            foreach( explode( '/' , get_lang('_date' ) ) as $index => $formatPart )
            {
                $dateArray[ $formatPart ] = $datePart[ $index ];
            }
            
            $date = $dateArray['Y'] . '-' . $dateArray['m'] . $dateArray['d'];
            $data['date_from'] = $date . ' ' . $data['hour_from'] . ':00';
            $data['date_to'] = $date . ' ' . $data['hour_to'] . ':00';
            unset( $data['date'] );*/
            
            $dateConverter = new CLMEETNG_DateConverter( get_lang( '_date' ) );
            $data['date_from'] = $dateConverter->in( $data['date'] , $data['hour_from'] );
            $data['date_to'] = $dateConverter->in( $data['date'] , $data['hour_to'] );
            $data['creation_date'] = date( 'Y-m-d H:M:s' );
            unset( $data['date'] , $data['hour_from'] , $data['hour_to'] );
            
            $meeting->setData( $data );
            
            if( empty( $data['title'] )
            || empty( $data['date_from'] )
            || empty( $data['date_to'] ) )
            {
                $msg[] = array( 'type' => 'error'
                            , 'text' => get_lang( '_missing_values' ) );
                $cmd = 'rqCreateMeeting';
            }
            else
            {
                $meeting->save();
            }
            
            break;
        }
        
        default :
        {
            throw new Exception( 'bad command' );
        }
    }
    
    // VIEW
    CssLoader::getInstance()->load( 'main' , 'screen' );
    CssLoader::getInstance()->load( 'kalendae' , 'screen' );
    
    JavascriptLoader::getInstance()->load('kalendae');
    
    $formData = array();
    $cmdList = array();
    $assignList = array();
    $pageTitle = array( 'mainTitle' => get_lang( 'Online Meetings' ) );
    
    if( ! $client->serviceAvailable() )
    {
        $msg[] = array( 'type' => 'error',
                        'text' => get_lang( 'Service unavailable' ) );
    }
    
    if( $meetingId )
    {
        $template = 'meeting';
        $pageTitle[ 'subTitle' ] = get_lang( 'Meeting' );
        $assignList[ 'url' ] = $meeting->url;
    }
    else
    {
        $template = 'meetinglist';
        $pageTitle[ 'subTitle' ] = get_lang( 'Meetings list' );
        $assignList[ 'meetingList' ] = $meetingList->getList();
        $assignList[ 'is_manager' ] = $is_allowed_to_edit;
        
        if( $is_allowed_to_edit)
        {
            $cmdList[] = array( 'img'  => 'icon',
                                'name' => get_lang( 'Schedule a new meeting' ),
                                'url'  => htmlspecialchars( Url::Contextualize( get_module_url( 'CLMEETNG' )
                                          .'/index.php?cmd=rqCreateMeeting' ) ) );
        }
    }
    
    switch( $cmd )
    {
        case 'rqCreateMeeting':
        {
            $formData[ 'message' ] = 'Schedule a new meeting';
            $formData[ 'urlAction'] = 'exCreateMeeting';
            $formData[ 'action' ] = 'Submit';
            $formData[ 'urlCancel' ] = 'rqShowMeetingList';
            $formData[ 'xid' ][] = array( 'name' => 'title' , 'required' => true );
            $formData[ 'xid' ][] = array( 'name' => 'description' , 'type' => 'textarea' );
            $formData[ 'xid' ][] = array( 'name' => 'date' , 'required' => true , 'value' => date( get_lang( '_date' ) ) , 'date_picker' => true );
            $formData[ 'xid' ][] = array( 'name' => 'hour_from' , 'required' => true , 'value' => date( 'H:i' ) , 'hour_picker' => true );
            $formData[ 'xid' ][] = array( 'name' => 'hour_to' , 'required' => true , 'value' => date( 'H:i' , time() + 3600 ) , 'hour_picker' => true );
            
            if( isset( $meeting ) )
            {
                $data = $meeting->getData();
                
                foreach( $formData[ 'xid' ] as $index => $line )
                {
                    $property = strtolower( $line[ 'name' ] );
                    
                    if( array_key_exists( $property , $data ) )
                    {
                        $formData[ 'xid' ][ $index ][ 'value' ] = $data[ $property ];
                    }
                }
            }
            break;
        }
        
        case 'rqShowMeetingList':
        case 'rqJoinMeeting':
        case 'rqEditMeeting':
        case 'rqDeleteMeeting':
        case 'exCreateMeeting':
        case 'exEditMeeting':
        case 'exDeleteMeeting':
        case 'exOpenMeeting':
        case 'exCloseMeeting':
        case 'exMkVisible':
        case 'exMkInvisible':
            break;
        
        default :
        {
            throw new Exception( 'bad command' );
        }
    }
    
    $view = new ModuleTemplate( 'CLMEETNG' , $template . '.tpl.php' );
    
    foreach( $assignList as $name => $value )
    {
        $view->assign( $name , $value );
    }
    
    if( ! empty( $msg ) )
    {
        foreach( $msg as $line )
        {
            $dialogBox->{$line['type']}($line['text']);
        }
    }
    
    if( ! empty( $formData ) )
    {
        $form = new ModuleTemplate( 'CLMEETNG' , 'form.tpl.php' );
        
        foreach( $formData as $name => $value )
        {
            $form->assign( $name , $value );
        }
        
        $dialogBox->form( $form->render() );
    }
    
    ClaroBreadCrumbs::getInstance()->append( $pageTitle[ 'subTitle' ]
                                           , htmlspecialchars( Url::Contextualize( $_SERVER[ 'PHP_SELF' ] ) ) );
    
    $content = claro_html_tool_title( $pageTitle , null /*$helpUrl*/ , $cmdList )
            . $dialogBox->render()
            . $view->render();
    
    Claroline::getInstance()->display->body->appendContent( $content );
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