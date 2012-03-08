<?php

class ICSURVEW_Survey
{
    protected $id;
    protected $questionList = array();
    
    public function __construct( $id )
    {
        $this->id = $id;
        $this->tbl = get_module_main_tbl( array( 'ICSURVEW_question',
                                                 'ICSURVEW_choice' ) );
        $this->load();
    }
    
    private function load()
    {
        $result = Claroline::getDatabase()->query( "
            SELECT
                id,
                question
            FROM
                `{$this->tbl['ICSURVEW_question']}`
            WHERE
                survey_id = " . Claroline::getDatabase()->escape( $this->id )
        );
        
        foreach( $result as $line )
        {
            $questionData = array( 'id' => $line['id'] , 'question' => $line['question'] );
            
            $choiceList = Claroline::getDatabase()->query( "
                SELECT
                    id,
                    choice
                FROM
                    `{$this->tbl['ICSURVEW_choice']}`
                WHERE
                    question_id = " . Claroline::getDatabase()->escape( $line[ 'id' ] )
            );
            
            foreach( $choiceList as $choice )
            {
                $questionData[ 'choice' ][ $choice[ 'id' ] ] = $choice[ 'choice' ];
            }
            
            $this->questionList[ $line[ 'id' ] ] = $questionData;
        }
    }
    
    public function get()
    {
        return $this->questionList;
    }
    
    public function getId()
    {
        return $this->id;
    }
}