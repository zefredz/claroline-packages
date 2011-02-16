<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.2.4 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

//$tlabelReq = 'CLLIBR';

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

FromKernel::uses(
    'utils/input.lib',
    'utils/validator.lib',
    'display/layout.lib',
    'fileUpload.lib' );

From::Module( 'CLLIBR' )->uses(
    'resource.lib',
    'resourceset.lib',
    'storedresource.lib',
    'catalogue.lib',
    'bibliography.lib',
    'bookmark.lib',
    'librarylist.lib',
    'library.lib',
    'pluginloader.lib' );

$claroline->currentModuleLabel( 'CLLIBR' );
load_module_config( 'CLLIBR' );
load_module_language( 'CLLIBR' );

$nameTools = get_lang( 'Online Library' );
$pageTitle = array( 'mainTitle' => get_lang( 'Online Library' ) );

$repository = get_conf( 'CLLIBR_storage_directory' );
if ( substr( $repository , -1 ) != '/' )
{
    $repository = $repository . '/';
}

$pluginLoader = new PluginLoader( 'lib/plugins/' );
$pluginLoader->loadPlugins();

$courseId = claro_get_current_course_id();
$userId = claro_get_current_user_id();

$is_allowed_to_edit = ( $courseId && claro_is_allowed_to_edit() ) || claro_is_allowed_to_create_course();
$is_platform_admin = claro_is_platform_admin();

$userInput = Claro_UserInput::getInstance();

$userInput->setValidator( 'context' ,
                          new Claro_Validator_AllowedList( array( 'LibraryList'
                                                                , 'Catalogue'
                                                                , 'Bibliography'
                                                                , 'Bookmark' )
                        ) );
$userInput->setValidator( 'cmd' ,
                          new Claro_Validator_AllowedList( array( 'rqShowList'
                                                                , 'rqView'
                                                                , 'exBookmark'
                                                                , 'exAdd'
                                                                , 'rqRemove'
                                                                , 'exRemove'
                                                                , 'rqAddResource'
                                                                , 'rqCreateLibrary'
                                                                , 'exCreateLibrary'
                                                                , 'rqEditLibrary'
                                                                , 'exEditLibrary'
                                                                , 'rqDeleteLibrary'
                                                                , 'exDeleteLibrary'
                                                                , 'exAddResource'
                                                                , 'rqDelete'
                                                                , 'exDelete' )
                        ) );


// CONTROLLER
$cmd = $userInput->get( 'cmd' , 'rqShowList' );
$libraryId = $userInput->get( 'libraryId' );
$resourceId = $userInput->get( 'resourceId' );
$context = $userInput->get( 'context' );

if ( ! $context )
{
    if ( $libraryId )
    {
        $context = 'Catalogue';
    }
    elseif ( $courseId )
    {
        $context = 'Bibliography';
    }
    else
    {
        $context = 'LibraryList';
    }
}

if ( $context == 'Catalogue' )
{
    $refId = $libraryId;
}
elseif( $context == 'Bibliography' )
{
    $refId = $courseId;
}
else
{
    $refId = $userId;
}

$resourceSet = new $context( $refId );

$errorMsg = false;

switch( $cmd )
{
    case 'rqShowList':
    case 'rqDeleteLibrary':
    case 'rqView':
    case 'rqAddResource':
    case 'rqDelete':
    case 'rqRemove':
    {
        break;
    }
    
    case 'exBookmark':
    {
        if ( $context == 'Bookmark' )
        {
            $errorMsg = get_lang( 'The resource is already bookmarked' );
        }
        else
        {
            $bookmark = new Bookmark( $userId );
        }
        
        $execution_ok = ! $errorMsg
                        && $bookmark->addResource( new Resource( $resourceId ) );
        break;
    }
    
    case 'exAdd':
    {
        $bibliography = new Bibliography( $courseId );
        
        $execution_ok = $bibliography->addResource( new Resource( $resourceId ) );
        break;
    }
    
    case 'exRemove':
    {
        $execution_ok = $resourceSet->removeResource( $resourceId );
        break;
    }
    
    case 'rqCreateLibrary':
    case 'rqEditLibrary':
    {
        $library = new Library( $libraryId );
        break;
    }
    
    case 'exEditLibrary':
    case 'exCreateLibrary':
    {
        $title = $userInput->get( 'title' );
        $is_public = $userInput->get( 'is_public' );
        
        $library = new Library( $libraryId );
        $library->setTitle( $title );
        $library->setPublic( (boolean)$is_public );
        $execution_ok = $library->save()
                         && $libraryId
                         || $library->addLibrarian( $userId );
        break;
    }
    
    case 'exDeleteLibrary':
    {
        $library = new Library( $libraryId );
        $execution_ok = $library->delete();
        break;
    }
    
    case 'exAddResource':
    {
        $title = $userInput->get( 'title' );
        $type = $userInput->get( 'type' );
        $storage = $userInput->get( 'storage' );
        
        $resource = new $type();
        $resource->setTitle( $title );
        $resource->setType( $storage );
        
        if ( $storage == 'file' )
        {
            $storedResource = new StoredResource( $repository );
            
            if ( $_FILES && $_FILES[ 'uploadedFile' ][ 'size' ] != 0 )
            {
                $file = $_FILES[ 'uploadedFile' ];
            }
            else
            {
                $errorMsg = get_lang( 'file missing' );
            }
            
            if ( ! $errorMsg && ! $resource->validate( $file['name'] ) )
            {
                $errorMsg = get_lang( 'invalid file' );
            }
            
            if ( ! $errorMsg && ! $storedResource->store( $file , $resource->getUid() ) )
            {
                $errorMsg = get_lang( 'file could not be stored' );
            }
        }
        else
        {
            $storedResource = new LinkedResource( $repository );
        }
        
        if ( ! $errorMsg )
        {
            $resource->save();
        }
        
        $execution_ok = ! $errorMsg
                            && $resourceSet->addResource( $resource );
        break;
    }
    
    case 'exDelete':
    {
        $resource = new Resource( $resourceId );
        $storedResource = new StoredResource( $repository , $resourceId );
        $execution_ok = $resource->delete() && $storedResource->delete();
        break;
    }
    
    default:
    {
        throw new Exception( 'bad command' );
    }
}


