<?php

if ( count( get_included_files() ) == 1 ) die( '---' );

include_once dirname( __FILE__ ) . '/lib/answer.class.php';
include_once dirname( __FILE__ ) . '/lib/survey.class.php';
include_once dirname( __FILE__ ) . '/lib/surveylist.class.php';

$userId = claro_get_current_user_id();

if ( Claro_KernelHook_Lock::lockAvailable() )
{
    if ( $userId && ! claro_is_platform_admin() )
    {
        $surveyList = new ICSURVEW_SurveyList();
        $surveyId = $surveyList->getActive();
        
        if( $surveyId && Claro_KernelHook_Lock::getLock() )
        {
            $survey = new ICSURVEW_Survey( $surveyId );
            $answer = new ICSURVEW_Answer( $userId , $survey->get() );
            
            if ( ! ( $answer->hasAnswered()
                && ( ! isset( $_SESSION[ 'ICSURVEW_STAGE' ] )
                    || $_SESSION[ 'ICSURVEW_STAGE' ] == 4 ) )
                && ! isset( $_SESSION[ 'ICSURVEW_LATER' ] ) )
            {
                claro_redirect( get_module_url('ICSURVEW') .'/entry.php' );
                die();
            }
            else
            {
                Claro_KernelHook_Lock::releaseLock();
            }
        }
    }
}
