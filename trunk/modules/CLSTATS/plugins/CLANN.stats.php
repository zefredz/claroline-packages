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
    
    public function getReportData( &$report, $itemStats, $nbCourses = 0 )
    {
        foreach( $itemStats as $itemName => $item )
        {
            parent::initReportData( $report, $itemName, $item );
            parent::setReportData( $report, $itemName, $item );
            parent::setReportMax( $report, $itemName, $item );
            //parent::setReportAverage( $report, $itemName, $item, $nbCourses );            
        }
        
        return $itemStats[ 'clann_count_announcements' ]['value'];
        
    }
    
    public function getSummarizedReport( $items )
    {
        if(isset( $items['clann_count_announcements' ] ) )
        {
            $items['clann_count_announcements']['lessFive'] = $items['clann_count_announcements']['zero']
                                                            + $items['clann_count_announcements']['one']
                                                            + $items['clann_count_announcements']['two']
                                                            + $items['clann_count_announcements']['three']
                                                            + $items['clann_count_announcements']['four'];
            $items['clann_count_announcements']['moreFive'] += $items['clann_count_announcements']['five'];
            return $items['clann_count_announcements' ];
        }
        else
        {
            return null;
        }
    }
}