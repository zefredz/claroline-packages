<?php // $Id$
/**
 * Online Meetings for Claroline
 *
 * @version     CLMEETNG 0.1.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLMEETNG
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class CLMEETNG_Meeting extends CLMEETNG_Decorator
{
    public $id;
    
    public $userId;
    
    protected $data = array();
    protected $is_manager = false;
    
    public function __construct( $client , $userId , $courseId , $groupId = null , $is_manager = false , $id = null )
    {
        parent::__construct( $client );
        
        $this->userId = $userId;
        
        $this->is_manager = (boolean)$is_manager;
        
        $this->tbl = get_module_course_tbl( array( 'CLMEETNG_meeting' ) );
        
        if( $id )
        {
            $this->load( $id );
        }
        else
        {
            $this->data[ 'group_id' ] = $groupId;
            
            if( $this->is_manager )
            {
                $this->data[ 'creator_id' ] = $userId;
            }
        }
    }
    
    public function load( $id = null )
    {
        if( ! $id )
        {
            $id = $this->id;
        }
        
        $sql = "SELECT
                    creator_id,
                    group_id,
                    title,
                    description,
                    date_from,
                    date_to,
                    creation_date,
                    modification_date,
                    meeting_type as type,
                    meeting_lang as lang,
                    max_user,
                    room_id,
                    room_recording_id,
                    is_moderated,
                    is_recording_allowed,
                    is_open,
                    is_visible
            FROM
                `{$this->tbl['CLMEETNG_meeting']}`
            WHERE
                id = " . Claroline::getDatabase()->escape( (int)$id );
        
        $result = Claroline::getDatabase()->query( $sql );
        
        if( $result->numRows() )
        {
            $this->data = $result->fetch( Database_ResultSet::FETCH_ASSOC );
            $this->is_manager = $this->data['creator_id'] == $this->userId;
            
            return $this->id = $id;
        }
    }
    
    public  function getData()
    {
        return $this->data;
    }
    
    public function setData( $data )
    {
        if( ! $this->is_manager )
        {
            throw new Exception( 'Not allowed' );
        }
        
        return $this->data = array_merge( $data , $this->data );
    }
    
    public function create( $data )
    {
        if( ! $this->is_manager )
        {
            throw new Exception( 'Not allowed' );
        }
        
        if( $this->createMeeting( $data ) )
        {
            $this->setData( $data );
            
            return $this->save();
        }
    }
    
    public function getProperty( $property )
    {
        if( array_key_exists( $property , $this->data ) )
        {
            return $this->data[ $property ];
        }
    }
    
    public function save()
    {
        if( ! $this->is_manager )
        {
            throw new Exception( 'Not allowed' );
        }
        
        if( $this->id )
        {
            $this->_update();
        }
        else
        {
            return $this->_insert()
                && $this->id = Claroline::getDatabase()->insertId();
        }
    }
    
    private function _insert()
    {
        return Claroline::getDatabase()->exec( "
            INSERT INTO
                `{$this->tbl['CLMEETNG_meeting']}`
            SET" . $this->_sqlString() );
    }
    
    private function _update()
    {
        return Claroline::getDatabase()->exec( "
            UPDATE
                `{$this->tbl['CLMEETNG_meeting']}`
            SET" . $this->_sqlString() . "
            WHERE
                id = " . Claroline::getDatabase()->escape( $this->id ) );
    }
    
    private function _sqlString()
    {
        $sql = array();
        
        foreach( $this->data as $property => $value )
        {
            $delimiter = is_int( $value ) ? 'escape' : 'quote';
            
            $sql[] = $property . " = " . Claroline::getDatabase()->{$delimiter}( $value );
        }
        
        return "\n" . implode( ",\n" , $sql );
    }
}