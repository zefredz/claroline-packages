<strong><?php echo get_lang( $this->message ); ?></strong>
<form method="post"
      action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=' . $this->urlAction ) ); ?>" >
<?php foreach( $this->xid as $index => $field ) : ?>
    <?php if( $not_hidden = ( ! isset( $field['type'] ) || $field['type'] != 'hidden' ) ) : ?>
    <fieldset>
    <dl>
        <dt>
            <label for="<?php echo $field['name']; ?>">
                <?php echo get_lang( ucwords( $field['name'] ) ); ?>
        <?php if( isset( $field['required'] ) && $field['required'] === true ) : ?>
                &nbsp;
                <span class="required">*</span>
        <?php endif; ?>
            </label>
        </dt>
        <dd>
    <?php endif; ?>
    <?php if( isset( $field['type'] ) && $field['type'] == 'textarea' ) : ?>
    <textarea name="data[<?php  echo isset( $field[ 'name' ] ) ? $field[ 'name' ]  : 'field_' . $index; ?>]"
              cols="60"
              rows="8"><?php echo isset( $field[ 'value' ] ) ? $field[ 'value' ] : ''; ?></textarea>
    <?php else : ?>
    <input type="<?php  echo isset( $field['type'] ) ? $field[ 'type' ] : 'text'; ?>"
           name="data[<?php  echo isset( $field[ 'name' ] ) ? $field[ 'name' ] : 'field_' . $index; ?>]"
           value="<?php echo isset( $field[ 'value' ] ) ? $field[ 'value' ] : ''; ?>"
        <?php if( isset( $field['date_picker'] ) && $field[ 'date_picker' ] === true ) : ?>
            class="auto-kal"
            lang="<?php echo get_lang( '_lang_code' ); ?>"
            size=8
    />&nbsp;[<?php echo get_lang( '_date_format' ); ?>]
        <?php elseif( isset( $field['hour_picker'] ) && $field[ 'hour_picker' ] === true ) : ?>
            class="auto-hour"
            size=4
    />&nbsp;[<?php echo get_lang( '_hour_format' ); ?>]
        <?php else : ?>
    />
        <?php endif; ?>
    <?php endif; ?>
    <?php if( $not_hidden ) : ?>
        </dd>
    </dl>
    </fieldset>
    <?php endif; ?>
<?php endforeach; ?>
    <input type="submit" name="<?php echo $this->action; ?>" value="<?php echo get_lang( $this->action ); ?>" />
    <?php echo claro_html_button( htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=' . $this->urlCancel ) ) , get_lang( 'Cancel' ) ); ?>
</form>