// VIEW
CssLoader::getInstance()->load( 'cllibr' , 'screen' );

$dialogBox = new DialogBox();

$pageTitle[ 'subTitle' ] = get_lang( $context );
$template = new PhpTemplate( dirname( __FILE__ ) . '/templates/' . strtolower( $context ) . '.tpl.php' );
$template->assign( 'is_allowed_to_edit' , $is_allowed_to_edit );
$template->assign( 'resourceList' , $resourceSet->getResourceList( true ) );
$template->assign( 'libraryId' , $libraryId );
$template->assign( 'courseId' , $courseId );
$template->assign( 'icon' , get_icon_url( 'icon' ) );

switch( $cmd )
{
    case 'rqShowList':
    {
        break;
    }
    
    case 'rqView':
    {
        $resourceId = $userInput->get( 'resourceId' );
        $storedResource = new StoredResource( $repository );
        $storedResource->load( $resourceId );
        $storedResource->getFile();
        break;
    }
    
    case 'exBookmark':
    case 'exAdd':
    case 'exRemove':
    case 'exAddResource':
    case 'exCreateLibrary':
    case 'exEditLibrary':
    case 'exDeleteLibrary':
    case 'exDelete':
    {
        if ( $execution_ok )
        {
            $dialogBox->success( get_lang( 'success' ) );
        }
        else
        {
            $dialogBox->error( $errorMsg ? $errorMsg : get_lang( 'error' ) );
        }
        break;
    }
    
    case 'rqCreateLibrary':
    case 'rqEditLibrary':
    {
        $form = new PhpTemplate( dirname( __FILE__ ) . '/templates/editlibrary.tpl.php' );
        $form->assign( 'userId' , $userId );
        $form->assign( 'libraryId' , $libraryId );
        $form->assign( 'title' , $library->getTitle() );
        $form->assign( 'is_public' , $library->isPublic() );
        $dialogBox->form( $form->render() );
        break;
    }
    
    case 'rqAddResource':
    {
        $pageTitle[ 'subTitle' ] = get_lang( 'Add a resource' );
        $template = new PhpTemplate( dirname( __FILE__ ) . '/templates/addresource.tpl.php' );
        $template->assign( 'userId' , $userId );
        $template->assign( 'libraryId' , $libraryId );
        break;
    }
    
    case 'rqRemove':
    {
        $question = new PhpTemplate( dirname( __FILE__ ) . '/templates/question.tpl.php' );
        $question->assign( 'msg' , get_lang( 'Do you really want to remove this resource?' ) );
        $question->assign( 'urlAction' , 'exRemove' );
        $question->assign( 'urlCancel' , 'rqShowList' );
        $question->assign( 'xid' , 'resourceId' );
        $question->assign( 'id' , $resourceId );
        $question->assign( 'context' , $context );
        $dialogBox->question( $question->render() );
        break;
    }
    
    case 'rqDeleteLibrary':
    {
        $question = new PhpTemplate( dirname( __FILE__ ) . '/templates/question.tpl.php' );
        $question->assign( 'msg' , get_lang( 'Do you really want to delete this library?' ) );
        $question->assign( 'urlAction' , 'exDeleteLibrary' );
        $question->assign( 'urlCancel' , 'rqShowList' );
        $question->assign( 'xid' , 'libraryId' );
        $question->assign( 'id' , $libraryId );
        $question->assign( 'context' , $context );
        $dialogBox->question( $question->render() );
        break;
    }
    
    case 'rqDelete':
    {
        $question = new PhpTemplate( dirname( __FILE__ ) . '/templates/question.tpl.php' );
        $question->assign( 'msg' , get_lang( 'Do you really want to delete this resource?' ) );
        $question->assign( 'urlAction' , 'exDelete' );
        $question->assign( 'urlCancel' , 'rqShowList' );
        $question->assign( 'xid' , 'resourceId' );
        $question->assign( 'id' , $resourceId );
        $question->assign( 'context' , $context );
        $dialogBox->question( $question->render() );
        break;
    }
    
    default:
    {
        throw new Exception( 'bad command' );
    }
}

ClaroBreadCrumbs::getInstance()->append( $pageTitle[ 'subTitle' ]
                                       , htmlspecialchars( Url::Contextualize( $_SERVER[ 'PHP_SELF' ] . '?context=' . $context ) ) );
Claroline::getInstance()->display->body->appendContent( claro_html_tool_title( $pageTitle )
                                                        . $dialogBox->render()
                                                        . $template->render() );

echo Claroline::getInstance()->display->render();