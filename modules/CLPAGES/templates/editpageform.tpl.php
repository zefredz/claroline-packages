<!-- $Id$ -->

<?php if ( $this->pageId ): ?>
<form 
    action="<?php echo htmlspecialchars( Url::contextualize( 
        $_SERVER['PHP_SELF'] . '?pageId=' . $this->pageId ) ); ?>" 
    method="post">
<?php else: ?>
<form 
    action="<?php echo htmlspecialchars( Url::contextualize( 
        $_SERVER['PHP_SELF'] ) ); ?>" 
    method="post">
<?php endif; ?>
    
    <?php echo claro_form_relay_context(); ?>
    <input type="hidden" name="claroFormId" value="<?php echo uniqid(''); ?>" />
    <input type="hidden" name="cmd" value="exEdit" />
    
    
    <fieldset>
    
    <?php if( $this->pageId ): ?>

        <legend><?php echo get_lang('Edit page settings'); ?></legend>

    <?php else: ?>

        <legend><?php echo get_lang('Create a new page'); ?></legend>

    <?php endif; ?>
    
    
    <dl>
        <dt>
            <label for="title"><?php echo get_lang('Title'); ?></label>&nbsp;<span class="required">*</span>
        </dt>
        <dd>
            <input type="text" name="title" id="title" maxlength="255" 
                value="<?php echo htmlspecialchars($this->page->getTitle()); ?>" />
        </dd>
        <dt>
            <label for="description"><?php echo get_lang('Description'); ?></label>
        </dt>
        <dd>
            <textarea name="description" id="description" cols="50" rows="5"><?php echo htmlspecialchars($this->page->getDescription()); ?></textarea>
        </dd>
        <dt>
            <?php echo get_lang('Display'); ?>&nbsp;<span class="required">*</span>
        </dt>
        <dd>
            <input type="radio" name="displayMode" id="displayModePage" value="PAGE"<?php echo ($this->page->getDisplayMode() == 'PAGE' ?' checked="checked"':''); ?> />
            <label for="displayModePage"><?php echo get_lang('One page'); ?></label><br />
            <input type="radio" name="displayMode" id="displayModeSlide" value="SLIDE"<?php echo ($this->page->getDisplayMode() == 'SLIDE' ?' checked="checked"':''); ?> />
            <label for="displayModeSlide"><?php echo get_lang('Slideshow'); ?></label>
        </dd>
        <dt>
            &nbsp;
        </dt>
        <dd>
            <span class="required">*</span>&nbsp;<?php echo get_lang('Denotes required fields')?>
        </dd>
        <dt>
            &nbsp;
        </dt>
        <dd>
            <input type="submit" value="<?php echo get_lang('Ok'); ?>" />&nbsp;
            <?php echo claro_html_button($_SERVER['PHP_SELF'], get_lang('Cancel')); ?>
        </dd>
    </dl>
    
    </fieldset>
    
</form>
