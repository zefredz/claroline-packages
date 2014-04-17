<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class TESTWS_AjaxRemoteService extends Ajax_Remote_Module_Service
{

    public function getInvokableMethods ()
    {
        return array ( 'getUserCourseList', 'getUserNotifiedItems' );
    }

    public function getInvokableClassName ()
    {
        return 'testws';
    }

    public function getModuleLabel ()
    {
        return 'TESTWS';
    }

    public function isMethodInvokationAllowed ( Ajax_Request $request )
    {
        if ( file_exists( get_module_path('CLKRNG') . '/' . get_module_entry ( 'CLKRNG' ) ) 
            && get_module_data('CLKRNG','activation') == 'activated' )
        {
            From::Module('CLKRNG')->uses('keyring.lib');
            Keyring::setOption('errorMode','exception');
            Keyring::checkForService('testws');
        }
        
        return true;
    }

    public function getUserCourseList ( Ajax_Request $request )
    {
        $courseTbl = get_module_main_tbl ( array ( 'rel_course_user' ) );

        $resultset = Claroline::getDatabase ()->query ( "
            SELECT
                user_id,
                profile_id,
                isCourseManager,
                tutor,
                code_cours
            FROM
                `{$courseTbl[ 'rel_course_user' ]}`
        " );

        $resultset->setFetchMode ( Database_ResultSet::FETCH_CLASS, 'TESTWS_CourseUserObject' );

        $result = array ( );

        foreach ( $resultset as $object )
        {
            $result[ ] = $object->__toString ();
        }

        return $result;
    }

    public function getUserNotifiedItems ( Ajax_Request $request )
    {

        $claroNotification = Claroline::getInstance ()->notification;
        
        $gid = 0;
        
        $date = $claroNotification->getLastActionBeforeLoginDate ( claro_get_current_user_id () );
        
        $result = array ( );
        
        foreach ( $claroNotification->getNotifiedCourses ( $date, claro_get_current_user_id () ) as $cid )
        {
            foreach ( $claroNotification->getNotifiedTools ( $cid, $date, claro_get_current_user_id () ) as $tid )
            {
                $result[ $cid ][ get_module_label_from_tool_id ( $tid ) ] = $claroNotification->getNotifiedRessources ( $cid, $date, claro_get_current_user_id (), $gid, $tid );
            }
        }
        
        return $result;
    }

}

class TESTWS_CourseUserObject implements Database_Object
{

    protected $id, $profileId, $_isCourseManager, $_isEligibleAsTutor, $courseId;

    protected function __construct (
    $id, $profileId, $isCourseManager, $isEligibleAsTutor, $courseId
    )
    {
        $this->id = $id;
        $this->profileId = $profileId;
        $this->_isCourseManager = $isCourseManager;
        $this->_isEligibleAsTutor = $isEligibleAsTutor;
        $this->courseId = $courseId;
    }

    public function getId ()
    {
        return $this->id;
    }

    public function getProfileId ()
    {
        return $this->profileId;
    }

    public function isCourseManager ()
    {
        return $this->_isCourseManager;
    }

    public function isEligibleAsTutor ()
    {
        return $this->_isEligibleAsTutor;
    }

    public function getCourseId ()
    {
        return $this->courseId;
    }

    public function __toString ()
    {
        return var_export ( $this, true );
    }

    public static function getInstance ( $data )
    {

        $obj = new self (
                $data[ 'user_id' ],
                $data[ 'profile_id' ],
                ($data[ 'isCourseManager' ] != 0 ),
                ($data[ 'tutor' ] != 0 ),
                $data[ 'code_cours' ]
        );

        return $obj;
    }

}
