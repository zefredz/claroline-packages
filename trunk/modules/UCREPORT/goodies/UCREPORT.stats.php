<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 2.4.3 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class UCREPORT_Stats extends ClaroStats_CourseTask
{
    public function getLabel()
    {
        return 'UCREPORT';
    }
    
    public function getData($course)
    {
        $tables = $this->getCourseTables(array('report_reports'),$course);
        
        $res = Claroline::getDatabase()->query(
            "SELECT COUNT(*) AS clann_count_reports FROM `{$tables['report_reports']}` WHERE 1"
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
        
        return $itemStats[ 'clann_count_reports' ]['value'];
    }
    
    public function getSummarizedReport( $items )
    {
        if(isset( $items['clann_count_reports' ] ) )
        {
            $items['clann_count_reports']['lessFive'] = $items['clann_count_reports']['zero']
                                                            + $items['clann_count_reports']['one']
                                                            + $items['clann_count_reports']['two']
                                                            + $items['clann_count_reports']['three']
                                                            + $items['clann_count_reports']['four'];
            $items['clann_count_reports']['moreFive'] += $items['clann_count_reports']['five'];
            return $items['clann_count_reports' ];
        }
        else
        {
            return null;
        }
    }
}