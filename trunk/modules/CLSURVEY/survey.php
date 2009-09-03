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

/**
 * This script is the view of a survey.
 * There is 3 modes
 * - Read only
 * - Voting
 * - Editing
 *
 * Editing is read only with edit link
 */

define('SURVEY_VOTE_MODE',  'SURVEY_VOTE_MODE' . __LINE__);
define('SURVEY_RONLY_MODE', 'SURVEY_RONLY_MODE' . __LINE__);
define('SURVEY_EDIT_MODE', 'SURVEY_EDIT_MODE' . __LINE__);
$tlabelReq = 'CLSURVEY';
$gidReset=1;
$cmdMenu=array();
$cmdQuestionListMenu = array();
$msgList=array();
$mode = SURVEY_VOTE_MODE;

/**
 *  CLAROLINE MAIN SETTINGS
 */

require '../../claroline/inc/claro_init_global.inc.php';
$context = array(CLARO_CONTEXT_COURSE=>claro_get_current_course_id());

add_module_lang_array($tlabelReq);

if ( ! get_init('in_course_context')  || ! get_init('is_courseAllowed') ) claro_disp_auth_form(true);

claro_set_display_mode_available(TRUE);
//set flag following init settings
$is_allowedToEdit = claro_is_allowed_to_edit();

// local librairies
include_once('./lib/survey.lib.php');
// claroline libraries
if ($mode != SURVEY_RONLY_MODE) include_once get_path('includePath') . '/lib/form.lib.php';
// get specific conf file

claro_set_display_mode_available(TRUE);

// DEFAULT DISPLAY
$displayList = FALSE;

/**
 *                    COMMANDS SECTION (COURSE MANAGER ONLY)
 */

$surveyId  	= isset($_REQUEST['surveyId'])         ? (int) $_REQUEST['surveyId']   : null;
$questionId = isset($_REQUEST['questionId']) ? $_REQUEST['questionId'] : 0;
$cmd 		= isset($_REQUEST['cmd'])        ? $cmd = $_REQUEST['cmd'] : '';
$switchMode = isset($_REQUEST['switchMode'])
? $_REQUEST['switchMode'] : 'rqVote';

$mode       = ($is_allowedToEdit && ($switchMode == 'rqEdit' ))
? SURVEY_EDIT_MODE : SURVEY_VOTE_MODE;

$cmdMenu=array();

if (is_null($surveyId)) header("Location: ./survey_list.php");
// test access

$surveyVisible = survey_get_survey_visibility($surveyId, $context);

if ( is_null($surveyVisible) || (!$surveyVisible AND !$is_allowedToEdit))
{
    header("Location: survey_list.php");
    exit();
}

if ( !empty($cmd) ) // check teacher status
{
    /**
     * DELETE question COMMAND
     */

    if ( 'exDelete' == $cmd )
    {
        $return = delete_question_survey($questionId);

        if ($return)
        {
            $msgList['info'][] = get_lang('Question has been deleted');
        }
        $displayList = true ;
    }

    /**
     * SAVE ANSWER COMMAND
     */
    if ( 'exSave' == $cmd )
    {
        if(is_survey_completed_by_user($surveyId))
        {
            $msgList['warn'][] = get_lang('This survey is already filled by you. Your answer are not saved again. As The survey are anonymous, we cannot show you your answers');
            $msgList['info'][] = '<a href="./survey_list.php"> '.get_lang('Continue').'</a>' . "\n";
        }
        else
        {
            $surveyQuestionQty = survey_count_question_in_survey($surveyId) ;
            $answer = isset($_REQUEST['answer']) ? $_REQUEST['answer'] 	: array();
            if (count($answer) > 0)
            {
                if (survey_save_user_answer( (int) $surveyId, $answer))
                {
                    // As Answer are anonymous, another flag would be set
                    // to know  who has vote

                    survey_set_vote_status_for_user( (int) $surveyId
                                                   , (int) claro_get_current_user_id());
                    $msgList['info'][] = get_lang('Answers have been saved');
                }
            }
            $msgList['info'][] = '<a href="./survey_list.php"> '.get_lang('Continue').'</a>' . "\n";
        }

    }
    /**
     * MOVE UP AND MOVE DOWN COMMANDS
     */
    if ($cmd == 'exMoveDown' OR $cmd =='exMoveUp')
    {
        if (move_question($questionId,'exMoveDown' == $cmd?'DOWN':'UP',$surveyId))
        {
            $msgList['info'][] = get_lang('Position modified');
			header("Location: survey.php?switchMode=rqEdit&surveyId=".$surveyId);
        }
    }
}
else
{
    $displayList = TRUE;
} // end if is_allowedToEdit

