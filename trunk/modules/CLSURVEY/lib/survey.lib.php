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
* function move_entry_survey($inte_id,$cmd,$tbl,$idName)
*
* @param  integer $item_id  an valid id
* @param  string $cmd       'UP' or 'DOWN'
* @param  string $tbl table
* @param  string $idName name of id field
* @return true;
*
* @author Philippe Dekimpe <dlp@ecam.be>
*/
function move_entry_survey($item_id, $cmd,$id_name, $id_survey = NULL, $context=null)
{
    $tbl = claro_sql_get_tbl('survey_list', $context);

    if ( $cmd == 'DOWN' )
    {
        $thisId = $item_id;
        $sortDirection      = 'ASC';
    }
    elseif ( $cmd == 'UP' )
    {
        $thisId = $item_id;
        $sortDirection      = 'DESC';
    }
    else
    return false;

    if ( $sortDirection )
    {
        $sql = "# " . basename(__FILE__) . " func " . __FUNCTION__ . "()
                SELECT " . $id_name . ",
                         rank
                FROM `" . $tbl['survey_list'] . "` "  ;
        if ($id_name == 'id_question')
        {
            $sql .= ' WHERE id_survey='.$id_survey;
        }
        $sql .=  "  ORDER BY `rank` " . $sortDirection;

        $result = claro_sql_query($sql);
        $thisRankFound = false;
        $thisRank = '';
        while ( (list ($id, $rank) = mysql_fetch_row($result)) )
        {
            if ($thisRankFound == true)
            {
                $nextId    = $id;
                $nextRank  = $rank;

                $sql = "# " . basename(__FILE__) . " func " . __FUNCTION__ . "()
                        UPDATE `" . $tbl['survey_list'] . "`
                        SET rank = '" . (int) $nextRank . "'
                        WHERE " . $id_name . " =  '" . (int) $thisId . "'";

                claro_sql_query($sql);

                $sql = "# " . basename(__FILE__) . " func " . __FUNCTION__ . "()
                        UPDATE `" . $tbl['survey_list'] . "`
                        SET rank = '" . $thisRank . "'
                        WHERE " . $id_name . " =  '" . $nextId . "'";
                claro_sql_query($sql);

                break;
            }

            if ( $id == $thisId )
            {
                $thisRank      = $rank;
                $thisRankFound = true;
            }
        }
    }
    return true;
}


/**
* function move_entry_survey($inte_id,$cmd,$tbl,$idName)
*
* @param  integer $item_id  an valid id
* @param  string $cmd       'UP' or 'DOWN'
* @param  string $tbl table
* @param  string $idName name of id field
* @return true;
*
* @author Philippe Dekimpe <dlp@ecam.be>
*/
function move_survey($item_id, $cmd, $context=null)
{
    $tbl = claro_sql_get_tbl('survey_list', $context);

    if ( $cmd == 'DOWN' )
    {
        $thisId = $item_id;
        $sortDirection      = 'ASC';
    }
    elseif ( $cmd == 'UP' )
    {
        $thisId = $item_id;
        $sortDirection      = 'DESC';
    }
    else
    return false;

    if ( $sortDirection )
    {
        $sql = "# " . basename(__FILE__) . " func " . __FUNCTION__ . "()
                SELECT id_survey,
                         rank
                FROM `" . $tbl['survey_list'] . "`
                ORDER BY `rank` " . $sortDirection;

        $result = claro_sql_query($sql);
        $thisRankFound = false;
        $thisRank = '';
        while ( (list ($id, $rank) = mysql_fetch_row($result)) )
        {
            if ($thisRankFound == true)
            {
                $nextId    = $id;
                $nextRank  = $rank;

                $sql = "# " . basename(__FILE__) . " func " . __FUNCTION__ . "()
                        UPDATE `" . $tbl['survey_list'] . "`
                        SET rank = '" . (int) $nextRank . "'
                        WHERE id_survey = " . (int) $thisId;

                claro_sql_query($sql);

                $sql = "# " . basename(__FILE__) . " func " . __FUNCTION__ . "()
                        UPDATE `" . $tbl['survey_list'] . "`
                        SET rank = '" . $thisRank . "'
                        WHERE id_survey = " . (int) $nextId;
                claro_sql_query($sql);

                break;
            }

            if ( $id == $thisId )
            {
                $thisRank      = $rank;
                $thisRankFound = true;
            }
        }
    }
    return true;
}


