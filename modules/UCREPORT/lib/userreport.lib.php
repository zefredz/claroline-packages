<?php // $Id$
/**
 * Claroline Poll Tool
 *
 * @version     UCREPORT 0.9.4 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
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
        
        $this->tbl = get_module_main_tbl( array( 'cours' , 'cours_user' ) );
        $this->load();
    }
    
    /**
     * Retrieves the report datas
     * This method is called by the constructor
     */
    private function load()
    {
        $userCourseList = Claroline::getDatabase()->query( "
            SELECT
                C.code, C.intitule AS title
            FROM
                `{$this->tbl['cours']}`          AS C
            INNER JOIN
                `{$this->tbl['cours_user']}`    AS U
            ON
                C.code = U.code_cours
            WHERE
                U.user_id = " . Claroline::getDatabase()->escape( $this->userId )
        );
        
        foreach( $userCourseList as $course )
        {
            $tbl = get_module_course_tbl( array( 'report_reports' ) , $course[ 'code' ] );
            $weightFileUrl = '../../courses/' . claro_get_course_path( $course[ 'code' ] ) . '/' . Report::ASSIGNMENT_DATA_FILE;
            
            if ( claro_is_tool_activated( get_tool_id_from_module_label( 'UCREPORT' ) , $course[ 'code' ] )
                 && file_exists( $weightFileUrl ) )
            {
                $courseReportList = Claroline::getDatabase()->query( "
                        SELECT
                            id, title, publication_date, datas
                        FROM
                            `{$tbl['report_reports']}`
                        WHERE
                            visibility = " . Claroline::getDatabase()->quote( Report::VISIBLE ) . "
                        ORDER BY
                            publication_date ASC"
                );
                
                foreach( $courseReportList as $courseReport )
                {
                    $reportDataList = unserialize( $courseReport[ 'datas' ] );
                    if ( isset( $reportDataList[ 'users' ][ $this->userId ][ 'active' ] ) )
                    {
                        $finalScore = isset( $reportDataList[ 'users' ][ $this->userId ][ 'final_score' ] )
                                    ? $reportDataList[ 'users' ][ $this->userId ][ 'final_score' ]
                                    : null;
                        
                        $this->userReportList[ $courseReport[ 'id' ] ] = array( 'title' => $courseReport[ 'title' ]
                                                                        , 'course_code' => $course[ 'code' ]
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