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

?>
<form name="chooseSlot" action="<?php echo $_SERVER['PHP_SELF'] . claro_url_relay_context( '?'); ?>" method="post" >
    <input type="hidden" name="subscrId" value="<?php echo (int) $this->subscriptionId; ?>" />
    <input type="hidden" name="cmd" value="exSlotChoice" />
    
    <table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">
        <thead>
            <tr class="headerX" align="center" valign="top">
                <th><?php echo get_lang( 'Slot' ); ?></th>
                <th><?php echo get_lang( 'Description' ); ?></th>
                <th><?php echo get_lang( 'Space (available/total)' ); ?></th>
                <th><?php echo get_lang( 'Choice' ); ?></th>
                <?php
                if( claro_is_allowed_to_edit() ) :                
                ?>
                <th><?php echo get_lang( 'Edit' ); ?></th>
                <?php
                endif;
                ?>
            </tr>
        </thead>
        <tbody>        
    <?php
    foreach( $this->slots as $slot ) :
        if( $slot['visibility'] == 'visible' || claro_is_allowed_to_edit() ) :
    ?>
        <tr>
            <td><?php echo $slot['title']; ?></td>
            <td><?php echo $slot['description']; ?></td>
            <td style="text-align: center;"><?php echo $slot['subscribersCount']; ?> / <?php echo $slot['availableSpace']; ?>
            <td style="text-align: center;">
                <?php
                if( isset( $this->userChoices[ $this->subscriptionId ][ $slot['id'] ] ) || $slot['subscribersCount'] != $slot['availableSpace'] ) :                
                ?>
                <input type="radio" name="choice" value="<?php echo (int) $slot['id']; ?>" <?php echo isset( $this->userChoices[ $this->subscriptionId ][ $slot['id'] ] ) ? 'checked="checked"' : ''; ?> />
                <?php
                endif;
                ?>
            </td>
            <?php
            if( claro_is_allowed_to_edit() ) :                
            ?>
            <td style="text-align: right;">
                <a href="<?php echo $_SERVER['PHP_SELF'] . '?cmd=rqSlotEdit&slotId=' . $slot['id'] . '&subscrId=' . $this->subscriptionId . claro_url_relay_context( '&' ); ?>"><img src="<?php echo get_icon_url( 'edit' ); ?>" alt="<?php echo get_lang( 'Edit' ); ?>" /></a>
                <a href="<?php echo $_SERVER['PHP_SELF'] . '?cmd=exSlotVisible&slotId=' . $slot['id'] . '&subscrId=' . $this->subscriptionId . claro_url_relay_context( '&' ); ?>"><img src="<?php echo $slot['visibility'] == 'visible'? get_icon_url( 'visible' ) : get_icon_url( 'invisible'); ?>" alt="<?php echo $slot['visibility'] == 'visible' ? get_lang( 'Visible' ) : get_lang('Invisible'); ?>" /></a>
                <a href="<?php echo $_SERVER['PHP_SELF'] . '?cmd=rqSlotDelete&slotId=' . $slot['id'] . '&subscrId=' . $this->subscriptionId . claro_url_relay_context( '&' ); ?>"><img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Delete' ); ?>" /></a>        
            </td>
            <?php
            endif;
            ?>
        </tr>
    <?php
        endif;
    endforeach;
    ?>
        </tbody>
    </table>
    <input type="submit" name="saveButton" value="<?php echo get_lang( 'Save' ); ?>" />
    <input type="button" name="cancelButton" value="<?php echo get_lang( 'Cancel' ); ?>" />
</form>