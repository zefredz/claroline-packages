<?php

if ( count( get_included_files() ) == 1 ) die( '---' );

include_once dirname( __FILE__ ) . '/lib/answer.lib.php';
include_once dirname( __FILE__ ) . '/lib/survey.lib.php';

$surveyFileUrl = dirname( __FILE__ ) . '/survey.json';
$userId = claro_get_current_user_id();

if ( Claro_KernelHook_Lock::lockAvailable() )
{
    if ( $userId
      && ! claro_is_platform_admin() )
    {
        if( Claro_KernelHook_Lock::getLock( 'ICSURVEW' ) )
        {
            $survey = new ICSURVEW_Survey( $surveyFileUrl );
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
                Claro_KernelHook_Lock::releaseLock( 'ICSURVEW' );
            }
        }
    }
}