function move_question($item_id, $cmd,$id_survey, $context=null)
{
    $tbl = claro_sql_get_tbl('survey_question', $context);

    if ( $cmd == 'DOWN' )
    {
        $thisId = $item_id;
        $sortDirection      = 'ASC';
    }
    elseif ( $cmd == 'UP' )
    {
        $thisId = $item_id;
        $sortDirection      = 'DESC';
    }
    else
    return false;

    if ( $sortDirection )
    {
        $sql = "# " . basename(__FILE__) . " func " . __FUNCTION__ . "()
                SELECT id_question AS questionId
                     , rank
                FROM `" . $tbl['survey_question'] . "`
                WHERE id_survey=" . (int) $id_survey . "
                ORDER BY `rank` " . $sortDirection
        ;

        $result = claro_sql_query($sql);
        $thisRankFound = false;
        $thisRank = '';
        while ( (list ($questionId, $rank) = mysql_fetch_row($result)) )
        {
            if ($thisRankFound == true)
            {
                $nextId    = $questionId;
                $nextRank  = $rank;

                $sql = "# " . basename(__FILE__) . " func " . __FUNCTION__ . "()
                        UPDATE `" . $tbl['survey_question'] . "`
                        SET rank = '" . (int) $nextRank . "'
                        WHERE id_question = " . (int) $thisId;

                claro_sql_query($sql);

                $sql = "# " . basename(__FILE__) . " func " . __FUNCTION__ . "()
                        UPDATE `" . $tbl['survey_question'] . "`
                        SET rank = '" . $thisRank . "'
                        WHERE id_question =  " . (int) $nextId
                ;
                claro_sql_query($sql);

                break;
            }

            if ( $questionId == $thisId )
            {
                $thisRank      = $rank;
                $thisRankFound = true;
            }
        }
    }
    return true;
}

/**
* function delete_question_survey($questionId)
*
* delete question and answers
*
* @param  integer $questionId
* @return true;
*
* @author Philippe Dekimpe <dlp@ecam.be>
*/
function delete_question_survey($questionId,$context=null)
{
    $tbl = claro_sql_get_tbl(array( 'survey_question'
                                  , 'question_list'
                                  , 'survey_answer'
                                  , 'survey_user'), $context);
    if ( $questionId <> '' )
    {
        $sql = "# " . basename(__FILE__) . " func " . __FUNCTION__ . "()
                DELETE FROM `" . $tbl['question_list'] . "` WHERE id_question=" . (int) $questionId;
        $return = claro_sql_query($sql);

        $sql = "# " . basename(__FILE__) . " func " . __FUNCTION__ . "()
                DELETE FROM `" . $tbl['survey_question'] . "` WHERE id_question=" . (int) $questionId;
        $return = claro_sql_query($sql);

        $sql = "# " . basename(__FILE__) . " func " . __FUNCTION__ . "()
                DELETE FROM `" . $tbl['survey_answer'] . "` WHERE id_question=" . (int) $questionId;
        $return = claro_sql_query($sql);

        return (bool) $return;
    }
    else
    return false;

}

/**
 * Enter description here...
 *
 * @param integer $surveyId id of the survey
 * @param array $context array  of  key if the context is not the current
 * @return boolean true if the survey is visible
 */

function survey_get_survey_visibility($surveyId, $context=null)
{
    $tbl = claro_sql_get_tbl( 'survey_list', $context);
    $sql = "# " . basename(__FILE__) . " func " . __FUNCTION__ . "()
            SELECT `visibility`
            FROM `" . $tbl['survey_list'] . "`
            WHERE cid='" . addslashes(claro_get_current_course_id()) . "'
              AND id_survey = " . (int) $surveyId;

    $result = claro_sql_query_get_single_value($sql);
    if ( is_null($result)) return null;
    else                   return (bool) ('SHOW' == $result);

}


/**
 * Return the list of survey in the context
 *
 * @param array $context
 * @return unknown
 */
function get_survey_list($context)
{
    $tbl = claro_sql_get_tbl('survey_list', $context);

    $sql = "# " . basename(__FILE__) . " func " . __FUNCTION__ . "()
            SELECT `id_survey`,
                   `title`,
                   `description`,
                   `visibility`
            FROM `" . $tbl['survey_list'] . "`
            WHERE cid = '" . addslashes($context[CLARO_CONTEXT_COURSE]) . "'
            ORDER BY rank";

    return claro_sql_query_fetch_all_rows($sql) ;

}

/**
 * Return the list of question for a given survey
 *
 * @param integer $surveyId id of question
 * @param array $context
 * @return unknown
 */

