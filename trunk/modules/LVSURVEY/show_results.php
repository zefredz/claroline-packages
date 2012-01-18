<?php
global $tlabelReq;
$tlabelReq = 'LVSURVEY';

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';
From::module('LVSURVEY')->uses( 'model/survey.class', 
                                'model/result.class', 
                                'model/csvResults.class', 
                                'controller/surveyPage.class');


class ShowResultsPage extends SurveyPage{
    
    private static $AUTHORIZED_FORMATS = array('HTML', 'SyntheticCSV', 'RawCSV', 'PerUserCSV');
    private $renderResetConf = false;
    
    public function performReset(){
        if(isset($_REQUEST['claroFormId'])){
            $this->doResetResults();
        }else{
            $this->renderResetConf = true;
        }
    }
        
    protected function render(){
        if($this->renderResetConf){
            return $this->renderResetConfirm();
        }

        $format = $this->getRequestedFormat();
        return $this->renderResults($format);
    }
    
    protected function defineBreadCrumb(){
        parent::defineBreadCrumb();
        parent::appendBreadCrumbElement(get_lang('Results'));
    }
    
    private function doResetResults(){
        $survey = parent::getSurvey();
        try
        {
            $survey->reset();
            parent::success('Results have been deleted');
        }catch(Exception $e){
            parent::error($e->getMessage());
        }
    }
    
    private function renderResetConfirm(){
        $survey = parent::getSurvey();
        $confirmationTpl = new PhpTemplate(dirname(__FILE__).'/templates/reset_survey_conf.tpl.php');
        $confirmationTpl->assign('survey', $survey);
        return $confirmationTpl->render();
    }
    
    private function getRequestedFormat()
    {
        $format = 'HTML';       
        if (isset($_REQUEST['format']))
        {
            $requestedFormat = $_REQUEST['format'];
            if (in_array($requestedFormat,self::$AUTHORIZED_FORMATS)) 
                $format = $requestedFormat;
        }
        return $format;
    }
    
    private function renderResults($format)
    {
        switch($format)
        {
            case 'SyntheticCSV' :
                $this->sendSyntheticCSVResultsAndDie();
                break;
            case 'RawCSV' :
                $this->sendRawCSVResultsAndDie();
                break;
            case 'PerUserCSV' :
                $this->sendPerUserCSVResultsAndDie();
                break;
            default:
            case 'HTML' :
                return $this->renderHTMLResults();  
        }
    }
    
    private function sendSyntheticCSVResultsAndDie()
    {
        $survey = parent::getSurvey();
        $csvData = new SyntheticResults($survey);
        $this->sendCSVAndDie($csvData);
    }
    
    private function sendRawCSVResultsAndDie()
    {
        $survey = parent::getSurvey();
        
        if($survey->is_anonymous)
        {
            $csvData= new AnonymousCSVResults($survey);
        }
        else
        {
            $csvData = new NamedCSVResults($survey);
        }
        
        $this->sendCSVAndDie($csvData);
    }
    
    private function sendPerUserCSVResultsAndDie()
    {
        $survey = parent::getSurvey();
        $csvData = new PerUserCSVResults($survey);
        $this->sendCSVAndDie($csvData);
    }
    
    private function sendCSVAndDie($csvResults)
    {
        $survey = parent::getSurvey();
        $csvResults->buildRecords();
        header("Content-type: application/csv");
        header('Content-Disposition: attachment; filename="' . get_lang( 'survey-results' ) .$survey->id.'.csv"');
        echo $csvResults->export();
        die();
    }
    
    private function renderHTMLResults()
    {
        $survey = parent::getSurvey();
        
        if(!claro_is_allowed_to_edit() && !$survey->areResultsVisibleNow())
        {
            $this->notAllowed();
            return '';
        }
        
        $showResultsTpl = new PhpTemplate(dirname(__FILE__).'/templates/show_results.tpl.php');
        $showResultsTpl->assign('survey', $survey);
        $showResultsTpl->assign('editMode', claro_is_allowed_to_edit());
        
        return $showResultsTpl->render();
    }
    
    private function notAllowed(){
        $survey = parent::getSurvey();
        parent::error('You are not allowed to see these results.');
        if($survey->resultsVisibility == 'VISIBLE_AT_END')
        {           
            if(is_null($survey->endDate))
            {
                parent::info('Results will be visible only at the end of the survey.');
            }
            else
            {
                $message = 'Results will be visible only at the end of the survey on %date.';
                $params =  array(
                    '%date' => claro_html_localised_date(
                        get_locale('dateFormatLong'), 
                        $survey->endDate
                    )
                );
                parent::info($message,$params);
            }
        }
    }
}

$page = new ShowResultsPage();
$page->execute();