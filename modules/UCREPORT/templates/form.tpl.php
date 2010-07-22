<strong><?php echo get_lang( 'Please, give a title to your new report...' ); ?></strong>
<form method="post" action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=exCreateReport' ) ); ?>" >
    <input type="text" name="title" value="" />
    <input type="submit" name="create" value="<?php echo get_lang( 'Create' ); ?>" />
    <?php echo claro_html_button( htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=rqShowReport' ) ) , get_lang("Cancel") ); ?>
</form>