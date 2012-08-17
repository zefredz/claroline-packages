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

class Session
{
    const OPTION_USER_NAME_VISIBLE = 'user_name_visible';
    const OPTION_UNSUBSCRIBE_ALLOWED = 'unsubscribe_allowed';
    const OPTION_VOTE_MODIFICATION_ALLOWED = 'vote_modification_allowed';
    const OPTION_BLANK_VOTE_ENABLED = 'blank_vote_enabled';
    const OPTION_PREFERENCE_ENABLED = 'preference_enabled';
    const OPTION_MINIMUM_NUMBER_OF_VOTE = 'minimum_number_of_vote';
    const OPTION_MAXIMUM_NUMBER_OF_VOTE = 'maximum_number_of_vote';
    
    protected $id;
    protected $optionList = array();
    protected $slotList;
    
    private static $slotOptionList = array(
        'title',
        'description',
        'availableSpace',
        'startDate',
        'endDate',
        'visibility' );
    
    /**
     * Constructor
     * @param int $id
     */
    public function __construct( $id = null )
    {
        $this->tbl = get_module_course_tbl(
            array(
                'icsubscr_session',
                'icsubscr_slot' ) );
        
        if( $id )
        {
            $this->id = $id;
            $this->load();
            
            $this->slotList = new Lister( $this->tbl['icsubscr_slot'] , array( 'sessionId' => $id ) );
        }
    }
    
    /**
     * Loads data from database
     * This method is called by the constructor
     */
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
    public function getOptionList( $force )
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
    
    /**
     * Gets the datas for an given slot
     * @param int $slotId
     * @return array
     */
    public function getSlotData( $slotId )
    {
        return $this->slotList->getData( $slotId );
    }
    
    /**
     * Adds a slot
     * @param string $title
     * @param string $description
     * @param string $startDate
     * @param string $endDate
     * @param int availableSpace
     * @return int : the slot's id
     */
    public function addSlot( $title , $description, $startDate = null , $endDate = null , $availableSpace = 1 )
    {
        $data = array( 'title' => $title
                      , 'description' => $description
                      , 'startDate' => $startDate
                      , 'endDate' => $endDate
                      , 'availableSpace' => $availableSpace );
        return $this->addSlot->add( $data );
    }
}