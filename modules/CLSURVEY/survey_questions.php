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
$gidReset=1;
$cmdMenu=array();
$msgList=array();
/**
 *  CLAROLINE MAIN SETTINGS
 */

require '../../claroline/inc/claro_init_global.inc.php';
$context = array(CLARO_CONTEXT_COURSE=>claro_get_current_course_id());

if ( ! get_init('in_course_context')  || ! get_init('is_courseAllowed') ) claro_disp_auth_form(true);

claro_set_display_mode_available(TRUE);
//set flag following init settings
$is_allowedToEdit = claro_is_allowed_to_edit();

// local librairies
include_once('./lib/survey.lib.php');

// claroline libraries
include_once get_path('includePath') . '/lib/form.lib.php';

// get specific conf file

claro_set_display_mode_available(TRUE);

//set flag following init settings

/**
 * DB tables definition
 */

$tbl = claro_sql_get_tbl(array('survey_question', 'survey_question_list', 'survey_answer', 'survey_list', 'survey_user'), $context);

// DEFAULT DISPLAY
$displayList = FALSE;

/**
 *                    COMMANDS SECTION (COURSE MANAGER ONLY)
 */

$idSurvey  	= isset($_REQUEST['surveyId'])         ? (int) $_REQUEST['surveyId']         : 0;
$questionId = isset($_REQUEST['questionId']) ? (int) $_REQUEST['questionId'] : 0;
$cmd 		= isset($_REQUEST['cmd'])        ? $cmd = $_REQUEST['cmd']       : '';

$cmdMenu=array();

// test access
$surveyVisible =  (bool) survey_get_survey_visibility($idSurvey, $context);

if (   claro_failure::get_last_failure() == ERR_UNKNOW_SURVEY
   || (!$surveyVisible AND !$is_allowedToEdit))
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


        $sql = "SELECT Q.`id_question` AS questionId
			    FROM `" . $tbl['survey_question'] . "`     AS S
			    INNER JOIN `" . $tbl['survey_question_list'] . "` AS Q
			            ON Q.id_question = S.id_question
			    WHERE S.id_survey = " . (int) $idSurvey;

        $surveyQuestionQty = claro_sql_query_affected_rows($sql) ;

        $sql = "INSERT INTO `" . $tbl['survey_user'] . "`
			    SET `id_survey` = " . (int) $idSurvey . "
		        ,   `id_user`   = " . (int) claro_get_current_user_id();
        claro_sql_query($sql);

        for ($i=1; $i<=$surveyQuestionQty; $i++)
        {
            $answer[$i]     = isset($_REQUEST['answer'.$i]) ? $answer[$i] = $_REQUEST['answer'.$i] 	: '';
            $questionId[$i] = isset($_REQUEST['questionId'.$i]) ? (int) $_REQUEST['questionId'.$i] : 0;

            $sql = "INSERT INTO  `" . $tbl['survey_answer'] . "`
			        SET `id_survey` = " . (int) $idSurvey . " ,
						`id_question` = " . (int) $questionId[$i] . " ,
						`answer` = '" . addslashes($answer[$i]) . "',
						`cid` = '" . addslashes(claro_get_current_course_id()) . "'";
            $return = claro_sql_query($sql);

        }
        if ($surveyQuestionQty)
        {
            $msgList['info'][] = get_lang('Answers have been saved');
        }
        $msgList['info'][] = '<a href="./survey_list.php"> '.get_lang('Continue').'</a>' . "\n";
    }
    /**
     * MOVE UP AND MOVE DOWN COMMANDS
     */
    if ($cmd == 'exMoveDown' OR $cmd =='exMoveUp')
    {
        if ( 'exMoveDown' == $cmd  )
        {
            $return = move_entry_survey($questionId,'DOWN',$tbl['survey_question'],'questionId',$idSurvey);
        }
        if ( 'exMoveUp' == $cmd )
        {
            $return = move_entry_survey($questionId,'UP',$tbl['survey_question'],'questionId',$idSurvey);
        }

        if ($return)
        {
            $msgList['info'][] = get_lang('Position modified');
        }
        $displayList = true;
    }
}
else
{
    $displayList = TRUE;
} // end if is_allowedToEdit


// PREPARE DISPLAYS

if ($idSurvey<>0)
{
    $sql = "SELECT Q.`id_question` AS questionId
                 , Q.`title`
                 , Q.`description`
                 , Q.`type`
                 , Q.`option`
            FROM       `" . $tbl['survey_question'] . "` AS S
            INNER JOIN `" . $tbl['survey_question_list'] . "`   AS Q
                    ON Q.id_question = S.id_question
            WHERE S.id_survey = " . (int) $idSurvey . "
            ORDER BY S.rank ASC";

    // get list
    $surveyQuestionList = claro_sql_query_fetch_all($sql) ;
    $surveyQuestionQty = claro_sql_query_affected_rows($sql) ;
}

$displayButtonLine = (bool) $is_allowedToEdit  ;


$sql = "SELECT `title`, `description`
        FROM `" . $tbl['survey_list'] . "`
        WHERE id_survey = " . (int) $idSurvey;
$survey = claro_sql_query_get_single_row($sql);
$surveyQty = claro_sql_query_affected_rows($sql) ;

/**
 *  DISPLAY SECTION
 */
// prepare
if ( $displayButtonLine )
{
    $cmdMenu[] = claro_html_cmd_link('edit_question.php?cmd=rqCreate&amp;surveyId=' . $idSurvey,
    '<img src="' . get_icon_url( 'survey' , 'CLSURVEY' ) . '" alt="" /> ' . get_lang('Add question'));
}
$interbredcrump[]= array ('url' => './survey_list.php', 'name' => get_lang('Surveys'));

