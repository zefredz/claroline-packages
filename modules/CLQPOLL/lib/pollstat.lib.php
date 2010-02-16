<?php // $Id$

/**
 * Claroline Poll Tool
 *
 * @version     CLQPOLL 0.9.1 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLQPOLL
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class that represents the statistics for a poll
 * @property Poll object $poll the poll for which we want generate stats
 * @property int $userCount the number of potential voters ( i.e the course's user )
 * @property int $answerCount the number of voters
 * @property string $answerRate the answering rate expressed in percent
 * @property array $result the poll result
 * @property array $pieColorList the list of custom Color objects
 * @property static array $pieColorPalette the list of custom colors
 */
class PollStat
{
    protected $poll;
    
    protected $userCount;
    protected $answerCount;
    protected $answerRate;
    
    protected $labels = array();
    protected $result = array();
    protected $voteHistory = array();
    
    /**
     * Contructor
     * @param int pollId
     */
    public function __construct( $poll )
    {
        $this->poll = $poll;
    }
    
    /**
     *
     */
    public function load()
    {
        $this->userCount = $this->getCourseUserCount( claro_get_current_course_id() );
        $this->answerCount = count( $this->poll->getAllVoteList() );
        $this->popularity = $this->answerCount / $this->userCount;
        $this->answerRate = ( round( 100 * ( $this->answerCount ) / ( $this->userCount ) , 1 ) ) . " %";
    }
    
    /**
     * Gets the numbers of users for a course
     * @param string courseId;
     * @return int
     */
    public function getCourseUserCount( $courseId )
    {
        $tbl_mdb_names = claro_sql_get_main_tbl();
        
        $tbl_rel_course_user = $tbl_mdb_names[ 'rel_course_user' ];
        
        return Claroline::getDatabase()->query( "
            SELECT
                user_id
            FROM
                `" . $tbl_rel_course_user . "`
            WHERE
                code_cours = " . Claroline::getDatabase()->quote( $courseId )
        )->numRows();
    }
    
    /**
     * Gets general result
     */
    public function getResult( $force = false )
    {
        if ( empty( $this->result ) || $force )
        {
            foreach ( $this->poll->getChoiceList() as $choiceId => $label )
            {
                $this->result[ $choiceId ] = 0;
                $this->labels[ $choiceId ] = $label;
            }
            
            if ( $this->poll->getAllVoteList() )
            {
                foreach ( $this->poll->getAllVoteList() as $userId  => $vote )
                {
                    foreach ( $vote as $choiceId => $check )
                    {
                        if ( $check == UserVote::CHECKED ) $this->result[ $choiceId ]++;
                    }
                }
            }
        }
        return $this->result;
    }
    
    /**
     * Gets general result sorted by decreasing popularity
     */
    public function getSortedResult()
    {
        $result = $this->getResult();
        
        arsort( $result );
        
        return $result;
    }
    
    /**
     * Gets the 'winning' option
     */
    public function getWinner()
    {
        $winner = $this->getSortedResult();
        
        return key( $winner );
    }
    
    /**
     * Gets the amount of blank votes
     */
    public function getEmptyVoteCount( $force = false )
    {
        if ( ! isset( $this->getEmptyVoteCount ) || $force )
        {
            $emptyVoteCount = 0;
            
            foreach ( $this->poll->getAllVoteList() as $userVote )
            {
                if ( ! in_array( UserVote::CHECKED , $userVote ) ) $emptyVoteCount++;
            }
        }
        
        return $emptyVoteCount;
    }
    
    /**
     * Gets the result formated for a graph display
     */
    public function getGraph()
    {
        $this->load();
        $graph = array();
        $result = $this->getResult();
        $max = $result[ $this->getWinner() ];
        
        foreach ( $result as $choiceId => $voteCount )
        {
            $rate = $this->answerCount ? $voteCount / $this->answerCount : 0;
            $percent = substr( (string)( $rate * 100 ) , 0 , 4 ) . ' %';
            $score = $max != 0 ? $voteCount / $max : 0;
            
            $style = 'height: '
                    . (int)( 200 * $score )
                    . 'px; margin-top: '
                    . (int)(200 * ( 1 - $score ) )
                    . 'px; background-color: rgb( '
                    . (int)(50 + ( 100 * $score ) )
                    . ', '
                    . (int)(150 - ( 100 * $score ) )
                    . ', 0 );';
            
            $graph[ $choiceId ] = array( 'label' => $this->labels[ $choiceId ] ,
                                         'count' => $voteCount ,
                                         'rate' => $rate ,
                                         'percent' => $percent ,
                                         'style' => $style );
        }
        
        return $graph;
    }
}
