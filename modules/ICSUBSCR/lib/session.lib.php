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
    protected $maxRank = 0;
    
    private static $slotOptionList = array( 'title'
        , 'description'
        , 'availableSpace'
        , 'startDate'
        , 'endDate'
        , 'visibility' );
    
    public function __construct( $id = null )
    {
        $this->tbl = get_module_course_tbl( array( 'icsubscr_session'
                                                , 'icsubscr_slot' ) );
        
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
                    `{$this->tbl['icsubscr_session']}`
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
                    `{$this->tbl['icsubscr_slot']}`
                WHERE
                    sessionId = " . Claroline::getDatabase()->escape( $this->id ) . "
                ORDER BY rank" );
        
        foreach( $slotList as $slotData )
        {
            $this->slotList[ 'slot_' . $slotData[ 'id' ] ] = $slotData;
        }
        
        $this->maxRank = $slotData[ 'rank' ];
    }
    
    public function getSlotList( $force = false )
    {
        if( $force )
        {
            $this->Load();
        }
        
        return $this->slotList;
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
                    `{$this->tbl['icsubscr_session']}`
                SET
                    optionList = " . Claroline::getDatabase()->quote( serialize( $this->optionList ) ) . "
                WHERE
                    id = " . Claroline::getDatabase()->escape( $this->id )
        );
    }
    
    public function getSlotData( $slotId )
    {
        if( array_key_exists( 'slot_' . $slotId , $this->slotList ) )
        {
            return $this->slotList[ 'slot_' . $slotId ];
        }
    }
    
    public function addSlot( $title , $description, $startDate = null , $endDate = null , $availableSpace = 1 )
    {
        if( Claroline::getDatabase()->exec( "
            INSERT INTO
                `{$this->tbl['icsubscr_slot']}`
            SET
                title = " . Claroline::getDatabase()->quote( $title ) . ",
                description = " . Claroline::getDatabase()->quote( $description ) . ",
                startDate = " . Claroline::getDatabase()->quote( $startDate ) . ",
                endDate = " . Claroline::getDatabase()->quote( $endDate ) . ",
                availableSpace = " . Claroline::getDatabase()->escape( (int)$availableSpace ) . ",
                rank = " . Claroline::getDatabase()->escape( ++$this->maxRank ) ) )
        {
            $slotId = Claroline::getDatabase()->insertId();
            $this->slotList[ 'slot_' . $slotId ] = array( 'title' => $title
                                                        , 'description' => $description
                                                        , 'startDate' => $startDate
                                                        , 'endDate' => $endDate
                                                        , 'availableSpace' => $availableSpace
                                                        , 'rank' => $this->maxRank
                                                        , 'id' => $sliotId );
            
            return $slotId;
        }
    }
    
    public function modifySlot( $slotId , $name , $value )
    {
        if( array_key_exists( 'slot_' . $slotId )
        && array_key_exists( $name , self::$slotOptionList ) )
        {
            $this->slotList[ 'slot_' . $slotId ][ $name ] = $value;
        }
    }
    
    public function deleteSlot( $slotId )
    {
        if( array_key_exists( 'slot_' . $slotId , $this->slotList ) )
        {
            return Claroline::getDatabase()->exec( "
                DELETE FROM
                    `{$this->tbl['icsubscr_slot']}`
                WHERE
                    id = " . Claroline::getDatabase()->escape( $slotId ) );
        }
    }
    
    public function saveSlot()
    {
        $nbRows = 0;
        
        foreach( $this->slotList as $slot )
        {
            if( Claroline::getDatabase()->exec( "
                UPDATE
                    `{$this->tbl['icsubscr_slot']}`
                SET
                    title = " . Claroline::getDatabase()->quote( $slot[ 'title' ] ) . ",
                    description = " . Claroline::getDatabase()->quote( $slot[ 'description' ] ) . ",
                    startDate = " . Claroline::getDatabase()->quote( $slot[ 'startDate' ] ) . ",
                    endDate = " . Claroline::getDatabase()->quote( $slot[ 'endDate' ] ) . ",
                    availableSpace = " . Claroline::getDatabase()->escape( $slot[ 'availableSpace' ] ) . ",
                    rank = " . Claroline::getDatabase()->escape( $slot[ 'rank' ] ) . "
                WHERE
                    id = " . Claroline::getDatabase()->escape( $slot[ 'id' ] ) ) )
            {
                $nbRows++;
            }
        }
        
        return $nbRows;
    }
    
    public function moveSlot( $slotId , $direction = 1 )
    {
        if( abs( $direction ) != 1 )
        {
            throw new Exception( 'Invalid value for direction: must be +1 for up, -1 for down' );
        }
        
        if( array_key_exists( 'slot_' . $slotId , $this->slotList ) )
        {
            $oldRank = $this->slotList[ 'slot_' . $slotId ][ 'rank' ];
            $newRank = $oldRank - $direction;
            
            $this->slotList[ 'slot_' . $slotId ][ 'rank' ] = $newRank;
            
            foreach( $this->slotList as $slot )
            {
                if( $slot[ 'id' ] != $slotId && $slot[ 'rank' ] == $rank )
                {
                    $this->slotList[ 'slot_' . $slot[ 'id' ] ][ 'rank' ] = $oldRank;
                }
            }
        }
        
        return $this->slotSave();
    }
    
    public function slotUp( $slotId )
    {
        return $this->moveSlot( $slotId , 1 );
    }
    
    public function slotDown( $slotId )
    {
        return $this->moveSlot( $slotId , -1 );
    }
}