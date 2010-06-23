<?php // $Id$

/**
 * Claroline Poll Tool
 *
 * @version     CLQPOLL 0.9.9 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLQPOLL
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * Represents the poll (without its votes)
 * @property  int $id  the poll id
 * @property  string $title  the poll title (i.e. the subject)
 * @property  string $question  the complete description of the asked question
 * @property array $choiceList
 */
class Poll
{
    const VISIBLE = 'visible';
    const INVISIBLE = 'invisible';
    const OPEN_VOTE = 'open';
    const CLOSED_VOTE = 'closed';
    const UNLOCKED = 'unlocked';
    const LOCKED = 'locked';
    
    protected static $optionValueList = array(
        '_type'        => array( '_multi' , '_single' ), // the first value is default
        '_answer'      => array( '_required' , '_optional' ),
        '_privacy'     => array( '_public' , '_private' , '_anonymous' ),
        '_stat_access' => array( '_granted' , '_when_closed' , '_forbidden' ),
        '_revote'      => array( '_allowed' , '_not_allowed' )
        //...
    );
    
    protected $id;
    protected $title;
    protected $question;
    protected $optionList;
    protected $status;
    protected $visibility;
    protected $choiceList = array();
    protected $allVoteList = array();
    
    /**
     * Constructor
     * @param  int $id  the poll id
     * Loads the tables names from database into a string array $tbl, then calls the load() method
     */
    public function __construct( $id = false )
    {
        $this->tbl = get_module_course_tbl ( array ( 'poll_polls' , 'poll_choices' , 'poll_votes' ) );
        $this->tbl_names = get_module_main_tbl( array( 'user' ) );
        
        if ( $id )
        {
            $this->load( $id );
        }
        else
        {
            $this->visibility = Poll::VISIBLE;
            $this->status = Poll::OPEN_VOTE;
        }
        
        foreach( self::$optionValueList as $option => $valueList )
        {
            if ( ! isset( $this->optionList[ $option ] ) ) $this->optionList[ $option ] = $valueList[ 0 ];
        }
    }
    
    /**
     * Loads the attributes values from database
     * This method is called by the constructor
     */
    protected function load( $id )
    {
        $pollData =  Claroline::getDatabase()->query( "
            SELECT
                id, title, question, poll_options, visibility, status
            FROM
                `{$this->tbl['poll_polls']}`
            WHERE
                id = " . Claroline::getDatabase()->escape( $id )
        );
        
        if ( $pollData->numRows() )
        {
            $poll = $pollData->fetch();
            $this->title = $poll[ 'title' ];
            $this->question = $poll[ 'question' ];
            $this->visibility = $poll[ 'visibility' ];
            $this->status = $poll[ 'status' ];
            $this->optionList = unserialize( $poll[ 'poll_options' ] );
            $this->id = $id;
        }
    }
    
    /**
     * Common getter for $id
     * @return  int $id  the poll id
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Common getter for $title
     * @return  string $title  the poll title
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Common getter for $question
     * @return  string $question  the poll question
     */
    public function getQuestion()
    {
        return $this->question;
    }
    
    /**
     * gets the poll choice list
     * @return resultset $choiceList
     */
    public function getChoiceList( $force = false )
    {
        if ( $this->id && empty( $this->choiceList ) || $force )
        {
            foreach ( Claroline::getDatabase()->query( "
                SELECT
                    id, label
                FROM
                    `{$this->tbl['poll_choices']}`
                WHERE
                    poll_id =" . Claroline::getDatabase()->escape( $this->id ) ."
                ORDER BY
                    id ASC"
                ) as $choice )
            {
                $this->choiceList[ $choice[ 'id' ] ] = $choice[ 'label' ];
            }
        }
        
        return $this->choiceList;
    }
    
    /**
     * Common getter for $visibility
     * @return string $visibility
     */
    public function getVisibility()
    {
        return $this->visibility;
    }
    
    /**
     * Common getter for status
     * Determines if the vote is open or close for this poll
     * @return string $status ( 'open' or 'closed' )
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * Common getter
     * @return array $optionList
     */
    public function getOptionList()
    {
        return $this->optionList;
    }
    
    /**
     * Gets the value for a specified option
     * @param string $option the option name
     * @return string $value
     */
    public function getOption( $option )
    {
        if ( ! array_key_exists( $option , self::$optionValueList ) )
        {
            throw new Exception( 'Invalid option!' );
        }
        
        return $this->optionList[ $option ];
    }
    
    /**
     * Gets the value list for a specified option
     * @param string $option the option name
     * @return array $valueList the value list
     */
    public function getOptionValueList( $option )
    {
        if ( ! array_key_exists( $option , self::$optionValueList ) )
        {
            throw new Exception( 'Invalid option!' );
        }
        
        return self::$optionValueList[ $option ];
    }
    
