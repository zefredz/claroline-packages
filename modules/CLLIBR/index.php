<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.3.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
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
    'collection.lib',
    'storedresource.lib',
    'catalogue.lib',
    'bibliography.lib',
    'bookmark.lib',
    'librarylist.lib',
    'library.lib',
    'librarian.lib',
    'metadata.lib',
    'metadataview.lib',
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

$database = Claroline::getDatabase();
// for later -->
$tableList = get_module_main_tbl( array( 'library_resource'
                                       , 'library_metadata'
                                       , 'library_library'
                                       , 'library_librarian'
                                       , 'library_collection' ) );
/* for use with:
  $DBTable = new DatabaseTable( $database , $tableList[ 'library_' . $className ] );
  $class = new ClassName( $DBTable , ... ); */
// <-- for later

$pluginLoader = new PluginLoader( 'lib/plugins/' );
$pluginLoader->loadPlugins();
$pluginList = $pluginLoader->getPluginList();

$courseId = claro_get_current_course_id();
$userId = claro_get_current_user_id();

if( ! $courseId && ! $userId ) claro_disp_auth_form( true );

$is_allowed_to_edit = ( $courseId && claro_is_allowed_to_edit() ) || claro_is_allowed_to_create_course();
$is_platform_admin = claro_is_platform_admin();

$userInput = Claro_UserInput::getInstance();

$userInput->setValidator( 'context' ,
                          new Claro_Validator_AllowedList( array( 'librarylist'
                                                                , 'catalogue'
                                                                , 'bibliography'
                                                                , 'bookmark' )
                        ) );
$userInput->setValidator( 'cmd' ,
                          new Claro_Validator_AllowedList( array( 'rqShowList'
                                                                , 'rqView'
                                                                , 'exBookmark'
                                                                , 'exAdd'
                                                                , 'rqRemove'
                                                                , 'exRemove'
                                                                , 'rqAddResource'
                                                                , 'rqEditResource'
                                                                , 'rqCreateLibrary'
                                                                , 'exCreateLibrary'
                                                                , 'rqEditLibrary'
                                                                , 'exEditLibrary'
                                                                , 'rqDeleteLibrary'
                                                                , 'exDeleteLibrary'
                                                                , 'exAddResource'
                                                                , 'exEditResource'
                                                                , 'rqDelete'
                                                                , 'exDelete' )
                        ) );


// CONTROLLER
$cmd = $userInput->get( 'cmd' , 'rqShowList' );
$libraryId = $userInput->get( 'libraryId' );
$resourceId = $userInput->get( 'resourceId' );
$context = $userInput->get( 'context' );

$library = new Library( $database , $libraryId );
$resource = new Resource( $database , $resourceId );
$metadata = new Metadata( $database , $resourceId );

if ( $libraryId )
{
    $librarian = new Librarian( $database , $libraryId );
}

if ( ! $context )
{
    if ( $libraryId )
    {
        $context = 'catalogue';
    }
    elseif ( $courseId )
    {
        $context = 'bibliography';
    }
    elseif ( $userId )
    {
        $context = 'bookmark';
    }
    else
    {
        $context = 'librarylist';
    }
}

$refId = null;

if ( $context == 'catalogue' )
{
    $refId = $libraryId;
    $refName = 'libraryId';
}
elseif( $context == 'bibliography' )
{
    $refId = $courseId;
    $refName = 'courseId';
}
else
{
    $refId = $userId;
    $refName = 'userId';
}

if ( $context == 'librarylist' )
{
    $resourceSet = new LibraryList( $database , $userId );
}
else
{
    $resourceSet = new Collection( $database , $context , $refId );
}

$errorMsg = false;

