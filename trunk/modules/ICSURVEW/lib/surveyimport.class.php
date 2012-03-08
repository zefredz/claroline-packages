<?php

class ICSURVEW_SurveyImport
{
    public $errorMsg = null;
    
    public function __construct()
    {
        $this->tbl = get_module_main_tbl( array( 'ICSURVEW_question',
                                                 'ICSURVEW_choice',
                                                 'ICSURVEW_survey' ) );
    }
    
    public function get()
    {
        return $this->questionnaire;
    }
    
    public function import( $file , $title )
    {
        if( ! $title )
        {
            return $this->errorMsg = 'Missing title';
        }
        
        if( ! file_exists( $file ) )
        {
            return $this->errorMsg = 'Invalid argument';
        }
        
        $content = file_get_contents( $file );
        $data = json_decode( $content );
        
        if( ! $data )
        {
            return $this->errorMsg = 'Error while parsing Json datas';
        }
        
        $surveyId = $this->_insertSurvey( $title );
        
        if( ! $surveyId )
        {
            return $this->errorMsg = 'Error while writing datas in survey table';
        }
        
        foreach( $data as $question )
        {
            $questionId = $this->_insertQuestion( $question->question , $surveyId );
            
            if( ! $questionId )
            {
                return $this->errorMsg = 'Error while writing datas in question table';
            }
            
            if( ! $this->_insertChoiceList( $question->choices , $questionId ) )
            {
                return $this->errorMsg = 'Error while writing datas in choice table';
            }
        }
        
        return count( $data );
    }
    
    private function _insertSurvey( $title )
    {
        if( Claroline::getDatabase()->exec( "
            INSERT INTO
                `{$this->tbl['ICSURVEW_survey']}`
            SET
                title = " . Claroline::getDatabase()->quote( $title ) ) )
        {
            return Claroline::getDatabase()->insertId();
        }
    }
    
    private function _insertQuestion( $question , $surveyId )
    {
        if( Claroline::getDatabase()->exec( "
            INSERT INTO
                `{$this->tbl['ICSURVEW_question']}`
            SET
                survey_id = " . Claroline::getDatabase()->escape( $surveyId ) . ",
                question = " . Claroline::getDatabase()->quote( $question ) ) )
        {
            return Claroline::getDatabase()->insertId();
        }
    }
    
    private function _insertChoiceList( $choiceList , $questionId )
    {
        $sqlString = array();
        
        foreach( $choiceList as $choice )
        {
            $sqlString[] = '(' . $questionId . ',"' . $choice . '")';
        }
        
        $sqlString = implode( ",\n" , $sqlString );
        
        return Claroline::getDatabase()->exec( "
            INSERT INTO
                `{$this->tbl['ICSURVEW_choice']}` (question_id,choice)
            VALUES" . $sqlString );
    }
    
    public static function toJson_( $questionList )
    {
        return json_encode( claro_utf8_encode_array( $questionList ) );
    }
}
