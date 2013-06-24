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
$msgList=array();
$gidReset=1;

/**
 *  CLAROLINE MAIN SETTINGS
 */

require '../../claroline/inc/claro_init_global.inc.php';
FromKernel::uses( 'utils/input.lib' );

/**
 * DB tables definition
 */

$tbl = get_module_main_tbl(array('survey_question', 'survey_question_list' ));


// claroline libraries
include_once get_path('includePath') . '/lib/form.lib.php';

if ( ! get_init('in_course_context')  || ! get_init('is_courseAllowed') ) claro_disp_auth_form(true);

$is_allowedToEdit = claro_is_allowed_to_edit();

// courseadmin reserved page
if( !$is_allowedToEdit )
{
    header("Location: ./survey_list.php");
    exit();
}

// claroline libraries
include_once get_path('includePath') . '/lib/form.lib.php';
// local librairies
include_once('./lib/survey.lib.php');

$displayForm = false;

$form['title'] = '';
$form['description'] = '';
$form['type'] = '';
$form['option'] = '';

/*
* Execute commands
*/
$userInput = Claro_UserInput::getInstance();

$idSurvey       = $userInput->get( 'surveyId' )     ? (int) $userInput->get( 'surveyId' )    : 0;
$questionId     = $userInput->get( 'questionId' )   ? (int) $userInput->get( 'questionId' )  : 0;
$cmd            = $userInput->get( 'cmd' )          ? $userInput->get( 'cmd' )               : '';
$title          = $userInput->get( 'title' )        ? $userInput->get( 'title' )             : '';
$option         = $userInput->get( 'option' )       ? $userInput->get( 'option' )            : '';
$description    = $userInput->get( 'description' )  ? $userInput->get( 'description' )       : '';
$type           = $userInput->get( 'type' )         ? $userInput->get( 'type' )              : '';

$listType		= array( get_lang('Multiple choice - horizontal answer')=>'radioH'
                       , get_lang('Multiple choice - vertical answer')=>'radioV'
                       , get_lang('Textbox')=>'input'
                       );

if (($cmd=='exEdit') and ($questionId<>0))
{

    $affectedRow = survey_update_question($questionId, $title, $description, $option, $type);

    if ($affectedRow == 1)
    {
        $msgList['info'][] = get_lang('Question has been updated')
        .                    '<br />'
        .                    '<a href="./survey.php?surveyId=' . (int) $idSurvey . '">'
        .                    get_lang('Continue')
        .                    '</a>'
        .                    '<br />' . "\n"
        ;
    }
}

if (($cmd=='exEdit') and ($questionId==0))
{
    $sql = "INSERT INTO `" . $tbl['survey_question_list'] . "`
	        SET `title`       = '" . addslashes($title) . "'
	        ,   `description` = '" . addslashes($description) . "'
	        ,   `option`      = '" . addslashes($option) . "'
	        ,   `type`        = '" . addslashes($type) . "'
	        ,   `cid`         = '" . get_init('_cid') . "'";
    $insertId=claro_sql_query_insert_id($sql);

    if (($idSurvey<>0) and ($insertId<>0))
    {
        $sql = "INSERT INTO `" . $tbl['survey_question']."`
		        SET `id_question` = " . (int) $insertId . "
		        ,   `id_survey`   = " . (int) $idSurvey;
        claro_sql_query_insert_id($sql);

        $sql = "SELECT MAX(rank) FROM `" . $tbl['survey_question'] . "`";
        $insertId2 = claro_sql_query_get_single_value($sql)+1;

        $sql = "UPDATE `" . $tbl['survey_question'] . "`
		        SET rank=" . (int) $insertId2  . "
		        WHERE id_question = " . (int) $insertId;
        $affectedRow = claro_sql_query_affected_rows($sql);
    }
    if ($insertId)
    {
        $msgList['info'][] = get_lang('Question has been inserted')
        .                    '<br />'
        .                    '<a href="./survey.php?surveyId=' . $idSurvey . '"> '
        .                    get_lang('View this survey')
        .                    '</a>'
        .					 ' | '
        .                    '<a href="./edit_question.php?cmd=rqCreate&surveyId=' . $idSurvey . '"> '
        .                    get_lang('Add question')
        .                    '</a>'
        .                    '<br />' . "\n"
        ;
    }
}

if( $cmd == 'rqEdit' )
{
    $survey    = survey_get_survey_question_data($questionId);

    $form['title'] 				= $survey['title'];
    $form['description'] 		= $survey['description'];
    $form['option'] 			= $survey['option'];
    $form['type'] 				= $survey['type'];

    $displayForm = true;

    // test if cid owner is correct
    if (!count($survey))
    {
        header("Location: ../");
        exit();
    }
}

