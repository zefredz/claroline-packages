<?php

class CLWIKI_Stats extends ClaroStats_CourseTask
{
    public function getLabel()
    {
        return 'CLWIKI';
    }
    
    public function getData($course)
    {
        $tables = $this->getCourseTables(array('wiki_properties'), $course);
        
        $resCourseWiki = Claroline::getDatabase()->query("SELECT COUNT(*) AS `value` FROM `{$tables['wiki_properties']}` WHERE group_id = 0");
        
        $clwiki_count_coursewiki = $resCourseWiki->fetch();
        
        $resGroupWiki = Claroline::getDatabase()->query("SELECT COUNT(*) AS `value` FROM `{$tables['wiki_properties']}` WHERE group_id != 0");
        
        $clwiki_count_groupwiki = $resGroupWiki->fetch();
        
        return array(
            'clwiki_count_coursewiki' => $clwiki_count_coursewiki['value'],
            'clwiki_count_groupwiki' => $clwiki_count_groupwiki['value']
        );
    }
}