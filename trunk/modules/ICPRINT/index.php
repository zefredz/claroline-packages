<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * Main script for Impression Service module
 *
 * @version     1.8-backport $Revision$
 * @copyright   2001-2007 Universite catholique de Louvain (UCL)
 * @author      Frederic Minne <zefredz@claroline.net>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE version 2 or later
 * @package     icprint
 */
try
{    
    $tlabelReq = 'ICPRINT';
    $nameTools = 'Print Service';
    // load Claroline kernel
    require dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';
    claro_set_display_mode_available(true);
    
    if ( ! claro_is_in_a_course() )
    {
        claro_disp_auth_form(true);
    }
    
    // PDO CRUD Database Framework
    require_once dirname(__FILE__) . '/lib/pdocrud/pdocrudclaro.lib.php';
    require_once dirname(__FILE__) . '/lib/pdocrud/pdocrud.lib.php';
    require_once dirname(__FILE__) . '/lib/pdocrud/pdofactory.lib.php';
    require_once dirname(__FILE__) . '/lib/pdocrud/pdosqlscript.lib.php';
    require_once dirname(__FILE__) . '/lib/pdocrud/pdomapper.lib.php';
    require_once dirname(__FILE__) . '/lib/pdocrud/pdomapperbuilder.lib.php';
    require_once dirname(__FILE__) . '/lib/pdocrud/pdomapperschema.lib.php';
    
    require_once dirname(__FILE__) . '/lib/request/userinput.lib.php';
    require_once dirname(__FILE__) . '/lib/request/inputfilters.lib.php';
    require_once dirname(__FILE__) . '/lib/html/form.lib.php';
    
    require_once dirname(__FILE__) . '/lib/datagrid.lib.php';
    require_once dirname(__FILE__) . '/lib/pager.lib.php';
    
    require_once dirname(__FILE__) . '/lib/filefinder.lib.php';
    require_once dirname(__FILE__) . '/lib/time.lib.php';
    
    define ( 'APP_PATH', dirname(__FILE__).'/crud' );
    !defined( 'CLARO_DSN' ) && define ( 'CLARO_DSN', 'mysql://'.get_conf('dbLogin')
        .':'.get_conf('dbPass').'@'.get_conf('dbHost').'/'
        .get_conf('mainDbName') );
            
    // Init database :
    PDOCrud::init( CLARO_DSN );
    // Init data objects :
    require_once APP_PATH . '/classes/document.class.php';
    require_once APP_PATH . '/classes/action.class.php';
    $mapperBuilder = PDOCrud::getBuilder();
    $mapperBuilder->register( PDOMapperSchema::fromFile(APP_PATH.'/schemas/document.xml'));
    $mapperBuilder->register( PDOMapperSchema::fromFile(APP_PATH.'/schemas/action.xml'));
    $documentMapper = $mapperBuilder->getMapper('PrintServiceDocument');
    $actionMapper = $mapperBuilder->getMapper('PrintServiceAction');
    
    $userInput = FilteredUserInput::getInstance();
    
    $allowedCommandList = claro_is_allowed_to_edit()
        ? array( 'list'
            , 'rqPublish', 'exPublish'
            , 'rqDelete', 'exDelete'
            , 'exDeleteSelection', 'rqDeleteSelection' )
        : array( 'list' )
        ;
        
    $userInput->setFilter( 
        'cmd', 
        array( new AllowedValueListFilter( $allowedCommandList ), 'isValid' ) 
    );
        
    $cmd = $userInput->get( 'cmd', 'list' ); 
    
    if ( 'rqDelete' == $cmd )
    {
        $path = $userInput->getMandatory( 'localpath' );
        
        $filesToDelete = array( $path );
        
        $cmd = 'rqDeleteFiles';
    }
    
    if ( 'rqDeleteSelection' == $cmd )
    {
        $filesToDelete = $userInput->get( 'selectedFiles', array() );
        
        $cmd = 'rqDeleteFiles';
    }
    
    if ( 'rqDeleteFiles' == $cmd )
    {        
        if ( !empty( $filesToDelete ) && is_array( $filesToDelete ) )
        {
            $form = new Form;
            $form->addElement( new InputHidden( 'cmd', 'exDeleteSelection' ) );
            
            foreach ( $filesToDelete as $idx => $localPath ) 
            {
                $form->addElement( new InputHidden( "selectedFiles[{$idx}]", htmlspecialchars($localPath) ) );
            }
            
            $form->addElement( new InputSubmit( 'submit', get_lang('Yes') ) );
            $form->addElement( new InputCancel( 'cancel', get_lang('No'), $_SERVER['PHP_SELF'] ) );
        }
        else
        {
            $message = get_lang('No file to delete');
            $cmd = 'list';
        }
    }
    
    if ( 'exDelete' == $cmd )
    {
        $path = $userInput->getMandatory( 'localpath' );
        
        $filesToDelete = array( $path );
        
        $cmd = 'exDeleteFiles';
    }
    
    if ( 'exDeleteSelection' == $cmd )
    {
        $filesToDelete = $userInput->get( 'selectedFiles', array() );
        
        $cmd = 'exDeleteFiles';
    }
    
    if ( 'exDeleteFiles' == $cmd )
    {
        if ( !empty( $filesToDelete ) && is_array( $filesToDelete ) )
        {
            $message = '';
            
            foreach ( $filesToDelete as $localPath )
            {
                $doc = $documentMapper->selectOne(
                    $documentMapper->getSchema()->getField( 'localPath' ) . ' = :localpath'
                    . ' AND ' . $documentMapper->getSchema()->getField( 'courseId' ) . ' = :courseId',
                    array( ':localpath' => $localPath, ':courseId' => claro_get_current_course_id() )
                );
                
                if ( ! $doc )
                {
                    $message .= get_lang('Document %document% not found',
                        array( '%document%' => htmlspecialchars( $localPath ) ) )."<br />\n";
                }
                else
                {
                    $action = $documentMapper->hasOne( $doc, 'action' );
                    $actionMapper->delete( $action );
                    
                    $action = PrintServiceAction::actionDelete( $doc );
                    $actionMapper->create( $action );
                    
                    // does not work with PHP 5.1... ok in PHP 5.2
        //            $actionMapper->updateWhere( 
        //                $action,  
        //                $actionMapper->getSchema()->getField( 'documentId' ) . " = :documentId"
        //            );
                    
                    if ( $documentMapper->delete( $doc ) )
                    {
                        $message .= get_lang('Document %document% deleted',
                            array( '%document%' => htmlspecialchars( $localPath ) ) )."<br />\n";
                    }
                }
            }
        }
        else
        {
            $message = get_lang('No file to delete');
        }
        
        $cmd = 'list';
    }    
    
    if ( 'rqPublish' == $cmd )
    {            
        $courseDir = realpath( 
              get_path('coursesRepositorySys') 
            . claro_get_course_path()
            .'/document' );
            
        $pdfFinder = new ExtensionFileFinder( $courseDir, '.pdf' );
        
        $fileList = array();
        
        foreach ( $pdfFinder as $file )
        {
            $hash = md5_file( $file->getPathname() );
            $locPath = str_replace( '\\', '/', str_replace( $courseDir, '', $file->getPathname() ) );
            
            if ( ! $documentMapper->selectOne(
                $documentMapper->getSchema()->getField( 'localPath' ) . ' = :localPath'
                . ' AND ' . $documentMapper->getSchema()->getField( 'courseId' ) . ' = :courseId',
                array( ':localPath' => $locPath, ':courseId' => claro_get_current_course_id() )
            ) )
            {
                $fileList[] = array(
                    'title' => $file->getFilename(), 
                    'path' => $file->getPathname(),
                    'localPath' => str_replace( '\\', '/', str_replace( $courseDir, '', $file->getPathname() ) ),
                    'globalpath' => str_replace( '\\', '/', $file->getPathname() ), 
                    'hash' => $hash,
                    'length' => filesize( $file->getPathname() ),
                    'courseId' => claro_get_current_course_id()
                );
            }
        }

        $fileTable = new Claro_Utils_Clarogrid;
        
        $fileTable->fullWidth();
        $fileTable->emphaseLine();
        $fileTable->setTitle( get_lang( 'Discovered documents' ) );
        $fileTable->setRows( $fileList );
        $fileTable->setEmptyMessage( get_lang('No document to publish') );
        $fileTable->addDataColumn( 'title', get_lang( 'Title' ) );
        $fileTable->addDataColumn( 'localPath', get_lang( 'Local path' ) );
        $fileTable->addDataColumn( 'length', get_lang( 'Size (octets)' ) );
        $fileTable->prependColumn( 'publish', 
            claro_html_icon('print'), 
            '<input type="checkbox" name="filesToPublish[%_lineNumber_%]" value="%localPath%" />' );
            
        $submit = new InputImage( 'submit', get_icon_url('print') );
        $submit->setLabel( get_lang( 'Publish selected files' ), true, array( 'class' => 'right claroCmd' ) );
            
        $fileTable->setFooter( $submit->render() );
        
        $form = new Form;
        $form->addElement( $fileTable );
        $form->addElement( new InputHidden( 'cmd', 'exPublish' ) );
        // $form->addElement( new InputSubmit( 'submit', get_lang( 'Publish selected' ) ) );
        // $form->addElement( new InputCancel( 'cancel', get_lang( 'Cancel' ), $_SERVER['PHP_SELF'] ) );
    }
    
    if ( 'exPublish' == $cmd )
    {
        $filesToPublish = $userInput->get( 'filesToPublish', array() );
        
        if ( ! empty( $filesToPublish ) )
        {
            $message = '';
            
            foreach ( $filesToPublish as $file )
            {
                $doc = PrintServiceDocument::fromLocalPath( $file );
                $documentMapper->create( $doc );
                
                // reset aldor versions of same document
                $actionMapper->deleteWhere(
                    $actionMapper->getSchema()->getField( 'documentLocalPath' ) . " = :localPath"
                    . ' AND ' . $actionMapper->getSchema()->getField( 'courseId' ) . ' = :courseId',
                    array( ":localPath" => $doc->localPath, ':courseId' => $doc->courseId )
                );
                
                $action = PrintServiceAction::actionAdd( $doc );
                $documentMapper->insertHasOne( $doc, $action, 'action' );
                
                $message .= get_lang( 'Document %document% published<br />',
                    array( '%document%' => htmlspecialchars( $file ) ) );
            }
        }
        else
        {
            $message = get_lang('No file to publish');
        }
        
        $cmd = 'list';
    }
    
    if ( 'list' == $cmd )
    {
        $documentList = $documentMapper->selectAll( 
            $documentMapper->getSchema()->getField( 'courseId' ) . ' = :courseId',
            array( ':courseId' => claro_get_current_course_id() )
        );

        $publishedFileTable = new Claro_Utils_Clarogrid;
        
        $publishedFileTable->fullWidth();
        $publishedFileTable->emphaseLine();
        $publishedFileTable->setEmptyMessage( get_lang('No document published') );
        $publishedFileTable->setTitle( get_lang('Published documents') );
        $publishedFileTable->setRows( $documentList );
        $publishedFileTable->addDataColumn( 'title', get_lang( 'Title' ) );
        $publishedFileTable->addDataColumn( 'localPath', get_lang( 'Local path' ) );
        $publishedFileTable->addDataColumn( 'length', get_lang( 'Size (octets)' ) );
        
        if ( claro_is_allowed_to_edit() )
        {
            $publishedFileTable->prependColumn( 'selection', 
                '', 
                '<input type="checkbox" name="selectedFiles[%_lineNumber_%]" value="%localPath%" />' );
                
            $publishedFileTable->addColumn( 'delete', 
                get_lang('Delete'), 
                '<a href="'.$_SERVER['PHP_SELF']
                    .'?cmd=rqDelete&amp;localpath=%uu(localPath)%" '
                    . 'onclick="return deleteDocument(\'%localPath%\');">'
                    . claro_html_icon('delete').'</a>' );
                    
            $submit = new InputImage( 'submit1', get_icon_url('delete') );
            $submit->setLabel( get_lang( 'Delete selected files' ), true, array( 'class' => 'right claroCmd' ) );
            
            $publishedFileTable->setFooter( $submit->render() );

            $publishedDocumentList = new Form('', Form::METHOD_POST, array( 'onsubmit' => 'return deleteSelection(this)' ) );
            $publishedDocumentList->addElement( $publishedFileTable );
            $publishedDocumentList->addElement( new InputHidden( 'cmd', 'rqDeleteSelection' ) );
        }
        else
        {
            $publishedDocumentList = $publishedFileTable;
        }
    } 
    
    // Display
    $htmlHeadXtra[] = '<script type="text/javascript" src="./js/jquery.js"></script>';
    
    $htmlHeadXtra[] = '<script type="text/javascript">
function countCheckedBox()
{
    var counter = 0;

    $("input[@type=checkbox][@checked]").each( function() {
        counter++;
    });

    return counter;
}

function deleteDocument ( localPath )
{
    if (confirm(" Are you sure to delete "+ localPath + " ?"))
    {
        window.location=\''.$_SERVER['PHP_SELF'].'?cmd=exDelete&localpath=\'+escape(localPath);
        return false;
    }
    else
    {
        return false;
    }
}

function deleteSelection ( thisForm )
{
    if ( ! countCheckedBox() )
    {
        alert("No document selected !");
        return false;
    }

    if (confirm(" Are you sure to delete the selected documents ?"))
    {
        $("input[@name=cmd]").val("exDeleteSelection");
        return true;
    }
    else
    {
        return false;
    }
}
</script>';

    $htmlHeadXtra[] = '<link rel="stylesheet" type="text/css" href="./css/form.css" media="all" />';

    $noQUERY_STRING = true;
    
    require_once get_path('includePath') . '/claro_init_header.inc.php';
    
    echo claro_html_tool_title( $nameTools );
    
    if ( isset( $message ) )
    {
        echo claro_html_message_box( '<p>'.$message.'</p>' );
    }
    
    if ( 'rqPublish' == $cmd )
    {
        echo '<p>'.'<a href="'
            . $_SERVER['PHP_SELF'].'" class="claroCmd">'
            . claro_html_icon('back').' '.get_lang('Back to list').'</a>'
            . ( claro_is_platform_admin() 
                ? ' | <a href="keyring.php" class="claroCmd">'
                    . claro_html_icon('key')
                    . ' ' . get_lang('Manage key ring').'</a>'
                : ''
                )
            . '</p>'."\n"
            ;
        
        echo $form->render();
    }
    elseif ( 'rqDeleteFiles' == $cmd )
    {
        echo '<p>'
            . get_lang( 'You are going to delete the following documents:' )
            . '<ul>'
            ;
       
        foreach ( $filesToDelete as $path )
        {
            echo '<li>'.htmlspecialchars($path).'</li>';
        }     
            
        echo '</ul>'
            . get_lang( 'Continue ?' ) . '<br />' . "\n"
            . $form->render()
            . '</p>' . "\n"
            ;
    }
    elseif ( 'list' == $cmd )
    {
        if ( claro_is_allowed_to_edit() )
        {
            echo '<p>'.'<a href="'
                . $_SERVER['PHP_SELF'].'?cmd=rqPublish" class="claroCmd">'
                . claro_html_icon('print').' '.get_lang('Publish a document').'</a>'
                . ( claro_is_platform_admin() 
                    ? ' | <a href="keyring.php" class="claroCmd">'
                        . claro_html_icon('key')
                        . ' ' . get_lang('Manage key ring').'</a>'
                    : ''
                    )
                . '</p>'."\n"
                ;
        }
                
        //echo $publishedFileTable->render();
        echo $publishedDocumentList->render();
    }
    
    require_once get_path('includePath') . '/claro_init_footer.inc.php';
}
catch ( Exception $e )
{
    if ( claro_debug_mode() )
    {
        claro_die( '<pre>' . $e->__toString() . '</pre>' );
    }
    else
    {
        claro_die( $e->getMessage() );
    }
}
?>