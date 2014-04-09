<?php // $Id$

// vim: set expandtab tabstop=4 shiftwidth=4:

/**
 * Claroline Podcast Reader Tool
 *
 * @version     ICPCRDR 1.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICPCRDR
 * @author      Frederic Minne <frederic.minne@uclouvain.be>
 */


// declare the module label (same as in the manifest.xml file) if this module is a course tool
$tlabelReq = 'ICPCRDR';

// require the claroline kernel
require dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

// load libraries (see core.lib.php in the kernel for more details about how this works)
// see http://api.claroline.net/ for details about the claroline api
// load libraries from the kernel (from claroline/inc/lib folder)
FromKernel::uses(
    'utils/input.lib',
    'utils/validator.lib',
    'display/layout.lib'
);

// load libraries from the current module (from module/MODULELABEL/lib folder)
From::Module('ICPCRDR')->uses(
    'podcastcollection.lib',
    'podcastparser.lib',
    'podcastproperties.lib'
);

// check if the module can be accessed
if ( !claro_is_in_a_course() || !claro_is_course_allowed() ) 
{
    claro_disp_auth_form(true);
}

// create a dialog box to display messages to the users
$dialogBox = new DialogBox;

try
{
    // get the podcast collection using PodcastCollection from podcastcollection.lib
    $collection = new PodcastCollection();
    
    // input validator from utils/input.lib and utils/validator.lib
    $userInput = Claro_UserInput::getInstance();
    
    // declare available actions (Claroline uses the variable cmd to send actions in the HTTP request)
    // by convention there are 3 types of actions in claroline :
    //
    //  * simple display actions : list, show...
    //  * form or confirmation requested (must start with rq) actions to be called before execution actions : rqDeleteSomething, rqAddSomething...
    //  * execution (must strat with ex) actions : exDeleteSomething, exAddSomething
    //

    // define actions available for a course manager
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
    // define actions available for students
    else
    {
       $userInput->setValidator('cmd', new Claro_Validator_AllowedList( array(
            'list', 'visit'
        ) ) );  
    }
    
    // get the action from the HTTP request and set it to 'list' if missing
    $cmd = $userInput->get( 'cmd','list' );
    
    // retreive data from the HTTP request using the $userInput object and
    // initialize some other variables
    switch ( $cmd )
    {
        case 'list':
            // nothing to do here
            break;

        case 'visit':
        case 'rqEditPodcast':
        case 'rqDeletePodcast':
        case 'exMkVisible':
        case 'exMkInvisible':
            {
                // get a mandatory request variable (i.e. throws an exception if missing)
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
                $properties = new PodcastProperties( $id );
                $properties->load();
            }
            break;

        case 'rqAddPodcast':
            {
                $id = null;
                $url = '';
                $title = '';
                $visibility = 'visible';
            }
            break;

        case 'exEditPodcast':
        case 'exAddPodcast':
            {
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
                
                $properties = array(
                    'download_link' => $userInput->get( 'download_link', 'visible' )
                );
            }
            break;

        case 'exDeletePodcast':
            {
                $id = $userInput->getMandatory( 'podcastId' );
            }
            break;

        default:
            throw new Exception('Unknown command');
    }
    
    // initialize the display using a layout (provided by display/layout.lib)
    $layout = new LeftMenuLayout;
    
    // prepare right pannel of the layout
    // this is where the actions are handled by the script
    switch ( $cmd )
    {
        case 'list':
            {
                $dialogBox->info(get_lang('Choose a podcast in the list to start'));
                $tpl = new ModuleTemplate( 'ICPCRDR', 'podcastnone.tpl.php' );
                $layout->appendToRight( $tpl->render() );
            }
            break;

        case 'visit':
            {
                // check if the podcast can be viewed by the user
                if( !($visibility == 'visible' || claro_is_allowed_to_edit()) )
                {
                    $dialogBox->error( get_lang('Not allowed') );
                    break;
                }
                
                $rsort = $userInput->get( 'sort' ) == 'chrono';
                // Parse the podcast RSS flux by using the podcast parser from podcastparser.lib
                $parser = new PodcastParser();
                $parser->parseFeed( $url , $rsort );
                
                // use a template to display the podcast
                // module templates are in module/MODULELABEL/templates folder
                $videoList = new ModuleTemplate( 'ICPCRDR', 'podcastdisplay.tpl.php' );
                
                // assign internal variables to the template
                // those variables are accessed from inside the template file 
                // by using $this->variableName
                $videoList->assign( 'channel', $parser->getChannelInfo() );
                $videoList->assign( 'items', $parser->getItems() );
                $videoList->assign( 'url', $url );
                $videoList->assign( 'rsort' , $rsort );
                $videoList->assign( 'id' , $id );
                $videoList->assign( 'downloadLink' , $properties->getProperty ( 'download_link', 'visible' ) );
                // $videoList->assign( 'title', $title );
                
                // append the template to the layout
                $layout->appendToRight( $videoList->render() );
            }    
            break;

        case 'rqEditPodcast':
            {
                // use a template to display edit podcast properties form
                $form = new ModuleTemplate( 'ICPCRDR', 'podcastform.tpl.php' );
                
                $form->assign( 'actionUrl', $_SERVER['PHP_SELF'].'?cmd=exEditPodcast' );
                $form->assign( 'id', $id );
                $form->assign( 'url', $url );
                $form->assign( 'title', $title );
                $form->assign( 'visibility', $visibility );
                $form->assign( 'downloadLink', $properties->getProperty('download_link','visible') );
                
                // append the template to the layout
                $layout->appendToRight( $form->render() );
                
            }
            break;

        case 'rqAddPodcast':
            {
                // use a template to display a HTML form
                $form = new ModuleTemplate( 'ICPCRDR', 'podcastform.tpl.php' );
                
                $form->assign( 'actionUrl', $_SERVER['PHP_SELF'].'?cmd=exAddPodcast' );
                $form->assign( 'id', $id );
                $form->assign( 'url', $url );
                $form->assign( 'title', $title );
                $form->assign( 'visibility', $visibility );
                $form->assign( 'downloadLink', 'visible' );
                
                $layout->appendToRight( $form->render() );
            }
            break;

        case 'rqDeletePodcast':
            {
                $out = '<p>' 
                    . get_lang('Do you want to delete the link: %linkTitle ?', array('%linkTitle' => $title )) 
                    . '</p>' . "\n"
                    . '<form method="post" action="index.php?cmd=exDeletePodcast">' . "\n"
                    . '<input type="hidden" name="podcastId" value="' . (int)$id . '" />'
                    . '<input type="submit" name="" id="" value="'. get_lang('Ok') .'" />&nbsp;&nbsp;'
                    . claro_html_button('./index.php', get_lang("Cancel") )
                    . '</form>'
                    ;
                
                $dialogBox->question( $out );
            }
            break;

        case 'exEditPodcast':
            {
                if( $collection->update( $id, $url, $title, $visibility, $properties ) )
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
                if( $collection->add( $url, $title, $visibility, $properties ) )
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
            {
                if( $collection->delete( $id ) )
                {
                    $dialogBox->success( get_lang('The podcast deleted successfully') );
                }
                else
                {
                    $dialogBox->error( get_lang('Error: unable to delete the podcast') );
                }
            }
            break;

        case 'exMkVisible' :
            {
                if( $collection->changeVisibility( $id, 'visible' ) )
                {
                    $dialogBox->success( get_lang( 'The podcast is now visible' ) );
                }
                else
                {
                    $dialogBox->error( get_lang( 'Error: unable to change the visibility of the podcast' ) );
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
    
    // add the dialog box to the right pannel to the layout
    $layout->prependToRight( $dialogBox->render() );
    
    // prepare left menu for any cmd
    
    $podcastList = new ModuleTemplate( 'ICPCRDR', 'podcastlist.tpl.php' );
    $podcastList->assign( 'podcasts', $collection->getAll() );
    
    // add the left menu to the layout
    $layout->appendToLeft( $podcastList->render() );  
    
    // add the title for our module to the claroline page
    Claroline::getDisplay()->body->appendcontent( claro_html_tool_title( get_lang("Video podcast reader") ) );
    
    // add the layout to the claroline page 
    Claroline::getDisplay()->body->appendcontent( $layout->render() );

    // load optional javascript and css
    // see core/loader.lib for details
    JavascriptLoader::getInstance()->load( 'flowplayer-3.2.6.min' );
    CssLoader::getInstance()->load( 'icpcrdr' , 'screen' );

    // define the name of the tool to be displayed in various locations
    $nameTools = get_lang("Video podcast reader");

    // set html page title 
    Claroline::getDisplay()->header->setTitle( $nameTools );

    // set module node in breadcrumbs
    ClaroBreadCrumbs::getInstance()->setCurrent( $nameTools, Url::Contextualize( $_SERVER['PHP_SELF'] ) );

    // if a podcast is displayed add it to the breadcrumbs
    if ( 'visit' == $cmd && isset( $parser ) )
    {
        $channelInfo = $parser->getChannelInfo();
        ClaroBreadCrumbs::getInstance()->append( claro_utf8_decode( $channelInfo['title'], get_conf('charset') ) );
    }

    // display the page and send it back to the user
    echo Claroline::getDisplay()->render();

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
    // define the name of the tool to be displayed in various locations
    $nameTools = get_lang("Video podcast reader");

    // set html page title 
    Claroline::getDisplay()->header->setTitle( $nameTools );

    // set module node in breadcrumbs
    ClaroBreadCrumbs::getInstance()->setCurrent( $nameTools, Url::Contextualize( $_SERVER['PHP_SELF'] ) );
    
    // add the title of the module to the claroline page
    Claroline::getDisplay()->body->appendcontent( claro_html_tool_title( get_lang("Video podcast reader") ) );

    // add the error message to the claroline page
    Claroline::getDisplay()->body->appendcontent( $dialogBox->render() );
    
    echo Claroline::getDisplay()->render();
}
