<?php
/*
Module COMPILATIO v1.6 testé sur Claroline 1.8.11 et 1.9rc5
Compilatio - www.compilatio.net
*/
//////////////////////////////////////////////////////////////////////////
//                          Identifier                                  //
//////////////////////////////////////////////////////////////////////////
$tlabelReq = 'COMPILAT';
//////////////////////////////////////////////////////////////////////////
//                          Includes                                    //
//////////////////////////////////////////////////////////////////////////
require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';
add_module_lang_array($tlabelReq);
if ( ! get_init('in_course_context') || ! get_init('is_courseAllowed') || !get_init('is_authenticated') ) claro_disp_auth_form(true);
//Classe compilatio
include("lib/compilatio.class.php");

//Gestion de l'authentifiaction CAS
include("lib/cas/check_cas.php");

//Parametrage pour l'utilisation de soap
ini_set('soap.wsdl_cache_enabled', 0); 
ini_set('default_socket_timeout', '100');
// initialise view mode tool
claro_set_display_mode_available(true);

if ( ! claro_is_in_a_course() || ! claro_is_course_allowed() ) claro_disp_auth_form(true);

//RECUP module work
require_once './lib/assignment.class.php';

//require_once get_path('incRepositorySys') . '/lib/assignment.lib.php';
require_once get_path('incRepositorySys') . '/lib/pager.lib.php';
require_once get_path('incRepositorySys') . '/lib/fileUpload.lib.php';
require_once get_path('incRepositorySys') . '/lib/fileDisplay.lib.php'; // need format_url function
require_once get_path('incRepositorySys') . '/lib/fileManage.lib.php'; // need claro_delete_file


$tbl_cdb_names = claro_sql_get_course_tbl();
$tbl_wrk_assignment = $tbl_cdb_names['wrk_assignment'];
$tbl_wrk_submission = $tbl_cdb_names['wrk_submission'];

$currentCoursePath =  claro_get_current_course_data('path');
//event_access_tool(claro_get_current_tool_id(), claro_get_current_course_tool_data('label'));

// 'step' of pager
$assignmentsPerPage = 20;
//////////////////////////////////////////////////////////////////////////
//                          Business Logic                              //
//////////////////////////////////////////////////////////////////////////
//$compilatio = new compilatio();
//$result=$compilatio->SendDoc($_SESSION['_cid'],1,utf8_encode("Le fichier"),"claro.pdf");
//RECUP WORK
$currentCourseRepositorySys = get_path('coursesRepositorySys') . $currentCoursePath . '/';
$currentCourseRepositoryWeb = get_path('coursesRepositoryWeb') . $currentCoursePath . '/';

$fileAllowedSize = get_conf('max_file_size_per_works') ;    //file size in bytes

// use with strip_tags function when strip_tags is used to check if a text is empty
// but a 'text' with only an image don't have to be considered as empty
$allowedTags = '<img>';

// initialise dialog box to an empty string, all dialog will be concat to it
$dialogBox = '';

// permission
$is_allowedToEdit = claro_is_allowed_to_edit();
//////////////////////////////////////////////////////////////////////////
//                          Display                                     //
//////////////////////////////////////////////////////////////////////////

/*--------------------------------------------------------------------
                            HEADER
  --------------------------------------------------------------------*/
$noQUERY_STRING = true;
$nameTools = get_lang('Compilatio')." - ".get_lang('Anti Plagiarism Tool');
/*--------------------------------------------------------------------
                              LIST
--------------------------------------------------------------------*/

// if user come from a group
if ( claro_is_in_a_group() && claro_is_group_allowed() )
{
    // select only the group assignments
    $sql = "SELECT `id`,
                    `title`,
                    `def_submission_visibility`,
                      `visibility`,
                    `assignment_type`,
                    `authorized_content`,
                    unix_timestamp(`start_date`) as `start_date_unix`,
                    unix_timestamp(`end_date`) as `end_date_unix`
            FROM `" . $tbl_wrk_assignment . "`
            WHERE `assignment_type` = 'GROUP'";
    
    if( isset($_GET['sort']) )
    {
        $sortKeyList[$_GET['sort']] = isset($_GET['dir']) ? $_GET['dir'] : SORT_ASC;
    }
    
    $sortKeyList['end_date']    = SORT_ASC;
}
else
{
    $sql = "SELECT `id`,
                    `title`,
                    `def_submission_visibility`,
                    `visibility`,
                    `assignment_type`,
                    unix_timestamp(`start_date`) as `start_date_unix`,
                    unix_timestamp(`end_date`) as `end_date_unix`
            FROM `" . $tbl_wrk_assignment . "`";
    
    if ( isset($_GET['sort']) )
    {
        $sortKeyList[$_GET['sort']] = isset($_GET['dir']) ? $_GET['dir'] : SORT_ASC;
    }
    
    $sortKeyList['end_date']    = SORT_ASC;
}

$offset = (isset($_REQUEST['offset']) && !empty($_REQUEST['offset']) ) ? $_REQUEST['offset'] : 0;
$assignmentPager = new claro_sql_pager($sql, $offset, $assignmentsPerPage);

foreach($sortKeyList as $thisSortKey => $thisSortDir)
{
    $assignmentPager->add_sort_key( $thisSortKey, $thisSortDir);
}

$assignmentList = $assignmentPager->get_result_list();

/*--------------------------------------------------------------------
                    TOOL TITLE
    --------------------------------------------------------------------*/

$out = claro_html_tool_title($nameTools);

if ($is_allowedToEdit)
{
    /*--------------------------------------------------------------------
                            DIALOG BOX SECTION
      --------------------------------------------------------------------*/
    
    if ( isset($dialogBox) && !empty($dialogBox) )
    {
        $out .= claro_html_message_box($dialogBox);
    }
}

