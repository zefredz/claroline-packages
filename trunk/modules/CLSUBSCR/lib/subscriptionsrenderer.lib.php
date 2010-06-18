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
    public static function displaySubscription( & $subscription, & $slots = null, & $userChoices = null )
    {
        $out = '';
        
        $tpl = new ModuleTemplate( 'CLSUBSCR', 'subscription.tpl.php' );
        
        $tpl->assign( 'subscription', $subscription->flat() );
        
        $out .= $tpl->render();
        
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
    
    public static function listSubscriptions( & $subscriptionsCollection, & $userChoices = null )
    {
        $tpl = new ModuleTemplate( 'CLSUBSCR', 'listAll.tpl.php' );
        
        $tpl->assign( 'userChoices', $userChoices );
        $tpl->assign( 'subscriptions', $subscriptionsCollection->getAll() );
        $tpl->assign( 'displayChoice', true);
        
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