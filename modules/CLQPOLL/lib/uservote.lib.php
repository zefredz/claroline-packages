<?php // $Id$

/**
 * Claroline Poll Tool
 *
 * @version     CLQPOLL 1.2.2 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLQPOLL
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * Represents the user vote for a poll
 * @property  const UserVote::CHECKED  the string value for marking an option as voted 'yes' in database
 * @property  const UserVote::NOTCHECKED  the string value for 'no'
 * @property  Poll object $poll  the poll which the user vote is attached
 * @property  int $userId  the user id (picked from the database)
 * @property  array( string ) $preparedVote  the user vote datas... 
 */
class UserVote
{
    const CHECKED = 'checked';
    const NOTCHECKED = 'notchecked';
    const UNCHECKED = 'unchecked';
    
    protected $poll;
    protected $userId;
    protected $vote = array();
    protected $voteCount;
    
    /**
     * Constructor
     * @param  Poll object $poll  the poll object which the user vote is attached
     * @param  int $userId  the user id
     */
    public function __construct ( $poll , $userId )
    {
        $this->tbl = get_module_course_tbl ( array ( 'poll_polls' , 'poll_choices' , 'poll_votes' ) );
        
        $this->userId = $userId;
        $this->poll = $poll;
        
        foreach( array_keys( $this->poll->getChoiceList() ) as $choiceId )
        {
            $this->vote[ $choiceId ] = self::UNCHECKED;
        }
        
        $this->load();
    }
    
    /**
     * Common getter
     * @return  Poll object
     */
    public function getPoll()
    {
        return $this->poll;
    }
    
    /**
     * Common getter
     * @return  int $userId
     */
    public function getUserId()
    {
        return $this->userId;
    }
    
    /**
     * Gets the vote
     * @return $this->vote
     */
    public function getVote( $force = false )
    {
        if ( $force )
        {
            $this->load();
        }
        
        return $this->vote;
    }
    
    /**
     * Loads the vote datas if exists
     */
    public function load()
    {
        $voteList = Claroline::getDatabase()->query( "
            SELECT
                choice_id , vote
            FROM
                `{$this->tbl['poll_votes']}`
            WHERE
                poll_id = " . Claroline::getDatabase()->escape( (int)$this->poll->getId() ) . "
            AND
                user_id = " . Claroline::getDatabase()->escape( $this->userId )
        );
        
        $this->voteCount = $voteList->numRows();
        
        if ( $this->voteCount )
        {
            /*
            if( $voteList->numRows() != count( $this->poll->getChoiceList() ) )
            {
                throw new Exception( 'Error while loading user vote' );
            }*/
            
            foreach( $voteList as $vote )
            {
                $this->vote[ $vote[ 'choice_id' ] ] = $vote[ 'vote' ]; 
            }
        }
        
        return $this;
    }
    
    /**
     * Controls if the vote has been recorded
     * @return boolean
     */
    public function voteExists( $force = false )
    {
        if ( $force )
        {
            $this->load();
        }
        
        return $this->voteCount;
    }
    
    /**
     * Controls the vote validity
     * @return boolean
     */
    public function isVoteValid()
    {
        $checkCount = array_count_values( $this->getVote() );
        $checkedCount = ( isset( $checkCount[ self::CHECKED ] ) ) ? $checkCount[ self::CHECKED ] : 0;
        
        return ! ( ( $this->poll->getOption( '_type' ) == '_single'
             &&
             $checkedCount > 1 )
             ||
             ( $this->poll->getOption( '_answer' ) == '_required'
             &&
             $checkedCount == 0 ) );
    }
    
    /**
     * Set an individual value ( i.e the CHECK and NOTCHECK constants )
     * for the user vote.
     * @param  int $choiceId  The poll option id that was entered
     * @param  const ([UserVote::CHECK|UserVote::NOTCHECKED]) $checked
     */
    public function setVote( $choiceId , $checked )
    {
        if ( ! isset( $this->vote[ $choiceId ] ) )
        {
            throw new Exception( 'This option does not exist' );
        }
        
        if ( $checked != self::CHECKED && $checked != self::NOTCHECKED )
        {
            throw new Exception( 'Invalid submission');
        }
        
        $this->vote[ $choiceId ] = $checked;
    }
    
    /**
     * The entry point method to commit vote into database
     * First checks if vote has been already submited
     * @return true if submission succeed
     */
    public function saveVote()
    {
        if ( $this->isVoteValid() )
        {
            if ( $this->voteExists() )
            {
                $this->voteCount = $this->updateVoteList();
            }
            else
            {
                $this->voteCount = $this->insertVoteList();
            }
            
            return $this->voteCount;
        }
        else
        {
            throw new Exception( 'Invalid vote' );
        }
    }
    
    /**
     * Records the vote
     * @return  int $updatedRows  the numbers of lines entered
     */
    protected function insertVoteList()
    {
        $values = array();
        
        foreach( $this->vote as $choiceId => $vote )
        {
            $values[] = "("
                . Claroline::getDatabase()->escape( $this->poll->getId() ) . ","
                . Claroline::getDatabase()->escape( $choiceId ) . ","
                . Claroline::getDatabase()->escape( $this->userId ) . ","
                . Claroline::getDatabase()->quote( $vote )
                . ")";
        }
        
        return Claroline::getDatabase()->exec( "
                INSERT INTO
                    `{$this->tbl['poll_votes']}` ( poll_id , choice_id , user_id , vote )
                VALUES" . "\n"
                . implode( ",\n" , $values )
        );
    }
    
    /**
     * Updates the vote
     * @return  int $updatedRows  the numbers of modified lines
     */
    protected function updateVoteList()
    {
        $updatedRows = 0;
        
        foreach( $this->vote as $choiceId => $vote )
        {
            if ( Claroline::getDatabase()->exec( "
                UPDATE
                    `{$this->tbl['poll_votes']}`
                SET
                    vote = " . Claroline::getDatabase()->quote( $vote ) . "
                WHERE
                    choice_id = " . Claroline::getDatabase()->escape( $choiceId ) . "
                AND
                    user_id = " . Claroline::getDatabase()->escape( $this->userId ) . "
                AND
                    poll_id = " . Claroline::getDatabase()->escape( $this->poll->getId() )
                )
                ||
                 Claroline::getDatabase()->exec( "
                INSERT INTO
                    `{$this->tbl['poll_votes']}`
                SET
                    poll_id = " . Claroline::getDatabase()->escape( $this->poll->getId() ) . ",
                    choice_id = " . Claroline::getDatabase()->escape( $choiceId ) . ",
                    user_id = " . Claroline::getDatabase()->escape( $this->userId ) . ",
                    vote = " . Claroline::getDatabase()->quote( $vote )
                )
            )
            {
                $updatedRows++;
            }
        }
        
        return $updatedRows;
    }
    
    /**
     * Delete the user vote
     * @return  boolean true if the operation proceeded successfully
     */
    public function deleteVote()
    {
        return Claroline::getDatabase()->exec("
                DELETE FROM
                    `{$this->tbl['poll_votes']}`
                WHERE
                    user_id = " . Claroline::getDatabase()->escape( $this->userId ) . "
                AND
                    poll_id = " . Claroline::getDatabase()->escape( $this->poll->getId() )
        );
    }
    
    /**
     * Deletes the vote for an specified user
     * @param int $userId
     * @return  boolean true if the operation proceeded successfully
     */
    public static function deleteUserVote( $poll , $userId )
    {
        $userVote = new self( $poll , $userId );
        return $userVote->deleteVote();
    }
}