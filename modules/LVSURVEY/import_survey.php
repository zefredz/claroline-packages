<?php
global $tlabelReq;
$tlabelReq = 'LVSURVEY';

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';
From::module('LVSURVEY')->uses( 'model/survey.class',
                                'controller/managerSurveyLessPage.class',
                                'util/surveyConstants.class', 
                                'model/surveyImport.class' );

class ImportSurveyPage extends ManagerSurveyLessPage
{
    const STATE_SUBMIT  = 'form';
    const STATE_SUCCESS = 'success';
    const STATE_ERROR   = 'error';
    
    protected static $defaultCmd = 'Submit';
    
    protected $dialogContent;
    protected $state;
    
    public function __construct( $courseId )
    {
        $this->surveyImport = new SurveyImport( $courseId );
        parent::__construct();
    }
    
    protected function performImport()
    {
        $this->state = self::STATE_ERROR;
        
        if( $_FILES && $_FILES[ 'uploadedFile' ][ 'size' ] != 0 )
        {
            $file = $_FILES[ 'uploadedFile' ];
        }
        else
        {
            $this->dialogContent = get_lang( 'File missing!' );
            return;
        }
        
        if( ! $surveyData = json_decode( file_get_contents( $file[ 'tmp_name' ] ) , true ) )
        {
            $this->dialogContent = get_lang( 'Corrupt file!' );
            return;
        }
        
        if ( ! array_key_exists( 'title' , $surveyData )
          || ! array_key_exists( 'description' , $surveyData )
          || ! array_key_exists( 'questionList' , $surveyData ) )
        {
            $this->dialogContent = get_lang( 'Missing or invalid datas!' );
            return;
        }
        
        if( ! $this->surveyImport->import( $surveyData ) )
        {
            $this->dialogContent = get_lang( 'An error occured during the import process!');
            return;
        }
        
        $this->dialogContent = get_lang( 'The survey has been succesfully imported and created!' );
        return $this->state = self::STATE_SUCCESS;
    }
    
    protected function performSubmit()
    {
        $content = '<form method="post" enctype="multipart/form-data" action="' . Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=import' ) . '">';
        $content .= '    <p>' . get_lang( 'Submit the file to import' ) . '</p>';
        $content .= '    <input type="file" name="uploadedFile" />';
        $content .= '    <input type="submit" value="Submit" />';
        $content .= '</form>';
        
        $this->dialogContent = $content;
        return $this->state = self::STATE_SUBMIT;
    }
    
    protected function render()
    {
        $dialogBox = new DialogBox();
        $dialogBox->{$this->state}( $this->dialogContent );
        return $dialogBox->render();
    }
}

$export = new ImportSurveyPage( claro_get_current_course_id() );
$export->execute();