if( $cmd == 'rqCreate' )
{
    $displayForm = true;
}

/*
* Output
*/
$interbredcrump[]= array ('url' => './survey_list.php', 'name' => get_lang('Surveys'));
$interbredcrump[]= array ('url' => './survey.php?surveyId=' . $idSurvey , 'name' => get_lang('Questions'));

if( is_null($idSurvey) )
{
    $nameTools = get_lang('New question');
    $toolTitle = $nameTools;
}
elseif( $cmd == 'rqEdit' )
{
    $nameTools = get_lang('Edit question');
    $toolTitle['mainTitle'] = $nameTools;
    //$toolTitle['subTitle'] = ;
}
else
{
    $nameTools = get_lang('Question');
    $toolTitle['mainTitle'] = $nameTools;
    //$toolTitle['subTitle'] = );
}

$out = claro_html_tool_title($toolTitle)
     . claro_html_msg_list($msgList);

/*--------------------------------------------------------------------
FORM SECTION
--------------------------------------------------------------------*/

if( $displayForm )
{
    $out .= '<form method="post" action="./edit_question.php" >' . "\n\n"
    .    form_input_hidden('questionId',$questionId) . "\n"
    .    form_input_hidden('surveyId',$idSurvey)  . "\n"
    .    form_input_hidden('cmd','exEdit')  . "\n"
    .    '<table border="0" cellpadding="5">' . "\n"

    //--
    // title
    .    '<tr>' . "\n"
    .	 '<td valign="top">' . "\n"
    .	 '<label for="title">' . get_lang('Question') . '&nbsp;' . "\n"
    .	 '<span class="required">*</span>&nbsp;:</label>' . "\n"
    .	 '</td>' . "\n"
    .	 '<td>' . "\n"
    .	 '<input type="text" name="title" id="title" size="60" maxlength="200" value="' . $form['title'] . '" />' . "\n"
    .	 '</td>' . "\n"
    .	 '</tr>' . "\n\n"


    // description
    /*$out .= '<tr>' . "\n"
    .	 '<td valign="top"><label for="description">'.get_lang('Commentar').'&nbsp;:</label></td>' . "\n"
    .	 '<td>'.claro_html_textarea_editor('description',htmlspecialchars($form['description'])).'</td>' . "\n"
    .	 '</tr>' . "\n\n"; */

    // type
    .    '<tr>' . "\n"
    .	 '<td valign="top">' . "\n"
    .	 '<label for="type">' . get_lang('Type') . '&nbsp;:</label>' . "\n"
    .	 '</td>' . "\n"
    .	 '<td>' . "\n"
    .	 claro_html_form_select('type',$listType,$form['type']) . "\n"
    .	 '</td>' . "\n"
    .	 '</tr>' . "\n\n"


    // option
    .    '<tr>' . "\n"
    .	 '<td valign="top">' . "\n"
    .	 '<label for="option">'.get_lang('Options').'&nbsp;:</label>' . "\n"
    .	 '</td>' . "\n"
    .	 '<td>' . "\n"
    .	 get_lang('If you choose the type "Multiple choice", complete the options and separate each option by a semicolon character (;)<br>
			Example : Yes;No or 1;2;3;4;5 or Agree;Neither Agree nor Disagree;Disagree') . "\n"
    .	 '<br>' . "\n"
    .	 '<input type="text" name="option" id="option" value="'.$form['option'].'" size="60" />' . "\n"
    .	 '</td>' . "\n"
    .	 '</tr>' . "\n\n"

    .    '</tbody>' . "\n\n"
    .	 '</table>' . "\n\n"


    .    '<p>' . "\n"
    .	 '<input type="submit" name="cmdOk" value="'.get_lang('Submit').'" />' . "\n"
    .	 '</p>' . "\n"
    ;
}

/**
 * Update proprerties of a question in a survey
 *
 * @param integer $questionId
 * @param string $title
 * @param string $description
 * @param string $option
 * @param string $type
 * @return integer
 */

function survey_update_question($questionId, $title, $description, $option, $type)
{
    $tbl = get_module_main_tbl(array('survey_question_list'));

    $sql = "UPDATE `" . $tbl['survey_question_list'] . "` " . "\n"
    .      "SET `title` = '" . addslashes($title) . "' ," . "\n"
    .      "    `description` = '" . addslashes($description) . "'," . "\n"
    .      "    `option` = '" . addslashes($option) . "'," . "\n"
    .      "    `type` = '" . addslashes($type) . "'" . "\n"
    .      "WHERE id_question = " . (int) $questionId ;
    return claro_sql_query_affected_rows($sql);
}

Claroline::getInstance()->display->body->appendContent( $out );
echo Claroline::getInstance()->display->render();
