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
 * Represents the polls list attached to a course
 * @var protected $polList
 */
class PollList
{
    protected $tbl;
    
    protected $pollList = false;
    
    /**
     * Constructor
     * Loads the tables names from database into a string array $tbl
     */
    public function __construct()
    {
        $this->tbl = get_module_course_tbl ( array ( 'poll_polls' , 'poll_choices' , 'poll_votes' ) );
    }
    
    /**
     * Gets the polls associated to the course
     * @return resultset $this->pollList
     */
    public function getPollList( $seeAll = true , $force = false)
    {
        if ( ! $this->pollList || $force )
        {
            $where = $seeAll ? "" : "\nWHERE visibility = 'visible'";
            
            $this->pollList = Claroline::getDatabase()->query( "
                SELECT
                    id, title, question, status, visibility
                FROM
                    `{$this->tbl[ 'poll_polls' ]}`" . $where
            );
        }
        
        return $this->pollList;
    }
    
    /**
     * Controls if poll exists
     * @param  int $pollId
     * @return int : 0 or 1
     */
    public function pollExists( $pollId )
    {
        return Claroline::getDatabase()->query( "
            SELECT
                id
            FROM
                `{$this->tbl[ 'poll_polls' ]}`
            WHERE
                id = " . Claroline::getDatabase()->escape( $pollId )
            )->numRows();
    }
}