// PREPARE DISPLAYS

$surveyItemList = array();

if (!is_null($surveyId))
{
    $surveyItemList = survey_get_questions_of_survey($surveyId) ;
    $surveyQuestionQty = count($surveyItemList) ;
}

$displayButtonSwitchMode = (bool) $is_allowedToEdit && ($mode != SURVEY_EDIT_MODE) ;

$survey = survey_get_survey_data($surveyId);

$surveyQty = count($survey) ;

/**
 *  DISPLAY SECTION
 */
// prepare
if ( $mode == SURVEY_EDIT_MODE )
{
    $cmdMenu[] = claro_html_cmd_link('edit_question.php?cmd=rqCreate&amp;surveyId=' . $surveyId,
    '<img src="' . get_icon_url( 'survey' , 'CLSURVEY' ) . '" alt="" /> ' . get_lang('Add question'));
}
else
{
    $msgList['info'][] = get_lang('This survey is anonymous.  We only save your answer not your identification.');
}

if ( $is_allowedToEdit )
{
    if ( $mode == SURVEY_EDIT_MODE  )
    {
        $cmdMenu[] = claro_html_cmd_link($_SERVER['PHP_SELF'] . '?switchMode=rqVote&amp;surveyId=' . $surveyId,
        '<img src="' .get_icon_url( 'survey' , 'CLSURVEY' ) . '" alt="" /> ' . get_lang('Vote for this survey'));
    }
    else
    {
        $cmdMenu[] = claro_html_cmd_link($_SERVER['PHP_SELF'] . '?switchMode=rqEdit&amp;surveyId=' . $surveyId,
        '<img src="' . get_icon_url( 'edit' ) . ' alt="" /> ' . get_lang('Edit this survey'));
    }

    $cmdMenu[] = claro_html_cmd_link('survey_result.php?switchMode=rqEdit&amp;surveyId=' . $surveyId
                                    , '<img src="' . get_icon_url( 'statistics' ) . '" '
                                    . 'alt="' . get_lang('Results') . '" />'
                                    . get_lang('Results'));
}


$interbredcrump[]= array ('url' => './survey_list.php', 'name' => get_lang('Surveys'));

$nameTools = $survey['title'];
$noPHP_SELF=true;
// Start output

// Display header
include get_path('includePath') . '/claro_init_header.inc.php' ;

echo claro_html_tool_title($nameTools)
.    '<p>' . claro_html_menu_horizontal($cmdMenu) . '</p>'
.    claro_html_msg_list($msgList)
;

echo '<table class="claroTable emphaseLine" border="0" align="center" cellpadding="2" cellspacing="2" width="100%">';

if ($mode == SURVEY_EDIT_MODE)
{
    echo '<tr class="headerX">' . "\n"
    .    '<th>' . "\n"
    .    '<span style="float:right" >'
    .    '<img src="' . get_icon_url( 'textzone' ) .'" alt="' . get_lang('Textzone') . '" border="0" />'
    .    claro_html_cmd_link( 'edit_survey.php?cmd=rqEdit&surveyId=' . $surveyId
                            , '<img src="' . get_icon_url( 'edit' ) . ' alt="" /> ')
    .    '</span>'
    .    get_lang('Description')
    .    '</th>' . "\n"
    .    '</tr>' . "\n"
    ;
}

echo '<tr>' . "\n"
.    '<td>' . "\n"
.    $survey['description']
.    '</td>' . "\n"
.    '</tr>' . "\n"
;

