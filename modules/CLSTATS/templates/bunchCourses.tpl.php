<?php
if( $this->pendingCourses == 0) :
?>
<div class="claroDialogBox boxWarning">
    <?php echo get_lang( 'No pending courses. Do you want to execute stats from scratch ?' ); ?>
<?php
else :
?>
<div class="claroDialogBox boxQuestion">
    <?php echo get_lang( 'Pending courses : %pendingCourses', array( '%pendingCourses' => $this->pendingCourses ) ); ?>
<?php
endif;
?>
    <form name="bunchCourses" action="index.php?cmd=exStats&action=bunchCourses" method="post">
        <?php
        echo get_lang( 'For how many courses do you want to generate stats ? ' );
        ?>
        <?php if( $this->pendingCourses == 0 ) : ?><input type="hidden" name="rest" value="1" /><?php endif; ?>
        <input type="text" name="bunchCourses" value="50" style="width: 50px; text-align: right;" />
        <input type="submit" name="submitButton" value="<?php echo get_lang('Generate stats'); ?>" />
    </form>
</div>