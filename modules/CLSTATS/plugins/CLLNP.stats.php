<?php

class CLLNP_Stats extends ClaroStats_CourseTask
{
    public function getLabel()
    {
        return 'CLLNP';
    }
    
    public function getData($course)
    {
        $tables = $this->getCourseTables(array('lp_learnPath','lp_asset','lp_module'),$course);
        
        $res = Claroline::getDatabase()->query(
            "SELECT COUNT(*) AS `value` FROM `{$tables['lp_learnPath']}` WHERE learnPath_id != 1"
        );
        
        $cllp_count_learningpaths = $res->fetch();
        
        $res = Claroline::getDatabase()->query(
            "SELECT COUNT(*) AS `value` FROM `{$tables['lp_module']}` WHERE module_id > 2"
        );
        
        $cllp_count_modules = $res->fetch();
        
        $res = Claroline::getDatabase()->query(
            "SELECT COUNT(*) AS `value` FROM `{$tables['lp_asset']}` WHERE asset_id > 2"
        );
        
        $cllp_count_assets = $res->fetch();
        
        return array(
            'cllp_count_learningpaths' => $cllp_count_learningpaths['value'],
            'cllp_count_modules' => $cllp_count_modules['value'],
            'cllp_count_assets' => $cllp_count_assets['value']
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
        
        return $itemStats[ 'cllp_count_learningpaths' ]['value'];
    }
    
    public function getSummarizedReport( $items )
    {
        if(isset( $items['cllp_count_learningpaths' ] ) )
        {
            $items['cllp_count_learningpaths']['lessFive'] = $items['cllp_count_learningpaths']['zero']
                                                            + $items['cllp_count_learningpaths']['one']
                                                            + $items['cllp_count_learningpaths']['two']
                                                            + $items['cllp_count_learningpaths']['three']
                                                            + $items['cllp_count_learningpaths']['four'];
            $items['cllp_count_learningpaths']['moreFive'] += $items['cllp_count_learningpaths']['five'];
            return $items['cllp_count_learningpaths' ];
        }
        else
        {
            return null;
        }
    }
}