<?php

define( 'ICSURVEW_ACCESSED', true );

$tlabelReq = 'ICSURVEW';

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

FromKernel::uses(
    'utils/input.lib',
    'utils/validator.lib',
    'display/layout.lib',
    'claroCourse.class' );

From::Module( 'ICSURVEW' )->uses( 'answer.class',
                                  'survey.class',
                                  'surveylist.class' );

$postpone_allowed = get_conf( 'ICSURVEW_postpone_allowed' );

if ( ! isset( $_SESSION[ 'ICSURVEW_STAGE' ] ) )
{
    $_SESSION[ 'ICSURVEW_STAGE' ] = 0;
}

$userId = claro_get_current_user_id();

$surveyList = new ICSURVEW_SurveyList();
$surveyId = $surveyList->getActive();

$survey = new ICSURVEW_Survey( $surveyId );
$answer = new ICSURVEW_Answer( $userId , $survey->get() );

$userInput = Claro_UserInput::getInstance();
$dialogBox = new DialogBox();

try
{
    switch( $_SESSION[ 'ICSURVEW_STAGE' ] )
    {
        case 0:
        case 1:
            $cmd = $userInput->get( 'cmd' );
            $submission = $userInput->get( 'answer' );
            
            if ( $cmd == 'accept' )
            {
                $_SESSION[ 'ICSURVEW_STAGE' ] = 1;
                
                if( $answer->otherAnswered() )
                {
                    $dialogBox->info( get_lang( '_other_answered' ) );
                }
            }
            elseif( $cmd == 'later' && $postpone_allowed )
            {
                $_SESSION[ 'ICSURVEW_LATER' ] = true;
                claro_redirect( get_path( 'url' ) );
            }
            
            if ( $submission )
            {
                foreach( $submission as $courseId => $question )
                {
                    foreach ( $question as $questionId => $choiceId )
                    {
                        $answer->set( $courseId , $questionId , $choiceId );
                    }
                }
                
                if ( ! $answer->hasAnswered() && ! $postpone_allowed )
                {
                    $dialogBox->error( get_lang( '_not_complete' ) );
                }
                
                if( $answer->hasAnswered() )
                {
                    $_SESSION[ 'ICSURVEW_STAGE' ] = 2;
                }
                elseif( $postpone_allowed )
                {
                    $_SESSION[ 'ICSURVEW_STAGE' ] = 3;
                }
            }
            
            break;
        
        case 2:
            $codeList = $userInput->get( 'code' );
            $newCodeList = $userInput->get( 'newCode' );
            
            if ( $codeList )
            {
                foreach( $newCodeList as $courseId => $newCode )
                {
                    if( $codeList[ $courseId ] != $newCode )
                    {
                        $course = new claroCourse();
                        $course->load( $courseId );
                        $course->officialCode = $newCode;
                        $course->save();
                    }
                }
            }
            
            if( ! $answer->hasAnswered() )
            {
                $dialogBox->info( get_lang( '_not_finished' ) );
            }
            
            $_SESSION[ 'ICSURVEW_STAGE' ] = 3;
            break;
        
        case 3:
            Claro_KernelHook_Lock::releaseLock();
            break;
        
        default:
            throw new Exception( 'Error' );
    }
    
    CssLoader::getInstance()->load( 'style' , 'screen' );
    $pageTitle = array( 'mainTitle' => get_lang( 'iCampus Course Survey' ) );
    $pageTitle[ 'subTitle' ] = get_lang( 'Stage %_stage : %_description' , array( '%_stage' => $_SESSION[ 'ICSURVEW_STAGE' ] + 1
                                                                                , '%_description' => get_lang( '_description_stage_' . $_SESSION[ 'ICSURVEW_STAGE' ] ) ) );
    
    $template = new ModuleTemplate( 'ICSURVEW' , 'stage' . $_SESSION[ 'ICSURVEW_STAGE' ] . '.tpl.php' );
    $template->assign( 'answer' , $answer );
    
    ClaroBreadCrumbs::getInstance()->append( $pageTitle[ 'mainTitle' ] , $_SERVER[ 'PHP_SELF' ] );
    ClaroBreadCrumbs::getInstance()->append( $pageTitle[ 'subTitle' ] );
    Claroline::getInstance()->display->body->appendContent( claro_html_tool_title( $pageTitle )
                                                          . $dialogBox->render()
                                                          . $template->render() );
}
catch( Exception $e )
{
    if ( claro_debug_mode() )
    {
        $errorMsg = '<pre>' . $e->__toString() . '</pre>';
    }
    else
    {
        $errorMsg = $e->getMessage();
    }
    
    $dialogBox->error( '<strong>' . get_lang( 'Error' ) . ' : </strong>' . $errorMsg );
    Claroline::getInstance()->display->body->appendContent( $dialogBox->render() );
}

echo Claroline::getInstance()->display->render();