<?php // $Id$

/**
 * Claroline Advanced Link Tool
 *
 * @version     ICPCRDR 1.0-alpha $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLKTOOL
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
    $is_allowed_to_edit = claro_is_allowed_to_edit();
    
    $collection = PodcastCollection::getInstance();
    
    $userInput = Claro_UserInput::getInstance();
    
    if( $is_allowed_to_edit )
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
                if ( 'exEditLink' == $cmd )
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
                $layout->appendToRight(get_lang('Choose a podcast in the list to start'));
            break;
        case 'visit':
                if( !($visibility == 'visible' || $is_allowed_to_edit) )
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
    
    $podcastList = '<ul>' . "\n";
    
    $podcasts = $collection->getAll();
    
    foreach ( $podcasts as $currentPodcast )
    {
        if( $currentPodcast['visibility'] == 'visible' || $is_allowed_to_edit )
        {
            $podcastList .= '<li><a href="'
                . htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=visit&podcastId='.(int)$currentPodcast['id'] ) )
                . '">'
                . htmlspecialchars($currentPodcast['title'])
                . '</a>' . "\n"
                ;
            if( $is_allowed_to_edit )
            {
                // Edit link
                $podcastList .= ' <a href="' . htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqEditPodcast&podcastId='.(int)$currentPodcast['id'] ) ) . '">'
                . '<img src="./img/feed_edit.png" alt="'.get_lang('Modify').'" />'
                . '</a>' . "\n"
                // Delete link
                . ' <a href="' . htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDeletePodcast&podcastId='.(int)$currentPodcast['id'] ) ) . '">'
                . '<img src="./img/feed_delete.png" alt="'.get_lang('Delete').'" />'
                . '</a>' . "\n"
                ;
                // Visibility
                if( $currentPodcast['visibility'] == 'visible' )
                {
                    $podcastList .= ' <a href="' . htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMkInvisible&podcastId='.(int)$currentPodcast['id'] ) ) . '">'
                    . '<img src="' . get_icon_url('visible') . '" alt="'.get_lang('Make Invisible').'" />'
                    . '</a>' . "\n"
                    ;  
                }
                else
                {
                    $podcastList .= ' <a href="' . htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMkVisible&podcastId='.(int)$currentPodcast['id'] ) ) . '">'
                    . '<img src="' . get_icon_url('invisible') . '" alt="'.get_lang('Make Visible').'" />'
                    . '</a>' . "\n"
                    ;   
                }            
            }
            $podcastList .= '</li>' . "\n"
            ;   
        }        
    }
    
    $podcastList .= '</ul>' . "\n";
    
    $url_addPodcast = '<a href="' . htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqAddPodcast' ) ) . '">'
    . '<img src="./img/feed_add.png" /> '
    . get_lang( 'Create a new podcast')
    . '</a>' . "\n"
    ;
    
    $layout->appendToLeft( $url_addPodcast );
    
    if(count($podcasts))
    {
        $layout->appendToLeft( $podcastList );
        $layout->appendToLeft( $url_addPodcast );   
    }    
    
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

$jsLoader = JavascriptLoader::getInstance();
$jsLoader->load( 'flowplayer-3.0.6.min');

$nameTools = get_lang("Video podcast reader");

echo $claroline->display->render();