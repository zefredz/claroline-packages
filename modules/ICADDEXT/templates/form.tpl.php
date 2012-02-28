<strong><?php echo $this->message ?></strong>
<form method="post" action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=' . $this->urlAction ) ); ?>" >
<?php foreach( $this->xid as $index => $field ) : ?>
    <input type="<?php  echo isset( $field['type'] )    ? $field[ 'type' ]  : 'text'; ?>"
           name="<?php  echo isset( $field[ 'name' ] )  ? $field[ 'name' ]  : 'field_' . $index; ?>"
           value="<?php echo isset( $field[ 'value' ] ) ? $field[ 'value' ] : ''; ?>" />
<?php endforeach; ?>
    <input type="submit" name="create" value="<?php echo get_lang( 'Create' ); ?>" />
    <?php echo claro_html_button( htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=' . $this->urlCancel ) ) , get_lang( 'Cancel' ) ); ?>
</form>