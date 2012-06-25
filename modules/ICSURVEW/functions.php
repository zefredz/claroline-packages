<?php

if ( count( get_included_files() ) == 1 ) die( '---' );

include_once dirname( __FILE__ ) . '/lib/answer.class.php';
include_once dirname( __FILE__ ) . '/lib/survey.class.php';
include_once dirname( __FILE__ ) . '/lib/surveylist.class.php';

$userId = claro_get_current_user_id();

if ( Claro_KernelHook_Lock::getLock() )
{
    if ( $userId && ! claro_is_platform_admin() )
    {
        $surveyList = new ICSURVEW_SurveyList();
        $surveyId = $surveyList->getActive();
        
        if( $surveyId )
        {
            $survey = new ICSURVEW_Survey( $surveyId );
            $answer = new ICSURVEW_Answer( $userId , $survey->get() );
            
            /*if ( ! ( $answer->hasAnswered()
                && ( ! isset( $_SESSION[ 'ICSURVEW_STAGE' ] )
                    || $_SESSION[ 'ICSURVEW_STAGE' ] == 3 ) )
                && ! isset( $_SESSION[ 'ICSURVEW_LATER' ] ) )*/
            
            if( ! $answer->hasAnswered()
               && ( ! isset( $_SESSION[ 'ICSURVEW_STAGE' ] )
                    || $_SESSION[ 'ICSURVEW_STAGE' ] == 3 )
               && ! isset( $_SESSION[ 'ICSURVEW_LATER' ] ) )
            {
                $uriParts = explode( '?' , $_SERVER['REQUEST_URI'] );
                
                if( $uriParts[0] != get_module_url('ICSURVEW') .'/entry.php' )
                {
                    claro_redirect( get_module_url('ICSURVEW') .'/entry.php' );
                    die();
                }
            }
            else
            {
                $_SESSION[ 'ICSURVEW_LATER' ] = true;
                Claro_KernelHook_Lock::releaseLock();
            }
        }
    }
}
