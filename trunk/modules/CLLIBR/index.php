<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.8.8 $Revision$ - Claroline 1.9
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
    'resourcetype.lib',
    'resourcetypelist.lib',
    'collection.lib',
    'storedresource.lib',
    'librarylist.lib',
    'courselibrary.lib',
    'library.lib',
    'librarian.lib',
    'metadata.lib',
    'search.lib',
    'metadataview.lib',
    'pluginloader.lib',
    'resourceview.lib',
    'acl.lib',
    'tagcloud.lib',
    'tools.lib',
    'usernote.lib' );

$claroline->currentModuleLabel( 'CLLIBR' );
load_module_config( 'CLLIBR' );
load_module_language( 'CLLIBR' );

$nameTools = get_lang( 'Online Library' );
$pageTitle = array( 'mainTitle' => get_lang( 'Online Library' ) );
$secretKey = get_conf ( 'CLLIBR_encryption_key' );
$repository = get_path( 'rootSys' ) . 'cllibrary/';
$typeRepository = get_module_path( 'CLLIBR' ) . '/lib/types/';
$pluginRepository = get_module_path( 'CLLIBR' ) . '/lib/plugins/';
$database = Claroline::getDatabase();
/* for later -->
$tableList = get_module_main_tbl( array( 'library_resource'
                                       , 'library_metadata'
                                       , 'library_library'
                                       , 'library_librarian'
                                       , 'library_collection' ) );
  for use with:
  $DBTable = new DatabaseTable( $database , $tableList[ 'library_' . $className ] );
  $class = new ClassName( $DBTable , ... );
<-- for later */

$pluginLoader = new PluginLoader( $pluginRepository );
$pluginLoader->loadPlugins();
$pluginList = $pluginLoader->getPluginList();

$resourceTypeList = new ResourceTypeList( $typeRepository );

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
                   , 'exUnbookmark'
                   , 'exNote'
                   , 'exExport'
                   , 'rqSearch' );

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
                             , 'rqAddLibrary'
                             , 'exAddLibrary'
                             , 'rqRemoveLibrary'
                             , 'rqRemoveLibrary'
                             , 'exRemoveLibrary'
                             , 'exAddResource'
                             , 'exEditResource'
                             , 'exDeleteResource'
                             , 'exMoveResource'
                             , 'exAdd'
                             , 'rqRemove'
                             , 'exRemove'
                             , 'exVisible'
                             , 'exInvisible'
                             , 'rqSearch' );

$redirectionList = array( 'exAdd'             => 'rqShowBibliography'
                        , 'exRemove'          => 'rqShowBibliography'
                        , 'exAddLibrary'      => 'rqShowBibliography'
                        , 'exRemoveLibrary'   => 'rqShowBibliography'
                        , 'exAddResource'     => 'rqEditResource'
                        , 'exDeleteResource'  => 'rqShowCatalogue'
                        , 'exMoveResource'    => 'rqShowCatalogue'
                        , 'exCreateLibrary'   => 'rqShowCatalogue'
                        , 'exEditLibrary'     => 'rqShowLibrarylist'
                        , 'exDeleteLibrary'   => 'rqShowLibrarylist'
                        , 'exAddLibrarian'    => 'rqShowLibrarian'
                        , 'exRemoveLibrarian' => 'rqShowLibrarian'
                        , 'exEditResource'    => 'rqView'
                        , 'exNote'            => 'rqView'
                        , 'exBookmark'        => ''
                        , 'exUnbookmark'      => '' );

// OBJECTS INITIALISATION
$userInput = Claro_UserInput::getInstance();
$cmd = $userInput->get( 'cmd' , $courseId ? 'rqShowBibliography' : 'rqShowLibrarylist' );
$option = $userInput->get( 'option' );
$sort = $userInput->get( 'sort' , 'title' );
$libraryId = $userInput->get( 'libraryId' );
$librarianId = $userInput->get( 'librarianId' );
$resourceId = $userInput->get( 'resourceId' );
$resourceList = $resourceId
              ? array( $resourceId => 'on' )
              : $userInput->get( 'resource' );

