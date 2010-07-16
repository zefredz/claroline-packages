<?php // $Id$

/**
 * Claroline surveys duplication tool
 *
 * @version     1.0 $Revision$
 * @copyright   (c) 2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     EMSURVEY
 * @author      Antonin Bourguignon <antonin.bourguignon@claroline.net>
 *
 */

class ModuleRenderer {
    
    /**
     * Display the survey list, grouped by course code.
     * 
     * @param   $list   array of surveys ordered by course code
     */
    public static function surveyList( $surveyList )
    {
        // Before rendering, group the surveys by course
        $tempCourseId = '';
        $groupedList = array();
        
        // Browse the array a first time to isolate the distinct courses
        foreach ($surveyList as $survey)
        {
            // New course ?  Then new entry in the $groupedList array
            if ($tempCourseId != $survey['courseId'])
            {
                $tempCourseId = $survey['courseId'];
                $groupedList[$survey['courseCode']] = array(
                    'course' => array(
                        'courseId' => $survey['courseId'],
                        'courseCode' => $survey['courseCode'],
                        'officialCode' => $survey['officialCode'],
                        'intitule' => $survey['intitule']
                    ),
                    'surveys' => array()
                );
            }
        }
        
        // Browse the array a second time to classify surveys into courses
        foreach ($surveyList as $survey)
        {
            $groupedList[$survey['courseCode']]['surveys'][] = array(
                'surveyId' => $survey['surveyId'],
                'title' => $survey['title'],
                'description' => $survey['description'],
                'rank' => $survey['rank']
            );
        }
        
        $tpl = new PhpTemplate( dirname(__FILE__) . '/../templates/surveyList.tpl.php' );
        
        $tpl->assign('surveyList', $groupedList);
        
        return $tpl->render();
    }
    
    
    /**
     * Display the course list, ordered by title. 
     * 
     * @param   $survey     array
     * @param   $courseList array of courses ordered by title
     */
    public static function courseList( $selectedSurvey, $courseList )
    {
        $tpl = new PhpTemplate( dirname(__FILE__) . '/../templates/courseList.tpl.php' );
        
        $tpl->assign('selectedSurvey', $selectedSurvey);
        $tpl->assign('courseList', $courseList);
        
        return $tpl->render();
    }
    
    
    /**
     * Display the form to edit surveys properties. 
     * 
     * @param   $survey             array
     * @param   $selectedCourseList array of courses ordered by title
     */
    public static function editProperties( $selectedSurvey, $selectedCourseList )
    {
        $tpl = new PhpTemplate( dirname(__FILE__) . '/../templates/editProperties.tpl.php' );
        
        $tpl->assign('selectedSurvey', $selectedSurvey);
        $tpl->assign('selectedCourseList', $selectedCourseList);
        
        return $tpl->render();
    }
}