switch( $cmd )
{
    case 'rqShowList':
    case 'rqDeleteLibrary':
    case 'rqView':
    case 'rqAddResource':
    case 'rqEditResource':
    case 'rqDelete':
    case 'rqRemove':
    case 'rqCreateLibrary':
    case 'rqEditLibrary':
    {
        break;
    }
    
    case 'exBookmark':
    {
        if ( $userId )
        {
            $bookmark = new Collection( $database , 'bookmark' , $userId );
            
            if ( $bookmark->resourceExists( $resourceId ) )
            {
                $errorMsg = get_lang( 'The resource is already bookmarked' );
            }
        }
        else
        {
            $errorMsg = get_lang( 'not allowed' );
        }
        
        $execution_ok = ! $errorMsg
                       && $bookmark->add( $resourceId );
        break;
    }
    
    case 'exAdd':
    {
        $bibliography = new Collection( $database , 'bibliography' , $courseId );
        
        if ( $bibliography->resourceExists( $resourceId ) )
        {
            $errorMsg = get_lang( 'The resource is already is added' );
        }
        
        $execution_ok = ! $errorMsg
                       && $bibliography->add( $resourceId );
        break;
    }
    
    case 'exRemove':
    {
        $execution_ok = $resourceSet->remove( $resourceId );
        break;
    }
    
    case 'exEditLibrary':
    case 'exCreateLibrary':
    {
        $title = $userInput->get( 'title' );
        $is_public = $userInput->get( 'is_public' );
        
        $library->setTitle( $title );
        $library->setPublic( (boolean)$is_public );
        $librarian = new Librarian( $database , $library->save() );
        $execution_ok = $librarian->register( $userId );
        break;
    }
    
    case 'exDeleteLibrary':
    {
        $library = new Library( $database , $libraryId );
        $execution_ok = $library->delete();
        break;
    }
    
    case 'exAddResource':
    {
        $type = $userInput->get( 'type' );
        $storage = $userInput->get( 'storage' );
        $metadataList = $userInput->get( 'metadata' );
        
        $resource = new $type( $database );
        $resource->setType( $storage );
        
        if ( $storage == 'file' )
        {
            $storedResource = new StoredResource( $database , $repository );
            
            if ( $_FILES && $_FILES[ 'uploadedFile' ][ 'size' ] != 0 )
            {
                $file = $_FILES[ 'uploadedFile' ];
            }
            else
            {
                $errorMsg = get_lang( 'file missing' );
            }
            
            if ( ! $errorMsg
              && ! $resource->validate( $file['name'] ) )
            {
                $errorMsg = get_lang( 'invalid file' );
            }
            
            if ( ! $errorMsg
              && ! $resource->setSecretId( $storedResource->store( $file ) ) )
            {
                $errorMsg = get_lang( 'file could not be stored' );
            }
        }
        else
        {
            break; // NOT IMPLEMENTED YET
            $storedResource = new LinkedResource( $database , $repository );
        }
        
        if ( ! $errorMsg && $resource->save() && ! empty( $metadataList ) )
        {
            $metadata = new Metadata( $database , $resource->getId() );
            
            foreach( $metadataList as $property => $value )
            {
                $metadata->add( $property , $value );
            }
        }
        
        $execution_ok = ! $errorMsg
                       && $resourceSet->add( $resource->getId() );
        break;
    }
    
    case 'exEditResource':
    {
        $metadataList = $userInput->get( 'metadata' );
        
        if ( ! empty( $metadataList ) )
        {
            foreach( $metadataList as $id => $value )
            {
                $metadata->modify( $id , $value );
            }
        }
        
        $execution_ok = $resource->save();
        break;
    }
    
    case 'exDelete':
    {
        $resource = new Resource( $database , $resourceId );
        $storedResource = new StoredResource( $database , $repository , $resource->getSecretId() );
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

$pageTitle[ 'subTitle' ] = get_lang( $context ) . ( $libraryId ? ' - ' . $library->getTitle() : '' );
$template = new PhpTemplate( dirname( __FILE__ ) . '/templates/' . strtolower( $context ) . '.tpl.php' );
$template->assign( 'is_allowed_to_edit' , $is_allowed_to_edit );
$template->assign( 'resourceList' , $resourceSet->getResourceList( true ) );
$template->assign( 'userId' , $userId );
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
        $storedResource = new StoredResource( $database
                                            , $repository
                                            , $resource->getSecretId()
                                            , $resource->getName() );
        $storedResource->getFile();
        break;
    }
    
    case 'exBookmark':
    case 'exAdd':
    case 'exRemove':
    case 'exAddResource':
    case 'exEditResource':
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
    case 'rqEditResource':
    {
        $pageTitle[ 'subTitle' ] = $cmd == 'rqAddResource'
                                 ? get_lang( 'Add a resource' )
                                 : get_lang( 'Edit a resource' );
        $template = new PhpTemplate( dirname( __FILE__ ) . '/templates/editresource.tpl.php' );
        $template->assign( 'resourceId' , $resourceId );
        $template->assign( 'title' , $resourceId ? $metadata->get( 'title' ) : '' );
        $template->assign( 'metadataList' , $resourceId ? $metadata->export() : array() );
        $template->assign( 'userId' , $userId );
        $template->assign( 'context' , $context );
        $template->assign( 'refId' , $refId );
        $template->assign( 'refName' , $refName );
        $template->assign( 'typeList' , $pluginList[ 'resource' ] );
        $template->assign( 'defaultMetadataList' , Metadata::getDefaultMetadataList() );
        $template->assign( 'urlAction' , 'ex' . substr( $cmd , 2 ) );
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

if ( $resourceId )
{
    $dcView = new DublinCore( $metadata->export() );
    ClaroHeader::getInstance()->addHtmlHeader( $dcView->render() );
}

ClaroBreadCrumbs::getInstance()->append( $pageTitle[ 'subTitle' ]
                                       , htmlspecialchars( Url::Contextualize( $_SERVER[ 'PHP_SELF' ] . '?context=' . $context ) ) );
Claroline::getInstance()->display->body->appendContent( claro_html_tool_title( $pageTitle )
                                                        . $dialogBox->render()
                                                        . $template->render() );

echo Claroline::getInstance()->display->render();