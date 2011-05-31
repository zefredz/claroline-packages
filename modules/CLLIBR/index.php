<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.5.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

$tlabelReq = 'CLLIBR';

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
    'search.lib',
    'metadataview.lib',
    'pluginloader.lib',
    'thirdparty/uuid.lib',
    'tools.lib' );

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

$is_course_creator = claro_is_allowed_to_create_course();
$is_platform_admin = claro_is_platform_admin();

$actionList = array( 'rqShowBookmark'
                   , 'rqShowBibliography'
                   , 'rqShowCatalogue'
                   , 'rqShowLibrarylist'
                   , 'rqShowLibrarian'
                   , 'rqView'
                   , 'rqDownload'
                   , 'exBookmark'
                   , 'exAdd'
                   , 'rqRemove'
                   , 'exRemove'
                   , 'rqQSearch' );

$restrictedActionList = array( 'rqAddResource'
                             , 'rqEditResource'
                             , 'rqDeleteResource'
                             , 'rqCreateLibrary'
                             , 'exCreateLibrary'
                             , 'rqEditLibrary'
                             , 'exEditLibrary'
                             , 'exAddLibrarian'
                             , 'rqRemoveLibrarian'
                             , 'exRemoveLibrarian'
                             , 'rqDeleteLibrary'
                             , 'exDeleteLibrary'
                             , 'exAddResource'
                             , 'exEditResource'
                             , 'exDeleteResource'
                             , 'rqQSearch' );

$userInput = Claro_UserInput::getInstance();
$userInput->setValidator( 'cmd' ,
    new Claro_Validator_AllowedList( array_merge( $actionList , $restrictedActionList ) ) );

// OBJECTS INITIALISATION
$cmd = $userInput->get( 'cmd' , $courseId ? 'rqShowBibliography' : 'rqShowLibrarylist' );
$option = $userInput->get( 'option' );

$libraryId = $userInput->get( 'libraryId' );
$librarianId = $userInput->get( 'librarianId' );
$resourceId = $userInput->get( 'resourceId' );

$library = new Library( $database , $libraryId );
$resource = new Resource( $database , $resourceId );
$metadata = new Metadata( $database , $resourceId );

if ( $libraryId )
{
    $librarian = new Librarian( $database , $libraryId );
}

// SETTING CONTEXT
$refId = null;

if ( substr( $cmd , 0 , 6 ) == 'rqShow' )
{
    $context = strtolower( substr( $cmd , 6 ) );
}
elseif( $librarianId )
{
    $context = 'librarian';
}
elseif( $libraryId )
{
    $context = 'catalogue';
}
elseif( $courseId )
{
    $context = 'bibliography';
}
elseif( $userId )
{
    $context = 'bookmark';
}
else
{
    $context = 'librarylist';
}

// RESOURCESET INITIALISATION & RIGHTS MANAGEMENT
$access_allowed = $edit_allowed = $is_platform_admin;

if ( $context == 'bibliography' )
{
    $refId = $courseId;
    $access_allowed = $access_allowed || claro_is_course_allowed();
    $edit_allowed = $edit_allowed || claro_is_allowed_to_edit();
}
elseif( $context == 'bookmark' )
{
    $refId = $userId;
    $access_allowed = $access_allowed || (boolean)$refId;
    $edit_allowed = $edit_allowed || (boolean)$refId;
}
elseif( $context == 'catalogue')
{
    $refId = $libraryId;
    $access_allowed = $access_allowed || $library->isPublic();
    $edit_allowed = $edit_allowed || $librarian->isLibrarian( $userId );
}
else
{
    $access_allowed = true;
}

if ( $context == 'librarylist' || $context == 'librarian' )
{
    $resourceSet = new LibraryList( $database , $userId , $is_platform_admin );
}
else
{
    $resourceSet = new Collection( $database , $context , $refId );
}

$accessControl = $resourceId && ! $is_platform_admin
               ? $resourceSet->resourceExists( $resourceId )
               : true;

$accessTicket = $accessControl
            && ( $access_allowed && in_array( $cmd , $actionList ) )
            || ( $edit_allowed && in_array( $cmd , $restrictedActionList ) );

