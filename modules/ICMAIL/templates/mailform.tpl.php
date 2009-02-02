<form method="post" action="<?php echo php_self() . '?cmd=send'; ?>">
<input type="hidden" name="claroFormId" value="<?php echo uniqid(''); ?>" />
<?php echo claro_form_relay_context(); ?>
<label for="addressee_type"><?php echo get_lang("Addressee"); ?> : </label><br />
<select id="addressee_type" name="addressee">
<?php foreach ( $this->addresseeTypeList as $label => $text ) : ?>
<?php if ( $label == $this->selectedAddressee ) : ?>
<option value="<?php echo htmlspecialchars($label);?>" selected="selected"><?php echo htmlspecialchars($text);?></option>
<?php else: ?>
<option value="<?php echo htmlspecialchars($label);?>"><?php echo htmlspecialchars($text);?></option>
<?php endif; ?>
<?php endforeach; ?>
</select><br />
<label for="message_subject"><?php echo get_lang('Subject');?> : </label><br/>
<input type="text" id="message_subject" name="subject" value="<?php echo htmlspecialchars($this->subject); ?>" maxlength="255" size="40" /><br/>
<label for="message"><?php echo get_lang('Message'); ?> : </label><br/>
<?php echo claro_html_textarea_editor('message', $this->message); ?><br/>
<input type="checkbox" id="copyToAdmin" name="copyToAdmin" /><label for="copyToAdmin"><?php echo get_lang('Send copy to admin');?></label><br />
<input type="submit" value="<?php echo get_lang('Send'); ?>" name="send" />
</form>