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
    
    public function getReportData( &$report, $itemStats, $nbCourses = 0 )
    {
        foreach( $itemStats as $itemName => $item )
        {
            parent::initReportData( $report, $itemName, $item );
            parent::setReportData( $report, $itemName, $item );
            parent::setReportMax( $report, $itemName, $item );
            //parent::setReportAverage( $report, $itemName, $item, $nbCourses );            
        }
        
        return $itemStats[ 'clann_count_events' ]['value'];
    }
    
    public function getSummarizedReport( $items )
    {
        if(isset( $items['clann_count_events' ] ) )
        {
            $items['clann_count_events']['lessFive'] = $items['clann_count_events']['zero']
                                                            + $items['clann_count_events']['one']
                                                            + $items['clann_count_events']['two']
                                                            + $items['clann_count_events']['three']
                                                            + $items['clann_count_events']['four'];
            $items['clann_count_events']['moreFive'] += $items['clann_count_events']['five'];
            return $items['clann_count_events' ];
        }
        else
        {
            return null;
        }
    }
}