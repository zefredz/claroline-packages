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
$msgList = array();
$gidReset = array();
/**
 *  CLAROLINE MAIN SETTINGS
 */

require '../../claroline/inc/claro_init_global.inc.php';
FromKernel::uses( 'utils/input.lib' );

$context = array(CLARO_CONTEXT_COURSE=>claro_get_current_course_id());

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

$userInput = Claro_UserInput::getInstance();

/*
* Execute commands
*/
$cmd = $userInput->get( 'cmd' ) ? $userInput->get( 'cmd' ) : '';
$surveyId = $userInput->get( 'surveyId' ) ? (int)$userInput->get( 'surveyId' ) : null;
$surveyData = get_survey_data( $surveyId );
$title = $userInput->get( 'title' ) ? $userInput->get( 'title' ) : $surveyData[ 'title' ];
$description = $userInput->get( 'description' ) ? $userInput->get( 'description' ) : $surveyData[ 'description' ];


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

$out = claro_html_tool_title($toolTitle)
     . claro_html_msg_list($msgList);

/*--------------------------------------------------------------------
FORM SECTION
--------------------------------------------------------------------*/

if( $displayForm )
{
    if ($survey['date_created'] != '')
    {
        $out .= '<p>'
        .    get_lang( 'Date of creation : %date ',
                       array ( '%date'=> $survey['date_created']))
        .    '</p>'
        ;
    }

    /*$out .= '<form method="post" action="./edit_survey.php" >' . "\n\n"
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
    .	 '</td>' . "\n"
    .	 '</tr>' . "\n\n"
    .	 '</form>'
    .    '</tbody>' . "\n\n"
    .	 '</table>' . "\n\n"
    .    '<td>' . get_lang( '<span class="required">*</span> denotes required field' ) . '</td>' . "\n"
    ;*/

    if ( $cmd == 'rqCreate' ) $cmd = 'exCreate';
    if ( $cmd == 'rqEdit' ) $cmd = 'exEdit';
    
    $out .= '<form id="importSurveyForm" action="'
            . htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) )
            . '" enctype="multipart/form-data" method="post">'  . "\n"
            . '    <input type="hidden" name="cmd" value="'. $cmd . '" />' . "\n"
            . '    <input type="hidden" name="surveyId" value="' . $surveyId . '" />' . "\n"
            . '    <h4>' . get_lang( 'Title' ) . '</h4>'
            . '    <input id="surveyTitle" type="text" name="title" style="width: 330px;" value="' . $title . '"/><br />'
            . '    <h4>' . get_lang( 'Description ') . '</h4>'
            . '    <textarea id="surveyDescription" name="description" rows="8" cols="40">' . $description . '</textarea><br />'
            . '    <input type="submit" name="submitCSV" value="' . get_lang( 'Submit' ) . '" />' . "\n"
            .      claro_html_button( htmlspecialchars( Url::Contextualize( 'survey_list.php' ) ) , get_lang( 'Cancel' ) )  . "\n"
            . '</form>';
            
    //$dialogBox->form( $form );
}

Claroline::getInstance()->display->body->appendContent( $out );
echo Claroline::getInstance()->display->render();