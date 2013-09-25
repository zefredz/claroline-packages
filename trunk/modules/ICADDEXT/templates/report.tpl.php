<?php if( $this->controller->importer->getReport() ) : ?>
<h3><?php echo get_lang( 'added_message' ); ?></h3>
<textarea rows="<?php echo count( $this->controller->importer->added ) + 1; ?>" style="width: 100%;"><?php echo $this->controller->importer->getReport(); ?></textarea>
<?php endif; ?>
<?php echo claro_html_button( claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) ) , get_lang( 'OK' ) ); ?>
