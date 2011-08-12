<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 2.1.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCEXAM/UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

$tlabelReq = 'UCREPORT';
$nameTools = 'Examination Report';

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

FromKernel::uses( 'utils/input.lib'
                , 'utils/validator.lib'
                , 'display/layout.lib' );
From::Module( 'UCREPORT' )->uses( 'assetlist.lib'
                                , 'examination.lib'
                                , 'examinationlist.lib'
                                , 'userexamination.lib' );

$dialogBox = new DialogBox();
$pageTitle = array( 'mainTitle' => get_lang( 'Examination Report' ) );

$userInput = Claro_UserInput::getInstance();

if ( claro_is_allowed_to_edit() )
{
    $userInput->setValidator( 'cmd'
        , new Claro_Validator_AllowedList( array( 'rqShowList'
                                                , 'rqResult'
                                                , 'rqShow'
                                                , 'rqCreate'
                                                , 'rqEdit'
                                                , 'rqDelete'
                                                , 'rqReset'
                                                , 'exCreate'
                                                , 'exEdit'
                                                , 'exDelete'
                                                , 'exReset'
                                                , 'exModifyMark'
                                                , 'exDeleteMark'
                                                , 'exReset'
                                                , 'exMkVisible'
                                                , 'exMkInvisible' ) ) );
}
else
{
    $userInput->setValidator( 'cmd'
        , new Claro_Validator_AllowedList( array( 'rqShowList', 'rqShow' ) ) );
}

