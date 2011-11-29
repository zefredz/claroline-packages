<?php

define( 'ICSURVEW_ACCESSED', true );

$tlabelReq = 'ICSURVEW';

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

FromKernel::uses(
    'utils/input.lib',
    'utils/validator.lib',
    'display/layout.lib',
    'claroCourse.class' );

From::Module( 'ICSURVEW' )->uses( 'answer.lib' , 'survey.lib' );

if ( ! isset( $_SESSION[ 'ICSURVEW_STAGE' ] ) )
{
    $_SESSION[ 'ICSURVEW_STAGE' ] = 0;
}

$surveyFileUrl = dirname( __FILE__ ) . '/survey.json';
$userId = claro_get_current_user_id();
$survey = new ICSURVEW_Survey( $surveyFileUrl );
$answer = new ICSURVEW_Answer( $userId , $survey->get() );

$pageTitle = array( 'mainTitle' => get_lang( 'iCampus Course Survey' ) );

$success = false;
$userInput = Claro_UserInput::getInstance();
$dialogBox = new DialogBox();

try
{
    switch( $_SESSION[ 'ICSURVEW_STAGE' ] )
    {
        case 0:
        case 1:
            $submission = $userInput->get( 'answer' );
            $cmd = $userInput->get( 'cmd' );
            
            if ( $cmd == 'accept' && $_SESSION[ 'ICSURVEW_STAGE' ] == 0 )
            {
                $success = true;
                break;
            }
            elseif( $cmd == 'later' && get_conf( 'ICSURVEW_postpone_allowed' ) )
            {
                $_SESSION[ 'ICSURVEW_LATER' ] = true;
                claro_redirect( get_path( 'url' ) );
                die();
            }
            
            if ( $submission )
            {
                foreach( $submission as $courseId => $question )
                {
                    foreach ( $question as $questionId => $optionId )
                    {
                        $answer->set( $courseId , $questionId , $optionId );
                    }
                }
                
                if ( $answer->hasAnswered() )
                {
                    $success = true;
                }
                else
                {
                    $dialogBox->error( get_lang( '_not_complete' ) );
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
                
                $success = true;
            }
            
            break;
        
        case 3:
            break;
        
        default:
            throw new Exception( 'Error' );
    }
    
    if ( $success )
    {
        $_SESSION[ 'ICSURVEW_STAGE' ]++;
    }
    
    CssLoader::getInstance()->load( 'style' , 'screen' );
    
    $pageTitle[ 'subTitle' ] = get_lang( 'Stage %_stage' , array( '%_stage' => $_SESSION[ 'ICSURVEW_STAGE' ] + 1 ) );
    
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