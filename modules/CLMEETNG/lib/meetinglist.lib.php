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

class CLMEETNG_MeetingList
{
    public $courseId;
    public $groupId;
    
    protected $meetingList = array();
    
    protected $is_manager;
    
    public function __construct( $courseId , $groupId = null , $is_manager = false )
    {
        $this->courseId = $courseId;
        $this->groupId = $groupId;
        $this->is_manager = $is_manager;
        
        $this->tbl = get_module_course_tbl( array( 'CLMEETNG_meeting' ) );
    }
    
    public function load()
    {
        $sql = "SELECT
                    id,
                    creator_id,
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
                `{$this->tbl['CLMEETNG_meeting']}`";
        
        if( $this->groupId )
        {
            $sql .= "\nWHERE group_id = " . Claroline::getDatabase()->escape( $this->groupId );
        }
        else
        {
            $sql .= "\nWHERE group_id = 0";
        }
        
        if( ! $this->is_manager )
        {
            $sql .= "\nAND is_visible = 1";
        }
        
        $result = Claroline::getDatabase()->query( $sql );
        
        foreach( $result as $line )
        {
            $this->meetingList[ $line['id'] ] = $line;
        }
    }
    
    public function getList( $refresh = false )
    {
        if( $refresh || empty( $this->meetingList ) )
        {
            $this->load();
        }
        
        return $this->meetingList;
    }
}