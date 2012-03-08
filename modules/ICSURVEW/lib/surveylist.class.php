<?php

class ICSURVEW_SurveyList
{
    public function __construct()
    {
        $this->tbl = get_module_main_tbl( array( 'ICSURVEW_survey' ) );
    }
    
    public function get()
    {
        return Claroline::getDatabase()->query( "
            SELECT 
                id,
                title,
                is_active
            FROM
                `{$this->tbl['ICSURVEW_survey']}`" );
    }
    
    public function getActive()
    {
        $result = Claroline::getDatabase()->query( "
            SELECT
                id
            FROM
                `{$this->tbl['ICSURVEW_survey']}`
            WHERE
                is_active = 1" );
        
        if( $result->numRows() > 1 )
        {
            $this->deactivate();
            throw new Exception( 'An error occured : there was more than one active surveys!' );
        }
        
        return $result->fetch( Database_ResultSet::FETCH_VALUE );
    }
    
    public function activate( $id )
    {
        $this->deactivate();
        
        return Claroline::getDatabase()->exec( "
            UPDATE
                `{$this->tbl['ICSURVEW_survey']}`
            SET
                is_active = 1
            WHERE
                id = " . Claroline::getDatabase()->escape( $id )
        );
    }
    
    public function deactivate( $id = null )
    {
        if( ! $id )
        {
            $id = (int)$this->getActive();
        }
        
        return Claroline::getDatabase()->exec( "
            UPDATE
                `{$this->tbl['ICSURVEW_survey']}`
            SET
                is_active = 0
            WHERE
                id = " . Claroline::getDatabase()->escape( $id ) );
    }
}