<?php // $Id$

/**
 * Claroline Advanced Link Tool
 *
 * @version     CLLKTOOL 1.0-alpha $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLKTOOL
 * @author      Frederic Minne <frederic.minne@uclouvain.be>
 */

$tlabelReq = 'CLLKTOOL';

require dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

FromKernel::uses('utils/input.lib','utils/validator.lib');
From::Module('CLLKTOOL')->uses('linkcollection.lib','layout.lib.php');

$dialogBox = new DialogBox;

try
{
    
    $collection = LinkCollection::getInstance();
    
    $userInput = Claro_UserInput::getInstance();
    
    $userInput->setValidator('cmd', new Claro_Validator_AllowedList( array(
        'list', 'visit',
        'rqAddLink', 'exAddLink',
        'rqEditLink', 'exEditLink',
        'rqDeleteLink', 'exDeleteLink'
    ) ) );
    
    $userInput->setValidator('type', new Claro_Validator_AllowedList(array(
        'post:json','post:xml','post:plain','widget','iframe','popup'
    )));
    
    $cmd = $userInput->get( 'cmd','list' );
    
    // get data
    
    switch ( $cmd )
    {
        case 'list':
            break;
        case 'visit':
        case 'rqEditLink':
        case 'rqDeleteLink':
                $id = $userInput->getMandatory( 'linkId' );
                $link = $collection->get( $id );
                
                if ( ! $link )
                {
                    throw new Exception('Link not found');
                }
                
                $id = (int) $link['id'];
                $url = $link['url'];
                $title = $link['title'];
                $options = unserialize( $link['options'] );
                $type = $link['type'];
            break;
        case 'rqAddLink':
            break;
        case 'exEditLink':
        case 'exAddLink':
                if ( 'exEditLink' == $cmd )
                {
                    $id = $userInput->getMandatory( 'linkId' );
                }
                else
                {
                    $id = null;
                }
                
                $url = $userInput->getMandatory( 'linkUrl' );
                $title = $userInput->get( 'title', $url );
                $options = $userInput->get( 'options', array() );
                $type = $userInput->get( 'type', 'iframe' );
            break;
        case 'exDeleteLink':
                $id = $userInput->getMandatory( 'linkId' );
            break;
        default:
            throw new Exception('Unknown command');
    }
    
    $layout = new LeftMenuLayout;
    
    // prepare left menu for any cmd
    
    $linkList = '<ul>' . "\n";
    
    foreach ( $collection->getAll() as $currentLink )
    {
        $linkList .= '<li><a href="'
            . htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=visit&linkId='.(int)$currentLink['id'] ) )
            . '">'
            . htmlspecialchars($currentLink['title'])
            . '</a></li>' . "\n"
            ;
    }
    
    $linkList .= '</ul>' . "\n";
    
    $layout->appendToLeft( $linkList );
    
    // prepare right menu
    switch ( $cmd )
    {
        case 'list':
                $layout->appendToRight(get_lang('Choose a link to start'));
            break;
        case 'visit':
                $layout->appendToRight( $url );
            break;
        case 'rqEditLink':
            break;
        case 'rqAddLink':
            break;
        case 'rqDeleteLink':
            break;
        case 'exEditLink':
                $collection->update( $id, $url, $title, $type, $params );
            break;
        case 'exAddLink':
                $collection->add( $url, $title, $type, $params );
            break;
        case 'exDeleteLink':
                $collection->delete( $id );
            break;
        default:
            throw new Exception('Unknown command');
    }
    
    $layout->prependToRight( $dialogBox->render() );
    
    Claroline::getDisplay()->body->append( $layout->render() );
}
catch ( Exception $e )
{
    if ( claro_debug_mode() )
    {
        $dialogBox->error( '<pre>' . $e->__toString() . '</pre>' );
    }
    else
    {
        $dialogBox->error( $e->getMessage() );
    }
    
    Claroline::getDisplay()->body->append( $dialogBox->render() );
}
