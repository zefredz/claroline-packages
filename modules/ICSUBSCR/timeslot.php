<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.1 $Revision$ - Claroline 1.11
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
    'list.lib',
    'sessionlist.lib',
    'hidable.lib',
    'session.lib',
    'slot.lib',
    'subscription.lib',
    'result.lib',
    'dateutil.lib',
    'message.lib' );

$message = new Message();

try
{
    ////////////////
    // CONTROLLER //
    ////////////////
    
    $userInput = Claro_UserInput::getInstance();
    $step = $userInput->get( 'step' , '0' );
    $sessionId = $userInput->get( 'sessionId' );
    $data = $userInput->get( 'data' );
    
    $dateUtil = new DateUtil( get_lang( '_date' ) );
    $session = new Session( $sessionId );
    
    $template = 'timeslot' . $step;
    
    switch( $step )
    {
        case '0':
            break;
        
        case '1':
            break;
        
        case '2':
            break;
        default:
        {
            throw new Exception( 'bad command' );
        }
    }
    
    //////////
    // VIEW //
    //////////
    
    CssLoader::getInstance()->load( 'kalendae' , 'screen' );
    CssLoader::getInstance()->load( 'main' , 'screen' );
    
    JavascriptLoader::getInstance()->load('kalendae');
    JavascriptLoader::getInstance()->load('dateutil');
    
    $pageTitle = array( 'mainTitle' => get_lang( 'Subscriptions' )
                    , 'subTitle' => get_lang( 'Creating new slots' ) );
    $cmdList = array();
    
    $cmdList[] = array( 'img'  => 'back',
        'name' => get_lang( 'Back' ),
        'url'  => htmlspecialchars( Url::Contextualize( get_module_url( $tlabelReq )
                .'/index.php?cmd=rqModifySession&sessionId=' . $session->getId() ) ) );
    
    $view = new ModuleTemplate( $tlabelReq , 'timeslot' . $step . '.tpl.php' );
    $view->assign( 'data' , $data );
    
    Claroline::getInstance()->display->body->appendContent(
        claro_html_tool_title( $pageTitle , null , $cmdList )
        . $message->render()
        . $view->render() );
}
catch( Exception $e )
{
    if ( claro_debug_mode() )
    {
        $errorMsg = '<pre>' . $e->__toString() . '</pre>';
    }
    else
    {
        $errorMsg = $e->getMessage();
    }
    
    $message->error( '<strong>' . get_lang( 'Error' ) . ' : </strong>' . $errorMsg );
    Claroline::getInstance()->display->body->appendContent( $message->render() );
}

echo Claroline::getInstance()->display->render();