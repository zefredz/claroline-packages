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
            'clwiki_count_groupwiki' => $clwiki_count_groupwiki['value'],
            'clwiki_count_totalwiki' => $clwiki_count_coursewiki['value'] + $clwiki_count_groupwiki['value']
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
        
        return $report[ 'clwiki_count_totalwiki' ]['value'];
    }
    
    public function getSummarizedReport( $items )
    {
        if(isset( $items['clwiki_count_totalwiki' ] ) )
        {
            $items['clwiki_count_totalwiki']['lessFive'] = $items['clwiki_count_totalwiki']['zero']
                                                            + $items['clwiki_count_totalwiki']['one']
                                                            + $items['clwiki_count_totalwiki']['two']
                                                            + $items['clwiki_count_totalwiki']['three']
                                                            + $items['clwiki_count_totalwiki']['four'];
            $items['clwiki_count_totalwiki']['moreFive'] += $items['clwiki_count_totalwiki']['five'];
            return $items['clwiki_count_totalwiki' ];
        }
        else
        {
            return null;
        }
    }
}