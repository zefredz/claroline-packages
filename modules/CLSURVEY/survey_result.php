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
$msgList=array();
$cmdMenu=array();
$gidReset=1;

/**
 *  CLAROLINE MAIN SETTINGS
 */

require '../../claroline/inc/claro_init_global.inc.php';
FromKernel::uses( 'utils/input.lib' );
$context = array(CLARO_CONTEXT_COURSE=>claro_get_current_course_id());

// local librairies
include_once('./lib/survey.lib.php');

if ( ! get_init('in_course_context')  || ! get_init('is_courseAllowed') ) claro_disp_auth_form(true);

claro_set_display_mode_available(FALSE);

//set flag following init settings
$is_allowedToEdit = claro_is_allowed_to_edit();

$displayList = FALSE;
// courseadmin reserved page
if( !$is_allowedToEdit )
{
    header("Location: ./survey_list.php");
    exit();
}

/**
 *                    COMMANDS SECTION (COURSE MANAGER ONLY)
 */

$userInput = Claro_UserInput::getInstance();

$surveyId   = $userInput->get( 'surveyId' )   ? (int)$userInput->get( 'surveyId' )      : null;
$questionId = $userInput->get( 'questionId' ) ? (int) $userInput->get( 'questionId' )   : 0;
$cmd        = $userInput->get( 'cmd' )        ? $userInput->get( 'cmd' )                : '';

$cmdMenu = array();

if (!is_null($surveyId))
{
    $surveyQuestionList = survey_get_questions_of_survey($surveyId) ;

    // test if cid owner is correct
    if (count($surveyQuestionList) >0)
    {
        $displayList = TRUE;
    }
    else
    {
        $msgList['info'][]  = get_lang('This survey is empty')
        .                    '<br />'
        .                    '<a href="./survey_list.php">' . get_lang('Continue') . '</a>'
        .                    '<br />' . "\n"
        ;
    }

/**
 * DELETE ALL RESULTS COMMAND
 */

    if ( 'exDelete' == $cmd )
    {
        if (survey_empty_votes($surveyId))
        {
            $msgList['info'][]  = get_lang('Results have been deleted')
            .                    '<br />'
            .                    '<a href="./survey_list.php">' . get_lang('Continue') . '</a>'
            .                    '<br />' . "\n"
            ;
            $displayList = FALSE;
        }
        else
        {
            $msgList['warn'][]  = get_lang('Results have not been deleted')
            .                    '<br />'
            .                    '<a href="./survey_list.php">' . get_lang('Continue') . '</a>'
            .                    '<br />' . "\n"
            ;
            $displayList = TRUE;
        }

    }

    /**
       * EXPORT RESULTS COMMAND
       */

    if ( 'exExport' == $cmd )
    {
        $iterator = 0;
        $content ='';
        $fileName = 'survey' . $surveyId . '.csv';

        foreach ( $surveyQuestionList as $thisSurveyQuestion)
        {
            $iterator ++ ;
            $questionId = $thisSurveyQuestion['questionId'];

            $tbl = get_module_main_tbl( array( 'survey_answer' ) );
            $sql = "SELECT answer, count(answer) as qty
                    FROM `" . $tbl['survey_answer'] . "`
                    WHERE id_question = " . (int) $questionId . "
                      AND cid = '" . addslashes(claro_get_current_course_id()) . "'
                    GROUP BY answer";
            $results = claro_sql_query_fetch_all_rows($sql);

            if( ! empty( $results ) )
            {
                foreach ($results as $thisresult )
                {
                    $content .= $thisSurveyQuestion['title'] .';'
                    . $thisresult['answer'] .';'
                    . $thisresult['qty'] . "\n";
                }
            }
        }
        
        $fileExportSize = strlen( $content );

        header('Expires: Wed, 01 Jan 1990 00:00:00 GMT');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Content-type: application/text');
        header('Content-Length: '.$fileExportSize);
        header('Content-Disposition: attachment; filename="'.$fileName.'";');
        echo( $content );
        exit();
    }
}

//if($is_allowedToEdit) // check teacher status
//{
//     $displayList = TRUE;
//} // end if is_allowedToEdit


// PREPARE DISPLAYS

if ( $is_allowedToEdit && $displayList )
{
    $cmdMenu[] = claro_html_cmd_link('survey_result.php?cmd=exDelete&surveyId='.$surveyId, '<img src="' . get_icon_url( 'delete' ) . '" border="0" alt="">&nbsp;' . get_lang('Delete all results'));
    $cmdMenu[] = claro_html_cmd_link($_SERVER['PHP_SELF'] . '?cmd=exExport&amp;surveyId=' . $surveyId, '<img src="' . get_icon_url( 'export' ) . '" border="0" alt="">&nbsp;'  . get_lang("Export results"));
}

$survey = get_survey_data($surveyId);


