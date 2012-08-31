<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.1 $Revision$ - Claroline 1.11
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICSUBSCR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class DatedLister extends Lister
{
    const PARAM_START_DATE = 'startDate';
    const PARAM_END_DATE = 'endDate';
    
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