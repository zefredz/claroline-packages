<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 2.1.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class to export a report into a csv file
 * @param string $delimiter
 */
class CsvReportView extends ReportView
{
    protected $report;
    protected $delimiter;
    
    
    /**
     * Constructor.
     * @param Report Object $report
     * @param char $delimitor
     * @param int $userId
     */
    public function  __construct ( $report , $userId , $is_admin = false , $delimiter = ',' )
    {
        $this->delimiter = $delimiter;
        parent::__construct( $report , $userId , $is_admin );
    }
    
    /**
     * Render the csv
     * @return string $csv
     */
    public function render()
    {
        
        $csv = get_lang( 'Student\'s name' );
        $reportDatas = $this->report->getDatas();
        
        foreach( $reportDatas[ 'items' ] as $item )
        {
            $csv .= $this->delimiter . $item[ 'title' ];
        }
        
        $csv .= $this->delimiter . get_lang( 'Average score' ) . "\n";
        
        $nameList = array();
        
        foreach( $reportDatas[ 'users' ] as $userId => $datas )
        {
            $name = $reportDatas[ 'users' ][ $userId ][ 'lastname' ]
                  . ' '
                  . $reportDatas[ 'users' ][ $userId ][ 'lastname' ];
            $nameList[ $name ] = $userId;
        }
        
        ksort( $nameList );
        
        $resultList = array();
        
        foreach( $reportDatas[ 'report' ] as $userId => $datas )
        {
            $resultList[ $userId ] = implode( $this->delimiter , $datas );
        }
        
        foreach( $nameList as $name => $userId )
        {
            $csv .= $name
                 .  $this->delimiter
                 .  $resultList[ $userId ]
                 .  $this->delimiter
                 .  $reportDatas[ 'users' ][ $userId ][ 'final_score' ]
                 .  "\n";
        }
        
        return $csv;
    }
    
    /**
     * Convert data array into csv string and send it to the user.
     *
     * @param string $filename
     * @return string $csv
     */
    public function export( $filename )
    {
        $csv = $this->render();
        
        header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
        header( "Content-Length: " . strlen( $csv ) );
        
        // Output to browser with appropriate mime type
        header( "Content-type: text/csv" );
        header( "Content-Disposition: attachment; filename=$filename" );
        
        echo $csv;
    }
}