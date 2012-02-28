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
    'utils/input.lib',
    'utils/validator.lib',
    'display/layout.lib',
    'fileUpload.lib' );

From::Module( 'ICADDEXT' )->uses(
    'importer.class',
    'controller.class',
    'view.class',
    'thirdparty/parseCsv.class' );

$claroline->currentModuleLabel( 'ICADDEXT' );
load_module_config( 'ICADDEXT' );
load_module_language( 'ICADDEXT' );

$dialogBox = new DialogBox();
CssLoader::getInstance()->load( 'icaddext' , 'screen' );

try
{
    $actionList = array( 'rqAdd'
                       , 'rqSelect'
                       , 'exAdd' );
    $userInput = Claro_UserInput::getInstance();
    $userInput->setValidator( 'cmd' , new Claro_Validator_AllowedList( $actionList ) );
    $cmd = $userInput->get( 'cmd' , 'rqAdd' );
    
    $csvParser = new ParseCsv();
    $importer = new ICADDEXT_Importer( $csvParser );
    $controller = new ICADDEXT_Controller( $importer , $userInput );
    $view = new ICADDEXT_View( $cmd , $controller );
    
    if ( $msg = $controller->message )
    {
        $dialogBox->{$msg[ 'type' ]}( $msg[ 'text' ] );
    }
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

Claroline::getInstance()->display->body->appendContent( $dialogBox->render() . $view->render() );
echo Claroline::getInstance()->display->render();