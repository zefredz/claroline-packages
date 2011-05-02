<?php // $Id$
/**
 * User group chooser block class
 *
 * @version 0.2 $Revision$
 * @copyright (c) 2011 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package CLSUBSCR
 * @author Frederic Minne <zefredz@claroline.net>
 *
 */

FromKernel::uses('group.lib.inc');

class UserGroupChooser implements Display
{
    protected 
        $url,
        $userId,
        $courseId
        ;
    
    /**
     * Create a HTML group chooser for a user in a course
     * @param type $url base url 
     * @param type $userId user id
     * @param type $courseId course code (sys code)
     */
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
    
    /**
     * Render the group chooser
     * @return type string
     */
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
