<?php // $Id$

/**
 * Claroline polls duplication tool
 *
 * @version     1.0 $Revision$
 * @copyright   (c) 2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     EMSURVEY
 * @author      Antonin Bourguignon <antonin.bourguignon@claroline.net>
 *
 */

require_once dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';

//SECURITY CHECK

if ( ! claro_is_user_authenticated() ) claro_disp_auth_form();
if ( ! claro_is_platform_admin() ) claro_die(get_lang('Not allowed'));

FromKernel::uses('utils/input.lib','utils/validator.lib','user.lib');
From::Module('EMSURVEY')->uses('modulerenderer.lib', 'modulemanager.lib');

ClaroBreadCrumbs::getInstance()->prepend( get_lang('Administration'), get_path('rootAdminWeb') );
ClaroBreadCrumbs::getInstance()->setCurrent( get_lang( 'Survey duplication'), get_module_url('EMSURVEY') );

$dialogBox = new DialogBox();

try {
    $userInput = Claro_UserInput::getInstance();
    
    $userInput->setValidator(
        'cmd', 
        new Claro_Validator_AllowedList( 
            array(
                'selectSurvey',
                'selectCourse',
                'editProperties',
                'duplicate'
            )
        ) 
    );
    
    $cmd        = $userInput->get( 'cmd','selectSurvey' );
    $surveyId   = $userInput->get( 'surveyId' );
    $courseId   = $userInput->get( 'courseId' );
    
    $out = '';
    
    if ($cmd == 'selectSurvey')
    {
        $surveyList = ModuleManager::getSurveyList();
        
        $out .= ModuleRenderer::surveyList( $surveyList );
    }
    elseif ($cmd == 'selectCourse')
    {
        $survey = ModuleManager::getSurvey($surveyId);
        $courseList = ModuleManager::getCourseList();
        
        $out .= ModuleRenderer::courseList( $survey, $courseList );
    }
    elseif ($cmd == 'editProperties')
    {
        $selectedSurvey = ModuleManager::getSurvey($surveyId);
        $selectedCodeList = ModuleManager::handleSelectCourseForm($userInput);
        $selectedCourseList = ModuleManager::getSelectedCourseList($selectedCodeList);
        
        $out .= ModuleRenderer::editProperties( $selectedSurvey, $selectedCourseList );
    }
    elseif ($cmd == 'duplicate')
    {
        $newSurveyList = ModuleManager::handlePropertiesEdition($userInput);
        
        $nbOfSurveyCreated = ModuleManager::handleSurveyListDuplication($newSurveyList);
        
        if ($nbOfSurveyCreated > 0)
        {
            $dialogBox->success( get_lang("You have successfully duplicated 
                %nb survey(s)", array('%nb' => $nbOfSurveyCreated) ) );
        }
        else
        {
            $dialogBox->failure( get_lang("No survey duplicated.") );
        }
    }
}
catch(Exception $e )
{
    if ( claro_debug_mode() )
    {
        $dialogBox->error( '<pre>' . $e->__toString() . '</pre>' );
    }
    else
    {
        $dialogBox->error( $e->getMessage() );
    }
    
    Claroline::getDisplay()->body->appendcontent( $dialogBox->render() );
}

$claroline->display->body->appendContent($dialogBox->render());
$claroline->display->body->appendContent($out);

echo $claroline->display->render();

?>