<h3><?php echo get_lang("Edit configuration in course %courseCode%", array('%courseCode%' => $this->courseId)); ?></h3>
<form action="<?php echo $_SERVER['PHP_SELF'] . '?cmd=changeConf&amp;cid='.$this->courseId; ?>" method="post">
    <fieldset>
        <legend><?php echo get_lang("Documents and links"); ?></legend>
        <dl>
            <dt>
                <label for="maxFilledSpace_for_course"><?php echo get_lang("Maximum space allowed for course documents"); ?></label>
            </dt>
            <dd>
                <input type="text" name="maxFilledSpace_for_course" id="maxFilledSpace_for_course" value="<?php echo $this->config['maxFilledSpace_for_course']; ?>" />
                <input type="checkbox" name="reset_maxFilledSpace_for_course" id="reset_maxFilledSpace_for_course" />
                <label for="reset_maxFilledSpace_for_course"><?php echo get_lang("Reset to platform value"); ?></label>
            </dd>
            
            <dt>
                <label for="maxFilledSpace_for_groups"><?php echo get_lang("Maximum space allowed for group documents"); ?></label>
            </dt>
            <dd>
                <input type="text" name="maxFilledSpace_for_groups" id="maxFilledSpace_for_groups" value="<?php echo $this->config['maxFilledSpace_for_groups']; ?>" />
                <input type="checkbox" name="reset_maxFilledSpace_for_groups" id="reset_maxFilledSpace_for_groups" />
                <label for="reset_maxFilledSpace_for_groups"><?php echo get_lang("Reset to platform value"); ?></label>
            </dd>
            
            <dt>
            <label><?php echo get_lang("Open links in new window"); ?></label>
            </dt>
            <dd>
            <?php
                $checked1 = $this->config['openNewWindowForDoc'] ? ' checked="checked"' : '';
                $checked2 = !$this->config['openNewWindowForDoc'] ? ' checked="checked"' : '';
            ?>
                <input type="radio" name="openNewWindowForDoc" id="openNewWindowForDoc_true"<?php echo $checked1; ?> value="true" />
                <label for="openNewWindowForDoc_true"><?php echo get_lang("Yes"); ?></label>
                <input type="radio" name="openNewWindowForDoc" id="openNewWindowForDoc_false"<?php echo $checked2; ?>  value="false" />
                <label for="openNewWindowForDoc_false"><?php echo get_lang("No"); ?></label>
                <input type="checkbox" name="reset_openNewWindowForDoc" id="reset_openNewWindowForDoc" />
                <label for="reset_openNewWindowForDoc"><?php echo get_lang("Reset to platform value"); ?></label>
            </dd>
        </dl>
    </fieldset>
    <fieldset>
        <legend><?php echo get_lang("Assignements"); ?></legend>
        <dl>
            <dt>
                <label for="max_file_size_per_works"><?php echo get_lang("Maximum size of a document that a user can upload"); ?></label>
            </dt>
            <dd>
                <input type="text" name="max_file_size_per_works" id="max_file_size_per_works" value="<?php echo $this->config['max_file_size_per_works']; ?>" />
                <input type="checkbox" name="reset_max_file_size_per_works" id="reset_max_file_size_per_works" />
                <label for="reset_max_file_size_per_works"><?php echo get_lang("Reset to platform value"); ?></label>
            </dd>
            
            <dt>
                <label for="maxFilledSpace"><?php echo get_lang("Max space allowed submissions in an assignement"); ?></label>
            </dt>
            <dd>
                <input type="text" name="maxFilledSpace" id="maxFilledSpace" value="<?php echo $this->config['maxFilledSpace']; ?>" />
                <input type="checkbox" name="reset_maxFilledSpace" id="reset_maxFilledSpace" />
                <label for="reset_maxFilledSpace"><?php echo get_lang("Reset to platform value"); ?></label>
            </dd>
        </dl>
    </fieldset>
    <input type="hidden" name="cid" id="courseSysCode" value="<?php echo $this->courseId; ?>" />
    <input type="submit" name="submit" value="<?php echo get_lang("Apply changes"); ?>" />
    <a href="<?php echo $_SERVER['PHP_SELF']; ?>"><input type="button" name="cancel" value="<?php echo get_lang('Cancel'); ?>" /></a>
</form>