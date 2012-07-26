<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.0.2 $Revision$ - Claroline 1.9
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICSUBSCR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class SessionList extends FilteredLister
{
    const DEFAULT_TYPE = 'generic';
    const PARAM_TYPE = 'type';
    const PARAM_START_DATE = 'startDate';
    const PARAM_END_DATE = 'endDate';
    const PARAM_STATUS = 'status';
    const PARAM_RANK = 'rank';
    const PARAM_VISIBILITY = 'visibility';
    const ENUM_CONTEXT_USER = 'user';
    const ENUM_CONTEXT_GROUP = 'group';
    const ENUM_STATUS_OPEN = 'open';
    const ENUM_STATUS_CLOSED = 'closed';
    const ENUM_VISIBILITY_VISIBLE = 'visible';
    const ENUM_VISIBILITY_INVISIBLE = 'invisible';
    
    protected static $allowedFields = array(
        'type' => self::DEFAULT_TYPE,
        'context' => self::ENUM_CONTEXT_USER,
        'title' => '',
        'description' => '',
        'startDate' => null,
        'endDate' => null,
        'status' => self::ENUM_STATUS_OPEN,
        'visibility' => self::ENUM_VISIBILITY_VISIBLE,
        'rank' => null );
    
    /**
     * Constructor
     * @param string $context : the actual context
     */
    public function __construct( $context = self::ENUM_CONTEXT_USER )
    {
        $tbl = get_module_course_tbl( array( 'icsubscr_session' ) );
        
        parent::__construct( $tbl[ 'icsubscr_session' ] , array( 'context' => $context ) );
    }
    
    /**
     * Helper for getting session type
     * @param int $sessionId : the session id
     * @return string : session type
     */
    public function getType( $sessionId )
    {
        return $this->get( $sessionId , self::PARAM_TYPE );
    }
    
    /**
     * Helper for getting start date
     * @param int $sessionId : the session id
     * @return string : start date
     */
    public function getStartDate( $sessionId )
    {
        return $this->get( $sessionId , self::PARAM_START_DATE );
    }
    
    /**
     * Helper for getting end date
     * @param int $sessionId : the session id
     * @return string : end date
     */
    public function getEndDate( $sessionId )
    {
        return $this->get( $sessionId , self::PARAM_END_DATE );
    }
    
    /**
     * Helper for verifying if session is open
     * @param int $sessionId : the session id
     * @return boolean
     */
    public function isOpen( $sessionId )
    {
        return $this->get( $sessionId , self::PARAM_STATUS ) == self::ENUM_STATUS_OPEN;
    }
    
    /**
     * Helper for verifying if session is visible
     * @param int $sessionId : the session id
     * @return boolean
     */
    public function isVisible( $sessionId )
    {
        return $this->get( $sessionId , self::PARAM_VISIBILITY ) == self::ENUM_VISIBILITY_VISIBLE;
    }
    
    /**
     * Helper for verifying if session is available
     * @param int $sessionId : the session id
     * @return boolean
     */
    public function isAvailable( $sessionId )
    {
        $now = new date( 'Y-m-d H:i:s' );
        
        return $this->isOpen( $sessionId )
            && $this->isVisible( $sessionId )
            && ( ! $this->getStartDate() || $this->getStartDate( $sessionId ) < $now )
            && ( ! $this->getEndDate() || $this->getEndDate( $sessionId ) > $now );
    }
    
    /**
     * Helper for setting session visible
     * @param int $sessionId : the session id
     * @return boolean
     */
    public function setVisible( $sessionId )
    {
        return $this->set( $sessionId
                        , self::PARAM_VISIBILITY
                        , self::ENUM_VISIBILITY_VISIBLE );
    }
    
    /**
     * Helper for setting session invisible
     * @param int $sessionId : the session id
     * @return boolean
     */
    public function setInvisible( $sessionId )
    {
        return $this->set( $sessionId
                        , self::PARAM_VISIBILITY
                        , self::ENUM_VISIBILITY_INVISIBLE );
    }
    
    /**
     * Helper for setting session open
     * @param int $sessionId : the session id
     * @return boolean
     */
    public function setOpen( $sessionId )
    {
        return $this->set( $sessionId
                        , self::PARAM_STATUS
                        , self::ENUM_STATUS_OPEN );
    }
    
    /**
     * Helper for setting session closed
     * @param int $sessionId : the session id
     * @return boolean
     */
    public function setClosed( $sessionId )
    {
        return $this->set( $sessionId
                        , self::PARAM_STATUS
                        , self::ENUM_STATUS_CLOSED );
    }
    
    /**
     * Helper for setting start date of a session
     * @param int $sessionId : the session id
     * @return boolean
     */
    public function setStartDate( $sessionId , $date )
    {
        return $this->set( $sessionId
                        , self::PARAM_START_DATE
                        , $date );
    }
    
    /**
     * Helper for setting end date of a session
     * @param int $sessionId : the session id
     * @return boolean
     */
    public function setEndDate( $sessionId , $date )
    {
        return $this->set( $sessionId
                        , self::PARAM_END_DATE
                        , $date );
    }
    
    /**
     * Helper for unsetting dates of a session
     * @param int $sessionId : the session id
     * @return boolean
     */
    public function unsetDate( $sessionId )
    {
        return $this->set( $sessionId
                            , self::PARAM_START_DATE
                            , null )
            &&  $this->set( $sessionId
                            , self::PARAM_END_DATE
                            , null );
    }
}