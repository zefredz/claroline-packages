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
 * @author Christophe Gesch� <moosh@claroline.net>
 * @author Philippe Dekimpe <dkp@ecam.be>
 * @author Claro Team <cvs@claroline.net>
 *
 */

$tlabelReq = 'CLSURVEY';
$msgList = array();
$gidReset = array();
/**
 *  CLAROLINE MAIN SETTINGS
 */

require '../../claroline/inc/claro_init_global.inc.php';
$context = array(CLARO_CONTEXT_COURSE=>claro_get_current_course_id());

add_module_lang_array($tlabelReq);

if ( ! get_init('in_course_context')  || ! get_init('is_courseAllowed') ) claro_disp_auth_form(true);

$is_allowedToEdit = claro_is_allowed_to_edit();

$form['title']  = '';
$form['description'] = '';

$survey['title'] = '';
$survey['description'] = '';
$survey['date_created'] = '';

$displayForm = false;


// courseadmin reserved page
if( !$is_allowedToEdit )
{
    header("Location: ./survey_list.php");
    exit();
}

// local librairies
include_once('./lib/survey.lib.php');
// claroline libraries
include_once get_path('includePath') . '/lib/form.lib.php';

/**
 * DB tables definition
 */

$tbl = claro_sql_get_tbl('survey_list', $context );

/*
* Execute commands
*/
$cmd = isset($_REQUEST['cmd']) ? $_REQUEST['cmd'] : '';
$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : '';
$surveyId = (int) isset($_REQUEST['surveyId']) ? $_REQUEST['surveyId'] : null;
$description = isset($_REQUEST['description']) ? $_REQUEST['description'] : '';

if ($cmd == 'exCreate')
{
    $cmd = 'exEdit';
    $surveyId = null;

}

if ($cmd == 'exEdit')
{
    if (is_null($surveyId))
    {
        $sql = "INSERT INTO `" . $tbl['survey_list'] . "`
	           SET `title`        = '" . addslashes($title) . "',
                   `description`  = '" . addslashes($description) . "',
                   `cid`          = '" . addslashes(get_init('_cid')) . "',
                   `date_created` = '" . date('Y-m-d') . "'";
        $surveyId = claro_sql_query_insert_id($sql);


        if ($surveyId)
        {
            $sql = "UPDATE `" . $tbl['survey_list'] . "`
	           SET `rank`   = " . (int) $surveyId . "
			WHERE id_survey = " . (int) $surveyId;
            claro_sql_query($sql);

            $msgList['info'][] = get_lang('Survey has been inserted') . '<br />'
            .                    '<a href="edit_question.php?cmd=rqCreate&amp;surveyId=' . $surveyId . '">'
            .                    get_lang('Continue')
            .                    '</a><br />' . "\n"
            ;

            $eventNotifier->notifyCourseEvent('survey_added', get_init('_cid'), get_init('_tid'), $surveyId, get_init('_gid'), '0');
        }
    }
    else
    {

        $sql = "UPDATE `" . $tbl['survey_list'] . "`
	        SET `title` = '" . addslashes($title) . "'
	          , `description`= '" . addslashes($description) .  "'
			WHERE id_survey = " . (int) $surveyId;
        $affectedRow = claro_sql_query_affected_rows($sql);

        if ($affectedRow == 1)
        {
            $msgList['info'][] = get_lang('Survey has been updated') . '<br /><a href="./survey.php?switchMode=rqEdit&amp;surveyId=' . $surveyId .'"> '.get_lang('Continue').'</a><br />' . "\n";
        }
    }
}

if( $cmd == 'rqEdit' && !is_null($surveyId))
{
    $survey = survey_get_survey_data($surveyId);

    $form['title']       = $survey['title'];
    $form['description'] = $survey['description'];

    $displayForm = true;
}

if( $cmd == 'rqCreate' )
{
    $displayForm = true;
}

/*
* Output
*/

$interbredcrump[]= array ('url' => './survey_list.php', 'name' => get_lang('Survey'));

if( is_null($surveyId) )
{
    $nameTools = get_lang('New survey');
    $toolTitle = $nameTools;
}
elseif( $cmd == 'rqEdit' )
{
    $nameTools = get_lang('Edit survey');
    $toolTitle['mainTitle'] = $nameTools;
    //$toolTitle['subTitle'] = ;
}
else
{
    $nameTools = get_lang('Survey');
    $toolTitle['mainTitle'] = $nameTools;
    //$toolTitle['subTitle'] = ;
}

include(get_path('includePath') . '/claro_init_header.inc.php');

echo claro_html_tool_title($toolTitle)
.    claro_html_msg_list($msgList)
;

/*--------------------------------------------------------------------
FORM SECTION
--------------------------------------------------------------------*/

if( $displayForm )
{
    if ($survey['date_created'] != '')
    {
        echo '<p>'
        .    get_lang( 'Date of creation : %date ',
                       array ( '%date'=> $survey['date_created']))
        .    '</p>'
        ;
    }

    echo '<form method="post" action="./edit_survey.php" >' . "\n\n"
    .    ( is_null($surveyId)
    ?    '<input type="hidden" name="cmd" value="exCreate" />' . "\n"
    :    '<input type="hidden" name="surveyId" value="' . (int) $surveyId . '" />' . "\n"
    .    '<input type="hidden" name="cmd" value="exEdit" />' . "\n"
    )
    .    '<input type="hidden" name="claroFormId" value="'.uniqid('').'">' . "\n"
    .    '<table border="0" cellpadding="5">' . "\n"

    //--
    // title
    .    '<tr>' . "\n"
    .	 '<td valign="top">' . "\n"
    .	 '<label for="title">' . get_lang('Title').'&nbsp;' . "\n"
    .	 '<span class="required">*</span>&nbsp;:' . "\n"
    .	 '</label>' . "\n"
    .	 '</td>' . "\n"
    .	 '<td>' . "\n"
    .	 '<input type="text" name="title" id="title" size="60" maxlength="200" value="' . $form['title'] . '" />' . "\n"
    .	 '</td>' . "\n"
    .	 '</tr>' . "\n\n"

    // description
    .    '<tr>' . "\n"
    .	 '<td valign="top">' . "\n"
    .	 '<label for="description">' . get_lang('Description') . '&nbsp;:</label>' . "\n"
    .	 '</td>' . "\n"
    .	 '<td>' . "\n"
    .	 claro_html_textarea_editor('description', $form['description']) . "\n"
    .	 '</td>' . "\n"
    .	 '</tr>' . "\n\n"

    // submit
    .    '<tr>' . "\n"
    .	 '<td colspan="3">'
    .	 '<input type="submit" value="'.get_lang('Finish').'" />' . "\n"
    .	 '</form>'
    .	 '</td>' . "\n"
    .	 '</tr>' . "\n\n"

    .    '</tbody>' . "\n\n"
    .	 '</table>' . "\n\n"
    ;
}

include(get_path('includePath').'/claro_init_footer.inc.php');

?>