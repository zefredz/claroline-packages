<h3><?php echo get_lang('Query result'); ?></h3>
<dl>
    <dt>
        <?php echo get_lang('Courses code'); ?>
    </dt>
    <dd>
        <?php echo $this->info['courseInitials']; ?><?php echo $this->info['courseNumber']; ?><?php echo $this->info['courseSubdivision']; ?>
    </dd>
    <dt>
        <?php echo get_lang('Academic year'); ?>
    </dt>
    <dd>
        <?php echo $this->info['courseValidity']; ?><?php echo is_numeric($this->info['courseValidity']) ? "-".((int)$this->info['courseValidity'] + 1) : ''; ?>
    </dd>
    <dt>
        <?php echo get_lang('Number of students'); ?>
    </dt>
    <dd>
        <?php echo $this->info['numberOfStudents']; ?>
    </dd>
</dl>
