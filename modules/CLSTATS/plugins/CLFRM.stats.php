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
    
    public function getReportData( &$report, $itemStats, $nbCourses = 0 )
    {
        foreach( $itemStats as $itemName => $item )
        {
            parent::initReportData( $report, $itemName, $item );
            parent::setReportData( $report, $itemName, $item );
            parent::setReportMax( $report, $itemName, $item );
            //parent::setReportAverage( $report, $itemName, $item, $nbCourses );
        }
        
        return $itemStats[ 'clfrm_count_topics' ]['value'] != 0
               ? round( $itemStats[ 'clfrm_count_posts' ]['value'] / $itemStats[ 'clfrm_count_topics' ]['value'] )
               : 0;
    }
    
    public function getSummarizedReport( $items )
    {
        if(isset( $items['clfrm_count_posts' ] ) )
        {
            $items['clfrm_count_posts']['lessFive'] = $items['clfrm_count_posts']['zero']
                                                            + $items['clfrm_count_posts']['one']
                                                            + $items['clfrm_count_posts']['two']
                                                            + $items['clfrm_count_posts']['three']
                                                            + $items['clfrm_count_posts']['four'];
            $items['clfrm_count_posts']['moreFive'] += $items['clfrm_count_posts']['five'];
            return $items['clfrm_count_posts' ];
        }
        else
        {
            return null;
        }
    }
}