/**
 *  DISPLAY SECTION
 */

$interbredcrump[]= array ('url' => './survey_list.php', 'name' => get_lang('Surveys'));
$interbredcrump[]= array ('url' => './survey.php?surveyId=' . $surveyId, 'name' => $survey['title']);

$nameTools = get_lang('results');

$out = claro_html_tool_title($nameTools)
     . claro_html_msg_list($msgList)
     . claro_html_menu_horizontal($cmdMenu);

/*----------------------------------------------------------------------------
LIST OF SURVEYS
----------------------------------------------------------------------------*/

if ($displayList && (count($surveyQuestionList) > 0) )
{
    $vote = survey_votes_for_survey($surveyId);

    if (strip_tags($survey['description'] != ''))
	{
        $out .= '<p>' . $survey['description'] . '</p>';
	}

	$out .=  	'<table class="claroTable emphaseLine" border="0" align="center" cellpadding="2" cellspacing="2" width="100%">'  . "\n";
	$iterator = 0;

	foreach ( $surveyQuestionList as $thisSurveyQuestion)
	{

		$iterator ++ ;
		$questionId = $thisSurveyQuestion['questionId'];

        $out .= '<tr class="superHeader">'
        .    '<th colspan="3">'
		.   $thisSurveyQuestion['title'] . "\n"
		.	'</th>'
		;

	    if (strip_tags($thisSurveyQuestion['description'])<>'')
		{
			$out .= '</td></tr>'
            .    '<tr ><td colspan="3">' . "\n"
			.    $thisSurveyQuestion['description'] . "\n"
			.    '</td></tr>' . "\n"
			;
		}

		$fin = substr_count($thisSurveyQuestion['option'],';');
		$options = explode(';',$thisSurveyQuestion['option']);

		switch ($thisSurveyQuestion['type'])
		{
			case 'radioH' || 'radioV':

/*
 devenu utile par   $vote = survey_votes_for_survey($surveyId);
mais conserv� pour info
			    $tbl = claro_sql_get_tbl('survey_answer', $context);
                $sql = "SELECT answer,
                               count(answer) AS qty
                        FROM `" . $tbl['survey_answer'] . "`
                        WHERE id_question = " . (int) $questionId . "
                          AND cid ='" . addslashes(claro_get_current_course_id()) . "'
                          AND id_survey = " . (int) $surveyId . "
								GROUP BY answer";
				$results = claro_sql_query_fetch_all_rows($sql);
*/

				$total = 0;

                if ( isset ($vote[$questionId]) )
                {
				    foreach ($vote[$questionId] as $qty )
				    {
					    $total += $qty;
				    }
                }

				if ($total == 0)
				{
                    $out .= '<tr>' . "\n"
                    .    '<td colspan="3">' . get_lang('No result available') . '</td>' . "\n"
                    .    '</tr>'
                    ;
				}
				else
				{
                    $out .= '<tr class="headerX">' . "\n"
                    .    '<th>' . get_lang('Answer') . '</th>' . "\n"
                    .	 '<th>' . get_lang('Number of answer/Total') . $total. '</th>' . "\n"
                    .	 '<th>' . get_lang('Percentage') . '</th>' . "\n"
                    .    '</tr>' . "\n"
                    ;

				foreach ($vote[$questionId] as $answer => $qty )
				{
						if ($answer=='')
						{
							$answer = get_lang('No answer');
						}
						$percent = number_format(($qty/$total) * 100,2);
                        $out .= '<tr>' . "\n"
                        .    '<td>' . $answer.'</td>' . "\n"
                        .    '<td>' . $qty . '</td>' . "\n"
                        .    '<td>'.claro_html_progress_bar($percent,1).' '.$percent.' %</td>'
						.	'</tr>'
						;
					}
				}
			break;

			case 'input':

                $result = get_answer_by_question($questionId,$context);

				$total = 0;
				foreach ($results as $thisresult )
				{
					$total = $total + $thisresult['qty'];
				}

				if ($total == 0)
				{
                    $out .= '<tr>' . "\n"
                    .    '<td colspan="3"> ' . get_lang('No result available') . '</td>' . "\n"
                    .    '</tr>'
                    ;
				}
				else
				{
                    $out .= '<tr class="headerX">' . "\n"
                    .    '<th colspan="3">' . get_lang('Answer') . '</th>' . "\n"
                    ;

					foreach ($results as $thisresult )
					{
                        $out .= '<tr>'
                        .    '<td colspan="3">' . $thisresult['answer'] . '</td>'
                        .    '</tr>'
						;
					}
				}
				break;
		}

    }    // end foreach ( $surveyList as $thisSurveyQuestion)

    $out .= '</table>' . "\n"	;
}

Claroline::getInstance()->display->body->appendContent( $out );
echo Claroline::getInstance()->display->render();