function get_survey_question_list($surveyId, $context)
{
    $tbl = claro_sql_get_tbl('survey_question', $context);

    $sql = "# " . basename(__FILE__) . " func " . __FUNCTION__ . "()
                SELECT `id_question` AS questionId
                FROM `" . $tbl['survey_question'] . "`
                WHERE id_survey = " . (int) $surveyId ;

    return claro_sql_query_fetch_all_rows($sql);

}

/**
 * Delete a survey
 *
 * @param integer $id
 * @param array $context
 * @return query result
 */
function delete_survey($surveyId, $context)
{
    $tbl = claro_sql_get_tbl('survey_list', $context);
    $questionList = get_survey_question_list($surveyId, $context);
    if (count($questionList))
    {
        foreach ($questionList as $thisQuestion)
        {
            delete_question_survey($thisQuestion);
        }
    }

    $sql = "# " . basename(__FILE__) . " func " . __FUNCTION__ . "()
            DELETE FROM `" . $tbl['survey_list'] . "`
            WHERE id_survey = " . (int) $surveyId;
    return claro_sql_query($sql);
}


function is_survey_completed_by_user($surveyId, $userId=null)
{
    if (is_null($userId) ) $userId = claro_get_current_user_id();
    $tbl = claro_sql_get_tbl('survey_user');

    $sql = "# " . basename(__FILE__) . " func " . __FUNCTION__ . "()
         SELECT count(`id_survey`)
		    FROM `" . $tbl['survey_user'] . "`
		 WHERE id_user=" . (int) $userId . "
		   AND id_survey= " . (int) $surveyId;
    return (bool) claro_sql_query_get_single_value($sql);
}

function is_survey_started_by_user($surveyId, $userId=null)
{
    if (is_null($userId) ) $userId = claro_get_current_user_id();
    $tbl = claro_sql_get_tbl( 'survey_user');
    $sql = "# " . basename(__FILE__) . " func " . __FUNCTION__ . "()
         SELECT count(`id_survey`)
		    FROM `" . $tbl['survey_user'] . "`
		 WHERE id_user=" . (int) $userId . "
		   AND id_survey= " . (int) $surveyId;
    return claro_sql_query_get_single_value($sql);
}

/**
 * return date of a given question in a given course
 *
 * @param integer $questionId
 * @param array $context
 * @return array( `title`, `description`, `option`, `type`)
 */
function survey_get_survey_question_data($questionId, $context=null)
{
    $tbl = claro_sql_get_tbl( 'question_list' , $context);

    $cid = get_init('_cid');

    $sql = "# " . basename(__FILE__) . " func " . __FUNCTION__ . "()
            SELECT `title`,
                   `description`,
                   `option`,
                   `type`
      FROM `" . $tbl['question_list'] . "`
	 WHERE `id_question` = " . (int) $questionId . "
	   AND `cid` = '" . addslashes($cid) . "'";

    return claro_sql_query_get_single_row($sql);

}
/*
function survey_save_answer($surveyId, $answer,$questionId,$context=null)
{
    $tbl = claro_sql_get_tbl(array( 'survey_question'
                                  , 'question_list'
                                  , 'survey_answer'
                                  , 'survey_user'), $context);

    $sql = "SELECT count(Q.`id_question`)
		    FROM `" . $tbl['survey_question'] . "`     AS S
		    INNER JOIN `" . $tbl['question_list'] . "` AS Q
		            ON Q.id_question = S.id_question
		    WHERE S.id_survey = " . (int) $surveyId;

    $surveyQuestionQty = claro_sql_query_get_single_value($sql);

    $sql = "INSERT INTO `" . $tbl['survey_user'] . "`
			    SET `id_survey` = " . (int) $surveyId . "
		        ,   `id_user`   = " . (int) claro_get_current_user_id();
    claro_sql_query($sql);

    for ($i=1; $i<=$surveyQuestionQty; $i++)
    {
    //     les requests doivent sortir ici
        $answer[$i]     = isset($_REQUEST['answer'.$i]) ? $answer[$i] = $_REQUEST['answer'.$i] 	: '';
        $questionId[$i] = isset($_REQUEST['questionId'.$i]) ? (int) $_REQUEST['questionId'.$i] : 0;

        $sql = "INSERT INTO  `" . $tbl['survey_answer'] . "`
			        SET `id_survey` = " . (int) $surveyId . " ,
						`id_question` = " . (int) $questionId[$i] . " ,
						`answer` = '" . addslashes($answer[$i]) . "',
						`cid` = '" . addslashes(claro_get_current_course_id()) . "'";
        $return = claro_sql_query($sql);

    }

}
*/
/**
 * return the main properties of a given survey
 *
 * @param integer $surveyId
 * @param array $context
 * @return array(id, `title`, `description`, date_created, `visibility`, `rank`)
 */
