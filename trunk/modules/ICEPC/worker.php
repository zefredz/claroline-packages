<?php

// $Id$

/**
 * AJAX backend for the EPC module
 *
 * @version 0.1 $Revision$
 * @copyright (c) 2013 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package ICEPC
 * @author Frederic Minne <zefredz@claroline.net>
 *
 */

try
{
    $tlabelReq = 'ICEPC';

    require_once dirname ( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';

    // to put into config
    /*$GLOBALS[ '_conf' ][ 'epcServiceUrl' ] = 'https://dev.epc.uclouvain.be/WebApi/resources/EtudInsc';
    $GLOBALS[ '_conf' ][ 'epcServiceUser' ] = 'icampus';
    $GLOBALS[ '_conf' ][ 'epcServicePassword' ] = '1ntelIgent';*/

    FromKernel::uses (
        'utils/iterators.lib', 
        'utils/input.lib', 
        'utils/validator.lib' 
    );

    From::Module ( 'ICEPC' )->uses (
        'utils/stringbuffer.lib',
        'users/userlist.lib', 
        'users/claroclass.lib',
        'users/classutils.lib.php',
        'connectors/adminuser.lib',
        'epc/helpers.lib',
        'epc/epc.lib',
        'epc/epcclasses.lib'
    );

    if ( !claro_is_course_manager () )
    {
        claro_die ( get_lang ( "Not allowed!" ) );
    }

    $userInput = Claro_UserInput::getInstance ();

    $cmd = $userInput->get ( 'cmd', 'preview' );
    $out = new Claro_StringBuffer;
    
    if ( $cmd == 'exImport' || $cmd = 'preview' )
    {
        $epcSearchString = $userInput->get ( 'epcSearchString', '' );
        $epcAcadYear = $userInput->get ( 'epcAcadYear', epc_get_current_acad_year () );
        $epcSearchFor = $userInput->get ( 'epcSearchFor', 'course' );
        $epcLinkExistingStudentsToClass = $userInput->get ( 'epcLinkExistingStudentsToClass', 'yes' );
        $epcValidatePendingUsers = $userInput->get ( 'epcValidatePendingUsers', 'yes' );
    }
    elseif ( $cmd == 'exSync' )
    {
        $classId = $userInput->get ( 'classId' );
        
        if ( ! $classId )
        {
            throw new Exception("Missing class id");
        }
        
        $claroClass = new Claro_Class( Claroline::getDatabase() );
        $claroClass->load( $classId );
        
        $className = EpcClassName::parse( $claroClass->getName() );
        
        $epcAcadYear = $className->getEpcAcademicYear();
        $epcSearchFor = $className->getEpcClassType();
        $epcSearchString = $className->getEpcCourseOrProgramCode();
        $epcLinkExistingStudentsToClass = $userInput->get ( 'epcLinkExistingStudentsToClass', 'yes' );
        $epcValidatePendingUsers = $userInput->get ( 'epcValidatePendingUsers', 'yes' );
    }
    
    if ( $cmd == 'preview' )
    {
        $epcService = new EpcStudentListService (
            get_conf ( 'epcServiceUrl' ),
            get_conf ( 'epcServiceUser' ),
            get_conf ( 'epcServicePassword' )
        );
        
        if ( 'course' == $epcSearchFor )
        {
            $users = $epcService->getStudentsInCourse ( $epcAcadYear, $epcSearchString );
        }
        else
        {
            $users = $epcService->getStudentsInProgram ( $epcAcadYear, $epcSearchString );
        }
        
        if ( $epcService->hasError () )
        {
            throw new Exception("Epc Service error : <pre>".var_export($epcService->getInfo(), true)."</pre>");
        }
        
        /* $out->appendContent ( '<pre>' . var_export ( $epcService->getInfo (), true ) . '</pre>' ); */

        if ( count ( $users ) )
        {
            $courseUserList = new Claro_CourseUserList( claro_get_current_course_id() );
            $epcCourseUserListInfo = new EpcCourseUserListInfo( claro_get_current_course_id() );
            $courseUserListToUpdate = ( $epcLinkExistingStudentsToClass == 'yes' || $epcValidatePendingUsers == 'yes' ) 
                ? $epcCourseUserListInfo->getUsernameListToUpdate ( $users->getIterator (), $epcLinkExistingStudentsToClass == 'yes', $epcValidatePendingUsers == 'yes' ) 
                : array();
            
            $queryInfoTpl = new ModuleTemplate( 'ICEPC', 'epc_query_info.tpl.php' );
            $queryInfoTpl->assign( 'info', $users->getInfo() );
            $queryInfoTpl->assign( 'type', $epcSearchFor );
            
            $userListTpl = new ModuleTemplate( 'ICEPC', 'epc_userlist_preview.tpl.php' );
            $userListTpl->assign( 'responseInfo', $queryInfoTpl->render() );
            $userListTpl->assign( 'userListIterator', $users->getIterator() );
            $userListTpl->assign( 'actionUrl', claro_htmlspecialchars( Url::Contextualize ( get_module_entry_url('ICEPC') ) ) );
            $userListTpl->assign( 'epcSearchString', $epcSearchString );
            $userListTpl->assign( 'epcAcadYear', $epcAcadYear );
            $userListTpl->assign( 'epcSearchFor', $epcSearchFor );
            $userListTpl->assign( 'epcLinkExistingStudentsToClass', $epcLinkExistingStudentsToClass );
            $userListTpl->assign( 'epcValidatePendingUsers', $epcValidatePendingUsers );
            $userListTpl->assign( 'courseUserToUpdateList', $courseUserListToUpdate );
            $userListTpl->assign( 'courseUserList', $courseUserList->getUsernameList () );
            
            $out->appendContent( $userListTpl->render() );
        }
        else
        {
            $out->appendContent ( '<pre>' . var_export ( $epcService->getInfo (), true ) . '</pre>' );
        }
    }
    elseif ( $cmd == 'exImport' || $cmd == 'exSync' )
    {
        $epcService = new EpcStudentListService (
            get_conf ( 'epcServiceUrl' ),
            get_conf ( 'epcServiceUser' ),
            get_conf ( 'epcServicePassword' )
        );
        
        Console::debug("EPC service started");
        
        if ( 'course' == $epcSearchFor )
        {
            $users = $epcService->getStudentsInCourse ( $epcAcadYear, $epcSearchString );
        }
        else
        {
            $users = $epcService->getStudentsInProgram ( $epcAcadYear, $epcSearchString );
        }
        
        if ( $epcService->hasError () )
        {
            throw new Exception("Epc Service error : <pre>".var_export($epcService->getInfo(), true)."</pre>");
        }

        if ( count( $users ) )
        {
            $qrTpl = new ModuleTemplate( 'ICEPC', 'epc_queryresult.tpl.php' );
            
            $qrTpl->assign( 'type', $epcSearchFor );
            
            $qrTpl->assign ( 'serviceInfo', $epcService->getInfo () );
            $qrTpl->assign ( 'info', $users->getInfo () );

            $platformUserList = new Claro_PlatformUserList();
            $platformUserList->registerUserList( $users->getIterator(), 'ldap', true );
            
            $qrTpl->assign ( 'validUsersCnt', count ( $platformUserList->getValidUserIdList () ) );
            Console::debug("<pre>Valid users : ".var_export($platformUserList->getValidUserIdList(),true)."</pre>", 'debug');
            $qrTpl->assign ( 'newUsersCnt', count ( $platformUserList->getInsertedUserIdList () ) );
            Console::debug("<pre>Users added to platform : ".var_export($platformUserList->getInsertedUserIdList(),true)."</pre>", 'debug');
            $qrTpl->assign ( 'failuresCnt', count ( $platformUserList->getFailedUserInsertionList () ) );
            Console::debug("<pre>Failed users : ".var_export($platformUserList->getFailedUserInsertionList(),true)."</pre>", 'debug');
            
            $epcUserDataCache = new EpcUserDataCache();
            
            $epcUserDataCache->registerUserData( $users->getIterator (), $platformUserList->getValidUserIdList () );
            
            $epcClassName = new EpcClassName($epcSearchFor,$epcAcadYear,$epcSearchString);
            $epcClass = new EpcClass($epcClassName);
            
            // BEFORE : class could exist or not in the course and user can be enroled or not
            
            if ( !$epcClass->associatedClassExists() )
            {
                $epcClass->createAssociatedClass();
                
                Console::debug("<pre>Associated class {$epcClass->getName()} created</pre>", 'debug');
                
                $claroClass = $epcClass->getAssociatedClass();
                
                // add class to current course
            }
            else
            {
                $claroClass = $epcClass->getAssociatedClass();
                // add class to current course if not already there
                // .. this method should take an argument saying whether an existing 
                // .. user not related to the class must me enrolled twice or only 
                // .. once (the latter will cause the user registration to be change 
                // .. to a class registration)
            }
            
            // AFTER : class registered in course with previous user list enroled in course
            
            Console::debug("<pre>Associated class {$claroClass->getName()} loaded</pre>",'debug');
            
            $qrTpl->assign ( 'className', $claroClass->getName () );
            
            // BEFORE : class registered in course with previous user list enroled in course
            
            // add valid new EPC users to class
            
            $claroClassUserList = new Claro_ClassUserList( $claroClass );
            $claroClassUserList->addUserIdList( $platformUserList->getValidUserIdList () );
            
            Console::debug("<pre>Add user list to class {$claroClass->getName()}</pre>", 'debug');
            
            
            if ( ! $claroClass->isRegisteredToCourse ( claro_get_current_course_id () ) )
            {
                $claroClass->registerToCourse( claro_get_current_course_id () );
                Console::debug("<pre>Register class {$claroClass->getName()} to current course</pre>", 'debug');
            }
            
            $courseList = $claroClass->getClassCourseList();
    
            foreach ( $courseList as $course )
            {
                $courseObj = new Claro_Course( $course['code'] );
                $courseObj->load();
                
                Console::debug( '<pre>Register class users in course '.$course['code'].'</pre>', 'debug' );
                
                $courseUserList = new Claro_BatchCourseRegistration($courseObj);
                
                if ( $claroClass->isRegisteredToCourse ( $courseObj->courseId ) )
                {
                    $userAlreadyInClass = $claroClassUserList->getClassUserIdList( true );
                }
                
                $courseUserList->addUserIdListToCourse( 
                    $claroClassUserList->getClassUserIdList (), 
                    true, 
                    $epcLinkExistingStudentsToClass == 'yes', 
                    $userAlreadyInClass, $epcValidatePendingUsers == 'yes' );
            }
            
            $qrTpl->assign ( 'courseList', $courseList );
            
            // AFTER : new valid user list from EPC added to class and enrolled to course
            
            $out->appendContent( $qrTpl->render() );
            
            Console::debug("EPC service ended");
        }
        else
        {
            $out->appendContent ( '<pre>' . var_export ( $epcService->getInfo (), true ) . '</pre>' );
        }
    }
    else
    {
        claro_die( get_lang('Unknown command') );
    }

    echo claro_utf8_encode( $out->render () );
}
catch ( Exception $e )
{
    $out = new Claro_StringBuffer;
    
    $out->appendContent ( $e->getMessage () );
    
    if ( claro_debug_mode() )
    {
        $out->appendContent ( $e->getTraceAsString () );
    }
    
    echo $out->render ();
}
