<?php
/**
 * CLAROLINE
 *
 * Course home page agregator for Claroline
 *
 * @author 
 *
 * @package 
 *
 */

$tlabelReq = 'CLCOURSE';

// Name of the tool (displayed in title)
$nameTools = 'Course Homepage';

// load Claroline kernel
require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

// load libraries
require_once dirname(__FILE__) . '/lib/portlet.lib.php';

// load the manageCourse's functions
require_once('lib/manageCourse.php');

// specify the table to use
$tblPrefix = claro_get_course_db_name_glued(null);
$thisTable = $tblPrefix.'manage_course_homepage';

// set newRank
if (isset($_GET['action']))
	setNewSpec($thisTable,$_GET['id'],$_GET['action']);

//get current rank
$itemsRank = getItemsSpec($thisTable);

/*
 * Init other vars
 */
claro_set_display_mode_available(true);

// check if the current user is allow to edit
$is_allowedToEdit = claro_is_allowed_to_edit();

$dialogBox = new DialogBox();

// add to bread crumbs
claroBreadCrumbs::getInstance()->setCurrent( get_lang( 'Home' ) );

// Command line
$cmdMenu = array();
if($is_allowedToEdit)
{
    $cmdMenu[] = ''; //claro_html_cmd_link('edit.php?cmd=rqEdit'. claro_url_relay_context('&amp;'),get_lang('Edit home page'));
}

$output ='';

$output .= '<p>'.claro_html_menu_horizontal( $cmdMenu ).'</p>';

// links to the connectors to manage the course homepage
require_once './connector/course_description/CLCHP.cnr.php';
require_once './connector/announcements/CLCHP.cnr.php';
require_once './connector/calendar/CLCHP.cnr.php';

// call the display functions to manage the rank and visibility for each "widget"
if ($is_allowedToEdit || (!$is_allowedToEdit && $itemsRank[1] == 'VISIBLE'))
	$output .= displayItem($itemsRank[0],1,$itemsRank[1],$is_allowedToEdit);
if ($is_allowedToEdit || (!$is_allowedToEdit && $itemsRank[3] == 'VISIBLE'))
	$output .= displayItem($itemsRank[2],2,$itemsRank[3],$is_allowedToEdit);
if ($is_allowedToEdit || (!$is_allowedToEdit && $itemsRank[5] == 'VISIBLE'))
	$output .= displayItem($itemsRank[4],3,$itemsRank[5],$is_allowedToEdit);

/////////////////////////////////////////////////////////
///// content of "claroline/course/index.php" file /////
/*
$gidReset = TRUE;
$tidReset = TRUE;
*/
if ( isset($_REQUEST['cid']) ) $cidReq = $_REQUEST['cid'];

require_once get_path('incRepositorySys') . '/lib/course_home.lib.php';
include claro_get_conf_repository() . 'rss.conf.php';

if ( !claro_is_in_a_course()  || !claro_is_course_allowed() ) claro_disp_auth_form(true);

$toolRepository = get_path('clarolineRepositoryWeb');
claro_set_display_mode_available(TRUE);


// Language initialisation of the tool names
$toolNameList = claro_get_tool_name_list();

// get tool id where new events have been recorded since last login

if (claro_is_user_authenticated())
{
    $date = $claro_notifier->get_notification_date(claro_get_current_user_id());
    $modified_tools = $claro_notifier->get_notified_tools(claro_get_current_course_id(), $date, claro_get_current_user_id());
}
else
{
    $modified_tools = array();
}


// TOOL LIST


$is_allowedToEdit = claro_is_allowed_to_edit();

$toolList = claro_get_course_tool_list(claro_get_current_course_id(),$_profileId,true);
$toolLinkList = array();

foreach ($toolList as $thisTool)
{
    // special case when display mode is student and tool invisible doesn't display it
    if ( ( claro_get_tool_view_mode() == 'STUDENT' ) && ! $thisTool['visibility']  )
    {
        continue;
    }

    if (isset($thisTool['label'])) // standart claroline tool or module of type tool
    {
        $thisToolName = $thisTool['name'];
        $toolName = get_lang($thisToolName);

        //trick to find how to build URL, must be IMPROVED

        $url = htmlspecialchars( Url::Contextualize( get_module_url($thisTool['label']) . '/' . $thisTool['url'] ) );
        $icon = get_module_url($thisTool['label']) .'/'. $thisTool['icon'];
        $htmlId = 'id="' . $thisTool['label'] . '"';
        $removableTool = false;
    }
    else   // external tool added by course manager
    {
        if ( ! empty($thisTool['external_name'])) $toolName = $thisTool['external_name'];
        else $toolName = '<i>no name</i>';
        $url = htmlspecialchars( trim($thisTool['url']) );
        $icon = get_icon_url('link');
        $htmlId = '';
        $removableTool = true;
    }

    $style = !$thisTool['visibility']? 'invisible ' : '';
    $classItem = (in_array($thisTool['id'], $modified_tools)) ? ' hot' : '';

    //deal with specific case of group tool

    // TODO : get_notified_groups can know itself if $_uid is set
    if ( claro_is_user_authenticated() && ('CLGRP' == $thisTool['label']))
    {
        // we must notify if there is at least one group containing notification
        $groups = $claro_notifier->get_notified_groups(claro_get_current_course_id(), $date);
        $classItem = ( ! empty($groups) ) ? ' hot ' : '';
    }
    
    if ( ! empty($url) )
    {
        $toolLinkList[] = '<a '.$htmlId.'class="' . $style . 'item' . $classItem . '" href="' . $url . '">'
        .                 '<img class="clItemTool"  src="' . $icon . '" alt="" />&nbsp;'
        .                 $toolName
        .                 '</a>' . "\n"
        ;
    }
    else
    {
        $toolLinkList[] = '<span ' . $style . '>'
        .                 '<img class="clItemTool" src="' . $icon . '" alt="" />&nbsp;'
        .                 $toolName
        .                 '</span>' . "\n"
        ;
    }
}

    $courseManageToolLinkList[] = '<a class="claroCmd" href="' . htmlspecialchars(Url::Contextualize( get_path('clarolineRepositoryWeb')  . 'course/tools.php' )) . '">'
    .                             '<img src="' . get_icon_url('edit') . '" alt="" /> '
    .                             get_lang('Edit Tool list')
    .                             '</a>'
    ;
    $courseManageToolLinkList[] = '<a class="claroCmd" href="' . htmlspecialchars(Url::Contextualize( $toolRepository . 'course/settings.php' )) . '">'
    .                             '<img src="' . get_icon_url('settings') . '" alt="" /> '
    .                             get_lang('Course settings')
    .                             '</a>'
    ;

    if( get_conf('is_trackingEnabled') )
    {
        $courseManageToolLinkList[] =  '<a class="claroCmd" href="' . htmlspecialchars(Url::Contextualize( $toolRepository . 'tracking/courseReport.php' )) . '">'
        .                             '<img src="' . get_icon_url('statistics') . '" alt="" /> '
        .                             get_lang('Statistics')
        .                             '</a>'
        ;
    }

// Display header

$template = new CoreTemplate('course_index.tpl.php');
$template->assign('toolLinkList', $toolLinkList);
$template->assign('courseManageToolLinkList', $courseManageToolLinkList);

$claroline->display->body->setContent($template->render());

// end of content of "claroline/course/index.php" file //
/////////////////////////////////////////////////////////

//echo $output;
$claroline->display->body->appendContent($output);

// generate output
echo $claroline->display->render();
?>
