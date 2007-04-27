<?php
// $Id$
// vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:
/**
 * CLAROLINE
 * 
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * 
 * @author Tanguy Delooz <tdelooz@gmail.com>
 */

$tlabelReq = 'CLTASK';

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

install_module_in_course( 'CLTASK', claro_get_current_course_id() );

claro_set_display_mode_available(TRUE);

require_once dirname( __FILE__ ) . '/lib/task.lib.php';
require_once get_path('incRepositorySys') . '/lib/form.lib.php';
require_once dirname( __FILE__ ) . '/lib/sanitizer.class.php';

$san = new HTML_Sanitizer;
$san->allowStyle();
$san->addAdditionalTags('<font>');

// html_sanitize_all -> $san->sanitize

$isAllowedToManage = claro_is_allowed_to_edit();


/**
 * INITIALISATION
 * (Anything from the outside is suspect)
 * 
 */

// id
$id = array_key_exists( 'id', $_REQUEST ) 
    ? (int) $_REQUEST['id']
    : 0
    ; // for deleting after an update

// title
$title = array_key_exists( 'title', $_REQUEST ) 
    ? trim( $_REQUEST['title'] ) 
    : ''
    ;
    
$title = $san->sanitize($title);

// due date
if ( array_key_exists('dueDateYear', $_REQUEST) 
    && array_key_exists('dueDateMonth', $_REQUEST)
    && array_key_exists('dueDateDay', $_REQUEST)
    && array_key_exists('dueDateHour', $_REQUEST)
    && array_key_exists('dueDateMinute', $_REQUEST)
    && array_key_exists('dueDateIsActive', $_REQUEST)
    && '1' == $_REQUEST['dueDateIsActive']
)
{
    $dueDate = $_REQUEST['dueDateYear'] 
    . '-' . $_REQUEST['dueDateMonth'] 
    . '-' . $_REQUEST['dueDateDay'] 
    . ' ' . $_REQUEST['dueDateHour'] 
    . ':' . $_REQUEST['dueDateMinute'] 
    . ':00'
    ;
    
    // converts date to yyyy-mm-dd hh:mm:ss format
    $dueDate = date('Y-m-d H:i:s', strtotime($dueDate));
}
elseif (! array_key_exists('dueDateIsActive', $_REQUEST) )
{
	$dueDate = '0000-00-00 00:00:00';
}
else
{
    $dueDate = '';
}

// start date
if ( array_key_exists('startDateYear', $_REQUEST) 
    && array_key_exists('startDateMonth', $_REQUEST)
    && array_key_exists('startDateDay', $_REQUEST)
    && array_key_exists('startDateHour', $_REQUEST)
    && array_key_exists('startDateMinute', $_REQUEST)
    && array_key_exists('startDateIsActive', $_REQUEST)
    && '1' == $_REQUEST['startDateIsActive']
)
{
    $startDate = $_REQUEST['startDateYear'] 
    . '-' . $_REQUEST['startDateMonth'] 
    . '-' . $_REQUEST['startDateDay'] 
    . ' ' . $_REQUEST['startDateHour'] 
    . ':' . $_REQUEST['startDateMinute'] 
    . ':00'
    ;
    
    $startDate = date('Y-m-d H:i:s', strtotime($startDate));
}
elseif (! array_key_exists('startDateIsActive', $_REQUEST) )
{
    $startDate = '0000-00-00 00:00:00';
}
else
{
    $startDate = '';
}

// end date
if ( array_key_exists('endDateYear', $_REQUEST) 
    && array_key_exists('endDateMonth', $_REQUEST)
    && array_key_exists('endDateDay', $_REQUEST)
    && array_key_exists('endDateHour', $_REQUEST)
    && array_key_exists('endDateMinute', $_REQUEST)
    && array_key_exists('endDateIsActive', $_REQUEST)
    && '1' == $_REQUEST['endDateIsActive']
)
{
    $endDate = $_REQUEST['endDateYear'] 
    . '-' . $_REQUEST['endDateMonth']
    . '-' . $_REQUEST['endDateDay'] 
    . ' ' . $_REQUEST['endDateHour'] 
    . ':' . $_REQUEST['endDateMinute'] 
    . ':00'
    ;
    
    $endDate = date('Y-m-d H:i:s', strtotime($endDate));
}
elseif (! array_key_exists('endDateIsActive', $_REQUEST) )
{
    $endDate = '0000-00-00 00:00:00';
}
else
{
    $endDate = '';
}

// description
$description = array_key_exists( 'description', $_REQUEST ) 
    ? trim( $_REQUEST['description'] ) 
    : ''
    ;
    
$description = $san->sanitize($description);

// priority
$priority = array_key_exists( 'priority', $_REQUEST ) 
    ? (int) $_REQUEST['priority']
    : 0
    ;
    
// progress
define ( 'PROGRESS_NOT_PASSED', -1 );

if ( array_key_exists( 'progress', $_REQUEST ) )
{
    $progress = ( '-' != $_REQUEST['progress'] )
        ? (int) $_REQUEST['progress']
        : null
        ;
}
else
{
    $progress = PROGRESS_NOT_PASSED;
}

// visible
$visible = null;


// URL FLAGS (LIST DISPLAY)
$maskEndedTasks  = ( array_key_exists( 'maskEndedTasks', $_REQUEST ) 
        && 'true' == $_REQUEST['maskEndedTasks'] )
    ? true
    : false
    ;


/**
 * COMMAND SECTION
 * 
 */

