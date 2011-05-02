<?php // $Id$
/**
 * CLAROLINE
 *
 * @version 0.2 $Revision$
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

FromKernel::uses(
    'utils/input.lib',
    'utils/validator.lib',
    'user.lib'
);

From::Module( $tlabelReq )->uses( 
    'subscriptions.lib',
    'subscriptionsrenderer.lib',
    'datetool.lib'
);

$nameTools = get_lang( 'Subscriptions' );

$jsLoader = JavascriptLoader::getInstance();
$jsLoader->load( 'claroline.ui');

ClaroBreadCrumbs::getInstance()->setCurrent(
    get_lang('Subscriptions'),
    htmlspecialchars( Url::Contextualize( php_self() ) )
);

claro_set_display_mode_available(true);

$dialogBox = new DialogBox();

$context = claro_is_in_a_group() ? 'group' : 'user';

$groupId = claro_get_current_group_id();

try
{
    $userInput = Claro_UserInput::getInstance();
    
    if ( claro_is_allowed_to_edit() )
    {
        $userInput->setValidator( 
            'cmd',
            new Claro_Validator_AllowedList( array(
                'list',
                'rqAdd', 'exAdd',
                'rqEdit', 'exEdit',
                'rqDelete', 'exDelete',
                'exVisible', 'exInvisible',
                'exLock',
                'rqResult',
                'export',
                'rqSlotAdd', 'exSlotAdd',
                'rqSlotEdit', 'exSlotEdit',
                'rqSlotDelete', 'exSlotDelete',
                'rqSlotChoice', 'exSlotChoice',
                'rqRemoveChoice', 'exRemoveChoice',
                // 'rqEditChoice', 'exEditChoice', // not implemented yet
                'exSlotVisible'
            ) )
        );
    }
    else
    {
        $userInput->setValidator( 
            'cmd',
            new Claro_Validator_AllowedList( array(
                'list',
                'rqSlotChoice', 'exSlotChoice'
            ) )
        );
    }
    
    $cmd        = $userInput->get( 'cmd', 'list' );
    $subscrId   = $userInput->get( 'subscrId' );
    $slotId     = $userInput->get( 'slotId' );
    $type       = $userInput->get( 'type' );
    
    $subscription = new Subscription( $subscrId );
    
    $out = '';
    
    $out .= claro_html_tool_title( get_lang( 'Subscriptions' ) );
    
    $cmdMenu = array();
    
    $cmdMenu[] = claro_html_cmd_link( 
        htmlspecialchars( Url::Contextualize( php_self() . '?cmd=list' ) ),
        get_lang( 'Subscriptions list' ) );

    if( claro_is_allowed_to_edit() )
    {
        $cmdMenu[] = claro_html_cmd_link( 
            htmlspecialchars( Url::Contextualize( php_self() . '?cmd=rqAdd' ) ),
            get_lang( 'Create a new subscription' ) );
    }
    
    $out .= claro_html_menu_horizontal( $cmdMenu );
    
    if( $subscrId
        && $result = checkRequestSubscription( $subscription, $dialogBox ) )
    {
        $out .= $result;
    }
    else
    {
        switch( $cmd )
        {
            case 'rqRemoveChoice' :
            {
                try
                {
                    // are you sure ?
                    $subscriberId = Claroline::getDatabase()->escape( $userInput->getMandatory('subscriberId') );
                    $subscriptionId = Claroline::getDatabase()->escape( $userInput->getMandatory('subscriptionId') );
                    $slotId = Claroline::getDatabase()->escape( $userInput->getMandatory('slotId') );

                    $dialogBox->question( '<p>' . get_lang( "Are you sure you want to delete this user choice ?" ) . '</p>'
                        . '<a href="' 
                        . htmlspecialchars( Url::Contextualize($_SERVER['PHP_SELF'] 
                             . '?cmd=exRemoveChoice&slotId=' . $slotId
                             . '&subscriberId=' . $subscriberId
                             . '&subscriptionId=' . $subscriptionId ) ).'">'
                        . get_lang('Yes') 
                        . '</a>'
                        . '&nbsp;-&nbsp;'
                        . '<a href="'
                        . htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) ) .'">'
                        . get_lang('No')
                        . '</a>'
                    );
                }
                catch ( Exception $e )
                {
                    $dialogBox->error(
                        get_lang(
                            "An error occured %error%, please contact the administrator", 
                            array( '%error%' => $e->getMessage() )
                        )
                    );
                }
                
                $out .= $dialogBox->render();
                
                break;
            }
            case 'exRemoveChoice' :
            {
                try
                {
                    $subscriberId = Claroline::getDatabase()->escape( $userInput->getMandatory('subscriberId') );
                    $subscriptionId = Claroline::getDatabase()->escape( $userInput->getMandatory('subscriptionId') );
                    $slotId = Claroline::getDatabase()->escape( $userInput->getMandatory('slotId') );

                    $tbl = get_module_course_tbl( array( 'subscr_slots_subscribers' ) );

                    if ( Claroline::getDatabase()->exec( "
                        DELETE FROM 
                            `{$tbl['subscr_slots_subscribers']}`
                        WHERE 
                            subscriberId = {$subscriberId}
                        AND 
                            subscriptionId = {$subscriptionId}
                        AND 
                            slotId = {$slotId};" ) )
                    {
                        $dialogBox->success(get_lang("User choice deleted"));
                        
                        Console::info("User choice slot={$slotId}:subscription={$subscriptionId}:context={$subscription->getContext()}:subscriber={$subscriberId} deleted by "
                            . claro_get_current_user_id() . ' in course ' . claro_get_current_course_id()
                        );
                    }
                    else
                    {
                        $dialogBox->error(get_lang("Cannot delete user choice"));
                    }
                }
                catch ( Exception $e )
                {
                    $dialogBox->error(
                        get_lang(
                            "An error occured %error%, please contact the administrator", 
                            array( '%error%' => $e->getMessage() )
                        )
                    );
                }
                
                $out .= $dialogBox->render();
                    
                break;
            }
            case 'exSlotVisible' :
            {
                if( ! isset( $slotId ) )
                {
                    $dialogBox->error( 
                        get_lang( 'Unable to load this slot.' )
                        . ' '
                        . get_lang( 'The ID is missing.' )
                    );
                    
                    $out .= $dialogBox->render();
                }
                else
                {
                    $slot = new slot();

                    if( ! $slot->load( $slotId ) )
                    {
                        $dialogBox->error( 
                            get_lang( 'Unable to load this slot.' )
                        );
                        
                        $out .= $dialogBox->render();
                    }
                    else
                    {
                        if( $slot->isVisible() )
                        {
                            $slot->setVisibility( 'invisible' );
                            
                            if( ! $slot->save() )
                            {
                                $dialogBox->error( 
                                    get_lang( 'Unable to change the visibility of the slot.' )
                                );
                            }
                            else
                            {
                                $dialogBox->success( 
                                    get_lang( 'The slot is now invisible.' )
                                );
                            }
                        }
                        else
                        {
                            $slot->setVisibility( 'visible' );
                            
                            if( ! $slot->save() )
                            {
                                $dialogBox->error( 
                                    get_lang( 'Unable to change the visibility of the slot.' )
                                );
                            }
                            else
                            {
                                $dialogBox->success( 
                                    get_lang( 'The slot is now visible.' )
                                );
                            }
                        }
                        $out .= $dialogBox->render();
                        
                        $slotsCollection = new slotsCollection();
                    
                        $allSlots = $slotsCollection->getAll( $subscription->getId() );
                        
                        $subscriberId = claro_is_in_a_group() && $subscription->getContext() == 'group' 
                            ? $groupId
                            : claro_get_current_user_id()
                            ;

                        $allSlotsFromUsers = $slotsCollection->getAllFromUser( 
                            $subscriberId,
                            $subscription->getContext()
                        );

                        $out .= SubscriptionsRenderer::displaySubscription(
                            $subscription,
                            $allSlots,
                            $allSlotsFromUsers
                        );
                    }
                }
            }
            break;
            
            case 'exSlotChoice' :
            {
                if( ( ( $subscription->getContext() == 'group' && ! claro_is_in_a_group() ) 
                    || ( $subscription->getContext() == 'user' && claro_is_in_a_group() ) )
                        && ! claro_is_allowed_to_edit() )
                {
                    $dialogBox->error( 
                        get_lang( 'You must be in a group to access this session' )
                    );
                    
                    $out .= $dialogBox->render();
                }
                else
                {
                    if( ! isset( $_POST['choice'] ) )
                    {
                        $dialogBox->error( get_lang( 'No choice selected.') );
                    
                        $out .= $dialogBox->render();
                        
                        $slotsCollection = new slotsCollection();
                        
                        $allSlots = $slotsCollection->getAll( $subscription->getId() );
                        
                        $subscriberId = claro_is_in_a_group() && $subscription->getContext() == 'group' 
                            ? $groupId
                            : claro_get_current_user_id()
                            ;

                        $allSlotsFromUsers = $slotsCollection->getAllFromUser( 
                            $subscriberId,
                            $subscription->getContext()
                        );

                        $out .= SubscriptionsRenderer::displaySubscription(
                            $subscription,
                            $allSlots,
                            $allSlotsFromUsers
                        );
                    }
                    else
                    {
                        $subscriptionType = $subscription->getType();
                        
                        From::Module( $tlabelReq )->loadPlugins( $subscriptionType );
                        
                        $className = 'slot' . ucfirst( $subscriptionType );

                        if(  ! class_exists( $className ) )
                        {
                            claro_die( 'ERROR IN PLUGIN' );
                        }
                        
                        $slot = new $className();
                        
                        if( ! $slot->load( $_POST['choice'] ) )
                        {
                            $dialogBox->error( get_lang( 'Unable to load this slot.' ) );
                            
                            $out .= $dialogBox->render();
                        }
                        else
                        {
                            if( ! $slot->isVisible() &&  ! claro_is_allowed_to_edit() )
                            {
                                claro_die( get_lang( 'Not allowed' ) );
                            }
                            
                            if( $subscription->isLocked() )
                            {
                                $dialogBox->error( 
                                    get_lang( 'Unable to save your choice.' )
                                    . ' '
                                    . get_lang( 'The subscription is locked.' )
                                );
                                
                                $out .= $dialogBox->render();
                                
                                $slotsCollection = new slotsCollection();
                        
                                $allSlots = $slotsCollection->getAll( $subscription->getId() );
                                
                                $subscriberId = claro_is_in_a_group() && $subscription->getContext() == 'group' 
                                    ? $groupId
                                    : claro_get_current_user_id()
                                    ;

                                $allSlotsFromUsers = $slotsCollection->getAllFromUser( 
                                    $subscriberId,
                                    $subscription->getContext()
                                );

                                $out .= SubscriptionsRenderer::displaySubscription(
                                    $subscription,
                                    $allSlots,
                                    $allSlotsFromUsers
                                );
                            }
                            else
                            {
                                // Save the choice for the user/slot
                                $subscriberId = claro_is_in_a_group() && $subscription->getContext() == 'group' 
                                    ? $groupId
                                    : claro_get_current_user_id()
                                    ;

                                $resultSave = $slot->saveSubscriberChoice(
                                    $subscriberId,
                                    $subscription->getId(),
                                    $subscription->getContext()
                                );
                                
                                if( !$resultSave )
                                {
                                     $dialogBox->error( get_lang( $resultSave ) );
                                
                                    $out .= $dialogBox->render();
                                }
                                else
                                {
                                    $dialogBox->success(
                                        get_lang( 'Choice saved successfully.' ) .
                                        "<br />\n<br />\n" .
                                        '<a href="' . htmlspecialchars( Url::Contextualize( php_self() ) ) . '">' .
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
                if( ( ( $subscription->getContext() == 'group' 
                    && ! claro_is_in_a_group() )
                    || ( $subscription->getContext() == 'user'
                        && claro_is_in_a_group() ) )
                        && ! claro_is_allowed_to_edit() )
                {
                    From::Module('CLSUBSCR')->uses('groupchooser.lib');
                    
                    $groupChooser = new UserGroupChooser(
                        $_SERVER['PHP_SELF'] . '?cmd=rqSlotChoice&subscrId=' . $subscrId,
                        claro_get_current_user_id(),
                        claro_get_current_course_id()
                    );
                    
                    $dialogBox->error( 
                        get_lang( 'You must be in a group to access this session' )
                        . '<br />'
                        . $groupChooser->render()
                    );
                    
                    $out .= $dialogBox->render();
                }
                else
                {
                    $slotsCollection = new slotsCollection();
                    
                    $allSlots = $slotsCollection->getAll( $subscription->getId() );
                    
                    $subscriberId = claro_is_in_a_group() && $subscription->getContext() == 'group' 
                        ? $groupId
                        : claro_get_current_user_id()
                        ;

                    $allSlotsFromUsers = $slotsCollection->getAllFromUser( 
                        $subscriberId,
                        $subscription->getContext()
                    );

                    $out .= SubscriptionsRenderer::displaySubscription(
                        $subscription,
                        $allSlots,
                        $allSlotsFromUsers
                    );
                }
            }
            break;
            
            case 'exSlotDelete' :
            {
                if( ! isset( $slotId ) )
                {
                    $dialogBox->error( 
                        get_lang( 'Unable to load this slot.' )
                        . ' '
                        . get_lang( 'The ID is missing.' )
                    );
                    
                    $out .= $dialogBox->render();
                }
                else
                {
                    $slot = new slot();
                    if( ! $slot->load( $slotId ) )
                    {
                        $dialogBox->error( get_lang( 'Unable to load this slot.' ) );
                        
                        $out .= $dialogBox->render();
                    }
                    else
                    {
                        if( ( $totalSubscribers = $slot->totalSubscribers() ) > 0
                            && ! ( isset( $_GET['confirmDelete'] ) ) )
                        {
                            //Warning if the total of Subsctibers for this slot is not 0
                            $dialogBox->warning(
                                get_lang( 'There are %totalSubscribers subsctibers registered to this slot. Do you confirm you want to delete the slot <strong>%slotTitle</strong> in session <strong>%sessionTitle</strong> ?',
                                    array(
                                        '%totalSubscribers' => $totalSubscribers,
                                        '%slotTitle' => $slot->getTitle(),
                                        '%sessionTitle' => $subscription->getTitle() ) )
                                . '<br /><br />'
                                . '<a href="'
                                    . htmlspecialchars( Url::Contextualize(
                                        $_SERVER['PHP_SELF']
                                        . '?cmd=exSlotDelete&confirmDelete=1&slotId='
                                        . $slot->getId()
                                        . '&subscrId='
                                        . $subscription->getId() ) )
                                . '">' . get_lang('Yes') . '</a>'
                                . '&nbsp;|&nbsp;'
                                . '<a href="' . htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) ) . '">'
                                    . get_lang('No')
                                . '</a>'
                            );
                            
                            $out .= $dialogBox->render();
                        }
                        else
                        {
                            //Delete the slot
                            if( ! $slot->delete() )
                            {
                                $dialogBox->error(
                                    get_lang(
                                        'Unable to delete the slot <strong>%slotTitle</strong> in session <strong>%sessionTitle</strong>.',
                                        array( '%slotTitle' => $slot->getTitle(), '%sessionTitle' => $subscription->getTitle() )
                                    )
                                );
                                
                                $out .= $dialogBox->render();
                            }
                            else
                            {
                                $dialogBox->success(
                                    get_lang(
                                        'Slot <strong>%slotTitle</strong> in session <strong>%sessionTitle</strong> deleted successfully.',
                                        array( '%slotTitle' => $slot->getTitle(), '%sessionTitle' => $subscription->getTitle() )
                                    )
                                );
                                
                                $out .= $dialogBox->render();
                            }
                        }
                    }
                }
            }
            break;
            
            case 'rqSlotDelete' :
            {
                if( ! isset( $slotId ) )
                {
                    $dialogBox->error( 
                        get_lang( 'Unable to load this slot.' )
                        . ' '
                        . get_lang( 'The ID is missing.' )
                    );
                    
                    $out .= $dialogBox->render();
                }
                else
                {
                    $slot = new slot();

                    if( ! $slot->load( $slotId ) )
                    {
                        $dialogBox->error( get_lang( 'Unable to load this slot.' ) );
                        
                        $out .= $dialogBox->render();
                    }
                    else
                    {
                        $out .= SubscriptionsRenderer::deleteSlot( $subscription, $slot );
                    }
                }
            }
            break;
            
            case 'exSlotEdit' :
            {
                if( ! isset( $slotId ) )
                {
                    $dialogBox->error( 
                        get_lang( 'Unable to load this slot.' )
                        . ' '
                        . get_lang( 'The ID is missing.' )
                    );
                    
                    $out .= $dialogBox->render();
                }
                else
                {
                    $slot = new slot();
                    if( ! $slot->load( $slotId ) )
                    {
                        $dialogBox->error( get_lang( 'Unable to load this slot.' ) );
                        
                        $out .= $dialogBox->render();
                    }
                    else
                    {
                        if( ! ( isset( $_POST['title'] ) 
                            && isset( $_POST['description'] )
                            && isset( $_POST['places'] ) ) )
                        {
                            $dialogBox->error( get_lang( 'Unable to save this slot.' ) );
                        
                            $out .= $dialogBox->render();
                        }
                        else
                        {
                            $slot
                                ->setTitle( $_POST['title'] )
                                ->setDescription( $_POST['description'] )
                                ->setAvailableSpace( $_POST['places'] );
                            
                            if( ! $slot->validate() )
                            {
                                $dialogBox->error( get_lang( 'Unable to save this slot.' ) );
                                
                                $out .= $dialogBox->render();
                                
                                $slotsCollection = new slotsCollection();
                                $allSlots = $slotsCollection->getAll( $subscription->getId() );
                                
                                $out .= SubscriptionsRenderer::editSlot(    $subscription,
                                                                            $slot,
                                                                            $allSlots,
                                                                            true
                                                                        );
                            }
                            else
                            {
                                //check if the number of subscribers in the slot ($oldAvailableSpace - $slot->spaceAvailable()) is not lower than the new availableSpace
                                if( $slot->totalSubscribers() > $slot->getAvailableSpace() )
                                {
                                    $dialogBox->error( get_lang( 'There are more subscribers registered to this slot than the number of available places you defined.' ) );
                                
                                    $out .= $dialogBox->render();
                                    
                                    $slotsCollection = new slotsCollection();
                                    $allSlots = $slotsCollection->getAll( $subscription->getId() );
                                    
                                    $out .= SubscriptionsRenderer::editSlot(    $subscription,
                                                                                $slot,
                                                                                $allSlots,
                                                                                true
                                                                            );
                                }
                                elseif( ! $slot->save() )
                                {
                                    $dialogBox->error( get_lang( 'Unable to save this slot.' ) );
                                
                                    $out .= $dialogBox->render();
                                    
                                    $slotsCollection = new slotsCollection();
                                    $allSlots = $slotsCollection->getAll( $subscription->getId() );
                                    
                                    $out .= SubscriptionsRenderer::editSlot(    $subscription,
                                                                                $slot,
                                                                                $allSlots,
                                                                                true
                                                                            );
                                }
                                else
                                {
                                    $dialogBox->success( get_lang( 'Slot saved successfully.' ) );
                                    
                                    $out .= $dialogBox->render();
                                    
                                    $slotsCollection = new slotsCollection();
                    
                                    $allSlots = $slotsCollection->getAll( $subscription->getId() );
                                    
                                    $subscriberId = claro_is_in_a_group() && $subscription->getContext() == 'group' ? $groupId : claro_get_current_user_id();
                                    $allSlotsFromUsers = $slotsCollection->getAllFromUser( $subscriberId, $subscription->getContext() );
                                    $out .= SubscriptionsRenderer::displaySubscription( $subscription,
                                                                                        $allSlots,
                                                                                        $allSlotsFromUsers
                                                                                        );
                                }
                            }
                        }
                    }
                }
            }
            break;
            
            case 'rqSlotEdit' :
            {
                if( ! isset( $slotId ) )
                {
                    $dialogBox->error( get_lang( 'Unable to load this slot.' ) . ' ' . get_lang( 'The ID is missing.' ) );
                    
                    $out .= $dialogBox->render();
                }
                else
                {
                    $slot = new slot();
                    if( ! $slot->load( $slotId ) )
                    {
                        $dialogBox->error( get_lang( 'Unable to load this slot.' ) );
                        
                        $out .= $dialogBox->render();
                    }
                    else
                    {
                        $slotsCollection = new slotsCollection();
                        $allSlots = $slotsCollection->getAll( $subscription->getId() );
                        //$allSlotsFromUsers = $slotsCollection->getAllFromUser( claro_get_current_user_id() );
                        
                        $out .= SubscriptionsRenderer::editSlot(    $subscription,
                                                                    $slot,
                                                                    $allSlots
                                                                );
                    }
                }
            }
            break;
            
            case 'exSlotAdd' :
            {
                if( ( isset( $_POST['title'] ) && is_array( $_POST['title'] ) && count( $_POST['title'] ) )
                    &&
                    ( isset( $_POST['description'] ) && is_array( $_POST['description'] ) && count( $_POST['description'] ) )
                    &&
                    ( isset( $_POST['places'] ) && is_array( $_POST['places'] ) && count( $_POST['places'] ) )
                    && ( ( count( $_POST['title'] ) == count( $_POST['description'] ) ) == count( $_POST['places'] ) )
                )
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
            break;
            
            case 'rqSlotAdd' :
            {
                if( isset( $_POST['slots'] ) && isset( $_POST['places'] ) )
                {
                    $out .= SubscriptionsRenderer::addSlot( $subscription, $dialogBox, $_POST['slots'], $_POST['places'] );
                }
                else
                {
                    $out .= SubscriptionsRenderer::addSlot( $subscription, $dialogBox );
                }
            }
            break;
            
            case 'export' :
            {
                if( ! ( isset( $type ) && $type == 'csv' ) )
                {
                    claro_die( get_lang( 'Not allowed' ) );
                }
                
                FromKernel::uses( 'csv.class' );
                
                $csv = new csv( ';' );
                
                $slotsCollection = new slotsCollection();
                
                $allSlotsFromUsersToExport = $slotsCollection->getAllFromUsers( $subscription->getId(), $subscription->getContext() );
                
                $export[ 0 ][ 'title' ] = get_lang( 'Title' );
                if( $subscription->getContext() == 'user' )
                {
                    $export[ 0 ][ 'lastname' ] = get_lang( 'Last name' );
                    $export[ 0 ][ 'firstname' ] = get_lang( 'First name' );
                }
                else
                {
                    $export[ 0 ][ 'groupname' ] = get_lang( 'Group name' );
                }
                $i = 1;
                
                foreach( $allSlotsFromUsersToExport as $allSlots )
                {
                    foreach( $allSlots as $slot )
                    {
                        $export[ $i ][ 'title' ] = $slot[ 'title' ];
                        if( $subscription->getContext() == 'user' )
                        {
                           $export[ $i ][ 'lastname' ] = $slot[ 'subscriberData' ][ 'lastname' ];
                           $export[ $i ][ 'firstname' ] = $slot[ 'subscriberData' ][ 'firstname' ];
                        }
                        else
                        {
                            $export[ $i ][ 'groupname' ] = $slot[ 'subscriberData' ][ 'name' ];
                        }
                        $i++;
                    }
                }
                
                $csv->recordList = $export;
                
                $csvContent = $csv->export();
                
                header("Content-type: application/csv");
                header('Content-Disposition: attachment; filename="'. $subscription->getTitle() . '.csv"');
                echo $csvContent;
                exit;
            }
            break;
            
            case 'rqResult' :
            {
                $slotsCollection = new slotsCollection();
                
                $allSlots = $slotsCollection->getAll( $subscription->getId() );
                $allSlotsFromUsers = $slotsCollection->getAllFromUsers( $subscription->getId(), $subscription->getContext() );
                $out .= SubscriptionsRenderer::result( $subscription, $allSlots, $allSlotsFromUsers );
            }
            break;
            
            case 'exLock' :
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
                
                $subscriberId = claro_is_in_a_group() && $subscription->getContext() == 'group' ? $groupId : claro_get_current_user_id();
                $allSlotsFromUsers = $slotsCollection->getAllFromUser( $subscriberId, $subscription->getContext() );
                $out .= SubscriptionsRenderer::listSubscriptions( $subscriptionsCollection, $allSlotsFromUsers, $context );
            }
            break;
            
            case 'exVisible' :
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
                
                $out .= $dialogBox->render();
                
                $subscriptionsCollection = new subscriptionsCollection();
                
                $slotsCollection = new slotsCollection();
                
                $subscriberId = claro_is_in_a_group() && $subscription->getContext() == 'group' ? $groupId : claro_get_current_user_id();
                $allSlotsFromUsers = $slotsCollection->getAllFromUser( $subscriberId, $subscription->getContext() );
                $out .= SubscriptionsRenderer::listSubscriptions( $subscriptionsCollection, $allSlotsFromUsers, $context );
            }
            break;
            
            case 'exInvisible' :
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
                
                $out .= $dialogBox->render();
                
                $subscriptionsCollection = new subscriptionsCollection();
                
                $slotsCollection = new slotsCollection();
                
                $subscriberId = claro_is_in_a_group() && $subscription->getContext() == 'group' ? $groupId : claro_get_current_user_id();
                $allSlotsFromUsers = $slotsCollection->getAllFromUser( $subscriberId, $subscription->getContext() );
                $out .= SubscriptionsRenderer::listSubscriptions( $subscriptionsCollection, $allSlotsFromUsers, $context );
            }
            break;
            
            case 'exDelete' :
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
            break;
            
            case 'rqDelete' :
            {
                $out .= SubscriptionsRenderer::delete( $subscription );
            }
            break;
            
            case 'exEdit' :
            {
                $subscription->setTitle( $userInput->get( 'title' ) );
                $subscription->setDescription( $userInput->get( 'description' ) );
                $subscription->setContext( $userInput->get( 'context' ) );
                $subscription->setType( $userInput->get( 'type' ) );
                $subscription->setModifiable( $userInput->get( 'modifiable' ) );
                $subscription->setVisibility( $userInput->get( 'visibility' ) );
                if( (int) $userInput->get( 'visibilityFrom' ) == 1 )
                {
                    $fromDate = claro_mktime( $userInput->get( 'visibilityFromHour' ),
                                        $userInput->get( 'visibilityFromMinute' ),
                                        0,
                                        $userInput->get( 'visibilityFromMonth' ),
                                        $userInput->get( 'visibilityFromDay' ),
                                        $userInput->get( 'visibilityFromYear')
                                      );
                    $subscription->setVisibilityFrom( $fromDate );
                }
                else
                {
                    $subscription->setVisibilityFrom( 0 );
                }
                
                if( (int) $userInput->get( 'visibilityTo' ) == 1 )
                {
                    $toDate = claro_mktime( $userInput->get( 'visibilityToHour' ),
                                        $userInput->get( 'visibilityToMinute' ),
                                        0,
                                        $userInput->get( 'visibilityToMonth' ),
                                        $userInput->get( 'visibilityToDay' ),
                                        $userInput->get( 'visibilityToYear')
                                      );
                    $subscription->setVisibilityTo( $toDate );
                }
                else
                {
                    $subscription->setVisibilityTo( 0 );
                }
                
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
            break;
            
            case 'rqEdit' :
            {
                $out .= SubscriptionsRenderer::edit( $subscription );
            }
            break;
            
            case 'exAdd' :
            {
                $subscription = new subscription();
                $subscription->setTitle( $userInput->get( 'title' ) );
                $subscription->setDescription( $userInput->get( 'description' ) );
                $subscription->setContext( $userInput->get( 'context' ) );
                $subscription->setType( $userInput->get( 'type' ) );
                $subscription->setModifiable( $userInput->get( 'modifiable' ) );
                $subscription->setVisibility( $userInput->get( 'visibility' ) );
                if( (int) $userInput->get( 'visibilityFrom' ) == 1 )
                {
                    $fromDate = mktime( $userInput->get( 'visibilityFromHour' ),
                                        $userInput->get( 'visibilityFromMinute' ),
                                        0,
                                        $userInput->get( 'visibilityFromMonth' ),
                                        $userInput->get( 'visibilityFromDay' ),
                                        $userInput->get( 'visibilityFromYear')
                                      );
                    $subscription->setVisibilityFrom( $fromDate );
                }
                
                if( (int) $userInput->get( 'visibilityTo' ) == 1 )
                {
                    $toDate = mktime( $userInput->get( 'visibilityToHour' ),
                                        $userInput->get( 'visibilityToMinute' ),
                                        0,
                                        $userInput->get( 'visibilityToMonth' ),
                                        $userInput->get( 'visibilityToDay' ),
                                        $userInput->get( 'visibilityToYear')
                                      );
                    $subscription->setVisibilityTo( $toDate );
                }
                
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
                $out .= SubscriptionsRenderer::add();
            }
            break;
            
            case 'list' :
            {
               $subscriptionsCollection = new subscriptionsCollection();
               
               $slotsCollection = new slotsCollection();
               
               $subscriberId = claro_is_in_a_group() ? $groupId : claro_get_current_user_id();
               $allSlotsFromUsers = $slotsCollection->getAllFromUser( $subscriberId, (claro_is_in_a_group() ? 'group' : 'user' ) );
               $out .= SubscriptionsRenderer::listSubscriptions( $subscriptionsCollection, $allSlotsFromUsers, $context ); 
            }
            break;
        }
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
