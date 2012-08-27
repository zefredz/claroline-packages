<p><?php echo get_lang( '_intro_text' ); ?></p>
<?php if( $this->answer->getAnswerNb() ) : ?>
<p style="font-weight: bold; color: red;"><?php echo get_lang( '_pending_answer : %pendingNb' , array( '%pendingNb' => $this->answer->pending() ) ); ?></p>
<?php endif; ?>
<a href="<?php echo  htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=accept' ) );?>">
    <input type="button" name="accept" value="<?php echo get_lang( '_now' );?>" />
</a>
<?php if( get_conf( 'ICSURVEW_postpone_allowed' ) ) : ?>
<a href="<?php echo  htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=later' ) );?>">
    <input type="button" name="later" value="<?php echo get_lang( '_later' );?>" />
</a>
<?php endif; ?>