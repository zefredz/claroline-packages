<?php if( $this->controller->is_ok() ) : ?>
    <h3><?php echo get_lang( 'success_message' ); ?></h3>
    <textarea><?php echo $this->controller->importer->csvParser->output(); ?></textarea>
<?php else : ?>
    <h3><?php echo get_lang( 'fail_message' ); ?></h3>
    <textarea><?php echo implode( ',' , $this->controller->importer->ouput[ 'fail' ] ); ?></textarea>
<?php endif; ?>