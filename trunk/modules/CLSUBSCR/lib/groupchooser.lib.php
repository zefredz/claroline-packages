<?php

FromKernel::uses('group.lib.inc');

class UserGroupChooser implements Display
{
    protected 
        $url,
        $userId,
        $courseId
        ;
    
    public function __construct( $url = null, $userId = null, $courseId = null )
    {
        $this->url = (empty($url))
            ? $_SERVER['PHP_SELF']
            : $url
            ;
        
        $this->userId = (empty($userId)) 
            ? claro_get_current_user_id() 
            : $userId
            ;
        
        $this->courseId = (empty($courseId)) 
            ? claro_get_current_course_id() 
            : $courseId
        ;
    }
    
    public function render()
    {
        $userGroupList = get_user_group_list(
            $this->userId,
            $this->courseId
        );

        if ( count ( $userGroupList ) )
        {
            $groupChooser = new ModuleTemplate( 'CLSUBSCR', 'select_group_form.tpl.php' );
            $groupChooser->assign( 'userGroupList', $userGroupList );
            $groupChooser->assign( 'url', $this->url );

            return $groupChooser->render();
        }
        else
        {
            return '';
        }
    }
}
