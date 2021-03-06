<?php // $Id$
/**
 * CLSURVEY
 *
 * @version 1.0.0
 *
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLSURVEY
 *
 * @author Christophe Gesché <moosh@claroline.net>
 * @author Philippe Dekimpe <dkp@ecam.be>
 * @author Claro Team <cvs@claroline.net>
 *
 */

$tlabelReq = 'CLSURVEY';
$msgList   = array();
$cmdMenu   = array();
$gidReset  = true;

/**
 *  CLAROLINE MAIN SETTINGS
 */

require dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';
FromKernel::uses( 'utils/input.lib' );

add_module_lang_array($tlabelReq);

if ( ! get_init('in_course_context') || ! get_init('is_courseAllowed') || !get_init('is_authenticated') ) claro_disp_auth_form(true);

claro_set_display_mode_available(TRUE);

// local librairies
From::Module( 'CLSURVEY' )->uses( 'survey.lib' );

//set flag following init settings
$is_allowedToEdit = claro_is_allowed_to_edit();

/**
 * DB tables definition
 */
$tbl = get_module_main_tbl(array('survey_question', 'survey_question_list', 'survey_answer', 'survey_list', 'survey_user'));

// DEFAULT DISPLAY
$displayList = FALSE;

/**
 *                    COMMANDS SECTION (COURSE MANAGER ONLY)
 */

$userInput = Claro_UserInput::getInstance();

$idSurvey   = $userInput->get( 'surveyId' ) ? (int) $userInput->get( 'surveyId' )   : 0;
$cmd        = $userInput->get( 'cmd' )      ? $userInput->get( 'cmd' )              : '';

if (($is_allowedToEdit) and ( !empty($cmd) )) // check teacher status
{
    /**
     * DELETE survey COMMAND
     */
    if ( 'exDelete' == $cmd )
    {
        $return = delete_survey($idSurvey);

        if ($return)
        {
            $msgList['info'][] = get_lang('Survey has been deleted')
            .                    '<br />'
            .                    '<a href="./survey_list.php">' . get_lang('Continue') . '</a>'
            .                    '<br />' . "\n"
            ;
            $eventNotifier->notifyCourseEvent('survey_deleted', get_init('_cid'), get_init('_tid'), $idSurvey, get_init('_gid'), '0');
        }
    }

    /**
     * EDIT VISIBILITY
     */

    if ( 'mkShow' == $cmd || 'mkHide' == $cmd )
    {
        if ('mkShow' == $cmd )
        {
            // TODO : use a function
            $sql = "UPDATE `" . $tbl['survey_list'] . "`
                    SET visibility = 'SHOW'
                    WHERE id_survey= " . (int) $idSurvey;
            $return = claro_sql_query($sql);
            $eventNotifier->notifyCourseEvent('survey_visible', get_init('_cid'), get_init('_tid'), $idSurvey, get_init('_gid'), '0');
        }
        if ('mkHide' == $cmd )
        {
            // TODO : use a function
            $sql = "UPDATE `" . $tbl['survey_list'] . "`
                    SET visibility = 'HIDE'
                    WHERE id_survey = " . (int) $idSurvey;
            $return = claro_sql_query($sql);
            $eventNotifier->notifyCourseEvent('survey_invisible',  get_init('_cid'), get_init('_tid'), $idSurvey, get_init('_gid'), '0');
        }
        $displayList = TRUE;
    }

    /**
     * MOVE UP AND MOVE DOWN COMMANDS
     */
    if ($cmd == 'exMoveDown' OR $cmd =='exMoveUp')
    {
        if ( 'exMoveDown' == $cmd  )
        {
            $return = move_survey($idSurvey,'DOWN','id_survey',get_init('_cid'));
        }
        if ( 'exMoveUp' == $cmd )
        {
            $return = move_survey($idSurvey,'UP','id_survey',get_init('_cid'));
        }
        if ($return)
        {
            $msgList['info'][] = get_lang('Position modified');
        }
        $displayList = TRUE;
    }
    
    /**
     * Export a surver into cvs format
    */
    if ($cmd == 'exExport' )
    {
        exportSurvey( $idSurvey );
    }
}
else
{
    $displayList = TRUE;
} // end if is_allowedToEdit



/////////////////////////////////////////////////////////////////////////////////////
// PREPARE DISPLAYS

$surveyList = get_survey_list() ;