function survey_get_survey_data($id_survey, $context=null)
{
    $tbl = claro_sql_get_tbl( 'survey_list', $context);

    $courseId = (is_array($context) && array_key_exists(CLARO_CONTEXT_COURSE,$context))
    ? $context[CLARO_CONTEXT_COURSE]
    : $courseId = claro_get_current_course_id()
    ;

    $sql = "# " . basename(__FILE__) . " func " . __FUNCTION__ . "()
            SELECT `id_survey` AS id,
                   `title`,
                   `description`,
                   `date_created`,
                   `visibility`,
                   `rank`
      			FROM `" . $tbl['survey_list'] . "`
        		WHERE id_survey = " . (int) $id_survey . "
        		AND cid = '" . addslashes($courseId) . "'" ;
    return claro_sql_query_get_single_row($sql);

}

/**
 * Fetch list of the Question of a given survey
 *
 * @param integer $surveyId id of the survey
 * @param array $context (default: current)
 * @return array(`id_question`, `title`, `description`, `type`, `option`)
 */

function survey_get_questions_of_survey($surveyId,$context=null)
{
    if (!is_null($context) && !is_array($context))
    {
        trigger_error('Invalid fortmat for context: array or NULL attempt',E_USER_ERROR);
    }
    else
    {
        $courseId = (!is_null($context) && array_key_exists(CLARO_CONTEXT_COURSE,$context))
        ? $context[CLARO_CONTEXT_COURSE]
        : claro_get_current_course_id();
    }

    $tbl = claro_sql_get_tbl(array('survey_question', 'question_list'), $context);

    $sql = "# " . basename(__FILE__) . " func " . __FUNCTION__ . "()
            SELECT Q.`id_question` AS questionId,
	               Q.`title`,
	               Q.`description`,
	               Q.`type`,
	               Q.`option`
			  FROM `" . $tbl['survey_question'] . "` AS S
			  INNER JOIN `" . $tbl['question_list'] . "`  AS Q
	                  ON Q.id_question = S.id_question
			  WHERE S.id_survey = " . (int) $surveyId."
			    AND Q.`cid` = '" . addslashes($courseId) . "'
			  ORDER BY Q.id_question";

    return  claro_sql_query_fetch_all_rows($sql) ;
}











/**
 * Count the question ask in a given survey
 *
 * @param integer $surveyId
 * @param array $context
 * @return integer or claro_error
 */
 function survey_count_question_in_survey($surveyId, $context=null)
 {
    $tbl = claro_sql_get_tbl(array('survey_question', 'question_list'), $context);

    $sql = "# " . basename(__FILE__) . " func " . __FUNCTION__ . "()
        SELECT count(`id_question`)
	    FROM `" . $tbl['survey_question'] . "`
	    WHERE id_survey = " . (int) $surveyId;

    return claro_sql_query_get_single_value($sql) ;

 }

 /**
  * set user as voted for a given survey
  *
  * @param integer $surveyId
  * @param integer $userId
  * @param array $context
  * @return query result.
  */
 function survey_set_vote_status_for_user($surveyId, $userId, $context=null)
 {
     $tbl = claro_sql_get_tbl(array('survey_user'), $context);
     $sql = "# " . basename(__FILE__) . " func " . __FUNCTION__ . "()
             INSERT INTO `" . $tbl['survey_user'] . "`
		     SET `id_survey` = " . (int) $surveyId . "
	         ,   `id_user`   = " . (int) $userId;
     return  claro_sql_query($sql);

 }

/**
 * save vote of a user
 *
 * @param unknown_type $surveyId
 * @param unknown_type $answer
 * @param unknown_type $context
 * @return unknown
 */
