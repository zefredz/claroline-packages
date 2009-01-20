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

//Tool label
$tlabelReq = 'ICPRINT';

//Load claroline kernel
require_once dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';

//Tool name
$nameTools = get_lang('Print Service');

//Load used lib
FromKernel::uses ('utils/input.lib','utils/validator.lib','utils/datagrid.lib','utils/finder.lib');

require_once dirname(__FILE__) . '/lib/pdocrud/pdocrudclaro.lib.php';
require_once dirname(__FILE__) . '/lib/pdocrud/pdocrud.lib.php';
require_once dirname(__FILE__) . '/lib/pdocrud/pdofactory.lib.php';
require_once dirname(__FILE__) . '/lib/pdocrud/pdosqlscript.lib.php';
require_once dirname(__FILE__) . '/lib/pdocrud/pdomapper.lib.php';
require_once dirname(__FILE__) . '/lib/pdocrud/pdomapperbuilder.lib.php';
require_once dirname(__FILE__) . '/lib/pdocrud/pdomapperschema.lib.php';

require_once dirname(__FILE__) . '/lib/html/form.lib.php';

//Check
if ( !claro_is_tool_allowed() )
{
    if ( claro_is_in_a_course() )
    {
        claro_die( get_lang( "Not allowed" ) );
    }
    else
    {
        claro_disp_auth_form( true );
    }
}

//On the fly install
install_module_in_course( 'ICPRINT', claro_get_current_course_id() ) ;
add_module_lang_array('ICPRINT');

//Constant Declaration
define ( 'APP_PATH', dirname(__FILE__).'/crud' );
!defined( 'CLARO_DSN' );
define ( 'CLARO_DSN', 'mysql://'.get_conf('dbLogin')
.':'.get_conf('dbPass').'@'.get_conf('dbHost').'/'
.get_conf('mainDbName') );


//Init vars
$userInput = Claro_UserInput::getInstance();

claro_set_display_mode_available(true);
$is_allowedToEdit = claro_is_allowed_to_edit();

if($is_allowedToEdit)
{
    $allowedCommandList = array ('list', 'rqPublish', 'exPublish', 'rqDelete', 'exDelete', 'exDeleteSelection', 'rqDeleteSelection');
}
else
{
    $allowedCommandList = array('list');
}

$userInput->setValidator('cmd',new Claro_Validator_AllowedList($allowedCommandList));

try
{
    $cmd = $userInput->get('cmd', 'list');
}
catch(Exception $e)
{
    die('Invalid action');
}

//Init database
PDOCrud::init( CLARO_DSN );

// Init data objects :
require_once APP_PATH . '/classes/document.class.php';
require_once APP_PATH . '/classes/action.class.php';

$mapperBuilder = PDOCrud::getBuilder();
$mapperBuilder->register( PDOMapperSchema::fromFile(APP_PATH.'/schemas/document.xml'));
$mapperBuilder->register( PDOMapperSchema::fromFile(APP_PATH.'/schemas/action.xml'));
$documentMapper = $mapperBuilder->getMapper('PrintServiceDocument');
$actionMapper = $mapperBuilder->getMapper('PrintServiceAction');

$dialogBox = new DialogBox();

