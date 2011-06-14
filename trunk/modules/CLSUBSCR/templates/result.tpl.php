<!-- // $Id$

/**
 * Subscription
 *
 * @version     CLSUBSCR 0.2 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLSUBSCR
 * @author      Dimitri Rambout <dim@claroline.net>
 */
-->

<?php echo $this->menu; ?>

<table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">
   <thead>
       <tr class="headerX" align="center" valign="top">
           <th><?php echo get_lang( 'Slot' ); ?></th>
           <th><?php echo get_lang( 'Description' ); ?></th>
           <th><?php echo get_lang( 'Space (available/total)' ); ?></th>           
       </tr>
   </thead>
   <tbody>
    
      <?php foreach( $this->slots as $slot ): ?>
        <tr>
            <td style="font-weight: bold;">
                <?php echo $slot['title']; ?>
            </td>
            <td style="font-weight: bold;">
                <?php echo $slot['description']; ?>
            </td>
            <td style="font-weight: bold; text-align: center;">
                <?php echo $slot['subscribersCount']; ?> / <?php echo $slot['availableSpace']; ?>
            </td>
        </tr>
      
         <?php if( isset( $this->usersChoices[ $slot['id'] ] ) ) : ?>
      
            <?php foreach( $this->usersChoices[ $slot['id'] ] as $userSlot ) : ?>
      
        <tr>
             <td colspan="2">
                <?php
                    pushClaroMessage(var_export($userSlot,true),'debug');
                    switch( $userSlot['type'] ) :
                        case 'group' :
                            echo $userSlot['subscriberData']['name'];
                            break;
                        case 'user' :
                            echo $userSlot['subscriberData']['lastname'] . ' ' . $userSlot['subscriberData']['firstname'] . ( $userSlot['subscriberData']['email'] ? ' ('. $userSlot['subscriberData']['email'] .')' : '' ) . ' ';
                            break;
                    endswitch;
                ?>
             </td>
             <td style="text-align: center;">
                 <!-- a href="<?php echo htmlspecialchars( 
                     Url::Contextualize($_SERVER['PHP_SELF'] 
                         . '?cmd=rqEditChoice&slotId=' . $slot['id']
                         . '&subscriberId=' . $userSlot['subscriberId']
                         . '&subscriptionId=' . $userSlot['subscriptionId'] ) ); ?>">
                     <img src="<?php echo get_icon_url( 'edit' ); ?>" alt="<?php echo get_lang( 'Edit' ); ?>" />
                 </a -->
                 <a href="<?php echo htmlspecialchars( 
                     Url::Contextualize($_SERVER['PHP_SELF'] 
                         . '?cmd=rqRemoveChoice&slotId=' . $slot['id']
                         . '&subscriberId=' . $userSlot['subscriberId']
                         . '&subscriptionId=' . $userSlot['subscriptionId'] ) ); ?>">
                     <img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Delete' ); ?>" />
                 </a>
             </td>
        </tr>
    
            <?php endforeach; ?>
    
        <?php else: ?>
      
        <tr>
            <td colspan="3">
                <em><?php echo get_lang( 'No subscriber in this slot.' ); ?></em>
            </td>
        </tr>
        
        <?php endif; ?>
        
<?php endforeach; ?>
        
   </tbody>
</table>