/*--------------------------------------------------------------------
                            ASSIGNMENT LIST
    --------------------------------------------------------------------*/

// if we don't display assignment form
if ( (!isset($displayAssigForm) || !$displayAssigForm) )
{
    /*--------------------------------------------------------------------
                        ADMIN LINKS
      --------------------------------------------------------------------*/
    $cmdMenu = array();

    if( !empty($cmdMenu) ) $out .= '<p>' . claro_html_menu_horizontal($cmdMenu) . '</p>' . "\n";

    $headerUrl = $assignmentPager->get_sort_url_list($_SERVER['PHP_SELF']);

    $out .= $assignmentPager->disp_pager_tool_bar($_SERVER['PHP_SELF']);

    $out .= '<table class="claroTable" width="100%">' . "\n"
    .     '<tr class="headerX">'
    .     '<th><a href="' . $headerUrl['title'] . '">' . get_lang('Title') . '</a></th>' . "\n"
    .     '<th><a href="' . $headerUrl['assignment_type'] . '">' . get_lang('Type') . '</a></th>' . "\n"
    .     '<th><a href="' . $headerUrl['start_date_unix'] . '">' . get_lang('Start date') . '</a></th>' . "\n"
    .     '<th><a href="' . $headerUrl['end_date_unix'] . '">' . get_lang('End date') . '</a></th>' . "\n";

    $colspan = 4;

    $out .= '</tr>' . "\n"
    .     '<tbody>' . "\n\n";

    $atLeastOneAssignmentToShow = false;

    if (claro_is_user_authenticated())
    {
        $date = $claro_notifier->get_notification_date(claro_get_current_user_id());
    }
    
    foreach ( $assignmentList as $anAssignment )
    {
        //modify style if the file is recently added since last login and that assignment tool is used with visible default mode for submissions.
        $classItem='';
        if( claro_is_user_authenticated() )
        {
            if ( $claro_notifier->is_a_notified_ressource(claro_get_current_course_id(), $date, claro_get_current_user_id(), '',  claro_get_current_tool_id(), $anAssignment['id'],FALSE) && ($anAssignment['def_submission_visibility']=="VISIBLE"  || $is_allowedToEdit))
            {
                $classItem=' hot';
            }
            else //otherwise just display its name normally and tell notifier that every ressources are seen (for tool list notification consistancy)
            {
                $claro_notifier->is_a_notified_ressource(claro_get_current_course_id(), $date, claro_get_current_user_id(), '', claro_get_current_tool_id(), $anAssignment['id']);
            }
        }
        
        if ( $anAssignment['visibility'] == "INVISIBLE" )
        {
            if ( $is_allowedToEdit )
            {
                $style=' class="invisible"';
            }
            else
            {
                continue; // skip the display of this file
            }
        }
        else
        {
            $style='';
        }
        
        $out .= '<tr ' . $style . '>'."\n"
        .    '<td>' . "\n"
        .    '<a href="compilist.php?assigId=' . $anAssignment['id'] . '" class="item' . $classItem . '">'
        .    '<img src="' . get_icon_url( 'assignment' ) . '" alt="" /> '
        .    $anAssignment['title']
        .    '</a>' . "\n"
        .    '</td>' . "\n"
        ;
    
        $out .= '<td align="center">';
    
        if( $anAssignment['assignment_type'] == 'INDIVIDUAL' )
        {
            $out .= '<img src="' . get_icon_url( 'user' ) . '" border="0" alt="' . get_lang('Individual') . '" />' ;
        }
        elseif( $anAssignment['assignment_type'] == 'GROUP' )
        {
            $out .= '<img src="' . get_icon_url( 'group' ) . '" border="0" alt="' . get_lang('Groups (from groups tool, only group members can post)') . '" />' ;
        }
        else
        {
            $out .= '&nbsp;';
        }
    
        $out .= '</td>' . "\n"
        .    '<td><small>' . claro_html_localised_date(get_locale('dateTimeFormatLong'),$anAssignment['start_date_unix']) . '</small></td>' . "\n"
        .    '<td><small>' . claro_html_localised_date(get_locale('dateTimeFormatLong'),$anAssignment['end_date_unix']) . '</small></td>' . "\n";
        
        if ( isset($_REQUEST['submitGroupWorkUrl']) && !empty($_REQUEST['submitGroupWorkUrl']) )
        {
            if( !isset($anAssignment['authorized_content']) || $anAssignment['authorized_content'] != 'TEXT' )
            {
                $out .= '<td align="center">'
                .     '<a href="compilist.php?cmd=rqSubWrk&amp;assigId=' . $anAssignment['id'] . '&amp;submitGroupWorkUrl=' . urlencode($_REQUEST['submitGroupWorkUrl']) . '">'
                .      '<small>' . get_lang('Publish') . '</small>'
                .     '</a>'
                .     '</td>' . "\n";
            }
            else
            {
                $out .= '<td align="center">'
                .      '<small>-</small>'
                .     '</td>' . "\n";
            }
        }
        
        $atLeastOneAssignmentToShow = true;
    }
    
    if ( ! $atLeastOneAssignmentToShow )
    {
        $out .= '<tr>' . "\n"
        .    '<td colspan=' . $colspan . '>' . "\n"
        .    get_lang('There is no assignment at the moment')
        .    '</td>' . "\n"
        .    '</tr>' . "\n";
    }
    
    $out .= '</tbody>' . "\n"
    .     '</table>' . "\n\n";
}

Claroline::getInstance()->display->body->appendContent( $out );
echo Claroline::getInstance()->display->render();