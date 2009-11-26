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
}