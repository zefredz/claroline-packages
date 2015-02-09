<?php // $Id$

/**
 * Moodle Resource Exporter
 *
 * @version     MOODLEEX 1.0 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2015 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     MOODLEEX
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class MoodleQuestion {
    const CORRECT_DEFAULT_FEEDBACK = 'Correct!';
    const WRONG_DEFAULT_FEEDBACK = 'Wrong answer!';
    
    public $clarolineType;
    public $moodleType;
    public $subType;
    public $template;
    public $title;
    public $description;
    public $attachment;
    public $grade;
    public $penalty;
    public $correctDefaultFeedback;
    public $wrongDefaultFeedback;
    
    public $optionList;
    public $answerList;
    
    private $answerData;
    private $attachmentData;
    
    static public $answer_db_table_list = array(
        'MCUA'     => 'multiple_choice',
        'MCMA'     => 'multiple_choice',
        'TF'       => 'truefalse',
        'FIB'      => 'fib',
        'MATCHING' => 'matching',
    );
    
    static public $answer_field_list = array(
        'multiple_choice' => 'answer,correct,grade,comment',
        'truefalse'       => 'trueFeedback,trueGrade,falseFeedback,falseGrade,correctAnswer',
        'fib'             => 'answer,gradeList,wrongAnswerList,type',
        'matching'        => 'answer,match,grade,code',
    );
    
    /*static public $claroline_to_moodle_quiz = array(
        'MCUA'     => 'multichoice;single=true',
        'MCMA'     => 'multichoice;single=false',
        'TF'       => 'truefalse',
        'MATCHING' => 'matching',
        'FIB'      => 'type==1:cloze,type==2:gapselect',
    );*/
    
    /**
     * Constructor
     * @param int $id : the question's id
     */
    public function __construct( $id , $cdf = null , $wdf = null )
    {
        $this->id = $id;
        
        $this->optionList = array();
        $this->correctDefaultFeedback = is_null( $cdf ) ? self::CORRECT_DEFAULT_FEEDBACK : $cdf;
        $this->wrongDefaultFeedback = is_null( $wdf ) ? self::WRONG_DEFAULT_FEEDBACK : $wdf;
        
        $this->load();
        
    }
    
    /**
     * Loads question's datas
     * @return void
     */
    private function load()
    {
        $tbl = get_module_course_tbl ( array ( 'qwz_question' ) );
        
        $data = Claroline::getDatabase()->query(
            "SELECT
                title, description, attachment, type, grade
            FROM
                `{$tbl['qwz_question']}`
            WHERE
                id = " . Claroline::getDatabase()->escape( $this->id )
        )->fetch();
        
        if( ! empty( $data ) )
        {
            $this->clarolineType = $data[ 'type' ];
            $this->title = $data['title'];
            $this->description = $data['description'];
            $this->attachment = $data['attachment'];
            $this->grade = (int)$data['grade'];
            $this->penalty = 0;
            
            $tableName = self::$answer_db_table_list[ $this->clarolineType ];
            $answerFieldList = explode( ',' , self::$answer_field_list[ $tableName ] );
            $tbl = get_module_course_tbl( array( 'qwz_answer_' . $tableName ) );
            
            $answerData = Claroline::getDatabase()->query(
                "SELECT
                    `id`,`" . implode( '`,`' , $answerFieldList ) . "`
                FROM
                    `{$tbl[ 'qwz_answer_' . $tableName ]}`
                WHERE
                    questionId = " . Claroline::getDatabase()->escape( $this->id )
            );
            
            if( $answerData->numRows() == 1 )
            {
                $answerData = $answerData->fetch( Database_ResultSet::FETCH_ASSOC );
            }
            
            $this->answerData = $answerData;
            $this->answerList = array();
            
            if( method_exists( $this , $this->clarolineType ) )
            {
                $this->{$this->clarolineType}();
            }
            else
            {
                throw new Exception( 'Invalid question type' );
            }
            
            if( MOODLEEX_is_image( $this->attachment ) )
            {
                $filePath = get_conf ( 'rootWeb' )
                    . 'courses/'
                    . claro_get_current_course_id ()
                    . '/exercise/question_'
                    . $this->id
                    . '/'
                    . $this->attachment;
                    
                $this->attachmentData = base64_encode( file_get_contents( $filePath ) );
                
                $this->description .= '<br /><img alt="'
                    . $this->attachment
                    . '" src="data:image/'
                    . MOODLEEX_getFileExtension( $this->attachment )
                    . ';base64,'
                    . $this->attachmentData
                    . '" />';
            }
        }
        else
        {
            throw new Exception( 'invalid id' );
        }
    }
    
    /**
     * Renders the questions data in xml form
     * @return string : a string with xml datas
     */
    public function render()
    {
        $template = new ModuleTemplate( 'MOODLEEX' , 'question.tpl.php' );
        $template->assign( 'question' , $this );
        
        return $template->render();
    }
    
    /**
     * Formats datas for MCUA
     * @return void
     */
    private function MCUA()
    {
        $this->moodleType = 'multichoice';
        $this->optionList[ 'single' ] = 'true';
        $this->multichoice();
    }
    
    /**
     * Formats datas for MCMA
     * @return void
     */
    private function MCMA()
    {
        $this->moodleType = 'multichoice';
        $this->optionList[ 'single' ] = 'false';
        $this->multichoice();
    }
    
    /**
     * Converts datas from Claroline's MCUA/MCMA answers to Moodle format
     * @return void
     */
    private function multichoice()
    {
        foreach( $this->answerData as $answer )
        {
            $grade = (int)$answer[ 'grade' ];
            
            if( $answer[ 'correct'] == '1' )
            {
                $fraction = $grade / $this->grade;
            }
            else
            {
                $fraction = (-1) * ( abs( $grade ) / $this->grade );
                $this->penalty += $fraction;
            }
            
            $this->answerList[] = array(
                'content' => $answer[ 'answer' ],
                'feedback' => $answer[ 'comment' ],
                'fraction' => $fraction,
            );
        }
    }
    
    /**
     * Converts datas from Claroline's TF answers to Moodle format
     * @return void
     */
    private function TF()
    {
        $this->moodleType = 'truefalse';
        $this->penalty = (-1) * $this->grade;
        
        $trueAnswer = ( $this->answerData[ 'correctAnswer' ] == 'TRUE' ) ? 1 : -1;
        
        $this->answerList = array(
            'true' => array(
                'feedback' => $this->answerData[ 'trueFeedback' ],
                'fraction' => abs( (int)$this->answerData[ 'trueGrade'] ) * $trueAnswer
            ),
            'false' => array(
                'feedback' => $this->answerData[ 'falseFeedback' ],
                'fraction' => abs( (int)$this->answerData[ 'falseGrade' ] ) * $trueAnswer * (-1)
            )
        );
    }
    
    /**
     * Converts datas from Claroline's MATCHING answers to Moodle format
     * @return void
     */
    private function MATCHING()
    {
        $this->moodleType = 'matching';
        
        foreach( $this->answerData as $answer )
        {
            if( ! is_null( $answer[ 'match' ] ) )
            {
                $this->answerList[ $answer[ 'match' ] ][ 'proposition' ] = $answer[ 'answer' ];
            }
            else
            {
                $this->answerList[ $answer[ 'code' ] ][ 'answer' ] = $answer[ 'answer' ];
                
                if( ! isset( $this->answerList[ $answer[ 'code' ] ][ 'proposition' ] ) )
                {
                    $this->answerList[ $answer[ 'code' ] ][ 'proposition' ] = '';
                }
            }
        }
    }
    
    /**
     * Converts datas from Claroline's FIB answers to Moodle format
     * @return void
     */
    private function FIB()
    {
        $this->moodleType = (int)$this->answerData[ 'type' ] == 2 ? 'gapselect' : 'cloze';
        
        $answerText = $this->answerData[ 'answer' ];
        
        $gradeList = explode( ',' , $this->answerData[ 'gradeList' ] );
        $wrongList = explode( ',' , $this->answerData[ 'wrongAnswerList' ] );
        
        $this->grade = array_sum( $gradeList );
        
        preg_match_all( '/\[([^]]*)\]/' , $this->answerData[ 'answer' ] , $answerList );
        
        foreach( $answerList[ 0 ] as $index => $option )
        {
            $this->answerList[ $index + 1 ][ 'option' ] = trim( $option , '[]' );
            $this->answerList[ $index + 1 ][ 'fraction' ] = (int)$gradeList[ $index ] / $this->grade;
            
            //$this->answerList[ $index + 1 ] = trim( $option , '[]' );
            
            if( $this->moodleType == 'cloze' )
            {
                // Moodle question type : cloze
                $answerText = str_replace(
                    $option ,
                    '{1:SHORTANSWER:=' . trim( $option , '[]' ) . '#'
                    . get_lang( $this->correctDefaultFeedback ) . '~*#'
                    . get_lang( $this->wrongDefaultFeedback ) . '}' ,
                    $answerText );
            }
            elseif( $this->moodleType == 'gapselect' )
            {
                // Moodle question type : gapselect
                $answerText = str_replace(
                    $option ,
                    '[' . (string)( $index + 1 ) . ']' ,
                    $answerText );
            }
            else
            {
                throw new Exception( 'Wrong question type' );
            }
        }
        
        if( ! empty( $wrongList ) )
        {
            //$this->answerList = array_merge( $this->answerList , $wrongList );
            foreach( $wrongList as $wrongAnswer )
            {
                $this->answerList[] = array( 'option' => $wrongAnswer ,
                                             'fraction' => '0' );
            }
        }
        
        $this->description = MOODLEEX_clear( $this->description )
            . "\n\n"
            . MOODLEEX_clear( $answerText );
    }
}
