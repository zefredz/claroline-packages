<form name="editLink" action="<?php echo $this->formUrl; ?>" method="post">
<?php if(!is_null($this->id)) : ?><input type="hidden" name="linkId" value="<?php echo $this->id; ?>" /><?php endif; ?>
    <fieldset>
        <legend><?php echo get_lang('Edit information for the curent link'); ?></legend>
        <dl>
            <dt><label for="title"><?php echo get_lang('Title'); ?>&nbsp;<span class="required">*</span>&nbsp;:</label></dt>
            <dd><input type="text" name="title" id="title" size="60" maxlength="200" value="<?php echo $this->title; ?>" /></dd>
            <dt><label for="url"><?php echo get_lang('Url'); ?>&nbsp;<span class="required">*</span>&nbsp;:</label></dt>
            <dd><input type="text" name="url" id="url" size="60" maxlength="200" value="<?php echo $this->url; ?>" /></dd>
            <dt><label for="type"><?php echo get_lang('Type'); ?>&nbsp;:</label></dt>
            <dd>
                <select name="type" id="type">
                    <?php foreach ( $this->typeList as $type ) : ?>
                    <option value="<?php echo $type; ?>" <?php if($type == $this->type) : ?> selected="selected" <?php endif; ?>><?php echo $type; ?></option>
                    <?php endforeach; ?>
                </select>
            </dd>
            <dt><label for="width"><?php echo get_lang('Width'); ?>&nbsp;:</label></dt>
            <dd><input type="text" name="options[width]" id="width" value="<?php echo $this->width; ?>" style="width: 50px;" /></dd>
            <dt><label for="height"><?php echo get_lang('Height'); ?>&nbsp;:</label></dt>
            <dd><input type="text" name="options[height]" id="height" value="<?php echo $this->height; ?>" style="width: 50px;" /></dd>
            <dt><label><?php echo get_lang('Options'); ?>&nbsp;:</label></dt>
            <dd>
                <div id="options">
                    <?php
                    $i = 0;
                    foreach( $this->options as $option) :                    
                    ?>
                    <div style="padding: 2px;" id="option_<?php echo $i; ?>" >
                        <?php echo get_lang('Name'); ?> :
                        <input type="text" name="options[params][<?php echo $i; ?>][name]" value="<?php echo $option['name']; ?>" id="name_<?php echo $i; ?>" />
                        <?php echo get_lang('Value'); ?> :
                        <select name="options[params][<?php echo $i; ?>][var]" id="options_<?php echo $i; ?>" onchange="linkLoadOptionValue(this, <?php echo $i; ?>)" ><?php echo $this->optionsList; ?></select>
                        <?php
                        if( $option['var'] == 'freeValue' ) :
                        ?><input type="text" name="options[params][<?php echo $i; ?>][value]" value="<?php echo $option['value']; ?>" id="value_<?php echo $i; ?>" style="width: 100px;" />
                        <?php
                        else :
                        ?>
                        <input type="hidden" name="options[params][<?php echo $i; ?>][value]" value="" id="value_<?php echo $i; ?>" />
                        <?php
                        endif;
                        ?>
                        <a href="#" onclick="linkDelOption('option_<?php echo $i; ?>');"><img src="./img/brick_delete.png" alt="<?php echo get_lang( 'Remove option' ); ?>" /></a>
                        <script type="text/javascript" >
                        $(document).ready(function(){                            
                            var opt = $("#option_<?php echo $i; ?>").children().find("._<?php echo $option['var']; ?>");                            
                            opt.attr('selected','selected');
                        })
                        </script>
                    </div>
                    <?php
                    $i++;
                    endforeach;
                    ?>
                </div>
                <a href="#" onclick="linkAddOption('options','<?php echo get_lang('Name'); ?>','<?php echo get_lang('Value'); ?>', '<?php echo get_lang( 'Remove option' ); ?>')" ><img src="./img/brick_add.png" alt="<?php echo get_lang( 'Add an option' ); ?>" /><?php echo get_lang( 'Add an option'); ?></a>
            </dd>
        </dl>        
    </fieldset>
    <div style="text-align: center;">
        <input type="submit" name="submitButton" value="<?php echo get_lang('Ok'); ?>" />&nbsp;&nbsp;
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>"><input type="button" name="cancelButton" value="<?php echo get_lang("Cancel"); ?>" /></a>
    </div>
</form>