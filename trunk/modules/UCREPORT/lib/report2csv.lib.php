<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 0.9.6 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

FromKernel::uses( 'csv.class' );

class Report2Csv extends csv
{
    public function loadDataList( $report )
    {
        try
        {
            $reportDataList = $report->getReportDataList();
            $userList = $report->getUserList();
            $assignmentDataList = $report->getAssignmentDataList();
            $averageScore = $report->getAverageScore();
            
            $assignmentTitleList = array();
            $weight = array();
            $average = array();
            
            foreach( $assignmentDataList as $assignment )
            {
                if ( $assignment[ 'active' ] )
                {
                    $title[] = $assignment[ 'title' ];
                    $weight[] = (string)(100 * $assignment[ 'proportional_weight' ] ) . ' %';
                    $average[] = isset( $assignment[ 'average' ] )
                                ? $assignment[ 'average' ]
                                : '';
                }
            }
            
            $this->recordList[] = array_merge( array( get_lang( 'Name' ) )
                                              , $title
                                              , array( get_lang( 'Weighted global score' ) ) );
            
            $this->recordList[] = array_merge( array( get_lang( 'Weight' ) )
                                                , $weight
                                                , array( '100 %' ) );
            
            $this->recordList[] = array_merge( array( get_lang( 'Average' ) )
                                              , $average
                                              , array( $averageScore ) );
            
            foreach( $reportDataList as $userId  => $reportData )
            {
                $userName = array( $userList[ $userId ][ 'firstname' ] . ' ' . $userList[ $userId ][ 'lastname' ] );
                
                $userScoreList = array();
                
                foreach( $assignmentDataList as $id => $assignment )
                {
                    if ( $assignment[ 'active' ] )
                    {
                        if ( isset( $reportData[ $id ] ) )
                        {
                            $userScoreList[] = $reportData[ $id ];
                        }
                        else
                        {
                            $userScoreList[] = '';
                        }
                    }
                }
                
                $userScoreList[] = isset( $userList[ $userId ][ 'final_score' ] )
                                 ? (string)$userList[ $userId ][ 'final_score' ]
                                 : '';
                
                $this->recordList[] = array_merge( $userName , $userScoreList );
            }
        }
        catch ( Exception $e ) // exceptions handling
        {
            if ( claro_debug_mode() )
            {
                $dialogBox->error( '<pre>' . $e->__toString() . '</pre>' );
            }
            else
            {
                $dialogBox->error( $e->getMessage() );
            }
        }
    }
}