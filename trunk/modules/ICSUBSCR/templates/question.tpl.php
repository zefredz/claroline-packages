<p><?php echo $this->msg ?></p>
<form method="post" action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=' . $this->cmd ) ); ?>" >
<?php foreach( $this->xid as $xid => $id ) : ?>
    <input type="hidden" name="<?php echo $xid; ?>" value="<?php echo $id; ?>" />
<?php endforeach; ?>
    <input type="submit" name="" value="<?php echo get_lang( 'Yes' ); ?>" />
    <a href="<?php echo  htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) );?>">
        <input type="button" name="cancel" value="<?php echo get_lang( 'Cancel' );?>" />
    </a>
</form>