$nameTools = $survey['title'];

// Start output

// Display header
include get_path('includePath') . '/claro_init_header.inc.php' ;

echo claro_html_tool_title($nameTools)
.    claro_html_msg_list($msgList)
.    claro_html_menu_horizontal($cmdMenu)
;

/*----------------------------------------------------------------------------
LIST OF SURVEYS
----------------------------------------------------------------------------*/

if ($surveyQuestionQty == 0)
{
    echo '<br /><blockquote>' . get_lang('No question') . '</blockquote>' . "\n";
}

elseif ($displayList)
{
    echo '<p>' . get_lang('This survey is anonymous.  We only save your answer not your identification.') . '</p>';

    if (strip_tags($survey['description']<>''))
    {
        echo '<p>' . $survey['description'] . '</p>';
    }
    echo '<form method="get" action="survey.php">';
    echo form_input_hidden('surveyId',$idSurvey);
    echo form_input_hidden('cmd','exSave');

    echo '<table class="claroTable emphaseLine" border="0" align="center" cellpadding="2" cellspacing="2" width="100%">'  . "\n";
    $iterator = 0;

    foreach ( $surveyQuestionList as $thisSurveyQuestion)
    {

        $iterator ++ ;
        $questionId = $thisSurveyQuestion['questionId'];
        echo form_input_hidden('questionId'.$iterator,$thisSurveyQuestion['questionId']). "\n";

        echo '<tr class="headerX">' . "\n"
        .    '<th>'
        .    $thisSurveyQuestion['title'] . "\n"
        ;

        if ($is_allowedToEdit)
        {
            // EDIT Request LINK
            echo '<a href="edit_question.php?cmd=rqEdit&amp;questionId=' . $questionId . '&amp;surveyId=' . $idSurvey . '">'
            .    '<img src="' . get_icon_url( 'edit' ) . ' alt="' . get_lang('Modify') . '" />'
            .    '</a>' . "\n"


            // DELETE  Request LINK
            .    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exDelete&amp;questionId=' . $questionId . '&amp;surveyId=' . $idSurvey .'" '
            .    ' onclick="javascript:if(!confirm(\'' . clean_str_for_javascript(get_lang('Please confirm your choice')) . '\')) return false;">'
            .    '<img src="' . get_icon_url( 'delete' ) .'" alt="' . get_lang('Delete') . '" border="0" />'
            .    '</a>' . "\n";

            // DISPLAY MOVE UP COMMAND only if it is not the top
            if ($iterator != 1)
            {
                echo   '<a href="'.$_SERVER['PHP_SELF'].'?cmd=exMoveUp&amp;questionId=' . $questionId . '&amp;surveyId=' . $idSurvey .'">'."\n"
                .      '<img src="'.get_icon_url( 'move_up' ) . '" alt= "'. get_lang('Move up') . '" border="0" />'."\n"
                .      '</a>'."\n";
            }

            // DISPLAY MOVE DOWN COMMAND only if it is not the bottom
            if ($iterator < $surveyQuestionQty)
            {
                echo   '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exMoveDown&amp;questionId=' . $questionId . '&amp;surveyId=' . $idSurvey .'">'."\n"
                .      '<img src="' . get_icon_url( 'move_down' ) . '" alt="' . get_lang('Move down') . '" border="0" />'."\n"
                .      '</a>' . "\n"
                ;
            }

        } // end if is_AllowedToEdit

        if (strip_tags($thisSurveyQuestion['description'])<>'')
        {
            echo
            '</th></tr>'
            .	'<tr><td>' . "\n"
            .   $thisSurveyQuestion['description'] . "\n"
            .   '</td></tr>' . "\n"
            ;
        }

        $fin = substr_count($thisSurveyQuestion['option'],';');
        $options = explode(';',$thisSurveyQuestion['option']);

        echo '<tr>'. "\n"
        .	 '<td>'
        ;

        switch ($thisSurveyQuestion['type'])
        {
            case 'radioH':
                echo '<table width="100%"><tr><td>';
                for ($i=0; $i<=$fin; $i++)
                {
                    echo '<input id="q' . $questionId.$i . '" type="radio" name="answer'.$iterator.'" value="'.$options[$i].'">'
                    .	 '<label for="q' . $questionId.$i . '">' . $options[$i].'</label></td><td>'
                    ;
                }
                echo '</td></tr></table>';
                break;

            case 'radioV':
                for ($i=0; $i<=$fin; $i++)
                {
                    echo '<input  id="q' . $questionId.$i . '" type="radio" name="answer' . $iterator . '" value="' . $options[$i] . '">'. "\n"
                    .	 '<label for="q' . $questionId.$i . '">' . $options[$i] . '</label>'. "\n"
                    .	 '<td>'. "\n"
                    .	 '<tr>'. "\n"
                    .	 '<td>'. "\n"
                    ;
                }
                break;

            case 'input':
                echo '<textarea name="answer'.$iterator.'" cols="60">'
                .	 '</textarea>'
                ;
                break;
        }
        echo '</td></tr>';

    }    // end foreach ( $surveyList as $thisSurveyQuestion)

    echo '<tr>' . "\n"
    .	 '<td>'
    .	 '<input type="submit" value="' . get_lang('Finish') . '" />' . "\n"
    .	 '</form>'
    .	 '</td>' . "\n"
    .	 '</tr>' . "\n\n"
    .	 '</table>' . "\n"
    ;
}

include get_path('includePath') . '/claro_init_footer.inc.php';


?>
