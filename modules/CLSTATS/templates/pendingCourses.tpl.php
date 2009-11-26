<div class="claroDialogBox boxQuestion">
    <?php echo get_lang( '%pendingCourses courses are still pending. <br />Do you want to continue the statistics\' generation ?', array( '%pendingCourses' => $this->pendingCourses ) ); ?>
    <br />
    <br />
    <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] .'?cmd=exStats&action=doPending' ) ); ?>"><?php echo get_lang( 'Yes' ); ?></a>
    |
    <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=exStats&action=reset' ) ); ?>"><?php echo get_lang( 'No, I want to generate all courses statistics' ); ?></a>
</div>