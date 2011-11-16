<p><?php echo get_lang( '_intro_text' ); ?></p>
<a href="<?php echo  htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=accept' ) );?>">
    <input type="button" name="accept" value="<?php echo get_lang( 'Now' );?>" />
</a>
<?php if( get_conf( 'ICSURVEW_postpone_allowed' ) ) : ?>
<a href="<?php echo  htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=later' ) );?>">
    <input type="button" name="later" value="<?php echo get_lang( 'Later' );?>" />
</a>
<?php endif; ?>