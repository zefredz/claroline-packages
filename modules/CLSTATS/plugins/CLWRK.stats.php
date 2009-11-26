<?php

class CLWRK_Stats extends ClaroStats_CourseTask
{
    public function getLabel()
    {
        return 'CLWRK';
    }
    
    public function getData($course)
    {
        $tables = $this->getCourseTables(array('wrk_assignment','wrk_submission'), $course);
        
        $resAssignments = Claroline::getDatabase()->query("SELECT COUNT(*) AS `value` FROM `{$tables['wrk_assignment']}`");
        
        $clwrk_count_assignments = $resAssignments->fetch();
        
        $resSubmissions = Claroline::getDatabase()->query("SELECT COUNT(*) AS `value` FROM `{$tables['wrk_submission']}`");
        
        $clwrk_count_submissions = $resSubmissions->fetch();
        
        return array(
            'clwrk_count_assignments' => $clwrk_count_assignments['value'],
            'clwrk_count_submissions' => $clwrk_count_submissions['value']
        );
    }
}