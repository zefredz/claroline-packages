<?php // $Id$
/**
 * Student Monitoring Tool
 *
 * @version     ICMONIT 1.0.0 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICMONIT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * User report for the desktop connector
 * @property int $userId the user's id
 * @property array $userReportList the report list
 */
class UserReport
{
    private $userId;
    private $userReportList;
    
    /**
     * Constructor
     * @param int $userId
     */
    public function __construct( $userId )
    {
        $this->userId = $userId;
        
        $this->tbl = get_module_main_tbl( array( 'cours' ) );
        $this->load();
    }
    
    /**
     * Retrieves the report datas
     * This method is called by the constructor
     */
    private function load()
    {
        $userCourseList = claro_get_user_course_list();
        
        foreach( $userCourseList as $course )
        {
            $tbl = get_module_course_tbl( array( 'ICMONIT_report' ) , $course[ 'sysCode' ] );
            
            $tblExists = Claroline::getDatabase()->query( "
                SHOW TABLES LIKE " . Claroline::getDatabase()->quote( $tbl['ICMONIT_report'] ) )->numRows();
            
            if ( $tblExists )
            {
                $courseReportList = Claroline::getDatabase()->query( "
                        SELECT
                            id, title, publication_date, datas
                        FROM
                            `{$tbl['ICMONIT_report']}`
                        WHERE
                            visibility = " . Claroline::getDatabase()->quote( AssetList::VISIBLE ) . "
                        ORDER BY
                            publication_date ASC"
                );
                
                foreach( $courseReportList as $courseReport )
                {
                    $reportDataList = unserialize( $courseReport[ 'datas' ] );
                    if ( isset( $reportDataList[ 'report' ][ $this->userId ] ) )
                    {
                        $finalScore = isset( $reportDataList[ 'users' ][ $this->userId ][ 'final_score' ] )
                                    ? $reportDataList[ 'users' ][ $this->userId ][ 'final_score' ]
                                    : null;
                        
                        $this->userReportList[ $courseReport[ 'id' ] ] = array( 'title' => $courseReport[ 'title' ]
                                                                        , 'course_code' => $course[ 'sysCode' ]
                                                                        , 'course_title' => $course[ 'title' ]
                                                                        , 'final_score' => $finalScore
                                                                        , 'date' => $courseReport[ 'publication_date' ]
                        );
                    }
                }
            }
        }
    }
    
    /**
     * Common getter for $userReportList
     */
    public function getUserReportList()
    {
        return $this->userReportList;
    }
}