if ( $isAllowedToManage )
{
$allowedCommands = array( 
    'showTaskList', 
    'showTask',
    'rqEditTask',
    'exEditTask',
    'rqAddTask',
    'exAddTask',
    'rqDeleteTask',
    'exDeleteTask',
    'mkVisible',
    'mkVisibleFromList',
    'mkInvisible', 
    'mkInvisibleFromList'
);
}
else
{
	$allowedCommands = array( 'showTaskList', 'showTask' );
}
    
$cmd = array_key_exists( 'cmd', $_REQUEST ) 
    && in_array( $_REQUEST['cmd'], $allowedCommands ) 
    ? $_REQUEST['cmd'] 
    : ''
    ;
    

// CONTROL FLAGS
$fatalError = false;

$loadTask = false;
$setTaskProperties = false;
$setTaskVisibility = false;
$saveTask = false;
$deleteTask = false;
$loadList = false;

// DISPLAY FLAGS
$dispToolMenu = true;

$dispList = false;
$dispTask = false;
$dispForm = false;
$dispConfirm = false;

$nextCmd = null;
$cancelCmd = null;

claro_set_display_mode_available(true);

switch ( $cmd )
{
	case 'showTask':
    {
        $loadTask = true;                    
        $dispTask = true;
    } break;
    
    case 'rqEditTask':
    {
        claro_set_display_mode_available(false);
        $loadTask = true;     
        $nextCmd = 'exEditTask';
        $cancelCmd = 'showTask&amp;id='.(int)$id;              
        $dispForm = true;
    } break;
    
    case 'exEditTask':
    {       
        $message = get_lang( 'The task has been updated' );
        $loadTask = true; // compare
        $setTaskProperties = true; // set values from REQUEST  
        $saveTask = true;   
        $dispTask = true;
    } break;
    
    case 'rqAddTask':
    {   
    	claro_set_display_mode_available(false);
        $nextCmd = 'exAddTask';
        $cancelCmd = 'showTaskList';
        $dispForm = true;
    } break;
    
    case 'exAddTask':
    {
        $message = get_lang( 'The task has been saved' );
        $setTaskProperties = true;
        $saveTask = true;               
        $dispTask = true;
    } break;
    
    case 'rqDeleteTask':
    {   
    	claro_set_display_mode_available(false);
    	$loadTask = true;        
        $dispConfirm = true;
    } break;
    
    case 'exDeleteTask':
    {
        $message = get_lang( 'The task has been deleted' );
        $deleteTask = true;
        $loadList = true;        
        $dispList = true;
    } break;
    
    case 'mkVisible':
    case 'mkVisibleFromList':
    case 'mkInvisible':
    case 'mkInvisibleFromList':
    {   
    	if ( 'mkVisible' == $cmd || 'mkVisibleFromList' == $cmd )
        {
        	$message = get_lang( 'The task is now visible' );
            $visible = true;
        }
        else
        {
        	$message = get_lang( 'The task is now invisible' );
            $visible = false;
        }
        
        $loadTask = true;
        $setTaskVisibility = true;
        $saveTask = true;
        
        if ( 'mkVisible' == $cmd || 'mkInvisible' == $cmd)
        {
        	$dispTask = true;
        }
        else
        {
        	$loadList = true;
            $dispList = true;
        }
    } break;    
    
    case 'showTaskList':
    default :
    {
        $loadList = true;        
        $dispList = true;      
    }
}


// CRUD CONTROL with Task and TaskList objects
$task =& new Task;


// LOAD TASK
if ( $loadTask )
{
    if ( 0 != $id )
    {
        if ( false === ( $task->load( $id ) ) )
        {
            $fatalError = true;
            $message = get_lang( 'Fatal error : could not load task' );
        }
        elseif ( !$isAllowedToManage && $task->isInvisible() )
        {
        	$fatalError = true;
            $message = get_lang( 'Fatal error : you cannot access this task' );
            $dispTask = false;
        }   
    }
    else
    {
        //$fatalError = true;
        $message = get_lang( 'Error : missing task id' );      
        
        //$loadTask = false;
        $setTaskProperties = false;
        $saveTask = false;
        $deleteTask = false;
        $loadList = true; //false
        
        $dispList = true; //false
        $dispTask = false;
        $dispForm = false;
        $dispConfirm = false;
    } 
}


// SET TASK PROPERTIES
if ( $setTaskProperties)
{
    if ( !empty( $title ) )
    {
    	$task->setTitle( $title );
    }
    else
    {
        $message = get_lang( 'Please enter a title for the task' );
        
        $saveTask = false;
        
        $dispTask = false;
        $dispForm = true;
    }
    
    if ( !empty( $startDate ) ) $task->setStartDate( $startDate );
    if ( !empty( $endDate ) ) $task->setEndDate( $endDate );
    if ( !empty( $dueDate ) ) $task->setDueDate( $dueDate );
    if ( !empty( $description ) ) $task->setDescription( $description );
    if ( !empty( $priority ) ) $task->setPriority( $priority );
    if ( PROGRESS_NOT_PASSED != $progress ) $task->setProgress( $progress );
}


// SET TASK VISIBILITY
if ( $setTaskVisibility )
{
	if ( !is_null( $visible ) )
    {
    	if ( $visible ) 
        {
            $task->setVisible();
        }
        else
        {
            $task->setInvisible();
        }
    }
    else
    {
    	// should not happen as $visible is set in the switch( $cmd )
    }
}


// SAVE OR UPDATE TASK PROPERTIES
if ( $saveTask )
{          
	if ( false === $task->save() )
    {
    	$fatalError = true;
        $message = get_lang( 'Fatal error : could not save task' );
    }
}


