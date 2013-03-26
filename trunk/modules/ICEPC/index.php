<?php

// $Id$

/**
 * CLAROLINE
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
        'users/userlist.lib', 
        'users/claroclass.lib',
        'users/classutils.lib.php',
        'connectors/adminuser.lib',
        'epc/helpers.lib',
        'epc/epc.lib',
        'epc/epcclasses.lib'
    );

    if ( !claro_is_user_authenticated () )
    {
        claro_disp_auth_form ( true );
    }

    if ( !claro_is_course_manager () )
    {
        claro_die ( get_lang ( "Not allowed!" ) );
    }

    $userInput = Claro_UserInput::getInstance ();

    $cmd = $userInput->get ( 'cmd', 'listEpcClasses' );
    
    Claroline::getDisplay()->body->appendContent('<script type="text/javascript">
$(function(){
    $(\'.checkClassDeletion\').click(function(){
        return confirm("'.get_lang( "You are going to delete this class, do you want to continue ?" ).'");
    });
});
</script>');
    
    Claroline::getDisplay()->body->appendContent('<script type="text/javascript">
$(function(){
    $(\'.warnTakesTime\').click(function(){
        return confirm("'.get_lang( "This operation could take some time, please wait until it's finished" ).'");
    });
});
</script>');
    
    $breadcrumbs = ClaroBreadCrumbs::getInstance();
    $breadcrumbs->setCurrent( get_lang('EPC'), php_self () );
    
    $toolTitle = new ToolTitle( get_lang('Manage student lists from EPC') );

    
    if ( $cmd == 'rqImport' || $cmd == 'exImport' )
    {
        $breadcrumbs->append( get_lang('Import students from EPC') );
        
        $epcSearchString = $userInput->get ( 'epcSearchString', '' );
        $epcAcadYear = $userInput->get ( 'epcAcadYear', epc_get_current_acad_year () );
        $epcSearchFor = $userInput->get ( 'epcSearchFor', 'course' );
        $epcLinkExistingStudentsToClass = $userInput->get ( 'epcLinkExistingStudentsToClass', 'yes' );
    }
    
    if ( $cmd == 'exSync' )
    {
        $breadcrumbs->append( get_lang('Synchronize students with EPC') );
        
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
    }
    
    if ( $cmd == 'dispUserList' )
    {
        $breadcrumbs->append( get_lang('List of students in Class') );
        
        
        $classId = $userInput->get ( 'classId' );
        
        if ( ! $classId )
        {
            throw new Exception("Missing class id");
        }
        
        $claroClass = new Claro_Class( Claroline::getDatabase() );
        $claroClass->load( $classId );
        
        $toolTitle->setSubTitle(get_lang('List of student in class %className', array('%className' => $claroClass->getName ())));
        $toolTitle->addCommand( get_lang('Delete'), Url::Contextualize ( get_module_entry_url ( 'ICEPC' ) . '?cmd=exUnreg&classId='.$classId ), 'delete', array( 'class' => 'checkClassDeletion' ) );
        $toolTitle->addCommand( get_lang('Update'), Url::Contextualize ( get_module_entry_url ( 'ICEPC' ) . '?cmd=exSync&classId='.$classId ), 'import' );
        
        $classUserList = new Claro_ClassUserList( $claroClass, Claroline::getDatabase() );
        $courseUserList = new Claro_CourseUserList( claro_get_current_course_id(), Claroline::getDatabase() );
        
        $classUserListIterator = $classUserList->getClassUserListIterator();
        $classUserListIterator->useId('user_id');
        
        $userListTemplate = new ModuleTemplate('ICEPC', 'epc_class_users.tpl.php');
        $userListTemplate->assign( 'classUserList', $classUserListIterator );
        $userListTemplate->assign( 'courseUserList', $courseUserList->getUserIdList () );
        
        Claroline::getDisplay()->body->appendContent( $userListTemplate->render() );
        
    }
    if ( $cmd == 'listEpcClasses' )
    {
        // FromKernel::uses('class.lib');
        $epcClassList = new EpcCourseClassList( claro_get_current_course_id () );
        $epcListToDisplay = $epcClassList->getEpcClassList();
        
        $list = new ModuleTemplate ( 'ICEPC', 'epc_classlist.tpl.php' );
        $list->assign( 'epcClassList' , $epcListToDisplay );
        
        $toolTitle->addCommand( get_lang('Import student list from EPC'), Url::Contextualize ( get_module_entry_url ( 'ICEPC' ) . '?cmd=rqImport' ), 'class' );
        
        Claroline::getDisplay ()->body->appendContent ( $list->render () );
    }
    elseif ( $cmd == 'rqImport' )
    {
        $dialogBox = new DialogBox;

        $form = new ModuleTemplate ( 'ICEPC', 'epc_form.tpl.php' );
        $form->assign ( 'actionUrl', php_self () );
        $form->assign ( 'epcSearchString', $epcSearchString );
        $form->assign ( 'epcAcadYear', $epcAcadYear );
        $form->assign ( 'epcSearchFor', $epcSearchFor );
        $form->assign ( 'epcLinkExistingStudentsToClass', $epcLinkExistingStudentsToClass );
        
        Claroline::getDisplay ()->body->appendContent ( $form->render () );
    }
    // check before import/sync
    elseif ( $cmd == 'exImport' || $cmd == 'exSync' )
    {
        $epcService = new EpcStudentListService (
            get_conf ( 'epcServiceUrl' ),
            get_conf ( 'epcServiceUser' ),
            get_conf ( 'epcServicePassword' )
        );
        
        /*if ( 'course' == $epcSearchFor )
        {
            $users = $epcService->getStudentsInCourse ( $epcAcadYear, $epcSearchString ); // LBIO1111A' );
        }
        else
        {
            $users = $epcService->getStudentsInProgram ( $epcAcadYear, $epcSearchString );
        }

        if ( !empty ( $users ) )
        {
            Claroline::getDisplay ()->body->appendContent ( '<pre>' . var_export ( $epcService->getInfo (), true ) . '</pre>' );

            Claroline::getDisplay ()->body->appendContent ( '<pre>' . var_export ( $users->getInfo (), true ) . '</pre>' );

            $platformUserList = new Claro_PlatformUserList();
            $platformUserList->registerUserList( $users->getIterator(), 'ldap', true );
            
            Claroline::getDisplay ()->body->appendContent ( '<pre>Number of valid users : ' . count ( $platformUserList->getValidUserIdList ()) . '</pre>' );
            Claroline::getDisplay ()->body->appendContent ( '<pre>Number inserted : ' . count ( $platformUserList->getInsertedUserIdList ()) . '</pre>' );
            Claroline::getDisplay ()->body->appendContent ( '<pre>Failed insertions : ' . var_export ( $platformUserList->getFailedUserInsertionList (), true ) . '</pre>' );
            
            $epcClassName = new EpcClassName($epcSearchFor,$epcAcadYear,$epcSearchString);
            $epcClass = new EpcClass($epcClassName);
            
            // BEFORE : class could exist or not in the course and user can be enroled or not
            
            if ( !$epcClass->associatedClassExists() )
            {
                Claroline::getDisplay ()->body->appendContent ( '<pre>Create associated class for ' . $epcClass->getName() . '</pre>' );
                $epcClass->createAssociatedClass();
                
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
            
            Claroline::getDisplay ()->body->appendContent ( '<pre>Associated class : ' . var_export ( $claroClass, true ) . '</pre>' );
            
            // BEFORE : class registered in course with previous user list enroled in course
            
            // add valid new EPC users to class
            
            $claroClassUserList = new Claro_ClassUserList( $claroClass );
            $claroClassUserList->addUserIdList( $platformUserList->getValidUserIdList () );
            
            Claroline::getDisplay ()->body->appendContent ( '<pre>Valid users added to class</pre>' );
            
            if ( ! $claroClass->isRegisteredToCourse ( claro_get_current_course_id () ) )
            {
                $claroClass->registerToCourse( claro_get_current_course_id () );
                Claroline::getDisplay ()->body->appendContent ( '<pre>Class registrered to current course</pre>' );
            }
            
            $courseList = $claroClass->getClassCourseList();
    
            foreach ( $courseList as $course )
            {
                $courseObj = new Claro_Course( $course['code'] );
                $courseObj->load();
                Claroline::getDisplay ()->body->appendContent ( '<pre>Register class users in course '.$course['code'].'</pre>' );
                $courseUserList = new Claro_BatchCourseRegistration($courseObj);
                
                if ( $claroClass->isRegisteredToCourse ( $courseObj->courseId ) )
                {
                    $userAlreadyInClass = $claroClassUserList->getClassUserIdList( true );
                }
                
                $courseUserList->addUserIdListToCourse( $claroClassUserList->getClassUserIdList (), true, $epcLinkExistingStudentsToClass == 'yes', $userAlreadyInClass );
            }
            
            // AFTER : new valid user list from EPC added to class and enrolled to course
        }
        else
        {
            Claroline::getDisplay ()->body->appendContent ( '<pre>' . var_export ( $epcService->getInfo (), true ) . '</pre>' );
        }*/
        
        $epcAjaxWrapper = new ModuleTemplate( 'ICEPC', 'epc_ajax_container.tpl.php' );
        $epcAjaxWrapper->assign ( 'epcSearchString', $epcSearchString );
        $epcAjaxWrapper->assign ( 'epcAcadYear', $epcAcadYear );
        $epcAjaxWrapper->assign ( 'epcSearchFor', $epcSearchFor );
        $epcAjaxWrapper->assign ( 'epcLinkExistingStudentsToClass', $epcLinkExistingStudentsToClass );
        $epcAjaxWrapper->assign ( 'cmd', $cmd );
        
        Claroline::getDisplay()->body->appendContent( $epcAjaxWrapper->render() );
    }
    elseif ( $cmd == 'exUnreg' )
    {
        $dialogBox = new DialogBox();
        
        $classId = $userInput->get ( 'classId' );
        
        if ( ! $classId )
        {
            throw new Exception("Missing class id");
        }
        
        $claroClass = new Claro_Class( Claroline::getDatabase() );
        $claroClass->load( $classId );
        
        // delete users from course
        
        $course = new Claro_Course( claro_get_current_course_id () );
        $course->load();
        
        $classUserIdList = $claroClass->getClassUserList()->getClassUserIdList();
        
        $courseBatchRegistretion = new Claro_BatchCourseRegistration( $course );
        
        $courseBatchRegistretion->removeUserIdListFromCourse( $classUserIdList, true );
        
        $dialogBox->success("Users deleted from course");
        
        // unregister class from course
        $claroClass->unregisterFromCourse( $course->courseId );
        
        $dialogBox->success("Class unregistered from course");
        
        Claroline::getDisplay()->body->appendContent( $dialogBox->render() );
    }
    
    Claroline::getDisplay ()->body->prependContent ( $toolTitle->render () );

    echo Claroline::getDisplay ()->render ();
}
catch ( Exception $e )
{
    Claroline::getDisplay ()->body->appendContent ( $e->getMessage () );
    
    if ( claro_debug_mode() )
    {
        Claroline::getDisplay ()->body->appendContent ( $e->getTraceAsString () );
    }
    
    echo Claroline::getDisplay ()->render ();
}