if (count($surveyItemList) == 0)
{
    echo '<tr>' . "\n"
    .    '<td>' . "\n"
    .    '<blockquote>' . get_lang('No question') . '</blockquote>' . "\n"
    .    '</td>' . "\n"
    .    '</tr>' . "\n"
    ;
}
elseif ($displayList)
{
    echo ($mode == SURVEY_VOTE_MODE
    ?    '<form method="get" action="survey.php">'
    .    form_input_hidden('surveyId',$surveyId)
    .    form_input_hidden('cmd','exSave')
    :    '')
    .    ''  . "\n"
    ;

    $iterator = 0;

    foreach ( $surveyItemList as $thisSurveyQuestion)
    {
        $iterator ++ ;
        $questionMenu = array();
        $questionId = $thisSurveyQuestion['questionId'];

        // EDIT Request LINK
        $questionMenu[]= claro_html_cmd_link( 'edit_question.php'
        . '?cmd=rqEdit&amp;questionId=' . $questionId
        . '&amp;surveyId=' . $surveyId
        , '<img src="' . get_icon_url( 'edit' ) . ' alt="' . get_lang('Modify') . '" border="0" />'
        );
        // DELETE  Request LINK
        $questionMenu[]= claro_html_cmd_link( $_SERVER['PHP_SELF']
        . '?cmd=exDelete&amp;questionId=' . $questionId
        . '&amp;surveyId=' . $surveyId
        , '<img src="' . get_icon_url( 'delete' ) . '" alt="' . get_lang('Delete') . '" border="0" />'
        , array( 'onclick' => 'javascript:if(!confirm(\'' . clean_str_for_javascript(get_lang('Please confirm your choice')) . '\')) return false;')
        );


        // DISPLAY MOVE UP COMMAND only if it is not the top
        if ($iterator != 1)
        {
            $questionMenu[]= '<a href="'.$_SERVER['PHP_SELF'].'?cmd=exMoveUp&questionId=' . $questionId . '&surveyId=' . $surveyId .'">'."\n"
            .      '<img src="'. get_icon_url( 'move_up' ) .'" alt= "'. get_lang('Move up') . '" border="0" />'."\n"
            .      '</a>'."\n";
        }

        // DISPLAY MOVE DOWN COMMAND only if it is not the bottom
        if ($iterator < count($surveyItemList))
        {
            $questionMenu[] = '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exMoveDown&questionId=' . $questionId . '&surveyId=' . $surveyId .'">'."\n"
            .      '<img src="' . get_icon_url( 'move_down' ) . '" alt="' . get_lang('Move down') . '" border="0" />'."\n"
            .      '</a>' . "\n"
            ;
        }
        echo form_input_hidden('questionId['.$iterator.']',$thisSurveyQuestion['questionId']). "\n";
        echo '<tr class="headerX">' . "\n"
        .    '<th>'

        .    ($mode == SURVEY_EDIT_MODE ? '<span style="float:right" >' . "\n"
             .    implode( "\n" ,$questionMenu) . "\n"
             .    '</span>' . "\n"
             : ''
             )        .    $thisSurveyQuestion['title'] . "\n"


        .   '</th>' . "\n"
        .    '</tr>' . "\n"
        ;


        if (strip_tags($thisSurveyQuestion['description'])<>'')
        {
            echo '<tr>' . "\n"
            .    '<td>' . "\n"
            .    $thisSurveyQuestion['description'] . "\n"
            .    '</td>' . "\n"
            .    '</tr>' . "\n"
            ;
        }

        $fin = substr_count($thisSurveyQuestion['option'],';');
        $options = explode(';',$thisSurveyQuestion['option']);

        switch ($thisSurveyQuestion['type'])
        {
            case 'radioH':
                echo '<tr>'. "\n"
                .	 '<td>' . "\n"
                .    '<table width="100%">'. "\n"
                .	 '<tr>'. "\n"
                ;
                for ($i=0; $i<=$fin; $i++)
                {
                    echo '<td>'. "\n"
                    .    '<input id="q' . $questionId . $i . '" type="radio" name="answer['.$questionId.']" value="'.$options[$i].'">'
                    .	 '<label for="q' . $questionId . $i . '">' . $options[$i].'</label>'. "\n"
                    .	 '</td>'
                    ;
                }
                echo '</tr>'. "\n"
                .	 '</table>'. "\n"
                .    '</td>'. "\n"
                .	 '</tr>'
                ;
                break;

            case 'radioV':
                for ($i=0; $i<=$fin; $i++)
                {
                    echo '<tr>'. "\n"
                    .	 '<td>'. "\n"
                    .    '<input  id="q' . $questionId.$i . '" type="radio" name="answer[' . $questionId . ']" value="' . $options[$i] . '">'. "\n"
                    .	 '<label for="q' . $questionId.$i . '">' . $options[$i] . '</label>'. "\n"
                    .	 '</td>'. "\n"
                    .	 '</tr>'. "\n"
                    ;
                }
                break;

            case 'input':
                echo '<tr>'. "\n"
                .	 '<td>' . "\n"
                .    '<textarea name="answer['.$questionId.']" cols="60">'
                .	 '</textarea>'
                .    '</td>'. "\n"
                .	 '</tr>'
                ;
                break;
        }

    }    // end foreach ( $surveyList as $thisSurveyQuestion)

    if ($mode == SURVEY_VOTE_MODE)
    echo '<tr>' . "\n"
    .	 '<td>'. "\n"
    .	 '<input type="submit" value="' . get_lang('Finish') . '" />' . "\n"
    .	 '</form>'
    ;
}

echo '</table>' . "\n"
;

include get_path('includePath') . '/claro_init_footer.inc.php';

?>