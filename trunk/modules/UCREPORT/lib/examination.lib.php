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
 * A class that represents an examination's session
 * @const DEFAULT_MAX_SCORE
 * @property int $sessionId
 * @property int $courseId
 * @property int $maxScore
 * @property array $scoreList
 */
class Examination
{
    const DEFAULT_MAX_SCORE = 20;
    
    protected $sessionId;
    protected $courseId;
    protected $maxScore;
    protected $scoreList = array();
    
    /**
     * Constructor
     * @param int $sessionId
     */
    public function __construct( $sessionId )
    {
        $this->sessionId = $sessionId;
        
        $this->main_tbl = get_module_main_tbl( array( 'user' , 'rel_course_user' ) );
        $this->tbl = get_module_course_tbl( array( 'examination_session' , 'examination_score' ) );
        
        $result = Claroline::getDatabase()->query( "
            SELECT
                title, max_score, visibility
            FROM
                `{$this->tbl['examination_session']}`
            WHERE
                id = " . Claroline::getDatabase()->escape( $this->sessionId )
        )->fetch();
        
        $this->title = $result[ 'title' ];
        $this->maxScore = $result[ 'max_score' ];
        $this->visibility = $result[ 'visibility' ];
        
        $userList = Claroline::getDatabase()->query( "
             SELECT
                U.user_id, U.nom, U.prenom
            FROM
                `{$this->main_tbl['user']}` AS U
            INNER JOIN
                `{$this->main_tbl['rel_course_user']}` AS C
            ON
                U.user_id = C.user_id
            AND
                C.code_cours =" . Claroline::getDatabase()->quote( ( claro_get_current_course_id() ) ) . "
            ORDER BY
                U.nom"
        );
        
        foreach( $userList as $user )
        {
            $this->scoreList[ $user[ 'user_id' ] ] = array( 'firstName' => $user[ 'prenom'],
                                                            'lastName' => $user[ 'nom' ],
                                                            'score' => null,
                                                            'comment' => null );
        }
        
        $this->load();
    }
    
    protected function load()
    {
        $result = Claroline::getDatabase()->query( "
            SELECT
                M.user_id, U.nom, U.prenom, M.score, M.comment
            FROM
                `{$this->tbl['examination_score']}` AS M,
                `{$this->main_tbl['user']}` AS U
            WHERE
                M.user_id = U.user_id
            AND
                M.session_id = " . Claroline::getDatabase()->quote( $this->sessionId ) . "
            ORDER BY
                U.nom"
        );
        
        foreach( $result as $line )
        {
            $this->scoreList[ $line[ 'user_id' ] ] = array( 'lastName' => $line[ 'nom' ],
                                                            'firstName' => $line[ 'prenom' ],
                                                            'score' => $line[ 'score' ],
                                                            'comment' => $line[ 'comment' ] );
        }
    }
    
    public function getScoreList( $force = false )
    {
        if ( $force )
        {
            $this->load();
        }
        
        return $this->scoreList;
    }
    
    public function getMaxScore()
    {
        return $this->maxScore;
    }
    
    public function getSessionId()
    {
        return $this->sessionId;
    }
    
    public function setScore( $userId , $score , $comment = '' )
    {
        if ( (int)$score > $this->maxScore || (int)$score < 0 ) throw new Exception( 'Invalid score value' );
        
        if ( $score == '' )
        {
            $this->scoreList[ $userId ][ 'score' ] = null;
            $this->scoreList[ $userId ][ 'comment' ] = null;
            $this->deleteScore( $userId );
        }
        elseif ( isset( $this->scoreList[ $userId ][ 'score' ] ) || isset( $this->scoreList[ $userId ][ 'comment' ] ) )
        {
            $this->updateScore( $userId , $score , $comment );
        }
        else
        {
            $this->insertScore( $userId , $score , $comment );
        }
    }
    
    protected function insertScore( $userId , $score , $comment )
    {
        return Claroline::getDatabase()->exec( "
            INSERT INTO
                `{$this->tbl['examination_score']}`
            SET
                session_id = " . Claroline::getDatabase()->escape( $this->sessionId ) . ",
                user_id = " . Claroline::getDatabase()->escape( $userId ) . ",
                score = " . Claroline::getDatabase()->escape( $score ) . ",
                comment =" . Claroline::getDatabase()->quote( $comment )
        );
    }
    
    protected function updateScore( $userId , $score , $comment )
    {
        return Claroline::getDatabase()->exec( "
            UPDATE
                `{$this->tbl['examination_score']}`
            SET
                score = " . Claroline::getDatabase()->escape( $score ) . ",
                comment =" . Claroline::getDatabase()->quote( $comment ) . "
            WHERE
                user_id = " . Claroline::getDatabase()->escape( $userId ) ."
            AND
                session_id = " . Claroline::getDatabase()->quote( $this->sessionId )
        );
    }
    
    public function deleteScore( $userId )
    {
        return Claroline::getDatabase()->exec( "
            DELETE FROM
                `{$this->tbl['examination_score']}`
            WHERE
                user_id = " . Claroline::getDatabase()->escape( $userId ) . "
            AND
                session_id = " . Claroline::getDatabase()->escape( $this->sessionId )
        );
    }
    
    public function resetScoreList()
    {
        if ( ! $this->is_courseReport ) throw new Exception( 'Not in appropriate mode' );
        
        return Claroline::getDatabase()->exec( "
            DELETE FROM
                `{$this->tbl['examination_score']}`
            WHERE
                session_id = " . Claroline::getDatabase()->escape( $this->sessionId )
        );
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function setTitle( $title )
    {
        $this->title = $title;
    }
    
    public function setMaxScore( $maxScore )
    {
        $this->maxScore = $maxScore;
    }
    
    public function save()
    {
        return Claroline::getDatabase()->exec( "
            UPDATE
                `{$this->tbl['examination_session']}`
            SET
                title = " . Claroline::getDatabase()->quote( $this->title ) . ",
                max_score = " . Claroline::getDatabase()->escape( $this->maxScore ) . ",
                visibility = " . Claroline::getDatabase()->quote( $this->visibility ) . "
            WHERE
                id = " . Claroline::getDatabase()->escape( $this->sessionId )
        );
    }
}