    /**
     * Changes the poll title
     * @param string $title
     */
    public function setTitle( $title )
    {
        $this->title = $title;
    }
    
    /**
     * Changes the poll question
     * @param string $question
     */
    public function setQuestion( $question )
    {
        $this->question = $question;
    }
    
    /**
     * Changes the poll visibility
     * @param  string $visibility
     */
    public function setVisibility( $visibility )
    {
        if ( $visibility != self::VISIBLE && $visibility != self::INVISIBLE )
        {
            throw new Exception ( 'Invalid argument' );
        }
        
        $this->visibility = $visibility;
    }
    
    /**
     * Helper method to verify visibility
     */
    public function isVisible()
    {
        return $this->visibility == self::VISIBLE;
    }
    
    /**
     * Helper method to change visibility
     */
    public function changeVisibility()
    {
        if ( $this->isVisible() )
        {
            $this->setVisibility( self::INVISIBLE );
        }
        else
        {
            $this->setVisibility( self::VISIBLE );
        }
    }
    
    /**
     * Changes the poll status
     * @param  string $status ('open' or 'close')
     */
    public function setStatus( $status )
    {
        if ( $status != self::OPEN_VOTE && $status != self::CLOSED_VOTE )
        {
            throw new Exception ( 'Invalid argument' );
        }
        
        $this->status = $status;
    }
    
    /**
     * Helper method to open votes the poll
     */
    public function open()
    {
        $this->setStatus( self::OPEN_VOTE );
    }
    
    /**
     * Helper method to set invisible
     */
    public function close()
    {
        $this->setStatus( self::CLOSED_VOTE );
    }
    
    /**
     * Controls if the poll is open
     * $return boolean
     */
    public function isOpen()
    {
        return $this->status == self::OPEN_VOTE;
    }
    
     /**
     * A generic method to set a poll option
     * @param string $option the name of the option
     * @param string $value the value for the option
     * @return Poll $this
     */
    public function setOption( $option , $value )
    {
        if ( ! isset( self::$optionValueList[ $option ] ) )
        {
            throw new Exception( 'Invalid option' );
        }
        
        if( ! in_array( $value , self::$optionValueList[ $option ] ) )
        {
            throw new Exception( 'Invalid value' );
        }
        
        $this->optionList[ $option ] = $value;
    }
    
    /**
     * Adds a choice for the poll
     * $param string $option
     */
    public function addChoice( $label )
    {
        if ( ! $this->id )
        {
            throw new Exception( 'You must save the poll first!' );
        }
        
        if ( Claroline::getDatabase()->exec( "
            INSERT INTO
                `{$this->tbl['poll_choices']}`
            SET
                label = " . Claroline::getDatabase()->quote( $label ) .",
                poll_id = " . Claroline::getDatabase()->escape( $this->id )
        ) )
        {
            return $this->choiceList[ Claroline::getDatabase()->insertId() ] = $label;
        }
        else
        {
            throw new Exception( 'Cannot create new choice' );
        }
    }
    
    /**
     * Removes a choice from the options list
     * @param  int $choiceId
     * @return boolean true
     */
    public function deleteChoice( $choiceId )
    {
        if ( Claroline::getDatabase()->exec( "
                DELETE FROM
                    `{$this->tbl['poll_choices']}`
                WHERE
                    id = " . Claroline::getDatabase()->escape( $choiceId )
            ) )
        {
            unset( $this->choiceList[ $choiceId ] );
            
            return Claroline::getDatabase()->exec( "
                DELETE FROM
                    `{$this->tbl['poll_votes']}`
                WHERE
                    choice_id = " . Claroline::getDatabase()->escape( $choiceId )
            );
        }
        else
        {
            throw new Exception( 'Cannot delete option' );
        }
    }
    
    /**
     * Modifies a choice content
     * @param  int $choiceId
     * @param  string $label
     * @return boolean true
     */
    public function updateChoice ( $choiceId , $label )
    {
        if ( Claroline::getDatabase()->exec( "
                UPDATE
                    `{$this->tbl['poll_choices']}`
                SET
                    label = " . Claroline::getDatabase()->quote( $label ) . "
                WHERE
                   id = " . Claroline::getDatabase()->escape( $choiceId ) . "
                AND
                    poll_id = " . Claroline::getDatabase()->escape( $this->id )
            ) )
        {
            $this->choiceList[ $choiceId ] = $label;
            
            return $this;
        }
        else
        {
            throw new Exception( 'Cannot update option' );
        }
    }
    
