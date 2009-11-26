<?php

class CLDSC_Stats extends ClaroStats_CourseTask
{
    public function getLabel()
    {
        return 'CLDSC';
    }
    
    public function getData($course)
    {
        $tables = $this->getCourseTables(array('course_description'),$course);
        
        $res = Claroline::getDatabase()->query(
            "SELECT COUNT(*) AS cldsc_count_items FROM `{$tables['course_description']}` WHERE 1"
        );
        
        return $res->fetch();
    }
}