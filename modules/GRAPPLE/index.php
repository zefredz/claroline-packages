<?php // $Id$
/**
 * CLAROLINE
 *
 * @version 0.1 $Revision$
 *
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package GRAPPLE
 *
 * @author Sebastien Piraux
 * @author Dimitri Rambout <dim@claroline.net>
 *
 */

$tlabelReq = 'GRAPPLE';

require_once dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';

require_once dirname( __FILE__ ) . '/lib/path.class.php';
require_once dirname( __FILE__ ) . '/lib/attempt.class.php';
// those include should not be required when user is a student
require_once dirname( __FILE__ ) . '/lib/item.class.php';
require_once get_path('incRepositorySys') . '/lib/fileManage.lib.php';

/*
 * init request vars
 */
$acceptedCmdList = array(   'rqDelete', 'exDelete',
                            //'exLock', 'exUnlock',
                            'exVisible', 'exInvisible',
                            'exExport',
                            'rqImport', 'exImport',
                            'exMoveUp', 'exMoveDown'
                    );
if( isset($_REQUEST['cmd']) && in_array($_REQUEST['cmd'], $acceptedCmdList) )   $cmd = $_REQUEST['cmd'];
else                                                                            $cmd = null;

if( isset($_REQUEST['pathId']) && is_numeric($_REQUEST['pathId']) )   $pathId = (int) $_REQUEST['pathId'];
else                                                                  $pathId = null;


/*
 * init other vars
 */

$path = new path(); // todo do not instanciate when we do not use or create a single path, use pathList instead

if( !is_null($pathId) )
{
    if( !$path->load($pathId) )
    {
        $cmd = null;
        $pathId = null;
    }
}

claro_set_display_mode_available(true);

$is_allowedToEdit = claro_is_allowed_to_edit();
$user_id = claro_get_current_user_id();

$dialogBox = new DialogBox();

/*
 * Admin only commands
 */

