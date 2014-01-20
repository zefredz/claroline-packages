<?php // $Id$
/**
 * Student Monitoring Tool
 *
 * @version     ICMONIT 1.0.0 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICMONIT
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
        $reportDatas = $this->report->export();
        
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
                  . $reportDatas[ 'users' ][ $userId ][ 'firstname' ];
            
            $userResult = array();
            
            foreach( array_keys( $reportDatas[ 'items' ] ) as $itemId )
            {
                $userResult[] = array_key_exists( $userId , $reportDatas[ 'report' ] )
                                && array_key_exists( $itemId , $reportDatas[ 'report' ][ $userId ] )
                                ? $reportDatas[ 'report' ][ $userId ][ $itemId ] : '';
            }
            
            $csv .= $name . $this->delimiter . implode( $userResult , $this->delimiter ) . "\n";
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
