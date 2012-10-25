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

class Result
{
    protected $session;
    protected $result;
    
    public function __construct( $session )
    {
        $this->session = $session;
        
        $tbl = get_module_course_tbl( array( 'icsubscr_record' ) );
        $this->tbl = $tbl[ 'icsubscr_record' ];
        
        $this->load();
    }
    
    public function load()
    {
        $slotList = array_keys( $this->session->getSlotList() );
        
        $sqlResult = Claroline::getDatabase()->query( "
            SELECT
                userId,
                groupId,
                slotId
            FROM
                `{$this->tbl}`
            WHERE
                slotId IN ( " . implode( ',' , $slotList ) . " )" );
        
        $this->result = array();
        
        foreach( $sqlResult as $data )
        {
            $slotId = $data[ 'slotId' ];
            $userId = $data[ 'userId' ];
            $groupId = $data[ 'groupId' ];
            
            $this->result[ $slotId ][] = array( 'userId' => $userId ,
                                                'groupId' => $groupId );
        }
    }
    
    public function getResult()
    {
        return $this->result;
    }
}