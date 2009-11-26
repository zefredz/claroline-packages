<?php

class CLFRM_Stats extends ClaroStats_CourseTask
{
    public function getLabel()
    {
        return 'CLFRM';
    }
    
    public function getData($course)
    {
        $tables = $this->getCourseTables(array('bb_posts','bb_topics','bb_forums'), $course);
        
        $resForums = Claroline::getDatabase()->query("SELECT COUNT(*) AS `value` FROM `{$tables['bb_forums']}`");
        
        $clfrm_count_forums = $resForums->fetch();
        
        $resTopics = Claroline::getDatabase()->query("SELECT COUNT(*) AS `value` FROM `{$tables['bb_topics']}`");
        
        $clfrm_count_topics = $resTopics->fetch();
        
        $resPosts = Claroline::getDatabase()->query("SELECT COUNT(*) AS `value` FROM `{$tables['bb_posts']}`");
        
        $clfrm_count_posts = $resPosts->fetch();
        
        return array(
            'clfrm_count_forums' => $clfrm_count_forums['value'],
            'clfrm_count_topics' => $clfrm_count_topics['value'],
            'clfrm_count_posts' =>$clfrm_count_posts['value']
        );
    }
}