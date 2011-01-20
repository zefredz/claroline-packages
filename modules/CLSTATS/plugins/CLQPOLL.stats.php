<?php

class CLQPOLL_Stats extends ClaroStats_CourseTask
{
    public function getLabel()
    {
        return 'CLQPOLL';
    }
    
    public function getData($course)
    {
        $tables = $this->getCourseTables(array('poll_polls','poll_choices','poll_votes'), $course);
        
        $resPolls = Claroline::getDatabase()->query("SELECT COUNT(*) AS `value` FROM `{$tables['poll_polls']}`");
        
        $clfrm_count_polls = $resPolls->fetch();
        
        $resVotes = Claroline::getDatabase()->query("SELECT COUNT(DISTINCT `V`.`user_id`) AS `value` FROM `{$tables['poll_votes']}` AS `V`
                                                    LEFT JOIN `{$tables['poll_polls']}` AS `P`
                                                    ON `P`.`id` = `V`.`poll_id`");
        
        $clfrm_count_votes = $resVotes->fetch();
        
        return array(
            'clfrm_count_polls' => $clfrm_count_polls['value'],
            'clfrm_count_votes' => $clfrm_count_votes['value']
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
        
        return $itemStats[ 'clfrm_count_polls' ]['value'] != 0
               ? round( $itemStats[ 'clfrm_count_votes' ]['value'] / $itemStats[ 'clfrm_count_polls' ]['value'] )
               : 0;
    }
    
    public function getSummarizedReport( $items )
    {
        if(isset( $items['clfrm_count_polls' ] ) )
        {
            $items['clfrm_count_polls']['lessFive'] = $items['clfrm_count_polls']['zero']
                                                            + $items['clfrm_count_polls']['one']
                                                            + $items['clfrm_count_polls']['two']
                                                            + $items['clfrm_count_polls']['three']
                                                            + $items['clfrm_count_polls']['four'];
            $items['clfrm_count_polls']['moreFive'] += $items['clfrm_count_polls']['five'];
            return $items['clfrm_count_polls' ];
        }
        else
        {
            return null;
        }
    }
}