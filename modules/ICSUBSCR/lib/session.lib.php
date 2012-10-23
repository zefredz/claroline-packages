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

class Session
{
    const CONTEXT_USER = 'user';
    const CONTEXT_GROUP = 'group';
    const TYPE_UNDATED = 'undated';
    const TYPE_DATED = 'dated';
    const TYPE_TIMESLOT = 'timeslot';
    
    protected $id;
    protected $title;
    protected $description;
    protected $context;
    protected $type;
    protected $openingDate;
    protected $closingDate;
    protected $optionList;
    protected $slotList;
    protected $is_visible;
    protected $is_open;
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
            $this->optionList = unserialize( $data['optionList'] );
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
    public function getOpeningdate() { return $this->openingDate; }
    public function getClosingdate() { return $this->closingdate; }
    
    public function getSlotList()
    {
        return $this->slotList->getItemList();
    }
    
    public function isVisible()
    {
        return $this->is_visible === true;
    }
    
    public function show()
    {
        return $this->setVisibility( true );
    }
    
    public function hide()
    {
        return $this->setVisibility( false );
    }
    
    private function setVisibility( $is_visible = false )
    {
        if( $this->id )
        {
            throw new Exception( 'Session does not exist' );
        }
        
        $visibility = $is_visible === true ? 0 : 1;
        
        return Claroline::getDatabase()->exec( "
            UPDATE
                `{$this->tbl}`
            SET
                is_visible = " . Claroline::getDatabase()->escape( $visibility ) ) . "
            WHERE
                id = " . Claroline::getDatabase()->escape( $this->id );
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
}