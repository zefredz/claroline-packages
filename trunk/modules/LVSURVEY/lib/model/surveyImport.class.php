<?php

From::module('LVSURVEY')->uses( 'util/surveyConstants.class',
                                'model/question.class',
                                'model/surveyLine.class' );

class SurveyImport
{
    protected $courseId;
    protected $surveyId;
    
    public function __construct( $courseId )
    {
        $this->courseId = $courseId;
    }
    
    public function import( $surveyData )
    {
        $this->surveyId = $this->surveySave( $surveyData[ 'title' ]
                                           , $surveyData[ 'description' ] );
        
        $rank = 0;
        $insertedLineCount = 0;
        
        foreach( $surveyData[ 'questionList' ] as $line )
        {
            $lineId = $this->addLine( ++$rank );
            
            if( $line[ 'type' ] == SurveyLine::TYPE_SEPARATOR )
            {
                self::addSeparatorLine( $line[ 'title' ] , $line[ 'description' ] , $lineId );
            }
            else
            {
                $text = $line[ 'text' ];
                $type = $line[ 'type' ];
                
                $questionId = self::questionExists( $text , $type );
                
                if ( ! $questionId )
                {
                    $questionId = self::addNewQuestion( $text , $type , $line[ 'choiceList' ] );
                }
                
                self::addQuestionLine( $questionId , $lineId );
            }
            
            $insertedLineCount++;
        }
        
        return $insertedLineCount;
    }
    
    protected function surveySave( $title , $description )
    {
        Claroline::getDatabase()->exec( "
            INSERT INTO
                `" . SurveyConstants::$SURVEY_TBL . "`
            SET
                courseId = " . Claroline::getDatabase()->quote( $this->courseId ) . ",
                title = " . Claroline::getDatabase()->quote( $title ) . ",
                description = " . Claroline::getDatabase()->quote( $description ) );
        
        return Claroline::getDatabase()->insertId();
    }
    
    protected function addLine( $rank )
    {
        Claroline::getDatabase()->exec( "
            INSERT INTO
                `" . SurveyConstants::$SURVEY_LINE_TBL . "`
            SET
                surveyId = " . Claroline::getDatabase()->escape( $this->surveyId ) . ",
                rank = " . Claroline::getDatabase()->escape( $rank ) );
        
        return Claroline::getDatabase()->insertId();
    }
    
    private static function questionExists( $text , $type )
    {
        if( ! in_array( $type , Question::$VALID_QUESTION_TYPES ) )
        {
            throw new Exception( 'Invalid type' );
        }
        
        return Claroline::getDatabase()->query( "
            SELECT id FROM `" . SurveyConstants::$QUESTION_TBL . "`
            WHERE text = " . Claroline::getDatabase()->quote( $text ) . "
            AND type = " . Claroline::getDatabase()->quote( $type )
        )->fetch( Database_ResultSet::FETCH_VALUE );
    }
    
    private static function addSeparatorLine( $title , $description , $lineId )
    {
        Claroline::getDatabase()->exec( "
            INSERT INTO
                `" . SurveyConstants::$SURVEY_LINE_SEPARATOR_TBL . "`
            SET
                id = " . Claroline::getDatabase()->escape( $lineId ) . ",
                title = " . Claroline::getDatabase()->quote( $title ) . ",
                description = " . Claroline::getDatabase()->quote( $description ) );
        
        return Claroline::getDatabase()->insertId();
    }
    
    private static function addQuestionLine( $questionId , $lineId )
    {
        return Claroline::getDatabase()->exec( "
            INSERT INTO
                `" . SurveyConstants::$SURVEY_LINE_QUESTION_TBL . "`
            SET
                id = " . Claroline::getDatabase()->escape( $lineId ) . ",
                questionId = " . Claroline::getDatabase()->escape( $questionId ) );
    }
    
    private static function addNewQuestion( $text , $type , $choiceList )
    {
        Claroline::getDatabase()->exec( "
            INSERT INTO
                `" . SurveyConstants::$QUESTION_TBL . "`
            SET
                text = " . Claroline::getDatabase()->quote( $text ) . ",
                type = " . Claroline::getDatabase()->quote( $type ) . ",
                author_id = " . Claroline::getDatabase()->escape( claro_get_current_user_id() ) );
        
        $questionId = Claroline::getDatabase()->insertId();
        
        foreach( $choiceList as $choice )
        {
            Claroline::getDatabase()->exec( "
                INSERT INTO
                    `" . SurveyConstants::$CHOICE_TBL . "`
                SET
                    text = " . Claroline::getDatabase()->quote( $choice[ 'text' ] ) . ",
                    questionId = " . Claroline::getDatabase()->escape( $questionId ) );
            
            $choiceId = Claroline::getDatabase()->insertId();
            
            if ( $choiceList[ 'optionList' ] )
            {
                foreach( $choice[ 'optionList' ] as $option )
                {
                    Claroline::getDatabase()->exec( "
                        INSERT INTO
                    SET
                            `" . SurveyConstants::$OPTION_TBL . "`
                            text = " . Claroline::getDatabase()->quote( $choice[ 'text' ] ) . ",
                            choiceId = " . Claroline::getDatabase()->escape( $choiceId ) );
                }
            }
            
            return $questionId;
        }
    }
}