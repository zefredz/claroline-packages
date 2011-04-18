<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.4.0 $Revision$ - Claroline 1.9
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
                                          , 'rqDownload'
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
                                          , 'rqEditLibrarian'
                                          , 'rqAddLibrarian'
                                          , 'exAddLibrarian'
                                          , 'rqRemoveLibrarian'
                                          , 'exRemoveLibrarian'
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
$librarianId = $userInput->get( 'librarianId');
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
    case 'rqDownload':
    case 'rqAddResource':
    case 'rqEditResource':
    case 'rqDelete':
    case 'rqRemove':
    case 'rqCreateLibrary':
    case 'rqEditLibrary':
    case 'rqEditLibrarian':
    case 'rqAddLibrarian':
    case 'rqRemoveLibrarian':
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
            if ( $_FILES && $_FILES[ 'uploadedFile' ][ 'size' ] != 0 )
            {
                $file = $_FILES[ 'uploadedFile' ];
                $resourceName = $file[ 'name' ];
            }
            else
            {
                $errorMsg = get_lang( 'file missing' );
            }
            
            /**
            if ( ! $errorMsg
              && ! $resource->validate( $file['name'] ) )
            {
                $errorMsg = get_lang( 'invalid file' );
            }*/
        }
        else
        {
            $resourceName = $userInput->get( 'resourceUrl' );
            
            if ( ! $resourceName )
            {
                $errorMsg = get_lang( 'url missing' );
            }
        }
        
        if ( ! $errorMsg
            && $resource->setName( $resourceName )
            && $resource->save()
            && ! empty( $metadataList ) )
        {
            $metadata = new Metadata( $database , $resource->getId() );
            
            foreach( $metadataList as $property => $value )
            {
                $metadata->add( $property , $value );
            }
        }
        
        if ( ! $errorMsg && $file )
        {
            $storedResource = new StoredResource( $repository , $resource );
            
            if ( ! $storedResource->store( $file ) )
            {
                $errorMsg = get_lang( 'cannot store the file' );
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
        $metadata = new Metadata( $database , $resourceId );
        $catalogue = new Collection( $database , 'catalogue' , $libraryId );
        
        $execution_ok = $metadata->removeAll()
                     && $catalogue->removeResource( $resourceId )
                     && $resource->delete();
        
        if ( $execution_ok && $resource->getType() == 'file' )
        {
            $storedResource = new StoredResource( $repository , $resource );
            $execution_ok = $storedResource->delete();
        }
        break;
    }
    
    case 'exRemoveLibrarian':
    {
        $execution_ok = $librarian->unregister( $librarianId );
        break;
    }
    
    default:
    {
        throw new Exception( 'bad command' );
    }
}


// VIEW
CssLoader::getInstance()->load( 'cllibr' , 'screen' );

$jsLoader = JavascriptLoader::getInstance();
$jsLoader->load( 'editresource' );


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
        $template = new PhpTemplate( dirname( __FILE__ ) . '/templates/resource.tpl.php' );
        $template->assign( 'resourceId' , $resourceId );
        $template->assign( 'storageType' , $resource->getType() );
        $template->assign( 'url' , $resource->getName() );
        $template->assign( 'title' , $metadata->get( 'title' ) );
        $template->assign( 'metadataList' , $metadata->export() );
        $template->assign( 'userId' , $userId );
        $template->assign( 'libraryId' , $libraryId );
        $template->assign( 'context' , $context );
        break;
    }
    
    case 'rqDownload':
    {
        $storedResource = new StoredResource( $repository , $resource );
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
    case 'exRemoveLibrarian':
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
    
    case 'rqAddLibrarian':
    {
        $form = new PhpTemplate( dirname( __FILE__ ) . '/templates/addlibrarian.tpl.php' );
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
    
    case 'rqEditLibrarian':
    {
        $template = new PhpTemplate( dirname( __FILE__ ) . '/templates/librarian.tpl.php' );
        $template->assign( 'libraryId' , $libraryId );
        $template->assign( 'librarianList' , $resourceSet->getLibrarianList( $libraryId ) );
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
        $template->assign( 'libraryId' , $libraryId );
        $template->assign( 'context' , $context );
        $template->assign( 'refId' , $refId );
        $template->assign( 'refName' , $refName );
        $template->assign( 'typeList' , $pluginList[ 'resource' ] );
        $template->assign( 'defaultMetadataList' , Metadata::getDefaultMetadataList() );
        $template->assign( 'urlAction' , 'ex' . substr( $cmd , 2 ) );
        $template->assign( 'propertyList' , $metadata->getAllProperties() );
        $template->assign( 'keywordsList' , $metadata->getAllKeywords() );
        break;
    }
    
    case 'rqRemove':
    {
        $msg = get_lang( 'Do you really want to remove this resource?' );
        $urlAction = 'exRemove';
        $urlCancel = 'rqShowList';
        $xid = array( 'resourceId' => $resourceId );
        break;
    }
    
    case 'rqDeleteLibrary':
    {
        $msg = get_lang( 'Do you really want to delete this library?' );
        $urlAction = 'exDeleteLibrary';
        $urlCancel = 'rqShowList';
        $xid = array( 'libraryId' => $libraryId );
        break;
    }
    
    case 'rqDelete':
    {
        $msg = get_lang( 'Do you really want to delete this resource?' );
        $urlAction = 'exDelete';
        $urlCancel = 'rqShowList';
        $xid = array( 'resourceId' => $resourceId
                    , 'context' => 'catalogue'
                    , 'libraryId' => $libraryId );
        break;
    }
    
    case 'rqRemoveLibrarian':
    {
        $msg = get_lang( 'Do you really want to remove this librarian?' );
        $urlAction = 'exRemoveLibrarian';
        $urlCancel = 'rqShowList';
        $xid = array( 'librarianId' => $librarianId , 'libraryId' => $libraryId );
        break;
    }
    
    default:
    {
        throw new Exception( 'bad command' );
    }
}

if ( isset( $msg ) )
{
    $question = new PhpTemplate( dirname( __FILE__ ) . '/templates/question.tpl.php' );
    $question->assign( 'msg' , $msg );
    $question->assign( 'urlAction' , $urlAction );
    $question->assign( 'urlCancel' , $urlCancel );
    $question->assign( 'xid' , $xid );
    $question->assign( 'context' , $context );
    $dialogBox->question( $question->render() );
}

if ( $resourceId && $cmd == 'rqView' )
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