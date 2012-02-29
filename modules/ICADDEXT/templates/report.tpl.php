<?php if( $this->controller->is_ok() ) : ?>
    <h3><?php echo get_lang( 'success_message' ); ?></h3>
    <textarea rows="<?php echo count( $this->controller->importer->output['success'] ) + 1; ?>" style="width: 100%;">
        <?php echo $this->controller->importer->getReport(); ?>
    </textarea>
<?php else : ?>
    <h3><?php echo get_lang( 'mail_fail_message' ); ?></h3>
    <textarea>
        <?php echo implode( ',' , $this->controller->importer->ouput[ 'mail_failed' ] ); ?>
    </textarea>
<?php endif; ?>