function survey_save_user_answer($surveyId, $answer, $context=null)
{
    if (!is_null($context) && !is_array($context))
    {
        trigger_error('Invalid fortmat for context: array or NULL attempt',E_USER_ERROR);
    }
    else
    {
        $courseId = (!is_null($context) && array_key_exists(CLARO_CONTEXT_COURSE,$context))
                  ? $context[CLARO_CONTEXT_COURSE]
                  : claro_get_current_course_id();
    }
    $tbl = claro_sql_get_tbl( 'survey_answer', $context);

    $sqlLineTemplate  = "# QUESTION = #ID_QUESTION# " . "\n"
                      . "( " . (int) $surveyId
                      . ", #ID_QUESTION#"
                      . ", '#OPTION#',"
                      . " '" . addslashes($courseId) . "')";

    foreach ($answer as $questionId => $selectedOption )
    {
        // check id $questionId own $selectedOption

        $sqlLines[] = str_replace('#OPTION#', addslashes($selectedOption),
                      str_replace('#ID_QUESTION#', $questionId,
                      $sqlLineTemplate));

    }

    $sql = "# " . basename(__FILE__) . " func " . __FUNCTION__ . "()
            # SURVEY = " . (int) $surveyId . "\n"
         . "INSERT INTO  `" . $tbl['survey_answer'] . "`
                (`id_survey`, `id_question`, `answer`, `cid` )
		    VALUES
		    ". implode(', '."\n",$sqlLines)."

			";

    return claro_sql_query($sql);

}

/**
 * remove all answer posted by users for a given survey
 *
 * @param integer $surveyId id of the survey
 * @param array $context (default: current)
 */
function survey_empty_votes($surveyId, $context=null)
{

    if (!is_null($context) && !is_array($context))
    {
        trigger_error('Invalid fortmat for context: array or NULL attempt',E_USER_ERROR);
    }
    else
    {
        $courseId = (!is_null($context) && array_key_exists(CLARO_CONTEXT_COURSE,$context))
        ? $context[CLARO_CONTEXT_COURSE]
        : claro_get_current_course_id();
    }

    $tbl = claro_sql_get_tbl(array('survey_answer', 'survey_user'), $context);

    $sql = "DELETE FROM `" . $tbl['survey_user'] . "`
                 WHERE `id_survey` = " . (int) $surveyId;

    $return = claro_sql_query($sql);

    $sql = "DELETE FROM `" . $tbl['survey_answer'] . "`
                WHERE id_survey = " . (int) $surveyId . "
                  AND cid = '" . addslashes($courseId) . "'";
    $return = $return && claro_sql_query($sql);

}


/**
 * Fetch voting result for a given survey
 *
 * @param integer $surveyId
 * @param array $context (default null to get currents values)
 * @return array of question containing array of answer with qty  as value :
 */
function survey_votes_for_survey($surveyId, $context=null)
{

    if (!is_null($context) && !is_array($context))
    {
        trigger_error('Invalid fortmat for context: array or NULL attempt',E_USER_ERROR);
    }
    else
    {
        $courseId = (!is_null($context) && array_key_exists(CLARO_CONTEXT_COURSE,$context))
        ? $context[CLARO_CONTEXT_COURSE]
        : claro_get_current_course_id();
    }


    $tbl = claro_sql_get_tbl('survey_answer', $context);

    $sql = "# " . basename(__FILE__) . " func " . __FUNCTION__ . "()
            SELECT id_question AS questionId,
                   answer,
                   count(answer) AS qty
            FROM `" . $tbl['survey_answer'] . "`
            WHERE cid ='" . addslashes($courseId) . "'
              AND id_survey = " . (int) $surveyId . "
					GROUP BY id_question, answer";
    $answerList = claro_sql_query_fetch_all_rows($sql);

    foreach ($answerList as $answerLine)
    {
        $votesResult[$answerLine['questionId']][$answerLine['answer']] = $answerLine['qty'];
    }

    return $votesResult;
}



/**
 * Return the list of answer propostion and count of vote.
 *
 * @param integer $questionId
 * @param array $context
 * @return array of array(answer,qty)
 */
function get_answer_by_question($questionId, $context)
{
    if (!is_array($context) || ! array_key_exists(CLARO_CONTEXT_COURSE,$context)) return claro_failure::set_failure('Need course context');
    $tbl = claro_sql_get_tbl( 'survey_answer', $context);
    $sql = "# function get_answer_by_question()
             SELECT answer,
                   count(*) as qty
              FROM `" . $tbl['survey_answer'] . "`
             WHERE id_question = " . (int) $questionId . "
               AND cid = '" . $context['CLARO_CONTEXT_COURSE'] . "'
                GROUP BY answer";
    return claro_sql_query_fetch_all_rows($sql);
}

/**
 * Get title and description of a given survey
 *
 * @param integer $surveyId
 * @param array $context
 * @return array(title, description)
 */
function get_survey_data($surveyId,$context=null)
{

    $tbl = claro_sql_get_tbl('survey_list', $context);
    $sql = "SELECT `title`,
               `description`
        FROM `" . $tbl['survey_list'] . "`
        WHERE id_survey = " . (int) $surveyId."
        ORDER BY rank";
    return claro_sql_query_get_single_row($sql);
}

?>