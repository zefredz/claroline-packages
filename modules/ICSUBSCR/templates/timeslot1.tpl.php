<form method="post"
      action="<?php echo htmlspecialchars( Url::Contextualize( get_module_url( 'ICSUBSCR' ) .'/timeslot.php?step=2&sessionId=' . $this->sessionId ) ); ?>">
    <ul>
    <?php foreach( $this->data['dateToAdd'] as $index => $date ) : ?>
        <li>
            <?php echo $date; ?>
            <input name="data[<?php echo $date; ?>][0]" value="" />
            <input name="data[<?php echo $date; ?>][1]" value="" />
            <input name="data[<?php echo $date; ?>][2]" value="" />
        </li>
    <?php endforeach; ?>
    </ul>
    <input type="checkbox" value="data[slice]" /><?php echo( get_lang('slice the ranges above in slot of ') ); ?><input type="text" name="data[slotTime]" /><?php echo( 'minutes' ); ?><br />
    <input type="checkbox" value="data[limit]" /><?php echo( get_lang('limit the number of available places to ') ); ?><input type="text" name="data[availableSpace]" /><br />
    <input type="submit"
           value="<?php echo get_lang( 'Next step' ); ?>" />
</form>