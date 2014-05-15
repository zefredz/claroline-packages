<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 1.1.5 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
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
    'metadataexport.lib',
    'pluginloader.lib',
    'resourceview.lib',
    'acl.lib',
    'tagcloud.lib',
    'tools.lib',
    'usernote.lib' );

set_current_module_label( 'CLLIBR' );
load_module_config( 'CLLIBR' );
load_module_language( 'CLLIBR' );
$dialogBox = new DialogBox();

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

try
{
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
                       , 'exCite'
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
                                 , 'exRemoveLibrary'
                                 , 'exAddResource'
                                 , 'exEditResource'
                                 , 'exDeleteResource'
                                 , 'rqMoveResource'
                                 , 'exMoveResource'
                                 , 'rqUpdateResource'
                                 , 'exUpdateResource'
                                 , 'exAdd'
                                 , 'rqRemove'
                                 , 'exRemove'
                                 , 'exVisible'
                                 , 'exInvisible'
                                 , 'rqShowResourceType'
                                 , 'rqAddResourceType'
                                 , 'rqEditResourceType'
                                 , 'exEditResourceType'
                                 , 'rqDeleteResourceType'
                                 , 'exDeleteResourceType' );
    
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
                            , 'exUpdateResource'  => 'rqView'
                            , 'exNote'            => 'rqView'
                            , 'exBookmark'        => ''
                            , 'exUnbookmark'      => ''
                            , 'exEditResourceType' => 'rqShowResourceType'
                            , 'exDeleteResourceType' => 'rqShowResourceType' );
    
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
    $typeName = $userInput->get( 'typeName' );
    
    $resourceType = $typeName ? $resourceTypeList->get( $typeName ) : false;
    
    $library = new Library( $database , $libraryId );
    $resource = new Resource( $database , $resourceId );
    
    if( $cmd != 'rqDeleteResource' && $cmd != 'rqMove' )
    {
        $metadata = new Metadata( $database , $resourceId );
        
        $exporterList = array();
        
        if ( array_key_exists( 'metadataview' , $pluginList ) )
        {
            foreach( $pluginList[ 'metadataview' ] as $pluginName )
            {
                $exporter = new $pluginName( $metadata->getMetadataList() );
                
                if ( is_a( $exporter , 'MetadataExport' ) )
                {
                    $exporterList[ $pluginName ] = $exporter;
                }
            }
        }
    }
    
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
    elseif( $typeName || $cmd == 'rqAddResourceType' )
    {
        $context = 'resourcetype';
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
    elseif( $context == 'librarylist' )
    {
        $access_allowed = true;
        $edit_allowed = $is_course_creator;
    }
    else
    {
        $access_allowed = true;
        $edit_allowed = $is_platform_admin;
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
            case 'rqShowBibliography':
            case 'rqShowCatalogue':
            case 'rqShowLibrarylist':
            case 'rqDeleteLibrary':
            case 'rqAddLibrary':
            case 'rqView':
            case 'rqDownload':
            case 'rqAddResource':
            case 'rqEditResource':
            case 'rqMoveResource':
            case 'rqDeleteResource':
            case 'rqUpdateResource' :
            case 'rqRemove':
            case 'rqCreateLibrary':
            case 'rqEditLibrary':
            case 'rqRemoveLibrarian':
            case 'rqRemoveLibrary':
            case 'rqShowResourceType':
            case 'rqEditResourceType':
            case 'rqDeleteResourceType':
            {
                break;
            }
            
            case 'exBookmark':
            {
                if( $userId )
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
                }
                else $errorMsg = get_lang( 'Bookmarking is not allowed to anonymous users' );
                
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
                    
                    if ( empty( $collectionList ) )
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
                    
                    $resource->setSubmitterId( $userId );
                    $resource->setType( $type );
                    $resource->setStorageType( $storage );
                    $resource->setDate();
                    
                    if ( $storage == 'file' )
                    {
                        $storedResource = new StoredResource( $repository , $resource , $authorizedFileList , $secretKey );
                        
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
                               && $metadata->setType( $type )
                               && $metadata->setTitle( $title )
                               && $metadata->setDescription( $description );
                break;
            }
            
            case 'exUpdateResource':
            {
                $execution_ok = false;
                
                if( $acl->deletionGranted( $resourceId ) )
                {
                    if ( $resource->getStorageType() == Resource::TYPE_FILE )
                    {
                        $resourceType = $resourceTypeList->get( $resource->getType() );
                        $authorizedFileList = $resourceType->getAuthorizedFileList();
                        $storedResource = new StoredResource( $repository , $resource , $authorizedFileList , $secretKey );
                        
                        if ( $_FILES && $_FILES[ 'uploadedFile' ][ 'size' ] != 0 )
                        {
                            $file = $_FILES[ 'uploadedFile' ];
                        }
                        else
                        {
                            $errorMsg = get_lang( 'File missing' );
                        }
                        
                        if ( ! $errorMsg
                          && ! $storedResource->validate( $file[ 'name' ] ) )
                        {
                            $errorMsg = get_lang( 'Invalid file' );
                        }
                        
                        if ( ! $errorMsg
                          && ! $storedResource->update( $file ) )
                        {
                            $errorMsg = get_lang( 'File cannot be stored' );
                        }
                    }
                    elseif( $resource->getStorageType() == Resource::TYPE_URL )
                    {
                        $resourceName = $userInput->get( 'resourceUrl' );
                        
                        if ( $resourceName )
                        {
                            $resource->updateResource( $resourceName );
                        }
                        else
                        {
                            $errorMsg = get_lang( 'Url missing' );
                        }
                    }
                    
                    $execution_ok = ! $errorMsg
                                   && $resource->save();
                }
                break;
            }
            
            case 'exEditResource':
            {
                $type = $userInput->get( 'type' );
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
                
                $resource->setType( $type );
                
                if( $title )
                {
                    $metadata->setTitle( $title );
                }
                
                if( $description )
                {
                    $metadata->setDescription( $description );
                }
                
                if( $type )
                {
                    $metadata->setType( $type );
                }
                
                if( is_array( $names ) )
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
            
            case 'exMoveResource':
            {
                $execution_ok = false;
                
                if( isset( $librarian) && $librarian->isLibrarian( $userId ) )
                {
                    $execution_ok = true;
                    
                    foreach( array_keys( $resourceList ) as $resourceId )
                    {
                        $execution_ok = $execution_ok && $resourceSet->moveResource( $resourceId , $libraryId );
                    }
                }
                break;
            }
            
            case 'exDeleteResource':
            {
                $execution_ok = false;
                
                if( $acl->deletionGranted( $resourceId ) )
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
                        $storedResource = new StoredResource( $repository , $resource , null , $secretKey );
                        $execution_ok = $storedResource->delete();
                    }
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
            
            case 'exAddLibrary':
            {
                $title = $userInput->get( 'title' );
                $execution_ok = claro_is_allowed_to_edit() && $courseLibraryList->add( $libraryId , $title );
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
                $exportFormat = $userInput->get( 'format' )
                              ? $userInput->get( 'format' )
                              : 'DublinCore';
                if ( array_key_exists( $exportFormat , $exporterList ) )
                {
                    $url = claro_htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                         . '/index.php?cmd=rqView&resourceId=' . $resourceId ) );
                    $fileName = $exporterList[ $exportFormat ]->getFileName( $resourceId );
                    header("Content-type: " . StoredResource::getMimeType( $fileName ) );
                    header('Content-Disposition: attachment; filename="' . $fileName . '"');
                    echo $exporterList[ $exportFormat ]->export( $url );
                    exit();
                }
            }
            
            case 'exCite' :
            {
                $exporter = new BibliographicNote( $metadata->getMetadataList() );
                $exporter->setType( $resource->getType() );
                $cite = $exporter->render();
                break;
            }
            
            case 'rqAddResourceType':
            {
                $resourceType = new ResourceType();
                break;
            }
            
            case 'exEditResourceType':
            {
                $extensionList = explode( ',' , $userInput->get( 'extensions' ) );
                $nameList = $userInput->get( 'name' );
                $typeList = $userInput->get( 'type' );
            
                $fileName = $typeRepository . 'type.' . preg_replace( '/ /' , '_' , $typeName ) . '.xml';
                
                $resourceType = new ResourceType( $resourceTypeList->get( $typeName ) ? $fileName : null );
                
                $resourceType->delete();
                $resourceType->wipe();
                
                $resourceType->setName( $typeName );
                
                foreach( $extensionList as $extension )
                {
                    $resourceType->addAuthorizedFile( trim( $extension ) );
                }
                
                foreach( $nameList as $index => $name )
                {
                    $resourceType->addMetadata( $name , $typeList[ $index ] );
                }
                
                $execution_ok = $resourceType->save( $fileName );
                break;
            }
            
            case 'exDeleteResourceType':
            {
                if ( $execution_ok = $resourceType->delete() )
                {
                    $resourceTypeList->remove( $typeName );
                    $resourceType =$typeName = false;
                }
                break;
            }
            
            default:
            {
                throw new Exception( 'bad command' );
            }
        }
        
        // VIEW
        CssLoader::getInstance()->load( 'cllibr' , 'screen' );
        $cmdList = array();
        $advancedCmdList = array();
        $warning = new DialogBox();
        
        if ( isset( $metadata) && $metadata->getResourceId() )
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
        $template->assign( 'userId' , $userId );
        $template->assign( 'courseId' , $courseId );
        $template->assign( 'libraryId' , $libraryId );
        $template->assign( 'icon' , get_icon_url( 'icon' ) );
        $template->assign( 'tagCloud' , $tagCloud->render() );
        $template->assign( 'subTitle' , $pageTitle[ 'subTitle' ] );
        $template->assign( 'acl' , $acl );
        
        if( $libraryId )
        {
            $template->assign( 'is_librarian' , $librarian->isLibrarian( $libraryId ) );
        }
        
        if ( $context == 'resourcetype' )
        {
            $template->assign( 'resourceTypeList' , $resourceTypeList->getResourceTypeList() );
            $template->assign( 'typeName' , $typeName );
            $template->assign( 'edit' , false );
            
            $cmdList[] = array( 'img'  => 'new_xml',
                                'name' => get_lang( 'Add a new type' ),
                                'url'  => claro_htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                          .'/index.php?cmd=rqAddResourceType' ) ) );
        }
        else
        {
            $template->assign( 'resourceList' , $resourceSet->getResourceList( true , $sort ) );
        }
        
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
        
        if ( $userId )
        {
            $cmdList[] = array( 'img'  => 'icon',
                                'name' => get_lang( 'Libraries' ),
                                'url'  => claro_htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                          .'/index.php?cmd=rqShowLibrarylist' ) ) );
            $cmdList[] = array( 'img'  => 'bookmark',
                                'name' => get_lang( 'My bookmark' ),
                                'url'  => claro_htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                          .'/index.php?cmd=rqShowBookmark') ) );
        }
        
        if ( $resourceType )
        {
            $template->assign( 'authorizedFileList' , $resourceType->getAuthorizedFileList() );
            $template->assign( 'defaultMetadataList' , $resourceType->getDefaultMetadataList() );
            
            array_unshift( $cmdList , array( 'img'  => 'back',
                                             'name' => get_lang( 'Back to the resource type list' ),
                                             'url'  => claro_htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                                       .'/index.php?cmd=rqShowResourceType' ) ) ) );
        }
        
        switch( $cmd )
        {
            case 'rqShowBibliography';
            case 'rqShowBookmark':
            case 'rqShowResourceType':
            case 'exVisible':
            case 'exInvisible':
            case '';
            {
                break;
            }
            
            case 'rqMoveResource':
            {
                $libraryList = new LibraryList( $database , $userId , $edit_allowed );
                $form = new ModuleTemplate( 'CLLIBR' , 'moveresource.tpl.php' );
                $form->assign( 'resourceList' , $resourceList );
                $form->assign( 'libraryList' , $libraryList->getResourceList( true ) );
                $dialogBox->form( $form->render() );
            }
            
            case 'rqShowCatalogue':
            {
                if ( $edit_allowed )
                {
                    $cmdList[] = array( 'img'  => 'librarian',
                                        'name' => get_lang( 'Manage librarians' ),
                                        'url'  => claro_htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                                  .'/index.php?cmd=rqShowLibrarian&libraryId='
                                                  . $libraryId ) ) );
                    $cmdList[] = array( 'img'  => 'new_book',
                                        'name' => get_lang( 'Add a resource' ),
                                        'url'  => claro_htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                                  .'/index.php?cmd=rqAddResource&libraryId='
                                                  . $libraryId ) ) );
                }
                
                if ( claro_is_allowed_to_edit()
                    && $edit_allowed
                    && $courseId
                    && ! $courseLibraryList->libraryExists( $libraryId ) )
                {
                    $cmdList[] = array( 'img'  => 'add',
                                        'name' => get_lang( 'Link this library to your course\'s bibliography' ),
                                        'url'  => claro_htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                                  .'/index.php?cmd=rqAddLibrary&libraryId='
                                                  . $libraryId ) ) );
                }
                
                
                if ( $is_platform_admin )
                {
                    $advancedCmdList[] = array( 'img'  => 'xml',
                                        'name' => get_lang( 'Resource type definitions' ),
                                        'url'  => claro_htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                                  .'/index.php?cmd=rqShowResourceType' ) ) );
                }
                break;
            }
            
            case 'rqShowLibrarylist':
            case 'rqAddLibrary':
            {
                if ( $cmd == 'rqAddLibrary' )
                {
                    $form = new ModuleTemplate( 'CLLIBR' , 'form.tpl.php');
                    $form->assign( 'message' , get_lang( 'Choose a title for this link to the library' ) );
                    $form->assign( 'urlAction' , 'exAddLibrary' );
                    $form->assign( 'urlCancel' , 'rqShowLibrarylist' );
                    $form->assign( 'xid' , array( array( 'type'  => 'hidden'
                                                       , 'name'  => 'libraryId'
                                                       , 'value' => $library->getId() )
                                                , array( 'type'  => 'text'
                                                       , 'name'  => 'title'
                                                       , 'value' => $library->getTitle() ) ) );
                    $dialogBox->question( $form->render() );
                }
                
                if ( $edit_allowed )
                {
                    $cmdList[] = array( 'img'  => 'new_book',
                                        'name' => get_lang( 'Create a new library' ),
                                        'url'  => claro_htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                                  .'/index.php?cmd=rqCreateLibrary' ) ) );
                }
                break;
            }
            
            case 'rqShowLibrarian':
            {
                $cmdList[] = array( 'img'  => 'add_librarian',
                                    'name' => get_lang( 'Add a librarian' ),
                                    'url'  => claro_htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                              .'/index.php?cmd=rqShowLibrarian&option=add&libraryId=' . $libraryId ) ) );
                array_unshift( $cmdList , array( 'img'  => 'back',
                                                 'name' => get_lang( 'Back to the catalogue' ),
                                                 'url'  => claro_htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                                           .'/index.php?cmd=rqShowCatalogue&libraryId=' . $libraryId ) ) ) );
                break;
            }
            
            case 'rqView':
            case 'exExport':
            case 'exCite' :
            {
                $is_validated = false;
                
                if ( ! $is_deleted )
                {
                    $viewName = $resource->getType() . 'View';
                    
                    if ( array_key_exists( 'resourceview' , $pluginList )
                      && in_array( strtolower( $viewName ) , $pluginList[ 'resourceview' ] ) )
                    {
                        if ( $resource->getStorageType() == Resource::TYPE_FILE )
                        {
                            $resourceViewer = new $viewName( new StoredResource( $repository
                                                                               , $resource
                                                                               , null
                                                                               , $secretKey ) );
                        }
                        else
                        {
                            $resourceViewer = new $viewName( $resource->getName() );
                        }
                        
                        $is_validated = $resourceViewer->validate( StoredResource::getFileExtension( $resource->getName() ) );
                    }
                }
                
                $type = $resource->getType();
                
                $template = new ModuleTemplate( 'CLLIBR' , 'resource.tpl.php' );
                $template->assign( 'resourceId' , $resourceId );
                $template->assign( 'storageType' , $resource->getStorageType() );
                $template->assign( 'resourceType' , $type );
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
                $template->assign( 'defaultMetadataList' , $resourceTypeList->get( $type )
                                                         ? $resourceTypeList->get( $type )->getDefaultMetadataList()
                                                         : array() );
                
                if ( $edit_allowed )
                {
                    if( $courseId )
                    {
                        $cmdList[] = array( 'img'  => 'book',
                                            'name' => get_lang( 'Add to the course\'s bibliography' ),
                                            'url'  => claro_htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                                      .'/index.php?cmd=exAdd&resourceId=' . $resourceId ) ) );
                    }
                    
                    $cmdList[] = array( 'img'  => 'edit',
                                        'name' => get_lang( 'Edit resource\'s metadatas' ),
                                        'url'  => claro_htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                                  .'/index.php?cmd=rqEditResource&resourceId=' . $resourceId ) ) );
                    
                    if( $acl->deletionGranted( $resourceId ) )
                    {
                        $cmdList[] = array( 'img'  => 'edit',
                                            'name' => get_lang( 'Update resource' ),
                                            'url'  => claro_htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                                      .'/index.php?cmd=rqUpdateResource&resourceId=' . $resourceId ) ) );
                    }
                }
                
                if ( $userId )
                {
                    $cmdList[] = array( 'img'  => 'bookmark',
                                        'name' => get_lang( 'Add to my bookmark' ),
                                        'url'  => claro_htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                                  .'/index.php?cmd=exBookmark&resourceId=' . $resourceId ) ) );
                }
                
                foreach( $exporterList as $name => $exporter )
                {
                    $advancedCmdList[] = array( 'img'  => 'export',
                                        'name' => get_lang( 'Export metadatas' ) . ' ( ' . $name . ' ) ',
                                        'url'  => claro_htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                                  .'/index.php?cmd=exExport&format='. $name
                                                  . '&resourceId=' . $resourceId ) ) );
                }
                
                if ( $cmd == 'exCite' )
                {
                    $out = get_lang( 'You can copy/paste this line into you word processing software' ) . ' :<br /><br />'
                         . $cite;
                    $dialogBox->info( $out );
                }
                else
                {
                    $advancedCmdList[] = array( 'img'  => 'biblio',
                                        'name' => get_lang( 'Generate a bibliographic citation' ),
                                        'url'  => claro_htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                                  .'/index.php?cmd=exCite&resourceId=' . $resourceId ) ) );
                }
                
                if ( $libraryId )
                {
                    array_unshift( $cmdList , array( 'img'  => 'back',
                                                     'name' => get_lang( 'Back to the catalogue' ),
                                                     'url'  => claro_htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                                               .'/index.php?cmd=rqShowCatalogue&libraryId=' . $libraryId ) ) ) );
                }elseif( $courseId )
                {
                    array_unshift( $cmdList , array( 'img'  => 'back',
                                                     'name' => get_lang( 'Back to the course\'s bibliography' ),
                                                     'url'  => claro_htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' ) ) ) ) );
                }
                break;
            }
            
            case 'rqDownload':
            {
                $storedResource = new StoredResource( $repository , $resource , null , $secretKey );
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
                
                if ( $libraryId )
                {
                    array_unshift( $cmdList , array( 'img'  => 'back',
                                                     'name' => get_lang( 'Back to the catalogue' ),
                                                     'url'  => claro_htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                                               .'/index.php?cmd=rqShowCatalogue&libraryId=' . $libraryId ) ) ) );
                }elseif( $courseId )
                {
                    array_unshift( $cmdList , array( 'img'  => 'back',
                                                     'name' => get_lang( 'Back to the course\'s bibliography' ),
                                                     'url'  => claro_htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' ) ) ) ) );
                }
                break;
            }
            
            case 'rqUpdateResource':
            {
                $pageTitle[ 'subTitle' ] = get_lang( 'Update resource' );
                $template = new ModuleTemplate( 'CLLIBR' , 'updateresource.tpl.php' );
                $template->assign( 'userId' , $userId );
                $template->assign( 'resource' , $resource );
                
                array_unshift( $cmdList , array( 'img'  => 'back',
                                                 'name' => get_lang( 'Back to the resource\'s page' ),
                                                 'url'  => claro_htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                                           .'/index.php?cmd=rqView&resourceId=' . $resourceId ) ) ) );
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
                $template->assign( 'urlAction' , 'ex' . substr( $cmd , 2 ) );
                $template->assign( 'propertyList' , $metadata->getAllProperties() );
                $template->assign( 'tagCloud' , $tagCloud->render() );
                $template->assign( 'typeList' , $resourceTypeList->getResourceTypeList() );
                $template->assign( 'resourceType' , $metadata->getType() );
                $template->assign( 'defaultMetadataList' , $resourceTypeList->get( $type )
                                                         ? $resourceTypeList->get( $type )->getDefaultMetadataList()
                                                         : array() );
                
                array_unshift( $cmdList , array( 'img'  => 'back',
                                                 'name' => get_lang( 'Back to the resource\'s page' ),
                                                 'url'  => claro_htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' )
                                                           .'/index.php?cmd=rqView&resourceId=' . $resourceId ) ) ) );
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
                                . get_lang( 'Warning : this library is not empty! First, you must delete or move all the resources within' )
                                .  '</strong>';
                }
                else
                {
                    $msg = get_lang( 'Do you really want to delete this library?' );
                    $urlAction = 'exDeleteLibrary';
                    $urlCancel = 'rqShowLibrarylist';
                    $xid = array( 'libraryId' => $libraryId );
                }
                
                break;
            }
            
            case 'rqDeleteResource':
            {
                if( $acl->deletionGranted( $resourceId ) )
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
                }
                else
                {
                    $dialogBox->error( get_lang( 'You are not allowed to delete this resource!' ) );
                }
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
            
            case 'rqAddResourceType':
            case 'rqEditResourceType':
            {
                $template->assign( 'edit' , true );
                break;
            }
            
            case 'rqDeleteResourceType':
            {
                $msg = get_lang( 'Do you really want to remove this type definition?' );
                $urlAction = 'exDeleteResourceType';
                $urlCancel = 'rqShowResourceType';
                $xid = array( 'typeName' => $typeName );
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
        Claroline::getInstance()->display->body->appendContent( claro_html_tool_title( $pageTitle , null , $cmdList , $advancedCmdList )
                                                                . $warning->render()
                                                                . $dialogBox->render()
                                                                . $template->render() );
    }
    else // FORBIDDEN ACTION
    {
        $dialogBox->error( get_lang( 'Access denied' ) );
        Claroline::getInstance()->display->body->appendContent( $dialogBox->render() );
    }
}
catch( Exception $e )
{
    if ( claro_debug_mode() )
    {
        $errorMsg = '<pre>' . $e->__toString() . '</pre>';
    }
    else
    {
        $errorMsg = $e->getMessage();
    }
    
    $dialogBox->error( '<strong>' . get_lang( 'Error' ) . ' : </strong>' . $errorMsg );
    Claroline::getInstance()->display->body->appendContent( $dialogBox->render() );
}

echo Claroline::getInstance()->display->render();