<?php // $Id$

/**
 * Claroline Advanced Link Tool
 *
 * @version     ICPCRDR 1.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICPCRDR
 * @author      Frederic Minne <frederic.minne@uclouvain.be>
 */

$tlabelReq = 'ICPCRDR';

require dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';


FromKernel::uses('utils/input.lib','utils/validator.lib','display/layout.lib');
From::Module('ICPCRDR')->uses('podcastcollection.lib','podcastparser.lib');

if ( !claro_is_in_a_course() || !claro_is_course_allowed() ) claro_disp_auth_form(true);

$dialogBox = new DialogBox;

try
{
    $collection = PodcastCollection::getInstance();
    
    $userInput = Claro_UserInput::getInstance();
    
    if( claro_is_allowed_to_edit() )
    {
        $userInput->setValidator('cmd', new Claro_Validator_AllowedList( array(
            'list', 'visit',
            'rqAddPodcast', 'exAddPodcast',
            'rqEditPodcast', 'exEditPodcast',
            'rqDeletePodcast', 'exDeletePodcast',
            'exMkVisible', 'exMkInvisible'
        ) ) );   
    }
    else
    {
       $userInput->setValidator('cmd', new Claro_Validator_AllowedList( array(
            'list', 'visit'
        ) ) );  
    }
    
    $cmd = $userInput->get( 'cmd','list' );
    
    // get data
    
    switch ( $cmd )
    {
        case 'list':
            break;
        case 'visit':
        case 'rqEditPodcast':
        case 'rqDeletePodcast':
        case 'exMkVisible':
        case 'exMkInvisible':
                $id = $userInput->getMandatory( 'podcastId' );
                
                $podcast = $collection->get( $id );
                
                if ( ! $podcast )
                {
                    throw new Exception('Podcast not found');
                }
                
                $id = (int) $podcast['id'];
                $url = $podcast['url'];
                $title = $podcast['title'];
                $visibility = $podcast['visibility'];
            break;
        case 'rqAddPodcast':
                $id = null;
                $url = '';
                $title = '';
                $visibility = 'visible';
            break;
        case 'exEditPodcast':
        case 'exAddPodcast':
                if ( 'exEditPodcast' == $cmd )
                {
                    $id = $userInput->getMandatory( 'podcastId' );
                }
                else
                {
                    $id = null;
                }
                
                $url = $userInput->getMandatory( 'url' );
                $title = $userInput->getMandatory( 'title' );
                $url = $userInput->getMandatory( 'url' );
                $visibility = $userInput->get ( 'visibility', 'visible' );
            break;
        case 'exDeletePodcast':
                $id = $userInput->getMandatory( 'podcastId' );
            break;
        default:
            throw new Exception('Unknown command');
    }
    
    $layout = new LeftMenuLayout;
    
    // prepare right menu
    switch ( $cmd )
    {
        case 'list':
                $dialogBox->info(get_lang('Choose a podcast in the list to start'));
            break;
        case 'visit':
                if( !($visibility == 'visible' || claro_is_allowed_to_edit()) )
                {
                    $dialogBox->error( get_lang('Not allowed') );
                    break;
                }
                
                $parser = new PodcastParser();
                $parser->parseFeed( $url );
                
                $videoList = new PhpTemplate(dirname(__FILE__) . '/templates/podcastdisplay.tpl.php');
                $videoList->assign( 'channel', $parser->getChannelInfo() );
                $videoList->assign( 'items', $parser->getItems() );
                $videoList->assign( 'url', $url );
                // $videoList->assign( 'title', $title );
                
                $layout->appendToRight( $videoList->render() );
                
            break;
        case 'rqEditPodcast':
            {
                
                $formUrl = htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exEditPodcast' ) );
                
                // get template
                $form = new PhpTemplate(dirname(__FILE__) . '/templates/podcastform.tpl.php');
                $form->assign( 'actionUrl', $formUrl );
                $form->assign( 'id', $id );
                $form->assign( 'url', $url );
                $form->assign( 'title', $title );
                $form->assign( 'visibility', $visibility );
                
                $layout->appendToRight( $form->render() );
                
            }
            break;
        case 'rqAddPodcast':
            {
                
                $formUrl = htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exAddPodcast' ) );
                
                // get template
                $form = new PhpTemplate(dirname(__FILE__) . '/templates/podcastform.tpl.php');
                $form->assign( 'actionUrl', $formUrl );
                $form->assign( 'id', $id );
                $form->assign( 'url', $url );
                $form->assign( 'title', $title );
                $form->assign( 'visibility', $visibility );
                
                $layout->appendToRight( $form->render() );
            }
            break;
        case 'rqDeletePodcast':
            {
                $out = '<p>' . get_lang('Do you want to delete the link: %linkTitle ?', array('%linkTitle' => $title )) . '</p>' . "\n"
                .   '<form method="post" action="index.php?cmd=exDeletePodcast">' . "\n"
                .   '<input type="hidden" name="podcastId" value="' . (int)$id . '" />'
                .   '<input type="submit" name="" id="" value="'. get_lang('Ok') .'" />&nbsp;&nbsp;'
                .   claro_html_button('./index.php', get_lang("Cancel") )
                .   '</form>'
                ;
                
                $dialogBox->question( $out );
            }
            break;
        case 'exEditPodcast':
            {
                if( $collection->update( $id, $url, $title, $visibility ) )
                {
                    $dialogBox->success( get_lang('Podcast edited successfully') );
                }
                else
                {
                    $dialogBox->error( get_lang('Error: unable to edit the podcast') );
                }
            }
            break;
        case 'exAddPodcast':
            {
                if( $collection->add( $url, $title, $visibility ) )
                {
                    $dialogBox->success( get_lang('The podcast has been added successfully') );
                }
                else
                {
                    $dialogBox->error( get_lang('Error: unable to add the podcast') );
                }
            }
            break;
        case 'exDeletePodcast':
                if( $collection->delete( $id ) )
                {
                    $dialogBox->success( get_lang('The podcast deleted successfully') );
                }
                else
                {
                    $dialogBox->error( get_lang('Error: unable to delete the podcast') );
                }
            break;
        case 'exMkVisible' :
            {
                if( $collection->changeVisibility( $id, 'visible' ) )
                {
                    $dialogBox->success('The podcast is now visible');
                }
                else
                {
                    $dialogBox->error('Unable to change the visibility of the podcast');
                }
            }
            break;
        case 'exMkInvisible':
            {
                if( $collection->changeVisibility( $id, 'invisible' ) )
                {
                    $dialogBox->success( get_lang( 'The podcast is now invisible' ) );
                }
                else
                {
                    $dialogBox->error( get_lang( 'Unable to change the visibility of the podcast' ) );
                }
            }
            break;
        default:
            throw new Exception('Unknown command');
    }
    
    $layout->prependToRight( $dialogBox->render() );
    
    // prepare left menu for any cmd
    
    $podcastList = new PhpTemplate(dirname(__FILE__) . '/templates/podcastlist.tpl.php');
    $podcastList->assign( 'podcasts', $collection->getAll() );
    
    $layout->appendToLeft( $podcastList->render() );  
    
    Claroline::getDisplay()->body->appendcontent( claro_html_tool_title( get_lang("Video podcast reader") ) );
    Claroline::getDisplay()->body->appendcontent( $layout->render() );
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
    
    Claroline::getDisplay()->body->appendcontent( claro_html_tool_title( get_lang("Video podcast reader") ) );
    Claroline::getDisplay()->body->appendcontent( $dialogBox->render() );
}

JavascriptLoader::getInstance()->load( 'flowplayer-3.2.4.min' );
CssLoader::getInstance()->load( 'icpcrdr' , 'screen' );

$nameTools = get_lang("Video podcast reader");

echo $claroline->display->render();