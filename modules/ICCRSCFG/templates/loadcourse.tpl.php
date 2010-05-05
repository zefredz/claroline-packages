<form action="<?php echo $_SERVER['PHP_SELF'] . '?cmd=loadCourse'; ?>" method="post">
    <label for="courseSysCode"><?php echo get_lang("Course code (system) : "); ?></label> <input type="text" name="cid" id="courseSysCode" value="" /><br />
    <input type="submit" name="submit" value="<?php echo get_lang("Load configuration"); ?>"
    <a href="<?php echo get_path('rootAdminWeb'); ?>"><input type="button" name="cancel" value="<?php echo get_lang('Cancel'); ?>" /></a>
</form>