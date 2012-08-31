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

class SlotList extends DatedLister
{
    const SESSION_ID = 'sessionId';
    const VISIBLE = 'visible';
    
    /**
     * Constructor
     * @param string $context : the actual context
     */
    public function __construct( $sessionId , $allowedToEdit = false )
    {
        $tbl = get_module_course_tbl( array( 'icsubscr_slot' ) );
        
        $allowedFields = array(
            'sessionId' => $sessionId,
            'title' => '',
            'description' => '',
            'startDate' => null,
            'endDate' => null,
            'availableSpace' => 0,
            'visibility' => self::VISIBLE,
            'rank' => null );
        
        $filter = array( self::SESSION_ID => $sessionId );
        
        parent::__construct( $tbl[ 'icsubscr_slot' ] , $filter , $allowedFields );
    }
}