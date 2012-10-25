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

class Session extends Hidable
{
    const CONTEXT_USER = 'user';
    const CONTEXT_GROUP = 'group';
    const TYPE_UNDATED = 'undated';
    const TYPE_DATED = 'dated';
    const TYPE_TIMESLOT = 'timeslot';
    const OPTION_USER_NAME_VISIBLE = 'user_name_visible';
    const OPTION_UNSUBSCRIBE_ALLOWED = 'unsubscribe_allowed';
    const OPTION_VOTE_MODIFICATION_ALLOWED = 'vote_modification_allowed';
    const OPTION_BLANK_VOTE_ENABLED = 'blank_vote_enabled';
    const OPTION_PREFERENCE_ENABLED = 'preference_enabled';
    const OPTION_MINIMUM_NUMBER_OF_VOTE = 'minimum_number_of_vote';
    const OPTION_MAXIMUM_NUMBER_OF_VOTE = 'maximum_number_of_vote';
    const OPTION_AVAILABLE_SPACE = 'available_space';
    
    protected $id;
    protected $title;
    protected $description;
    protected $context;
    protected $type;
    protected $openingDate;
    protected $closingDate;
    protected $optionList = array();
    protected $slotList = array();
    protected $is_visible = 1;
    protected $is_open = 1;
    protected $tbl;
    
    public function __construct( $id = null )
    {
        $tbl = get_module_course_tbl( array( 'icsubscr_session' ) );
        $this->tbl = $tbl[ 'icsubscr_session' ];
        
        if( $id )
        {
            $this->load( $id );
            $this->slotList = new ICSUBSCR_List( 'slot' , $id );
        }
    }
    
    public function load( $id = null )
    {
        if( $this->id )
        {
            $id = $this->id;
        }
        elseif( ! $id )
        {
            throw new Exception( 'Needs id' );
        }
        
        $data = Claroline::getDatabase()->query( "
            SELECT
                title,
                description,
                context,
                type,
                optionList,
                openingDate,
                closingDate,
                is_open,
                is_visible
            FROM
                `{$this->tbl}`
            WHERE
                id = " . Claroline::getDatabase()->escape( $id )
        )->fetch( Database_ResultSet::FETCH_VALUE );
        
        if( ! empty( $data ) )
        {
            $optionList = unserialize( $data['optionList'] );
            $this->optionList = is_array( $optionList ) ? $optionList : array();
            
            $this->title = $data[ 'title' ];
            $this->description = $data[ 'description' ];
            $this->context = $data[ 'context' ];
            $this->type = $data[ 'type' ];
            $this->openingDate = $data[ 'openingDate' ];
            $this->closingDate = $data[ 'closingDate' ];
            $this->is_open = (boolean)$data[ 'is_open' ];
            $this->is_visible = (boolean)$data[ 'is_visible' ];
            $this->id = $id;
        }
    }
    
    public function getId() { return $this->id; }
    public function getTitle() { return $this->title; }
    public function getDescription() { return $this->description; }
    public function getContext() { return $this->constext; }
    public function getType() { return $this->type; }
    public function getOpeningDate() { return $this->openingDate; }
    public function getClosingDate() { return $this->closingDate; }
    
    public function getSlotList()
    {
        return $this->slotList->getItemList();
    }
    
    public function getSlot( $slotId )
    {
        $this->slotList->getItem( $slotId );
    }
    
    public function isOpen()
    {
        return $this->is_open === true;
    }
    
    public function open()
    {
        return $this->setOpen( true );
    }
    
    public function close()
    {
        return $this->setOpen( false );
    }
    
    private function setOpen( $is_open = false )
    {
        if( $this->id )
        {
            throw new Exception( 'Session does not exist' );
        }
        
        $open = $is_open === true ? 0 : 1;
        
        return Claroline::getDatabase()->exec( "
            UPDATE
                `{$this->tbl}`
            SET
                is_open = " . Claroline::getDatabase()->escape( $open ) ) . "
            WHERE
                id = " . Claroline::getDatabase()->escape( $this->id );
    }
    
    public function setContext( $context = self::CONTEXT_USER )
    {
        if( $context != self::CONTEXT_USER
           && $context != self::CONTEXT_GROUP )
        {
            throw new Exception( 'Invalid context' );
        }
        
        $this->context = $context;
    }
    
    public function setType( $type = self::TYPE_UNDATED )
    {
        if( $type != self::TYPE_UNDATED
           && $type != self::TYPE_DATED
           && $type != self::TYPE_TIMESLOT )
        {
            throw new Exception( 'Invalid type' );
        }
    }
    
    public function save()
    {
        $sqlData = "title = " . Claroline::getDatabase()->quote( $this->title ) . "\n"
            . "description = " . Claroline::getDatabase()->quote( $this->description ) . "\n"
            . "context = " . Claroline::getDatabase()->quote( $this->context ) . "\n"
            . "type = " . Claroline::getDatabase()->quote( $this->type ) . "\n"
            . "openingDate" . Claroline::getDatabase()->quote( $this->openingDate ) . "\n"
            . "closingDate" . Claroline::getDatabase()->quote( $this->closingDate ) . "\n";
        
        if( $this->id )
        {
            Claroline::getDatabase()->exec( "
                UPDATE `{$this->tbl}`
                SET\n" . $sqlData
                . "WHERE id = " . Claroline::getDatabase()->escape( $this->id ) );
        }
        else
        {
            Claroline::getDatabase()->exec( "
                INSERT INTO `{$this->tbl}`
                SET\n" . $sqlData );
            
            $this->id = Claroline::getDatabase()->insertId();
        }
        
        return $this->id;
    }
    
    public function delete()
    {
        return Claroline::getDatabase()->exec( "
            DELETE FROM `{$this->tbl}` WHERE id = " . Claroline::getDatabase()->escape( $this->id ) );
    }
    
    /**
     * 
     * @param int $sessionId : the session id
     * @return boolean
     */
    public function isAvailable()
    {
        $now = date( 'Y-m-d H:i:s' );
        
        return $this->isOpen()
            && $this->isVisible()
            && ( ! $this->openingDate
                || $this->openingDate < $now )
            && ( ! $this->closingDate
                || $this->closingDate > $now );
    }
    
    /**
     * Gets the value of an given option
     * @param string $option
     * @return string
     */
    public function getOption( $option )
    {
        if( array_key_exists( $option , $this->optionList ) )
        {
            return $this->optionList[ $option ];
        }
    }
    
    /**
     * Sets the value for an option
     * @param string $option
     * @param string $value
     * @return void
     */
    public function setOption( $option , $value )
    {
        $this->optionList[ $option ] = $value;
        
        return $this->saveOptionList();
    }
    
    public function removeOption( $option )
    {
        unset( $this->optionList[ $option ] );
    }
    
    public function resetOptionList()
    {
        $this->optionList = array();
        
        return $this->saveOptionList();
    }
    
    /**
     * Saves option list
     * @return boolean
     */
    public function saveOptionList()
    {
        return Claroline::getDatabase()->query( "
                UPDATE
                    `{$this->tbl}`
                SET
                    optionList = " . Claroline::getDatabase()->quote( serialize( $this->optionList ) ) . "
                WHERE
                    id = " . Claroline::getDatabase()->escape( $this->id )
        );
    }
}