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
    
    public function getReportData( &$report, $itemStats, $nbCourses = 0 )
    {
        foreach( $itemStats as $itemName => $item )
        {
            parent::initReportData( $report, $itemName, $item );
            parent::setReportData( $report, $itemName, $item );
            parent::setReportMax( $report, $itemName, $item );
            //parent::setReportAverage( $report, $itemName, $item, $nbCourses );            
        }
        
        return $itemStats[ 'cldsc_count_items' ]['value'];
    }
    
    public function getSummarizedReport( $items )
    {
        if(isset( $items['cldsc_count_items' ] ) )
        {
            $items['cldsc_count_items']['lessFive'] = $items['cldsc_count_items']['zero']
                                                            + $items['cldsc_count_items']['one']
                                                            + $items['cldsc_count_items']['two']
                                                            + $items['cldsc_count_items']['three']
                                                            + $items['cldsc_count_items']['four'];
            $items['cldsc_count_items']['moreFive'] += $items['cldsc_count_items']['five'];
            return $items['cldsc_count_items' ];
        }
        else
        {
            return null;
        }
    }
}