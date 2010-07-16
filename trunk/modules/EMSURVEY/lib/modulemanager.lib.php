<?php // $Id$

/**
 * Claroline polls duplication tool
 *
 * @version     1.0 $Revision$
 * @copyright   (c) 2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     EMSURVEY
 * @author      Antonin Bourguignon <antonin.bourguignon@claroline.net>
 *
 */

class ModuleManager {
    
    /**
     * Get a survey's datas.
     * 
     * @return  array
     */
    public static function getSurvey( $surveyId )
    {
        if (empty($surveyId))
            return false;
        
        // Get table name
        $tbl_name       = claro_sql_get_main_tbl();
        $tbl_name       = array_merge($tbl_name, get_module_main_tbl(array('survey2_survey')));
        $tbl_course     = $tbl_name['course'];
        $tbl_survey     = $tbl_name['survey2_survey'];
        
        $sql = "SELECT s.id AS surveyId, s.title, "
             . "s.description, s.rank, c.cours_id AS courseId, "
             . "c.code AS courseCode, "
             . "c.administrativeNumber AS officialCode, "
             . "c.intitule " . "\n"
             . "FROM `{$tbl_survey}` AS s, `{$tbl_course}` AS c " . "\n"
             . "WHERE s.courseId = c.code "
             . "AND s.id = " . (int) $surveyId;
        
        $result = Claroline::getDatabase()->query($sql);
        $result->setFetchMode(Database_ResultSet::FETCH_ASSOC);
        
        return $result->fetch();
    }
    
    
    /**
     * Get all the polls and the course's name which they are linked.
     * 
     * @return  array of arrays of surveys ordered by officialCode, rank
     */
    public static function getSurveyList()
    {
        // Get table name
        $tbl_name       = claro_sql_get_main_tbl();
        $tbl_name       = array_merge($tbl_name, get_module_main_tbl(array('survey2_survey')));
        $tbl_course     = $tbl_name['course'];
        $tbl_survey     = $tbl_name['survey2_survey'];
        
        $sql = "SELECT s.id AS surveyId, s.title, "
             . "s.description, s.rank, c.cours_id AS courseId, "
             . "c.code AS courseCode, "
             . "c.administrativeNumber AS officialCode, "
             . "c.intitule " . "\n"
             . "FROM `{$tbl_survey}` AS s, `{$tbl_course}` AS c " . "\n"
             . "WHERE s.courseId = c.code " . "\n"
             . "ORDER BY c.code ASC, s.rank";
        
        $result = Claroline::getDatabase()->query($sql);
        $result->setFetchMode(Database_ResultSet::FETCH_ASSOC);
        
        return $result;
    }
    
    
    /**
     * Get all the courses.
     * 
     * @return  array of arrays of courses (ordered by officialCode)
     */
    public static function getCourseList()
    {
        // Get table name
        $tbl_name       = claro_sql_get_main_tbl();
        $tbl_course     = $tbl_name['course'];
        
        $sql = "SELECT c.cours_id AS courseId, "
             . "c.code AS courseCode, c.sourceCourseId, "
             . "c.administrativeNumber AS officialCode, "
             . "c.intitule " . "\n"
             . "FROM `{$tbl_course}` AS c " . "\n"
             . "ORDER BY c.code ASC";
        
        $result = Claroline::getDatabase()->query($sql);
        $result->setFetchMode(Database_ResultSet::FETCH_ASSOC);
        
        return $result;
    }
    
    
    /**
     * Get the speakers of a specific course.
     * 
     * @param   string course code
     * @return  array of speakers
     */
    public static function getCourseSpeakersList($courseCode)
    {
        // Get table name
        $tbl_name               = claro_sql_get_main_tbl();
        $tbl_course             = $tbl_name['course'];
        $tbl_user               = $tbl_name['user'];
        $tbl_rel_course_user    = $tbl_name['rel_course_user'];
        
        $sql = "SELECT u.nom AS lastname, u.prenom AS firstname " . "\n"
             . "FROM `{$tbl_user}` AS u " . "\n"
             
             . "LEFT JOIN `{$tbl_rel_course_user}` AS rcu "
             . "ON rcu.user_id = u.user_id " . "\n"
             
             . "WHERE rcu.code_cours = " . Claroline::getDatabase()->quote($courseCode) 
             . "AND rcu.tutor = 1 " . "\n"
             
             . "ORDER BY u.nom ASC";
        
        $result = Claroline::getDatabase()->query($sql);
        $result->setFetchMode(Database_ResultSet::FETCH_ASSOC);
        
        return $result;
    }
    
    
    /**
     * Get the selected courses.
     * 
     * @param   array of strings $courseCodes
     * @return  array of courses
     */
    public static function getSelectedCourseList($courseCodes)
    {
        // Get table name
        $tbl_name       = claro_sql_get_main_tbl();
        $tbl_course     = $tbl_name['course'];
        
        // Build the request
        if (!empty($courseCodes))
        {
            $quotedCourseCodes = array();
            foreach ($courseCodes as $code)
            {
                $quotedCourseCodes[] = Claroline::getDatabase()->quote($code);
            }
            
            $codes = implode(', ', $quotedCourseCodes);
            
            $sql = "SELECT c.cours_id AS courseId, "
                 . "c.code AS courseCode, c.sourceCourseId, "
                 . "c.administrativeNumber AS officialCode, "
                 . "c.intitule " . "\n"
                 . "FROM `{$tbl_course}` AS c " . "\n"
                 . "WHERE c.code IN ({$codes}) " . "\n"
                 . "ORDER BY c.code ASC";
            
            $result = Claroline::getDatabase()->query($sql);
            $result->setFetchMode(Database_ResultSet::FETCH_ASSOC);
            
            return $result;
        }
        else
        {
            return false;
        }
    }
    
    
    /**
     * Manage inputs from the course selection form: collect courses' ids 
     * and return them.
     * 
     * @param   user input
     * @return  array of string (officialCode)
     */
    public static function handleSelectCourseForm( $userInput )
    {
        $courseList = self::getCourseList();
        $selectedCourses = array();
        
        foreach ($courseList as $course)
        {
            $courseId = $userInput->get( 'selectCourse'.$course['courseId'], null );
            
            if (!empty($courseId))
                $selectedCourses[] = $course['courseCode'];
        }
        
        return $selectedCourses;
    }
    
    
    /**
     * Duplicate a survey in a list of courses.
     * 
     * @param   int     survey identifier
     * @param   string  new title (optionnal)
     * @param   string  course identifier (code)
     * @return  array   courseCode => surveyId
     */
    public static function duplicateSurvey( $surveyId, $newTitle = null, $courseCode )
    {
        // Get table name
        $tbl_name       = claro_sql_get_main_tbl();
        $tbl_name       = array_merge($tbl_name, get_module_main_tbl(
            array(
                'survey2_survey', 
                'survey2_survey_line', 
                'survey2_survey_line_question', 
                'survey2_survey_line_separator'
            )));
        $tbl_course                 = $tbl_name['course'];
        $tbl_survey                 = $tbl_name['survey2_survey'];
        $tbl_survey_line            = $tbl_name['survey2_survey_line'];
        $tbl_survey_line_question   = $tbl_name['survey2_survey_line_question'];
        $tbl_survey_line_separator  = $tbl_name['survey2_survey_line_separator'];
        
        
        $resultsArray = array();
        
        // Duplicate the survey itself
        $title = (isset($newTitle)) ? 
            (Claroline::getDatabase()->quote($newTitle)." AS title") : 
            ("title");
        
        $sql = "INSERT INTO `{$tbl_survey}` "
             . "    SELECT '' AS id, '{$courseCode}' AS courseId, "
             . "    {$title}, description, "
             . "    is_anonymous, is_visible, resultsVisibility, "
             . "    startDate, endDate, rank, maxCommentSize " . "\n"
             . "    FROM `{$tbl_survey}` " . "\n"
             . "    WHERE id = " . (int) $surveyId;
        
        Claroline::getDatabase()->exec($sql);
        
        $newSurveyId = Claroline::getDatabase()->insertId();
        $resultsArray[$courseCode] = $newSurveyId;
        
        // Duplicate survey lines
        $sql = "INSERT INTO `{$tbl_survey_line}` " . "\n"
             . "    SELECT '' AS id, {$newSurveyId} AS surveyId, rank " . "\n"
             . "    FROM `{$tbl_survey_line}` " . "\n"
             . "    WHERE surveyId = {$surveyId}";
        
        Claroline::getDatabase()->exec($sql);
        
        // Duplicate survey questions
        $sql = "INSERT INTO `{$tbl_survey_line_question}` " . "\n"
             . "    SELECT sl2.id AS id, slq.questionId, slq.maxCommentSize " . "\n"
             . "    FROM `{$tbl_survey_line}` AS sl1, `{$tbl_survey_line}` AS sl2, "
             . "    `{$tbl_survey_line_question}` AS slq " . "\n"
             . "    WHERE sl1.surveyId = " . (int) $surveyId 
             . "    AND sl2.surveyId = " . (int) $newSurveyId
             . "    AND slq.id = sl1.id "
             . "    AND sl2.rank = sl1.rank";
        
        Claroline::getDatabase()->exec($sql);
        
        /*
         * Not sure about what this request is doing ?  Perform this one 
         * for a better understanding: 
         * 
         * SELECT sl1.id AS oldLineId, sl2.id AS newLineId, sl1.surveyId, sl1.rank, 
         * slq.id AS oldSlqId, slq.questionId, slq.maxCommentSize 
         * 
         * FROM `{$tbl_survey_line}` AS sl1, `{$tbl_survey_line}` AS sl2, 
         * `{$tbl_survey_line_question}` AS slq 
         * 
         * WHERE sl1.surveyId = {$surveyId} AND sl2.surveyId = {$newSurveyId} 
         * AND slq.id = sl1.id 
         * AND sl2.rank = sl1.rank;
         */
        
        // Duplicate survey separators
        $sql = "INSERT INTO `{$tbl_survey_line_separator}` " . "\n"
             . "    SELECT sl2.id AS id, sls.title, sls.description " . "\n"
             . "    FROM `{$tbl_survey_line}` AS sl1, `{$tbl_survey_line}` AS sl2, "
             . "    `{$tbl_survey_line_separator}` AS sls " . "\n"
             . "    WHERE sl1.surveyId = " . (int) $surveyId 
             . "    AND sl2.surveyId = " . (int) $newSurveyId
             . "    AND sls.id = sl1.id "
             . "    AND sl2.rank = sl1.rank";
        
        Claroline::getDatabase()->exec($sql);
        
        return $resultsArray;
    }
    
    
    /**
     * Manage inputs from the properties form.
     * 
     * @param   user input
     * @return  array of arrays of surveys to insert
     */
    public static function handlePropertiesEdition($userInput)
    {
        $sourceSurveyId = $userInput->get( 'surveyId' );
        $sourceSurvey = self::getSurvey($sourceSurveyId);
        
        $courseList = self::getCourseList();
        
        $newSurveyList = array();
        foreach ($courseList as $course)
        {
            $courseCode = $userInput->get( 'courseCode'.$course['courseCode'], null );
            
            if (!is_null($courseCode))
            {
                $newSurvey = array();
                $newSurvey['sourceSurveyId'] = $sourceSurveyId;
                $newSurvey['courseCode'] = $course['courseCode'];
                
                if (!is_null($userInput->get( 'useNewTitle'.$course['courseCode'] )))
                {
                    $newSurvey['title'] = $userInput->get( 'newTitle'.$course['courseCode'], null);
                }
                else
                {
                    $newSurvey['title'] = $sourceSurvey['title'];
                }
                
                $newSurveyList[] = $newSurvey;
            }
        }
        
        return $newSurveyList;
    }
    
    
    /**
     * Manage duplication of several surveys.
     * 
     * @param   array
     * @return  int number of surveys created/duplicated
     */
    public static function handleSurveyListDuplication( $surveyList )
    {
        $count = 0;
        
        foreach($surveyList as $survey)
        {
            if (preg_match("/%speaker%/i", $survey['title']))
            {
                $speakerList = self::getCourseSpeakersList($survey['courseCode']);
                
                foreach ($speakerList as $speaker)
                {
                    $fullName = $speaker['firstname'] . ' ' . $speaker['lastname'];
                    $title = preg_replace('/%speaker%/', $fullName, $survey['title']);
                    
                    self::duplicateSurvey($survey['sourceSurveyId'], $title, $survey['courseCode']);
                    //echo "self::duplicateSurvey({$survey['surveyId']}, {$newTitle}, {$survey['courseCode']})<br/>";
                }
            }
            else
            {
                self::duplicateSurvey($survey['sourceSurveyId'], $survey['title'], $survey['courseCode']);
                //echo "self::duplicateSurvey({$survey['surveyId']}, {$survey['newTitle']}, {$survey['courseCode']})<br/>";
            }
            
            $count++;
        }
        
        return $count;
    }
}