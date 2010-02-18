<?php // $Id$

/**
 * Claroline Poll Tool
 *
 * @version     CLQPOLL 0.9.4 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLQPOLL
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

$tlabelReq = 'CLQPOLL';

$nameTools = 'Quick Poll';

require dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';

FromKernel::uses( 'utils/input.lib' , 'utils/validator.lib' , 'display/layout.lib' );
From::Module( 'CLQPOLL' )->uses( 'poll.lib' , 'polllist.lib' , 'uservote.lib' , 'pollpager.lib' , 'pollstat.lib' );

if ( ! claro_is_in_a_course() || ! claro_is_course_allowed() ) claro_disp_auth_form( true );

claro_set_display_mode_available( true );

CssLoader::getInstance()->load( 'poll' , 'screen' );
$dialogBox = new DialogBox();
$pageTitle = array( 'mainTitle' => get_lang( 'Quick poll tool' ) );

try
{
    $userInput = Claro_UserInput::getInstance();
    
    if ( claro_is_allowed_to_edit() )
    {
        $userInput->setValidator( 'cmd' , new Claro_Validator_AllowedList( array(
            'rqShowList', 'rqViewPoll',
            'rqSubmitVote', 'rqDeleteVote',
            'rqCreatePoll', 'rqEditPoll',
            'rqAddChoice', 'rqDeleteChoice',
            'rqDeletePoll', 'rqPurgePoll',
            'exCreatePoll', 'exEditPoll',
            'exAddChoice', 'exDeleteChoice',
            'exDeletePoll', 'exPurgePoll',
            'exSubmitVote', 'exDeleteVote',
            'exMkVisible', 'exMkInvisible',
            'exOpen', 'exClose',
            'rqViewStats'
        ) ) );
    }
    elseif ( claro_is_course_member() )
    {
        $userInput->setValidator( 'cmd' , new Claro_Validator_AllowedList( array(
            'rqShowList', 'rqViewPoll',
            'rqSubmitVote', 'rqDeleteVote',
            'exSubmitVote', 'exDeleteVote',
            'rqViewStats'
        ) ) );
    }
    else
    {
        $userInput->setValidator( 'cmd' , new Claro_Validator_AllowedList( array(
            'rqShowList', 'rqViewPoll',
            'rqViewStats'
        ) ) );
    }
    
    // sets the command
    $cmd = $userInput->get( 'cmd', 'rqShowList' );
    
    // retrieves the parameters
    $pollId   = $userInput->get( 'pollId' );
    $choiceId = $userInput->get( 'choiceId' );
    $pageNb = $userInput->get( 'pageNb' );
    
    $userId = claro_get_current_user_id();
    
    // creates the PollList object
    $pollList = new PollList();
    
    // creates the Poll object
    $poll = new Poll( $pollId );
    
    // creates a PollStat objet ( if relevant )
    $pollStat = $poll ? new PollStat( $poll ) : false;
    
    // creates a UserVote object ( if relevant )
    $userVote = ( $userId && $pollId ) ? new UserVote( $poll , $userId ) : false;
    
    // defines user's rights
    $userRights = array( 'edit' => false , 'vote' => false , 'see_names' => false , 'see_stats' => false );
    
    if ( claro_is_course_allowed() )
    {
        if ( $poll->getOption( '_privacy' )     == '_public' )  $userRights[ 'see_names' ] = true;
        if ( $poll->getOption( '_stat_access' ) == '_granted' ) $userRights[ 'see_stats' ] = true;
        if ( $poll->getOption( '_stat_access' ) == '_when_closed' && ! $poll->isOpen() ) $userRights[ 'see_stats' ] = true;
    }
    
    if ( claro_is_course_member() )
    {
        if ( $poll->isOpen() ) $userRights[ 'vote' ] = true;
    }
    
    if ( claro_is_allowed_to_edit() )
    {
        $userRights[ 'vote' ]        = true; // for test purposes only, this must be deleted
        $userRights[ 'edit' ]        = true;
        $userRights[ 'see_stats' ]   = true;
        if ( $poll->getOption( '_privacy' ) != '_anonymous' ) $userRights[ 'see_names' ] = true;
    }
    
    if ( claro_is_platform_admin() )
    {
        $userRights[ 'see_names' ] = true;
    }
    
    // determines which option can be changed
    $change_allowed = array( '_type' => true , '_privacy' => true , '_stat_access' => true );
    
    if ( $poll->getAllVoteList() && ! claro_is_platform_admin() )
    {
        $change_allowed[ '_type' ] = false;
        if ( $poll->getOption( '_privacy' ) == '_anonymous' ) $change_allowed[ '_privacy' ] =  false;
    }
    
    // to handle in-betweens deletion
    if ( $poll->getId() || $cmd == 'rqShowList' || $cmd == 'rqCreatePoll' || $cmd == 'exCreatePoll' )
    {
        /// BEGIN CONTROLLER
        
        switch ( $cmd )
        {
            case 'rqShowList':
            case 'rqViewPoll':
            case 'rqCreatePoll':
            case 'rqEditPoll':
            case 'rqAddChoice':
            case 'rqEditChoice':
            case 'rqDeleteChoice':
            case 'rqPurgePoll':
            case 'rqSubmitVote':
            case 'rqDeleteVote':
            case 'rqDeletePoll':
            case 'rqViewStats':
            {
                break;
            }
            
            case 'exSubmitVote':
            {
                if ( isset( $choiceId ) )// this means it's a single vote poll
                {
                    $userVote->setVote( $choiceId , UserVote::CHECKED );
                }
                else
                {
                    foreach ( array_keys( $poll->getChoiceList() ) as $choiceId ) // multi vote poll
                    {
                        if ( $userInput->get( 'choice' . $choiceId ) )
                        {
                            $checked = UserVote::CHECKED;
                        }
                        else
                        {
                            $checked = UserVote::NOTCHECKED;
                        }
                        
                        $userVote->setVote( $choiceId , $checked );
                    }
                }
                
                $has_voted = ( $userVote->isVoteValid() && $poll->isOpen() ) ? $userVote->saveVote() : false;
                break;
            }
            
            case 'exDeleteVote':
            {
                $vote_deleted = $userVote->deleteVote();
                break;
            }
            
            case 'exCreatePoll':
            case 'exEditPoll':
            {
                $title    = $userInput->get( 'title' );
                $question = $userInput->get( 'question' );
                $label    = $userInput->get( 'label' );
                
                $poll->setTitle( $title );
                $poll->setQuestion( $question );
                
                foreach( array_keys( $poll->getOptionList() ) as $option)
                {
                    $value = $userInput->get( $option );
                    
                    // Cannot change privacy if the poll is anonymous
                    if ( $change_allowed[ $option ] )
                    {
                        $poll->setOption( $option , $value );
                    }
                    else
                    {
                        $poll_changed = false;
                    }
                }
                
                if ( ! isset( $poll_changed ) )
                {
                    $poll_changed = $poll->save();
                }
                
                if ( $label && ! $poll->getAllVoteList() ) $poll->addChoice( $label );
                
                foreach( array_keys( $poll->getChoiceList() ) as $choiceId )
                {
                    $label = $userInput->get( 'choice' . $choiceId );
                    if ( $label )
                    {
                        $poll->updateChoice( $choiceId , $label );
                    }
                }
                
                break;
            }
            
            case 'exDeleteChoice':
            {
                $choiceList = $poll->getChoiceList();
                $choice_deleted = isset( $choiceList[ $choiceId ] ) ? $poll->deleteChoice( $choiceId ) : false;
                break;
            }
            
            case 'exPurgePoll':
            {
                $poll_purged = $poll->purge();
                break;
            }
            
            case 'exDeletePoll':
            {
                $poll_deleted = $poll->delete();
                break;
            }
            
            case 'exOpen':
            {
                $poll->open();
                $poll_opened = $poll->save();
                break;
            }
            
            case 'exClose':
            {
                $poll->close();
                $poll_closed = $poll->save();
                break;
            }
            
            case 'exMkVisible':
            case 'exMkInvisible':
            {
                $poll->changeVisibility();
                $visibility_changed = $poll->save();
                break;
            }
            
            default:
            {
                throw new Exception ( 'Invalid command' );
            }
        }
        
        /// END CONTROLLER
        
        /// BEGIN VIEW
        
        switch ( $cmd )
        {
            case 'rqEditPoll':
            {
                if ( $poll->getAllVoteList() )
                {
                    $dialogBox->warning( get_lang( 'Some users already voted, so be careful! You cannot add or remove choices ( unless you purge the poll ).' ) );
                }
                
                $crumb = get_lang( 'Edit poll' );
                $template = 'polledit';
                break;
            }
            
            case 'rqDeleteChoice':
            {
                if ( $poll->getAllVoteList() )
                {
                    $dialogBox->warning( get_lang( 'Some users already voted, so you cannot delete choices anymore! (Unless you purge this poll...)' ) );
                }
                else
                {
                    $labelList = $poll->getChoiceList();
                    $label = $labelList[ $choiceId ];
                    $msg = get_lang( 'Do you really want to delete this choice?' ) . ' : <strong>"' . $label . '"</strong>';
                    $urlAction = 'exDeleteChoice';
                    $urlCancel = 'rqEditPoll';
                }
                
                $crumb = get_lang( 'Delete poll choice' );
                $template = 'polledit';
                break;
            }
            
            case 'rqShowList':
            {
                $template = 'polllist';
                break;
            }
            
            case 'rqViewPoll':
            {
                if ( ! $poll->isOpen() )
                {
                    $dialogBox->info( get_lang( 'The votes for this poll are closed' ) );
                }
                elseif ( ! $userRights[ 'vote' ] )
                {
                    $dialogBox->info( get_lang( 'Vote only granted to course registered users!' ) );
                }
                
                $crumb = get_lang( 'Poll' );
                $template = 'pollview';
                
                break;
            }
            
            case 'rqViewStats':
            {
                if ( $userRights[ 'see_stats' ] )
                {
                    $crumb = get_lang( 'Statistics' );
                    $template = 'pollstat';
                }
                else
                {
                    $template = 'polllist';
                    $dialogBox->info( get_lang( 'You cannot see the statistics for this poll!' ) );
                }
                break;
            }
            
            case 'rqCreatePoll':
            {
                $crumb = get_lang( 'Create poll' );
                $template = 'polledit';
                break;
            }
            
            case 'rqPurgePoll':
            {
                $crumb = get_lang( 'Purge poll' );
                $msg = get_lang( 'Do you really want to purge this poll?' );
                $urlAction = 'exPurgePoll';
                $urlCancel = 'rqEditPoll';
                $template = 'pollview';
                break;
            }
            
            case 'rqDeletePoll':
            {
                $crumb = get_lang( 'Delete Poll' );
                $msg = get_lang( 'Do you really want to delete this poll?' );
                $urlAction = 'exDeletePoll';
                $urlCancel = 'rqShowList';
                $template = 'polledit';
                break;
            }
            
            case 'rqSubmitVote':
            {
                if ( $userRights[ 'vote' ] )
                {
                    $crumb = get_lang( 'Vote' );
                    $template = 'pollview';
                }
                else
                {
                    claro_disp_auth_form( true );
                }
                break;
            }
            
            case 'rqDeleteVote':
            {
                if ( $poll )
                {
                    $crumb = get_lang( 'Delete vote' );
                    $msg = get_lang( 'Do you really want to delete your vote?' );
                    $urlAction = 'exDeleteVote';
                    $urlCancel = 'rqViewPoll';
                    $template = 'pollview';
                }
                else
                {
                    $dialogBox->error( 'The poll has been deleted by course manager!' );
                    $template = 'polllist';
                }
                break;
            }
            
            case 'exSubmitVote':
            {
                if ( $has_voted )
                {
                    $dialogBox->success( get_lang( 'Your vote has been successfully added!' ) );
                }
                elseif ( $userVote->getPoll()->isOpen() )
                {
                    $dialogBox->error( get_lang( 'You must validate one choice!' ) );
                }
                else
                {
                    $dialogBox->error( get_lang( 'The votes for this poll are closed' ) );
                }
                
                $template = 'pollview';
                break;
            }
            
            case 'exDeleteVote':
            {
                if ( $vote_deleted )
                {
                    $dialogBox->success( get_lang( 'Your vote has been successfully deleted!' ) );
                }
                elseif ( $userId )
                {
                    $dialogBox->error( get_lang( 'The poll has been purged!' ) );
                }
                else claro_disp_auth_form( true );
                
                $template = 'pollview';
                break;
            }
            
            case 'exCreatePoll':
            {
                if ( $poll_changed )
                {
                    $dialogBox->success( get_lang( 'Your new poll has been created!' ) );
                }
                else
                {
                    $dialogBox->error( get_lang( 'Error' ) );
                }
                
                $template = 'polledit';
                break;
            }
            
            case 'exDeletePoll':
            {
                if ( $poll_deleted )
                {
                    $dialogBox->success( get_lang( 'The poll has been successfully deleted!' ) );
                }
                else
                {
                    $dialogBox->error( get_lang( 'Error' ) );
                }
                
                $template = 'polllist';
                break;
            }
            
            case 'exEditPoll':
            {
                if ( $poll_changed )
                {
                    $dialogBox->success( get_lang( 'Changes successful!' ) );
                }
                else
                {
                    $dialogBox->error( '<strong>' . get_lang( 'Cannot modify poll' ) . '</strong>' );
                }
                
                $template  = 'polledit';
                break;
            }
            
            case 'exDeleteChoice':
            {
                if ( $choice_deleted )
                {
                    $dialogBox->success( get_lang( 'The choice has been successfully deleted!' ) );
                }
                else
                {
                    $dialogBox->error( '<strong>' . get_lang( 'Error' ) . '</strong>' );
                }
                
                $template = 'polledit';
                break;
            }
            
            case 'exPurgePoll':
            {
                if ( $poll_purged )
                {
                    $dialogBox->success( get_lang( 'The poll has been successfully purged!' ) );
                }
                else
                {
                    $dialogBox->error( get_lang( 'Error' ) );
                }
                
                $template = 'pollview';
                break;
            }
            
            case 'exOpen':
            {
                if ( $poll_opened )
                {
                    $dialogBox->success( get_lang( 'The votes for this poll are now open' ) );
                }
                else
                {
                    $dialogBox->error( get_lang( 'Error' ) );
                }
                
                $template = $userInput->get( 'tpl' );
                break;
            }
            
            case 'exClose':
            {
                if ( $poll_closed )
                {
                    $dialogBox->success( get_lang( 'The votes for this poll are now closed' ) );
                }
                else
                {
                    $dialogBox->error( get_lang( 'Error' ) );
                }
                
                $template = $userInput->get( 'tpl' );
                break;
            }
            
            case 'exMkVisible':
            case 'exMkInvisible':
            {
                if ( $visibility_changed )
                {
                    $dialogBox->success( get_lang( 'The visibility has been changed.' ) );
                }
                else
                {
                    $dialogBox->error( get_lang( 'Error' ) );
                }
                
                $template = $userInput->get( 'tpl' );
                break;
            }
            
            default:
            {
                throw new Exception ( 'Invalid command' ); // this will NEVER happen :-)
            }
        }
    }
    else
    {
        $dialogBox->error( get_lang( 'The poll has been deleted!' ) );
    }
}
catch ( Exception $e ) // exceptions handling
{
    if ( claro_debug_mode() )
    {
        $dialogBox->error( '<pre>' . $e->__toString() . '</pre>' );
    }
    else
    {
        $dialogBox->error( $e->getMessage() );
    }
}

// if $msg is defined, displays a question box containing a simple [OK]/[Cancel] form
if ( isset( $msg ) )
{
    $form = new PhpTemplate( dirname( __FILE__ ) . '/templates/question.tpl.php' );
    
    $form->assign( 'pollId' , $pollId );
    $form->assign( 'choiceId' , $choiceId );
    $form->assign( 'msg' , $msg );
    $form->assign( 'urlAction' , $urlAction );
    $form->assign( 'urlCancel' , $urlCancel );
    
    $dialogBox->question( $form->render() );
}

// assigns parameters to the template
if ( isset ( $template ) )
{
    $pollView = new PhpTemplate( dirname( __FILE__ ) . '/templates/' . $template . '.tpl.php' );
    
    switch ( $template )
    {
        case 'polllist':
            $pageTitle[ 'subTitle' ] = get_lang( 'Poll list' );
            break;
        
        case 'polledit':
            $pageTitle[ 'subTitle' ] = $poll ? get_lang( 'Edit poll' ) : get_lang( 'Create a new poll' );
            
            $scriptContent = '<script type="text/javascript">
                        <!--
                        $(document).ready(function(){';
            
            $scriptContent .=  '$("#addChoice").click(function(){
                                    $("#newChoice").append("<input type=\"text\" name=\"label\" value=\"' . get_lang( 'Put your choice here' ) . '\" size=\"40\" /><br/>");
                                });
                            });
                -->
                </script>';
                
            ClaroHeader::getInstance()->addHtmlHeader( $scriptContent );
            $pollView->assign( 'change_allowed' , $change_allowed );
            break;
        
        case 'pollstat':
            $pageTitle[ 'subTitle' ] = get_lang( 'Poll statistics' );
            break;
        
        case 'pollview':
            if ( ! isset( $pageNb ) ) $pageNb = 0;
            $pollPager = new Pager( $poll->getAllVoteList() , get_conf( 'pagerLineNb' ) );
            if ( $pageNb >= $pollPager->getPageCount() ) $pageNb = $pollPager->getPageCount() - 1;
            $pollView->assign( 'voteList' , $pollPager );
            $pollView->assign( 'pageNb' , $pageNb );
            break;
    }
    
    $pollView->assign( 'pollList'   , $pollList->getPollList( claro_is_allowed_to_edit() , true ) );
    $pollView->assign( 'poll'       , $poll );
    $pollView->assign( 'userVote'   , $userVote );
    $pollView->assign( 'userId'     , $userId );
    $pollView->assign( 'userRights' , $userRights );
    $pollView->assign( 'pollStat'   , $pollStat );
    $pollView->assign( 'dialogBox'  , $dialogBox );
    $pollView->assign( 'pageTitle'  , $pageTitle );
    
    Claroline::getInstance()->display->body->appendContent( $pollView->render() );
}
else
{
    Claroline::getInstance()->display->body->appendContent( $dialogBox->render() );
}

// generates breadcrumbs
ClaroBreadCrumbs::getInstance()->append( get_lang( 'Poll list' ) , htmlspecialchars( Url::Contextualize( $_SERVER[ 'PHP_SELF' ] ) ) );
if ( isset( $crumb ) )
{
    ClaroBreadCrumbs::getInstance()->append( $crumb );
}

// renders the page
echo Claroline::getInstance()->display->render();

/// END OF FILE
