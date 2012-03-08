<strong><?php echo $this->message ?></strong>
<form class="msform"
      enctype="multipart/form-data"
      method="post"
      action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=' . $this->urlAction ) ); ?>" >
    <dl class="ICSURVEW_formdl">
<?php foreach( $this->xid as $index => $field ) : ?>
        <dt>
            <strong>
                <?php  echo isset( $field['title'] ) ? get_lang( $field['title'] ) : get_lang( 'Field ' ) . $index; ?>
            </strong>
        </dt>
        <dd>
            <input type="<?php  echo isset( $field['type'] )  ? $field['type']  : 'text'; ?>"
                   name="<?php  echo isset( $field['name'] )  ? $field['name']  : 'field_' . $index; ?>"
                   value="<?php echo isset( $field['value'] ) ? $field['value'] : ''; ?>" />
        </dd>
<?php endforeach; ?>
    </dl>
    <input type="submit" name="create" value="<?php echo get_lang( 'Create' ); ?>" />
    <?php echo claro_html_button( htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=' . $this->urlCancel ) ) , get_lang( 'Cancel' ) ); ?>
</form>