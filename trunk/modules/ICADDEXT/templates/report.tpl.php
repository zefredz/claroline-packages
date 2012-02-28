<?php if( $this->controller->is_ok() ) : ?>
    <h3><?php echo get_lang( 'success_message' ); ?></h3>
    <textarea><?php echo $this->controller->importer->csvParser->unparse( $this->controller->importer->output[ 'success' ] ); ?></textarea>
<?php else : ?>
    <h3><?php echo get_lang( 'mail_fail_message' ); ?></h3>
    <textarea><?php echo implode( ',' , $this->controller->importer->ouput[ 'mail_failed' ] ); ?></textarea>
<?php endif; ?>