if( $is_allowedToEdit )
{
    if( $cmd == 'exImport')
    {
        if( get_conf( 'import_allowed' ) || claro_is_platform_admin() )
        {
            // include import lib
            require_once dirname( __FILE__ ) . '/lib/xmlize.php';
            require_once dirname( __FILE__ ) . '/lib/scorm.import.lib.php';
            // path class is already included
            require_once dirname( __FILE__ ) . '/lib/item.class.php';
            
            // check if something has been uploaded
            if ( !isset($_FILES['uploadedPackage']['name']) )
            {
                $dialogBox->error( get_lang('Error : no file uploaded') );
            }
            else
            {
                $scormImporter = new ScormImporter($_FILES['uploadedPackage']);
    
                if( $scormImporter->import() )
                {
                    $dialogBox->success('<strong>' . get_lang('Import done') . '</strong>');
                }
                else
                {
                    $dialogBox->error('<strong>' . get_lang('Import failed') . '</strong>');
                    $cmd = 'rqImport';
                }
                $dialogBox->info($scormImporter->backlog->output());
            }
        }
        else
        {
            $dialogBox->error( get_lang( 'Not allowed' ) );
        }
    }

    if( $cmd == 'rqImport' )
    {
        if ( get_conf( 'import_allowed' ) || claro_is_platform_admin() )
        {
            include_once get_path('incRepositorySys') . '/lib/fileUpload.lib.php';
            include_once get_path('incRepositorySys') . '/lib/fileDisplay.lib.php';
    
            $maxFilledSpace = 1000000000;
    
            $courseDir   = claro_get_course_path() . '/scormPackages/';
            $baseWorkDir = get_path('coursesRepositorySys').$courseDir;
    
            $dialogBox->form("\n\n"
            .    '<strong>' . get_lang('Import a learning path') . '</strong>' . "\n"
            .    '<form enctype="multipart/form-data" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . "\n"
            .    claro_form_relay_context()
            //.    '<input type="hidden" name="claroFormId" value="'.uniqid('').'">'."\n"
            .    '<label for="title">' . get_lang('Title') . ' : </label>' . "\n"
            .    '<br />' . "\n"
            .     '<input type="file" name="uploadedPackage" />' . "\n"
            .     '<br />' . "\n"
            .     '<small>' . get_lang('Max file size : %size', array('%size' => format_file_size( get_max_upload_size($maxFilledSpace,$baseWorkDir) ) ) ) . '</small>' . "\n"
            .    '<br /><br />' . "\n"
            .    '<input type="hidden" name="cmd" value="exImport" />' . "\n"
            .    '<input type="submit" value="' . get_lang('Ok') . '" />&nbsp;' . "\n"
            .    claro_html_button('index.php', get_lang('Cancel'))
            .    '</form>' . "\n");
        }
        else
        {
            $dialogBox->error( 'Not allowed' );
        }
    }

    if( $cmd == 'exDelete' )
    {
        if( $path->delete() )
        {
            $dialogBox->success( get_lang('Path succesfully deleted') );
            
            $eventNotifier->notifyCourseEvent("grapple_path_deleted",claro_get_current_course_id(), claro_get_current_tool_id(), $pathId, claro_get_current_group_id(), claro_get_current_user_id() );
        }
        else
        {
            $dialogBox->error( get_lang('Fatal error : cannot delete path') );
        }
    }

    if( $cmd == 'rqDelete' )
    {
        $htmlConfirmDelete = get_lang('Are you sure to delete learning path "%pathTitle" ?', array('%pathTitle' => htmlspecialchars($path->getTitle()) ))
        .     '<br /><br />'
        .    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exDelete&amp;pathId='.$pathId.'">' . get_lang('Yes') . '</a>'
        .    '&nbsp;|&nbsp;'
        .    '<a href="' . $_SERVER['PHP_SELF'] . '">' . get_lang('No') . '</a>'
        ;

        $dialogBox->question( $htmlConfirmDelete );
    }

    /*if( $cmd == 'exLock' )
    {
        $path->lock();

        $path->save();
    }

    if( $cmd == 'exUnlock' )
    {
        $path->unlock();

        $path->save();
    }*/

    if( $cmd == 'exVisible' )
    {
        $path->setVisible();

        $path->save();
        
        $eventNotifier->notifyCourseEvent("grapple_path_visible",claro_get_current_course_id(), claro_get_current_tool_id(), $path->getId(), claro_get_current_group_id(), claro_get_current_user_id() );
    }

    if( $cmd == 'exInvisible' )
    {
        $path->setInvisible();

        $path->save();
        
        $eventNotifier->notifyCourseEvent("grapple_path_invisible",claro_get_current_course_id(), claro_get_current_tool_id(), $path->getId(), claro_get_current_group_id(), claro_get_current_user_id() );
    }

    if( $cmd == 'exMoveUp' || $cmd == 'exMoveDown' )
    {
        // load list to be able to move path up or down
        $pathList = new pathListIterator();
        
        if( $is_allowedToEdit )
        {
            $pathList->load();
        }
        else
        {
            $pathList->load($user_id);
        }
        
        // make the move
        if( $cmd == 'exMoveUp' )
        {
            $pathList->movePathUp($path);
        }
    
        if( $cmd == 'exMoveDown' )
        {
            $pathList->movePathDown($path);
        }
        // clean 
        unset($pathList);
    }

    if( $cmd == 'exExport' )
    {
        if ( get_conf( 'export_allowed' ) || claro_is_platform_admin() )
        {
            $thisPath = $path;
            FromKernel::uses( 'core/linker.lib' );
            require_once dirname(__FILE__).'/../../claroline/exercise/lib/exercise.class.php';
            require_once dirname(__FILE__).'/../../claroline/exercise/export/scorm/scorm_classes.php';
            include_once get_path('incRepositorySys') . "/lib/fileUpload.lib.php";
    
            $pathExport = new PathScormExport( $thisPath );
            if( ! $pathExport->export() )
            {
                $dialogBox->error(
                                    get_lang('Unable to export the path %title', array('%title' => $thisPath->getTitle()))
                                    .   '<br />' . "\n"
                                    .   $pathExport->getError()
                                );
            }
        }
        else
        {
            $dialogBox->error( get_lang( 'Not allowed' ) );
        }
    }
}

