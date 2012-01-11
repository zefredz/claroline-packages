<?php 
global $tlabelReq;
$tlabelReq = 'LVSURVEY';

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';
FromKernel::uses(   'course_user.lib.php', 
                    'sendmail.lib');
From::module('LVSURVEY')->uses('controller/managerSurveyPage.class');



class ShowParticipationPage extends ManagerSurveyPage
{
    const SURVEY_PARTICIPANTS = __LINE__;
    const IN_COURSE_PARTICIPANTS = __LINE__;
    const OFF_COURSE_PARTICIPANTS = __LINE__;
    const IN_COURSE_NOT_PARTICIPANTS = __LINE__;
    
    private $participationMap;
    private $emailBody;
    
    public function __construct()
    {
        parent::__construct();
        $this->participationMap = $this->buildParticipationMap();
        $survey = parent::getSurvey();      
        $recall_message_parameters = array(
            '%course_name'                      => $survey->getCourse()->title,
            '%survey_name'                      => $survey->title, 
            '%survey_participation_address'     => dirname(ShowParticipationPage::absoluteUriToSelf()) . "/show_survey.php?surveyId={$survey->id}",
        ); 
        $message_contents = get_lang('__RECALL_MESSAGE__',$recall_message_parameters);
        $this->emailBody = $message_contents == '__RECALL_MESSAGE__'? '':$message_contents;
    }
    
    private static function absoluteUriToSelf()
    {
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
    }
    
    public function performSendRecallMail()
    {
        $userInput = Claro_UserInput::getInstance();
        try
        {
            $messageBody = (string)$userInput->getMandatory('emailBody');
            $recipient = $this->buildRecallRecipient();
            $subject = get_lang('Survey Recall');
            
            $message = new MessageToSend(claro_get_current_user_id(),$subject,$messageBody);
            $message->setCourse(claro_get_current_course_id());
            $message->setTools('LVSURVEY');             
            $recipient->sendMessage($message);
            
            $this->emailBody = $messageBody;
            parent::success('Message successfully sent');
        }
        catch (Exception $e)
        {
            parent::error($e->getMessage());
        }
    }
    
    private function buildRecallRecipient()
    {
        $recipient = new UserListRecipient();
        $recipientsIdList = $this->buildInCourseNotParticipantIdList();
        $recipient->addUserIdList($recipientsIdList);
        return $recipient;
    }
    
    private function buildInCourseNotParticipantIdList()
    {
        $inCourseNotParticipantList = $this->getInCourseNotParticipantsList();
        $result = array();
        foreach($inCourseNotParticipantList as $recipient)
        {
            $result[] = $recipient['id'];
        }
        return $result;
    }
    
    protected function render()
    {
        $survey = parent::getSurvey();
        $participantsMap = $this->getParticipationMap();
        $showParticipationTpl = new PhpTemplate(dirname(__FILE__).'/templates/show_participation.tpl.php');
        $showParticipationTpl->assign('survey', $survey);
        $showParticipationTpl->assign('participantsMap', $participantsMap);
        $showParticipationTpl->assign('emailBody', $this->emailBody);
        return $showParticipationTpl->render();
    }
    
    protected function defineBreadCrumb()
    {
        parent::defineBreadCrumb();
        parent::appendBreadCrumbElement(get_lang('Results'), "show_results.php?surveyId={$this->getSurvey()->id}");
        parent::appendBreadCrumbElement(get_lang('Participations'));
    }
    
    private function buildParticipationMap()
    {
        $surveyParticipantsList = $this->getSurveyParticipantList();
        $courseParticipantList = $this->getCourseParticipantList();
        $courseParticipantIdList = $this->getCourseParticipantIdList($courseParticipantList);
        
        $inCourseParticipantList = array();
        $offCourseParticipantList = array();
        $inCourseNotParticipantList = $courseParticipantList;
        
        foreach($surveyParticipantsList as $participant)
        {
            $participantId = $participant['id'];
            if(in_array($participantId,$courseParticipantIdList))
            {
                $inCourseParticipantList[] = $participant;
            }
            else
            {
                $offCourseParticipantList[] = $participant;
            }
            
            $key = array_search($participant,$inCourseNotParticipantList);
            unset($inCourseNotParticipantList[$key]);
        }
        
        return array(
            self::SURVEY_PARTICIPANTS           => $surveyParticipantsList, 
            self::IN_COURSE_PARTICIPANTS        => $inCourseParticipantList, 
            self::OFF_COURSE_PARTICIPANTS       => $offCourseParticipantList, 
            self::IN_COURSE_NOT_PARTICIPANTS    => $inCourseNotParticipantList 
        );
        
    }   
    
    private function getSurveyParticipantList()
    {
        $survey = parent::getSurvey();
        $participationList = $survey->getParticipationList();
        $allParticipantsList = array();
        foreach($participationList as $participation)
        {
            $user = $participation->getUser();
            $id = $user->userId;
            $firstName = $user->firstName;
            $lastName = $user->lastName;
            
            $participant = array(
                'id'        => $id, 
                'firstName' => $firstName, 
                'lastName'  => $lastName
            );
            $allParticipantsList[] = $participant;
        }
        return $allParticipantsList;
    }
    
    private function getCourseParticipantList()
    {
        $survey = parent::getSurvey();
        $courseUsersRowSet = claro_get_course_user_list($survey->getCourseId());
        $courseUserList = array();
        foreach($courseUsersRowSet as $courseUsersRow)
        {
            $id = $courseUsersRow['user_id'];
            $firstName = $courseUsersRow['prenom'];
            $lastName = $courseUsersRow['nom'];
            $courseUser = array(
                'id'        => $id, 
                'firstName' => $firstName, 
                'lastName'  => $lastName
            );
            $courseUserList[] = $courseUser;
        }
        return $courseUserList;
    }
    
    private function getCourseParticipantIdList($courseParticipantList)
    {
        $courseParticipantIdList = array();
        foreach($courseParticipantList as $courseParticipant)
        {
            $courseParticipantIdList[] = $courseParticipant['id'];
        }
        return $courseParticipantIdList;
    }
    
    private function getParticipationMap()
    {
        return $this->participationMap;
    }
    
    private function getInCourseNotParticipantsList()
    {
        $map =  $this->getParticipationMap();
        return $map[self::IN_COURSE_NOT_PARTICIPANTS];
    }
}

$page = new ShowParticipationPage();
$page->execute();