//Command traitment
if ( 'rqDelete' == $cmd )
    {
        $path = $userInput->getMandatory('localpath');
        $filesToDelete = array( $path );
        $cmd = 'rqDeleteFiles';
    }

    if ( 'rqDeleteSelection' == $cmd )
    {
        $filesToDelete = $userInput->get('selectedFiles', array() );
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
            $dialogBox->info(get_lang('No file to delete !'));
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

            foreach ( $filesToDelete as $localPath )
            {
                $doc = $documentMapper->selectOne(
                $documentMapper->getSchema()->getField( 'localPath' ) . ' = :localpath'
                . ' AND ' . $documentMapper->getSchema()->getField( 'courseId' ) . ' = :courseId',
                array( ':localpath' => $localPath, ':courseId' => claro_get_current_course_id() )
                );

                if ( ! $doc )
                {
                    $dialogBox->info(get_lang('Document %document% not found',
                    array( '%document%' => htmlspecialchars( $localPath ) ) )."<br />\n");
                }
                else
                {
                    $action = $documentMapper->hasOne( $doc, 'action' );
                    //var_dump($action);
                    $actionMapper->delete( $action );

                    $action = PrintServiceAction::actionDelete( $doc );
                    $actionMapper->create( $action );

                    if ( $documentMapper->delete( $doc ) )
                    {
                        $dialogBox->success(get_lang('Document %document% deleted',
                        array( '%document%' => htmlspecialchars( $localPath ) ) )."<br />\n");
                    }
                }
            }
        }
        else
        {
            $dialogBox->info(get_lang('No file to delete'));
        }

        $cmd = 'list';
    }

    if ( 'rqPublish' == $cmd )
    {
        $courseDir = realpath(
        get_path('coursesRepositorySys')
        . claro_get_course_path()
        .'/document' );

        $pdfFinder = new Claro_FileFinder_Extension( $courseDir, '.pdf' );

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
        $fileTable->setTitle( '<strong>'.get_lang( 'Discovered documents' ) . '</strong>' );
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
        
    }

    if ( 'exPublish' == $cmd )
    {
        $filesToPublish = $userInput->get( 'filesToPublish', array() );

        if ( ! empty( $filesToPublish ) )
        {

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

                $dialogBox->success(get_lang('Document %document% published',array( '%document%' => htmlspecialchars( $file ) ) ).'<br />');
            }
        }
        else
        {
            $dialogBox->info(get_lang('No file selected'));
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
        $publishedFileTable->setTitle( '<strong>'.get_lang('Published documents').'</strong>' );
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
            $submit->setLabel( get_lang('Delete selected files' ), true, array( 'class' => 'right claroCmd' ) );

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

//Display

$claroline->display->header->addHtmlHeader('<script type="text/javascript" src="./js/jquery.js"></script>');
$claroline->display->header->addHtmlHeader('<script type="text/javascript">
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
if (confirm("'.get_lang('Are you sure to delete').'" + localPath + " ?"))
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
    alert("'.get_lang('No document selected !').'");
    return false;
}

if (confirm("'.get_lang('Are you sure to delete the selected documents ?').'"))
{
    $("input[@name=cmd]").val("exDeleteSelection");
    return true;
}
else
{
    return false;
}
}
</script>');

//add breadcrumb
ClaroBreadCrumbs::getInstance()->setCurrent($nameTools,get_module_entry($tlabelReq));

//add title
$claroline->display->body->appendContent(claro_html_tool_title($nameTools));

//message display
if ( 'rqPublish' == $cmd )
{
    $dialogBox->warning(get_lang('Only pdf files are allowed at this time.'));
}

$claroline->display->body->appendContent($dialogBox->render());


if ( 'rqPublish' == $cmd )
{
    $claroline->display->body->appendContent('<p>'.'<a href="'
    . $_SERVER['PHP_SELF'].'" class="claroCmd">'
    . '<<'.' '.get_lang('Back to list').'</a>'
    . '</p>'."\n");
    
    $claroline->display->body->appendContent($form->render());
}
elseif ( 'rqDeleteFiles' == $cmd )
{

    $listToDelScript = get_lang('You are going to delete the following documents') . ' :<ul>';
        
    foreach ( $filesToDelete as $path )
    {
        $listToDelScript .= '<li>'.htmlspecialchars($path).'</li>';
    }

    $listToDelScript .= '</ul>'
    . get_lang('Continue ?') . '<br />' . "\n"
    . $form->render()
    . '</p>' . "\n"
    ;
    $dialogBox->question($listToDelScript);
    $claroline->display->body->appendContent($dialogBox->render());
}
elseif ( 'list' == $cmd )
{
    if ( claro_is_allowed_to_edit() )
    {
        $claroline->display->body->appendContent('<p>'.'<a href="'
        . $_SERVER['PHP_SELF'].'?cmd=rqPublish" class="claroCmd">'
        . claro_html_icon('print').' '.get_lang('Publish a document').'</a>'
        . '</p>'."\n"
        );
    }
    
    $claroline->display->body->appendContent($publishedDocumentList->render());
}


//return body html required
echo $claroline->display->render();