if ( $accessTicket ) // AUTHORIZED ACTION
{
    // CONTROLLER
    $errorMsg = false;
    
    switch( $cmd )
    {
        case 'rqShowBookmark':
        case 'rqShowBibliography';
        case 'rqShowCatalogue':
        case 'rqShowLibrarylist':
        case 'rqDeleteLibrary':
        case 'rqView':
        case 'rqDownload':
        case 'rqAddResource':
        case 'rqEditResource':
        case 'rqDeleteResource':
        case 'rqRemove':
        case 'rqCreateLibrary':
        case 'rqEditLibrary':
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
                $errorMsg = get_lang( 'Not allowed' );
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
            if ( $cmd == 'exCreateLibrary' )
            {
                $librarian = new Librarian( $database , $library->save() );
                $execution_ok = $librarian->register( $userId );
            }
            else
            {
                $execution_ok = $library->save();
            }
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
            $title = $userInput->get( 'title' );
            $description = $userInput->get( 'description' );
            $storage = $userInput->get( 'storage' );
            $metadataList = $userInput->get( 'metadata' );
            $newName = $userInput->get( 'name' );
            $newValue = $userInput->get( 'value' );
            
            $resource = new $type( $database );
            
            if ( $title )
            {
                $resource->setTitle( $title );
                $resource->setType( $storage );
                $resource->setDate();
                $resource->setDescription( $description );
                
                if ( $storage == 'file' )
                {
                    $storedResource = new StoredResource( $repository , $resource );
                    
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
                      && ! $storedResource->store( $file ) )
                    {
                        $errorMsg = getlang( 'File cannot be stored' );
                    }
                }
                else
                {
                    $resourceName = $userInput->get( 'resourceUrl' );
                    
                    if ( $resourceName )
                    {
                        $resource->setName( $resourceName );
                    }
                    else
                    {
                        $errorMsg = get_lang( 'url missing' );
                    }
                }
            }
            else
            {
                $errorMsg = get_lang( 'You must give a title' );
            }
            
            if ( ! $errorMsg
                && $resource->save()
                && ! empty( $metadataList )
                || ! empty( $newMetadata ) )
            {
                $metadata = new Metadata( $database , $resource->getId() );
                
                foreach( $metadataList as $property => $value )
                {
                    $metadata->add( $property , $value );
                }
                
                if ( ! empty( $newMetadata ) )
                {
                    foreach( $newMetadata as $id => $name )
                    {
                        if ( $name )
                        {
                            $metadata->add( $name , $newValue[ $id ] );
                        }
                    }
                }
            }
            
            $execution_ok = ! $errorMsg
                           && $resourceSet->add( $resource->getId() );
            break;
        }
        
        case 'exEditResource':
        {
            $title = $userInput->get( 'title' );
            $description = $userInput->get( 'description' );
            $metadataList = $userInput->get( 'metadata' );
            $toDelete = $userInput->get( 'del' );
            $newMetadata = $userInput->get( 'name' );
            $newValue = $userInput->get( 'value' );
            
            $resource->setTitle( $title );
            $resource->setDescription( $description );
            
            if ( ! empty( $metadataList ) )
            {
                foreach( $metadataList as $id => $value )
                {
                    $metadata->modify( $id , $value );
                }
            }
            
            if ( ! empty( $newMetadata ) )
            {
                foreach( $newMetadata as $id => $name )
                {
                    if ( $name )
                    {
                        $metadata->add( $name , $newValue[ $id ] );
                    }
                }
            }
            
            if ( ! empty( $toDelete ) )
            {
                foreach( array_keys( $toDelete ) as $id )
                {
                    $metadata->remove( $id );
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
            if ( $librarian->isLibrarian( $librarianId )
              && count( $resourceSet->getLibrarianList( $libraryId ) ) == 1 )
            {
                $errorMsg = get_lang( 'A library must have at least one librarian' );
            }
            
            $execution_ok = ! $errorMsg
                           && $librarian->unregister( $librarianId );
            break;
        }
        
        case 'rqShowLibrarian':
        {
            $searchString = $userInput->get( 'searchString' );
            $searchResult = array();
            
            if ( $option == 'add' && $searchString )
            {
                $searchResult = searchUser( $userInput->get( 'searchString' ) );
            }
            break;
        }
        
        case 'exAddLibrarian':
        {
            $userToAdd = $userInput->get( 'userId' );
            $execution_ok = $librarian->register( $userToAdd );
            break;
        }
        
        case 'rqQSearch':
        {
            $searchString = $userInput->get( 'searchString' );
            $searchEngine = new FulltextSearch( $database );
            $searchEngine->search( $searchString );
            $searchEngine->bake();
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
    
    if ( $resource->getId() )
    {
        $pageTitle[ 'subTitle' ] = $resource->getTitle();
    }
    else
    {
        $pageTitle[ 'subTitle' ] = ucwords( get_lang( $context ) )
                                 . ( $libraryId ? ' - ' . $library->getTitle() : '' );
    }
    
    $template = new ModuleTemplate( 'CLLIBR' , strtolower( $context ) . '.tpl.php' );
    $template->assign( 'edit_allowed' , $edit_allowed );
    $template->assign( 'resourceList' , $resourceSet->getResourceList( true ) );
    $template->assign( 'userId' , $userId );
    $template->assign( 'libraryId' , $libraryId );
    $template->assign( 'courseId' , $courseId );
    $template->assign( 'icon' , get_icon_url( 'icon' ) );
    if ( is_a( $resourceSet , 'LibraryList' ) && $libraryId )
    {
        $template->assign( 'librarianList' , $resourceSet->getLibrarianList( $libraryId ) );
    }
    
    switch( $cmd )
    {
        case 'rqShowBookmark':
        case 'rqShowBibliography';
        case 'rqShowCatalogue':
        case 'rqShowLibrarylist':
        case 'rqShowLibrarian':
        {
            break;
        }
        
        case 'rqView':
        {
            $template = new ModuleTemplate( 'CLLIBR' , 'resource.tpl.php' );
            $template->assign( 'resourceId' , $resourceId );
            $template->assign( 'storageType' , $resource->getType() );
            $template->assign( 'url' , $resource->getName() );
            $template->assign( 'title' , $resource->getTitle() );
            $template->assign( 'description' , $resource->getDescription() );
            $template->assign( 'metadataList' , $metadata->export() );
            $template->assign( 'userId' , $userId );
            $template->assign( 'libraryId' , $libraryId );
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
        case 'exDeleteResource':
        case 'exAddLibrarian':
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
        
        case 'rqCreateLibrary':
        case 'rqEditLibrary':
        {
            $form = new ModuleTemplate( 'CLLIBR' , 'editlibrary.tpl.php' );
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
            $template = new ModuleTemplate( 'CLLIBR' , 'editresource.tpl.php' );
            $template->assign( 'resourceId' , $resourceId );
            $template->assign( 'title' , $resourceId ? $resource->getTitle() : '' );
            $template->assign( 'description' , $resourceId ? $resource->getDescription() : '' );
            $template->assign( 'metadataList' , $resourceId ? $metadata->export() : array() );
            $template->assign( 'userId' , $userId );
            $template->assign( 'libraryId' , $libraryId );
            $template->assign( 'refId' , $resourceId );
            $template->assign( 'refName' , 'resourceId' );
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
            $urlCancel = 'rqShow' . $context;
            $xid = array( 'resourceId' => $resourceId );
            break;
        }
        
        case 'rqDeleteLibrary':
        {
            $msg = get_lang( 'Do you really want to delete this library?' );
            $urlAction = 'exDeleteLibrary';
            $urlCancel = 'rqShowLibrarylist';
            $xid = array( 'libraryId' => $libraryId );
            break;
        }
        
        case 'rqDeleteResource':
        {
            $msg = get_lang( 'Do you really want to delete this resource?' );
            $urlAction = 'exDelete';
            $urlCancel = 'rqShowCatalogue';
            $xid = array( 'resourceId' => $resourceId
                        , 'context' => 'catalogue'
                        , 'libraryId' => $libraryId );
            break;
        }
        
        case 'rqRemoveLibrarian':
        {
            $msg = get_lang( 'Do you really want to remove this librarian?' );
            $urlAction = 'exRemoveLibrarian';
            $urlCancel = 'rqShowLibrarian&libraryId='.$libraryId;
            $xid = array( 'librarianId' => $librarianId , 'libraryId' => $libraryId );
            break;
        }
        
        case 'rqQSearch':
        {
            $template = new ModuleTemplate( 'CLLIBR' , 'searchresult.tpl.php' );
            $template->assign( 'result' , $searchEngine->getResult() );
            break;
        }
        
        default:
        {
            throw new Exception( 'bad command' );
        }
    }
    
    if ( $option == 'add' )
    {
        $form = new ModuleTemplate( 'CLLIBR' , 'addlibrarian.tpl.php' );
        $form->assign( 'libraryId' , $libraryId );
        $form->assign( 'searchResult' , $searchResult );
        $dialogBox->form( $form->render() );
    }
    
    if ( isset( $msg ) )
    {
        $question = new ModuleTemplate( 'CLLIBR' , 'question.tpl.php' );
        $question->assign( 'msg' , $msg );
        $question->assign( 'urlAction' , $urlAction );
        $question->assign( 'urlCancel' , $urlCancel );
        $question->assign( 'xid' , $xid );
        $dialogBox->question( $question->render() );
    }
    
    if ( $resourceId && $cmd == 'rqView' )
    {
        $dcView = new DublinCore( $metadata->export() );
        ClaroHeader::getInstance()->addHtmlHeader( $dcView->render() );
    }
    
    ClaroBreadCrumbs::getInstance()->append( $pageTitle[ 'subTitle' ] );
    Claroline::getInstance()->display->body->appendContent( claro_html_tool_title( $pageTitle )
                                                            . $dialogBox->render()
                                                            . $template->render() );
}
else // FORBIDDEN ACTION
{
    $dialogBox = new DialogBox();
    $dialogBox->error( get_lang( 'Access denied' ) );
    Claroline::getInstance()->display->body->appendContent( $dialogBox->render() );
}

echo Claroline::getInstance()->display->render();