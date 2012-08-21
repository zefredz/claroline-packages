<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.1 $Revision$ - Claroline 1.9
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICSUBSCR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class SessionList extends Lister
{
    const CONTEXT = 'context';
    const DEFAULT_TYPE = 'generic';
    const OPTION_LIST = 'optionList';
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
    
    protected $typeList;
    
    /**
     * Constructor
     * @param string $context : the actual context
     */
    public function __construct( $typeList , $context = self::ENUM_CONTEXT_USER , $allowedToEdit = false )
    {
        $this->typeList = $typeList;
        
        $tbl = get_module_course_tbl( array( 'icsubscr_session' ) );
        
        $allowedFields = array(
            'type' => self::DEFAULT_TYPE,
            'context' => self::ENUM_CONTEXT_USER,
            'title' => '',
            'optionList' => null,
            'description' => '',
            'startDate' => null,
            'endDate' => null,
            'status' => self::ENUM_STATUS_OPEN,
            'visibility' => self::ENUM_VISIBILITY_VISIBLE,
            'rank' => null );
        
        $filter = array( self::CONTEXT => $context );
        
        if( ! $allowedToEdit )
        {
            $filter[ self::PARAM_VISIBILITY ] = self::ENUM_VISIBILITY_VISIBLE; 
        }
        
        parent::__construct( $tbl[ 'icsubscr_session' ] , $filter , $allowedFields );
    }
    
    /**
     *
     */
    public function getTypeList()
    {
        return $this->typeList;
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
        $startDate = $this->get( $sessionId , self::PARAM_START_DATE );
        
        if( $startDate != '0000-00-00 00:00:00' )
        {
            return $startDate;
        }
    }
    
    /**
     * Helper for getting end date
     * @param int $sessionId : the session id
     * @return string : end date
     */
    public function getEndDate( $sessionId )
    {
        $endDate = $this->get( $sessionId , self::PARAM_END_DATE );
        
        if( $endDate != '0000-00-00 00:00:00' )
        {
            return $endDate;
        }
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
        $now = date( 'Y-m-d H:i:s' );
        
        return $this->isOpen( $sessionId )
            && $this->isVisible( $sessionId )
            && ( ! $this->getStartDate( $sessionId )
                || $this->getStartDate( $sessionId ) < $now )
            && ( ! $this->getEndDate( $sessionId )
                || $this->getEndDate( $sessionId ) > $now );
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
                        , self::ENUM_VISIBILITY_VISIBLE )
        && $this->save( $sessionId );
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
                        , self::ENUM_VISIBILITY_INVISIBLE )
        && $this->save( $sessionId );
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
                        , self::ENUM_STATUS_OPEN )
        && $this->save( $sessionId );
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
                        , self::ENUM_STATUS_CLOSED )
        && $this->save( $sessionId );
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
    
    /**
     * Getter for option value
     * @param int $sessionId : the session id
     * @param string $option : the name of the option
     * @return string : the value associated with the option
     */
    public function getOption( $sessionId , $option )
    {
        $optionList = $this->get( $sessionId , self::OPTION_LIST );
        
        if( is_string( $optionList ) )
        {
            $optionList = $optionList != '' ? unserialize( $optionList ) : array();
        }
        
        if( ! empty( $optionList ) && array_key_exists( $option , $optionList ) )
        {
            return $optionList[ $option ];
        }
    }
}