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
    $moduleLabel = 'ICEPC';
    
    require_once dirname ( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';
    
    set_and_load_current_module($moduleLabel);
    
    if ( !claro_is_platform_admin () )
    {
        claro_disp_auth_form();
        die();
    }

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

    if ( ! claro_is_platform_admin() )
    {
        claro_die ( get_lang ( "Not allowed!" ) );
    }

    $userInput = Claro_UserInput::getInstance ();

    $cmd = $userInput->get ( 'cmd', 'listEpcClasses' );
    
    JavascriptLanguage::getInstance ()->addLangVar("This operation could take some time, please wait until it's finished");
    JavascriptLanguage::getInstance ()->addLangVar("You are going to delete this class, do you want to continue ?");
    
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
        
        $epcAcadYear = $className->getEpcAcademicYear();
        $epcSearchFor = $className->getEpcClassType();
        $epcSearchString = $className->getEpcCourseOrProgramCode();
        $epcLinkExistingStudentsToClass = $userInput->get ( 'epcLinkExistingStudentsToClass', 'yes' );
        $epcValidatePendingUsers = $userInput->get ( 'epcValidatePendingUsers', 'yes' );
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
        $toolTitle->addCommand( get_lang('Delete'), Url::Contextualize ( get_module_url ( 'ICEPC' ) . '/admin.php?cmd=exUnreg&classId='.$classId ), 'delete', array( 'class' => 'checkClassDeletion' ) );
        $toolTitle->addCommand( get_lang('Update'), Url::Contextualize ( get_module_url ( 'ICEPC' ) . '/admin.php?cmd=exSync&classId='.$classId ), 'import', array('class' => 'warnTakesTime' ) );
        
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
        // FromKernel::uses('class.lib');
        $epcClassList = new EpcClassList();
        $epcListToDisplay = $epcClassList->getEpcClassList();
        
        $list = new ModuleTemplate ( 'ICEPC', 'epc_classlist_admin.tpl.php' );
        $list->assign( 'epcClassList' , $epcListToDisplay );
        
        $toolTitle->addCommand( get_lang('Import student list from EPC'), Url::Contextualize ( get_module_url ( 'ICEPC' ) . 'admin.php?cmd=rqImport' ), 'class' );
        
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
    elseif ( $cmd == 'exUnreg' )
    {
        $dialogBox = new DialogBox();
        
        $classId = $userInput->get ( 'classId' );
        $removeUsers = $userInput->get ( 'removeUsers', 'no' ) == 'yes' ? true : false;
        $disableUsers = $userInput->get ( 'disableUsers', 'no' ) == 'yes' ? true : false;
        
        if ( ! $classId )
        {
            throw new Exception("Missing class id");
        }
        
        $claroClass = new Claro_Class( Claroline::getDatabase() );
        $claroClass->load( $classId );
        
        $className = $claroClass->getName();
        
        $courseList = $claroClass->getClassCourseList()->useId( 'code' );
        
        $coursesUnreg = array();
        
        foreach ( $courseList as $courseId => $course )
        {
            $claroClass->unregisterFromCourse( $courseId );
            
            $coursesUnreg[] = "{$course['administrativeNumber']} <em>({$courseId})</em> - {$course['title']} - {$course['titulars']}";
        }
        
        $dialogBox->success( get_lang( 'Class unregistered from <ul><li>%courses</li></ul>', array('%courses' => implode("</li>\n<li>", $coursesUnreg ) ) ) );
        
        Console::info(get_lang( "Class {$className} {$classId}  unregistered from " . implode("\n", $coursesUnreg ) ) );
        
        if ( $disableUsers )
        {
            // disable users
            $dialogBox->success(get_lang("Class users disabled"));
        }
        elseif ( $removeUsers )
        {
            // delete users
            $dialogBox->success(get_lang("Class users unregistered"));
        }
        
        $dialogBox->info('<a href="'.Url::Contextualize(get_module_url('ICEPC')).'/admin.php">'.get_lang('Back').'</a>');
        
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
