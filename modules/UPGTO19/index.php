<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
* Upgrade to 1.9 stable
*
* @version     1.8-backport $Revision$
* @copyright   2001-2007 Universite catholique de Louvain (UCL)
* @author      Frederic Minne <zefredz@claroline.net>
* @license     http://www.gnu.org/copyleft/gpl.html
*              GNU GENERAL PUBLIC LICENSE version 2 or later
* @package     icprint
*/

//Tool label
$tlabelReq = 'UPGTO19';

//Load claroline kernel
require_once dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';

$nameTools = get_lang('Upgrade to 1.9 stable');

if ( ! claro_is_platform_admin() )
{
    claro_die( get_lang('Not allowed !') );
}

require_once dirname(__FILE__) . '/lib/upgrade.lib.php';
require_once dirname(__FILE__) . '/lib/upgradetasks.lib.php';
require_once dirname(__FILE__) . '/lib/registry.lib.php';

$dialogBox = new DialogBox();

// REQUEST VARIABLES INIT


$cmd = isset($_REQUEST['cmd']) ? $_REQUEST['cmd'] : null;
$cid = isset($_REQUEST['cid']) ? $_REQUEST['cid'] : null;

$dispMainScreen = true;
$dispCourseList = false;

// END OF REQUEST VARIABLES INIT

// DATABASE INITIALISATION

if ( $cmd == 'resetUpgradeDatabase' )
{
    $resetUpgradeDatabase = true;
}
else
{
    $resetUpgradeDatabase = false;
}


if ( $resetUpgradeDatabase || ! PersistantVariableStorage::module('UPGTO19')->get('upgrade.course.databaseInitialized',false) )
{
    try
    {
        Upgrade_CourseDatabase::init($resetUpgradeDatabase);
        PersistantVariableStorage::module('UPGTO19')->set('upgrade.course.databaseInitialized',true);
        $dialogBox->success(get_lang("Course upgrade database initialized"));
    }
    catch ( Exception $e )
    {
        $dialogBox->error(get_lang("Cannot initialized course upgrade database (see log for details)"));
        Console::error($e->__toString());
    }
}

// END OF DATABASE INITIALISATION

// START OF AJAX REQUESTS HANDLERS

if ( $cmd == 'setAutoUpgrade' )
{
    if ( isset($_REQUEST['auto']) )
    {
        PersistantVariableStorage::module('UPGTO19')->set('upgrade.course.auto', $_REQUEST['auto'] == 'true');
        
        echo 'Auto upgrade set to ' . $_REQUEST['auto'];
    }
    
    die();
}

// END OF AJAX REQUESTS HANDLERS

// UPGRADE REQUESTS HANDLERS

