<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.0.1 $Revision$ - Claroline 1.9
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICSUBSCR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class Session
{
    protected $id;
    protected $optionList = array();
    protected $slotList = array();
    
    public function __construct( $id = null )
    {
        $this->tbl = get_module_course_tbl( array( 'ICSUBSCR_session' , 'ICSUBSCR_slot' ) );
        
        if( $id )
        {
            $this->id = $id;
            $this->load();
        }
    }
    
    public function load()
    {
        $this->optionList = unserialize(
            Claroline::getDatabase()->query( "
                SELECT
                    optionList
                FROM
                    `{$this->tbl['ICSUBSCR_session']}`
                WHERE
                    id = " . Claroline::getDatabase()->escape( $this->id )
            )->fetch( Database_ResultSet::FETCH_VALUE ) );
        
        $slotList = Claroline::getDatabase()->query( "
                SELECT
                    id,
                    title,
                    description,
                    startDate
                    endDate,
                    rank,
                    visibility
                FROM
                    `{$this->tbl['ICSUBSCR_session']}`
                WHERE
                    sessionId = " . Claroline::getDatabase()->escape( $this->id ) . "
                ORDER BY rank" );
        
        foreach( $slotList as $slotData )
        {
            $slot = new Slot();
            $slot->load( $slotData );
            
            $this->slotList[ $slotData[ 'id' ] ] = $slot;
        }
    }
    
    public function getSlotList()
    {
        return $this->slotList;
    }
    
    public function getSlot( $slotId )
    {
        if( array_key_exists( $slotId , $this->slotList ) )
        {
            return $this->slotList[ $slotId ];
        }
    }
    
    public function getOptionList( $force )
    {
        if( $force )
        {
            $this->load();
        }
        
        return $this->optionList;
    }
    
    public function getOption( $option )
    {
        if( array_key_exists( $option , $this->optionList ) )
        {
            return $this->optionList[ $option ];
        }
    }
    
    public function setOption( $option , $value )
    {
        $this->optionList[ $option ] = $value;
    }
    
    public function save()
    {
        return Claroline::getDatabase()->query( "
                UPDATE
                    `{$this->tbl['ICSUBSCR_session']}`
                SET
                    optionList = " . Claroline::getDatabase()->quote( serialize( $this->optionList ) ) . "
                WHERE
                    id = " . Claroline::getDatabase()->escape( $this->id )
        );
    }
}