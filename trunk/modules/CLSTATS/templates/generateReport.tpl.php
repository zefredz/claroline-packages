<?php if( $this->saveResult ) : ?>
<div class="claroDialogBox boxSuccess"><?php echo get_lang( 'Report generated successfully' ); ?></div>
<?php else : ?>
<div class="claroDialogBox boxWarning"><?php echo get_lang( 'No new report generated' ); ?></div>
<?php endif; ?>