<?php

// $Id$

/**
 * EPC module entry point
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
    
    FromKernel::uses (
        'utils/iterators.lib', 
        'utils/input.lib', 
        'utils/validator.lib',
        'connectors/adminuser.lib',
        'utils/stringbuffer.lib',
        'users/userlist.lib', 
        'users/claroclass.lib',
        'users/classutils.lib.php'
    );

    pushClaroMessage('kernel libraries loaded from kernel','debug');
    
    From::Module ( 'ICEPC' )->uses (
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
    
    JavascriptLanguage::getInstance ()->addLangVar("This operation could take some time, please wait until it's finished");
    JavascriptLanguage::getInstance ()->addLangVar("You are going to delete this class, do you want to continue ?");

    $userInput = Claro_UserInput::getInstance ();

    $cmd = $userInput->get ( 'cmd', 'listEpcClasses' );
    
    Claroline::getDisplay()->body->appendContent('<script type="text/javascript">
$(function(){
    $(\'.checkClassDeletion\').click(function(){
        return confirm( Claroline.getLang( "You are going to delete this class, do you want to continue ?" ) );
    });
});
</script>');
    
    Claroline::getDisplay()->body->appendContent('<script type="text/javascript">
$(function(){
    $(\'.warnTakesTime\').click(function(){
        return confirm( Claroline.getLang( "This operation could take some time, please wait until it\'s finished" ) );
    });
});
</script>');
    
    $breadcrumbs = ClaroBreadCrumbs::getInstance();
    $breadcrumbs->setCurrent( get_lang('EPC'), php_self () );
    
    $toolTitle = new ToolTitle( get_lang('Manage student lists from EPC') );
    
    if ( $cmd == 'rqImport' || $cmd == 'preview' || $cmd == 'exImport' )
    {
        $breadcrumbs->append( get_lang('Import students from EPC') );
        
        $epcSearchString = $userInput->get ( 'epcSearchString', '' );
        $epcAcadYear = $userInput->get ( 'epcAcadYear', epc_get_current_acad_year () );
        $epcSearchFor = $userInput->get ( 'epcSearchFor', 'course' );
        $epcLinkExistingStudentsToClass = $userInput->get ( 'epcLinkExistingStudentsToClass', 'yes' );
        $epcValidatePendingUsers = $userInput->get ( 'epcValidatePendingUsers', 'yes' );
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
        
        $props = new EpcClassQueryProperties ( $className );
        $properties = $props->getOptions();
        
        if ( is_null($properties['epcLinkExistingStudentsToClass']) )
        {
            $epcLinkExistingStudentsToClass = $userInput->get ( 'epcLinkExistingStudentsToClass', 'yes' );
        }
        else
        {
            $epcLinkExistingStudentsToClass = $properties['epcLinkExistingStudentsToClass'] ? 'yes' : 'no';
        }
        
        if ( is_null($properties['epcValidatePendingUsers']) )
        {
            $epcValidatePendingUsers = $userInput->get ( 'epcValidatePendingUsers', 'yes' );
        }
        else
        {
            $epcValidatePendingUsers = $properties['epcValidatePendingUsers'] ? 'yes' : 'no';
        }
        
        Console::debug("Properties loaded ".var_export($properties, true));
        
        $epcAcadYear = $className->getEpcAcademicYear();
        $epcSearchFor = $className->getEpcClassType();
        $epcSearchString = $className->getEpcCourseOrProgramCode();
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
        $toolTitle->addCommand( get_lang('Update'), Url::Contextualize ( get_module_entry_url ( 'ICEPC' ) . '?cmd=exSync&classId='.$classId ), 'import', array('class' => 'warnTakesTime' ) );
        
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
        
        Claroline::getDisplay()->body->appendContent( $userListTemplate->render() );
        
    }
    if ( $cmd == 'listEpcClasses' )
    {
        $dialogBox = new DialogBox();
        $dialogBox->info(
            get_lang('This tool allows you to import student lists from the official EPC database. However, it does not ensure an automatic synchronisation of those list so if you import a list a the beginning of the academic year or quadrimester, you\'ll have to update it later to import the latest registered students.' )
            . '<br /><em><small>' 
            . get_lang('This is a limitation due to the current implementation of EPC and we will try to remove this limitation as soon as the EPC service allows it.') 
            . '</small></em>');
        
        Claroline::getDisplay ()->body->appendContent ( $dialogBox->render () );
        
        $epcClassList = new EpcClassList();
        $epcListToDisplay = $epcClassList->getEpcCourseClassList(claro_get_current_course_id ());
        
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
        $form->assign ( 'epcValidatePendingUsers', $epcValidatePendingUsers );
        
        Claroline::getDisplay ()->body->appendContent ( $form->render () );
    }
    // check before import/sync
    elseif ( $cmd == 'preview' || $cmd == 'exImport' || $cmd == 'exSync' )
    {
        $epcAjaxWrapper = new ModuleTemplate( 'ICEPC', 'epc_ajax_container.tpl.php' );
        $epcAjaxWrapper->assign ( 'epcSearchString', $epcSearchString );
        $epcAjaxWrapper->assign ( 'epcAcadYear', $epcAcadYear );
        $epcAjaxWrapper->assign ( 'epcSearchFor', $epcSearchFor );
        $epcAjaxWrapper->assign ( 'epcLinkExistingStudentsToClass', $epcLinkExistingStudentsToClass );
        $epcAjaxWrapper->assign ( 'epcValidatePendingUsers', $epcValidatePendingUsers );
        $epcAjaxWrapper->assign ( 'cmd', $cmd );
        
        Claroline::getDisplay()->body->appendContent( $epcAjaxWrapper->render() );
    }
    elseif ( $cmd == 'addExistingClass' )
    {
        $classId = $userInput->get( 'classId' );
        $epcLinkExistingStudentsToClass = $userInput->get ( 'epcLinkExistingStudentsToClass', 'yes' );
        $epcValidatePendingUsers = $userInput->get ( 'epcValidatePendingUsers', 'yes' );
        
        if ( ! $classId )
        {
            throw new Exception( get_lang( 'Missing class id' ) );
        }
        
        if ( !claro_is_in_a_course () )
        {
            throw new Exception( get_lang ( 'Cannot add an existing class outside of a course' ) );
        }
        
        $claroClass = new Claro_Class( Claroline::getDatabase() );
        $claroClass->load( $classId );
        
        $dialogBox = new DialogBox();
        
        if ( ! $claroClass->isRegisteredToCourse ( claro_get_current_course_id () ) )
        {
            $claroClass->registerToCourse( claro_get_current_course_id () );
            
            Console::debug("<pre>Register class {$claroClass->getName()} to current course</pre>", 'debug');
        
        
            $claroClassUserList = new Claro_ClassUserList( $claroClass );

            $courseObj = new Claro_Course( claro_get_current_course_id () );
            $courseObj->load();

            Console::debug( '<pre>Register class users in course '.$courseObj->courseId.'</pre>', 'debug' );

            $courseUserList = new Claro_BatchCourseRegistration($courseObj);

            if ( $claroClass->isRegisteredToCourse ( $courseObj->courseId ) )
            {
                $userAlreadyInClass = $claroClassUserList->getClassUserIdList( true );
            }

            $courseUserList->addUserIdListToCourse( 
                $claroClassUserList->getClassUserIdList (), 
                $claroClass, 
                $epcLinkExistingStudentsToClass == 'yes', 
                $userAlreadyInClass, $epcValidatePendingUsers == 'yes' );
            
            $dialogBox->success(get_lang('User list added to course'));
        }
        else
        {
            // already registered
            $dialogBox->info(get_lang('Nothing to do : user list already in course'));
        }
        
        $dialogBox->info('<a href="'.Url::Contextualize(get_module_url('ICEPC')).'">'.get_lang('Back').'</a>');
        
        Claroline::getDisplay()->body->appendContent( $dialogBox->render() );
        
        $epcClassName = EpcClassName::parse( $claroClass->getName() );
        $props = new EpcClassQueryProperties ( $epcClassName );
        $props->setOptions( $epcLinkExistingStudentsToClass, $epcValidatePendingUsers );
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
        
        $courseBatchRegistretion->removeUserIdListFromCourse( $classUserIdList, $claroClass );
        
        $dialogBox->success(get_lang("Users deleted from course"));
        
        // unregister class from course
        /*if ( $claroClass->isRegisteredToCourse( $course->courseId ) )
        {
            $claroClass->unregisterFromCourse( $course->courseId );
        }*/
        
        $dialogBox->success(get_lang("Class unregistered from course"));
        
        $dialogBox->info('<a href="'.Url::Contextualize(get_module_url('ICEPC')).'">'.get_lang('Back').'</a>');
        
        Claroline::getDisplay()->body->appendContent( $dialogBox->render() );
    }
    
    if ( claro_is_platform_admin () )
    {
        $toolTitle->addAdvancedCommand( 'EPC module administration', get_module_url('ICEPC').'/admin.php?cidReset=true&cidReq=', 'platformadmin' );
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
