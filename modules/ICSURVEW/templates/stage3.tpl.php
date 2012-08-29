<p><?php echo get_lang( '_exit_message' ); ?></p>
<?php if( ! $this->hasAnswered() ) : ?>
<p style="font-weight: bold; color: red;"><?php echo get_lang( '_pending_answer : %pendingNb' , array( '%pendingNb' => $this->answer->pending() ) ); ?></p>
<?php endif; ?>
<a href="<?php echo  get_path( 'rootWeb' );?>">
    <input type="button" value="<?php echo get_lang( 'OK' );?>" />
</a>