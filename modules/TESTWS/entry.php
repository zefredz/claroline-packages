<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:

// load Claroline kernel
$tlabelReq = 'TESTWS';
require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

claro_set_display_mode_available(true);

$GLOBALS['claroline']['display']->header->setTitle( 'Library testing module' );

// ------------- Business Logic ---------------------------

//$databaseConnection = new Claroline_Database_Connection_new();
//$databaseConnection->connect();

class CourseUserObject implements Database_Object
{
    protected $id, $profileId, $_isCourseManager, $_isEligibleAsTutor, $courseId;

    protected function __construct(
        $id,
        $profileId,
        $isCourseManager,
        $isEligibleAsTutor,
        $courseId
    )
    {
        $this->id = $id;
        $this->profileId = $profileId;
        $this->_isCourseManager = $isCourseManager;
        $this->_isEligibleAsTutor = $isEligibleAsTutor;
        $this->courseId = $courseId;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getProfileId()
    {
        return $this->profileId;
    }

    public function isCourseManager()
    {
        return $this->_isCourseManager;
    }

    public function isEligibleAsTutor()
    {
        return $this->_isEligibleAsTutor;
    }

    public function getCourseId()
    {
        return $this->courseId;
    }

    public function __toString()
    {
        return var_export($this, true);
    }

    public static function getInstance( $data )
    {
        
        $obj = new self(
            $data['user_id'],
            $data['profile_id'],
            ($data['isCourseManager'] != 0),
            ($data['tutor'] != 0),
            $data['code_cours']
        );

        return $obj;
    }
}

$courseTbl = get_module_main_tbl(array('rel_course_user'));

$resultset = Claroline::getDatabase()->query("
    SELECT
        user_id,
        profile_id,
        isCourseManager,
        tutor,
        code_cours
    FROM
        `{$courseTbl['rel_course_user']}`
");

$resultset->setFetchMode(Database_ResultSet::FETCH_CLASS, 'CourseUserObject');

foreach ( $resultset as $user )
{
    $GLOBALS['claroline']['display']->body->appendContent(
        '<pre>'
        . $user->__toString()
        .'</pre>'
    );
}

$ajaxHandler = Ajax_Remote_Module_Service::getModuleServiceInstance('TESTWS');

$url = $ajaxHandler->getInvokationUrl('getUserCourseList');
$url->relayCurrentContext();
$urlStr = htmlspecialchars($url->toUrl());

$url2 = $ajaxHandler->getInvokationUrl('getUserNotifiedItems');
$url2->relayCurrentContext();
$urlStr2 = htmlspecialchars($url2->toUrl());

$claroline->display->body->appendContent("<p><a href='$urlStr'>Call the user course list service (internal)</a></p>");
$claroline->display->body->appendContent("<p><a href='$urlStr2'>Call the notification service (internal)</a></p>");

$url = $ajaxHandler->getExternalInvokationUrl('getUserCourseList');
$url->relayCurrentContext();
$urlStr = htmlspecialchars($url->toUrl());

$url2 = $ajaxHandler->getExternalInvokationUrl('getUserNotifiedItems');
$url2->relayCurrentContext();
$urlStr2 = htmlspecialchars($url2->toUrl());

$GLOBALS['claroline']['display']->body->appendContent("<p><a href='$urlStr'>Call the user course list service (external)</a></p>");
$GLOBALS['claroline']['display']->body->appendContent("<p><a href='$urlStr2'>Call the notification service (external)</a></p>");

$GLOBALS['claroline']['display']->body->appendContent( var_export( claro_is_platform_admin(), true ) );

echo $GLOBALS['claroline']['display']->render();
