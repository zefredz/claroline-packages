<?php

$tlabelReq = 'LPUTRACK';
$cidReq = null;
$cidReset = true;

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

FromKernel::uses(
//    'utils/input.lib',
    'utils/validator.lib',
    'display/layout.lib'
);

From::Module( 'LPUTRACK' )->uses(
    'trackingUtils.lib',
    'trackingData.class',
    'infoUser.class',
    'infoClass.class',
    'infoCourse.class',
    'infoLearnPath.class',
    'infoModule.class',
    'trackingUser.class',
    'trackingCourse.class',
    'trackingLearnPath.class',
    'trackingModule.class',
    'trackingEntry.class',
    'trackingController.class');

try
{
    if( !claro_is_user_authenticated() )
    {
        claro_disp_auth_form();
    }
    if( !claro_is_platform_admin() )
    {
        claro_die( get_lang( 'Not allowed' ) );
    }
    
    CssLoader::getInstance()->load( 'learnPathTracking', 'all' );
    
    $trackingController = new TrackingController();
    
    $userInput = Claro_UserInput::getInstance();
    $userInput->setValidator( 'cmd', new Claro_Validator_AllowedList( array(
        'classList',
        'classViewTrackCourse', 
        'classViewTrackLearnPath',
        'classViewTrackModule',
        'userViewTrackCourse',
        'userViewTrackLearnPath',
        'userViewTrackModule'
    ) ) );
    
    $cmd = $userInput->get( 'cmd', 'classList' );
    $mode = $userInput->get( 'mode', 1 );
    $parentMode = 1;
    
    $excelExport = false;
    if( isset( $_POST['excelexport'] ) )
    {
        $excelExport = true;
    }
    
    switch( $cmd )
    {
        case 'classList' :
            break;

        case 'classViewTrackCourse' :
        case 'classViewTrackLearnPath' :
        case 'classViewTrackModule' :
        case 'userViewTrackCourse' :
        case 'userViewTrackLearnPath' :
        case 'userViewTrackModule' :
            $classId = $userInput->getMandatory( 'classId' );
            
            $trackingController->setInfoClass( $classId );
            $trackingData = TrackingData::getInstance();
            
            $infoClass = $trackingController->getInfoClass();
            $infoUserList = $infoClass->getInfoUserList();
            
            foreach( $infoUserList as $infoUser )
            {
                $trackingData->addUser( $infoUser->getUserId() );
            }
            
            break;
        default :
            break;
    }
    
    switch ( $cmd )
    {
        case 'classViewTrackCourse' :
        case 'userViewTrackCourse' :
            $infoCourseList = $infoClass->getInfoCourseList();
            foreach( $infoCourseList as $infoCourse )
            {
                $trackingData->addCourse( $infoCourse->getCourseCode() );
            }
            $trackingData->generateData();
            foreach( $infoUserList as $infoUser )
            {
                $trackingUser = new TrackingUser( $infoUser->getUserId(), $infoUser->getFirstName(), $infoUser->getLastName() );
                $trackingUser->generateTrackingCourseList( $infoClass->getCourseCodeList() );
                $trackingUser->generateCourseTrackingList( $mode );
                $trackingController->addTrackingUser( $trackingUser );
            }
            
            break;

        case 'classViewTrackLearnPath' :
            $courseCode = $userInput->getMandatory( 'courseCode' );
            $infoCourse = $infoClass->getInfoCourse( $courseCode );
            
            $trackingData->addCourse( $courseCode );
            $trackingData->generateData();
            foreach( $infoUserList as $infoUser )
            {
                $trackingUser = new TrackingUser( $infoUser->getUserId(), $infoUser->getFirstName(), $infoUser->getLastName() );
                $trackingUser->generateTrackingCourseList( array( $courseCode ) );
                $trackingUser->generateLearnPathTrackingList( $mode );
                $trackingController->addTrackingUser( $trackingUser );
            }
            break;
        
        case 'classViewTrackModule' :
            $courseCode = $userInput->getMandatory( 'courseCode' );
            $learnPathId = $userInput->getMandatory( 'learnPathId' );
            $infoCourse = $infoClass->getInfoCourse( $courseCode );
            $infoLearnPath = $infoCourse->getInfoLearnPath( $learnPathId );
            
            $trackingData->addCourse( $courseCode );
            $trackingData->generateData();
            foreach( $infoUserList as $infoUser )
            {
                $trackingUser = new TrackingUser( $infoUser->getUserId(), $infoUser->getFirstName(), $infoUser->getLastName() );
                $trackingUser->generateTrackingCourseList( array( $courseCode ) );
                $trackingCourse = $trackingUser->getTrackingCourse( $courseCode );
                $trackingCourse->generateTrackingLearnPath();
                $trackingLearnPath = $trackingCourse->getTrackingLearnPath( $learnPathId );
                $trackingLearnPath->generateModuleTrackingList( $infoUser->getUserId(), $mode );
                $trackingController->addTrackingUser( $trackingUser );
            }
            break;
            
        case 'userViewTrackLearnPath' :
            $infoCourseList = $infoClass->getInfoCourseList();
            foreach( $infoCourseList as $infoCourse )
            {
                $trackingData->addCourse( $infoCourse->getCourseCode() );
            }
            $trackingData->generateData();
            foreach( $infoUserList as $infoUser )
            {
                $trackingUser = new TrackingUser( $infoUser->getUserId(), $infoUser->getFirstName(), $infoUser->getLastName() );
                $trackingUser->generateTrackingCourseList( $infoClass->getCourseCodeList() );
                $trackingUser->generateCourseTrackingList( $parentMode );
                $trackingUser->generateLearnPathTrackingList( $mode );
                $trackingController->addTrackingUser( $trackingUser );
            }
            
            break;
        
        case 'userViewTrackModule' :
            $infoCourseList = $infoClass->getInfoCourseList();
            foreach( $infoCourseList as $infoCourse )
            {
                $trackingData->addCourse( $infoCourse->getCourseCode() );
            }
            $trackingData->generateData();
            foreach( $infoUserList as $infoUser )
            {
                $trackingUser = new TrackingUser( $infoUser->getUserId(), $infoUser->getFirstName(), $infoUser->getLastName() );
                $trackingUser->generateTrackingCourseList( $infoClass->getCourseCodeList() );
                $trackingUser->generateCourseTrackingList( $parentMode );
                $trackingUser->generateLearnPathTrackingList( $parentMode );
                $trackingUser->generateModuleTrackingList( $mode );
                $trackingController->addTrackingUser( $trackingUser );
            }
            
            break;
            
        default:
            break;
    }
    
    // VIEW
    $title = new ToolTitle( null );
    $title->setMainTitle( get_lang( 'LearnPath tracking' ) );
    $mainBody = null;
    
    switch ( $cmd )
    {
        case 'classList' :
            $classDisplay = new ModuleTemplate( 'LPUTRACK', 'classlisting.tpl.php' );
            $classDisplay->assign( 'trackingController', $trackingController );
            
            $mainBody = $classDisplay->render();
            
            break;

        case 'classViewTrackCourse' :
            $trackingClassDisplay = new ModuleTemplate( 'LPUTRACK', 'classtrackingcourse.tpl.php' );
            $trackingClassDisplay->assign( 'classId', $classId );
            $trackingClassDisplay->assign( 'mode', $mode );
            $trackingClassDisplay->assign( 'className', $infoClass->getClassName() );
            $trackingClassDisplay->assign( 'infoCourseList', $infoCourseList );
            $trackingClassDisplay->assign( 'infoUserList', $infoUserList );
            $trackingClassDisplay->assign( 'trackingController', $trackingController );
            $trackingClassDisplay->assign( 'excelExport', $excelExport );
            
            $title->setSubTitle( get_lang( 'Class tracking' ) . " \"" . $infoClass->getClassName() . "\"" );
            $mainBody = $trackingClassDisplay->render();
            
            break;
        
        case 'classViewTrackLearnPath' :
            $trackingClassDisplay = new ModuleTemplate( 'LPUTRACK', 'classtrackinglearnpath.tpl.php' );
            $trackingClassDisplay->assign( 'classId', $classId );
            $trackingClassDisplay->assign( 'mode', $mode );
            $trackingClassDisplay->assign( 'nbLearnPath', $infoCourse->getNbLearnPath() );
            $trackingClassDisplay->assign( 'courseCode', $infoCourse->getCourseCode() );
            $trackingClassDisplay->assign( 'courseName', $infoCourse->getCourseName() );
            $trackingClassDisplay->assign( 'infoLearnPathList', $infoCourse->getInfoLearnPathList() );
            $trackingClassDisplay->assign( 'infoUserList', $infoClass->getInfoUserList() );
            $trackingClassDisplay->assign( 'trackingController', $trackingController );
            $trackingClassDisplay->assign( 'excelExport', $excelExport );
            
            $title->setSubTitle( get_lang( 'Course tracking' )
                                . " \"" . $infoCourse->getCourseName()
                                . "\" " . get_lang( 'for class' )
                                . " \"" . $infoClass->getClassName()
                                . "\"" );
            $mainBody = $trackingClassDisplay->render();
            
            break;
        
        case 'classViewTrackModule' :
            $trackingClassDisplay = new ModuleTemplate( 'LPUTRACK', 'classtrackingmodule.tpl.php' );
            $trackingClassDisplay->assign( 'classId', $classId );
            $trackingClassDisplay->assign( 'mode', $mode );
            $trackingClassDisplay->assign( 'courseCode', $infoCourse->getCourseCode() );
            $trackingClassDisplay->assign( 'courseName', $infoCourse->getCourseName() );
            $trackingClassDisplay->assign( 'learnPathId', $infoLearnPath->getLearnPathId() );
            $trackingClassDisplay->assign( 'learnPathName', $infoLearnPath->getLearnPathName() );
            $trackingClassDisplay->assign( 'infoModuleList', $infoLearnPath->getInfoModuleList() );
            $trackingClassDisplay->assign( 'infoUserList', $infoClass->getInfoUserList() );
            $trackingClassDisplay->assign( 'trackingController', $trackingController );
            $trackingClassDisplay->assign( 'excelExport', $excelExport );
            
            $title->setSubTitle( get_lang( 'LearnPath tracking 2' )
                                . " \"" . $infoLearnPath->getLearnPathName()
                                . "\" " . get_lang( 'from course' )
                                . " \"" . $infoCourse->getCourseName()
                                . "\" " . get_lang( 'for class' )
                                . " \"" . $infoClass->getClassName()
                                . "\"" );
            $mainBody = $trackingClassDisplay->render();
            
            break;
        
        
        case 'userViewTrackCourse' :
            $trackingClassDisplay = new ModuleTemplate( 'LPUTRACK', 'usertrackingcourse.tpl.php' );
            $trackingClassDisplay->assign( 'classId', $classId );
            $trackingClassDisplay->assign( 'mode', $mode );
            $trackingClassDisplay->assign( 'className', $infoClass->getClassName() );
            $trackingClassDisplay->assign( 'infoCourseList', $infoCourseList );
            $trackingClassDisplay->assign( 'infoUserList', $infoUserList );
            $trackingClassDisplay->assign( 'trackingController', $trackingController );
            $trackingClassDisplay->assign( 'excelExport', $excelExport );
            
            $title->setSubTitle( get_lang( 'Class tracking' ) . " \"" . $infoClass->getClassName() . "\"" );
            $mainBody = $trackingClassDisplay->render();
            
            break;
        
        case 'userViewTrackLearnPath' :
            $trackingClassDisplay = new ModuleTemplate( 'LPUTRACK', 'usertrackinglearnpath.tpl.php' );
            $trackingClassDisplay->assign( 'classId', $classId );
            $trackingClassDisplay->assign( 'mode', $mode );
            $trackingClassDisplay->assign( 'className', $infoClass->getClassName() );
            $trackingClassDisplay->assign( 'infoCourseList', $infoCourseList );
            $trackingClassDisplay->assign( 'infoUserList', $infoUserList );
            $trackingClassDisplay->assign( 'trackingController', $trackingController );
            $trackingClassDisplay->assign( 'excelExport', $excelExport );
            
            $title->setSubTitle( get_lang( 'Class tracking' ) . " \"" . $infoClass->getClassName() . "\"" );
            $mainBody = $trackingClassDisplay->render();
            
            break;
        
        case 'userViewTrackModule' :
            $trackingClassDisplay = new ModuleTemplate( 'LPUTRACK', 'usertrackingmodule.tpl.php' );
            $trackingClassDisplay->assign( 'classId', $classId );
            $trackingClassDisplay->assign( 'mode', $mode );
            $trackingClassDisplay->assign( 'className', $infoClass->getClassName() );
            $trackingClassDisplay->assign( 'infoCourseList', $infoCourseList );
            $trackingClassDisplay->assign( 'infoUserList', $infoUserList );
            $trackingClassDisplay->assign( 'trackingController', $trackingController );
            $trackingClassDisplay->assign( 'excelExport', $excelExport );
            
            $title->setSubTitle( get_lang( 'Class tracking' ) . " \"" . $infoClass->getClassName() . "\"" );
            $mainBody = $trackingClassDisplay->render();
            
            break;
        
        default :
            break;
    }
    if( $excelExport )
    {
        $fileName = 'learnPath_tracking.xls';
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$fileName");
        echo $trackingClassDisplay->render();
    }
    else
    {
        Claroline::getDisplay()->body->appendContent( $title->render() );
        Claroline::getDisplay()->body->appendContent( $mainBody );
        echo Claroline::getDisplay()->render();
    }
}
catch( Exception $e )
{
    echo "<pre>";
    echo $e->__toString();
    echo "</pre>";
}

