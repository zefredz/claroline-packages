<?php // $Id$
/**
 * CLAROLINE
 *
 * @version 0.1 $Revision$
 *
 * @copyright (c) 2010 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLSUBSCR
 *
 * @author Dimitri Rambout <dimitri.rambout@uclouvain.be>
 *
 */

$tlabelReq = 'CLSUBSCR';

require_once dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';

if ( ! claro_is_user_authenticated() ) claro_disp_auth_form();

FromKernel::uses('utils/input.lib','utils/validator.lib','user.lib');
From::Module( $tlabelReq )->uses( 'subscriptions.lib', 'subscriptionsrenderer.lib' );

$jsLoader = JavascriptLoader::getInstance();
$jsLoader->load( 'claroline.ui');

//ClaroBreadCrumbs::getInstance()->prepend( get_lang('Subscriptions'), '../index.php'.claro_url_relay_context('?') );
ClaroBreadCrumbs::getInstance()->setCurrent( get_lang('Subscriptions'), './index.php' . claro_url_relay_context('?') );

claro_set_display_mode_available(true);

$dialogBox = new DialogBox();

try
{
    $userInput = Claro_UserInput::getInstance();
  
    $userInput->setValidator('cmd', new Claro_Validator_AllowedList( array(
        'list', 'rqAdd', 'exAdd', 'rqEdit', 'exEdit', 'rqDelete', 'exDelete', 'exVisible', 'exLock',
        'rqSlotAdd', 'exSlotAdd', 'rqSlotEdit', 'exSlotEdit', 'rqSlotChoice', 'exSlotChoice'
    ) ) );
    
    $cmd = $userInput->get( 'cmd','list' );
    
    $out = '';
    
    $out .= claro_html_tool_title( 'Subscriptions' );
    
    $cmdMenu = array();
    
    $cmdMenu[] = claro_html_cmd_link( 'index.php?cmd=list' . claro_url_relay_context( '&' ), get_lang( 'Home' ) );
    if( claro_is_allowed_to_edit() )
    {
        $cmdMenu[] = claro_html_cmd_link( 'index.php?cmd=rqAdd' . claro_url_relay_context( '&' ), get_lang( 'Create a new subscription' ) );
    }
    
    $out .= claro_html_menu_horizontal( $cmdMenu );
    
    switch( $cmd )
    {
        case 'exSlotChoice' :
            {
                if( ! isset( $_REQUEST['subscrId'] ) )
                {
                    $dialogBox->error( get_lang( 'Unable to load this subscription.') . ' ' . get_lang( 'The ID is missing.' ) );
                    
                    $out .= $dialogBox->render();
                }
                else
                {
                    $subscription = new subscription();
                    
                    if( ! $subscription->load( $_REQUEST['subscrId'] ) )
                    {
                        $dialogBox->error( get_lang( 'Unable to load this subscription.' ) );
                    }
                    elseif( ! isset( $_POST['choice'] ) )
                    {
                        $dialogBox->error( get_lang( 'No choice selected.') );
                    
                        $out .= $dialogBox->render();
                        
                        $slotsCollection = new slotsCollection();
                        
                        $allSlots = $slotsCollection->getAll( $subscription->getId() );
                        $allSlotsFromUsers = $slotsCollection->getAllFromUser( claro_get_current_user_id() );
                        $out .= SubscriptionsRenderer::displaySubscription( $subscription,
                                                                            $allSlots,
                                                                            $allSlotsFromUsers
                                                                            );
                    }
                    else
                    {
                        $subscriptionType = $subscription->getType();
                        
                        From::Module( $tlabelReq )->loadConnectors( $subscriptionType );
                        
                        $className = 'slot' . ucfirst( $subscriptionType );
                        if(  ! class_exists( $className ) ){
                            claro_die( 'ERROR IN CONNECTOR' );
                        }
                        
                        $slot = new $className();
                        
                        //$slot = new slot();
                        
                        if( ! $slot->load( $_POST['choice'] ) )
                        {
                            $dialogBox->error( get_lang( 'Unable to load this slot.' ) );
                            
                            $out .= $dialogBox->render();
                        }
                        else
                        {
                            if( $subscription->isLocked() )
                            {
                                $dialogBox->error( get_lang( 'Unable to save your choice.' ) . ' ' . get_lang( 'The subscription is locked.' ) );
                                
                                $out .= $dialogBox->render();
                                
                                $slotsCollection = new slotsCollection();
                        
                                $allSlots = $slotsCollection->getAll( $subscription->getId() );
                                $allSlotsFromUsers = $slotsCollection->getAllFromUser( claro_get_current_user_id() );
                                $out .= SubscriptionsRenderer::displaySubscription( $subscription,
                                                                                    $allSlots,
                                                                                    $allSlotsFromUsers
                                                                                    );
                            }
                            else
                            {
                                // Save the choice for the user/slot
                                if( ( $resultSave = $slot->saveSubscriberChoice( claro_get_current_user_id(), $subscription->getId(), $subscription->getContext() ) ) != 1 )
                                {
                                     $dialogBox->error( get_lang( $resultSave ) );
                                
                                    $out .= $dialogBox->render();
                                }
                                else
                                {
                                    $dialogBox->success(
                                        get_lang( 'Choice saved successfully.' ) .
                                        "<br />\n<br />\n" .
                                        '<a href="' . $_SERVER['PHP_SELF'] . claro_url_relay_context( '?' ) . '">' .
                                        get_lang( 'Continue' ) .
                                        '</a>'
                                    );
                                    
                                    $out .= $dialogBox->render();
                                }
                            }
                        }
                    }
                }
            }
            break;
        case 'rqSlotChoice' :
            {
                if( ! isset( $_REQUEST['subscrId'] ) )
                {
                    $dialogBox->error( get_lang( 'Unable to load this subscription.') . ' ' . get_lang( 'The ID is missing.' ) );
                    
                    $out .= $dialogBox->render();
                }
                else
                {
                    $subscription = new subscription();
                    
                    if( ! $subscription->load( $_REQUEST['subscrId'] ) )
                    {
                        $dialogBox->error( get_lang( 'Unable to load this subscription.' ) );
                    }
                    else
                    {
                        $slotsCollection = new slotsCollection();
                        
                        $allSlots = $slotsCollection->getAll( $subscription->getId() );
                        $allSlotsFromUsers = $slotsCollection->getAllFromUser( claro_get_current_user_id() );
                        $out .= SubscriptionsRenderer::displaySubscription( $subscription,
                                                                            $allSlots,
                                                                            $allSlotsFromUsers
                                                                            );
                    }
                }
            }
            break;
        case 'rqSlotEdit' :
            {
                $subscription = new subscription();
                
                if( $result = checkRequestSubscription( $subscription, $dialogBox ) )
                {
                    $out .= $result;
                }
                else
                {
                    if( ! isset( $_REQUEST['slotId'] ) )
                    {
                        $dialogBox->error( get_lang( 'Unable to load this slot.' ) . ' ' . get_lang( 'The ID is missing.' ) );
                        
                        $out .= $dialogBox->render();
                    }
                    else
                    {
                        $slot = new slot();
                        if( ! $slot->load( $_REQUEST['slotId'] ) )
                        {
                            $dialogBox->error( get_lang( 'Unable to load this slot.' ) );
                            
                            $out .= $dialogBox->render();
                        }
                        else
                        {
                            $slotsCollection = new slotsCollection();
                            
                            $out .= SubscriptionsRenderer::editSlot(    $subscription,
                                                                        $slot,
                                                                        $slotsCollection->getAll( $subscription->getId() ),
                                                                        $slotsCollection->getAllFromUser( claro_get_current_user_id() )
                                                                    );
                        }
                    }                    
                }
            }
            break;
        case 'exSlotAdd' :
            {
                if( ! claro_is_allowed_to_edit() )
                {
                    claro_die( get_lang( 'Not allowed' ) );
                }
                
                if( ! isset( $_REQUEST['subscrId'] ) )
                {
                    $dialogBox->error( get_lang( 'Unable to load this subscription.') . ' ' . get_lang( 'The ID is missing.' ) );
                    
                    $out .= $dialogBox->render();
                }
                else
                {
                    if( ( isset( $_POST['title'] ) && is_array( $_POST['title'] ) && count( $_POST['title'] ) )
                        &&
                        ( isset( $_POST['description'] ) && is_array( $_POST['description'] ) && count( $_POST['description'] ) )
                        &&
                        ( isset( $_POST['places'] ) && is_array( $_POST['places'] ) && count( $_POST['places'] ) )
                        && ( ( count( $_POST['title'] ) == count( $_POST['description'] ) ) == count( $_POST['places'] ) )
                    )
                    {
                        $subscription = new subscription();
                    
                        if( ! $subscription->load( $_REQUEST['subscrId'] ) )
                        {
                            $dialogBox->error( get_lang( 'Unable to load this subscription.' ) );
                            
                            $out .= $dialogBox->render();
                        }
                        else
                        {
                            $errors = array();
                            $error = false;
                            $slots = array();
                            foreach( $_POST['title'] as $i => $title )
                            {
                                $slot = new slot();
                                $slot->setTitle( $title );
                                $slot->setDescription( $_POST['description'][ $i ] );
                                $slot->setAvailableSpace( $_POST['places'][ $i ]);
                                $slot->setSubscriptionId( $_REQUEST['subscrId'] );
                                
                                $slots[] = $slot;
                                
                                if( ! $slot->validate() )                                
                                {
                                    $errors[] = true;
                                    $error = true;
                                }
                                else
                                {
                                    $errors[] = false;
                                }
                            }
                            
                            if( $error )
                            {
                                $out .= SubscriptionsRenderer::addSlot( $subscription, $dialogBox, count( $_POST['title'] ), null, array( 'title' => $_POST['title'], 'description' => $_POST['description'], 'places' => $_POST['places'], 'errors' => $errors ) );
                            }
                            else
                            {
                                // each slot is valide and can be saved
                                foreach( $slots as $slot )
                                {
                                    if( ! $slot->save() )
                                    {
                                        $error = false;
                                    }
                                }
                                
                                if( $error )
                                {
                                    $out .= SubscriptionsRenderer::addSlot( $subscription, $dialogBox, count( $_POST['title'] ), null, array( 'title' => $_POST['title'], 'description' => $_POST['description'], 'places' => $_POST['places'], 'errors' => $errors ) );
                                }
                                else
                                {
                                    $dialogBox->success( get_lang( 'Slots added successfully to the subscription.' ) );
                            
                                    $out .= $dialogBox->render();
                                }
                            }
                            
                        }
                    }
                }                
            }
            break;
        case 'rqSlotAdd' :
            {
                if( ! claro_is_allowed_to_edit() )
                {
                    claro_die( get_lang( 'Not allowed' ) );
                }
                
                
                if( ! isset( $_REQUEST['subscrId'] ) )
                {
                    $dialogBox->error( get_lang( 'Unable to load this subscription.') . ' ' . get_lang( 'The ID is missing.' ) );
                    
                    $out .= $dialogBox->render();
                }
                else
                {
                    $subscription = new subscription();
                    
                    if( ! $subscription->load( $_REQUEST['subscrId'] ) )
                    {
                        $dialogBox->error( get_lang( 'Unable to load this subscription.' ) );
                        
                        $out .= $dialogBox->render();
                    }
                    else
                    {
                        
                        
                        if( isset( $_POST['slots'] ) && isset( $_POST['places'] ) )
                        {
                            $out .= SubscriptionsRenderer::addSlot( $subscription, $dialogBox, $_POST['slots'], $_POST['places'] );
                        }
                        else
                        {
                            $out .= SubscriptionsRenderer::addSlot( $subscription, $dialogBox );
                        }
                            
                            /*$slots = (int) $_POST['slots'];
                            
                            for( $i=0; $i < $slots; $i++ )
                            {
                                $out .= SubscriptionsRenderer::addSlotPlaces( $_POST['places'] );
                            }*/
                    }                    
                }
            }
            break;
        case 'exLock' :
            {
                
                if( ! claro_is_allowed_to_edit() )
                {
                    claro_die( get_lang( 'Not allowed' ) );
                }
                
                $subscription = new subscription();
                
                if( $result = checkRequestSubscription( $subscription, $dialogBox ) )
                {
                    $out .= $result;
                }
                else
                {
                    if( $subscription->isLocked() )
                    {
                        $subscription->setLock( 'open' );
                        if( ! $subscription->save() )
                        {
                            $dialogBox->error( get_lang( 'Unable to change the lock of the subscription.' ) );
                        }
                        else
                        {
                            $dialogBox->success( get_lang( 'The subscription is now open.' ) );
                        }
                    }
                    else
                    {
                        $subscription->setLock( 'close' );
                        if( ! $subscription->save() )
                        {
                            $dialogBox->error( get_lang( 'Unable to change the lock of the subscription.' ) );
                        }
                        else
                        {
                            $dialogBox->success( get_lang( 'The subscription is now closed.' ) );
                        }
                    }
                    
                    $out .= $dialogBox->render();
                    
                    $subscriptionsCollection = new subscriptionsCollection();
                    
                    $slotsCollection = new slotsCollection();
                    
                    $allSlotsFromUsers = $slotsCollection->getAllFromUser( claro_get_current_user_id() );
                    $out .= SubscriptionsRenderer::listSubscriptions( $subscriptionsCollection, $allSlotsFromUsers );
                }
            }
            break;
        case 'exVisible' :
            {
                if( ! claro_is_allowed_to_edit() )
                {
                    claro_die( get_lang( 'Not allowed' ) );
                }
                
                $subscription = new subscription();
                
                if( $result = checkRequestSubscription( $subscription, $dialogBox ) )
                {
                    $out .= $result;
                }
                else
                {
                    if( $subscription->isVisible() )
                    {
                        $subscription->setVisibility( 'invisible' );
                        if( ! $subscription->save() )
                        {
                            $dialogBox->error( get_lang( 'Unable to change the visibility of the subscription.' ) );
                        }
                        else
                        {
                            $dialogBox->success( get_lang( 'The subscription is now invisible.' ) );
                        }
                    }
                    else
                    {
                        $subscription->setVisibility( 'visible' );
                        if( ! $subscription->save() )
                        {
                            $dialogBox->error( get_lang( 'Unable to change the visibility of the subscription.' ) );
                        }
                        else
                        {
                            $dialogBox->success( get_lang( 'The subscription is now visible.' ) );
                        }
                    }
                    
                    $out .= $dialogBox->render();
                    
                    $subscriptionsCollection = new subscriptionsCollection();
                    
                    $slotsCollection = new slotsCollection();
                    
                    $allSlotsFromUsers = $slotsCollection->getAllFromUser( claro_get_current_user_id() );
                    $out .= SubscriptionsRenderer::listSubscriptions( $subscriptionsCollection, $allSlotsFromUsers );
                }
            }
            break;
        case 'exDelete' :
            {
                if( ! claro_is_allowed_to_edit() )
                {
                    claro_die( get_lang( 'Not allowed' ) );
                }
                
                $subscription = new subscription();
                
                if( $result = checkRequestSubscription( $subscription, $dialogBox ) )
                {
                    $out .= $result;
                }
                else
                {
                    if( ! $subscription->delete() )
                    {
                        $dialogBox->error( get_lang( 'Impossible to delete the subscription <strong>%title</strong>', array( '%title' => $subscription->getTitle() ) ) );
                    }
                    else
                    {
                        $dialogBox->success( get_lang( 'Subscription <strong>%title</strong> deleted successfully', array( '%title' => $subscription->getTitle() ) ) );
                    }
                    
                    $out .= $dialogBox->render();
                }
            }
            break;
        case 'rqDelete' :
            {
                if( ! claro_is_allowed_to_edit() )
                {
                    claro_die( get_lang( 'Not allowed' ) );
                }
                
                $subscription = new subscription();
                
                if( $result = checkRequestSubscription( $subscription, $dialogBox ) )
                {
                    $out .= $result;
                }
                else
                {
                    $out .= SubscriptionsRenderer::delete( $subscription );
                }
            }
            break;
        case 'exEdit' :
            {
                if( ! claro_is_allowed_to_edit() )
                {
                    claro_die( get_lang( 'Not allowed' ) );
                }
                
                $subscription = new subscription();
                
                if( $result = checkRequestSubscription( $subscription, $dialogBox ) )
                {
                    $out .= $result;
                }
                else
                {
                    $subscription->setTitle( $userInput->get( 'title' ) );
                    $subscription->setDescription( $userInput->get( 'description' ) );
                    $subscription->setContext( $userInput->get( 'context' ) );
                    $subscription->setType( $userInput->get( 'type' ) );
                    $subscription->setVisibility( $userInput->get( 'visibility' ) );
                    
                    if( $subscription->validate() )
                    {
                        if( $subscription->save() )
                        {
                            $dialogBox->success( get_lang( 'Subscription saved successfully.' ) );
                        }
                        else
                        {
                            $dialogBox->error( get_lang( 'Unable to save the subscription.' ) );
                        }
                        
                        $out .= $dialogBox->render();
                    }
                    else
                    {
                        $errors = $subscription->getErrors( true );
                        
                        
                        $dialogBox->error( get_lang('Unable to save the subscription.') . ' ' .get_lang( 'Please check the following errors.') . "<br />\n<br />\n" . str_replace( "\n", "<br />\n", $errors ) );
                        
                        $out .= $dialogBox->render();
                        
                        //set fields for form                    
                        $out .= SubscriptionsRenderer::add( $subscription );
                    }
                }
            }
            break;
        case 'rqEdit' :
            {
                if( ! claro_is_allowed_to_edit() )
                {
                    claro_die( get_lang( 'Not allowed' ) );
                }
                
                $subscription = new subscription();
                
                if( $result = checkRequestSubscription( $subscription, $dialogBox ) )
                {
                    $out .= $result;
                }
                else
                {
                    $out .= SubscriptionsRenderer::edit( $subscription );
                }
            }
            break;        
        case 'exAdd' :
            {
                if( ! claro_is_allowed_to_edit() )
                {
                    claro_die( get_lang( 'Not allowed' ) );
                }
                
                $subscription = new subscription();
                $subscription->setTitle( $userInput->get( 'title' ) );
                $subscription->setDescription( $userInput->get( 'description' ) );
                $subscription->setContext( $userInput->get( 'context' ) );
                $subscription->setType( $userInput->get( 'type' ) );
                $subscription->setVisibility( $userInput->get( 'visibility' ) );
                
                if( $subscription->validate() )
                {
                    if( $subscription->save() )
                    {
                        $dialogBox->success( get_lang( 'Subscription saved successfully.' ) . "<br />\n<br />" . get_lang( 'If you want to continue by adding slots to your subscription, click <strong><a href="%ahrefContinue">here</a></strong>.', array( '%ahrefContinue' => 'index.php?cmd=rqSlotAdd&subscrId=' . $subscription->getId() . claro_url_relay_context( '&' ) ) ) );
                    }
                    else
                    {
                        $dialogBox->error( get_lang( 'Unable to save the subscription.' ) );
                    }
                    
                    $out .= $dialogBox->render();
                }
                else
                {
                    $errors = $subscription->getErrors( true );
                    
                    
                    $dialogBox->error( get_lang('Unable to save the subscription.') . ' ' .get_lang( 'Please check the following errors.') . "<br />\n<br />\n" . str_replace( "\n", "<br />\n", $errors ) );
                    
                    $out .= $dialogBox->render();
                    
                    //set fields for form                    
                    $out .= SubscriptionsRenderer::add( $subscription );
                }
                
            }
            break;
        case 'rqAdd' :
            {
                if( ! claro_is_allowed_to_edit() )
                {
                    claro_die( get_lang( 'Not allowed' ) );
                }
                
                $out .= SubscriptionsRenderer::add();
            }
            break;
        case 'list' :
            {
               $subscriptionsCollection = new subscriptionsCollection();
               
               $slotsCollection = new slotsCollection();
               $allSlotsFromUsers = $slotsCollection->getAllFromUser( claro_get_current_user_id() );
               $out .= SubscriptionsRenderer::listSubscriptions( $subscriptionsCollection, $allSlotsFromUsers ); 
            }
            break;
    }
    
    $claroline->display->body->appendContent( $out );

}
catch(Exception $e )
{
  if ( claro_debug_mode() )
  {
    $dialogBox->error( '<pre>' . $e->__toString() . '</pre>' );
  }
  else
  {
    $dialogBox->error( $e->getMessage() );
  }
  
  Claroline::getDisplay()->body->appendcontent( $dialogBox->render() );
}

echo $claroline->display->render();
?>