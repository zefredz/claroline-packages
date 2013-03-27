<h3><?php echo get_lang('Query result'); ?></h3>
<dl>
    <dt>
        <?php echo get_lang('Program code'); ?>
    </dt>
    <dd>
        <?php 
        echo $this->info['programInitials']
            .$this->info['programNumber']
            .$this->info['programCycle']
            .$this->info['programLevel']
            .$this->info['programSuffix']; 
        echo $this->info['programOrientation']?'/'.$this->info['programOrientation']:''; 
        ?>
    </dd>
    <dt>
        <?php echo get_lang('Academic year'); ?>
    </dt>
    <dd>
        <?php echo $this->info['programValidity']; ?><?php echo is_numeric($this->info['programValidity']) ? "-".((int)$this->info['programValidity'] + 1) : ''; ?>
    </dd>
    <dt>
        <?php echo get_lang('Number of students'); ?>
    </dt>
    <dd>
        <?php echo $this->info['numberOfStudents']; ?>
    </dd>
</dl>
