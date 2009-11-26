<?php

class CLANN_Stats extends ClaroStats_CourseTask
{
    public function getLabel()
    {
        return 'CLANN';
    }
    
    public function getData($course)
    {
        $tables = $this->getCourseTables(array('announcement'),$course);
        
        $res = Claroline::getDatabase()->query(
            "SELECT COUNT(*) AS clann_count_announcements FROM `{$tables['announcement']}` WHERE 1"
        );
        
        return $res->fetch();
    }
}