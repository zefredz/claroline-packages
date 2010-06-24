<?php // $Id$

/**
 * Subscription
 *
 * @version     CLSUBSCR 1.0-alpha $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLSUBSCR
 * @author      Dimitri Rambout <dim@claroline.net>
 */

class SubscriptionsRenderer {
    
    public static function delete( & $subscription )
    {
        $tpl = new ModuleTemplate( 'CLSUBSCR', 'delete.tpl.php');
        
        $tpl->assign('id', $subscription->getId() );
        $tpl->assign('title', $subscription->getTitle() );
        
        return $tpl->render();
    }
    
    public static function result( & $subscription, & $slots, & $usersChoices )
    {
        $out = '';
        
        $tpl = new ModuleTemplate( 'CLSUBSCR', 'subscription.tpl.php' );
        
        $tpl->assign( 'subscription', $subscription->flat() );
        
        $out .= $tpl->render();
        
        
        $tpl = new ModuleTemplate( 'CLSUBSCR', 'result.tpl.php' );
        
        $tpl->assign( 'subscription', $subscription );
        $tpl->assign( 'slots', $slots );
        $tpl->assign( 'usersChoices', $usersChoices );
        
        $cmdMenu[] = claro_html_cmd_link( 'index.php?cmd=export&amp;type=csv&amp;subscrId=' . $subscription->getId() . claro_url_relay_context( '&' ), get_lang( 'Export in CSV' ) );
        //$cmdMenu[] = claro_html_cmd_link( 'index.php?cmd=export&amp;type=xls&amp;subscrId=' . $subscription->getId() . claro_url_relay_context( '&' ), get_lang( 'Export in XLS' ) );
        //$cmdMenu[] = claro_html_cmd_link( 'index.php?cmd=export&amp;type=ods&amp;subscrId=' . $subscription->getId() . claro_url_relay_context( '&' ), get_lang( 'Export in ODS' ) );
        $menu = claro_html_menu_horizontal( $cmdMenu );
        
        $tpl->assign( 'menu', $menu );
        
        $out .= $tpl->render();
        
        return $out;
    }
    
    public static function displaySubscription( & $subscription, & $slots = null, & $userChoices = null )
    {
        $out = '';
        
        $tpl = new ModuleTemplate( 'CLSUBSCR', 'subscription.tpl.php' );
        
        $tpl->assign( 'subscription', $subscription->flat() );
        
        $out .= $tpl->render();
        
        //use a connector
        switch( $subscription->getType() )
        {
            case 'multiple' :
            {
                
            }
            break;
            case 'preference' :
            {
                
            }
            break;
            default :
            {
                $tpl = new ModuleTemplate( 'CLSUBSCR', 'chooseSlotUnique.tpl.php');
            }
        }
        
        $tpl->assign( 'subscriptionId', $subscription->getId() );
        $tpl->assign( 'slots', $slots );
        $tpl->assign( 'userChoices', $userChoices );
        $out .= $tpl->render();
        
        return $out;
    }
    
    public static function listSubscriptions( & $subscriptionsCollection, & $userChoices = null, $context = null )
    {
        $tpl = new ModuleTemplate( 'CLSUBSCR', 'listAll.tpl.php' );
        
        $tpl->assign( 'userChoices', $userChoices );
        $tpl->assign( 'subscriptions', $subscriptionsCollection->getAll( $context ) );
        $tpl->assign( 'displayChoice', true);
        
        return $tpl->render();
    }
    
    public static function edit( & $subscription )
    {
        $tpl = new ModuleTemplate( 'CLSUBSCR', 'add.tpl.php' );
        
        $tpl->assign( 'id', $subscription->getId() );
        $tpl->assign( 'title', $subscription->getTitle() );
        $tpl->assign( 'description', $subscription->getDescription() );
        $tpl->assign( 'context', $subscription->getContext() );
        $tpl->assign( 'type', $subscription->getType() );
        $tpl->assign( 'visibility', $subscription->getVisibility() );
        
        return $tpl->render();
    }
    public static function add( & $subscription = null )
    {
        $tpl = new ModuleTemplate( 'CLSUBSCR', 'add.tpl.php' );
        
        if( ! is_null( $subscription ) )
        {
            $tpl->assign( 'title', $subscription->getTitle() );
            $tpl->assign( 'description', $subscription->getDescription() );
            $tpl->assign( 'context', $subscription->getContext() );
            $tpl->assign( 'type', $subscription->getType() );
            $tpl->assign( 'visibility', $subscription->getVisibility() );
        }
        
        return $tpl->render();
    }
    
    public function deleteSlot( & $subscription, & $slot )
    {
        $out = '';
        
        $tpl =  new ModuleTemplate( 'CLSUBSCR', 'deleteSlot.tpl.php' );
        
        $tpl->assign( 'slot', $slot );
        $tpl->assign( 'subscription', $subscription );
        
        return $tpl->render();        
    }
    
    public static function editSlot( & $subscription, & $slot, & $slots, $error = false )
    {
        $out = '';
        
        $tpl = new ModuleTemplate( 'CLSUBSCR', 'subscription.tpl.php' );
        
        $tpl->assign( 'subscription', $subscription->flat() );
        
        $out .= $tpl->render();
        
        $template = 'editSlot' . ucfirst( $subscription->getType() ) . '.tpl.php';
        
        $tpl = new ModuleTemplate( 'CLSUBSCR', $template );
        $tpl->assign( 'thisSlot', $slot );
        $tpl->assign( 'slots', $slots );
        $tpl->assign( 'subscriptionId', $subscription->getId() );
        $tpl->assign( 'error', $error );
        
        $out .= $tpl->render();
        
        return $out;
    }
    
    public static function addSlot( & $subscription, & $dialogBox, $slots = null, $places = null, $slotsContent = null )
    {
        if( ! $subscription->validate() )
        {
            $dialogBox->error( get_lang( 'Unable to load this subscription.' ) );
            
            return $dialogBox->render();
        }
        else
        {
            $tpl = new ModuleTemplate( 'CLSUBSCR', 'addSlot.tpl.php' );
            
            $tpl->assign( 'subscription', $subscription );
            
            if( ! is_null( $slots ) )
            {
                $tpl->assign( 'slots', (int) $slots );
                $tpl->assign( 'places', ( ! is_null( $places ) ? (int) $places : 0 ) );
                if( ! is_null( $slotsContent ) && is_array( $slotsContent ))
                {
                    $tpl->assign( 'slotsContent', $slotsContent );
                }
                
            }
            
            return $tpl->render();
        }
    }
    
    public static function addSlotPlaces( $places )
    {
        $places = (int) $places;
        
        if( $places > 0 )
        {
            $tpl = new ModuleTemplate( 'CLSUBSCR', 'addSlotPlaces.tpl.php' );
            
            $tpl->assign( 'places', $places );
            
            return $tpl->render();
        }
    }
}
 
?>