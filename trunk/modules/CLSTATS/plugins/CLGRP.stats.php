<?php

class CLGRP_Stats extends ClaroStats_CourseTask
{
    public function getLabel()
    {
        return 'CLGRP';
    }
    
    public function getData($course)
    {
        $tables = $this->getCourseTables(array('group_team'),$course);
        
        $res = Claroline::getDatabase()->query(
            "SELECT COUNT(*) AS clgrp_count_groups FROM `{$tables['group_team']}` WHERE 1"
        );
        
        return $res->fetch();
    }
}