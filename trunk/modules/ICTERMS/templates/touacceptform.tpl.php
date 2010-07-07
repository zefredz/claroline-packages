<div id="icterms_termsContents">
    <?php echo claro_parse_user_text( $this->termsContents ); ?>
</div>

<form action="<?php echo php_self(); ?>?cmd=exAcceptTerms" method="post">
    <input id="acceptTerms" type="checkbox" name="acceptTerms" value="true" />
    <label for="acceptTerms">
        <?php
            echo get_lang('I have read and understood the terms of use of this web site and I accept them.');
        ?>
    </label>
    <br />
    <input type="submit" name="submit" value="<?php echo get_lang('Submit'); ?>" />
</form>