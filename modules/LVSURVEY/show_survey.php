<?php 
global $tlabelReq;
$tlabelReq = 'LVSURVEY';


require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';
From::module('LVSURVEY')->uses('controller/surveyPage.class');


class ShowSurveyPage extends SurveyPage
{
    private function getMandatorySurveyLineId(){
        $input = Claro_UserInput::getInstance();
        return $input->getMandatory('surveyLineId');
    }
    
    public function performLineMoveUp(){
        $lineId = $this->getMandatorySurveyLineId();
        $this->moveLine($lineId, true );
    }

    public function performLineMoveDown(){
        $lineId = $this->getMandatorySurveyLineId();
        $this->moveLine($lineId, false );
    }
    public function performLineRemove(){
        $lineId = $this->getMandatorySurveyLineId();
        $this->removeLine($lineId );
    }
    public function performSetCommentSize(){
        $lineId = $this->getMandatorySurveyLineId();
        $input = Claro_UserInput::getInstance();
        $newCommentSize =  $input->getMandatory('commentSize');
        $this->setCommentSize($lineId, $newCommentSize);
    }
    
    public function performSaveParticipation(){
            if(isset($_REQUEST['claroFormId']))
            {
                $this->processForm();
            }
    }
    
    protected function defineBreadCrumb()
    {
        parent::defineBreadCrumb();
        parent::appendBreadCrumbElement(get_lang('Display survey'));
    }
    
    private function processForm(){
            try
            {
                $participation = Participation::loadFromForm();
                if(!$participation->isValid())
                {
                    throw new Exception('Cannot save participation, you might have forgotten required answers');
                }
                $participation->save();
                $this->redirectToResultsIfPossible();
                parent::success('Participation saved');
            }
            catch(Exception $e)
            {
                    parent::error($e->getMessage());
            }
    }
    
    private function redirectToResultsIfPossible(){
        $survey = parent::getSurvey();
        if($survey->areResultsVisibleNow())
        {
            claro_redirect('show_results.php?surveyId='.$survey->id);
            die();
        }
    }
    
    private function removeLine($surveyLineId)
    {
        $survey = parent::getSurvey();
        try
        {
            $survey->removeLine($surveyLineId);
            parent::success("Question removed from Survey");
        }
        catch(Exception $e)
        {
            parent::error($e->getMessage());
        }
    }
    
    private function moveLine($surveyLineId, $up )
    {
        $survey = parent::getSurvey();
        try
        {
            $survey->moveLine($surveyLineId, $up);
        }
        catch (Exception $e)
        {
            parent::error($e->getMessage());
        }
    }
    
    private function setCommentSize($surveyLineId, $newCommentSize)
    {
        $survey = parent::getSurvey();
        
        if($newCommentSize < 0)
        {
            $newCommentSize = 0;
        }
        
        if($newCommentSize > 200)
        {
            $newCommentSize = 200;
        }
        
        $surveyLineList = $survey->getSurveyLineList();
        $surveyLineList[$surveyLineId]->maxCommentSize = $newCommentSize;
        $surveyLineList[$surveyLineId]->save();
    }
    
    public function render()
    {
        $survey = parent::getSurvey();
        $editMode = claro_is_allowed_to_edit();
        
        if(!$editMode && !$survey->isAccessible())
        {
            parent::error('This survey is not accessible');
            return '';
        }
        
        if(claro_is_user_authenticated())
        {
            $participation = Participation::loadParticipationOfUserForSurvey(claro_get_current_user_id(), $survey->id);
        }
        else
        {
            $participation = new Participation($survey->id, null);
        }
        
        $showSurveyTpl = new PhpTemplate(dirname(__FILE__).'/templates/show_survey.tpl.php');
        $showSurveyTpl->assign('survey', $survey);
        $showSurveyTpl->assign('participation', $participation);
        $showSurveyTpl->assign('editMode', claro_is_allowed_to_edit());
        
        return $showSurveyTpl->render();
    }
}

$page = new ShowSurveyPage();
$page->execute();