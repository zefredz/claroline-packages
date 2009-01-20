<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * ICPRINT web service access point
 *
 * @version     1.9 $Revision$
 * @copyright   2001-2008 Universite catholique de Louvain (UCL)
 * @author      Claroline Team <info@claroline.net>
 * @author      Frederic Minne <zefredz@claroline.net>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE version 2 or later
 * @package     ICPRINT
 */
 
try
{
    // load Claroline kernel
    require dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';
    
    Fromkernel::uses('utils/input.lib','utils/validator.lib','utils/time.lib','language.lib');
    From::Module('CLKRNG')->uses('keyring.lib');
    
    require_once get_path('includePath') . '/lib/user.lib.php';
    require_once get_path('includePath') . '/lib/file.lib.php';
    
    require_once dirname(__FILE__) . '/lib/pdocrud/pdocrudclaro.lib.php';
    require_once dirname(__FILE__) . '/lib/pdocrud/pdocrud.lib.php';
    require_once dirname(__FILE__) . '/lib/pdocrud/pdofactory.lib.php';
    require_once dirname(__FILE__) . '/lib/pdocrud/pdosqlscript.lib.php';
    require_once dirname(__FILE__) . '/lib/pdocrud/pdomapper.lib.php';
    require_once dirname(__FILE__) . '/lib/pdocrud/pdomapperbuilder.lib.php';
    require_once dirname(__FILE__) . '/lib/pdocrud/pdomapperschema.lib.php';
    
    $userInput = Claro_userInput::getInstance();
        
    try
    {
        $serviceUser = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : null;
        $serviceKey = $userInput->getMandatory('serviceKey');
        
        $checked = false;
        
        if ( !empty( $serviceUser ) )
        {
            $checked = Keyring::checkKeyForHost( 'icprint', $serviceUser, $serviceKey );
        }
    
        if ( ! $checked )
        {
            header( 'Forbidden', true, 403 );
            echo '<h1>Forbidden !</h1>';
            echo '<p>Worng service key or host</p>';
            if ( claro_debug_mode() )
            {
                var_dump( $serviceUser.'::'.$serviceKey );
            }
            exit();
        }
    }
    catch ( Exception $e )
    {
        header( 'Forbidden', true, 403 );
        echo '<h1>Forbidden !</h1>';
        
        if ( claro_debug_mode() )
        {
            echo '<pre>'.$e->__toString().'</pre>';
        }
        else
        {
            echo '<p>An exception occurs !</p>';
        }        
        
        exit();
    }
    
    define ( 'APP_PATH', dirname(__FILE__).'/crud' );
    !defined( 'CLARO_DSN' ) && define ( 'CLARO_DSN', 'mysql://'.get_conf('dbLogin')
        .':'.get_conf('dbPass').'@'.get_conf('dbHost').'/'
        .get_conf('mainDbName') );
 
    date_default_timezone_set('Europe/Brussels');
    
    // Init database :
    PDOCrud::init( CLARO_DSN );
    $mapperBuilder = PDOCrud::getBuilder();
    
    // Init data objects :
    require_once APP_PATH . '/classes/document.class.php';
    require_once APP_PATH . '/classes/action.class.php';
    
    // Init schemas
    $mapperBuilder->register( PDOMapperSchema::fromFile(APP_PATH.'/schemas/document.xml'));
    $mapperBuilder->register( PDOMapperSchema::fromFile(APP_PATH.'/schemas/action.xml'));
    
    // Get mappers
    $documentMapper = $mapperBuilder->getMapper('PrintServiceDocument');
    $actionMapper = $mapperBuilder->getMapper('PrintServiceAction');
    
    // Process user commands
    
    $allowedCommandList = array ('list','get');
    $userInput->setValidator('cmd',new Claro_Validator_AllowedList($allowedCommandList));
    
    try
    {
        $cmd = $userInput->get('cmd','list');
    }
    catch(Exception $e)
    {
        die ('invalid action');
    }
    
    // get list
    if ( 'list' == $cmd )
    {
        $fromDate = $userInput->get( 'fromDate' );
        
        if ( $fromDate )
        {
            if ( Claro_Utils_Time::isIso8601( $fromDate ) )
             {
                $fromDate = Claro_Utils_Time::iso8601ToDatetime( $fromDate );
             }
             
            $documents = $actionMapper->selectAll(
                $actionMapper->getSchema()->getField( 'timestamp' ) . ' > :fromDate',
                array( ':fromDate' => Claro_Utils_Time::dateToDatetime( $fromDate ) )
            );
        }
        else
        {
            $documents = $actionMapper->selectAll();
        }
        
        header("Content-type: text/xml; charset=utf-8");
        $tpl = new PhpTemplate(dirname(__FILE__) . '/templates/list.xml.php');   
        $tpl->assign( 'documents', $documents ); 
        $tpl->assign( 'actionMapper', $actionMapper );
        $tpl->assign( 'serviceKey', $serviceKey );
        echo claro_utf8_encode( $tpl->render() );
    }
    
    // download document
    if ( 'get' == $cmd )
    {
        $id = $userInput->getMandatory( 'id' );
        $doc = $documentMapper->selectOne(
            $documentMapper->getSchema()->getField( 'id' ) . ' = :id'
            . ' AND ' . $documentMapper->getSchema()->getField( 'courseId' ) . ' = :courseId',
            array( ':id' => $id, ':courseId' => claro_get_current_course_id() )
        );
        
        if ( $doc && file_exists( $doc->globalPath ) )
        {
            $action = $documentMapper->hasOne($doc,'action');
            if($action)
            {
                if('delete' != $action->action)
                {
                    claro_send_file( $doc->globalPath );
                }
                else
                {
                    header( 'Gone', true, 410 );
                    echo '<h1>Gone !</h1>';
                    exit();
                }
            }
            else
            {
                header( 'Gone', true, 410 );
                echo '<h1>Gone !</h1>';
                exit();
            }
        }
        else
        {
            header( 'Not found', true, 404 ); 
            echo '<h1>Not found !</h1>';
            exit();
        }
    }
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
