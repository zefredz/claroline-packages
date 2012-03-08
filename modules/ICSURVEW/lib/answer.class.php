<?php

class ICSURVEW_Answer
{
    protected $userId;
    protected $questionList;
    protected $courseList;
    protected $answerList;
    protected $answeredNb;
    protected $itemNb;
    
    /**
     * Constructor
     * @param array $questionList : the submitted questionnaire
     * @param int $userId : the user's ID
     * @param Iterator $courseList : the user's courslist for which he is manager
     */
    public function __construct( $userId , $questionList )
    {
        $this->userId = (int)$userId;
        $this->questionList = $questionList;
        
        $this->tbl = get_module_main_tbl( array( 'ICSURVEW_log' , 'course' , 'rel_course_user' ) );
        $this->load();
    }
    
    /**
     * loads the answer
     * this method is called by the constructor
     */
    public function load()
    {
        $this->loadCourseList();
        $this->loadAnswerList();
    }
    
    /**
     * Verifies if user has answered to the survey
     * @return boolean
     */
    public function hasAnswered()
    {
        return $this->answeredNb == count( (array)$this->questionList ) * count( $this->courseList );
    }
    
    /**
     * Getter for $this->questionList
     */
    public function getQuestionList()
    {
        return $this->questionList;
    }
    
    /**
     * Getter for $this->courseList
     * @param array $filter
     * @return array $courseList
     */
    public function getCourseList( $filter = null )
    {
        if ( $filter )
        {
            $courseList = array();
            
            $sql = "SELECT
                        L.course_id
                    FROM
                        `{$this->tbl['ICSURVEW_log']}` AS L\n";
            $sql2 = "";
            
            foreach( $filter as $index => $cond )
            {
                $tblAlias = "L" . $index;
                
                $sql .= "INNER JOIN
                            `{$this->tbl['ICSURVEW_log']}` AS ".  $tblAlias . "
                        ON L.course_id = " . $tblAlias . ".course_id\n";
                $arg = array();
                
                foreach( $cond as $field => $value )
                {
                    if( substr( (string)$value , 0 , 1 ) == '!' )
                    {
                        $comp = " != ";
                        $value = (int)substr( $value , 1 );
                    }
                    else
                    {
                        $comp = " = ";
                    }
                    
                    $arg[] = $tblAlias . "." . $field . $comp . $value;
                }
                
                $sql2 .= "( " . implode( " AND " , $arg ) .")\n AND ";
            }
            
            $sql .= "WHERE\n" . $sql2;
            $sql .= "L.user_id = " . Claroline::getDatabase()->escape( $this->userId );
            
            $result = Claroline::getDatabase()->query( $sql );
            
            foreach( $result as $course )
            {
                $courseId = $course[ 'course_id' ];
                
                if ( array_key_exists( $courseId , $this->courseList ) )
                {
                    $courseList[ $courseId ] = $this->courseList[ $courseId ];
                }
            }
            
            return $courseList;
        }
        else
        {
            return $this->courseList;
        }
    }
    
    /**
     * Getter for $this->answerList
     */
    public function getAnswerList()
    {
        return $this->answerList;
    }
    
    /**
     * Gets the answer for a specific course and question
     */
    public function get( $courseId , $questionId )
    {
        if( isset( $this->answerList[ $courseId ][ $questionId ] ) )
        {
            return $this->answerList[ $courseId ][ $questionId ];
        }
    }
    
    /**
     * Set answer
     * @param string $courseId
     * @param int $questionId
     * @param int $choiceId
     */
    public function set( $courseId , $questionId , $choiceId )
    {
        if ( isset( $this->questionList[$questionId]['choice'][$choiceId] ) )
        {
            if( ! isset( $this->answerList[ $courseId ][ $questionId ] ) )
            {
                $this->answeredNb++;
            }
            
            if( $this->save( $courseId , $questionId , $choiceId ) )
            {
                return $this->answerList[ $courseId ][ $questionId ] = $choiceId;
            }
        }
    }
    
    /**
     * Saves answer
     * @return int $nbln (number of lines inserted/modified in database)
     */
    public function save( $courseId , $questionId , $choiceId )
    {
        if ( isset( $this->answerList[ $courseId ][ $questionId ] ) )
        {
            return Claroline::getDatabase()->exec( "
                UPDATE
                    `{$this->tbl['ICSURVEW_log']}`
                SET
                    choice_id = " . Claroline::getDatabase()->escape( $choiceId ) . "
                WHERE
                    user_id = " . Claroline::getDatabase()->escape( $this->userId ) . "
                AND
                    course_id = " . Claroline::getDatabase()->quote( $courseId ) . "
                AND
                    question_id = " . Claroline::getDatabase()->escape( $questionId ) );
        }
        else
        {
            return Claroline::getDatabase()->exec( "
                INSERT INTO
                    `{$this->tbl['ICSURVEW_log']}`
                SET
                    user_id = " . Claroline::getDatabase()->escape( $this->userId ) . ",
                    course_id = " . Claroline::getDatabase()->quote( $courseId ) . ",
                    question_id = " . Claroline::getDatabase()->escape( $questionId ) . ",
                    choice_id = " . Claroline::getDatabase()->escape( $choiceId ) );
        }
    }
    
    private function loadCourseList()
    {
        $result = Claroline::getDatabase()->query( "
            SELECT
                code AS id,
                administrativeNumber AS code,
                intitule AS title,
                titulaires AS manager
            FROM
                `{$this->tbl['course']}` AS C
            INNER JOIN
                `{$this->tbl['rel_course_user']}` AS U
            ON
                U.code_cours = C.code
            WHERE
                U.user_id =" . Claroline::getDatabase()->escape( $this->userId ) . "
            AND
                U.isCourseManager = TRUE" );
        
        foreach( $result as $line )
        {
            $this->courseList[ $line[ 'id' ] ] = array( 'code'    => $line[ 'code' ]
                                                      , 'title'   => $line[ 'title' ]
                                                      , 'manager' => $line[ 'manager' ] );
        }
    }
    
    private function loadAnswerList()
    {
        $result = Claroline::getDatabase()->query( "
            SELECT
                course_id,
                question_id,
                choice_id
            FROM
                `{$this->tbl['ICSURVEW_log']}`
            WHERE
                user_id = " . Claroline::getDatabase()->escape( $this->userId ) );
        
        $this->answerList = array();
        $this->answeredNb = 0;
        
        foreach( $result as $line )
        {
            $questionId = $line[ 'question_id' ];
            $courseId = $line[ 'course_id' ];
            
            if ( array_key_exists( $questionId , $this->questionList )
              && array_key_exists( $courseId , $this->courseList ) )
            {
                $this->answerList[ $courseId ][ $questionId ] = $line[ 'choice_id' ];
                $this->answeredNb++;
            }
        }
    }
}