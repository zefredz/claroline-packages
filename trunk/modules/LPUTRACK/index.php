<?php

$tlabelReq = 'LPUTRACK';
$cidReq = null;
$cidReset = true;

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';
require_once dirname(__FILE__) . '/../../claroline/inc/lib/fileDisplay.lib.php';

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

    ClaroHeader::getInstance()->setTitle( get_lang( 'LearnPath tracking' ) );
    $breadCrumbs = ClaroBreadCrumbs::getInstance();
    $breadCrumbs->append( get_lang('Administration'), get_path('rootAdminWeb') );
    $breadCrumbs->append( get_lang('LearnPath tracking'), 'index.php' );

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
        'userViewTrackModule',
        'uniqueGlobalViewTrackCourse',
        'uniqueGlobalViewTrackLearnPath',
        'uniqueGlobalViewTrackModule',
        'uniqueUserViewTrackCourse',
        'uniqueUserViewTrackLearnPath',
        'uniqueUserViewTrackModule'
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
            $userSearch = '';
            if( isset( $_GET['searchuser'] ) )
            {
                $userSearch = trim( $_GET['searchuser'] );
            }
            if( empty( $userSearch ) )
            {
                $userList = TrackingUtils::getUsersBySearch();
            }
            else
            {
                $userList = TrackingUtils::getUsersBySearch( $userSearch );
            }
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
            $breadCrumbs->append( $infoClass->getClassName(),
                                  'index.php?cmd=classViewTrackCourse&classId=' . $classId );

            break;

        case 'uniqueGlobalViewTrackCourse' :
        case 'uniqueGlobalViewTrackLearnPath' :
        case 'uniqueGlobalViewTrackModule' :
        case 'uniqueUserViewTrackCourse' :
        case 'uniqueUserViewTrackLearnPath' :
        case 'uniqueUserViewTrackModule' :
            $userId = $userInput->getMandatory( 'userId' );
            $user = TrackingUtils::getUserFromUserId( $userId );
            $infoUser = new InfoUser( $userId, $user['prenom'], $user['nom']);
            $trackingData = TrackingData::getInstance();
            $trackingData->addUser( $userId );
            $breadCrumbs->append( $infoUser->getFirstName() . ' ' . $infoUser->getLastName(),
                                  'index.php?cmd=uniqueGlobalViewTrackCourse&userId=' . $userId );

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

        case 'uniqueGlobalViewTrackCourse' :
        case 'uniqueUserViewTrackCourse' :
            $courseList = TrackingUtils::getCourseFromUser( $userId );
            $courseCodeList = array();
            $infoCourseList = array();
            foreach( $courseList as $course )
            {
                $trackingData->addCourse($course['code']);
                $courseCodeList[] = $course['code'];
                $infoCourse = new InfoCourse( $course['code'], $course['intitule'] );
                $infoCourseList[] = $infoCourse;
            }
            $trackingData->generateData();
            $trackingUser = new TrackingUser( $infoUser->getUserId(), $infoUser->getFirstName(), $infoUser->getLastName() );
            $trackingUser->generateTrackingCourseList( $courseCodeList );
            $trackingUser->generateCourseTrackingList( $mode );
            $trackingController->addTrackingUser( $trackingUser );

            break;

        case 'uniqueGlobalViewTrackLearnPath' :
            $courseCode = $userInput->getMandatory( 'courseCode' );
            $course = TrackingUtils::getCourseIntituleFromCourseCode( $courseCode );
            $infoCourse = new InfoCourse( $courseCode, $course['intitule'] );

            $trackingData->addCourse( $courseCode );
            $trackingData->generateData();
            $trackingUser = new TrackingUser( $infoUser->getUserId(), $infoUser->getFirstName(), $infoUser->getLastName() );
            $trackingUser->generateTrackingCourseList( array( $courseCode ) );
            $trackingUser->generateLearnPathTrackingList( $mode );
            $trackingController->addTrackingUser( $trackingUser );

            break;

        case 'uniqueGlobalViewTrackModule' :
            $courseCode = $userInput->getMandatory( 'courseCode' );
            $learnPathId = $userInput->getMandatory( 'learnPathId' );
            $course = TrackingUtils::getCourseIntituleFromCourseCode( $courseCode );
            $infoCourse = new InfoCourse( $courseCode, $course['intitule'] );
            $infoLearnPath = $infoCourse->getInfoLearnPath( $learnPathId );

            $trackingData->addCourse( $courseCode );
            $trackingData->generateData();
            $trackingUser = new TrackingUser( $infoUser->getUserId(), $infoUser->getFirstName(), $infoUser->getLastName() );
            $trackingUser->generateTrackingCourseList( array( $courseCode ) );
            $trackingCourse = $trackingUser->getTrackingCourse( $courseCode );
            $trackingCourse->generateTrackingLearnPath();
            $trackingLearnPath = $trackingCourse->getTrackingLearnPath( $learnPathId );
            $trackingLearnPath->generateModuleTrackingList( $infoUser->getUserId(), $mode );
            $trackingController->addTrackingUser( $trackingUser );

            break;

        case 'uniqueUserViewTrackLearnPath' :
            $courseList = TrackingUtils::getCourseFromUser( $userId );
            $courseCodeList = array();
            $infoCourseList = array();
            foreach( $courseList as $course )
            {
                $trackingData->addCourse($course['code']);
                $courseCodeList[] = $course['code'];
                $infoCourse = new InfoCourse( $course['code'], $course['intitule'] );
                $infoCourseList[] = $infoCourse;
            }
            $trackingData->generateData();
            $trackingUser = new TrackingUser( $infoUser->getUserId(), $infoUser->getFirstName(), $infoUser->getLastName() );
            $trackingUser->generateTrackingCourseList( $courseCodeList );
            $trackingUser->generateCourseTrackingList( $parentMode );
            $trackingUser->generateLearnPathTrackingList( $mode );
            $trackingController->addTrackingUser( $trackingUser );

            break;

        case 'uniqueUserViewTrackModule' :
            $courseList = TrackingUtils::getCourseFromUser( $userId );
            $courseCodeList = array();
            $infoCourseList = array();
            foreach( $courseList as $course )
            {
                $trackingData->addCourse($course['code']);
                $courseCodeList[] = $course['code'];
                $infoCourse = new InfoCourse( $course['code'], $course['intitule'] );
                $infoCourseList[] = $infoCourse;
            }
            $trackingData->generateData();
            $trackingUser = new TrackingUser( $infoUser->getUserId(), $infoUser->getFirstName(), $infoUser->getLastName() );
            $trackingUser->generateCourseTrackingList( $parentMode );
            $trackingUser->generateLearnPathTrackingList( $parentMode );
            $trackingUser->generateModuleTrackingList( $mode );
            $trackingController->addTrackingUser( $trackingUser );

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
            $classDisplay->assign( 'userList', $userList );
            $mainBody = $classDisplay->render();

            break;

        case 'classViewTrackCourse' :
            $trackingDisplay = new ModuleTemplate( 'LPUTRACK', 'classtrackingcourse3.tpl.php' );
            $trackingDisplay->assign( 'classId', $classId );
            $trackingDisplay->assign( 'mode', $mode );
            $trackingDisplay->assign( 'className', $infoClass->getClassName() );
            $trackingDisplay->assign( 'infoCourseList', $infoCourseList );
            $trackingDisplay->assign( 'infoUserList', $infoUserList );
            $trackingDisplay->assign( 'trackingController', $trackingController );
            $trackingDisplay->assign( 'excelExport', $excelExport );

            $title->setSubTitle( get_lang( 'Class tracking' ) . " \"" . $infoClass->getClassName() . "\"" );
            $mainBody = $trackingDisplay->render();

            break;

        case 'classViewTrackLearnPath' :
            $breadCrumbs->append( $infoCourse->getCourseName(),
                                  "index.php?cmd=classViewTrackLearnPath&classId=$classId&courseCode=" . $infoCourse->getCourseCode() );
            $trackingDisplay = new ModuleTemplate( 'LPUTRACK', 'classtrackinglearnpath3.tpl.php' );
            $trackingDisplay->assign( 'classId', $classId );
            $trackingDisplay->assign( 'mode', $mode );
            $trackingDisplay->assign( 'nbLearnPath', $infoCourse->getNbLearnPath() );
            $trackingDisplay->assign( 'courseCode', $infoCourse->getCourseCode() );
            $trackingDisplay->assign( 'courseName', $infoCourse->getCourseName() );
            $trackingDisplay->assign( 'infoLearnPathList', $infoCourse->getInfoLearnPathList() );
            $trackingDisplay->assign( 'infoUserList', $infoClass->getInfoUserList() );
            $trackingDisplay->assign( 'trackingController', $trackingController );
            $trackingDisplay->assign( 'excelExport', $excelExport );

            $title->setSubTitle( get_lang( 'Course tracking' )
                                . " \"" . $infoCourse->getCourseName()
                                . "\" " . get_lang( 'for class' )
                                . " \"" . $infoClass->getClassName()
                                . "\"" );
            $mainBody = $trackingDisplay->render();

            break;

        case 'classViewTrackModule' :
            $breadCrumbs->append( $infoCourse->getCourseName(),
                                  "index.php?cmd=classViewTrackLearnPath&classId=$classId&courseCode=" . $infoCourse->getCourseCode() );
            $breadCrumbs->append( $infoLearnPath->getLearnPathName(),
                                  "index.php?cmd=classViewTrackModule&classId=$classId&courseCode="
                                  . $infoCourse->getCourseCode()
                                  . "&learnPathId=" . $infoLearnPath->getLearnPathId() );
            $trackingDisplay = new ModuleTemplate( 'LPUTRACK', 'classtrackingmodule3.tpl.php' );
            $trackingDisplay->assign( 'classId', $classId );
            $trackingDisplay->assign( 'mode', $mode );
            $trackingDisplay->assign( 'courseCode', $infoCourse->getCourseCode() );
            $trackingDisplay->assign( 'courseName', $infoCourse->getCourseName() );
            $trackingDisplay->assign( 'learnPathId', $infoLearnPath->getLearnPathId() );
            $trackingDisplay->assign( 'learnPathName', $infoLearnPath->getLearnPathName() );
            $trackingDisplay->assign( 'infoModuleList', $infoLearnPath->getInfoModuleList() );
            $trackingDisplay->assign( 'infoUserList', $infoClass->getInfoUserList() );
            $trackingDisplay->assign( 'trackingController', $trackingController );
            $trackingDisplay->assign( 'excelExport', $excelExport );

            $title->setSubTitle( get_lang( 'LearnPath tracking 2' )
                                . " \"" . $infoLearnPath->getLearnPathName()
                                . "\" " . get_lang( 'from course' )
                                . " \"" . $infoCourse->getCourseName()
                                . "\" " . get_lang( 'for class' )
                                . " \"" . $infoClass->getClassName()
                                . "\"" );
            $mainBody = $trackingDisplay->render();

            break;

        case 'userViewTrackCourse' :
            $breadCrumbs->append( get_lang( 'Course' ),
                                  'index.php?cmd=userViewTrackCourse&classId=' . $classId );
            $trackingDisplay = new ModuleTemplate( 'LPUTRACK', 'usertrackingcourse3.tpl.php' );
            $trackingDisplay->assign( 'classId', $classId );
            $trackingDisplay->assign( 'mode', $mode );
            $trackingDisplay->assign( 'className', $infoClass->getClassName() );
            $trackingDisplay->assign( 'infoCourseList', $infoCourseList );
            $trackingDisplay->assign( 'infoUserList', $infoUserList );
            $trackingDisplay->assign( 'trackingController', $trackingController );
            $trackingDisplay->assign( 'excelExport', $excelExport );

            $title->setSubTitle( get_lang( 'Class tracking' ) . " \"" . $infoClass->getClassName() . "\"" );
            $mainBody = $trackingDisplay->render();

            break;

        case 'userViewTrackLearnPath' :
            $breadCrumbs->append( get_lang( 'Course' ),
                                  'index.php?cmd=userViewTrackCourse&classId=' . $classId );
            $breadCrumbs->append( get_lang( 'LearnPath' ),
                                  'index.php?cmd=userViewTrackLearnPath&classId=' . $classId );
            $trackingDisplay = new ModuleTemplate( 'LPUTRACK', 'usertrackinglearnpath3.tpl.php' );
            $trackingDisplay->assign( 'classId', $classId );
            $trackingDisplay->assign( 'mode', $mode );
            $trackingDisplay->assign( 'className', $infoClass->getClassName() );
            $trackingDisplay->assign( 'infoCourseList', $infoCourseList );
            $trackingDisplay->assign( 'infoUserList', $infoUserList );
            $trackingDisplay->assign( 'trackingController', $trackingController );
            $trackingDisplay->assign( 'excelExport', $excelExport );

            $title->setSubTitle( get_lang( 'Class tracking' ) . " \"" . $infoClass->getClassName() . "\"" );
            $mainBody = $trackingDisplay->render();

            break;

        case 'userViewTrackModule' :
            $breadCrumbs->append( get_lang( 'Course' ),
                                  'index.php?cmd=userViewTrackCourse&classId=' . $classId );
            $breadCrumbs->append( get_lang( 'LearnPath' ),
                                  'index.php?cmd=userViewTrackLearnPath&classId=' . $classId );
            $breadCrumbs->append( get_lang( 'Module' ),
                                  'index.php?cmd=userViewTrackModule&classId=' . $classId );
            $trackingDisplay = new ModuleTemplate( 'LPUTRACK', 'usertrackingmodule3.tpl.php' );
            $trackingDisplay->assign( 'classId', $classId );
            $trackingDisplay->assign( 'mode', $mode );
            $trackingDisplay->assign( 'className', $infoClass->getClassName() );
            $trackingDisplay->assign( 'infoCourseList', $infoCourseList );
            $trackingDisplay->assign( 'infoUserList', $infoUserList );
            $trackingDisplay->assign( 'trackingController', $trackingController );
            $trackingDisplay->assign( 'excelExport', $excelExport );

            $title->setSubTitle( get_lang( 'Class tracking' ) . " \"" . $infoClass->getClassName() . "\"" );
            $mainBody = $trackingDisplay->render();

            break;

        case 'uniqueGlobalViewTrackCourse' :
            $trackingDisplay = new ModuleTemplate( 'LPUTRACK', 'uniquetrackingcourse.tpl.php' );
            $trackingDisplay->assign( 'userId', $userId );
            $trackingDisplay->assign( 'mode', $mode );
            $trackingDisplay->assign( 'infoCourseList', $infoCourseList );
            $trackingDisplay->assign( 'infoUser', $infoUser );
            $trackingDisplay->assign( 'trackingController', $trackingController );
            $trackingDisplay->assign( 'excelExport', $excelExport );

            $title->setSubTitle( get_lang( 'User tracking' ) . " \"" . $infoUser->getFirstName() . " " . $infoUser->getLastName() . "\"" );
            $mainBody = $trackingDisplay->render();

            break;

        case 'uniqueGlobalViewTrackLearnPath' :
            $breadCrumbs->append( $infoCourse->getCourseName(),
                                  "index.php?cmd=uniqueGlobalViewTrackLearnPath&userId=$userId&courseCode=" . $infoCourse->getCourseCode() );
            $trackingDisplay = new ModuleTemplate( 'LPUTRACK', 'uniquetrackinglearnpath.tpl.php' );
            $trackingDisplay->assign( 'userId', $userId );
            $trackingDisplay->assign( 'mode', $mode );
            $trackingDisplay->assign( 'nbLearnPath', $infoCourse->getNbLearnPath() );
            $trackingDisplay->assign( 'courseCode', $infoCourse->getCourseCode() );
            $trackingDisplay->assign( 'courseName', $infoCourse->getCourseName() );
            $trackingDisplay->assign( 'infoLearnPathList', $infoCourse->getInfoLearnPathList() );
            $trackingDisplay->assign( 'infoUser', $infoUser );
            $trackingDisplay->assign( 'trackingController', $trackingController );
            $trackingDisplay->assign( 'excelExport', $excelExport );

            $title->setSubTitle( get_lang( 'Course tracking' )
                                . " \"" . $infoCourse->getCourseName()
                                . "\" " . get_lang( 'for user' )
                                . " \"" . $infoUser->getFirstName() . " " . $infoUser->getLastName()
                                . "\"" );
            $mainBody = $trackingDisplay->render();

            break;

        case 'uniqueGlobalViewTrackModule' :
            $breadCrumbs->append( $infoCourse->getCourseName(),
                                  "index.php?cmd=uniqueGlobalViewTrackLearnPath&userId=$userId&courseCode=" . $infoCourse->getCourseCode() );
            $breadCrumbs->append( $infoLearnPath->getLearnPathName(),
                                  "index.php?cmd=uniqueGlobalViewTrackModule&userId=$userId&courseCode="
                                  . $infoCourse->getCourseCode()
                                  . "&learnPathId=" . $infoLearnPath->getLearnPathId() );
            $trackingDisplay = new ModuleTemplate( 'LPUTRACK', 'uniquetrackingmodule.tpl.php' );
            $trackingDisplay->assign( 'userId', $userId );
            $trackingDisplay->assign( 'mode', $mode );
            $trackingDisplay->assign( 'courseCode', $infoCourse->getCourseCode() );
            $trackingDisplay->assign( 'courseName', $infoCourse->getCourseName() );
            $trackingDisplay->assign( 'learnPathId', $infoLearnPath->getLearnPathId() );
            $trackingDisplay->assign( 'learnPathName', $infoLearnPath->getLearnPathName() );
            $trackingDisplay->assign( 'infoModuleList', $infoLearnPath->getInfoModuleList() );
            $trackingDisplay->assign( 'infoUser', $infoUser );
            $trackingDisplay->assign( 'trackingController', $trackingController );
            $trackingDisplay->assign( 'excelExport', $excelExport );

            $title->setSubTitle( get_lang( 'LearnPath tracking 2' )
                                . " \"" . $infoLearnPath->getLearnPathName()
                                . "\" " . get_lang( 'from course' )
                                . " \"" . $infoCourse->getCourseName()
                                . "\" " . get_lang( 'for user' )
                                . " \"" . $infoUser->getFirstName() . " " . $infoUser->getLastName()
                                . "\"" );
            $mainBody = $trackingDisplay->render();

            break;

        case 'uniqueUserViewTrackCourse' :
            $breadCrumbs->append( get_lang( 'Course' ),
                                  'index.php?cmd=uniqueUserViewTrackCourse&userId=' . $userId );
            $trackingDisplay = new ModuleTemplate( 'LPUTRACK', 'uniqueusertrackingcourse.tpl.php' );
            $trackingDisplay->assign( 'userId', $userId );
            $trackingDisplay->assign( 'mode', $mode );
            $trackingDisplay->assign( 'infoCourseList', $infoCourseList );
            $trackingDisplay->assign( 'infoUser', $infoUser );
            $trackingDisplay->assign( 'trackingController', $trackingController );
            $trackingDisplay->assign( 'excelExport', $excelExport );

            $title->setSubTitle( get_lang( 'User tracking' ) . " \"" . $infoUser->getFirstName() . " " . $infoUser->getLastName() . "\"" );
            $mainBody = $trackingDisplay->render();

            break;

        case 'uniqueUserViewTrackLearnPath' :
            $breadCrumbs->append( get_lang( 'Course' ),
                                  'index.php?cmd=uniqueUserViewTrackCourse&userId=' . $userId );
            $breadCrumbs->append( get_lang( 'LearnPath' ),
                                  'index.php?cmd=uniqueUserViewTrackLearnPath&userId=' . $userId );
            $trackingDisplay = new ModuleTemplate( 'LPUTRACK', 'uniqueusertrackinglearnpath.tpl.php' );
            $trackingDisplay->assign( 'userId', $userId );
            $trackingDisplay->assign( 'mode', $mode );
            $trackingDisplay->assign( 'infoCourseList', $infoCourseList );
            $trackingDisplay->assign( 'infoUser', $infoUser );
            $trackingDisplay->assign( 'trackingController', $trackingController );
            $trackingDisplay->assign( 'excelExport', $excelExport );

            $title->setSubTitle( get_lang( 'User tracking' ) . " \"" . $infoUser->getFirstName() . " " . $infoUser->getLastName()  . "\"" );
            $mainBody = $trackingDisplay->render();

            break;

        case 'uniqueUserViewTrackModule' :
            $breadCrumbs->append( get_lang( 'Course' ),
                                  'index.php?cmd=uniqueUserViewTrackCourse&userId=' . $userId );
            $breadCrumbs->append( get_lang( 'LearnPath' ),
                                  'index.php?cmd=uniqueUserViewTrackLearnPath&userId=' . $userId );
            $breadCrumbs->append( get_lang( 'Module' ),
                                  'index.php?cmd=uniqueUserViewTrackModule&userId=' . $userId );
            $trackingDisplay = new ModuleTemplate( 'LPUTRACK', 'uniqueusertrackingmodule.tpl.php' );
            $trackingDisplay->assign( 'userId', $userId );
            $trackingDisplay->assign( 'mode', $mode );
            $trackingDisplay->assign( 'infoCourseList', $infoCourseList );
            $trackingDisplay->assign( 'infoUser', $infoUser );
            $trackingDisplay->assign( 'trackingController', $trackingController );
            $trackingDisplay->assign( 'excelExport', $excelExport );

            $title->setSubTitle( get_lang( 'User tracking' ) . " \"" . $infoUser->getFirstName() . " " . $infoUser->getLastName()  . "\"" );
            $mainBody = $trackingDisplay->render();

            break;

        default :
            break;
    }
    if( $excelExport )
    {
        $fileName = 'learnPath_tracking.xls';
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$fileName");
        echo $trackingDisplay->render();
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