$library = new Library( $database , $libraryId );
$resource = new Resource( $database , $resourceId );
$metadata = new Metadata( $database , $resourceId );

if ( $libraryId )
{
    $librarian = new Librarian( $database , $libraryId );
}

if ( $courseId )
{
    $courseLibraryList = new CourseLibrary( $database , $courseId );
}

if ( $userId && $resourceId )
{
    $userNote = new UserNote( $database , $userId , $resourceId );
}

// SETTING CONTEXT
$is_deleted = $resource->isDeleted();
$refId = null;

if ( substr( $cmd , 0 , 6 ) == 'rqShow' )
{
    $context = strtolower( substr( $cmd , 6 ) );
}
elseif( $cmd == 'exUnbookmark' || $cmd == 'exBookmark' )
{
    $context = 'bookmark';
}
elseif( $librarianId || $cmd == 'exAddLibrarian' )
{
    $context = 'librarian';
}
elseif( $libraryId && $cmd != 'exAddLibrary' && $cmd != 'exRemoveLibrary' )
{
    $context = 'catalogue';
}
elseif( $courseId && $cmd != 'rqCreateLibrary' )
{
    $context = 'bibliography';
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
    $access_allowed = $access_allowed
                   || $librarian->isLibrarian( (int)$userId )
                   || $library->getStatus() != Library::LIB_PRIVATE
                   || $courseLibraryList->libraryExists( $libraryId );
    $edit_allowed = $edit_allowed || $librarian->isLibrarian( (int)$userId );
}
else
{
    $access_allowed = true;
    $edit_allowed = $is_course_creator;
}

if ( $refId )
{
    $resourceSet = new Collection( $database , $context , $refId );
}
else
{
    $resourceSet = new LibraryList( $database , $userId , $is_platform_admin );
}

$acl = new CLLIBR_ACL( $database , $userId , $is_course_creator , $is_platform_admin );

if ( $resourceId )
{
    $access_allowed = $acl->accessGranted( $resourceId );
    $edit_allowed = $acl->editGranted( $resourceId );
}

$accessTicket = ( $access_allowed && in_array( $cmd , $actionList ) )
             || ( $edit_allowed && in_array( $cmd , $restrictedActionList ) );

$tagCloud = new TagCloud( $database );