if ( $cmd == 'upgradeCourseBatch' )
{
    try
    {
        $executionResult = Upgrade_Course::upgradeNextBunchOfCourses();
        
        if ( count( $executionResult['success'] ) == 0
            && count( $executionResult['partial'] ) == 0
            &&  count( $executionResult['failure'] ) == 0 )
        {
            $dialogBox->info( get_lang("No course upgraded") );
        }
        else
        {
            if ( count( $executionResult['success'] ) )
            {
                $dialogBox->success(
                    get_lang("%nbrSuccess courses upgraded with success : %courseList",
                        array(
                            '%nbrSuccess'=>count($executionResult['success']),
                            '%courseList'=>implode(',', array_keys($executionResult['success']))
                        )
                    )
                );
                
                Console::success( "UPGTO19::Upgrade successful for "
                    . count($executionResult['success']). " courses : "
                    . implode(',', array_keys($executionResult['success']) )
                );
            }
            
            if ( count( $executionResult['partial'] ) )
            {
                $dialogBox->warning(
                    get_lang("%nbrPartial courses partialy upgraded : %courseList",
                        array(
                            '%nbrPartial'=>count($executionResult['partial']),
                            '%courseList'=>implode(',', array_keys($executionResult['partial']))
                        )
                    )
                );
                
                Console::success( "UPGTO19::Upgrade partial for "
                    . count($executionResult['partial']). " courses : "
                    . implode(',', array_keys($executionResult['partial']) )
                );
            }
            
            if ( count( $executionResult['failure'] ) )
            {
                $dialogBox->error(
                    get_lang("Upgrade's failed for %nbrError courses : %courseList",
                        array(
                            '%nbrError'=>count($executionResult['failure']),
                            '%courseList'=>implode(',', array_keys($executionResult['failure']))
                        )
                    )
                );
                
                Console::success( "UPGTO19::Upgrade fails for "
                    . count($executionResult['failure']). " courses : "
                    . implode(',', array_keys($executionResult['failure']) )
                );
            }
        }
    }
    catch (Exception $e )
    {
        Console::error( "UPGTO19::Exception : {$e->getMessage()}" );
        $dialogBox->error( get_lang("An error occurs while running the course upgrade tasks (see log for details)") );
    }
}
elseif ( $cmd == 'upgradeCourse' )
{
    if ( is_null ( $cid ) )
    {
        $dialogBox->error(get_lang("Missing course code"));
    }
    else
    {
        try
        {
            $course = Upgrade_CourseDatabase::getCourse( $cid );
            
            if ( $course['status'] == 'pending' )
            {
                $errorSteps = Upgrade_Course::execute( $course );
                    
                if ( ! count( $errorSteps ) )
                {
                    $dialogBox->success(get_lang("Course upgrade executed with success for course %cid", array('%cid' => htmlspecialchars( $cid ) )));
                    Console::success( "UPGTO19::Upgrade successful for {$cid}" );
                }
                else
                {
                    $dialogBox->warning(get_lang("Course upgrade executed with errors for course %cid at step %steps", array('%cid' => htmlspecialchars( $cid ), '%steps' => implode(',',$errorSteps) )));
                    Console::warning( "UPGTO19::Upgrade failed for ".claro_get_current_course_id() . " at steps " . implode( ',', $errorSteps ) );
                }
            }
            else
            {
                $dialogBox->info(get_lang("Course %cid already upgraded with status %status",array('%cid'=>htmlspecialchars($cid),'%status'=>$course['status'])));
                pushClaroMessage( "UPGTO19::Upgrade already done for {$cid} with status " . $course['status'], 'info' );
            }
        }
        catch (Exception $e )
        {
            Console::error( "UPGTO19::Exception in {$cid} : {$e->getMessage()}" );
            $dialogBox->error( get_lang("An error occurs while running the course upgrade tasks for course %cid (see log for details)", array('%cid' => htmlspecialchars( $cid ))) );
        }
    }
}
elseif ( $cmd == 'executeMainUpgrade' )
{
    if ( PersistantVariableStorage::module('UPGTO19')->get('upgrade.main.done',false) )
    {
        $dialogBox->error(get_lang("Main upgrade already done"));
    }
    else
    {
        try
        {
            $mainUpgradeTasks = new Upgrade_TaskQueue();
            
            require_once dirname(__FILE__) . '/tasks/main.task.php';
            
            $failedSteps = $mainUpgradeTasks->execute();
            
            if ( count( $failedSteps))
            {
                $dialogBox->warning(
                    get_lang(
                        "The following steps failed : %steps",
                        array( '%steps' => implode( ',', $failedSteps ) ) ) );
            }
            else
            {
                $dialogBox->success(get_lang("Main upgrade executed with success"));
                
                PersistantVariableStorage::module('UPGTO19')->set('upgrade.main.done',true);
            }
        }
        catch ( Exception $e )
        {
            $dialogBox->error( get_lang("An error occurs while running the main upgrade tasks (see log for details)") );
            Console::error($e->__toString());
        }
    }
}
elseif ( $cmd == 'showSuccess' )
{
    $dispMainScreen = false;
    $dispCourseList = true;
    $status = 'success';
    $title = get_lang('Courses upgraded with success');
}
elseif ( $cmd == 'showPending' )
{
    $dispMainScreen = false;
    $dispCourseList = true;
    $status = 'pending';
    $title = get_lang('Courses waiting for upgrade');
}
elseif ( $cmd == 'showFailure' )
{
    $dispMainScreen = false;
    $dispCourseList = true;
    $status = 'failure';
    $title = get_lang('Courses for which the upgrade process failed');
}
elseif ( $cmd == 'showPartial' )
{
    $dispMainScreen = false;
    $dispCourseList = true;
    $status = 'partial';
    $title = get_lang('Courses for which the upgrade process ends with some errors');
}
elseif ( $cmd == 'showStarted' )
{
    $dispMainScreen = false;
    $dispCourseList = true;
    $status = 'started';
    $title = get_lang('Courses currently upgrading');
}

// END OF UPGRADE REQUESTS HANDLERS

ClaroBreadCrumbs::getInstance()->prepend( get_lang( 'Administration' ), get_path('url').'/claroline/admin/index.php' );

Claroline::getDisplay()->body->appendContent( claro_html_tool_title($nameTools) );
Claroline::getDisplay()->body->appendContent( $dialogBox->render() );

if ( true == $dispMainScreen )
{
    ClaroBreadCrumbs::getInstance()->setCurrent( $nameTools, php_self() );

    $template = new PhpTemplate( dirname(__FILE__) . '/templates/main.tpl.php' );
    
    $template->assign('autoUpgrade', PersistantVariableStorage::module('UPGTO19')->get('upgrade.course.auto',false) );
    $template->assign('mainupgradeDone', PersistantVariableStorage::module('UPGTO19')->get('upgrade.main.done',false) );
    $template->assign('totalNumberOfCourses', Upgrade_CourseDatabase::countCourses() );
    $template->assign('successCount', Upgrade_CourseDatabase::countCoursesByStatus('success') );
    $template->assign('partialCount', Upgrade_CourseDatabase::countCoursesByStatus('partial'));
    $template->assign('failureCount', Upgrade_CourseDatabase::countCoursesByStatus('failure') );
    $template->assign('startedCount', Upgrade_CourseDatabase::countCoursesByStatus('started') );
    $template->assign('pendingCount', Upgrade_CourseDatabase::countCoursesByStatus('pending') );
    
    Claroline::getDisplay()->body->appendContent( $template->render() );

}
elseif ( true == $dispCourseList )
{
    ClaroBreadCrumbs::getInstance()->setCurrent( $nameTools, php_self() );
    
    $template = new PhpTemplate( dirname(__FILE__) . '/templates/courselist.tpl.php' );
    $template->assign('title', $title);
    $template->assign('courseList', Upgrade_CourseDatabase::getCoursesByStatus($status));
    
    Claroline::getDisplay()->body->appendContent( $template->render() );
}

echo Claroline::getDisplay()->render();
