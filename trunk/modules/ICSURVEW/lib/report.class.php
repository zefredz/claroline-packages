<?php

class ICSURVEW_Report
{
    const EXPORT_XML = 'xml';
    const EXPORT_JSON = 'json';
    
    protected $surveyId;
    protected $surveyTitle;
    
    protected $courseCount = 0;
    protected $userCount = 0;
    
    protected $questionList = array();
    
    protected $courseStat = array();
    protected $userStat = array();
    
    public function __construct( $surveyId )
    {
        $this->surveyId = $surveyId;
        $this->tbl = get_module_main_tbl( array(  'ICSURVEW_survey'
                                                , 'ICSURVEW_question'
                                                , 'ICSURVEW_choice'
                                                , 'ICSURVEW_answer' ) );
        $this->load();
    }
    
    public function load()
    {
        $this->_loadSurvey();
        $this->_globalStat();
        $this->_result();
    }
    
    private function _loadSurvey()
    {
        $this->surveyTitle = Claroline::getDatabase()->query( "
            SELECT
                title
            FROM
                `{$this->tbl['ICSURVEW_survey']}`
            WHERE
                id = " . Claroline::getDatabase()->escape( $this->surveyId )
        )->fetch( Database_ResultSet::FETCH_VALUE );
        
        $result = Claroline::getDatabase()->query( "
            SELECT
                id,
                question
            FROM
                `{$this->tbl['ICSURVEW_question']}`
            WHERE
                survey_id = " . Claroline::getDatabase()->escape( $this->surveyId ) );
        
        foreach( $result as $line )
        {
            $this->questionList[ $line['id'] ]['question'] = $line['question'];
        }
        
        $result = Claroline::getDatabase()->query( "
            SELECT
                id,
                question_id,
                choice
            FROM
                `{$this->tbl['ICSURVEW_choice']}`
            WHERE
                question_id IN (" . implode( ',' , array_keys( $this->questionList ) )
                                  . ")" );
        
        foreach( $result as $line )
        {
            $this->questionList[ $line['question_id'] ]['choices'][ $line['id'] ] = $line['choice'];
        }
    }
    
    private function _globalStat()
    {
        $this->courseCount = Claroline::getDatabase()->query( "
            SELECT
                COUNT( DISTINCT course_id )
            FROM
                `{$this->tbl['ICSURVEW_answer']}`"
        )->fetch( Database_ResultSet::FETCH_VALUE );
        
        $this->userCount = Claroline::getDatabase()->query( "
            SELECT
                COUNT( DISTINCT user_id )
            FROM
                `{$this->tbl['ICSURVEW_answer']}`"
        )->fetch( Database_ResultSet::FETCH_VALUE );
    }
    
    private function _result()
    {
        foreach( $this->questionList as $questionId => $questionData )
        {
            $choiceIdList = array_keys( $questionData['choices'] );
            $this->_statQuestion( $questionId , $choiceIdList );
        }
    }
    
    private function _statQuestion( $questionId , $choiceIdList )
    {
        foreach( $choiceIdList as $choiceId )
        {
            $this->_statChoice( $questionId , $choiceId );
        }
    }
    
    private function _statChoice( $questionId , $choiceId )
    {
        $result = Claroline::getDatabase()->query( "
            SELECT
                COUNT( DISTINCT( user_id ) ) as user_count,
                COUNT( DISTINCT( course_id ) ) as course_count
            FROM
                `{$this->tbl['ICSURVEW_answer']}`
            WHERE
                question_id = " . Claroline::getDatabase()->escape( $questionId ) ."
            AND
                choice_id = " . Claroline::getDatabase()->escape( $choiceId )
        )->fetch( Database_ResultSet::FETCH_ASSOC );
        
        $this->courseStat[ $questionId ][ $choiceId ] = $result['course_count'];
        $this->userStat[ $questionId ][ $choiceId ] = $result['user_count'];
    }
    
    private function _jsonExport()
    {
        $result = array( 'title' => $this->surveyTitle,
                         'questionnaire' => $this->questionList,
                         'user_count' => $this->userCount,
                         'course_count' => $this->courseCount,
                         'user_stat' => $this->userStat,
                         'course_stat' => $this->courseStat );
        
        return json_encode( $result );
    }
    
    private function _xmlExport()
    {
        $xml  = '<?xml version="1.0"?>' . "\n";
        $xml .= '<SurveyReport>' ."\n";
        $xml .= '    <questionnaire>' . "\n";
        
        foreach( $this->questionList as $questionId => $question )
        {
            $xml .= '        <question>' . "\n";
            $xml .= '            <id>' . $questionId . '</id>' . "\n";
            $xml .= '            <text>' . $question['question'] . '</text>' . "\n";
            $xml .= '            <choices>' . "\n";
            
            foreach( $question['choices'] as $choiceId => $choice )
            {
                $xml .= '                <choice>' . "\n";
                $xml .= '                    <id>' . $choiceId . '</id>' . "\n";
                $xml .= '                    <text>' . $choice . '</text>' . "\n";
                $xml .= '                </choice>' . "\n";
            }
            
            $xml .= '            </choices>' . "\n";
            $xml .= '        </question>' . "\n";
        }
        
        $xml .= '    </questionnaire>' . "\n";
        $xml .= '    <report>' . "\n";
        $xml .= '        <userCount>' . $this->userCount . '</userCount>' . "\n";
        $xml .= '        <courseCount>' . $this->courseCount . '</courseCount>' . "\n";
        $xml .= '        <userStat>' . "\n";
        
        foreach( $this->userStat as $question )
        {
            foreach( $question as $choiceId => $count )
            {
                $xml .= '            <item>' . "\n";
                $xml .= '                   <choiceId>' . $choiceId . '</choiceId>' . "\n";
                $xml .= '                   <answerCount>' . $count . '</answerCount>' . "\n";
                $xml .= '            </item>' . "\n";
            }
        }
        
        $xml .= '        </userStat>' . "\n";
        $xml .= '        <courseStat>' . "\n";
        
        foreach( $this->courseStat as $question )
        {
            foreach( $question as $choiceId => $count )
            {
                $xml .= '            <item>' . "\n";
                $xml .= '                   <choiceId>' . $choiceId . '</choiceId>' . "\n";
                $xml .= '                   <answerCount>' . $count . '</answerCount>' . "\n";
                $xml .= '            </item>' . "\n";
            }
        }
        
        $xml .= '        </courseStat>' . "\n";
        $xml .= '    </report>' . "\n";
        $xml .= '</SurveyReport>';
        
        return $xml;
    }
    
    public function export( $type = self::EXPORT_XML )
    {
        if( $type == self::EXPORT_JSON)
        {
            $content = $this->_jsonExport();
        }
        elseif( $type == self::EXPORT_XML )
        {
            $content = $this->_xmlExport();
        }
        else
        {
            throw new exception( 'Invalid argument' );
        }
        
        header('Content-type: application/' . $type );
        header('Content-Disposition: attachment; filename=report_survey' . $this->surveyId . '.' . $type );
        echo $content;
        exit();
    }
}