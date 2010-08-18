<?php

class CLWRK_Stats extends ClaroStats_CourseTask
{
    public function getLabel()
    {
        return 'CLWRK';
    }
    
    public function getData($course)
    {
        $tables = $this->getCourseTables(array('wrk_assignment','wrk_submission'), $course);
        
        $resAssignments = Claroline::getDatabase()->query("SELECT COUNT(*) AS `value` FROM `{$tables['wrk_assignment']}`");
        
        $clwrk_count_assignments = $resAssignments->fetch();
        
        $resSubmissions = Claroline::getDatabase()->query("SELECT COUNT(*) AS `value` FROM `{$tables['wrk_submission']}`");
        
        $clwrk_count_submissions = $resSubmissions->fetch();
        
        return array(
            'clwrk_count_assignments' => $clwrk_count_assignments['value'],
            'clwrk_count_submissions' => $clwrk_count_submissions['value']
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
        
        return $itemStats[ 'clwrk_count_assignments' ]['value'];
    }
    
    public function getSummarizedReport( $items )
    {
        if(isset( $items['clwrk_count_assignments' ] ) )
        {
            $items['clwrk_count_assignments']['lessFive'] = $items['clwrk_count_assignments']['zero']
                                                            + $items['clwrk_count_assignments']['one']
                                                            + $items['clwrk_count_assignments']['two']
                                                            + $items['clwrk_count_assignments']['three']
                                                            + $items['clwrk_count_assignments']['four'];
            $items['clwrk_count_assignments']['moreFive'] += $items['clwrk_count_assignments']['five'];
            return $items['clwrk_count_assignments' ];
        }
        else
        {
            return null;
        }
    }
}