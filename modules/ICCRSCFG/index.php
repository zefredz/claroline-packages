<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * Description
 *
 * @version     1.1 $Revision$
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @author      Claroline Team <info@claroline.net>
 * @author      Frederic Minne <zefredz@claroline.net>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE version 2 or later
 * @package     ICMAIL
 */

try
{
    $cidReq = null; $cidReset = true;
    $gidReq = null; $gidReset = true;
    // load Claroline kernel
    require dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';
    
    if ( ! claro_is_platform_admin() )
    {
        claro_die('Not allowed');
    }
    
    FromKernel::uses( 'utils/input.lib', 'utils/validator.lib' );
    require_once dirname(__FILE__) . '/lib/iccrscfg.lib.php';
    
    $userInput = Claro_UserInput::getInstance();
    $dialogBox = new DialogBox();
    
    ClaroBreadCrumbs::getInstance()->prepend( get_lang('Administration'), get_path('rootAdminWeb') );
    ClaroBreadCrumbs::getInstance()->append( get_lang('Course configuration editor'), $_SERVER['PHP_SELF'] );
    
    $action = $userInput->get('cmd','rqLoadCourse');
    
    Claroline::getDisplay()->body->appendContent( claro_html_tool_title(get_lang("Course configuration editor")) );
    
    if ( $action == 'loadCourse' )
    {
        $courseId = $userInput->getMandatory('cid');
        
        $course = claro_get_course_data( $courseId );
        
        if ( ! $course )
        {
            throw new Exception ("Course not found {$courseId}");
        }
        else
        {
            $configObj = new ICCRSCFG_Configuration( $courseId );
            
            $tpl = new PhpTemplate(dirname(__FILE__).'/templates/courseconf.tpl.php');
            $tpl->assign('courseId', $courseId);
            $tpl->assign('courseConfig', $configObj->getCourseConfiguration());
            $tpl->assign('platformConfig', $configObj->getPlatformConfiguration());
            $tpl->assign('config', $configObj->getCourseEffectiveConfiguration());
            
            Claroline::getDisplay()->body->appendContent( $tpl->render() );
        }
    }
    elseif ( $action == 'changeConf' )
    {
        $courseId = $userInput->getMandatory('cid');
        
        $course = claro_get_course_data( $courseId );
        
        if ( ! $course )
        {
            throw new Exception ("Course not found {$courseId}");
        }
        else
        {
            $configObj = new ICCRSCFG_Configuration( $courseId );
            $platformConfig = $configObj->getPlatformConfiguration();
            
            $configurationValuesSubmitted = array();
            $configurationValuesSubmitted['maxFilledSpace_for_course'] = $userInput->getMandatory('maxFilledSpace_for_course');
            $configurationValuesSubmitted['maxFilledSpace_for_groups'] = $userInput->getMandatory('maxFilledSpace_for_groups');
            $configurationValuesSubmitted['openNewWindowForDoc'] = $userInput->getMandatory('openNewWindowForDoc') == 'true' ? true : false;
            $configurationValuesSubmitted['max_file_size_per_works'] = $userInput->getMandatory('max_file_size_per_works');
            $configurationValuesSubmitted['maxFilledSpace'] = $userInput->getMandatory('maxFilledSpace');
            
            // Claroline::getDisplay()->body->appendContent( '<pre>'.var_export($configurationValuesSubmitted,true).'</pre>' );
            
            $configurationResetSubmitted = array();
            $configurationResetSubmitted['reset_maxFilledSpace_for_course'] = $userInput->get('reset_maxFilledSpace_for_course') == 'on' ? true : false;
            $configurationResetSubmitted['reset_maxFilledSpace_for_groups'] = $userInput->get('reset_maxFilledSpace_for_groups') == 'on' ? true : false;
            $configurationResetSubmitted['reset_openNewWindowForDoc'] = $userInput->get('reset_openNewWindowForDoc') == 'on' ? true : false;
            $configurationResetSubmitted['reset_max_file_size_per_works'] = $userInput->get('reset_max_file_size_per_works') == 'on' ? true : false;
            $configurationResetSubmitted['reset_maxFilledSpace'] = $userInput->get('reset_maxFilledSpace') == 'on' ? true : false;
            
            // Claroline::getDisplay()->body->appendContent( '<pre>'.var_export($configurationResetSubmitted,true).'</pre>' );
            
            $configurationToWrite = array(
                'CLDOC' => array(
                    'maxFilledSpace_for_course' => $platformConfig['maxFilledSpace_for_course'],
                    'maxFilledSpace_for_groups' => $platformConfig['maxFilledSpace_for_groups'],
                    'openNewWindowForDoc' => $platformConfig['openNewWindowForDoc']
                ),
                'CLWRK' => array(
                    'max_file_size_per_works' => $platformConfig['max_file_size_per_works'],
                    'maxFilledSpace' => $platformConfig['maxFilledSpace']
                ),
            );
            
            // CLDOC
            if ( ! $configurationResetSubmitted['reset_maxFilledSpace_for_course']
                && $configurationValuesSubmitted['maxFilledSpace_for_course'] != $platformConfig['maxFilledSpace_for_course']
            )
            {
                $configurationToWrite['CLDOC']['maxFilledSpace_for_course'] = $configurationValuesSubmitted['maxFilledSpace_for_course'];
            }
            elseif ( $configurationResetSubmitted['reset_maxFilledSpace_for_course']
                || $configurationValuesSubmitted['maxFilledSpace_for_course'] == $platformConfig['maxFilledSpace_for_course'] )
            {
                unset( $configurationToWrite['CLDOC']['maxFilledSpace_for_course'] );
            }
            
            if ( ! $configurationResetSubmitted['reset_maxFilledSpace_for_groups']
                && $configurationValuesSubmitted['maxFilledSpace_for_groups'] != $platformConfig['maxFilledSpace_for_groups']
            )
            {
                $configurationToWrite['CLDOC']['maxFilledSpace_for_groups'] = $configurationValuesSubmitted['maxFilledSpace_for_groups'];
            }
            elseif ( $configurationResetSubmitted['reset_maxFilledSpace_for_groups'] 
                || $configurationValuesSubmitted['maxFilledSpace_for_groups'] == $platformConfig['maxFilledSpace_for_groups'] )
            {
                unset( $configurationToWrite['CLDOC']['maxFilledSpace_for_groups'] );
            }
            
            if ( ! $configurationResetSubmitted['reset_openNewWindowForDoc']
                && $configurationValuesSubmitted['openNewWindowForDoc'] != $platformConfig['openNewWindowForDoc']
            )
            {
                $configurationToWrite['CLDOC']['openNewWindowForDoc'] = $configurationValuesSubmitted['openNewWindowForDoc'];
            }
            elseif ( $configurationResetSubmitted['reset_openNewWindowForDoc'] 
                || $configurationValuesSubmitted['openNewWindowForDoc'] == $platformConfig['openNewWindowForDoc'] )
            {
                unset( $configurationToWrite['CLDOC']['openNewWindowForDoc'] );
            }
            
            // CLWRK
            if ( ! $configurationResetSubmitted['reset_max_file_size_per_works']
                && $configurationValuesSubmitted['max_file_size_per_works'] != $platformConfig['max_file_size_per_works']
            )
            {
                $configurationToWrite['CLWRK']['max_file_size_per_works'] = $configurationValuesSubmitted['max_file_size_per_works'];
            }
            elseif ( $configurationResetSubmitted['reset_max_file_size_per_works']
                || $configurationValuesSubmitted['max_file_size_per_works'] == $platformConfig['max_file_size_per_works'] )
            {
                unset( $configurationToWrite['CLWRK']['max_file_size_per_works'] );
            }
            
            if ( ! $configurationResetSubmitted['reset_maxFilledSpace']
                && $configurationValuesSubmitted['maxFilledSpace'] != $platformConfig['maxFilledSpace']
            )
            {
                $configurationToWrite['CLWRK']['maxFilledSpace'] = $configurationValuesSubmitted['maxFilledSpace'];
            }
            elseif ( $configurationResetSubmitted['reset_maxFilledSpace'] 
                || $configurationValuesSubmitted['maxFilledSpace'] == $platformConfig['maxFilledSpace'] )
            {
                unset( $configurationToWrite['CLWRK']['maxFilledSpace'] );
            }
            
            // Claroline::getDisplay()->body->appendContent( '<pre>'.var_export($configurationToWrite,true).'</pre>' );
            
            $configObj->writeConfig( $courseId, $configurationToWrite );
            
            $dialogBox->success(get_lang("Configuration changed"));
            
            //Reload
            $configObj = new ICCRSCFG_Configuration( $courseId );
            
            $tpl = new PhpTemplate(dirname(__FILE__).'/templates/courseconf.tpl.php');
            $tpl->assign('courseId', $courseId);
            $tpl->assign('courseConfig', $configObj->getCourseConfiguration());
            $tpl->assign('platformConfig', $configObj->getPlatformConfiguration());
            $tpl->assign('config', $configObj->getCourseEffectiveConfiguration());
            
            Claroline::getDisplay()->body->appendContent( $tpl->render() );
        }
    }
    else
    {
        // load course form
        
        $tpl = new PhpTemplate(dirname(__FILE__).'/templates/loadcourse.tpl.php');
        
        Claroline::getDisplay()->body->appendContent( $tpl->render() );
    }
    
    echo Claroline::getInstance()->display->render();
}
catch ( Exception $e )
{
    if ( claro_debug_mode() )
    {
        claro_die( '<pre>' . $e->__toString() . '</pre>' );
    }
    else
    {
        claro_die( $e->getMessage() );
    }
}
