<form name="chooseSlot" action="<?php echo $_SERVER['PHP_SELF'] . claro_url_relay_context( '?'); ?>" method="post" >
    <input type="hidden" name="subscrId" value="<?php echo (int) $this->subscriptionId; ?>" />
    <input type="hidden" name="cmd" value="exSlotChoice" />
    
    <table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">
        <thead>
            <tr class="headerX" align="center" valign="top">
                <th><?php echo get_lang( 'Slot' ); ?></th>
                <th><?php echo get_lang( 'Space (available/total)' ); ?></th>
                <th><?php echo get_lang( 'Choice' ); ?></th>
            </tr>
        </thead>
        <tbody>        
    <?php
    foreach( $this->slots as $slot ) :
    ?>
        <tr>
            <td><?php echo $slot['title']; ?></td>
            <td>0 / <?php echo $slot['availableSpace']; ?>
            <td><input type="radio" name="choice" value="<?php echo (int) $slot['id']; ?>" <?php echo isset( $this->userChoices[ $this->subscriptionId ][ $slot['id'] ] ) ? 'checked="checked"' : ''; ?> /></td>
        </tr>
    <?php
    endforeach;
    ?>
        </tbody>
    </table>
    <input type="submit" name="saveButton" value="<?php echo get_lang( 'Save' ); ?>" />
    <input type="button" name="cancelButton" value="<?php echo get_lang( 'Cancel' ); ?>" />
</form>