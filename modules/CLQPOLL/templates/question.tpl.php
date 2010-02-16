<p><?php echo $this->msg ?></p>
<form method="post" action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=' . $this->urlAction ) ); ?>" >
    <input type="hidden" name="pollId" value="<?php echo $this->pollId; ?>" />
    <input type="hidden" name="choiceId" value="<?php echo $this->choiceId; ?>" />
    <input type="submit" name="" value="<?php echo get_lang( 'Yes' ); ?>" />
    <?php echo claro_html_button( htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=' . $this->urlCancel . '&pollId=' . $this->pollId ) ) , get_lang("Cancel") ); ?>
</form>