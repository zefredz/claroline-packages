<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.4 $Revision$ - Claroline 1.9
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICSUBSCR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class Session extends DatedListable
{
    const DEFAULT_TYPE = 'generic';
    const PARAM_STATUS = 'status';
    const ENUM_STATUS_OPEN = 'open';
    const ENUM_STATUS_CLOSED = 'closed';
    const ENUM_TYPE_UNDATED = 'undated';
    const ENUM_TYPE_DATED = 'dated';
    const ENUM_TYPE_TIMESLOT = 'timeslot';
    const OPTION_USER_NAME_VISIBLE = 'user_name_visible';
    const OPTION_UNSUBSCRIBE_ALLOWED = 'unsubscribe_allowed';
    const OPTION_VOTE_MODIFICATION_ALLOWED = 'vote_modification_allowed';
    const OPTION_BLANK_VOTE_ENABLED = 'blank_vote_enabled';
    const OPTION_PREFERENCE_ENABLED = 'preference_enabled';
    const OPTION_MINIMUM_NUMBER_OF_VOTE = 'minimum_number_of_vote';
    const OPTION_MAXIMUM_NUMBER_OF_VOTE = 'maximum_number_of_vote';
    const OPTION_AVAILABLE_SPACE = 'available_space';
    
    protected static $defaultOptionList = array(
        self::OPTION_USER_NAME_VISIBLE => false,
        self::OPTION_UNSUBSCRIBE_ALLOWED => false,
        self::OPTION_VOTE_MODIFICATION_ALLOWED => false,
        self::OPTION_BLANK_VOTE_ENABLED => false,
        self::OPTION_PREFERENCE_ENABLED => false,
        self::OPTION_MINIMUM_NUMBER_OF_VOTE => 1,
        self::OPTION_MAXIMUM_NUMBER_OF_VOTE => 1,
        self::OPTION_AVAILABLE_SPACE => 1 );
    
    protected $id;
    protected $optionList = array();
    protected $slotList;
    
    /**
     * Constructor
     * @param int $id
     */
    public function __construct( $id = null , $optionList = null )
    {
        $tbl = get_module_course_tbl( array( 'icsubscr_session' ) );
        $this->tbl = $tbl[ 'icsubscr_session' ];
        
        $this->propertyList = array(
            'title' => self::NOT_NULL,
            'description' => self::NOT_NULL,
            'type' => self::ENUM_TYPE_UNDATED,
            'subType' => self::DEFAULT_TYPE,
            'startDate' => null,
            'endDate' => null,
            'status' => self::ENUM_STATUS_OPEN );
        
        parent::__construct( $id );
        
        if( $this->id )
        {
            $this->loadOptionList();
        }
        else
        {
            if( ! is_array( $optionList ) )
            {
                $optionList = array();
            }
            
            $this->loadDefaultOptionList( $optionList );
        }
        
        $this->slotList = new slotList( $this->id );
    }
    
    /**
     * Loads option list
     * This method is called by the constructor
     */
    public function loadOptionList()
    {
        $this->optionList = unserialize(
            Claroline::getDatabase()->query( "
                SELECT
                    optionList
                FROM
                    `{$this->tbl}`
                WHERE
                    id = " . Claroline::getDatabase()->escape( $this->id )
        )->fetch( Database_ResultSet::FETCH_VALUE ) );
        
        if( ! is_array( $this->optionList ) )
        {
            $this->optionList = array();
        }
        
        return ! empty( $this->optionList );
    }
    
    public function loadDefaultOptionList( $optionList )
    {
        $this->optionList = $this->validate( $optionList , self::$defaultOptionList );
    }
    
    /**
     * Gets slot list (helper)
     * @param boolean $force : to force reload
     * @return array : the slot list
     */
    public function getSlotList( $force = false )
    {
        return $this->slotList->getItemList( $force );
    }
    
    /**
     * Gets option list
     * @param boolean $force : to force reload
     * @return array : the option list
     */
    public function getOptionList( $force = false )
    {
        if( $force )
        {
            $this->load();
        }
        
        return $this->optionList;
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
    }
    
    /**
     * Saves option list
     * @return boolean
     */
    public function saveOption()
    {
        return Claroline::getDatabase()->query( "
                UPDATE
                    `{$this->tbl}`
                SET
                    optionList = " . Claroline::getDatabase()->quote(  ) . "
                WHERE
                    id = " . Claroline::getDatabase()->escape( $this->id )
        );
    }
    
    /**
     * Gets the datas for an given slot (helper method)
     * @param int $slotId
     * @return array
     */
    public function getSlotData( $slotId )
    {
        return $this->slotList->getData( $slotId );
    }
    
    /**
     * Adds a slot (helper method)
     * @param string $title
     * @param string $description
     * @param string $startDate
     * @param string $endDate
     * @param int availableSpace
     * @return int : the slot's id
     */
    public function addSlot(
        $title,
        $description,
        $startDate = null,
        $endDate = null,
        $availableSpace = 1 )
    {
        $data = array( 'title' => $title
                      , 'description' => $description
                      , 'startDate' => $startDate
                      , 'endDate' => $endDate
                      , 'availableSpace' => $availableSpace );
        
        return $this->slotList->add( $data );
    }
    
    /**
     * Deletes a slot (helper method)
     * @param int $slotId
     * @return boolean
     */
    public function deleteSlot( $slotId )
    {
        return $this->slotList->delete( $slotId );
    }
    
    public function isOpen()
    {
        return $this->get( self::PARAM_STATUS ) == self::ENUM_STATUS_OPEN;
    }
    
    public function open()
    {
        $this->set( self::PARAM_STATUS , self::ENUM_STATUS_OPEN );
        return $this->save();
    }
    
    public function close()
    {
        $this->set( self::PARAM_STATUS , self::ENUM_STATUS_CLOSED );
        return $this->save();
    }
}