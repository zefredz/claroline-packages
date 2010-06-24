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

echo $this->menu;
?>
<table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">
   <thead>
       <tr class="headerX" align="center" valign="top">
           <th><?php echo get_lang( 'Slot' ); ?></th>
           <th><?php echo get_lang( 'Description' ); ?></th>
           <th><?php echo get_lang( 'Space (available/total)' ); ?></th>           
       </tr>
   </thead>
   <tbody>
<?php
foreach( $this->slots as $slot ):
?>
      <tr>
         <td style="font-weight: bold;"><?php echo $slot['title']; ?></td>
         <td style="font-weight: bold;"><?php echo $slot['description']; ?></td>
         <td style="font-weight: bold; text-align: center;"><?php echo $slot['subscribersCount']; ?> / <?php echo $slot['availableSpace']; ?>
      </tr>
      <tr>
         <td colspan="3">
         <?php
         if( isset( $this->usersChoices[ $slot['id'] ] ) ) :
            foreach( $this->usersChoices[ $slot['id'] ] as $userSlot ) :
            ?>
            <div>
            <?php
               switch( $userSlot['type'] ) :
                  case 'group' :
                     
                     break;
                  case 'user' :
                     echo $userSlot['subscriberData']['lastname'] . ' ' . $userSlot['subscriberData']['firstname'] . ( $userSlot['subscriberData']['email'] ? ' ('. $userSlot['subscriberData']['email'] .')' : '' ) . ' ';
                     break;
               endswitch;
            ?>
            </div>
            <?php
            endforeach;
         else:
         ?>
         <em><?php echo get_lang( 'No subscriber in this slot.' ); ?></em>
         <?php
         endif;
         ?>
         </td>
      </tr>
<?php
endforeach;
?>
   </tbody>
</table>
<?php
echo $this->menu;
?>