// DELETE TASK
if ( $deleteTask )
{
    if ( false === $task->delete( $id ) )
    {
    	$fatalError = true;
        $message = get_lang( 'Fatal error : could not delete task' );
    }
}


// LOAD LIST
if ( $loadList )
{
    $taskList =& new TaskList;
    
    $listArray = $taskList->loadAll( !$isAllowedToManage, $maskEndedTasks );
    
    if ( false === ( $listArray ) )
    {
        $fatalError = true;
        $message = get_lang( 'Fatal error : could not load task list' );
    }
}



/**
 *  DISPLAY SECTION
 * 
 */
 
if ( $fatalError )
{
    claro_die( $message );
}
 
// Update interbredcrump
$interbredcrump[] = array(
	'url' => 'entry.php',
	'name' => 'ToDo List'
);

// Set style
$htmlHeadXtra[] = '<link rel="stylesheet" type="text/css" ' 
    . 'href="./css/task.css" media="screen, projection, tv" />' . "\n";

// Javascript
$htmlHeadXtra[] = '<script type="text/javascript">
function confirmTaskDelete(id){
	if(confirm(\'' 
    .    clean_str_for_javascript( 
           get_lang('Please confirm your choice') 
         ) 
    .    '\')){
    	 window.location= "'.$_SERVER['PHP_SELF'].'?cmd=exDeleteTask&id="+id;
         return false;
    }
    else {
        return false;	
    }
}
</script>';


// DISPLAY HEADER

require_once get_path('includePath') . '/claro_init_header.inc.php';


// DISPLAY TOOL TITLE

$toolName = get_lang( 'ToDo List' );
$TitleArray = array();

if ( $dispList )
{
    $TitleArray['mainTitle'] = $toolName;
}

if ( $dispTask )
{
	$TitleArray['supraTitle'] = $toolName;
    $TitleArray['mainTitle'] = $task->getTitle();
}

if ( $dispForm )
{
	switch( $nextCmd )
    {
    	case 'exAddTask':
        {
        	$TitleArray['mainTitle'] = $toolName;
            $TitleArray['subTitle'] = get_lang( 'Add a new task' );
        }break;
        
        case 'exEditTask' :
        {
        	$TitleArray['supraTitle'] = $toolName;
            $TitleArray['mainTitle'] = $task->getTitle();
            $TitleArray['subTitle'] = get_lang ( 'Edit the task' );
        }break;
        
        default :
        {
        	$TitleArray['mainTitle'] = $toolName;
        }
    }
}

if ( $dispConfirm )
{
	$TitleArray['supraTitle'] = $toolName;
    $TitleArray['mainTitle'] = $task->getTitle();
    $TitleArray['subTitle'] = get_lang ( 'Delete the task' );
}

echo claro_html_tool_title( $TitleArray );


// DISPLAY MESSAGE

if ( !empty( $message ) ) echo claro_html_message_box( $message );


// DISPLAY TOOL MENU

if ( $dispToolMenu )
{
	// Construction of the tool menu
    $toolMenu = array();
    
    //if ( ! $dispList )
    {
        $toolMenu[] = '<a href="' . $_SERVER['PHP_SELF'] . '" ' 
        .             'class="claroCmd">' 
        .             '<img src="' . get_icon( 'info' ) . '" ' 
        .             'alt="Task list" />' 
        .             get_lang( 'Task list' )
        .             '</a>'
        ;
    }
    
    if ( $isAllowedToManage )
    {
        $toolMenu[] = '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=rqAddTask" ' 
        .             'class="claroCmd">' 
        .             '<img src="' . get_icon( 'new' ) . '" ' 
        .             'alt="New task" />' 
        .             get_lang( 'New task' )
        .             '</a>'
        ;
    }
    
    echo '<p>'
    .    claro_html_menu_horizontal( $toolMenu )
    .    '</p>'
    ;  
}


// VARIABLES FOR DISPLAY

$priorityName = array( 
    '-', 
    get_lang('Low'), 
    get_lang('Medium'), 
    get_lang('High') 
);

$priorityStyle = array( 
    'priorityNone', 
    'priorityLow', 
    'priorityMedium',  
    'priorityHigh' 
);
    
$priorityIcon = array(
    1 => 'priority_low', 
    2 => 'priority_medium', 
    3 => 'priority_high' 
);


// DISPLAY THE LIST OF TASKS

if ( $dispList )
{
	// Construction and display of the task list menu
    //if ( $isAllowedToManage )
    {
        $taskListMenu = array(); 
        
        // Show all tasks ( when ended tasks are hidden )
        $menuItem = ( $maskEndedTasks 
            ? '<a href="' . $_SERVER['PHP_SELF'] . '?maskEndedTasks=false" ' 
                . 'class="claroCmd">'
            : '<span class="claroCmdDisabled">'
            );
        
        $menuItem .= get_lang( 'Show all tasks' )
            . ( $maskEndedTasks ? '</a>' : '</span>' )
            ;
        
        $taskListMenu[] = $menuItem;
            
        // Hide ended tasks ( when all tasks are showed )
        $menuItem = ( !$maskEndedTasks 
            ? '<a href="' . $_SERVER['PHP_SELF'] . '?maskEndedTasks=true" '
                . 'class="claroCmd">'
            : '<span class="claroCmdDisabled">'
            );
        
        $menuItem .= get_lang( 'Hide ended tasks' )
            . ( !$maskEndedTasks ? '</a>' : '</span>' )
            ;
        
        $taskListMenu[] = $menuItem;
            
        echo '<p>'
        .    claro_html_menu_horizontal( $taskListMenu )
        .    '</p>'
        ;  
    } 
    
    // Display of the task list
    echo '<table class="claroTable emphaseLine" width="100%" ' 
    .    'summary="' . get_lang('Task list') . '">' 
    
    .    '<thead>' 
    .    '<tr class="headerX">' 
    //.    '<th>' . get_lang("Priority") . '</th>' 
    .    '<th>&nbsp;</th>' 
    .    '<th>' . get_lang("Task") . '</th>' 
    .    '<th>' . get_lang("Due date") . '</th>'
    .    '<th>' . get_lang("Start date") . '</th>' 
    .    '<th>' . get_lang("End date") . '</th>' 
    .    '<th>' . get_lang("Progress") . '</th>' 
    ;
    
    if ( $isAllowedToManage )
    {
        echo '<th>' . get_lang("Modify") . '</th>' 
        .    '<th>' . get_lang("Delete") . '</th>' 
        //.  '<th>' . get_lang("Move") . '</th>' 
        .    '<th>' . get_lang("Visibility") . '</th>' 
        ;
    }
    
    echo '</tr>' 
    .    '</thead>' 
    
    .    '<tbody>'
    ;

    if ( ! empty( $listArray ) && is_array( $listArray ) )
    {
		foreach ($listArray as $tsk)
		{
			echo '<tr' . ( $tsk['visible'] ? '' : ' class="invisible"' ) . '>' 
            
            // priority
            .    '<td class="' . $priorityStyle[$tsk['priority']] . '">' 
            ;
            
            if ( 0 == $tsk['priority'] )
            {
            	echo $priorityName[$tsk['priority']];
            }
            else
            {
                echo '<img ' 
                .    'src="' . get_icon( $priorityIcon[$tsk['priority']] ) . '" '
                .    'alt="' . $priorityName[$tsk['priority']] . '" border="0"' 
                .    '> '
                ;   
            }
            
            echo '</td>' 
            
            // title
            .    '<td>' 
            .    '<a href="' . $_SERVER['PHP_SELF'] 
            .    '?cmd=showTask&amp;id=' . (int)$tsk['id'] . '">' 
            .    '<img src="' . get_icon( 'enter' ) . '" '
            .    'alt="Enter" border="0"> '    
            .    htmlspecialchars($tsk['title']) 
            .    '</a>' 
            .    '</td>' 
            ;
            
            // due date
            if ( '0000-00-00 00:00:00' != $tsk['dueDate']
                && time() >= strtotime($tsk['dueDate'])
                && ( '0000-00-00 00:00:00' == $tsk['endDate']  
                    || time() < strtotime($tsk['endDate']) ) 
                && 100 != $tsk['progress']
               )
            {
                echo '<td class="dueDateExceeded">';
            }
            else
            {
                echo '<td>';
            }
            
            echo ( $tsk['dueDate'] == '0000-00-00 00:00:00' 
                     ? '-' 
                     : claro_html_localised_date( get_locale('dateTimeFormatShort')
                         , strtotime($tsk['dueDate']) ) 
                 ) 
            .    '</td>'
            
            // start date
            .    '<td>' 
            .    ( $tsk['startDate'] == '0000-00-00 00:00:00' 
                     ? '-' 
                     : claro_html_localised_date( get_locale('dateTimeFormatShort')
                         , strtotime($tsk['startDate']) )
                 ) 
            .    '</td>'
            ;
            
            // end date 
            if ( $tsk['endDate'] != '0000-00-00 00:00:00'
                && time() >= strtotime($tsk['endDate']) )
            {   
                echo '<td class="taskEnded">';
            }
            else
            {
                echo '<td>';
            }
            
            echo ( $tsk['endDate'] == '0000-00-00 00:00:00' 
                     ? '-' 
                     : claro_html_localised_date( get_locale('dateTimeFormatShort')
                         , strtotime($tsk['endDate']) ) 
                 )
            .    '</td>'
            ;  
            
            // progress
                //.  '<td class="' . $progressStyle[$tsk['progress']] . '">' 
                //.    '<td>'
                //.  $progressValue[$tsk['progress']] 
            if ( 100 == $tsk['progress']
                && ! ( $tsk['endDate'] != '0000-00-00 00:00:00'
                    && time() >= strtotime( $tsk['endDate'] ) )
               ) // if progress = 100 but not if the EndDate got marked
            {
                echo '<td class="taskEnded">';
            }
            else
            {
                echo '<td>';
            }
            
            echo ( is_null( $tsk['progress'] ) ? '-' : $tsk['progress'] . ' %' )
            .    '</td>'
            ;
            
            if ( $isAllowedToManage )
            {
                // modify
                echo '<td align="center">' 
                .    '<a href="' . $_SERVER['PHP_SELF'] 
                .    '?cmd=rqEditTask&amp;id=' . (int)$tsk['id'] . '">' 
                .    '<img src="' . get_icon( 'edit' ) . '" '
                .    'alt="Modify" border="0">' 
                .    '</a>'
                .    '</td>' 
                // delete
                .    '<td align="center">' 
                .    '<a href="' . $_SERVER['PHP_SELF'] 
                .    '?cmd=rqDeleteTask&amp;id=' . (int)$tsk['id'] . '" ' 
                .    ' onclick="return confirmTaskDelete(\'' 
                .    (int)$tsk['id'] 
                .    '\')"'
                .    '>' 
                .    '<img src="' . get_icon( 'delete' ) . '" ' 
                .    'alt="Delete" border="0">' 
                .    '</a>'                    
                .    '</td>' 
                /*
                // move
                .    '<td align="center">' 
                .    '<img src="' . get_icon( 'move' ) . '" ' 
                .    'alt="Move" border="0">' 
                .    '</td>' 
                */
                // visibility
                .    '<td align="center">' 
                .    '<a href="' . $_SERVER['PHP_SELF'] 
                .    '?cmd=' 
                .    ( $tsk['visible'] ? 'mkInvisibleFromList' : 'mkVisibleFromList' )
                .    '&amp;id=' 
                .    (int)$tsk['id'] 
                .    '">' 
                .    '<img src="' 
                .    get_icon( $tsk['visible'] ? 'visible' : 'invisible' ) 
                .    '" ' 
                .    'alt="Mask" border="0"' 
                .    '>'
                .    '</a>'
                .    '</td>'
                ;
            }
             
            echo '</tr>'
            ;
		}
		echo '</tbody></table>';
	}
    else
    {
    	$colspan = $isAllowedToManage ? 7 : 5;
        
        echo '<tr><td colspan="' . $colspan . '">'
        .    get_lang("Empty")
        .    '</td></tr>' . "\n"
        ;
        
        echo '</tbody></table>';  
    }
}


// DISPLAY THE TASK

if ( $dispTask )
{
	// Construction and display of the task menu
    if ( $isAllowedToManage )
    {
        $taskMenu = array();
        
        // modify
        $taskMenu[] = '<a href="' . $_SERVER['PHP_SELF'] 
            . '?cmd=rqEditTask&amp;id=' . (int)$task->getId() . '" ' 
            . 'class="claroCmd">' 
            . '<img src="' . get_icon( 'edit' ) . '" ' 
            . 'alt="Modify" border="0">' 
            . get_lang( 'Edit task' )
            . '</a>'
            ;
            
        // delete
        $taskMenu[] = '<a href="' . $_SERVER['PHP_SELF'] 
            . '?cmd=rqDeleteTask&amp;id=' . (int)$task->getId() . '" ' 
            . 'class="claroCmd" ' 
            . 'onclick="return confirmTaskDelete(\'' 
            . (int)$task->getId()
            . '\')"'
            . '>' 
            . '<img src="' . get_icon( 'delete' ) . '" ' 
            . 'alt="Delete" border="0">' 
            . get_lang( 'Delete task' )
            . '</a>'                    
            ;
            
        // visible        
        $taskMenu[] = '<td align="center">' 
            . '<a href="' . $_SERVER['PHP_SELF'] 
            . '?cmd=' 
            . ( $task->isVisible() ? 'mkInvisible' : 'mkVisible' )
            . '&amp;id=' 
            . (int)$task->getId() 
            . '" ' 
            . 'class="claroCmd">' 
            . '<img src="' 
            . get_icon( $task->isVisible() ? 'visible' : 'invisible' ) 
            . '" ' 
            . 'alt="Mask" border="0"' 
            . '>'
            . get_lang( $task->isVisible() ? 'Hide task' : 'Show task' )
            . '</a>'     
            ;
            
        echo '<p>'
        .    claro_html_menu_horizontal( $taskMenu )
        .    '</p>'
        ;  
    } 
    
    // Display of the task
    echo '<table' . ( $task->isVisible() ? '' : ' class="invisible"' ) . '>'
    
    // title
    .    '<tr valign="top">' 
    .    '<td><b>' . get_lang("Title") . ' : </b></td>' 
    .    '<td>' . htmlspecialchars($task->getTitle()) . '</td>' 
    .    '</tr>' 
    
    // due date
    .    '<tr valign="top">' 
    .    '<td><b>' . get_lang("Due date") . ' : </b></td>'
    ;
     
    if ( '0000-00-00 00:00:00' != $task->getDueDate() 
        && time() >= strtotime($task->getDueDate())
        && ( '0000-00-00 00:00:00' == $task->getEndDate() 
            || time() < strtotime($task->getEndDate()) )
        && 100 != $task->getProgress()
       )
    {
        echo '<td class="dueDateExceeded">';
    }
    else
    {
        echo '<td>';
    }
    
    echo ( $task->getDueDate() == '0000-00-00 00:00:00' 
             ? '-' 
             : claro_html_localised_date( get_locale( 'dateTimeFormatLong' )
                 , strtotime( $task->getDueDate() ) ) 
         ) 
    .    '</td>'
    .    '</tr>' 
    
    // start date
    .    '<tr valign="top">' 
    .    '<td><b>' . get_lang("Start date") . ' : </b></td>' 
    .    '<td>' 
    .    ( $task->getStartDate() == '0000-00-00 00:00:00' 
             ? '-' 
             : claro_html_localised_date( get_locale( 'dateTimeFormatLong' )
                 , strtotime( $task->getStartDate() ) )
         ) 
    .    '</td>'
    .    '</tr>' 
    
    // end date
    .    '<tr valign="top">' 
    .    '<td><b>' . get_lang("End Date") . ' : </b></td>' 
    ;
    
    if ( $task->getEndDate() != '0000-00-00 00:00:00'
        && time() >= strtotime($task->getEndDate()) )
    {
        echo '<td class="taskEnded">';
    }
    else
    {
        echo '<td>';
    }
    
    echo ( $task->getEndDate() == '0000-00-00 00:00:00' 
             ? '-' 
             : claro_html_localised_date( get_locale( 'dateTimeFormatLong' )
                 , strtotime( $task->getEndDate() ) ) 
         )
    .    '</td>'  
    .    '</tr>' 
    
    // priority
    .    '<tr valign="top">'
    .    '<td><b>' . get_lang("Priority") . ' : </b></td>' 
    .    '<td class="' . $priorityStyle[$task->getPriority()] . '">' 
    .    $priorityName[$task->getPriority()] 
    .    '</td>' 
    .    '</tr>'
    
    // progress
    .    '<tr valign="top">' 
    .    '<td><b>' . get_lang("Progress") . ' : </b></td>' 
    ;
    
    if ( 100 == $task->getProgress()
        && ! ( $task->getEndDate() != '0000-00-00 00:00:00'
            && time() >= strtotime( $task->getEndDate() ) )
       ) // if progress = 100 but not if the EndDate got marked
    {
        echo '<td class="taskEnded">';
    }
    else
    {
        echo '<td>';
    }
    
    echo ( is_null( $task->getProgress() ) ? '-' : $task->getProgress() . ' %' )
    .    '</td>'
    .    '</tr>' 
    
    .    '</table>'
    
    // description
    .    '<div class="taskDescription">' 
    .    '<p' . ( $task->isVisible() ? '' : ' class="invisible"' ) . '>' 
    .    '<b>' . get_lang("Description") . ' : </b>' 
    .    '</p>' 
    .    '<div class="taskDescriptionContent">' . $san->sanitize($task->getDescription()) . '</div>'
    .    '</div>' 
    ;
    
    
    
   
    /*  // DISPLAY same as list
    echo '<table class="claroTable emphaseLine" width="100%" ' 
    .    'summary="' . get_lang('Task list') . '">' 
    .    '<thead>' 
    .    '<tr class="headerX">' 
    .    '<th>' . get_lang("Priority") . '</th>' 
    .    '<th>' . get_lang("Task") . '</th>' 
    .    '<th>' . get_lang("Due date") . '</th>' 
    .    '<th>' . get_lang("Start date") . '</th>' 
    .    '<th>' . get_lang("End date") . '</th>' 
    .    '<th>' . get_lang("Progress") . '</th>'
    ;
                    
    if ( $isAllowedToManage )
    {
        echo '<th>' . get_lang("Modify") . '</th>' 
        .    '<th>' . get_lang("Delete") . '</th>'
        ////.'<th>' . get_lang( "Move" ) . '</th>'
        .    '<th>' . get_lang( "Visibility" ) . '</th>'
        ;
    }
                    
    echo '</tr>' 
    .    '</thead>' 
            
    .    '<tbody>' 
    .    '<tr' . ( $task->isVisible() ? '' : ' class="invisible"' ) . '>' 
    
    // priority
    .    '<td class="' . $priorityStyle[$task->getPriority()] . '">' 
    .    $priorityName[$task->getPriority()] 
    .    '</td>' 
    
    // title
    .    '<td>' 
    .    htmlspecialchars($task->getTitle())
    .    '</td>' 
    ;
    
    // due date 
    if ( $task->getDueDate() != '0000-00-00 00:00:00'
        && time() >= strtotime($task->getDueDate())
        && ( $task->getEndDate() == '0000-00-00 00:00:00' 
            || time() < strtotime($task->getEndDate()) )
       )
    {
        echo '<td class="dueDateExceeded">';
    }
    else
    {
    	echo '<td>';
    }
    
    echo ( $task->getDueDate() == '0000-00-00 00:00:00' 
             ? '-' 
             : claro_html_localised_date( get_locale( 'dateTimeFormatShort' )
                 , strtotime( $task->getDueDate() ) ) 
         ) 
    .    '</td>'
    
    // start date
    .    '<td>' 
    .    ( $task->getStartDate() == '0000-00-00 00:00:00' 
             ? '-' 
             : claro_html_localised_date( get_locale( 'dateTimeFormatShort' )
                 , strtotime( $task->getStartDate() ) )
         ) 
    .    '</td>'
    ;
    
    // end date 
    if ( $task->getEndDate() != '0000-00-00 00:00:00'
        && time() >= strtotime($task->getEndDate()) )
    {
        echo '<td class="taskEnded">';
    }
    else
    {
        echo '<td>';
    }
    
    echo ( $task->getEndDate() == '0000-00-00 00:00:00' 
             ? '-' 
             : claro_html_localised_date( get_locale( 'dateTimeFormatShort' )
                 , strtotime( $task->getEndDate() ) ) 
         )
    .    '</td>'  
    
    // progress
    .    '<td>'
    .    ( is_null( $task->getProgress() ) ? '-' : $task->getProgress() . ' %' )
    .    '</td>'
    ;
    
    if ( $isAllowedToManage )
    {
        // modify
        echo '<td align="center">' 
        .    '<a href="' . $_SERVER['PHP_SELF'] 
        .    '?cmd=rqEditTask&amp;id=' . (int)$task->getId() . '">' 
        .    '<img src="' . get_icon( 'edit' ) . '" ' 
        .    'alt="Modify" border="0">' 
        .    '</a>'
        .    '</td>' 
        // delete
        .    '<td align="center">' 
        .    '<a href="' . $_SERVER['PHP_SELF'] 
        .    '?cmd=rqDeleteTask&amp;id=' . (int)$task->getId() . '" ' 
        .    ' onclick="return confirmTaskDelete(\'' 
        .    (int)$task->getId()
        .    '\')"'
        .    '>' 
        .    '<img src="' . get_icon( 'delete' ) . '" ' 
        .    'alt="Delete" border="0">' 
        .    '</a>'                    
        .    '</td>' 
        // visible        
        .    '<td align="center">' 
        .    '<a href="' . $_SERVER['PHP_SELF'] 
        .    '?cmd=' 
        .    ( $task->isVisible() ? 'mkInvisible' : 'mkVisible' )
        .    '&amp;id=' 
        .    (int)$task->getId() 
        .    '">' 
        .    '<img src="' 
        .    get_icon( $task->isVisible() ? 'visible' : 'invisible' ) 
        .    '" ' 
        .    'alt="Mask" border="0"' 
        .    '>'
        .    '</a>'
        .    '</td>'      
        ;
    }
    
    echo '</tr>'
    .    '</tbody>'
    
    .    '</table>'
    ;          
	
    // Description
    echo '<br>' 
    .    '<table class="claroTable emphaseLine" width="100%" ' 
    .    'summary="' . get_lang('Task description') . '">' 
    .    '<thead>' 
    .    '<tr class="headerX">' 
    .    '<th>' . get_lang("Task description") . '</th>'
    .    '</tr>'
    .    '</thead>'
    .    '<tbody>'
    .    '<tr>'
    .    '<td>' . $san->sanitize($task->getDescription()) . '</td>'
    .    '</tr>'
    .    '</tbody>'
    .    '</table>'
    ;    
    */
    
    
    
    /*  // DISPLAY l'un en dessous de l'autre ( gérer par css )
    echo '<div class="task' . ( $task->isVisible() ? '' : ' invisible' ) . '">'
    
    // title
    .    '<h3>' . get_lang("Task") . '</h3>'
    .    '<div class="item">' 
    .    htmlspecialchars($task->getTitle())
    .    '</div>' 
    
    // due date 
    .    '<h3>' . get_lang("Due date") . '</h3>' 
    ;
    
    if ( $task->getDueDate() != '0000-00-00 00:00:00'
        && time() >= strtotime($task->getDueDate())
        && ( $task->getEndDate() == '0000-00-00 00:00:00' 
            || time() < strtotime($task->getEndDate()) )
       )
    {
        echo '<div class="dueDateExceeded item">';
    }
    else
    {
        echo '<div class="item">';
    }
    
    echo ( $task->getDueDate() == '0000-00-00 00:00:00' 
             ? '-' 
             : claro_html_localised_date( get_locale( 'dateTimeFormatShort' )
                 , strtotime( $task->getDueDate() ) ) 
         ) 
    .    '</div>'
    
    // start date
    .    '<h3>' . get_lang("Start date") . '</h3>' 
    .    '<div class="item">' 
    .    ( $task->getStartDate() == '0000-00-00 00:00:00' 
             ? '-' 
             : claro_html_localised_date( get_locale( 'dateTimeFormatShort' )
                 , strtotime( $task->getStartDate() ) )
         ) 
    .    '</div>'    
    
    // end date 
    .    '<h3>' . get_lang("End date") . '</h3>' 
    ;
    
    if ( $task->getEndDate() != '0000-00-00 00:00:00'
        && time() >= strtotime($task->getEndDate()) )
    {
        echo '<div class="taskEnded item">';
    }
    else
    {
        echo '<div  class="item">';
    }
    
    echo ( $task->getEndDate() == '0000-00-00 00:00:00' 
             ? '-' 
             : claro_html_localised_date( get_locale( 'dateTimeFormatShort' )
                 , strtotime( $task->getEndDate() ) ) 
         )
    .    '</div>'  
    
    // priority
    .    '<h3>' . get_lang("Priority") . '</h3>'
    .    '<div class="' . $priorityStyle[$task->getPriority()] . ' item">' 
    .    $priorityName[$task->getPriority()] 
    .    '</div>' 
    
    // progress
    .    '<h3>' . get_lang("Progress") . '</h3>'
    .    '<div class="item">'
    .    ( is_null( $task->getProgress() ) ? '-' : $task->getProgress() . ' %' )
    .    '</div>'
    
    // description
    .    '<h3>' . get_lang("Task description") . '</h3>'
    .    '<div>' 
    .    $san->sanitize($task->getDescription()) 
    .    '</div>'
    
    .    '</div>'
    ;
    */
       
}


// DISPLAY TASK FORM

if ( $dispForm )
{
    echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">'
    .    claro_form_relay_context()
    .    '<input type="hidden" name="cmd" value="' . $nextCmd . '" />'
    .    '<input type="hidden" name="id" value="' . (int)$task->getId() . '"/>'
    .    '<table>'
    
    // title
    .    '<tr valign="top">' 
    .    '<td><b>' . get_lang("Title") . ' : </b></td>' 
    .    '<td><input type="text" name="title" value="' . htmlspecialchars($task->getTitle()) . '" /></td>' 
    .    '</tr>' 
    
    // due date    
    .    '<tr valign="top">'
    .    '<td><b>' . get_lang("Due date") . ' : </b></td>' 
    .    '<td>' 
    .    '<input type="checkbox" name="dueDateIsActive" value="1"'
    .    ( $task->getDueDate() != '0000-00-00 00:00:00' ? ' checked="checked"' : '' ) . ' />' 
    .    get_lang('Use due date') 
    .    '<br />'
    .    claro_html_date_form( 'dueDateDay', 'dueDateMonth', 'dueDateYear'
             , $task->getDueDate() != '0000-00-00 00:00:00' ? strtotime( $task->getDueDate() ) : time(), 'long' ) . ' '
    .    claro_html_time_form( 'dueDateHour','dueDateMinute'
             , $task->getDueDate() != '0000-00-00 00:00:00' ? strtotime( $task->getDueDate() ) : time() ) 
    .    '&nbsp;'
    .    '<small>' . get_lang( '(d/m/y hh:mm)' ) . '</small>'
    .    '</td>' 
    .    '</tr>'        
    
    // start date
    .    '<tr valign="top">'
    .    '<td><b>' . get_lang("Start date") . ' : </b></td>' 
    .    '<td>'
    .    '<input type="checkbox" name="startDateIsActive" value="1"'
    .    ( $task->getStartDate() != '0000-00-00 00:00:00' ? ' checked="checked"' : '' ) . ' />' 
    .    get_lang('Use start date') 
    .    '<br />'
    .    claro_html_date_form( 'startDateDay', 'startDateMonth', 'startDateYear'
             , $task->getStartDate() != '0000-00-00 00:00:00' ? strtotime( $task->getStartDate() ) : time(), 'long' ) . ' '
    .    claro_html_time_form( 'startDateHour','startDateMinute'
             , $task->getStartDate() != '0000-00-00 00:00:00' ? strtotime( $task->getStartDate() ) : time() ) 
    .    '&nbsp;'
    .    '<small>' . get_lang( '(d/m/y hh:mm)' ) . '</small>'
    .    '</td>' 
    .    '</tr>'
    ;
    
    if ( 'rqEditTask' == $cmd )
    {
        // end date
        echo '<tr valign="top">'
        .    '<td><b>' . get_lang("End date") . ' : </b></td>' 
        .    '<td>'
        .    '<input type="checkbox" name="endDateIsActive" value="1"'
        .    ( $task->getEndDate() != '0000-00-00 00:00:00' ? ' checked="checked"' : '' ) . ' />'  
        .    get_lang('Use end date') 
        .    '<br />'
        .    claro_html_date_form( 'endDateDay', 'endDateMonth', 'endDateYear'
                 , $task->getEndDate() != '0000-00-00 00:00:00' ? strtotime( $task->getEndDate() ) : time(), 'long' ) . ' '
        .    claro_html_time_form( 'endDateHour','endDateMinute'
                 , $task->getStartDate() != '0000-00-00 00:00:00' ? strtotime( $task->getEndDate() ) : time() ) 
        .    '&nbsp;'
        .    '<small>' . get_lang( '(d/m/y hh:mm)' ) . '</small>'
        .    '</td>' 
        .    '</tr>'
        ;
    }
    
    // priority    
    echo '<tr valign="top">' 
    .    '<td><b>' . get_lang("Priority") . ' : </b></td>' 
    .    '<td>' 
    .    '<select name="priority">'
    ;
    
    foreach ( $priorityName as $index => $name )
    {
        echo '<option value="' . $index . '"' 
        .    ( $task->getPriority() == $index ? ' selected="selected"' : '' ) 
        .    '>' 
        .    $name 
        .    '</option>'
        ;
    }
        
    echo '</select>' 
    .    '</td>' 
    .    '</tr>' 
    ;
    
    // progress    
    echo '<tr valign="top">' 
    .    '<td><b>' . get_lang("Progress") . ' : </b></td>' 
    .    '<td>' 
    .    '<select name="progress">'
    .    '<option value="-"' 
    .    ( is_null( $task->getProgress() ) ? ' selected="selected"' : '' )
    .    '>'
    .    '-'
    .    '</option>'
    ;
        
    for ( $percent = 0 ; $percent <= 100 ; $percent += 5 )
    {
        echo '<option value="' . $percent . '"' 
        .    ( $task->getProgress() === $percent ? ' selected="selected"' : '' ) 
        .    '>' 
        .    $percent
        .    ' %' 
        .    '</option>'
        ;
    }
        
    echo '</select>' 
    .    '</td>' 
    .    '</tr>' 
    
    // description
    .    '<tr valign="top">' 
    .    '<td><b>Description : </b></td>' 
    .    '<td>'
    .    claro_html_textarea_editor( 'description'
             , !empty( $task ) ? $san->sanitize($task->getDescription()) : ''
             , 12, 67, $optAttrib=' wrap="virtual"'
         )
    .    '</td>'
    .    '</tr>'
        
    .    '<tr valign="top">' 
    .    '<td>&nbsp;</td>' 
    .    '<td>'
    .    '<input class="claroButton" type="submit" name="saveTask" value="' . get_lang( 'Ok' ) . '" />' . "&nbsp;"
    .    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=' . $cancelCmd . '" style="text-decoration: none;">' 
    .    '<input class="claroButton" type="button" name="cancel" value="' . get_lang( 'Cancel' ) . '" />'
    .    '</a>'
    .    '</td>'
    .    '</tr>'
    .    '</table>'
    
    .    '</form>'
    ; 
}


// DISPLAY CONFIRMATION FOR TASK DELETION

if ( $dispConfirm )
{
    $message = get_lang( 'Are you sure you want to delete the task "%title%"'
        , array( '%title%' => $san->sanitize($task->getTitle()) ) )
        ;	
    
    echo '<p>'  
    .    '<font color="#cc0000">' 
    .    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exDeleteTask">Oui</a>' 
    .    '&nbsp;|&nbsp;' 
    .    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=showTask&amp;id=' . (int)$task->getId() . '">Non</a>' 
    .    '</font>' 
    .    '</p>';
} 



require_once get_path('includePath') . '/claro_init_footer.inc.php';
?>