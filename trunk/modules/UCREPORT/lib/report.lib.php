<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 1.0.5 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * Retrieves the students' scores for all the assignments, adds weights,
 * calculates average scores and students'final (weighted) results,
 * and generates a report.
 * @const DEFAULT_ASSIGNMENT_WEIGHT the default weight for an assignment
 * @const DEFAULT_MAX_SCORE the default maximum score for an assignment
 * @const ASSIGNMENT_DATA_FILE the name of the file where assignments weights are stored
 * @const EXAMINATION_DATA_FILE the name of the file where examination scores are stored
 * @const ACTIVE_USER_DATA_FILE the name of the file where active users are listed
 * @const EXAMINATION_ID the examination id in $reportDataList
 * @const VISIBLE database value for visible assignments
 * @const INVISIBLE database value for invisible assignments
 * @property array $userList the list of course's users who posted works
 * @property array $assignmentDataList the visible assignment list with some datas (weight, average,...)
 * @property array $reportDataList the students' scores for each assignment
 * @property string $weightFileUrl the Url of the file which stores the assignements weights
 * @property int $averageScore the global weighted average score
 */
class Report
{
    const DEFAULT_ASSIGNMENT_WEIGHT = 100;
    const DEFAULT_MAX_SCORE = 100;
    const ASSIGNMENT_DATA_FILE = 'assignments.data';
    const EXAMINATION_DATA_FILE = 'examination.data';
    const ACTIVE_USER_DATA_FILE = 'active_users.data';
    const EXAMINATION_ID = 'exam';
    const VISIBLE = 'VISIBLE';
    const INVISIBLE = 'INVISIBLE';
    
    private $userList;
    private $reportDataList;
    private $assignmentDataList;
    
    private $id;
    private $courseId;
    private $title;
    private $date;
    private $visibility;
    private $assignmentListFileUrl;
    private $examinationFileUrl;
    private $activeUserListFileUrl;
    private $averageScore;
    
    /**
     * Constructor
     * If it receives a report id, it loads the datas stored in database.
     * If not, it retrieves the 'realtime' datas from the assignments
     * @param string $courseId
     * @param int $reportId the id of the report
     */
    public function __construct( $courseId = false , $reportId = false )
    {
        $this->courseId = $courseId ? $courseId : claro_get_current_course_id();
        
        $this->tbl_names = get_module_main_tbl( array( 'user'
                                                     , 'cours_user' ) );
        $this->tbl = get_module_course_tbl ( array ( 'wrk_assignment'
                                                   , 'wrk_submission'
                                                   , 'group_team'
                                                   , 'group_rel_team_user'
                                                   , 'report_reports' ) , $courseId );
        
        if ( ! $reportId || ! $this->loadReport( $reportId ) )
        {
            $this->assignmentListFileUrl = '../../courses/'
                                         . claro_get_course_path( $this->courseId )
                                         . '/' . self::ASSIGNMENT_DATA_FILE;
            $this->examinationFileUrl = '../../courses/'
                                      . claro_get_course_path( $this->courseId )
                                      . '/' . self::EXAMINATION_DATA_FILE;
            $this->activeUserListFileUrl = '../../courses/'
                                      . claro_get_course_path( $this->courseId )
                                      . '/' . self::ACTIVE_USER_DATA_FILE;
            $this->load();
        }
    }
    
