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
    $moduleLabel = 'ICEPC';

    require_once dirname ( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';
    
    set_and_load_current_module($moduleLabel);

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
        'epc/epcclasses.lib',
        'epc/epclog.lib'
    );

    if (  ! ( claro_is_platform_admin() || ( claro_is_course_manager () && claro_is_in_a_course() ) ) )
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
            // check if class exists
            $classId = $userInput->get ( 'classId' );
            
            if ( $classId )
            {
                $claroClass = new Claro_Class( Claroline::getDatabase() );
                $claroClass->load( $classId );
                $epcClass = EpcClass::loadFromClass($claroClass);
                $epcClass->updateEpcClassSyncErrorDate( null, "Epc Service error : <pre>".var_export($epcService->getInfo(), true)."</pre>" );
            }
            else
            {
                $epcClassName = new EpcClassName($epcSearchFor,$epcAcadYear,$epcSearchString);
                $epcClass = new EpcClass($epcClassName);
                $epcClass->updateEpcClassSyncErrorDate( null, "Epc Service error : <pre>".var_export($epcService->getInfo(), true)."</pre>" );
            
                if ( $epcClass->associatedClassExists() )
                {
                    $claroClass = $epcClass->getAssociatedClass();
                }
                else
                {
                    $claroClass = null;
                }
            }
            
            if ( $claroClass )
            {                
                // display class user list and propose to add it to the course
                if ( claro_is_in_a_course () )
                {
                    if ( !$claroClass->isRegisteredToCourse ( claro_get_current_course_id () ) )
                    {
                        $dialogBox = new DialogBox();

                        $dialogBox->error( get_lang( 'The EPC remote service is unavailable at the moment, it\'s not possible to retreive the latest version of the user list you asked for' ) );

                        $dialogBox->question( get_lang('Meanwhile, the following users from the requested EPC list are already registred to the platform.<br /> Do you want to add them to your course ?)' )
                            . '<br />' 
                            . '<a href="'.claro_htmlspecialchars( 
                                Url::Contextualize ( 
                                    get_module_entry_url('ICEPC') 
                                    . '?cmd=addExistingClass&classId='.$claroClass->getId () 
                                    . '&epcLinkExistingStudentsToClass=' . $epcLinkExistingStudentsToClass 
                                    . '&epcValidatePendingUsers=' . $epcValidatePendingUsers ) ).'">'.get_lang('yes').'</a>'
                            . '<a href="'.claro_htmlspecialchars( Url::Contextualize ( get_module_entry_url('ICEPC') ) ).'">'.get_lang('no').'</a>' );

                        $out->appendContent( $dialogBox->render() );

                        $classUserList = new Claro_ClassUserList( $claroClass, Claroline::getDatabase() );
                        $courseUserList = new Claro_CourseUserList( claro_get_current_course_id(), Claroline::getDatabase() );

                        $epcUserDataCache = new EpcUserDataCache( Claroline::getDatabase () );
                        $epcCachedUserData = $epcUserDataCache->getAllUsersCachedData( $courseUserList->getUserIdList () );

                        $classUserListIterator = $classUserList->getClassUserListIterator();
                        $classUserListIterator->useId('user_id');

                        $userListTemplate = new ModuleTemplate('ICEPC', 'epc_class_users.tpl.php');
                        $userListTemplate->assign( 'classUserList', $classUserListIterator );
                        $userListTemplate->assign( 'courseUserList', $courseUserList->getUserIdList () );
                        $userListTemplate->assign( 'epcUserData', $epcCachedUserData );

                        $out->appendContent( $userListTemplate->render() );
                    }
                    else
                    {
                        $dialogBox = new DialogBox();

                        $dialogBox->error( get_lang( 'The EPC remote service is unavailable at the moment, it\'s not possible to retreive and update this user list' )
                            .'<br />'
                            .get_lang( 'Please try again later' ) );
                        
                        if ( claro_is_platform_admin () )
                        {
                            $dialogBox->info( get_lang( 'Something can be wrong with the EPC remote service or the configuration of the module' )
                            . '<br />'
                            . '<pre>'.var_export($epcService->getInfo(), true ) . '</pre>' );
                        }

                        $out->appendContent( $dialogBox->render() );
                    }
                }
                else
                {
                    $dialogBox = new DialogBox();

                    $dialogBox->error( get_lang( 'The EPC remote service is unavailable at the moment, it\'s not possible to retreive and update this user list' )
                        . '<br />'
                        . get_lang( 'Please try again later' )
                        . '<br />' 
                        . get_lang( 'Something can be wrong with the EPC remote service or the configuration of the module' )
                        . '<br />'
                        . '<pre>'.var_export($epcService->getInfo(), true ) . '</pre>' );

                    $out->appendContent( $dialogBox->render() );
                }
            }
            else
            {
                $dialogBox = new DialogBox();

                $dialogBox->error( get_lang( 'The EPC remote service is unavailable at the moment, it\'s not possible to retreive and update this user list' )
                    . '<br />'
                    . get_lang( 'Please try again later' )
                    . '<br />' 
                    . get_lang( 'Something can be wrong with the EPC remote service or the configuration of the module' )
                    . '<br />'
                    . '<pre>'.var_export($epcService->getInfo(), true ) . '</pre>' );

                $out->appendContent( $dialogBox->render() );
            }
        }
        else
        {
            /* $out->appendContent ( '<pre>' . var_export ( $epcService->getInfo (), true ) . '</pre>' ); */

            if ( count ( $users ) )
            {
                $queryInfoTpl = new ModuleTemplate( 'ICEPC', 'epc_query_info.tpl.php' );
                $queryInfoTpl->assign( 'info', $users->getInfo() );
                $queryInfoTpl->assign( 'type', $epcSearchFor );

                if ( claro_is_in_a_course () )
                {
                    $courseUserList = new Claro_CourseUserList( claro_get_current_course_id() );
                    $epcCourseUserListInfo = new EpcCourseUserListInfo( claro_get_current_course_id() );
                    $courseUserListToUpdate = ( $epcLinkExistingStudentsToClass == 'yes' || $epcValidatePendingUsers == 'yes' ) 
                        ? $epcCourseUserListInfo->getUsernameListToUpdate ( $users->getIterator (), $epcLinkExistingStudentsToClass == 'yes', $epcValidatePendingUsers == 'yes' ) 
                        : array();

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
                }
                else
                {
                    $epcUserListInfo = new EpcUserListInfo();
                    $epcUserListToUpdate = $epcUserListInfo->getUserListToUpdate( $users->getIterator (), true );

                    $classId = $userInput->get ( 'classId' );

                    if ( $classId )
                    {
                        $claroClass = new Claro_Class( Claroline::getDatabase() );
                        $claroClass->load( $classId );

                        $classUserList = new Claro_ClassUserList($claroClass);
                        $classUsernameList = $classUserList->getClassUserListIndexedByUsername();
                    }
                    else
                    {
                        $classUsernameList = array();
                    }


                    $userListTpl = new ModuleTemplate( 'ICEPC', 'epc_userlist_preview_admin.tpl.php' );
                    $userListTpl->assign( 'responseInfo', $queryInfoTpl->render() );
                    $userListTpl->assign( 'userListIterator', $users->getIterator() );
                    $userListTpl->assign( 'actionUrl', claro_htmlspecialchars( Url::Contextualize ( get_module_url('ICEPC') ) . '/admin.php' ) );
                    $userListTpl->assign( 'epcSearchString', $epcSearchString );
                    $userListTpl->assign( 'epcAcadYear', $epcAcadYear );
                    $userListTpl->assign( 'epcSearchFor', $epcSearchFor );
                    $userListTpl->assign( 'epcLinkExistingStudentsToClass', $epcLinkExistingStudentsToClass );
                    $userListTpl->assign( 'epcValidatePendingUsers', $epcValidatePendingUsers );
                    $userListTpl->assign( 'platformToUpdate', $epcUserListToUpdate );
                    $userListTpl->assign( 'classUserList', $classUsernameList );
                }

                $out->appendContent( $userListTpl->render() );

            }
            else
            {
                $out->appendContent ( '<pre>' . var_export ( $epcService->getInfo (), true ) . '</pre>' );
            }
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
            if ( !empty($epcSearchFor) && !empty($epcAcadYear) && !empty($epcSearchString)  )
            {
                $epcClassName = new EpcClassName($epcSearchFor,$epcAcadYear,$epcSearchString);
                $epcClass = new EpcClass($epcClassName);
                $epcClass->updateEpcClassSyncErrorDate( null, "Epc Service error : <pre>".var_export($epcService->getInfo(), true)."</pre>" );
                EpcLog::syncError( $epcClassName, $epcService->getInfo(), true );
            }
            
            throw new Exception("Epc Service error : <pre>".var_export($epcService->getInfo(), true)."</pre>");
        }

        if ( count( $users ) )
        {
            $epcMessage = new EpcLogMessage();
            
            $qrTpl = new ModuleTemplate( 'ICEPC', 'epc_queryresult.tpl.php' );
            
            $qrTpl->assign( 'type', $epcSearchFor );
            
            $qrTpl->assign ( 'serviceInfo', $epcService->getInfo () );
            $qrTpl->assign ( 'info', $users->getInfo () );
            
            if ( claro_is_in_a_course () )
            {
                $qrTpl->assign( 'backUrl', Url::Contextualize ( get_module_entry_url ( 'ICEPC') ) );
            }
            else
            {
                $qrTpl->assign( 'backUrl', Url::Contextualize ( get_module_url ( 'ICEPC') ) . '/admin.php' );
            }

            $platformUserList = new Claro_PlatformUserList();
            $platformUserList->registerUserList( $users->getIterator(), 'ldap', true );
            
            $qrTpl->assign ( 'validUsersCnt', count ( $platformUserList->getValidUserIdList () ) );
            Console::debug("<pre>Valid users : ".var_export($platformUserList->getValidUserIdList(),true)."</pre>", 'debug');
            $epcMessage->setValidUsers($platformUserList->getValidUserIdList ());
            $qrTpl->assign ( 'newUsersCnt', count ( $platformUserList->getInsertedUserIdList () ) );
            Console::debug("<pre>Users added to platform : ".var_export($platformUserList->getInsertedUserIdList(),true)."</pre>", 'debug');
            $epcMessage->setInsertedUsers($platformUserList->getInsertedUserIdList());
            $qrTpl->assign ( 'failuresCnt', count ( $platformUserList->getFailedUserInsertionList () ) );
            Console::debug("<pre>Failed users : ".var_export($platformUserList->getFailedUserInsertionList(),true)."</pre>", 'debug');
            $epcMessage->setFailedUsers($platformUserList->getFailedUserInsertionList());
            
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
            
            // add condition for administration script
            if ( claro_is_in_a_course () )
            {
                if ( ! $claroClass->isRegisteredToCourse ( claro_get_current_course_id () ) )
                {
                    $claroClass->registerToCourse( claro_get_current_course_id () );
                    Console::debug("<pre>Register class {$claroClass->getName()} to current course</pre>", 'debug');
                }
            }
            
            $courseList = $claroClass->getClassCourseList();
    
            foreach ( $courseList as $course )
            {
                $courseObj = new Claro_Course( $course['code'] );
                $courseObj->load();
                
                $epcMessage->addCourse($course['code']);
                
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
            
            $epcClass->updateEpcClassSyncDate();
            EpcLog::getInstance()->syncDone( $epcClassName, $epcMessage->__toString(), $claroClass->getId () );
            
            Console::debug("EPC service ended");
        }
        else
        {
            $epcClassName = new EpcClassName($epcSearchFor,$epcAcadYear,$epcSearchString);
            $epcMessage->setMessageString(var_export ( $epcService->getInfo (), true ));
            EpcLog::syncError( $epcClassName, var_export ( $epcService->getInfo (), true ) );
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
    
    /*if ( !empty($epcSearchFor) && !empty($epcAcadYear) && !empty($epcSearchString)  )
    {
        $epcClassName = new EpcClassName($epcSearchFor,$epcAcadYear,$epcSearchString);
        $epcClass = new EpcClass($epcClassName);
        $epcClass->updateEpcClassSyncErrorDate( null, "Epc Service exception : <pre>".$e->getMessage ()."</pre>" );
        EpcLog::syncError( $epcClassName, var_export ( $e->getMessage (), true ) );
    }*/
    
    echo $out->render ();
}
