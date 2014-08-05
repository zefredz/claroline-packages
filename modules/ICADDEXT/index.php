<?php // $Id$
/**
 * Tool for adding external accounts
 *
 * @version     ICADDEXT $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICADDEXT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

$tlabelReq = 'ICADDEXT';

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

FromKernel::uses(
    'user.lib',
    'utils/input.lib',
    'utils/validator.lib',
    'display/layout.lib',
    'fileUpload.lib',
    'users/claroclass.lib' );

From::Module( 'ICADDEXT' )->uses(
    'importer.class',
    'controller.class',
    'view.class',
    'thirdparty/parseCsv.class' );

set_current_module_label( 'ICADDEXT' );
load_module_config( 'ICADDEXT' );
load_module_language( 'ICADDEXT' );

$dialogBox = new DialogBox();
CssLoader::getInstance()->load( 'icaddext' , 'screen' );

try
{
    $actionList = array( 'submit'
                       , 'rqFix'
                       , 'exFix'
                       , 'rqAdd'
                       , 'exAdd' );
    $userInput = Claro_UserInput::getInstance();
    $userInput->setValidator( 'cmd' , new Claro_Validator_AllowedList( $actionList ) );
    $cmd = $userInput->get( 'cmd' , 'submit' );
    
    $csvParser = new ParseCsv();
    $importer = new ICADDEXT_Importer( $csvParser );
    $controller = new ICADDEXT_Controller( $importer , $userInput );
    $controller->execute( $cmd );
    $view = new ICADDEXT_View( $controller );
    
    foreach( $controller->message as $msg )
    {
        $dialogBox->{$msg[ 'type' ]}( get_lang( $msg[ 'text' ] ) );
    }
    
    Claroline::getInstance()->display->body->appendContent( $dialogBox->render() . $view->render() );
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
    
    $dialogBox->error( '<strong>' . get_lang( 'Error' ) . ' : </strong>' . $errorMsg );
    Claroline::getInstance()->display->body->appendContent( $dialogBox->render() );
}

echo Claroline::getInstance()->display->render();