$surveyGrid = array();
$surveyVotedGrid = array();
// find the recent documents with the notification system
$date = $claro_notifier->get_notification_date(claro_get_current_user_id());
$iterator = 0;
foreach ( $surveyList as $thisSurvey)
{
    // test if visible
    if (($is_allowedToEdit) OR ($thisSurvey['visibility'] == 'SHOW' ))
    {
        $iterator ++;
        $surveyCompleted = is_survey_completed_by_user($thisSurvey['id_survey']);

        //modify style if the file is recently added since last login
        if ($claro_notifier->is_a_notified_ressource(claro_get_current_course_id(), $date, claro_get_current_user_id(), claro_get_current_group_id(), claro_get_current_tool_id(), $thisSurvey['id_survey']))
        {
            $classItem=' hot';
        }
        else // otherwise just display its name normally
        {
            $classItem='';
        }
        $style = ($thisSurvey['visibility'] != 'SHOW') ? 'invisible' :'';
        $title = $thisSurvey['title'];

        $thisRow['title'] = '<a href="survey.php?surveyId=' . $thisSurvey['id_survey'] . '"  class="item'.$classItem . ' ' . $style . '" >'
        .                   claro_htmlspecialchars($title) . "\n"
        .    '</a>'
        ;



        if ($is_allowedToEdit)
        {
            $thisRow['export'] =
                claro_html_cmd_link( claro_htmlspecialchars('survey_list.php?cmd=exExport&surveyId=') . $thisSurvey['id_survey']
                                   , '<img src="' . get_icon_url( 'export_list' ) . '" '
                                   . 'alt="' . get_lang('Export') . '" />' );
                
            $thisRow['stats'] =
                claro_html_cmd_link( 'survey_result.php?surveyId=' . $thisSurvey['id_survey']
                                   , '<img src="' . get_icon_url( 'statistics' ) . '" '
                                   . 'alt="' . get_lang('Results') . '" />' );

            // EDIT Request LINK
            $thisRow['edit'] =
                claro_html_cmd_link( 'survey.php?switchMode=rqEdit&amp;surveyId=' . $thisSurvey['id_survey']
                                   , '<img src="' . get_icon_url( 'edit' ) . '" alt="' . get_lang('Modify') . '" />');

            // DELETE  Request LINK
            $scriptToChangeSurvey =  $_SERVER['PHP_SELF'] . '?surveyId=' . $thisSurvey['id_survey'] . '&amp;cmd=';

            $thisRow['delete'] =
            claro_html_cmd_link( $scriptToChangeSurvey . 'exDelete'
                               , '<img src="' . get_icon_url( 'delete' ) . '" '
                               . 'alt="' . get_lang('Delete') . '" '. 'border="0" />'
                               , array( 'onclick' => 'javascript:if(!confirm(\'' . clean_str_for_javascript(get_lang('Please confirm your choice')) . '\')) return false;'));

            $thisRow['move'] ='';
            // DISPLAY MOVE UP COMMAND only if it is not the top
            if ($iterator != 1)
            {
                $thisRow['move'] .=
                    claro_html_cmd_link( $scriptToChangeSurvey . 'exMoveUp'
                        , '<img src="' . get_icon_url( 'move_up' ) . '" '
                        . 'alt="' . get_lang('Move up') . '" ' . 'border="0" />');
            }


            // DISPLAY MOVE DOWN COMMAND only if it is not the bottom
            if($iterator < count($surveyList) )
            {
                $thisRow['move'] .=
                    claro_html_cmd_link( $scriptToChangeSurvey . 'exMoveDown'
                        , '<img src="' . get_icon_url( 'move_down' ) . '" '
                        . ' alt="'.get_lang('Move down') . '" border="0" />');
            }

            //  Visibility
            if ($thisSurvey['visibility'] == 'SHOW')
            {
                $thisRow['disp'] = claro_html_cmd_link( $scriptToChangeSurvey . 'mkHide'
                ,    '<img src="' . get_icon_url( 'visible' ) . '" alt="' . get_lang('Invisible').'" />' );
            }
            else
            {

                $thisRow['disp'] = claro_html_cmd_link( $scriptToChangeSurvey . 'mkShow'
                ,    '<img src="' . get_icon_url( 'invisible' ) . '" alt="' . get_lang('Visible') . '" />');
            }

        } // end if is_AllowedToEdit

        $surveyGrid[] = $thisRow;

    } // end visibility and survey done


}    // end foreach ( $surveyList as $thisSurvey)

if ( $is_allowedToEdit )
{
    $cmdMenu[] = claro_html_cmd_link( 'edit_survey.php?cmd=rqCreate'
                                    ,  get_lang('Add survey'));
    
    $cmdMenu[] = claro_html_cmd_link( 'import_survey.php'
                                    ,  get_lang('Import survey'));
}


$titleList[] = get_lang('Title');
if ($is_allowedToEdit)
{
    $titleList[] = get_lang('Export');
    $titleList[] = get_lang('Results');
    $titleList[] = get_lang('Modify');
    $titleList[] = get_lang('Delete');
    $titleList[] = get_lang('Move');
    $titleList[] = get_lang('Visibility');
}
$dgSurvey = new claro_datagrid($surveyGrid);

$dgSurvey->set_colTitleList($titleList);

$cmdColAttr = array( 'align' => 'center' , 'width' => '5%');
$cmdColAttrList =  array( 'export' => $cmdColAttr
                        , 'stats' =>$cmdColAttr
                        , 'edit' =>$cmdColAttr
                        , 'delete' => $cmdColAttr
                        , 'move' => $cmdColAttr
                        , 'disp' => $cmdColAttr
                        );
$dgSurvey->set_colAttributeList($cmdColAttrList);


/**
 *  DISPLAY SECTION
 */

$nameTools = get_lang('List of surveys');

$out = claro_html_tool_title($nameTools )
     . claro_html_msg_list($msgList)
     . claro_html_menu_horizontal($cmdMenu,'CLSURVEYgeneralMenu')
     . $dgSurvey->render();

if (count($surveyList) < 1)
{
    $out .= '<br /><blockquote>' . get_lang('No survey') . '</blockquote>' . "\n";
}

Claroline::getInstance()->display->body->appendContent( $out );
echo Claroline::getInstance()->display->render();