/*
 * Load list of paths
 */
// prepare list to display
$pathList = new pathListIterator();

if( $is_allowedToEdit )
{
    $pathList->load();
}
else
{
    $pathList->load($user_id);
}


/*
 * Output
 */

//-- Content
$cssLoader = CssLoader::getInstance();
$cssLoader->load( 'clpages', 'screen');

$out = '';

$nameTools = get_lang('Learning path list');

$out .= claro_html_tool_title($nameTools);

$out .= $dialogBox->render();

$cmdMenu = array();
if($is_allowedToEdit)
{
    $cmdMenu[] = claro_html_cmd_link('admin/edit_path.php?cmd=rqEdit'. claro_url_relay_context('&amp;'), '<img src="' . get_icon_url('learnpath_new') . '" border="0" alt="" />' . get_lang('Create a new learning path'));
    
    if( get_conf( 'import_allowed' ) )
    {
        $cmdMenu[] = claro_html_cmd_link('index.php?cmd=rqImport' . claro_url_relay_context('&amp;'), '<img src="' . get_icon_url('import') . '" border="0" alt="" />' . get_lang('Import a learning path'));
    }

    if( get_conf('is_trackingEnabled') )
    {
        $cmdMenu[] = claro_html_cmd_link( './track_path_list.php'. claro_url_relay_context('?'), '<img src="' . get_icon_url('statistics') . '" border="0" alt="" />' . get_lang('Learning paths tracking'));
    }
}

$out .= '<p>'
.    claro_html_menu_horizontal( $cmdMenu )
.    '</p>';

$out .= '<table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">' . "\n"
.    '<thead>' . "\n"
.    '<tr class="headerX" align="center" valign="top">' . "\n";

