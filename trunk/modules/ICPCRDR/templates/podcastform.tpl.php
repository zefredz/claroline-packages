<form name="editPodcast" action="<?php echo $this->actionUrl; ?>" method="post">
<?php if(!is_null($this->id)) : ?>
    <input type="hidden" name="podcastId" value="<?php echo $this->id; ?>" />
<?php endif; ?>
    <fieldset>
        <legend><?php echo get_lang('Edit information for the curent link'); ?></legend>
        <dl>
            <dt><label for="title"><?php echo get_lang('Title'); ?>&nbsp;<span class="required">*</span>&nbsp;:</label></dt>
            <dd><input type="text" name="title" id="title" size="60" maxlength="200" value="<?php echo $this->title; ?>" /></dd>
            <dt><label for="url"><?php echo get_lang('Url'); ?>&nbsp;<span class="required">*</span>&nbsp;:</label></dt>
            <dd><input type="text" name="url" id="url" size="60" maxlength="200" value="<?php echo $this->url; ?>" /></dd>
            <dt><label for="visibility"><?php echo get_lang('Visibility'); ?>&nbsp;:</label></dt>
            <dd>
                <select name="visibility">                    
                    <option value="visible" <?php if( $this->visibility == 'visible' ) : echo 'selected="selected"'; endif; ?>><?php echo get_lang('Visible'); ?></option>
                    <option value="invisible" <?php if( $this->visibility == 'invisible' ) : echo 'selected="selected"'; endif; ?>><?php echo get_lang('Invisible'); ?></option>
                </select>
            </dd>
        </dl>
    </fieldset>
    <div style="text-align: center;">
        <input type="submit" name="" id="" value="<?php echo get_lang('Ok'); ?>" />&nbsp;&nbsp;
        <?php echo claro_html_button('./index.php', get_lang("Cancel") ); ?>
    </div>
</form>