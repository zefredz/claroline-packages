<h2><?php echo get_lang( 'Skype status notifier' ); ?> </h2>

<form action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER[ 'PHP_SELF' ] ) ); ?>" method="post">
    <input type="hidden" name="cmd" value="exUpdate" />
    <label for="skypeName"><?php echo get_lang('Skype name') .' : ' ?></label>
    <input type="text" name="skypeName" id="skypeName" value="<?php echo $this->skypeName; ?>" />
    <p>
        <small><?php echo get_lang( 'Leave empty to deactivate status notifier.' ); ?></small>
    </p>
    <p>
        <small><?php echo get_lang( 'Do not forget to allow your status to be shown from your Skype client.' ); ?></small><br />
        <img src="<?php echo get_module_url( 'UCONLINE' ) . '/img/privacy_shot.jpg'; ?>" alt="<?php echo get_lang( 'Skype options, Windows.' ); ?>" />
        <img src="<?php echo get_module_url( 'UCONLINE' ) . '/img/skype_mac.png'; ?>" alt="<?php echo get_lang( 'Skype options, MacOSX.' ); ?>" />
    </p>
    <input type="submit" value="<?php echo get_lang( 'OK' ); ?>" />
    <?php echo claro_html_button( htmlspecialchars( Url::Contextualize( 'user_connected.php' ) ) , get_lang("Cancel") ); ?>
</form>