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
        'utils/validator.lib',
        'connectors/adminuser.lib',
        'utils/stringbuffer.lib',
        'users/userlist.lib', 
        'users/claroclass.lib',
        'users/classutils.lib.php'
    );

    From::Module ( 'ICEPC' )->uses (
        'epc/helpers.lib',
        'epc/epc.lib',
        'epc/epcclasses.lib',
        'epc/epclog.lib'
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
    $breadcrumbs->prepend( get_lang( 'Modules' ), get_path('rootAdminWeb') . 'module/module_list.php' );
    $breadcrumbs->prepend( get_lang('Administration'), get_path('rootAdminWeb') );
    
    $toolTitle = new ToolTitle( get_lang('Manage student lists from EPC') );

    // prepare parameters
    
    if ( $cmd == 'rqImport' || $cmd == 'preview' || $cmd == 'exImport' )
    {
        $breadcrumbs->append( get_lang('Import students from EPC') );
        
        $epcSearchString = $userInput->get ( 'epcSearchString', '' );
        $epcAcadYear = $userInput->get ( 'epcAcadYear', epc_get_current_acad_year () );
        $epcSearchFor = $userInput->get ( 'epcSearchFor', 'course' );
        $epcLinkExistingStudentsToClass = $userInput->get ( 'epcLinkExistingStudentsToClass', 'yes' );
        $epcValidatePendingUsers = $userInput->get ( 'epcValidatePendingUsers', 'yes' );
    }
    elseif ( $cmd == 'exSync' )
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
    
    // business logic and prepare display
    
    if ( $cmd == 'dispUserList' )
    {
        $classId = $userInput->get ( 'classId' );
        
        if ( ! $classId )
        {
            throw new Exception("Missing class id");
        }
        
        $claroClass = new Claro_Class( Claroline::getDatabase() );
        $claroClass->load( $classId );
        
        $breadcrumbs->append($claroClass->getName ());
        $breadcrumbs->append( get_lang('List of students in Class') );
        
        $toolTitle->setSubTitle(get_lang('List of student in class %className', array('%className' => $claroClass->getName ())));
        $toolTitle->addCommand( get_lang('Delete'), Url::Contextualize ( get_module_url ( 'ICEPC' ) . '/admin.php?cmd=exUnreg&classId='.$classId ), 'delete', array( 'class' => 'checkClassDeletion' ) );
        $toolTitle->addCommand( get_lang('Update'), Url::Contextualize ( get_module_url ( 'ICEPC' ) . '/admin.php?cmd=exSync&classId='.$classId ), 'import', array('class' => 'warnTakesTime' ) );
        
        $classUserList = new Claro_ClassUserList( $claroClass, Claroline::getDatabase() );
        
        $epcUserDataCache = new EpcUserDataCache( Claroline::getDatabase () );
        $epcCachedUserData = $epcUserDataCache->getAllUsersCachedData( $classUserList->getClassUserIdList() );
        
        $classUserListIterator = $classUserList->getClassUserListIterator();
        $classUserListIterator->useId('user_id');
        
        $userListTemplate = new ModuleTemplate('ICEPC', 'epc_class_users_admin.tpl.php');
        $userListTemplate->assign( 'classUserList', $classUserListIterator );
        $userListTemplate->assign( 'epcUserData', $epcCachedUserData );
        
        Claroline::getDisplay()->body->appendContent( $userListTemplate->render() );
        
    }
    elseif ( $cmd == 'listEpcClasses' )
    {
        // FromKernel::uses('class.lib');
        $epcClassList = new EpcClassList();
        $epcListToDisplay = $epcClassList->getEpcClassList();
        
        var_dump($epcListToDisplay);
        
        $list = new ModuleTemplate ( 'ICEPC', 'epc_classlist_admin.tpl.php' );
        $list->assign( 'epcClassList' , $epcListToDisplay );
        
        $toolTitle->addCommand( get_lang('Import student list from EPC'), Url::Contextualize ( get_module_url ( 'ICEPC' ) . '/admin.php?cmd=rqImport' ), 'class' );
        
        Claroline::getDisplay ()->body->appendContent ( $list->render () );
    }
    elseif ( $cmd == 'dispCourseList' )
    {
        $classId = $userInput->get ( 'classId' );
        
        if ( ! $classId )
        {
            throw new Exception("Missing class id");
        }
        
        $claroClass = new Claro_Class( Claroline::getDatabase() );
        $claroClass->load( $classId );
        
        $breadcrumbs->append($claroClass->getName ());
        $breadcrumbs->append(get_lang('Course list'));
        
        $toolTitle->setSubTitle(get_lang('List of courses in which the class %className is registered', array('%className' => $claroClass->getName ())));
        
        $courseList = $claroClass->getClassCourseList();
        $courseList->useId('code');
        
        $courseListTemplate = new ModuleTemplate( 'ICEPC','epc_class_courselist.tpl.php' );
        
        $courseListTemplate->assign( 'unregFromCourseBaseUrl', get_module_url('ICEPC').'/admin.php?cmd=exUnregFromCourse&classId='.$classId );
        $courseListTemplate->assign( 'courseList', $courseList );
        
        Claroline::getDisplay()->body->appendContent( $courseListTemplate->render() );
    }
    elseif ( $cmd == 'exUnregFromCourse' )
    {
        $classId = $userInput->get ( 'classId' );
        
        if ( ! $classId )
        {
            throw new Exception(get_lang("Missing class id"));
        }
        
        $courseId = $userInput->get('courseId');
        
        if ( ! $courseId )
        {
            throw new Exception(get_lang("Missing course code"));
        }
        
        $claroClass = new Claro_Class( Claroline::getDatabase() );
        $claroClass->load( $classId );
        
        $claroClass->unregisterFromCourse( $courseId );
        
        $dialogBox = new DialogBox();
        
        $dialogBox->success( get_lang( 'Class unregistered from course' ) );
        
        Console::info(get_lang( "Class {$className} {$classId}  unregistered from " . $courseId ) );
        
        $dialogBox->info('<a href="'.Url::Contextualize(get_module_url('ICEPC')).'/admin.php?cmd=dispCourseList&classId='.$classId.'>'.get_lang('Back').'</a>');
        
        Claroline::getDisplay()->body->appendContent( $dialogBox->render() );
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
        EpcLog::getInstance()->syncError( $epcClassName, var_export ( $e->getMessage (), true ) );
    }*/
    
    $dialogBox = new DialogBox();
    
    $dialogBox->error($out->render ());
    
    echo $dialogBox->render();
}
