<?php

$tlabelReq = 'ICSURVEW';

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

FromKernel::uses(
    'utils/input.lib',
    'utils/validator.lib',
    'display/layout.lib',
    'fileUpload.lib' );

From::Module( 'ICSURVEW' )->uses( 'surveylist.class',
                                  'surveyimport.class' );

$userId = claro_get_current_user_id();

$dialogBox = new DialogBox();

try
{
    if( ! claro_is_platform_admin() )
    {
        throw new Exception( 'Not allowed' );
    }
    
    $surveyList = new iCSURVEW_SurveyList();
    
    $actionList = array( 'rqList'
                       , 'rqCreate'
                       , 'rqImport'
                       , 'exActivate'
                       , 'exImport' );
    $userInput = Claro_UserInput::getInstance();
    $userInput->setValidator( 'cmd' , new Claro_Validator_AllowedList( $actionList ) );
    $cmd = $userInput->get( 'cmd' , 'rqList' );
    
    // CONTROLLER
    switch( $cmd )
    {
        case 'rqList':
        case 'rqCreate':
            break;
        
        case 'rqImport':
            $formData = array();
            $formData['message'] = get_lang( 'Submit the survey definition file in JSON format' );
            $formData['urlAction'] = 'exImport';
            $formData['urlCancel'] = 'rqList';
            $formData['submit'] = 'upload';
            $formData['xid'] = array( array(  'title' => 'Title'
                                            , 'type' => 'text'
                                            , 'name' => 'title' )
                                    , array(  'title' => 'File'
                                            , 'type' => 'file'
                                            , 'name' => 'survey_file' )
                                    );
            break;
        
        case 'exActivate':
            $surveyId = $userInput->get( 'surveyId' );
            
            if( $surveyList->activate( $surveyId ) )
            {
                $sucessMsg = get_lang( 'The survey has been activated' );
            }
            else
            {
                $errorMsg = get_lang( 'Activation failed!' );
            }
            break;
        
        case 'exImport':
            if ( isset( $_FILES['survey_file'] ) )
            {
                $file = $_FILES['survey_file']['tmp_name'];
            }
            else
            {
                $errorMsg = get_lang( 'Missing file' );
                break;
            }
            
            $title = $userInput->get( 'title' );
            
            if( ! $title )
            {
                $errorMsg = get_lang( 'Missing title' );
                break;
            }
            
            $surveyImport = new ICSURVEW_SurveyImport();
            
            $output = $surveyImport->import( $file , $title );
            
            if( is_int( $output ) )
            {
                $successMsg = get_lang( 'The survey has been sucessfully imported!' )
                            . $output . get_lang( 'questions' );
            }
            else
            {
                $errorMsg = get_lang( 'Import failed!' );
            }
            break;
        
        default:
            throw new Exception( 'Error' );
    }
    
    // VIEW
    CssLoader::getInstance()->load( 'style' , 'screen' );
    $pageTitle = array( 'mainTitle' => get_lang( 'iCampus Course Survey' ) );
    $pageTitle[ 'subTitle' ] = get_lang( 'Administration' );
    
    if( isset( $successMsg ) )
    {
        $dialogBox->success( $successMsg );
    }
    
    if( isset( $errorMsg ) )
    {
        $dialogBox->error( $errorMsg );
    }
    
    if( isset( $formData ) )
    {
        $form = new ModuleTemplate( 'ICSURVEW' , 'form.tpl.php' );
        
        foreach( $formData as $key => $value )
        {
            $form->assign( $key , $value );
        }
        
        $dialogBox->form( $form->render() );
    }
    
    $template = new ModuleTemplate( 'ICSURVEW' , 'admin.tpl.php' );
    $template->assign( 'surveyList' , $surveyList->get() );
    $template->assign( 'activeId' , $surveyList->getActive() );
    
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