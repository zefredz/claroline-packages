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
 * @package CLLP
 *
 * @author Sebastien Piraux
 *
 */

$tlabelReq = 'CLLP';

require_once dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';

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

/*
 * On the fly install
 */

install_module_in_course( 'CLLP', claro_get_current_course_id() ) ;


require_once dirname( __FILE__ ) . '/lib/path.class.php';

/*
 * init request vars
 */
$acceptedCmdList = array(   'rqCreate', 'exCreate', 
                            'rqDelete', 'exDelete', 
                            'exLock', 'exUnlock', 
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

$pathList = new pathList();


claro_set_display_mode_available(true);

$is_allowedToEdit = claro_is_allowed_to_edit();
$user_id = claro_get_current_user_id();

$dialogBox = '';

/*
 * Admin only commands 
 */
 
if( $is_allowedToEdit )
{
    if( $cmd == 'exCreate' )
    {
        $path->setTitle($_REQUEST['title']);
        $path->setDescription($_REQUEST['description']);       
        // use default values for other fields
        
        if( $path->validate() )
        {
	        if( $newPathId = $path->save() )
	        {
	            $dialogBox .= get_lang('Empty learning path successfully created');
	        }
	        else 
	        {
	            $dialogBox .= get_lang('Fatal error : cannot save');
	        }
        }
        else
        {
        	$dialogBox .= '<p>' . get_lang('Missing field : title is mandatory.') . '</p>';	
        	$cmd = 'rqCreate';
        }
    }
    
    if( $cmd == 'rqCreate' )
    {
        $dialogBox .= "\n\n"
        .    '<strong>' . get_lang('Create a new learning path') . '</strong>' . "\n"        
        .    '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">' . "\n"
        .    claro_form_relay_context()
        .    '<input type="hidden" name="claroFormId" value="'.uniqid('').'">'."\n"
        .    '<label for="title">' . get_lang('Title') . ' : </label>' . "\n"
        .    '<br />' . "\n"
        .    '<input type="text" name="title" id="title" maxlength="255" value="' . htmlspecialchars($path->getTitle()). '" />' . "\n"
        .    '<br />' . "\n"
        .    '<label for="description">' . get_lang('Description') . ' : </label>' . "\n"
        .    '<br />' . "\n"
        .    '<textarea id="description" name="description" rows="5" cols="50">'
        .	 htmlspecialchars($path->getDescription())
        .    '</textarea>' . "\n"
        .    '<br /><br />' . "\n"
        .    '<input type="hidden" name="cmd" value="exCreate" />' . "\n"
        .    '<input type="submit" value="' . get_lang('Ok') . '" />&nbsp;' . "\n"
        .    claro_html_button('index.php', get_lang('Cancel'))
        .    '</form>' . "\n"
        ;
    }
    
    if( $cmd == 'exImport')
    {
    	// include import lib
    	require_once dirname( __FILE__ ) . '/../lib/xmlize.php';
        require_once dirname( __FILE__ ) . '/../lib/scorm.import.lib.php';
    	// exec import
    	
    	// display import log (use backlog)
    	
    	// display import result 'Success' or 'Failed'
    }
    
    if( $cmd == 'rqImport' )
    {
		include_once get_path('incRepositorySys') . '/lib/fileUpload.lib.php';
		include_once get_path('incRepositorySys') . '/lib/fileDisplay.lib.php';
    	
    	$maxFilledSpace = 100000000;

		$courseDir   = claro_get_course_path() . '/scormPackages/';
		$baseWorkDir = get_path('coursesRepositorySys').$courseDir;
		
        $dialogBox .= "\n\n"
        .    '<strong>' . get_lang('Import a learning path') . '</strong>' . "\n"        
        .    '<form enctype="multipart/form-data" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . "\n"
        .    claro_form_relay_context()
        .    '<input type="hidden" name="claroFormId" value="'.uniqid('').'">'."\n"
        .    '<label for="title">' . get_lang('Title') . ' : </label>' . "\n"
        .    '<br />' . "\n"
		.	 '<input type="file" name="uploadedPackage" />' . "\n"
		.	 '<br />' . "\n"
		.	 '<small>' . get_lang('Max file size : %size', array('%size' => format_file_size( get_max_upload_size($maxFilledSpace,$baseWorkDir) ) ) ) . '</small>' . "\n"
        .    '<br /><br />' . "\n"
        .    '<input type="hidden" name="cmd" value="exCreate" />' . "\n"
        .    '<input type="submit" value="' . get_lang('Ok') . '" />&nbsp;' . "\n"
        .    claro_html_button('index.php', get_lang('Cancel'))
        .    '</form>' . "\n"
        ;
    }
        
    if( $cmd == 'exDelete' )
    {
    	if( $path->delete() )
    	{
    		$dialogBox .= get_lang('Path succesfully deleted');
    	}
    	else
    	{
    		$dialogBox .= get_lang('Fatal error : cannot delete path');
    	}
    }
    
    if( $cmd == 'rqDelete' )
    {
        $dialogBox .= get_lang('Are you sure to delete learning path "%pathTitle" ?', array('%pathTitle' => htmlspecialchars($path->getTitle()) ));
        
        $dialogBox .= '<p>' 
        .    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exDelete&amp;pathId='.$pathId.'">' . get_lang('Yes') . '</a>' 
        .    '&nbsp;|&nbsp;' 
        .    '<a href="' . $_SERVER['PHP_SELF'] . '">' . get_lang('No') . '</a>' 
        .    '</p>' . "\n";
    }
    
    if( $cmd == 'exLock' )
    {
    	$path->lock();
    	
    	$path->save();
    }
    
    if( $cmd == 'exUnlock' )
    {
    	$path->unlock();
    	
    	$path->save();
    }
    
    if( $cmd == 'exVisible' )
    {
    	$path->setVisible();
    	
    	$path->save();
    }
    
    if( $cmd == 'exInvisible' )
    {
    	$path->setInvisible();
    	
    	$path->save();
    }

    if( $cmd == 'exMoveUp' )
    {
    	$pathList->movePathUp($path);
    }
    
    if( $cmd == 'exMoveDown' )
    {
    	$pathList->movePathDown($path);
    }
        
    if( $cmd == 'exExport' )
    {
    	// TODO
    }
}

// prepare list to display
if( $is_allowedToEdit )
{
    $pathListArray = $pathList->load();
}
else
{
    $pathListArray = $pathList->load($user_id);
}


/*
 * Output
 */

//-- Content 
$nameTools = get_lang('Learning path list');

include  get_path('includePath') . '/claro_init_header.inc.php';

echo claro_html_tool_title($nameTools);

if ( !empty($dialogBox) ) echo claro_html_message_box($dialogBox);

$cmdMenu = array();
if($is_allowedToEdit)
{
    $cmdMenu[] = claro_html_cmd_link('index.php?cmd=rqCreate'. claro_url_relay_context('&amp;'),get_lang('Create a new learning path'));
    $cmdMenu[] = claro_html_cmd_link('index.php?cmd=rqImport' . claro_url_relay_context('&amp;'),get_lang('Import a learning path'));

    if( get_conf('is_trackingEnabled') )
    {
        $cmdMenu[] = claro_html_cmd_link( get_path('clarolineRepositoryWeb') . 'tracking/learnPath_detailsAllPath.php'. claro_url_relay_context('?'),get_lang('Learning paths tracking'));
    }
}

echo '<p>'
.    claro_html_menu_horizontal( $cmdMenu )
.    '</p>';

echo '<table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">' . "\n"
.    '<thead>' . "\n"
.    '<tr class="headerX" align="center" valign="top">' . "\n";
  
if( $is_allowedToEdit )
{
    // display path name and tools to edit it
    // titles  
    echo '<th>' . get_lang('Learning path') . '</th>' . "\n"
    .    '<th>' . get_lang('Modify') . '</th>' . "\n"
    .    '<th>' . get_lang('Delete') . '</th>' . "\n"
    .    '<th>' . get_lang('Block') . '</th>' . "\n"
    .    '<th>' . get_lang('Visibility') . '</th>' . "\n"
    .    '<th colspan="2">' . get_lang('Order') . '</th>' . "\n"
    .    '<th>' . get_lang('Export').'</th>' . "\n";
 
    if( get_conf('is_trackingEnabled') ) echo '<th>' . get_lang('Tracking') . '</th>' . "\n";
    
    echo '</tr>' . "\n"
    .    '</thead>' . "\n";
    
    if( !empty($pathListArray) && is_array($pathListArray) )
    {
        $i = 0;
        $lpCount = count($pathListArray);
        
        echo '<tbody>' . "\n";
        
        foreach( $pathListArray as $aPath )
        {
            $i++;
            
            echo '<tr align="center"' . (($aPath['visibility'] == 'INVISIBLE')? 'class="invisible"': '') . '>' . "\n";
            // title
            echo '<td align="left">' 
            .    '<a href="viewer/index.php?pathId='.$aPath['id'].'" title="'.htmlspecialchars(strip_tags($aPath['description'])).'">'
            .    '<img src="' . get_path('imgRepositoryWeb') . 'learnpath.gif" alt="" border="0" />'
            .    htmlspecialchars($aPath['title'])            
            .    '</a>' . "\n"
            .    '</td>';
            // edit
            echo '<td>' . "\n"
	        .    '<a href="admin/edit_path.php?pathId=' . $aPath['id'] . '">' . "\n"
	        .    '<img src="' . get_path('imgRepositoryWeb') . 'edit.gif" border="0" alt="' . get_lang('Modify') . '" />' . "\n"
	        .    '</a>'
	        .    '</td>' . "\n";
	         
            // delete
            echo '<td>' . "\n"
	        .    '<a href="'.$_SERVER['PHP_SELF'].'?cmd=rqDelete&amp;pathId=' . $aPath['id'] . '">' . "\n"
	        .    '<img src="' . get_path('imgRepositoryWeb') . 'delete.gif" border="0" alt="' . get_lang('delete') . '" />' . "\n"
	        .    '</a>'
	        .    '</td>' . "\n";
	                     
            // block/unblock
            if( $aPath['lock'] == 'OPEN' )
            {
	            echo '<td>' . "\n"
		        .    '<a href="'.$_SERVER['PHP_SELF'].'?cmd=exLock&amp;pathId=' . $aPath['id'] . '">' . "\n"
		        .    '<img src="' . get_path('imgRepositoryWeb') . 'unblock.gif" border="0" alt="' . get_lang('Block') . '" />' . "\n"
		        .    '</a>'
		        .    '</td>' . "\n";    
            }
            else
            {
				echo '<td>' . "\n"
		        .    '<a href="'.$_SERVER['PHP_SELF'].'?cmd=exUnlock&amp;pathId=' . $aPath['id'] . '">' . "\n"
		        .    '<img src="' . get_path('imgRepositoryWeb') . 'block.gif" border="0" alt="' . get_lang('Unblock') . '" />' . "\n"
		        .    '</a>'
		        .    '</td>' . "\n";            	
            }        
            // visible/invisible
            if( $aPath['visibility'] == 'VISIBLE' )
            {
	            echo '<td>' . "\n"
		        .    '<a href="'.$_SERVER['PHP_SELF'].'?cmd=exInvisible&amp;pathId=' . $aPath['id'] . '">' . "\n"
		        .    '<img src="' . get_path('imgRepositoryWeb') . 'visible.gif" border="0" alt="' . get_lang('Make invisible') . '" />' . "\n"
		        .    '</a>'
		        .    '</td>' . "\n";    
            }
            else
            {
				echo '<td>' . "\n"
		        .    '<a href="'.$_SERVER['PHP_SELF'].'?cmd=exVisible&amp;pathId=' . $aPath['id'] . '">' . "\n"
		        .    '<img src="' . get_path('imgRepositoryWeb') . 'invisible.gif" border="0" alt="' . get_lang('Make visible') . '" />' . "\n"
		        .    '</a>'
		        .    '</td>' . "\n";            	
            }                    
            // order
            // Move up
            if( $i > 1 )
            {
                echo '<td>' . "\n"
                .    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exMoveUp&amp;pathId=' . $aPath['id'] . '">' . "\n"
                .    '<img src="' . get_path('imgRepositoryWeb') . 'up.gif" alt="' . get_lang('Move up') . '" border="0" />' . "\n"
                .    '</a>' . "\n"
                .    '</td>' . "\n";
            }
            else
            {
                echo '<td>&nbsp;</td>' . "\n";
            }

            // Move down
            if( $i < $lpCount )
            {
                echo '<td>' . "\n"
                .    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exMoveDown&amp;pathId=' . $aPath['id'] . '">' . "\n"
                .    '<img src="' . get_path('imgRepositoryWeb') . 'down.gif" alt="' . get_lang('Move down') . '" border="0" />' . "\n"
                .    '</a>' . "\n"
                .    '</td>' . "\n";
            }
            else
            {
                echo '<td>&nbsp;</td>' . "\n";
            }
            
            // export
            echo '<td>' . "\n"
	        .    '<a href="'.$_SERVER['PHP_SELF'].'?cmd=exExport&amp;pathId=' . $aPath['id'] . '">' . "\n"
	        .    '<img src="' . get_path('imgRepositoryWeb') . 'export.gif" border="0" alt="' . get_lang('Export') . '" />' . "\n"
	        .    '</a>'
	        .    '</td>' . "\n";
	                    
            // tracking
            echo '<td>' . "\n"
	        .    '<a href="' . get_path('clarolineRepositoryWeb') . 'tracking/learnPath_details.php?pathId=' . $aPath['id'] . '">' . "\n"
	        .    '<img src="' . get_path('imgRepositoryWeb') . 'statistics.gif" border="0" alt="' . get_lang('Statistics') . '" />' . "\n"
	        .    '</a>'
	        .    '</td>' . "\n";
	                    
            echo '</tr>' . "\n\n";
        }
        echo '</tbody>' . "\n";
    }
    else
    {
        echo '<tfoot>' . "\n"
        .    '<tr>' . "\n"
        .    '<td align="center" colspan="8">' . get_lang('No learning path') . '</td>' . "\n"
        .    '</tr>' . "\n"
        .    '</tfoot>' . "\n";
    }
}
else
{
    // display pah name and module progression
    // titles
    echo '<th>' . get_lang('Learning path') . '</th>' . "\n"
    .    '<th colspan="2">' . get_lang('Progress') . '</th>' . "\n"
    .    '</tr>' . "\n"
    .    '</thead>' . "\n\n";
    
    if( !empty($pathListArray) && is_array($pathListArray) )
    {
        $i = 0;
        $lpCount = count($pathListArray);
        $totalProgress = 0;
        
        echo '<tbody>' . "\n";
        
        foreach( $pathListArray as $aPath )
        {
            $i++;
            echo '<tr>' . "\n";                                
            
            // title
            echo '<td>' . "\n"
            .    '<a href="viewer/index.php?pathId='.$aPath['id'].'" title="'.htmlspecialchars(strip_tags($aPath['description'])).'">' 
            .    '<img src="' . get_path('imgRepositoryWeb') . 'learnpath.gif" alt="" border="0" />'
            .    htmlspecialchars($aPath['title']) 
            .    '</a>' . "\n"
            .    '</td>' . "\n";
            
            // TODO get
            $lpProgress = rand(0,100);
            
            // compute global progression
            $totalProgress += max(0,$lpProgress);
            
            // progression
            echo '<td align="right">'
            .    '<a href="viewer/index.php?pathId='.$aPath['id'].'" title="'.get_lang('See details').'">' . claro_html_progress_bar($lpProgress, 1) . '</a>' . "\n"
            .    '</td>' . "\n"
            .    '<td align="left">'
            .    '<small><a href="" title="'.get_lang('See details').'">' . $lpProgress . '%</a></small>'
            .    '</td>' . "\n"
            ;
                        
            echo '</tr>' . "\n\n";
        }
        
        // $i should not be lower than 1, but to avoid ugly error we use max trick to prevent division by 0
        $courseProgress = round( $totalProgress / max(1,$i) );
        
        echo '</tbody>' . "\n\n"
        .    '<tfoot>' . "\n"
        .    '<tr>' . "\n"
        .    '<td colspan="3">' . "\n"
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
        echo '<tfoot>' . "\n"
        .    '<tr>' . "\n"
        .    '<td align="center" colspan="3">' . get_lang('No learning path') . '</td>' . "\n"
        .    '</tr>' . "\n"
        .    '</tfoot>' . "\n";
    }
}

echo '</table>' . "\n";

include  get_path('includePath') . '/claro_init_footer.inc.php';

?>