    /**
     * Deletes all votes for this poll
     * @return boolean true
     */
    public function purge()
    {
        if ( Claroline::getDatabase()->exec( "
            DELETE FROM
                `{$this->tbl['poll_votes']}`
            WHERE
                poll_id = " . Claroline::getDatabase()->escape( $this->id )
        ) )
        {
            return $this;
        }
        else
        {
            return new Exception( 'Cannot purge poll' );
        }
    }
    
    /**
     * deletes the poll
     */
    function delete()
    {
        Claroline::getDatabase()->exec( "
            DELETE FROM
                `{$this->tbl['poll_votes']}`
            WHERE
                poll_id = " . Claroline::getDatabase()->escape( $this->id )
        );
        
        Claroline::getDatabase()->exec( "
            DELETE FROM
                `{$this->tbl['poll_choices']}`
            WHERE
                poll_id = " . Claroline::getDatabase()->escape( $this->id )
        );
        
        return Claroline::getDatabase()->exec("
            DELETE FROM
                `{$this->tbl['poll_polls']}`
            WHERE
                id = " . Claroline::getDatabase()->escape( $this->id )
        );
    }
    
    /**
     * Returns the vote content for each user
     * @return resultset $this->userVoteList
     */
    public function getAllVoteList( $force = false )
    {
        if ( $force )
        {
            $this->allVoteList = array();
        }
        
        if ( $this->id && empty( $this->allVoteList ) )
        {
            $voteList = Claroline::getDatabase()->query("
                SELECT
                    V.user_id, V.choice_id, V.vote, U.nom, U.prenom
                FROM
                    `{$this->tbl['poll_votes']}` AS V
                INNER JOIN
                    `{$this->tbl_names['user']}` AS U
                ON
                    U.user_id = V.user_id
                WHERE
                    V.poll_id = " . Claroline::getDatabase()->escape( $this->id ) . "
                ORDER BY
                    U.nom, V.user_id"
            );
            
            foreach( $voteList as $vote )
            {
                $this->allVoteList[ $vote[ 'user_id' ] ][ 'user_id' ] = $vote[ 'user_id' ];
                $this->allVoteList[ $vote[ 'user_id' ] ][ 'lastName' ] = $vote[ 'nom' ];
                $this->allVoteList[ $vote[ 'user_id' ] ][ 'firstName' ] = $vote[ 'prenom' ];
                $this->allVoteList[ $vote[ 'user_id' ] ][ $vote[ 'choice_id' ] ] = $vote[ 'vote' ];
            }
        }
        
        return $this->allVoteList;
    }
    
    /**
     * Saves the poll configuration
     */
    public function savePollConfig()
    {
        if ( Claroline::getDatabase()->exec( "
            UPDATE
                `{$this->tbl['poll_polls']}`
            SET
                poll_options = " . Claroline::getDatabase()->quote( unserialize( $this->optionList ) ) . "
            WHERE
                poll_id = " . Claroline::getDatabase()->escape( $this->id )
        ) )
        {
            return true;
        }
        else
        {
            return new Exception( 'Cannot save poll options' );
        }
    }
    
    /**
     * Saves the poll atributes into database
     */
    public function save()
    {
        $sql = "\n    `{$this->tbl['poll_polls']}`
                SET
                    title = " . Claroline::getDatabase()->quote( $this->title ) . ",
                    question = " . Claroline::getDatabase()->quote( $this->question ) . ",
                    poll_options = " . Claroline::getDatabase()->quote( serialize( $this->optionList ) ) . ",
                    status = " . Claroline::getDatabase()->quote( $this->status ) . ",
                    visibility = " . Claroline::getDatabase()->quote( $this->visibility );
                    
        if ( $this->id )
        {
            Claroline::getDatabase()->exec( "
                UPDATE" . $sql . "
                WHERE
                    id = " . Claroline::getDatabase()->escape( $this->id )
            );
        }
        else
        {
            Claroline::getDatabase()->exec( "
                INSERT INTO" . $sql
            );
            
            $this->id = Claroline::getDatabase()->insertId();
        }
        
        return $this;
    }
    
    /**
     * A static method to obtain a poll withe the specified id
     * @param int id
     * @return Poll object $poll
     */
    public static function loadFromId( $id )
    {
        $poll = new self( $id );
        
        if ( $poll->getId() )
        {
            return $poll;
        }
        else
        {
            //throw new Exception( 'Poll does not exist!' );
            return false;
        }
    }
    
    /**
     * A static method to create a poll with specified title and question
     * @param string $title
     * @param string $question
     * @return Poll object $poll
     */
    public static function create( $title, $question, $status = self::OPEN_VOTE , $visibility = self::INVISIBLE )
    {
        $poll = new self();
        $poll->setTitle( $title );
        $poll->setQuestion( $question );
        $poll->setStatus( $status );
        $poll->setVisibility( $visibility );
        $poll->save();
        
        return $poll;
    }
}