<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.0.2 $Revision$ - Claroline 1.11
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICSUBSCR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

$tlabelReq = 'ICSUBSCR';

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

FromKernel::uses(
    'utils/input.lib',
    'utils/validator.lib',
    'display/layout.lib' );

From::Module( 'ICSUBSCR' )->uses(
    'controller.lib',
    'defaultcontroller.lib',
    'view.lib',
    'defaultview.lib',
    'lister.lib',
    'pluginloader.lib',
    'plugincontroller.lib',
    'pluginview.lib',
    'record.lib',
    'session.lib',
    'sessionlist.lib',
    'dateutil.lib' );

CssLoader::getInstance()->load( 'kalendae' , 'screen' );
JavascriptLoader::getInstance()->load('kalendae');

$dialogBox = new DialogBox();

$userId = claro_get_current_user_id();
$courseId = claro_get_current_course_id();
$groupId = claro_get_current_group_id();

if( ! claro_is_in_a_course() || ! claro_is_course_allowed() || ( $groupId && ! claro_is_group_allowed() ) )
{
    $dialogBox->error( 'Not allowed' );
    $output = $dialogBox->render();
}
else
{
    $courseData = claro_get_current_course_data();
    $lang = $courseData[ 'language' ];
    
    $pluginRepository = get_module_path( 'ICSUBSCR' ) . '/plugins/';
    $pluginLoader = new PluginLoader( $pluginRepository , $lang );
    
    $userInput = Claro_UserInput::getInstance();
    
    $cmd = $userInput->get( 'cmd' );
    $sessionId = $userInput->get( 'sessionId' );
    $sessionType = $userInput->get( 'sessionType' );
    $data = $userInput->get( 'data' );
    
    $controller = $sessionType && $pluginLoader->pluginExists( $sessionType )
        ? $pluginLoader->get( $sessionType , new Session( $sessionId ) , claro_is_allowed_to_edit() )
        : new DefaultController( new SessionList( $pluginLoader->getPluginList() , $groupId ? 'group' : 'user' , claro_is_allowed_to_edit() )
            , $sessionId
            , claro_is_allowed_to_edit() );
    
    $controller->execute( $cmd , $data );
    $output = $controller->output();
}

CssLoader::getInstance()->load( 'main' , 'screen' );
Claroline::getInstance()->display->body->appendContent( $output );

echo Claroline::getInstance()->display->render();