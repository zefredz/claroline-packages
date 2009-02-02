<form method="post" action="<?php echo php_self() . '?cmd=compose'; ?>">
<input type="hidden" name="subject" value="<?php echo htmlspecialchars($this->subject); ?>" />
<input type="hidden" name="message" value="<?php echo htmlspecialchars($this->message); ?>" />
<input type="hidden" name="addressee" value="<?php echo htmlspecialchars($this->addressee); ?>" />
<input type="submit" value="<?php echo get_lang('Edit current'); ?>" name="compose" />&nbsp;
<a href="<?php echo php_self(); ?>">
<input type="button" value="<?php echo get_lang('Compose new'); ?>" name="cancel" />
</a>
</form>