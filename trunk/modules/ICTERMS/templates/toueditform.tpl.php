<form action="<?php echo php_self(); ?>?cmd=exEditTermsOfUse" method="post">
    <label for="touContents"><?php echo get_lang('Terms of use'); ?></label>:
    <br />
    <?php echo claro_html_simple_textarea( 'touContents', $this->touContents ); ?>
    <br />
    <!-- should never be checked by default -->
    <input type="checkbox" name="confirmEmpty" id="confirmEmpty" value="true" />
    <label for="confirmEmpty"><?php echo get_lang('Reset terms of use to defaults'); ?></label>:
    <br />
    <br />
    <input type="submit" name="submitTou" id="submitTou" value="<?php echo get_lang('Save'); ?>" />
</form>