    /**
     * Retrieves the 'realtime' data from assignments database.
     * This method is called by the constructor if it doesn't receive a report id
     */
    public function load()
    {
        $this->assignmentDataList = array();
        $this->userList = array();
        $this->reportDataList = array();
        
        if ( ! isset( $this->assignmentQueryResut ) )
        {
            $this->assignmentQueryResult = Claroline::getDatabase()->query( "
                SELECT
                    id, title, visibility
                FROM
                    `{$this->tbl['wrk_assignment']}`" );
        }
        
        foreach( $this->assignmentQueryResult as $line )
        {
            $is_visible = $line[ 'visibility' ] == self::VISIBLE;
            $this->assignmentDataList[ $line[ 'id' ] ][ 'title' ] = $line[ 'title' ];
            $this->assignmentDataList[ $line[ 'id' ] ][ 'active' ] = $is_visible;
            $this->assignmentDataList[ $line[ 'id' ] ][ 'weight' ] = self::DEFAULT_ASSIGNMENT_WEIGHT;
            $this->assignmentDataList[ $line[ 'id' ] ][ 'submission_count' ] = 0;
            $this->assignmentDataList[ $line[ 'id' ] ][ 'average' ] = 0;
        }
        
        $this->assignmentDataList[ self::EXAMINATION_ID ][ 'title' ] = get_lang( 'Examination' );
        $this->assignmentDataList[ self::EXAMINATION_ID ][ 'active' ] = false;
        $this->assignmentDataList[ self::EXAMINATION_ID ][ 'weight' ] = self::DEFAULT_ASSIGNMENT_WEIGHT;
        $this->assignmentDataList[ self::EXAMINATION_ID ][ 'submission_count' ] = 0;
        $this->assignmentDataList[ self::EXAMINATION_ID ][ 'average' ] = 0;
        
        if ( file_exists( $this->assignmentListFileUrl )
            && $content = unserialize( file_get_contents( $this->assignmentListFileUrl ) ) )
        {
            foreach( $content as $assignmentId => $datas )
            {
                if ( isset( $this->assignmentDataList[ $assignmentId ] ) )
                {
                    $this->assignmentDataList[ $assignmentId ][ 'weight' ] = $datas[ 'weight' ];
                    $this->assignmentDataList[ $assignmentId ][ 'active' ] = $datas[ 'active' ];
                }
            }
        }
        
        if ( ! isset( $this->userQueryResult ) )
        {
            $this->userQueryResult = Claroline::getDatabase()->query( "
                SELECT
                    U.user_id, U.prenom, U.nom
                FROM
                    `{$this->tbl_names['user']}` AS U
                INNER JOIN
                    `{$this->tbl_names['cours_user']}` AS C
                ON
                    U.user_id = C.user_id
                WHERE
                    C.code_cours = " . Claroline::getDatabase()->quote( $this->courseId ) . "
                ORDER BY U.prenom, U.nom ASC"
                );
        }
        
        foreach( $this->userQueryResult as $line )
        {
            $this->userList[ $line[ 'user_id' ] ][ 'firstname' ] = $line[ 'prenom' ];
            $this->userList[ $line[ 'user_id' ] ][ 'lastname' ] = $line[ 'nom' ];
            $this->reportDataList[ $line[ 'user_id' ] ] = array();
        }
        
        if ( ! isset( $this->individualQueryResult ) )
        {
            $this->individualQueryResult = Claroline::getDatabase()->query( "
                SELECT
                    S1.id,
                    S1.user_id,
                    U.prenom,
                    U.nom,
                    S1.assignment_id,
                    A.title,
                    S2.score,
                    S2.creation_date
                FROM
                    `{$this->tbl_names['user']}` AS U,
                    `{$this->tbl['wrk_assignment']}` AS A,
                    `{$this->tbl['wrk_submission']}` AS S2,
                    `{$this->tbl['wrk_submission']}` AS S1
                LEFT JOIN
                    `{$this->tbl['group_rel_team_user']}` AS R
                ON
                    R.team = S1.group_id
                AND
                    R.user = S1.user_id
                WHERE
                    U.user_id = S1.user_id
                AND
                    A.assignment_type = 'INDIVIDUAL'
                AND
                    A.id = S1.assignment_id
                AND
                    S2.parent_id = S1.id
                AND
                    S2.parent_id IS NOT NULL
                AND
                    S2.score >= 0
                ORDER BY
                    U.nom, U.prenom, S2.creation_date DESC" );
        }
        
        $this->extractResultSet( $this->individualQueryResult );
        
        if ( ! isset( $this->groupQueryResult ) )
        {
            $this->groupQueryResult = Claroline::getDatabase()->query( "
                SELECT
                    S1.id,
                    S1.user_id,
                    U.prenom,
                    U.nom,
                    S1.assignment_id,
                    A.title,
                    S2.score,
                    S2.creation_date
                FROM
                    `{$this->tbl_names['user']}` AS U,
                    `{$this->tbl['group_team']}` AS G,
                    `{$this->tbl['group_rel_team_user']}` AS R,
                    `{$this->tbl['wrk_assignment']}` AS A,
                    `{$this->tbl['wrk_submission']}` AS S2,
                    `{$this->tbl['wrk_submission']}` AS S1
                WHERE
                    G.id = R.team
                AND
                    R.user = U.user_id
                AND
                    A.assignment_type = 'GROUP'
                AND
                    A.id = S1.assignment_id
                AND
                    S2.parent_id = S1.id
                AND
                    S2.parent_id IS NOT NULL
                AND
                    S2.score >= 0
                ORDER BY
                    U.nom, U.prenom, S2.creation_date DESC" );
        }
        
        $this->extractResultSet( $this->groupQueryResult );
        
        if ( file_exists( $this->examinationFileUrl )
             && $content = unserialize( file_get_contents( $this->examinationFileUrl ) ) )
        {
            foreach( $content as $userId => $datas )
            {
                if ( isset( $this->userList[ $userId ] ) )
                {
                    $this->reportDataList[ $userId ][ self::EXAMINATION_ID ] = $datas[ 'score' ];
                    $this->userList[ $userId ][ 'comment' ] = $datas[ 'comment' ];
                    $this->assignmentDataList[ self::EXAMINATION_ID ][ 'submission_count' ]++;
                    $this->assignmentDataList[ self::EXAMINATION_ID ][ 'average' ] += $datas[ 'score' ];
                }
            }
        }
        
        if ( file_exists( $this->activeUserListFileUrl )
             && $content = unserialize( file_get_contents( $this->activeUserListFileUrl ) ) )
        {
            foreach( $content as $userId => $is_active )
            {
                if ( isset( $this->userList[ $userId ] ) )
                {
                    $this->userList[ $userId ][ 'active' ] = (boolean)$is_active;
                }
            }
        }
        
        $this->setProportionalWeight();
        
        foreach( $this->reportDataList as $userId => $userReport )
        {
            $finalScore = 0;
            $activeCount = 0;
            $submissionCount = 0;
            
            foreach( $this->assignmentDataList as $assignmentId => $assignment )
            {
                $activeCount += (int)$assignment[ 'active' ];
                
                if ( $assignment[ 'active' ] && isset( $userReport[ $assignmentId ] ) )
                {
                    $finalScore += $userReport[ $assignmentId ] * $this->assignmentDataList[ $assignmentId ][ 'proportional_weight' ];
                    $submissionCount++;
                }
            }
            
            $complete = $submissionCount == $activeCount;
            
            if ( ! isset( $this->userList[ $userId ][ 'active' ] ) )
            {
                $this->userList[ $userId ][ 'active' ] = $complete;
            }
            
            if ( $this->userList[ $userId ][ 'active' ] || $complete )
            {
                $this->userList[ $userId ][ 'final_score' ] = round( $finalScore , 1 );
                
                if ( ! $complete )
                {
                    foreach( array_keys( $this->assignmentDataList ) as $assignmentId )
                    {
                        if ( ! isset( $this->reportDataList[ $userId ][ $assignmentId ] ) )
                        {
                            $this->reportDataList[ $userId ][ $assignmentId ] = 0;
                            $this->assignmentDataList[ $assignmentId ][ 'submission_count' ]++;
                        }
                    }
                }
            }
        }
        
        foreach( $this->assignmentDataList as $assignmentId => $assignment )
        {
            if ( $assignment[ 'submission_count' ] )
            {
                $average = round( $this->assignmentDataList[ $assignmentId ][ 'average' ]
                            / $assignment[ 'submission_count' ] , 1 );
                $this->assignmentDataList[ $assignmentId ][ 'average' ] = $average;
            }
            else
            {
                unset( $this->assignmentDataList[ $assignmentId ][ 'average' ] );
            }
        }
        
        $this->averageScore = 0;
        
        foreach( $this->assignmentDataList as $assignment )
        {
            if ( $assignment[ 'submission_count' ] )
            {
                $this->averageScore += $assignment[ 'average' ] * $assignment[ 'proportional_weight' ];
            }
        }
    }
    
    /**
     * Extracts the datas from $this->dataQueryResult set to arrays
     * @param ResultSet $resultSet
     */
    private function extractResultSet( $resultSet )
    {
        foreach( $resultSet as $line )
        {
            if ( isset( $this->assignmentDataList[ $line[ 'assignment_id' ] ] )
                 && $this->assignmentDataList[ $line[ 'assignment_id' ] ][ 'active' ]
                 && isset( $this->userList[ $line[ 'user_id' ] ] )
                 && ! isset( $this->reportDataList[ $line[ 'user_id' ] ][ $line[ 'assignment_id' ] ] ) )
            {
                $this->reportDataList[ $line[ 'user_id' ] ][ $line[ 'assignment_id' ] ] = $line[ 'score' ];
                $this->assignmentDataList[ $line[ 'assignment_id' ] ][ 'submission_count' ]++;
                $this->assignmentDataList[ $line[ 'assignment_id' ] ][ 'average' ] += $line[ 'score' ];
            }
        }
    }
    
    /**
     * Loads the report datas.
     * This method is called by the constructor if it receives a report id
     */
    public function loadReport( $reportId )
    {
        if ( $reportQueryResult = Claroline::getDatabase()->query( "
            SELECT
                title, datas, publication_date, visibility
            FROM
                `{$this->tbl['report_reports']}`
            WHERE
                id = " . Claroline::getDatabase()->escape( (int)$reportId )
        )->fetch() )
        {
            try
            {
                $this->title = $reportQueryResult[ 'title' ];
                $this->date = $reportQueryResult[ 'publication_date' ];
                $this->visibility = $reportQueryResult[ 'visibility' ] == self::VISIBLE
                                    ? self::VISIBLE
                                    : self::INVISIBLE;
                
                $datas = unserialize( $reportQueryResult[ 'datas' ] );
                
                $this->reportDataList = $datas[ 'report' ];
                $this->userList = $datas[ 'users' ];
                $this->assignmentDataList = $datas[ 'assignments' ];
                $this->averageScore = $datas[ 'average' ];
                
                $this->id = $reportId;
            }
            catch( Exception $e )
            {
                echo 'invalid datas : ' . $e->getMessage();
            }
        }
        
        return $this->id;
    }
    
    /**
     * Common getter for id
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Common getter for courseId
     */
    public function getCourseId()
    {
        return $this->courseId;
    }
    
    /**
     * Common getter for title
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Common getter for date
     */
    public function getDate()
    {
        return $this->date;
    }
    
    /**
     * Common getter for reportDataList
     */
    public function getReportDataList()
    {
        return $this->reportDataList;
    }
    
    /**
     * Common getter for userList
     */
    public function getUserList()
    {
        return $this->userList;
    }
    
    /**
     * Common getter for assignmentDataList
     */
    public function getAssignmentDataList()
    {
        return $this->assignmentDataList;
    }
    
    /**
     * Gets the global average (weighted) score
     * @return int $this->averegaScore (rounded)
     */
    public function getAverageScore()
    {
        return round( $this->averageScore , 1 );
    }
    
    /**
     * Gets the final score for the specified user
     * @param int $userId
     * @return int $finalScore
     * @throws execption if there is no entry for the specified $userId
     */
    public function getFinalScore( $userId )
    {
        if ( isset( $this->userList[ $userId ] ) )
        {
            return $this->userList[ $userId][ 'final_score' ];
        }
        else
        {
            return false;
        }
    }
    
    /**
     * Common setter for title
     * @param string $title;
     */
    public function setTitle( $title )
    {
        $this->title = $title;
    }
    
    /**
     * Sets an assignment active
     * @param int $assignmentId
     * @param boolean $active
     */
    public function setActive( $assignmentId , $active = false )
    {
        if ( isset( $this->id ) )
        {
            throw new Exception( 'Cannot change the active list of an published report' );
        }
        
        if ( isset( $this->assignmentDataList[ $assignmentId ] ) )
        {
            $this->assignmentDataList[ $assignmentId ][ 'active' ] = (boolean)$active;
            $this->setProportionalWeight();
        }
        else
        {
            throw new Exception( 'Invalid assignment id' );
        }
    }
    
    /**
     * Activates an user
     * @param int $userId
     */
    public function setUserActive( $userId , $active = false )
    {
        if ( isset( $this->userList[ $userId ] ) )
        {
            return $this->userList[ $userId ][ 'active' ] = (boolean)$active;
        }
        else
        {
            throw new Exception( 'Invalid user id' );
        }
    }
    
    /**
     * Sets weight for assignment
     * @param int $assignmentId the id of the assignment
     * @param int $weight the weight of the assignment
     */
    public function setWeight( $assignmentId , $weight )
    {
        if ( isset( $this->id ) )
        {
            throw new Exception( 'Cannot change the weight of an published report' );
        }
        
        if ( isset( $this->assignmentDataList[ $assignmentId ] ) )
        {
            $this->assignmentDataList[ $assignmentId ][ 'weight' ] = (int)$weight;
            $this->setProportionalWeight();
        }
        else
        {
            throw new Exception( 'Invalid assignment id' );
        }
    }
    
    /**
     * Calculates the proportional weights and put them in assignmentDataList
     * @return void
     */
    public function setProportionalWeight()
    {
        if ( isset( $this->id ) )
        {
            throw new Exception( 'Cannot change the weight of an published report' );
        }
        
        $weightSum = 0;
        
        foreach( $this->assignmentDataList as $assignment )
        {
            if ( $assignment[ 'active' ] )
            {
                $weightSum += $assignment[ 'weight' ];
            }
        }
        
        foreach( array_keys( $this->assignmentDataList  ) as $assignmentId )
        {
            if ( $this->assignmentDataList[ $assignmentId ][ 'active' ] )
            {
                $proportionalWeight = round( $this->assignmentDataList[ $assignmentId ][ 'weight' ] / $weightSum , 3 );
            }
            else
            {
                $proportionalWeight = 0;
            }
            $this->assignmentDataList[ $assignmentId ][ 'proportional_weight' ] = $proportionalWeight;
        }
    }
    
    /**
     * Sets examination score for a specified user
     * @param int $userId the user id
     * @param int $score
     */
    public function setScore( $userId , $score = null )
    {
        if ( isset( $this->userList[ $userId ] ) && abs( (int)$score ) <= self::DEFAULT_MAX_SCORE )
        {
            if ( is_null( $score ) )
            {
                unset( $this->reportDataList[ $userId ][ self::EXAMINATION_ID ] );
                unset( $this->userList[ $userId ][ 'comment' ] );
            }
            else
            {
                $this->reportDataList[ $userId ][ self::EXAMINATION_ID ] = abs( (int)$score );
            }
        }
        else
        {
            throw new Exception( 'Wrong parameters : ' . $userId . ' ' . $score );
        }
    }
    
    /**
     * Set a comment for a user
     * @param int $userId
     * @param string $comment
     */
    public function setComment( $userId , $comment = null )
    {
        if ( isset( $this->userList[ $userId ] ) )
        {
            if ( is_null( $comment ) )
            {
                unset( $this->userList[ $userId ][ 'Comment' ] );
            }
            else
            {
                $this->userList[ $userId ][ 'comment' ] = $comment;
            }
        }
        else
        {
            throw new Exception( 'Wrong parameters : ' . $userId . ' ' . $score );
        }
    }
    
    /**
     * Helper method to unset score
     */
    public function unsetScore( $userId )
    {
        $this->setScore( $userId );
    }
    
    /**
     * Helper method to unset comment
     */
    public function unsetComment( $userId )
    {
        $this->setComment( $userId );
    }
    
    /**
     * Resets the examination score list
     */
    public function resetScoreList()
    {
        foreach( array_keys( $this->userList ) as $userId )
        {
            $this->unsetScore( $userId );
        }
    }
    
    /**
     * Resets the active users list
     * @return boolean true if process is successful
     */
    public function resetActiveUserList()
    {
        if ( file_exists( $this->activeUserListFileUrl ) )
        {
            foreach( $this->userList as $userId => $data )
            {
                $this->userList[ $userId ][ 'active' ] = isset( $this->userList[ $userId ][ 'final_score' ] );
            }
            
            unlink( $this->activeUserListFileUrl );
        }
        
        return ! file_exists( $this->activeUserListFileUrl );
    }
    
    /**
     * Stores the weights values in file
     * @return boolean true if process is successful
     */
    public function saveAssignmentList()
    {
        $weightList = array();
        
        foreach( $this->assignmentDataList as $assignmentId => $assignmentData )
        {
            $assignmentList[ $assignmentId ] = array( 'active' => $assignmentData[ 'active' ]
                                                    , 'weight' => $assignmentData[ 'weight' ] );
        }
        
        if ( create_file( $this->assignmentListFileUrl , serialize( $assignmentList ) ) )
        {
            return $this->resetActiveUserList();
        }
        else
        {
            throw new Exception( 'Cannont save assignments datas' );
        }
    }
    
    /**
     * Stores the examination datas in file
     * @return boolean true if process is successful
     */
    public function saveScoreList()
    {
        $scoreList = array();
        
        foreach( $this->reportDataList as $userId => $score )
        {
            if ( isset( $score[ self::EXAMINATION_ID ] ) )
            {
                $scoreList[ $userId ][ 'score' ] = $score[ self::EXAMINATION_ID ];
            }
            
            if ( isset( $this->userList[ $userId ][ 'comment' ] ) )
            {
                $scoreList[ $userId ][ 'comment' ] = $this->userList[ $userId ][ 'comment' ];
            }
        }
        
        return create_file( $this->examinationFileUrl , serialize( $scoreList ) );
    }
    
    /**
     * Stores the active user list in file
     * @return boolean true if process is successful
     */
    public function saveActiveUserList()
    {
        $activeUserList = array();
        
        foreach( $this->userList as $userId => $data )
        {
            $activeUserList[ $userId ] = $data[ 'active' ];
        }
        
        return create_file( $this->activeUserListFileUrl, serialize( $activeUserList ) );
    }
    
    /**
     * Saves the reports
     * @return boolean true if successful
     */
    public function saveReport()
    {
        if ( ! $this->title )
        {
            throw new Exception( 'Cannot save a report without title!' );
        }
        
        $this->saveAssignmentList();
        
        $reportDataList = array();
        
        foreach( $this->reportDataList as $userId => $userDataList )
        {
            if ( $this->userList[ $userId ][ 'active' ] )
            {
                $reportDataList[ $userId ] = $userDataList;
            }
        }
        
        $data = array(
                'users' => $this->userList,
                'assignments' => $this->assignmentDataList,
                'report' => $reportDataList,
                'average' => $this->averageScore );
        
        if ( $this->id )
        {
            return Claroline::getDatabase()->exec( "
                UPDATE
                    `{$this->tbl['report_reports']}`
                SET
                    title = " . Claroline::getDatabase()->quote( $this->title ) . ",
                    datas = " . Claroline::getDatabase()->quote( serialize( $data ) ) . ",
                    publication_date " . Claroline::getDatabase()->quote($this->date ) .",
                    visibility = " . Claroline::getDatabase()->quote( $this->visibility ) . "
                WHERE
                    id = " . Claroline::getDatabase()->escape( $this->id )
            );
        }
        else
        {
            $this->visibility = self::VISIBLE;
            
            Claroline::getDatabase()->exec( "
                INSERT INTO
                    `{$this->tbl['report_reports']}`
                SET
                    title = " . Claroline::getDatabase()->quote( $this->title ) . ",
                    datas = " . Claroline::getDatabase()->quote( serialize( $data ) ) . ",
                    publication_date = NOW(),
                    visibility = " . Claroline::getDatabase()->quote( $this->visibility )
            );
            
            return $this->id = Claroline::getDatabase()->insertId();
        }
    }
    
    /**
     * Change the visibility
     * @param enum( self::INVISIBLE , self::VISIBLE ) $visibility
     */
    public function changeVisibility( $visibility )
    {
        $newVisibility = $visibility == self::VISIBLE
                        ? self::INVISIBLE
                        : self::VISIBLE;
        
        return Claroline::getDatabase()->exec( "
            UPDATE
                `{$this->tbl['report_reports']}`
            SET
                visibility = " . Claroline::getDatabase()->quote( $newVisibility ) ."
            WHERE
                id = " . Claroline::getDatabase()->escape( $this->id )
        );
    }
    
    /**
     * Deletes the report with the specified id
     * @return boolean true if successful
     */
    public function delete()
    {
        return Claroline::getDatabase()->exec( "
            DELETE FROM
                `{$this->tbl['report_reports']}`
            WHERE
                id = " . (int)$this->id );
    }
    
    /**
     * Gets the list of all the reports associated to the course
     * @static
     * @param boolean $seeAll : false shows only the visible ones
     * @return resultset $reportList
     */
    public static function getReportList( $seeAll = true )
    {
        $tbl = get_module_course_tbl ( array ( 'report_reports' ) );
        $where = $seeAll ? "" : "\nWHERE visibility = '" . self::VISIBLE ."'";
        
        return Claroline::getDatabase()->query( "
            SELECT
                id, title, publication_date, visibility
            FROM
                `{$tbl['report_reports']}`" . $where . "
            ORDER BY
                publication_date ASC"
        );
    }
}