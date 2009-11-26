<?php

class CLCAL_Stats extends ClaroStats_CourseTask
{
    public function getLabel()
    {
        return 'CLCAL';
    }
    
    public function getData($course)
    {
        $tables = $this->getCourseTables(array('calendar_event'),$course);
        
        $res = Claroline::getDatabase()->query(
            "SELECT COUNT(*) AS clann_count_events FROM `{$tables['calendar_event']}` WHERE 1"
        );
        
        return $res->fetch();
    }
}