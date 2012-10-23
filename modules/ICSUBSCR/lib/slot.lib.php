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

class Slot
{
    protected $id;
    protected $sessionId;
    protected $label;
    protected $startDate;
    protected $endDate;
    protected $availableSpace;
    protected $recordList;
    protected $tbl;
    
    public function __construct( $sessionId , $id = null )
    {
        $tbl = get_module_course_tbl( array( 'icsubscr_slot' ) );
        $this->tbl = $tbl[ 'icsubscr_slot' ];
        
        $this->sessionId = $sessionId;
        
        if( $id )
        {
            $this->load( $id );
        }
    }
    
    public function load( $id )
    {
        if( $this->id )
        {
            $id = $this->id;
        }
        elseif( ! $this->id )
        {
            throw new Exception( 'Needs id' );
        }
        
        $data = Claroline::getDatabase()->query( "
            SELECT
                label,
                startDate,
                endDate,
                availableSpace
            FROM
                `{$this->tbl}`
            WHERE
                id = " . Claroline::getDatabase()->escape( $id )
        )->fetch( Database_ResultSet::FETCH_VALUE );
        
        if( ! empty( $data ) )
        {
            $this->label = $data[ 'label' ];
            $this->startDate = $data[ 'startDate' ];
            $this->endDate = $data[ 'endDate' ];
            $this->availableSpace = $data[ 'availableSpace' ];
            $this->id = $id;
        }
    }
    
    public function getId() { return $this->id; }
    public function getStartDate() { return $this->startDate; }
    public function getEndDate() { return $this->endDate; }
    public function getLabel() { return $this->label; }
    public function getAvailableSpace() { return $this->availableSpace; }
    
    public function setStartDate( $date )
    {
        $this->startDate = $date;
    }
    
    public function setEndDate( $date )
    {
        $this->endDate = $date;
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
            DELETE FROM `{$this->tbl}`
            WHERE id = " . Claroline::getDatabase()->escape( $this->id ) );
    }
}