try
{
    $cmd = $userInput->get( 'cmd', 'rqShowList' );
    $sessionId = $userInput->get( 'sessionId' );
    
    $courseId = claro_get_current_course_id();
    $currentUserId = claro_get_current_user_id();
    $is_allowedToEdit = claro_is_allowed_to_edit();
    
    if ( $sessionId && $cmd != 'exDelete' )
    {
        $examination = new Examination( $sessionId );
    }
    else
    {
        $myResult = new UserExamination( $currentUserId );
    }
    
    //$examination = $sessionId ? new Examination( $sessionId ) : false;
    
    $tbl = get_module_course_tbl( array( 'examination_session' , 'examination_score' ) );
    $examinationList = new ExaminationList(  $tbl[ 'examination_session' ] , 'max_score' );
    
    $title = $userInput->get( 'title' );
    $maxValue = $userInput->get( 'maxValue' );
    $visibility = $userInput->get( 'visibility' );
    
    //CONTROLLER BEGIN
    switch( $cmd )
    {
        case 'rqShowList':
        case 'rqResult':
        case 'rqShow':
        case 'rqCreate':
        case 'rqEdit':
        case 'rqReset':
        case 'rqDeleteMark':
        case 'rqDelete':
        {
            break;
        }
        
        case 'exCreate':
        {
            $examination = new Examination( $examinationList->add( $title , $maxValue ) );
            break;
        }
        
        case 'exEdit':
        {
            $examination->setTitle( $title );
            $examination->setMaxScore( $maxValue );
            break;
        }
        
        case 'exModifyMark':
        {
            $mark = $userInput->get( 'mark' );
            $comment = $userInput->get( 'comment' );
            
            foreach( array_keys( $mark ) as $userId )
            {
                if ( $mark[ $userId ] !='' || $comment[ $userId ] != '' )
                {
                    $examination->setScore( $userId , $mark[ $userId ] , $comment[ $userId ] );
                }
            }
            break;
        }
        
        case 'exDeleteMark':
        {
            $mark_deleted = $examination->deleteScore( $userId );
            break;
        }
        
        case 'exResetList':
        {
            $examination_reseted = $examination->resetScoreList();
            break;
        }
        
        case 'exDelete':
        {
            $examination_deleted = $examinationList->delete( $sessionId );
            break;
        }
        
        case 'exMkVisible':
        case 'exMkInvisible':
        {
            $is_visible = $cmd == 'exMkVisible';
            $examinationList->setVisibility( $sessionId , $is_visible );
            break;
        }
        
        default :
        {
            throw new Exception( 'bad command' );
        }
    }
    
    if ( isset( $examination ) )
    {
        $examination_updated = $examination->save();
        $markList = $examination ? $examination->getScoreList( true ) : false;
    }
    
    //CONTROLLER END
    
    //VIEW
    switch( $cmd )
    {
        case 'rqShowList':
        {
            $template = $is_allowedToEdit
                      ? 'list'
                      : 'result';
            break;
        }
        
        case 'rqShow':
        {
            $template = 'examination';
            break;
        }
        
        case 'rqResult':
        {
            $template = 'result';
            break;
        }
        
        case 'rqCreate':
        {
            $form = new PhpTemplate( dirname( __FILE__ ) . '/templates/examination_edit.tpl.php' );
            $dialogBox->question( $form->render() );
            
            $template = 'list';
            $urlAction = "exCreate";
            break;
        }
        
        case 'rqEdit':
        {
            $form = new PhpTemplate( dirname( __FILE__ ) . '/templates/examination_edit.tpl.php' );
            $form->assign( 'sessionId' , $examination->getSessionId() );
            $form->assign( 'title' , $examination->getTitle() );
            $form->assign( 'maxValue' , $examination->getMaxScore() );
            $form->assign( 'is_visible' , $examinationList->isVisible( $examination->getSessionId() ) );
            $dialogBox->question( $form->render() );
            
            $template = 'examination';
            $urlAction = 'exEdit';
            break;
        }
        
        case 'rqReset':
        {
            $template = 'examination';
            $msg = get_lang( 'Reset the examination?' );
            $xid = array( 'sessionId' => $examination->getSessionId() );
            $urlAction = 'exReset';
            break;
        }
        
        case 'rqDelete':
        {
            $template = 'examination';
            $msg = get_lang( 'delete the examination?' );
            $xid = array( 'sessionId' => $examination->getSessionId() );
            $urlAction = 'exDelete';
            break;
        }
        
        case 'exCreate':
        {
            if ( $examination )
            {
                $dialogBox->success( get_lang( 'The examination %title has been created'
                                              , array( 'title' => $title ) ) );
            }
            else
            {
                $dialogBox->error( get_lang( 'Error' ) );
            }
            
            $template = 'examination';
            break;
        }
        
        case 'exEdit':
        {
            if ( $examination_updated )
            {
                $dialogBox->success( get_lang( 'The changes has been recorded' ) );
            }
            else
            {
                $dialogBox->error( get_lang( 'Error' ) );
            }
            
            $template = 'examination';
            break;
        }
        
        case 'exReset':
        {
            if ( $examination_reseted )
            {
                $dialogBox->success( get_lang( 'The examination has been reseted' ) );
            }
            else
            {
                $dialogBox->error( get_lang( 'Error' ) );
            }
            
            $template = 'examination';
            break;
        }
        
        case 'exModifyMark':
        {
            if ( $examination )
            {
                $dialogBox->success( get_lang( 'The changes has been recorded' ) );
            }
            else
            {
                $dialogBox->error( get_lang( 'Error' ) );
            }
            
            $template = 'examination';
            break;
        }
        
        case 'exDeleteMark':
        {
            $template = 'examination';
            break;
        }
        
        case 'exMkVisible':
        case 'exMkInvisible':
        case 'exDelete':
        {
            $template = 'list';
            break;
        }
        
        default :
        {
            throw new Exception( 'bad command' );
        }
    }
    
    // if $msg is defined, displays a question box containing a simple [OK]/[Cancel] form
    if ( isset( $msg ) )
    {
        $form = new PhpTemplate( dirname( __FILE__ ) . '/templates/question.tpl.php' );
        
        $form->assign( 'message' , $msg );
        $form->assign( 'xid' , $xid );
        $form->assign( 'urlAction' , $urlAction );
        $form->assign( 'urlCancel' , '' );
        
        $dialogBox->question( $form->render() );
    }
    
    // assigns parameters to the template
    $examinationView = new PhpTemplate( dirname( __FILE__ ) . '/templates/examination_' . $template . '.tpl.php' );
    
    switch ( $template )
    {
        case 'list':
            $pageTitle[ 'subTitle' ] = get_lang( 'Examination list' );
            $examinationView->assign( 'examinationList' , $examinationList->getList( true ) );
            $examinationView->assign( 'has_result' , $myResult->hasResult() );
            break;
        
        case 'edit':
            $pageTitle[ 'subTitle' ] = get_lang( 'Edit examination');
            break;
        
        case 'examination':
            $pageTitle[ 'subTitle' ] = $examination->getTitle();
            $examinationView->assign( 'examination' , $examination );
            break;
        
        case 'result':
            $pageTitle[ 'subTitle' ] = get_lang( 'My examination results and comments' );
            $examinationView->assign( 'result' , $myResult->getResultList() );
            break;
    }
    
    $examinationView->assign( 'currentUserId' , $currentUserId );
    
    ClaroBreadCrumbs::getInstance()->append( get_lang( 'Session list' )
                                           , htmlspecialchars( Url::Contextualize( $_SERVER[ 'PHP_SELF' ] ) ) );
    Claroline::getInstance()->display->body->appendContent( claro_html_tool_title( $pageTitle )
                                                          . $dialogBox->render()
                                                          . $examinationView->render() );
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
    
    $dialogBox->error( $errorMsg );
    Claroline::getInstance()->display->body->appendContent( $dialogBox->render() );
}

echo Claroline::getInstance()->display->render();