if ( $accessTicket ) // AUTHORIZED ACTION
{
    // CONTROLLER
    $errorMsg = false;
    
    switch( $cmd )
    {
        case 'rqShowBookmark':
        case 'rqShowBibliography';
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
        case 'rqRemoveLibrary':
        {
            break;
        }
        
        case 'exBookmark':
        {
            $bookmark = new Collection( $database , 'bookmark' , $userId );
            
            foreach( array_keys( $resourceList ) as $resourceId )
            {
                if ( ! $bookmark->resourceExists( $resourceId ) )
                {
                    $bookmark->add( $resourceId );
                    $userNote = new UserNote( $database , $userId , $resourceId );
                    $userNote->create();
                }
                else
                {
                    $errorMsg = count( $resourceList ) == 1
                              ? get_lang( 'This resource is already bookmarked' )
                              : get_lang( 'Some of your selected resources were already bookmarked' );
                }
            }
            
            $execution_ok = ! $errorMsg;
            break;
        }
        
        case 'exNote':
        {
            $content = $userInput->get( 'content' );
            $userNote = new UserNote( $database , $userId , $resourceId );
            $userNote->set( $content );
            break;
        }
        
        case 'exAdd':
        {
            $bibliography = new Collection( $database , 'bibliography' , $courseId );
            
            foreach( array_keys( $resourceList ) as $resourceId )
            {
                if ( ! $bibliography->resourceExists( $resourceId ) )
                {
                    $bibliography->add( $resourceId );
                }
                else
                {
                    $errorMsg = count( $resourceList ) == 1
                              ? get_lang( 'This resource is already added' )
                              : get_lang( 'Some of your selected resources were already added' );
                }
            }
            
            $execution_ok = ! $errorMsg;
            break;
        }
        
        case 'exRemove':
        case 'exUnbookmark':
        {
            $execution_ok = true;
            
            foreach( array_keys( $resourceList ) as $resourceId )
            {
                $execution_ok = $execution_ok
                            && $resourceSet->remove( $resourceId );
            }
            
            if ( $resourceSet->getType() == Collection::USER_COLLECTION )
            {
                $userNote = new UserNote( $database , $userId , $resourceId );
                $userNote->delete();
                
                $collectionList = $resourceSet->getCollectionList( $resourceId );
                
                if ( ! isset( $collectionList[ Collection::USER_COLLECTION ] ) )
                {
                    $metadata->removeAll();
                }
            }
            break;
        }
        
        case 'exVisible':
        case 'exInvisible':
        {
            $execution_ok = true;
            
            foreach( array_keys( $resourceList ) as $resourceId )
            {
                $execution_ok = $execution_ok
                             && $resourceSet->setVisibility( $resourceId
                                                           , $cmd == 'exVisible' ? true : false );
            }
            break;
        }
        
        case 'exEditLibrary':
        case 'exCreateLibrary':
        {
            $title = $userInput->get( 'title' );
            $status = $userInput->get( 'status' );
            
            $library->setTitle( $title );
            $library->setStatus( $status );
            $librarian = new Librarian( $database , $library->save() );
            $resourceSet = new LibraryList( $database , $userId , $is_platform_admin );
            $context = 'librarylist';
            $execution_ok = $librarian->isLibrarian( $userId )
                         || $librarian->register( $userId );
            break;
        }
        
        case 'exDeleteLibrary':
        {
            $library = new Library( $database , $libraryId );
            $resourceSet = new LibraryList( $database , $userId , $is_platform_admin );
            $context = 'librarylist';
            $execution_ok = $resourceSet->resourceExists( $libraryId )
                         && $library->delete();
            break;
        }
        
        case 'exAddResource':
        {
            $type = $userInput->get( 'type' );
            $storage = $userInput->get( 'storage' );
            $title = $userInput->get( 'title' );
            $description = $userInput->get( 'description' )
                         ? $userInput->get( 'description' )
                         : get_lang( 'no description' );
            
            $resourceType = $resourceTypeList->get( $type );
            
            if ( $resourceType && $title )
            {
                $authorizedFileList = $resourceType->getAuthorizedFileList();
                $resource = new Resource( $database );
                $metadata = new Metadata( $database );
                
                $resource->setType( $type );
                $resource->setStorageType( $storage );
                $resource->setDate();
                
                if ( $storage == 'file' )
                {
                    $storedResource = new StoredResource( $repository , $authorizedFileList , $resource , $secretKey );
                    
                    if ( $_FILES && $_FILES[ 'uploadedFile' ][ 'size' ] != 0 )
                    {
                        $file = $_FILES[ 'uploadedFile' ];
                    }
                    else
                    {
                        $errorMsg = get_lang( 'File missing' );
                    }
                    
                    if ( ! $errorMsg
                      && $storedResource->validate( $file[ 'name' ] ) )
                    {
                        $resource->setName( $file[ 'name' ] );
                    }
                    else
                    {
                        $errorMsg = get_lang( 'Invalid file' );
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
                        $errorMsg = get_lang( 'Url missing' );
                    }
                }
            }
            else
            {
                $errorMsg = get_lang( 'You must fill all the fields' );
                $cmd = 'rqAddResource';
            }
            
            $execution_ok = ! $errorMsg
                           && $resourceSet->add( $resource->save() )
                           && $metadata->setResourceId( $resource->getId() )
                           && $metadata->setTitle( $title )
                           && $metadata->setDescription( $description );
            break;
        }
        
        case 'exEditResource':
        {
            $title = $userInput->get( 'title' );
            $description = $userInput->get( 'description' );
            $names = $userInput->get( 'name' );
            $values = $userInput->get( 'metadata' );
            $toDelete = $userInput->get( 'del' );
            $newNames = $userInput->get( 'newName' );
            $newValues = $userInput->get( 'value' );
            $keyword = $userInput->get( 'keyword' );
            $newKeyword = $userInput->get( 'keywords' );
            $kToDelete = $userInput->get( 'kdel' );
            
            $keywords = is_array( $keyword ) ? $keyword : array( $keyword );
            $newKeywords = explode( ',' , $newKeyword );
            
            if ( $title )
            {
                $metadata->setTitle( $title );
            }
            
            if ( $description )
            {
                $metadata->setDescription( $description );
            }
            
            if ( is_array( $names ) )
            {
                foreach( $names as $id => $name )
                {
                    if ( $values[ $id ] )
                    {
                        $metadata->set( $name , $values[ $id ] );
                    }
                    else
                    {
                        $metadata->remove( $name );
                    }
                }
            }
            
            if ( is_array( $newNames ) )
            {
                foreach( $newNames as $id => $newName )
                {
                    if ( $newValues[ $id ] )
                    {
                        $metadata->add( $newName , $newValues[ $id ] );
                    }
                }
            }
            
            if ( is_array( $toDelete ) )
            {
                foreach( $toDelete as $name )
                {
                    $metadata->remove( $name );
                }
            }
            
            if ( is_array( $keyword ) )
            {
                foreach( $metadata->getKeywordList( true ) as $value )
                {
                    if ( is_array( $keywords ) && ! array_key_exists( $value , $keywords ) )
                    {
                        $metadata->removeKeyword( $value );
                    }
                }
            }
            
            if ( is_array( $keywords ) )
            {
                foreach( $keywords as $value )
                {
                    if ( $value && ! $metadata->keywordExists( $value ) )
                    {
                        $metadata->addKeyword( $value );
                    }
                }
            }
            
            if ( is_array( $kToDelete ) )
            {
                foreach( $kToDelete as $value )
                {
                    $metadata->removeKeyword( $value );
                }
            }
            
            if( is_array( $newKeywords ) )
            {
                foreach( $newKeywords as $value )
                {
                    if ( $value )
                    {
                        $metadata->addKeyword( trim( $value ) );
                    }
                }
            }
            
            $execution_ok = $resource->save();
            break;
        }
        
        case 'exDeleteResource':
        {
            $resource = new Resource( $database , $resourceId );
            $catalogue = new Collection( $database , 'catalogue' , $libraryId );
            
            $execution_ok = $catalogue->removeResource( $resourceId )
                         && $resource->delete();
            
            if( $execution_ok )
            {
                $collectionList = $resourceSet->getCollectionList( $resourceId );
                
                if ( isset( $collectionList[ Collection::USER_COLLECTION ] ) )
                {
                    $metadata->remove( Metadata::KEYWORD );
                }
                else
                {
                    $metadata->removeAll();
                }
            }
            
            if ( $execution_ok && $resource->getStorageType() == 'file' )
            {
                $storedResource = new StoredResource( $repository , null , $resource , $secretKey );
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
        
        case 'rqShowCatalogue':
        {
            if ( $option == 'move' )
            {
                $libraryList = new LibraryList( $database , $userId , $edit_allowed );
            }
            break;
        }
        
        case 'exAddLibrarian':
        {
            $userToAdd = $userInput->get( 'userId' );
            $execution_ok = $librarian->register( $userToAdd );
            break;
        }
        
        case 'exAddLibrary':
        {
            $execution_ok = $courseLibraryList->add( $libraryId );
            break;
        }
        
        case 'exRemoveLibrary':
        {
            $execution_ok = $courseLibraryList->remove( $libraryId );
            break;
        }
        
        case 'rqSearch':
        {
            $searchString = $userInput->get( 'searchString' );
            $keyword = $userInput->get( 'keyword' );
            $searchQuery = $userInput->get( 'searchQuery' );
            if ( $keyword )
            {
                $searchEngine = new KeywordSearch( $database );
                $searchEngine->search( $keyword );
            }
            elseif ( $searchQuery )
            {
                $searchEngine = new MultiSearch( $database );
                $searchEngine->search( $searchQuery );
            }
            else
            {
                $searchEngine = new FulltextSearch( $database );
                $searchEngine->search( $searchString );
            }
            
            $searchEngine->bake();
            break;
        }
        
        case 'exExport':
        {
            $exporter = new DublinCore( $metadata->getMetadataList() );
            $url = htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                 . '/index.php?cmd=rqView&resourceId=' . $resourceId ) );
            $fileName = 'RDF_metadata_resource_' . $resourceId . '.rdf';
            header("Content-type: application/xml");
            header('Content-Disposition: attachment; filename="' . $fileName );
            echo claro_utf8_encode( $exporter->export( $url ) );
            exit();
        }
        
        default:
        {
            throw new Exception( 'bad command' );
        }
    }
    
    // VIEW
    CssLoader::getInstance()->load( 'cllibr' , 'screen' );
    
    $dialogBox = new DialogBox();
    $warning = new DialogBox();
    
    if ( $metadata->getResourceId() )
    {
        $pageTitle[ 'subTitle' ] = $metadata->get( Metadata::TITLE );
    }
    elseif ( $cmd == 'rqSearch' )
    {
        $pageTitle[ 'subTitle' ] = get_lang( 'Search result' );
    }
    else
    {
        $pageTitle[ 'subTitle' ] = get_lang( $context )
                                 . ( $libraryId ? ' - ' . $library->getTitle() : '' );
    }
    
    $template = new ModuleTemplate( 'CLLIBR' , strtolower( $context ) . '.tpl.php' );
    $template->assign( 'edit_allowed' , $edit_allowed );
    $template->assign( 'is_platform_admin' , $is_platform_admin );
    $template->assign( 'resourceList' , $resourceSet->getResourceList( true , $sort ) );
    $template->assign( 'userId' , $userId );
    $template->assign( 'libraryId' , $libraryId );
    $template->assign( 'courseId' , $courseId );
    $template->assign( 'icon' , get_icon_url( 'icon' ) );
    $template->assign( 'tagCloud' , $tagCloud->render() );
    if ( is_a( $resourceSet , 'LibraryList' ) && $libraryId )
    {
        $template->assign( 'librarianList' , $resourceSet->getLibrarianList( $libraryId ) );
    }
    if ( $courseId )
    {
        $template->assign( 'courseLibraryList' , $courseLibraryList->getLibraryList( true ) );
    }
    
    if ( array_key_exists( $cmd , $redirectionList ) )
    {
        $cmd = $redirectionList[ $cmd ];
    }
    
    if ( isset( $execution_ok ) )
    {
        $execution_ok ? $dialogBox->success( get_lang( 'Success' ) )
                      : $dialogBox->error( $errorMsg ? $errorMsg : get_lang( 'Error' ) );
    }
    
    $cmdList = array();
    
    if ( $userId )
    {
        $cmdList[] = array( 'img'  => 'icon',
                            'name' => get_lang( 'Libraries' ),
                            'url'  => htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                      .'/index.php?cmd=rqShowLibrarylist' ) ) );
        $cmdList[] = array( 'img'  => 'bookmark',
                            'name' => get_lang( 'My bookmark' ),
                            'url'  => htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                      .'/index.php?cmd=rqShowBookmark') ) );
    }
    
    switch( $cmd )
    {
        case 'rqShowBibliography';
        case 'rqShowBookmark':
        case 'exVisible':
        case 'exInvisible':
        case '';
        {
            break;
        }
        
        case 'rqShowCatalogue':
        {
            if ( $edit_allowed )
            {
                $cmdList[] = array( 'img'  => 'librarian',
                                    'name' => get_lang( 'Manage librarians' ),
                                    'url'  => htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                              .'/index.php?cmd=rqShowLibrarian&libraryId='
                                              . $libraryId ) ) );
                $cmdList[] = array( 'img'  => 'new_book',
                                    'name' => get_lang( 'Add a resource' ),
                                    'url'  => htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                              .'/index.php?cmd=rqAddResource&libraryId='
                                              . $libraryId ) ) );
            }
            
            if ( $edit_allowed && $courseId && ! $courseLibraryList->libraryExists( $libraryId ) )
            {
                $cmdList[] = array( 'img'  => 'add',
                                    'name' => get_lang( 'Link this library to your course\'s bibliography' ),
                                    'url'  => htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                              .'/index.php?cmd=exAddLibrary&libraryId='
                                              . $libraryId ) ) );
            }
            break;
        }
        
        case 'rqShowLibrarylist':
        {
            if ( $edit_allowed )
            {
                $cmdList[] = array( 'img'  => 'new_book',
                                    'name' => get_lang( 'Create a new library' ),
                                    'url'  => htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                              .'/index.php?cmd=rqCreateLibrary' ) ) );
            }
            break;
        }
        
        case 'rqShowLibrarian':
        {
            $cmdList[] = array( 'img'  => 'back',
                                'name' => get_lang( 'Back to the catalogue' ),
                                'url'  => htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                          .'/index.php?cmd=rqShowCatalogue&libraryId=' . $libraryId ) ) );
            $cmdList[] = array( 'img'  => 'add_librarian',
                                'name' => get_lang( 'Add a librarian' ),
                                'url'  => htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                          .'/index.php?cmd=rqShowLibrarian&option=add&libraryId=' . $libraryId ) ) );
            break;
        }
        
        case 'rqView':
        case 'exExport':
        {
            $is_validated = false;
            
            if ( ! $is_deleted )
            {
                $viewName = $resource->getType() . 'View';
                
                if ( array_key_exists( 'resourceview' , $pluginList )
                  && in_array( strtolower( $viewName ) , $pluginList[ 'resourceview' ] ) )
                {
                    $resourceViewer = new $viewName( new StoredResource( $repository
                                                                       , null
                                                                       , $resource
                                                                       , $secretKey ) );
                    
                    $is_validated = $resourceViewer->validate( StoredResource::getFileExtension( $resource->getName() ) );
                }
            }
            
            $type = $resource->getType();
            
            $template = new ModuleTemplate( 'CLLIBR' , 'resource.tpl.php' );
            $template->assign( 'resourceId' , $resourceId );
            $template->assign( 'storageType' , $resource->getStorageType() );
            $template->assign( 'resourceType' , $type );
            $template->assign( 'defaultMetadataList' , $resourceTypeList->get( $type )->getDefaultMetadataList() );
            $template->assign( 'url' , $resource->getName() );
            $template->assign( 'metadataList' , $metadata->getMetadataList( true ) );
            $template->assign( 'userId' , $userId );
            $template->assign( 'libraryId' , $libraryId );
            $template->assign( 'courseId' , $courseId );
            $template->assign( 'edit_allowed' , $acl->editGranted( $resourceId ) );
            $template->assign( 'read_allowed' , $acl->accessGranted( $resourceId , CLLIBR_ACL::ACCESS_READ ) );
            $template->assign( 'viewer' , $is_validated ? $resourceViewer : false );
            $template->assign( 'userNote' , isset( $userNote ) && $userNote->noteExists() ? $userNote->getContent() : null );
            $template->assign( 'is_deleted' , $is_deleted );
            
            if ( $libraryId )
            {
                $cmdList[] = array( 'img'  => 'back',
                                    'name' => get_lang( 'Back to the catalogue' ),
                                    'url'  => htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                              .'/index.php?cmd=rqShowCatalogue&libraryId=' . $libraryId ) ) );
            }
            
            if ( $edit_allowed )
            {
                $cmdList[] = array( 'img'  => 'book',
                                    'name' => get_lang( 'Add to the course\'s bibliography' ),
                                    'url'  => htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                              .'/index.php?cmd=exAdd&resourceId=' . $resourceId ) ) );
                $cmdList[] = array( 'img'  => 'edit',
                                    'name' => get_lang( 'Edit resource\'s metadatas' ),
                                    'url'  => htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                              .'/index.php?cmd=rqEditResource&resourceId=' . $resourceId ) ) );
            }
            
            if ( $userId )
            {
                $cmdList[] = array( 'img'  => 'bookmark',
                                    'name' => get_lang( 'Add to my bookmark' ),
                                    'url'  => htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                              .'/index.php?cmd=exBookmark&resourceId=' . $resourceId ) ) );
            }
            
            $cmdList[] = array( 'img'  => 'export',
                                'name' => get_lang( 'Export metadatas in RDF format' ),
                                'url'  => htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                          .'/index.php?cmd=exExport&resourceId=' . $resourceId ) ) );
            break;
        }
        
        case 'rqDownload':
        {
            $storedResource = new StoredResource( $repository , null , $resource , $secretKey );
            $storedResource->getFile();
            break;
        }
        
        case 'rqCreateLibrary':
        case 'rqEditLibrary':
        {
            $form = new ModuleTemplate( 'CLLIBR' , 'editlibrary.tpl.php' );
            $form->assign( 'userId' , $userId );
            $form->assign( 'libraryId' , $libraryId );
            $form->assign( 'title' , $library->getTitle() );
            $form->assign( 'status' , $library->getStatus() );
            $dialogBox->form( $form->render() );
            break;
        }
        
        case 'rqAddResource':
        {
            $pageTitle[ 'subTitle' ] = get_lang( 'Add a resource' );
            $template = new ModuleTemplate( 'CLLIBR' , 'addresource.tpl.php' );
            $template->assign( 'userId' , $userId );
            $template->assign( 'libraryId' , $libraryId );
            $template->assign( 'typeList' , $resourceTypeList->getResourceTypeList() );
            $template->assign( 'urlAction' , 'ex' . substr( $cmd , 2 ) );
            break;
        }
        
        case 'rqEditResource':
        {
            $resourceId = $resource->getId();
            $tagCloud = new TagCloud( $database
                                    , 'index.php?cmd=exEditResource&resourceId=' . $resourceId );
            $type = $resource->getType();
            $resource = new Resource( $database , $resourceId );
            
            $pageTitle[ 'subTitle' ] = get_lang( 'Edit resource' );
            $template = new ModuleTemplate( 'CLLIBR' , 'editresource.tpl.php' );
            $template->assign( 'resourceId' , $resourceId );
            $template->assign( 'metadataList' , $metadata->getMetadataList( true ) );
            $template->assign( 'userId' , $userId );
            $template->assign( 'libraryId' , $libraryId );
            $template->assign( 'refId' , $resourceId );
            $template->assign( 'refName' , 'resourceId' );
            $template->assign( 'defaultMetadataList' , $resourceTypeList->get( $type )->getDefaultMetadataList() );
            $template->assign( 'urlAction' , 'ex' . substr( $cmd , 2 ) );
            $template->assign( 'propertyList' , $metadata->getAllProperties() );
            $template->assign( 'tagCloud' , $tagCloud->render() );
            break;
        }
        
        case 'rqRemove':
        {
            $msg = get_lang( 'Do you really want to remove this resource?' );
            $urlAction = 'exRemove';
            $urlCancel = 'rqShow' . ucwords( $context );
            $xid = array( 'resourceId' => $resourceId );
            break;
        }
        
        case 'rqRemoveLibrary':
        {
            $msg = get_lang( 'Do you really want to remove this library?' );
            $urlAction = "exRemoveLibrary";
            $urlCancel = 'rqShowBibliography';
            $xid = array( 'libraryId' => $libraryId );
            break;
        }
        
        case 'rqDeleteLibrary':
        {
            if ( $resourceSet->getResourceList() )
            {
                $warningMsg = '<strong>'
                            . get_lang( 'Warning : this library is not empty! This will delete all its resources, along with the associated links in bookmarks and bibliographies!' )
                            .  '</strong>';
            }
            
            $msg = get_lang( 'Do you really want to delete this library?' );
            $urlAction = 'exDeleteLibrary';
            $urlCancel = 'rqShowLibrarylist';
            $xid = array( 'libraryId' => $libraryId );
            break;
        }
        
        case 'rqDeleteResource':
        {
            $collectionList = $resourceSet->getCollectionList( $resourceId );
            
            if ( isset( $collectionList[ 'bibliography' ] ) || isset( $collectionList[ 'bookmark' ] ) )
            {
                $warningMsg = '<strong>' . get_lang( 'Warning : this resource is in use' ) . ': <br /><br />';
                $warningMsg .= isset( $collectionList[ 'bibliography' ] )
                            ?'- ' . count( $collectionList[ 'bibliography' ] ) . ' ' . get_lang( 'bibliographies' ) . '<br />'
                            : '';
                $warningMsg .= isset( $collectionList[ 'bookmark' ] )
                            ? '- ' . count( $collectionList[ 'bookmark' ] ) . ' ' . get_lang( 'bookmarks' ) . '<br />'
                            : '';
                $warningMsg .= '<br />'
                            .  get_lang( 'It\'s not advised to remove this resource unless you are sure it will not cause problems!' )
                            .  '</strong>';
            }
            
            $msg = get_lang( 'Do you really want to delete this resource?' );
            $urlAction = 'exDeleteResource';
            $urlCancel = 'rqShowCatalogue&libraryId='.$libraryId;
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
        
        case 'rqSearch':
        {
            $searchResult = $searchEngine->getResult();
            
            foreach( $searchResult as $score => $results )
            {
                foreach( $results as $id => $result )
                {
                    if ( ! $acl->accessGranted( $id , CLLIBR_ACL::ACCESS_SEARCH ) )
                    {
                        unset( $searchResult[ $score ][ $id ] );
                    }
                }
            }
            
            $tpl = is_a( $searchEngine , 'KeywordSearch' )
                 ? 'keyword.tpl.php'
                 : 'searchresult.tpl.php';
            $template = new ModuleTemplate( 'CLLIBR' , $tpl );
            
            $template->assign( 'result' , $searchResult );
            $template->assign( 'tagCloud' , $tagCloud->render() );
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
    
    if ( $option == 'move' )
    {
        $form = new ModuleTemplate( 'CLLIBR' , 'moveresource.tpl.php' );
        $form->assign( 'resourceId' , $resourceId );
        $form->assign( 'libraryList' , $libraryList->getResourceList( true ) );
        $dialogBox->form( $form->render() );
    }
    
    if ( $option == 'multisearch' )
    {
        $form = new ModuleTemplate( 'CLLIBR' , 'multisearch.tpl.php' );
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
    
    if ( isset( $warningMsg ) )
    {
        $warning->error( $warningMsg );
    }
    
    if ( $resourceId && $cmd == 'rqView' )
    {
        $dcView = new DublinCore( $metadata->getMetadataList() );
        ClaroHeader::getInstance()->addHtmlHeader( $dcView->render() );
    }
    
    ClaroBreadCrumbs::getInstance()->append( $pageTitle[ 'subTitle' ] );
    Claroline::getInstance()->display->body->appendContent( claro_html_tool_title( $pageTitle , null , $cmdList )
                                                            . $warning->render()
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