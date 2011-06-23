<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.7.0 $Revision$ - Claroline 1.9
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
    'resourceview.lib',
    'acl.lib',
    'tagcloud.lib',
    'tools.lib' );

$claroline->currentModuleLabel( 'CLLIBR' );
load_module_config( 'CLLIBR' );
load_module_language( 'CLLIBR' );

$nameTools = get_lang( 'Online Library' );
$pageTitle = array( 'mainTitle' => get_lang( 'Online Library' ) );

$secretKey = get_conf ( 'CLLIBR_encryption_key' );

$repository = get_path( 'rootSys' ) . 'cllibrary/';

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
                   , 'exUnbookmark'
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
                        , 'exAddResource'     => 'rqEditResource'
                        , 'exDeleteResource'  => 'rqShowCatalogue'
                        , 'exMoveResource'    => 'rqShowCatalogue'
                        , 'exCreateLibrary'   => 'rqShowCatalogue'
                        , 'exEditLibrary'     => 'rqShowLibrarylist'
                        , 'exDeleteLibrary'   => 'rqShowLibrarylist'
                        , 'exAddLibrarian'    => 'rqShowLibrarian'
                        , 'exRemoveLibrarian' => 'rqShowLibrarian'
                        , 'exEditResource'    => 'rqView'
                        , 'exBookmark'        => ''
                        , 'exUnbookmark'      => '' );

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
    $access_allowed = $access_allowed
                   || $librarian->isLibrarian( $userId )
                   || $library->getStatus() != Library::LIB_PRIVATE;
    $edit_allowed = $edit_allowed || $librarian->isLibrarian( $userId );
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
                    $errorMsg = get_lang( 'This resource is already bookmarked' );
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
                $errorMsg = get_lang( 'This resource is already added' );
            }
            
            $execution_ok = ! $errorMsg
                           && $bibliography->add( $resourceId );
            break;
        }
        
        case 'exRemove':
        case 'exUnbookmark':
        {
            $execution_ok = $resourceSet->remove( $resourceId );
            break;
        }
        
        case 'exVisible':
        case 'exInvisible':
        {
            $execution_ok = $resourceSet->setVisibility( $resourceId
                                                       , $cmd == 'exVisible' ? true : false );
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
            $description = $userInput->get( 'description' );
            
            $resource = new $type( $database );
            
            if ( $title )
            {
                $resource->setType( $type );
                $resource->setStorageType( $storage );
                $resource->setDate();
                
                if ( $storage == 'file' )
                {
                    $storedResource = new StoredResource( $repository , $resource , $secretKey );
                    
                    if ( $_FILES && $_FILES[ 'uploadedFile' ][ 'size' ] != 0 )
                    {
                        $file = $_FILES[ 'uploadedFile' ];
                    }
                    else
                    {
                        $errorMsg = get_lang( 'File missing' );
                    }
                    
                    if ( ! $errorMsg
                      && ! $resource->validate( $file['name'] ) )
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
                $errorMsg = get_lang( 'You must give a title' );
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
            $keywords = $userInput->get( 'keyword' );
            $newKeywords = explode( ',' , $userInput->get( 'keywords' ) );
            $kToDelete = $userInput->get( 'kdel' );
            
            $metadata->setTitle( $title );
            $metadata->setDescription( $description );
            
            if ( ! empty( $values ) )
            {
                foreach( $values as $id => $value )
                {
                    if ( $value )
                    {
                        $metadata->modify( $names[ $id ] , $value );
                    }
                    else
                    {
                        $metadata->remove( $names[ $id ] );
                    }
                }
            }
            
            if ( ! empty( $newValues ) )
            {
                foreach( $newValues as $id => $value )
                {
                    if ( $value )
                    {
                        $metadata->add( $newNames[ $id ] , $value );
                    }
                }
            }
            
            if ( ! empty( $toDelete ) )
            {
                foreach( $toDelete as $name )
                {
                    $metadata->remove( $name );
                }
            }
            
            if ( ! empty( $keywords ) )
            {
                foreach( $keywords as $keyword )
                {
                    if ( ! $metadata->keywordExists( $keyword ) )
                    {
                        $metadata->addKeyword( $keyword );
                    }
                }
            }
            
            if ( ! empty( $kToDelete ) )
            {
                foreach( $kToDelete as $value )
                {
                    $metadata->removeKeyword( $value );
                }
            }
            
            foreach( $newKeywords as $value )
            {
                if ( $value )
                {
                    $metadata->addKeyword( trim( $value ) );
                }
            }
            
            $execution_ok = $resource->save();
            break;
        }
        
        case 'exMoveResource':
        {
            $execution_ok = $resourceSet->moveResource( $resourceId , $libraryId );
            break;
        }
        
        case 'exDeleteResource':
        {
            $resource = new Resource( $database , $resourceId );
            $metadata = new Metadata( $database , $resourceId );
            $catalogue = new Collection( $database , 'catalogue' , $libraryId );
            
            $execution_ok = $catalogue->removeResource( $resourceId )
                         && $resource->delete();
            
            if( $execution_ok )
            {
                $metadata->removeAll();
            }
            
            if ( $execution_ok && $resource->getStorageType() == 'file' )
            {
                $storedResource = new StoredResource( $repository , $resource , $secretKey );
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
    else
    {
        $pageTitle[ 'subTitle' ] = ucwords( get_lang( $context ) )
                                 . ( $libraryId ? ' - ' . $library->getTitle() : '' );
    }
    
    $template = new ModuleTemplate( 'CLLIBR' , strtolower( $context ) . '.tpl.php' );
    $template->assign( 'edit_allowed' , $edit_allowed );
    $template->assign( 'is_platform_admin' , $is_platform_admin );
    $template->assign( 'resourceList' , $resourceSet->getResourceList( true ) );
    $template->assign( 'userId' , $userId );
    $template->assign( 'libraryId' , $libraryId );
    $template->assign( 'courseId' , $courseId );
    $template->assign( 'icon' , get_icon_url( 'icon' ) );
    $template->assign( 'tagCloud' , $tagCloud->render() );
    if ( is_a( $resourceSet , 'LibraryList' ) && $libraryId )
    {
        $template->assign( 'librarianList' , $resourceSet->getLibrarianList( $libraryId ) );
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
    
    switch( $cmd )
    {
        case 'rqShowBookmark':
        case 'rqShowBibliography';
        case 'rqShowCatalogue':
        case 'rqShowLibrarylist':
        case 'rqShowLibrarian':
        case 'exVisible':
        case 'exInvisible':
        case '';
        {
            break;
        }
        
        case 'rqView':
        {
            $viewName = $resource->getType() . 'View';
            $is_validated = false;
            
            if ( array_key_exists( 'resourceview' , $pluginList )
              && in_array( strtolower( $viewName ) , $pluginList[ 'resourceview' ] ) )
            {
                $resourceViewer = new $viewName( new StoredResource( $repository
                                                                   , $resource
                                                                   , $secretKey ) );
                
                $is_validated = $resourceViewer->validate( StoredResource::getFileExtension( $resource->getName() ) );
            }
            
            $template = new ModuleTemplate( 'CLLIBR' , 'resource.tpl.php' );
            $template->assign( 'resourceId' , $resourceId );
            $template->assign( 'storageType' , $resource->getStorageType() );
            $template->assign( 'resourceType' , $resource->getType() );
            $template->assign( 'url' , $resource->getName() );
            $template->assign( 'metadataList' , $metadata->getMetadataList( true ) );
            $template->assign( 'userId' , $userId );
            $template->assign( 'libraryId' , $libraryId );
            $template->assign( 'courseId' , $courseId );
            $template->assign( 'edit_allowed' , $acl->editGranted( $resourceId ) );
            $template->assign( 'read_allowed' , $acl->accessGranted( $resourceId , CLLIBR_ACL::ACCESS_READ ) );
            $template->assign( 'viewer' , $is_validated ? $resourceViewer : false );
            break;
        }
        
        case 'rqDownload':
        {
            $storedResource = new StoredResource( $repository , $resource , $secretKey );
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
            $template->assign( 'typeList' , $pluginList[ 'resource' ] );
            $template->assign( 'urlAction' , 'ex' . substr( $cmd , 2 ) );
            break;
        }
        
        case 'rqEditResource':
        {
            $tagCloud = new TagCloud( $database
                                    , 'index.php?cmd=exEditResource&resourceId=' . $resource->getId() );
            $pageTitle[ 'subTitle' ] = get_lang( 'Edit resource' );
            $template = new ModuleTemplate( 'CLLIBR' , 'editresource.tpl.php' );
            $template->assign( 'resourceId' , $resource->getId() );
            $template->assign( 'metadataList' , $metadata->getMetadataList( true ) );
            $template->assign( 'userId' , $userId );
            $template->assign( 'libraryId' , $libraryId );
            $template->assign( 'refId' , $resource->getId() );
            $template->assign( 'refName' , 'resourceId' );
            $template->assign( 'defaultMetadataList' , $resource->getDefaultMetadataList() );
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
            
            foreach( $searchResult as $resourceId => $datas )
            {
                if ( ! $acl->accessGranted( $resourceId , CLLIBR_ACL::ACCESS_SEARCH ) )
                {
                    unset( $searchResult[ $resourceId ] );
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
        $form->assign( 'libraryList' , $libraryList->getResourceList() );
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
    Claroline::getInstance()->display->body->appendContent( claro_html_tool_title( $pageTitle )
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