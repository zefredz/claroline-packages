<?php

$cidReq = null;
$cidReset = true;

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';
require_once dirname(__FILE__) . '/../../claroline/inc/lib/admin.lib.inc.php';
require_once dirname(__FILE__) . '/../../claroline/inc/lib/fileDisplay.lib.php';

From::Module( 'LPUTRACK' )->uses(
    'trackingUtils.lib',
    'trackingData.class',
    'trackingCourse.class',
    'trackingLearnPath.class',
    'trackingModule.class',
    'trackingEntry.class'
);

$dialogBox = new DialogBox();

try
{
    if( !claro_is_user_authenticated() )
    {
        claro_disp_auth_form();
    }
    
    load_module_language( 'LPUTRACK' );
    load_module_config( 'LPUTRACK' );
    ClaroHeader::getInstance()->setTitle( get_lang( 'LearnPath tracking' ) );
    
    $userInput = Claro_UserInput::getInstance();
    $userId = (int)$userInput->getMandatory( 'userId' );
    $courseCode = $userInput->getMandatory( 'courseCode' );
    $courseIntitule = TrackingUtils::getCourseIntituleFromCourseCode( $courseCode );
    $mode = (int)$userInput->getMandatory( 'mode' );
    
    $breadCrumbs = ClaroBreadCrumbs::getInstance();
    
    if( claro_get_current_user_id() == $userId && is_registered_to( $userId, $courseCode ) )
    {
        $trackingData = TrackingData::getInstance();
        $trackingData->addUser( $userId );
        $trackingData->addCourse( $courseCode );
        $trackingData->generateData();
        
        $trackingCourse = new TrackingCourse( $courseCode, $courseIntitule );
        $trackingCourse->generateTrackingList( $userId, 1 );
        $trackingCourse->generateLearnPathTrackingList( $userId, 1 );
        $trackingCourse->generateModuleTrackingList( $userId, $mode );
        $userCourseList = TrackingUtils::getAllCourseFromUser( $userId );
        
        // VIEW
        $breadCrumbs->append( get_lang('LearnPath tracking'), 'currentuser.php?userId=' . $userId
                                                              . '&courseCode=' . $courseCode
                                                              . '&mode=' . $mode );
        CssLoader::getInstance()->load( 'learnPathTracking', 'all' );
        
        $title = new ToolTitle( null );
        $title->setMainTitle( get_lang( 'LearnPath tracking' ) );
        $title->setSubTitle( get_lang( 'Course tracking' ) . ' "' . $courseCode
                             . ' - ' . $courseIntitule . '"' );
        $trackingDisplay = new ModuleTemplate( 'LPUTRACK', 'individualtracking.tpl.php' );
        $trackingDisplay->assign( 'mode', $mode );
        $trackingDisplay->assign( 'trackingCourse', $trackingCourse );
        $trackingDisplay->assign( 'courseList', $userCourseList );
        $mainBody = $trackingDisplay->render();
        
        Claroline::getDisplay()->body->appendContent( $title->render() );
        Claroline::getDisplay()->body->appendContent( $mainBody );
    }
    echo Claroline::getDisplay()->render();
}
catch( Exception $e )
{
    if( claro_debug_mode() )
    {
        $dialogBox->error( '<pre>' . $e->__toString() . '</pre>' );
    }
    else
    {
        $dialogBox->error( $e->getMessage() );
    }
}
