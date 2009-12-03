<?php

class CLQWZ_Stats extends ClaroStats_CourseTask
{
    public function getLabel()
    {
        return 'CLQWZ';
    }
    
    public function getData($course)
    {
        $tables = $this->getCourseTables(array('qwz_question','qwz_exercise'), $course);
        
        $resQuestions = Claroline::getDatabase()->query("SELECT COUNT(*) AS `value` FROM `{$tables['qwz_question']}` WHERE id != 1");
        
        $clqwz_count_questions = $resQuestions->fetch();
        
        $resExercises = Claroline::getDatabase()->query("SELECT COUNT(*) AS `value` FROM `{$tables['qwz_exercise']}` WHERE id != 1");
        
        $clqwz_count_exercises = $resExercises->fetch();
        
        return array(
            'clqwz_count_questions' => $clqwz_count_questions['value'],
            'clqwz_count_exercises' => $clqwz_count_exercises['value']
        );
    }
    
    public function getReportData( &$report, $itemStats, $nbCourses = 0 )
    {
        foreach( $itemStats as $itemName => $item )
        {
            parent::initReportData( $report, $itemName, $item );
            parent::setReportData( $report, $itemName, $item );
            parent::setReportMax( $report, $itemName, $item );
            parent::setReportAverage( $report, $itemName, $item, $nbCourses );            
        }
        
        return $itemStats[ 'clqwz_count_exercises' ]['value'];
    }
    
    public function getSummarizedReport( $items )
    {
        if(isset( $items['clqwz_count_exercises' ] ) )
        {
            $items['clqwz_count_exercises']['lessFive'] = $items['clqwz_count_exercises']['zero']
                                                            + $items['clqwz_count_exercises']['one']
                                                            + $items['clqwz_count_exercises']['two']
                                                            + $items['clqwz_count_exercises']['three']
                                                            + $items['clqwz_count_exercises']['four'];
            $items['clqwz_count_exercises']['moreFive'] += $items['clqwz_count_exercises']['five'];
            return $items['clqwz_count_exercises' ];
        }
        else
        {
            return null;
        }
    }
}