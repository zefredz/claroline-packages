<?php
global $tlabelReq;
$tlabelReq = 'LVSURVEY';

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';
From::module('LVSURVEY')->uses( 'model/survey.class',
                                'model/questionLine.class',
                                'model/separatorLine.class' );

class SurveyExport
{
    //const TYPE_QUESTION = 'question';
    const TYPE_SEPARATOR = 'separator';
    
    private $surveyId;
    private $showSuccessBox = false;
    
    public function __construct( $surveyId )
    {
        $this->surveyId = $surveyId;
        $this->survey = Survey::load( $this->surveyId );
    }
    
    private function buildJson()
    {
        $surveyData = array();
        $surveyData[ 'title' ] = $this->survey->title;
        $surveyData[ 'description' ] = utf8_encode( $this->survey->description );
        
        $questionList = array();
        
        foreach( $this->survey->getSurveyLineList() as $surveyLine )
        {
            $lineData = array();
            
            if( is_a( $surveyLine , 'QuestionLine' ) )
            {
                $lineData[ 'type' ] = $surveyLine->question->type;
                $lineData[ 'text' ] = utf8_encode( $surveyLine->question->text );
                
                if( $surveyLine->question->type != 'OPEN' )
                {
                foreach( $surveyLine->question->getChoiceList() as $choice )
                {
                    $lineData[ 'choiceList' ][] = array( 'text' => utf8_encode( $choice->text )
                                                       , 'optionList' => claro_utf8_encode_array( $choice->getOptionList() ) );
                }
            }
            }
            elseif( is_a( $surveyLine , 'SeparatorLine' ) )
            {
                $lineData[ 'type' ] = self::TYPE_SEPARATOR;
                $lineData[ 'title' ] = utf8_encode( $surveyLine->title );
                $lineData[ 'description' ] = utf8_encode( $surveyLine->description );
            }
            else
            {
                throw new exception( 'Invalid line' );
            }
            
            $questionList[] = $lineData;
        }
        
        $surveyData[ 'questionList' ] = $questionList;
        
        return json_encode( $surveyData );
    }
    
    private function sendFile( $content , $fileName )
    {
        header("Content-type: text/x-json");
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        echo $content;
        exit();
    }
    
    public function execute()
    {
        $this->sendFile( $this->buildJson() , $this->survey->title . '.cls' );
    }
}

$input = Claro_UserInput::getInstance();
$surveyId = (int)$input->get( 'surveyId', '-1' );
if( $surveyId > 0 )
{
    $export = new SurveyExport( $surveyId );
    $export->execute();
}