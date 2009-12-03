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
    
    public function getReportData( &$report, $itemStats, $nbCourses = 0 )
    {
        foreach( $itemStats as $itemName => $item )
        {
            parent::initReportData( $report, $itemName, $item );
            parent::setReportData( $report, $itemName, $item );            
            
            parent::setReportMax( $report, $itemName, $item );
            parent::setReportAverage( $report, $itemName, $item, $nbCourses );            
        }        
        
        $report[ 'clwiki_count_totalwiki' ][ 'zero' ] = $report[ 'clwiki_count_coursewiki' ][ 'zero' ] + $report[ 'clwiki_count_groupwiki' ][ 'zero' ];
        $report[ 'clwiki_count_totalwiki' ][ 'one' ] = $report[ 'clwiki_count_coursewiki' ][ 'one' ] + $report[ 'clwiki_count_groupwiki' ][ 'one' ];
        $report[ 'clwiki_count_totalwiki' ][ 'two' ] = $report[ 'clwiki_count_coursewiki' ][ 'two' ] + $report[ 'clwiki_count_groupwiki' ][ 'two' ];
        $report[ 'clwiki_count_totalwiki' ][ 'three' ] = $report[ 'clwiki_count_coursewiki' ][ 'three' ] + $report[ 'clwiki_count_groupwiki' ][ 'three' ];
        $report[ 'clwiki_count_totalwiki' ][ 'four' ] = $report[ 'clwiki_count_coursewiki' ][ 'four' ] + $report[ 'clwiki_count_groupwiki' ][ 'four' ];
        $report[ 'clwiki_count_totalwiki' ][ 'five' ] = $report[ 'clwiki_count_coursewiki' ][ 'five' ] + $report[ 'clwiki_count_groupwiki' ][ 'five' ];
        $report[ 'clwiki_count_totalwiki' ][ 'moreFive' ] = $report[ 'clwiki_count_coursewiki' ][ 'moreFive' ] + $report[ 'clwiki_count_groupwiki' ][ 'moreFive' ];
        $report[ 'clwiki_count_totalwiki' ][ 'value' ] = $report[ 'clwiki_count_coursewiki' ][ 'value' ] + $report[ 'clwiki_count_groupwiki' ][ 'value' ];
        
        parent::setReportMax( $report, 'clwiki_count_totalwiki', $report[ 'clwiki_count_totalwiki'] );
        parent::setReportAverage( $report, 'clwiki_count_totalwiki', $report[ 'clwiki_count_totalwiki'], $nbCourses );
        
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