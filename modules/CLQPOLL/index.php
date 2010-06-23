<?php // $Id$

/**
 * Claroline Poll Tool
 *
 * @version     CLQPOLL 1.0.0 $Revision$ - Claroline 1.9
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
            'rqViewStats', 'rqDeleteVote',
            'rqCreatePoll', 'rqEditPoll',
            'rqDeletePoll', 'rqPurgePoll',
            'exCreatePoll', 'exEditPoll',
            'exDeletePoll', 'exPurgePoll',
            'exSubmitVote', 'exDeleteVote',
            'exMkVisible', 'exMkInvisible',
            'exOpen', 'exClose'
        ) ) );
    }
    elseif ( claro_is_course_member() )
    {
        $userInput->setValidator( 'cmd' , new Claro_Validator_AllowedList( array(
            'rqShowList', 'rqViewPoll', 'rqViewStats',
            'exSubmitVote'

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
    $pollId   = (int)$userInput->get( 'pollId' );
    $choiceId = (int)$userInput->get( 'choiceId' );
    $pageNb   = (int)$userInput->get( 'pageNb' );
    
    $userId = claro_get_current_user_id();
    
    // creates the PollList object
    $pollList = new PollList();
    
    // creates the Poll object
    $poll = new Poll( $pollId );
    
    // creates a PollStat objet ( if relevant )
    $pollStat = $pollId ? new PollStat( $poll ) : false;
    
    // creates a UserVote object ( if relevant )
    $userVote = ( $userId && $pollId ) ? new UserVote( $poll , $userId ) : false;
    
    // defines user's rights
    $userRights = array( 'edit' => false , 'vote' => false , 'see_names' => false , 'see_stats' => false );
    
    if ( claro_is_course_allowed() )
    {
        if ( $poll->getOption( '_privacy' )     == '_public' )  $userRights[ 'see_names' ] = true;
        if ( $poll->getOption( '_stat_access' ) == '_granted' ) $userRights[ 'see_stats' ] = true;
        if ( $poll->getOption( '_stat_access' ) == '_when_closed'
            && ! $poll->isOpen() ) $userRights[ 'see_stats' ] = true;
    }
    
    if ( claro_is_course_member() )
    {
        if ( $poll->isOpen()
            && $userVote
            && ( ! $userVote->voteExists()
            || $poll->getOption( '_revote' ) == '_allowed' ) ) $userRights[ 'vote' ] = true;
    }
    
    if ( claro_is_allowed_to_edit() )
    {
        $userRights[ 'vote' ]        = true;
        $userRights[ 'edit' ]        = true;
        $userRights[ 'see_stats' ]   = true;
        if ( $poll->getOption( '_privacy' ) != '_anonymous' ) $userRights[ 'see_names' ] = true;
    }
    
    if ( claro_is_platform_admin() )
    {
        $userRights[ 'see_names' ] = true;
    }
    
    // determines which option can be changed
    $change_allowed = array( '_type' => true ,
                             '_answer' => true,
                             '_privacy' => true,
                             '_stat_access' => true,
                             '_revote' => true );
    
    if ( $poll->getAllVoteList() && ! claro_is_platform_admin() )
    {
        $change_allowed[ '_type' ] = false;
        $change_allowed[ '_answer' ] = false;
        //if ( $poll->getOption( '_privacy' ) == '_anonymous' ) $change_allowed[ '_privacy' ] =  false;
        $change_allowed[ '_privacy' ] = false;
        $change_allowed[ '_revote' ] = false;
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
            case 'rqDeleteChoice':
            case 'rqPurgePoll':
            case 'rqDeleteVote':
            case 'rqDeletePoll':
            case 'rqViewStats':
            {
                break;
            }
            
            case 'exSubmitVote':
            {
                $has_voted = false;
                
                if ( $userRights[ 'vote' ] )
                {
                    foreach ( array_keys( $poll->getChoiceList() ) as $pollChoiceId )
                    {
                        $checked = ( $userInput->get( 'choice' . $pollChoiceId )
                                     ||
                                     $pollChoiceId == $choiceId )
                                    ? UserVote::CHECKED : UserVote::NOTCHECKED;
                        
                        $userVote->setVote( $pollChoiceId , $checked );
                    }
                    
                    $has_voted = $userVote->isVoteValid() ? $userVote->saveVote() : false;
                                
                    $userRights[ 'vote'] = $poll->getOption( '_revote' ) == '_allowed'
                                            ||
                                            claro_is_allowed_to_edit()
                                            ? true : false;
                }
                break;
            }
            
            case 'exDeleteVote':
            {
                $vote_deleted = claro_is_allowed_to_edit() ? UserVote::deleteUserVote( $poll , (int)$userInput->get( 'userId' ) )
                                                          : false;
                break;
            }
            
            case 'exCreatePoll':
            case 'exEditPoll':
            {
                $poll_changed = false;
                
                $title      = $userInput->get( 'title' );
                $question   = $userInput->get( 'question' );
                
                $visibility = $userInput->get( 'visibility' );
                $status     = $userInput->get( 'status' );
                
                $toModify   = $userInput->get( 'mod' ) ? $userInput->get( 'mod' ) : array();
                $toAdd      = $userInput->get( 'add' ) ? $userInput->get( 'add' ) : array();
                $toDelete   = $userInput->get( 'del' ) ? $userInput->get( 'del' ) : array();
                
                if ( $title )
                {
                    $poll->setTitle( $title );
                    $poll->setQuestion( $question );
                    $poll->setVisibility( $visibility );
                    $poll->setStatus( $status );
                    
                    foreach( array_keys( $poll->getOptionList() ) as $option)
                    {
                        $value = $userInput->get( $option );
                        
                        if ( $change_allowed[ $option ] )
                        {
                            $poll->setOption( $option , $value );
                        }
                    }
                    
                    $poll_changed = $poll->save();
                    
                    foreach( $toAdd as $label )
                    {
                        if ( $label ) $poll->addChoice( $label );
                    }
                    
                    foreach( $toModify as $choiceId => $label )
                    {
                        if ( $label ) $poll->updateChoice( $choiceId , $label );
                    }
                    
                    foreach( array_keys( $toDelete ) as $choiceId )
                    {
                        $poll->deleteChoice( $choiceId );
                    }
                }
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
            case 'rqShowList':
            {
                $template = 'polllist';
                break;
            }
            
            case 'rqViewPoll':
            {
                if ( ! claro_is_course_member() )
                {
                    $dialogBox->info( get_lang( 'Vote only granted to course registered users!' ) );
                }
                elseif ( ! $poll->isOpen() )
                {
                    $dialogBox->info( '<strong>' . get_lang( 'The votes for this poll are closed' ) .'</strong>' );
                }
                elseif ( $userRights[ 'vote' ] )
                {
                    if ( $poll->getOption( '_type' ) == '_single' )
                    {
                        $dialogBox->info( '<strong>' . get_lang( 'This poll allows only one choice' ) . '</strong>' );
                    }
                    
                    if ( $poll->getOption( '_answer' ) == '_required' )
                    {
                        $dialogBox->info( '<strong>' . get_lang( 'This poll requires to validate a choice' ) . '</strong>' );
                    }
                }
                
                $crumb = get_lang( 'Poll' );
                $template = 'pollview';
                
                break;
            }
            
            case 'rqEditPoll':
            {
                if ( $poll->getAllVoteList() )
                {
                    $dialogBox->warning( '<strong>'
                                        . get_lang( 'Warning: Some users already voted, so be careful!' )
                                        . '</strong>' );
                }
                
                $crumb = get_lang( 'Edit poll' );
                $template = 'polledit';
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
            
            case 'rqDeleteVote':
            {
                if ( $poll )
                {
                    $crumb = get_lang( 'Delete vote' );
                    $msg = get_lang( 'Do you really want to delete this vote?' );
                    $urlAction = 'exDeleteVote';
                    $urlCancel = 'rqViewPoll';
                    $template = 'pollview';
                }
                else
                {
                    $dialogBox->error( get_lang( 'The poll has been deleted by course manager!' ) );
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
                elseif ( $poll->getOption( '_answer' ) == '_required' )
                {
                    $dialogBox->error( '<strong>' . get_lang( 'You must validate a choice!' ) . '</strong>' );
                }
                elseif ( ! $userVote->getPoll()->isOpen() )
                {
                    $dialogBox->error( get_lang( 'The votes for this poll are closed' ) );
                }
                elseif ( $poll->getOption( '_revote' ) != '_allowed' )
                {
                    $dialogBox->error( get_lang( 'You have allready voted!' ) );
                }
                else
                {
                    $dialogBox->error( '<strong>' . get_lang( 'You must validate only one choice!' ) . '</strong>' );
                }
                
                $template = 'pollview';
                break;
            }
            
            case 'exDeleteVote':
            {
                if ( $vote_deleted )
                {
                    $dialogBox->success( get_lang( 'The vote has been successfully deleted!' ) );
                }
                else
                {
                    $dialogBox->error( get_lang( 'Cannot delete the vote' ) );
                }
                
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
                    $dialogBox->error( '<strong>' . get_lang( 'You have not correctly filled the form!' ) . '</strong>' );
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
    $form->assign( 'userId' , $userInput->get( 'userId' ) );
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
                        $(document).ready(function(){
                            var nbToAdd=0;
                            $("#addChoice").click(function(){
                                nbToAdd++;
                                $("#choiceList").append("<li>'
                                                        . '<input id=\"choicex"+nbToAdd+"\" type=\"text\" name=\"add["+nbToAdd+"]\" value=\"\" size=\"40\" \/>'
                                                        . '<a id=\"delx"+nbToAdd+"\" class=\"delChoice claroCmd\" href=\"#delx"+nbToAdd+"\"> '
                                                        .    get_lang( 'Delete' )
                                                        . '<\/a>'
                                                        . '<script>'
                                                        .     '$(\"#delx"+nbToAdd+"\").click(function(){'
                                                        .         '$(this).parent().remove();'
                                                        .     '});'
                                                        . '<\/script>'
                                                    . '<\/li>");
                            });
                            
                            $(".delChoice").click(function(){
                                var choiceId = $(this).attr("id").substr(3);
                                $("#choice"+choiceId).attr({name:"del["+choiceId+"]"});
                                $("#choice"+choiceId).parent().hide();
                            });
                        });
                -->
                </script>';
                
            if ( ! $poll->getAllVoteList() || claro_is_allowed_to_edit() )
            {
                ClaroHeader::getInstance()->addHtmlHeader( $scriptContent );
            }
            
            $pollView->assign( 'change_allowed' , $change_allowed );
            break;
        
        case 'pollstat':
            $pageTitle[ 'subTitle' ] = get_lang( 'Poll statistics' );
            break;
        
        case 'pollview':
            $pollPager = new Pager( $poll->getAllVoteList( true ) , get_conf( 'pagerLineNb' ) );
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