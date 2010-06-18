<div class="claroDialogBox">
    <div class="claroDialogMsg msgForm">        
        <fieldset style="border: none; margin: 0; padding: 0;">
            <dl>
                <dt><label for="title"><?php echo get_lang( 'Title' ); ?> :</label></dt>
                <dd>
                    <input type="text" name="title[]" value="" />
                    <div style="float: right;">
                        <input type="text" name="places[]" value="<?php echo $this->places; ?>" style="width: 20px; text-align: right;" /> <?php echo get_lang( 'places' ); ?>
                    </div>
                </dd>
                <dt><label for="description"><?php echo get_lang( 'Description' ); ?> :</label></dt>
                <dd>
                    <?php echo claro_html_textarea_editor( 'description[]', '', 8, 15, '', 'simple'); ?>                    
                </dd>
            </dl>
        </fieldset>
    </div>
</div>