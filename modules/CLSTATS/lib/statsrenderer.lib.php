<?php // $Id$

/**
 * Claroline Platform Stats
 *
 * @version     CLL10N 1.0-alpha $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLSTATS
 * @author      Dimitri Rambout <dim@claroline.net>
 */

class ClaroStatsRenderer{
    
    public static function view( $reports, $display, $report = null, $reportDate = null, $usageReport = null )
    {
        $tpl = new PhpTemplate( dirname(__FILE__) . '/../templates/index.tpl.php' );
        
        $cmdSubMenu[] = claro_html_cmd_link( 'index.php?cmd=view&display=summary&report=' . $reportDate, get_lang( 'Display summary' ) );
        $cmdSubMenu[] = claro_html_cmd_link( 'index.php?cmd=view&display=details&report=' . $reportDate, get_lang( 'Display details' ) );
        
        if( $display == 'summary' )
        {
            $summaryReport = array();
            $tmpReport = array();
            
            $plugins = ClaroStats::getPlugins();
            
            foreach( $report as $id => $itemReport )
            {
               $summaryReport[$itemReport['toolLabel']][$itemReport['itemName']] = $itemReport; 
            }
            
            foreach( $summaryReport as $toolLabel => $items )
            {
                require_once( $plugins[ $toolLabel ] );
                        
                $class = $toolLabel . '_Stats';
                $toolStats = new $class;
                unset( $summaryReport[ $toolLabel ] );
                if( method_exists( $toolStats, 'getSummarizedReport' ) )
                {
                    $summaryReport[ $toolLabel ][ 'summary' ] = $toolStats->getSummarizedReport( $items );
                }
                else
                {
                    /*$summaryReport[ $itemReport[ 'toolLabel' ] ]['toolLabel'] = $itemReport[ 'toolLabel' ];
                    $summaryReport[ $itemReport[ 'toolLabel' ] ]['lessFive'] = $itemReport['zero'] + $itemReport['one'] + $itemReport['two'] + $itemReport['three'] + $itemReport['four'];
                    $summaryReport[ $itemReport[ 'toolLabel' ] ]['moreFive'] = $itemReport['moreFive'] + $itemReport['five'];
                    if( ! isset( $summaryReport[ $itemReport[ 'toolLabel' ] ]['max'] ) )
                    {
                        $summaryReport[ $itemReport[ 'toolLabel' ] ]['max'] = $itemReport['max'];
                    }
                    else
                    {
                        if( ($itemReport['max'] > $summaryReport[ $itemReport[ 'toolLabel' ] ]['max']) )
                        {
                            $summaryReport[ $itemReport[ 'toolLabel' ] ]['max'] = $itemReport['max'];
                        }
                    }
                    
                    if( !isset( $summaryReport[ $itemReport[ 'toolLabel' ] ]['average'] ) )
                    {
                        $summaryReport[ $itemReport[ 'toolLabel' ] ]['average'] = $itemReport['average'];
                        
                    }
                    else
                    {
                        if( ($itemReport['average'] > $summaryReport[ $itemReport[ 'toolLabel' ] ]['average']) )
                        {
                            $summaryReport[ $itemReport[ 'toolLabel' ] ]['average'] = $itemReport['average'];
                        }
                    }*/
                }                
            }
            $report = $summaryReport;
        }
        
        $tpl->assign( 'subMenu' , claro_html_menu_horizontal( $cmdSubMenu ) );
        
        $tpl->assign( 'display', $display );
        $tpl->assign( 'reports' , $reports );
        $tpl->assign( 'report' , $report );
        $tpl->assign( 'usageReport' , $usageReport );
        $tpl->assign( 'reportDate', $reportDate );
        
        return $tpl->render();
    }
    
    public static function generateReport( $saveResult )
    {
        $tpl = new PhpTemplate( dirname( __FILE__ ) . '/../templates/generateReport.tpl.php' );
        
        $tpl->assign( 'saveResult' , $saveResult );
        
        return $tpl->render();
    }
    
    public static function generateStats( $reset, $bunchCourses )
    {
        $tpl = new PhpTemplate( dirname( __FILE__ ) . '/../templates/generateStats.tpl.php' );
        
        $tpl->assign( 'reset' , $reset ? 1 : 0 );
        
        $tpl->assign( 'bunchCourses', ( !is_null( $bunchCourses ) ? (int) $bunchCourses : 0 ) );
        
        return $tpl->render();
    }
    
    public static function pendingCourses( $pendingCourses )
    {
        $tpl = new PhpTemplate( dirname( __FILE__ ) . '/../templates/pendingCourses.tpl.php' );
        
        $tpl->assign( 'pendingCourses', $pendingCourses );
        
        return $tpl->render();
    }
    
    public static function bunchCourses( $pendingCourses )
    {
        $tpl = new PhpTemplate( dirname( __FILE__ ) . '/../templates/bunchCourses.tpl.php' );
        
        $tpl->assign( 'pendingCourses', $pendingCourses );
        
        return $tpl->render();
    }
}

?>