if( $is_allowedToEdit )
{
    // display path name and tools to edit it
    // titles
    $out .= '<th>' . get_lang('Learning path') . '</th>' . "\n"
    .    '<th>' . get_lang('Modify') . '</th>' . "\n"
    .    '<th>' . get_lang('Delete') . '</th>' . "\n"
    //.    '<th>' . get_lang('Block') . '</th>' . "\n"
    .    '<th>' . get_lang('Visibility') . '</th>' . "\n"
    .    '<th colspan="2">' . get_lang('Order') . '</th>' . "\n";
    
    if( get_conf( 'export_allowed') )
    {
        $out .= '<th>' . get_lang('Export').'</th>' . "\n";
    }

    if( get_conf('is_trackingEnabled') ) $out .= '<th>' . get_lang('Tracking') . '</th>' . "\n";

    $out .= '</tr>' . "\n"
    .    '</thead>' . "\n";

    $lpCount = count($pathList);
    if( $lpCount > 0 )
    {
        $i = 0;

        $out .= '<tbody>' . "\n";

        foreach( $pathList as $aPath )
        {
            $i++;

            $out .= '<tr align="center"' . (($aPath['visibility'] == 'INVISIBLE')? 'class="invisible"': '') . '>' . "\n";
            // title
            $out .= '<td align="left">'
            .    '<a href="'. htmlspecialchars( Url::Contextualize( 'viewer/index.php?pathId='.$aPath['id'] ) ).'" title="'.htmlspecialchars(strip_tags($aPath['description'])).'">'
            .    '<img src="' . get_icon_url('learnpath') .'" alt="" border="0" /> '
            .    htmlspecialchars($aPath['title'])
            .    '</a>' . "\n"
            .    '</td>';
            // edit
            $out .= '<td>' . "\n"
            .    '<a href="'. htmlspecialchars( Url::Contextualize( 'admin/edit_path.php?pathId=' . $aPath['id'] ) ) . '">' . "\n"
            .    '<img src="' . get_icon_url('edit') . '" border="0" alt="' . get_lang('Modify') . '" />' . "\n"
            .    '</a>'
            .    '</td>' . "\n";

            // delete
            $out .= '<td>' . "\n"
            .    '<a href="'. htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDelete&amp;pathId=' . $aPath['id'] ) ) . '">' . "\n"
            .    '<img src="' . get_icon_url('delete') . '" border="0" alt="' . get_lang('delete') . '" />' . "\n"
            .    '</a>'
            .    '</td>' . "\n";

            // block/unblock
            /*if( $aPath['lock'] == 'OPEN' )
            {
                $out .= '<td>' . "\n"
                .    '<a href="'. htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exLock&amp;pathId=' . $aPath['id'] ) ) . '">' . "\n"
                .    '<img src="' . get_icon_url('unblock') . '" border="0" alt="' . get_lang('Block') . '" />' . "\n"
                .    '</a>'
                .    '</td>' . "\n";
            }
            else
            {
                $out .= '<td>' . "\n"
                .    '<a href="'. htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exUnlock&amp;pathId=' . $aPath['id'] ) ) . '">' . "\n"
                .    '<img src="' . get_icon_url('block') . '" border="0" alt="' . get_lang('Unblock') . '" />' . "\n"
                .    '</a>'
                .    '</td>' . "\n";
            }*/
            // visible/invisible
            if( $aPath['visibility'] == 'VISIBLE' )
            {
                $out .= '<td>' . "\n"
                .    '<a href="'. htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exInvisible&amp;pathId=' . $aPath['id'] ) ) . '">' . "\n"
                .    '<img src="' . get_icon_url('visible') . '" border="0" alt="' . get_lang('Make invisible') . '" />' . "\n"
                .    '</a>'
                .    '</td>' . "\n";
            }
            else
            {
                $out .= '<td>' . "\n"
                .    '<a href="'. htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exVisible&amp;pathId=' . $aPath['id'] ) ) . '">' . "\n"
                .    '<img src="' . get_icon_url('invisible') . '" border="0" alt="' . get_lang('Make visible') . '" />' . "\n"
                .    '</a>'
                .    '</td>' . "\n";
            }
            // order
            // Move up
            if( $i > 1 )
            {
                $out .= '<td>' . "\n"
                .    '<a href="' . htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=exMoveUp&amp;pathId=' . $aPath['id'] ) ) . '">' . "\n"
                .    '<img src="' . get_icon_url('move_up') . '" alt="' . get_lang('Move up') . '" border="0" />' . "\n"
                .    '</a>' . "\n"
                .    '</td>' . "\n";
            }
            else
            {
                $out .= '<td>&nbsp;</td>' . "\n";
            }

            // Move down
            if( $i < $lpCount )
            {
                $out .= '<td>' . "\n"
                .    '<a href="' . htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=exMoveDown&amp;pathId=' . $aPath['id'] ) ) . '">' . "\n"
                .    '<img src="' . get_icon_url('move_down') . '" alt="' . get_lang('Move down') . '" border="0" />' . "\n"
                .    '</a>' . "\n"
                .    '</td>' . "\n";
            }
            else
            {
                $out .= '<td>&nbsp;</td>' . "\n";
            }

            // export
            if( get_conf( 'export_allowed' ) )
            {
                $out .= '<td>' . "\n"
                .    '<a href="'. htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exExport&amp;pathId=' . $aPath['id'] ) ) . '" onclick="return confirm(\'' . get_lang( 'Only Exercises and documents will be exported.' ) . '\')";>' . "\n"
                .    '<img src="' . get_icon_url('export') . '" border="0" alt="' . get_lang('Export') . '" />' . "\n"
                .    '</a>'
                .    '</td>' . "\n";
            }
            
            // tracking
            $out .= '<td>' . "\n"
            .    '<a href="' . htmlspecialchars( Url::Contextualize( get_module_url('GRAPPLE') . '/track_path.php?pathId=' . $aPath['id'] ) ) . '">' . "\n"
            .    '<img src="' . get_icon_url('statistics') . '" border="0" alt="' . get_lang('Statistics') . '" />' . "\n"
            .    '</a>'
            .    '</td>' . "\n";

            $out .= '</tr>' . "\n\n";
        }
        $out .= '</tbody>' . "\n";
    }
    else
    {
        $out .= '<tfoot>' . "\n"
        .    '<tr>' . "\n"
        .    '<td align="center" colspan="9">' . get_lang('No learning path') . '</td>' . "\n"
        .    '</tr>' . "\n"
        .    '</tfoot>' . "\n";
    }
}
else
{
    // display pah name and module progression
    // titles
    $out .= '<th>' . get_lang('Learning path') . '</th>' . "\n"
    .    '<th colspan="2">' . get_lang('Progress') . '</th>' . "\n"
    .    '</tr>' . "\n"
    .    '</thead>' . "\n\n";

    $lpCount = count($pathList);
    if( $lpCount > 0 )
    {
        $i = 0;
        $totalProgress = 0;

        $out .= '<tbody>' . "\n";

        foreach( $pathList as $aPath )
        {
            $i++;
            $out .= '<tr>' . "\n";

            // title
            $out .= '<td>' . "\n"
            .    '<a href="' . htmlspecialchars( Url::Contextualize( 'viewer/index.php?pathId='.$aPath['id'] ) ) .'" title="'.htmlspecialchars(strip_tags($aPath['description'])).'">'
            .    '<img src="' . get_icon_url('learnpath') . '" alt="" border="0" />'
            .    htmlspecialchars($aPath['title'])
            .    '</a>' . "\n"
            .    '</td>' . "\n";

            //load Attempt
	    $thisAttempt = new Attempt();
	    $lpProgress = 0;
	    if( is_null($aPath['id']) )
	    {
		// cannot find path ... 
		lpDebug('cannot find attempt');
	    }
	    else
	    {
				if( $user_id )
				{
						$thisAttempt->load( $aPath['id'], $user_id );
						$itemList = new PathUserItemList($aPath['id'], $user_id, $thisAttempt->getId());
				}
				else
				{
						$itemList = new PathItemList( $aPath['id'] );
				}
				
				$lpProgress = $thisAttempt->getProgress();
	    }
            
            // compute global progression
            $totalProgress += max(0,$lpProgress);

            // progression
            $out .= '<td align="right">'
            .    '<a href="' . htmlspecialchars( Url::Contextualize( get_module_url('GRAPPLE') . '/track_path_details.php?pathId=' . $aPath['id'] ) ) . '" title="'.get_lang('See details').'">' . claro_html_progress_bar($lpProgress, 1) . '</a>' . "\n"
            .    '</td>' . "\n"
            .    '<td align="left">'
            .    '<small><a href="' . htmlspecialchars( Url::Contextualize( get_module_url('GRAPPLE') . '/track_path_details.php?pathId=' . $aPath['id'] ) ) . '" title="'.get_lang('See details').'">' . $lpProgress . '%</a></small>'
            .    '</td>' . "\n"
            ;

            $out .= '</tr>' . "\n\n";
        }

        // $i should not be lower than 1, but we use max trick to prevent division by 0
        $courseProgress = round( $totalProgress / max(1,$i) );

        $out .= '</tbody>' . "\n\n"
        .    '<tfoot>' . "\n"
        .    '<tr>' . "\n"
        .    '<td colspan="2">' . "\n"
        .    '&nbsp;' . "\n"
        .    '</td>' . "\n"
        .    '</tr>' . "\n"
        .    '<tr>' . "\n"
        .    '<td align ="right">' . "\n"
        .    get_lang('Course progression') . "\n"
        .    ' :' . "\n"
        .    '</td>' . "\n"
        .    '<td align="right" >' . "\n"
        .    claro_html_progress_bar($courseProgress, 1)
        .    '</td>' . "\n"
        .    '<td align="left">' . "\n"
        .    '<small>'
        .    $courseProgress . '%' . "\n"
        .    '</small>' . "\n"
        .    '</td>' . "\n"
        .    '</tr>' . "\n"
        .    '</tfoot>' . "\n\n";
    }
    else
    {
        $out .= '<tfoot>' . "\n"
        .    '<tr>' . "\n"
        .    '<td align="center" colspan="3">' . get_lang('No learning path') . '</td>' . "\n"
        .    '</tr>' . "\n"
        .    '</tfoot>' . "\n";
    }
}


$out .= '</table>' . "\n";


$claroline->display->body->appendContent($out);

echo $claroline->display->render();

?>