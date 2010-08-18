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
    
    public function getReportData( &$report, $itemStats, $nbCourses = 0 )
    {
        foreach( $itemStats as $itemName => $item )
        {
            parent::initReportData( $report, $itemName, $item );
            parent::setReportData( $report, $itemName, $item );
            parent::setReportMax( $report, $itemName, $item );
            //parent::setReportAverage( $report, $itemName, $item, $nbCourses );            
        }
        
        return $itemStats[ 'clgrp_count_groups' ]['value'];
    }
    
    public function getSummarizedReport( $items )
    {
        if(isset( $items['clgrp_count_groups' ] ) )
        {
            $items['clgrp_count_groups']['lessFive'] = $items['clgrp_count_groups']['zero']
                                                            + $items['clgrp_count_groups']['one']
                                                            + $items['clgrp_count_groups']['two']
                                                            + $items['clgrp_count_groups']['three']
                                                            + $items['clgrp_count_groups']['four'];
            $items['clgrp_count_groups']['moreFive'] += $items['clgrp_count_groups']['five'];
            return $items['clgrp_count_groups' ];
        }
        else
        {
            return null;
        }
    }
}