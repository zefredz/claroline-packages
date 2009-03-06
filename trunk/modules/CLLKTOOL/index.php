<?php // $Id$

/**
 * Claroline Advanced Link Tool
 *
 * @version     CLLKTOOL 1.0-alpha $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLKTOOL
 * @author      Frederic Minne <frederic.minne@uclouvain.be>
 * @author      Dimitri Rambout <dimitri.rambout@uclouvain.be>
 */

$tlabelReq = 'CLLKTOOL';

require dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

FromKernel::uses('utils/input.lib','utils/validator.lib','user.lib','display/layout.lib');
From::Module('CLLKTOOL')->uses('linkcollection.lib','linkrenderer.lib');

if ( !claro_is_in_a_course() || !claro_is_course_allowed() ) claro_disp_auth_form(true);

$dialogBox = new DialogBox;

try
{
    $is_allowed_to_edit = claro_is_allowed_to_edit();
    
    $collection = LinkCollection::getInstance();
    
    $internOptionsList = $collection->loadOptionsList();
    
    $userInput = Claro_UserInput::getInstance();
    
    if( $is_allowed_to_edit )
    {
        $userInput->setValidator('cmd', new Claro_Validator_AllowedList( array(
            'list', 'visit',
            'rqAddLink', 'exAddLink',
            'rqEditLink', 'exEditLink',
            'rqDeleteLink', 'exDeleteLink',
            'exMkVis', 'exMkInvis'
        ) ) );   
    }
    else
    {
       $userInput->setValidator('cmd', new Claro_Validator_AllowedList( array(
            'list', 'visit'
        ) ) );  
    }
    
    $typeList = array(
        'post:json','post:xml','post:plain','widget','iframe','popup'
    );
    
    $userInput->setValidator('type', new Claro_Validator_AllowedList( $typeList ));
    
    $cmd = $userInput->get( 'cmd','list' );
    
    // get data
    
    switch ( $cmd )
    {
        case 'list':
            break;
        case 'visit':
        case 'rqEditLink':
        case 'rqDeleteLink':
        case 'exMkVis':
        case 'exMkInvis':
                $id = $userInput->getMandatory( 'linkId' );
                
                $link = $collection->get( $id );
                
                if ( ! $link )
                {
                    throw new Exception('Link not found');
                }
                
                $id = (int) $link['id'];
                $url = $link['url'];
                $title = $link['title'];
                $options = !empty($link['options']) ? unserialize( $link['options'] ) : array('params','width','height');
                $type = $link['type'];
                $visibility = $link['visibility'];
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
                $url = $userInput->getMandatory( 'url' );
                $title = $userInput->getMandatory( 'title' );
                $options = $userInput->get( 'options', array('params' => array(),'width' => '','height' => '') );
                $type = $userInput->get( 'type', 'iframe' );
                $visibility = $userInput->get ( 'visibility', 'visible' );
            break;
        case 'exDeleteLink':
                $id = $userInput->getMandatory( 'linkId' );
            break;
        default:
            throw new Exception('Unknown command');
    }
    
    $layout = new LeftMenuLayout;
    
    // prepare right menu
    switch ( $cmd )
    {
        case 'list':
                $layout->appendToRight(get_lang('Choose a link to start'));
            break;
        case 'visit':
            {
                if( !($visibility == 'visible' || $is_allowed_to_edit) )
                {
                    $dialogBox->error( get_lang('Error: unable to display the link') );
                    break;
                }
                $_options = array();
                if(!(isset($options['params']) && is_array($options['params']) && count($options['params']) ) )
                {
                    $options['params'] = array();
                }
                foreach( $options['params'] as $option )
                {
                    if( $option['var'] == 'freeValue' )
                    {
                        $_options[$option['name']] = $option['value'];
                    }
                    else
                    {
                        $_options[$option['name']] = $collection->loadInternalOptionValue( $option['var'] );
                    }                        
                }
                $options['params'] = $_options;
                $linkRenderer = new LinkRenderer( $url, $options, $type, $title );
                
                $layout->appendToRight($linkRenderer->render());                
            }
            break;
        case 'rqEditLink':
            {
                
                $formUrl = htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exEditLink' ) );
                
                $form = LinkRenderer::displayForm(
                  $formUrl,
                  $title,
                  $url,
                  $typeList,
                  $type,
                  $options,
                  $visibility,
                  $id,
                  $internOptionsList
                );
                
                $layout->appendToRight( $form );
                
            }
            break;
        case 'rqAddLink':
            {
                
                $formUrl = htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exAddLink' ) );
                    
                $form = LinkRenderer::displayForm(
                  $formUrl,
                  null,
                  null,
                  $typeList,
                  'iframe',
                  array('params' => array(),'width' => '','height' => ''),
                  null,
                  null,
                  $internOptionsList
                );
                
                $layout->appendToRight( $form );                
            }
            break;
        case 'rqDeleteLink':
            {
                $out = '<p>' . get_lang('Do you want to delete the link: %linkTitle ?', array('%linkTitle' => $title )) . '</p>' . "\n"
                .   '<form method="post" action="index.php?cmd=exDeleteLink">' . "\n"
                .   '<input type="hidden" name="linkId" value="' . $id . '" />'
                .   '<input type="submit" name="" id="" value="'. get_lang('Ok') .'" />&nbsp;&nbsp;'
                .   claro_html_button('./index.php', get_lang("Cancel") )
                .   '</form>'
                ;
                $dialogBox->question( $out );                
            }
            break;
        case 'exEditLink':
            {
                $error = false;
                if(isset($options['params']) && is_array($options['params']) && count($options['params']) )
                {
                    foreach( $options['params'] as $option )
                    {
                        if( !( isset($option['name']) && isset($option['var']) && isset($option['value']) ) )
                        {
                            $error = true;
                            break;
                        }
                        if( empty($option['name']) )
                        {
                            $error = true;
                            break;
                        }
                        if( empty($option['var']) )
                        {
                            $error = true;
                            break;
                        }
                        if( $option['var'] != 'freeValue' )
                        {
                            if( ! $collection->checkOptionExist( $option['var'] ) )
                            {
                                $error = true;
                                break;
                            }
                        }
                        else
                        {
                            if( empty( $option['value']) )
                            {
                                $error = true;
                                break;
                            }
                        }
                        
                    }
                }
                
                if( !$error )
                {
                    if( $collection->update( $id, $url, $title, $type, $options, $visibility ) )
                    {
                        $dialogBox->success( get_lang('Link edited successfully') );
                    }
                    else
                    {
                        $dialogBox->error( get_lang('Error: unable to edit the link') );
                    }
                }
                else
                {
                    $dialogBox->error( get_lang('Error: check Options'));
                    
                    $formUrl = htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exEditLink' ) );
                    
                    $form = LinkRenderer::displayForm(
                      $formUrl,
                      $title,
                      $url,
                      $typeList,
                      $type,
                      $options,
                      $visibility,
                      $id,
                      $internOptionsList
                    );
                    
                    $layout->appendToRight( $form );
                }
            }
            break;
        case 'exAddLink':
            {
                $error = false;
                if(isset($options['params']) && is_array($options['params']) && count($options['params']) )
                {                    
                    foreach( $options['params'] as $option )
                    {
                        if( !( isset($option['name']) && isset($option['var']) && isset($option['value']) ) )
                        {
                            $error = true;
                            break;
                        }
                        if( empty($option['name']) )
                        {
                            $error = true;
                            break;
                        }
                        if( empty($option['var']) )
                        {
                            $error = true;
                            break;
                        }
                        if( $option['var'] != 'freeValue' )
                        {
                            if( ! $collection->checkOptionExist( $option['var'] ) )
                            {
                                $error = true;
                                break;
                            }
                        }
                        else
                        {
                            if( empty( $option['value']) )
                            {
                                $error = true;
                                break;
                            }
                        }
                        
                    }
                }
                
                if( !$error )
                {
                    if( $collection->add( $url, $title, $type, $options, $visibility ) )
                    {
                        $dialogBox->success( get_lang('Link added successfully') );
                    }
                    else
                    {
                        $dialogBox->error( get_lang('Error: unable to add the link') );
                    }
                }
                else
                {
                    $dialogBox->error( get_lang('Error: check Options'));
                    
                    $formUrl = htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exAddLink' ) );
                    
                    $form = LinkRenderer::displayForm(
                      $formUrl,
                      $title,
                      $url,
                      $typeList,
                      $type,
                      $options,
                      $visibility,
                      null,
                      $internOptionsList
                    );
                    
                    $layout->appendToRight( $form );                    
                    
                }                
            }
            break;
        case 'exDeleteLink':
            {
                if( $collection->delete( $id ) )
                {
                    $dialogBox->success( get_lang('Link deleted successfully') );
                }
                else
                {
                    $dialogBox->error( get_lang('Error: unable to delete the link') );
                }
            }
            break;
        case 'exMkVis' :
            {
                if( $collection->changeVisibility( $id, 'visible' ) )
                {
                    $dialogBox->success(get_lang('The link is now visible'));
                }
                else
                {
                    $dialogBox->error(get_lang('Unable to change the visibility of the link'));
                }
            }
            break;
        case 'exMkInvis':
            {
                if( $collection->changeVisibility( $id, 'invisible' ) )
                {
                    $dialogBox->success(get_lang('The link is now invisible'));
                }
                else
                {
                    $dialogBox->error(get_lang('Unable to change the visibility of the link'));
                }
            }
            break;
        default:
            throw new Exception(get_lang('Unknown command'));
    }
    
    $layout->prependToRight( $dialogBox->render() );
    
    
    // prepare left menu for any cmd
    
    $linkList = '<ul>' . "\n";
    
    $links = $collection->getAll();
    
    foreach ( $links as $currentLink )
    {
        if( $currentLink['visibility'] == 'visible' || $is_allowed_to_edit )
        {
            $linkList .= '<li><a href="'
                . htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=visit&linkId='.(int)$currentLink['id'] ) )
                . '" ' .($currentLink['visibility'] != 'visible' ? 'class="invisible"' : ''). '>'
                . htmlspecialchars($currentLink['title'])
                . '</a>' . "\n"
                ;
            if( $is_allowed_to_edit )
            {
                // Edit link
                $linkList .= ' <a href="' . htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqEditLink&linkId='.(int)$currentLink['id'] ) ) . '">'
                . '<img src="./img/link_edit.png" alt="'.get_lang('Modify').'" />'
                . '</a>' . "\n"
                // Delete link
                . ' <a href="' . htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDeleteLink&linkId='.(int)$currentLink['id'] ) ) . '">'
                . '<img src="./img/link_delete.png" alt="'.get_lang('Delete').'" />'
                . '</a>' . "\n"
                ;
                // Visibility
                if( $currentLink['visibility'] == 'visible' )
                {
                    $linkList .= ' <a href="' . htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMkInvis&linkId='.(int)$currentLink['id'] ) ) . '">'
                    . '<img src="' . get_icon_url('visible') . '" alt="'.get_lang('Make Invisible').'" />'
                    . '</a>' . "\n"
                    ;  
                }
                else
                {
                    $linkList .= ' <a href="' . htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMkVis&linkId='.(int)$currentLink['id'] ) ) . '">'
                    . '<img src="' . get_icon_url('invisible') . '" alt="'.get_lang('Make Visible').'" />'
                    . '</a>' . "\n"
                    ;   
                }            
            }
            $linkList .= '</li>' . "\n"
            ;   
        }        
    }
    
    $linkList .= '</ul>' . "\n";
    
    $url_addLink = '<a href="' . htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqAddLink' ) ) . '">'
    . '<img src="./img/link_add.png" alt="" /> '
    . get_lang( 'Create a new link')
    . '</a>' . "\n"
    ;
    
    $layout->appendToLeft( $url_addLink );
    if(count($links))
    {
        $layout->appendToLeft( $linkList );
        $layout->appendToLeft( $url_addLink );   
    }    
    
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
    
    Claroline::getDisplay()->body->appendcontent( $dialogBox->render() );
}

$jsLoader = JavascriptLoader::getInstance();
$jsLoader->load( 'cllktool');

$cssLoader = CssLoader::getInstance();
$cssLoader->load( 'cllktool', 'screen');

echo $claroline->display->render();