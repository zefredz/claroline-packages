<?php require dirname(__FILE__) . '/epc_query_info.tpl.php'; ?>
<h3><?php echo get_lang('User processed'); ?></h3>
<dl>
    <dt><?php echo get_lang('Number of valid users');?></dt>
    <dd><?php echo $this->validUsersCnt; ?></dd>
    <dt><?php echo get_lang('Number of users added to platform');?></dt>
    <dd><?php echo $this->newUsersCnt; ?></dd>
    <dt><?php echo get_lang('Number of failures');?></dt>
    <dd><?php echo $this->failuresCnt; ?></dd>
</dl>
<h3><?php echo get_lang('EPC class information'); ?></h3>
<dl>
    <dt><?php echo get_lang('Class name'); ?></dt>
    <dd><?php echo $this->className; ?></dd>
    <dt><?php echo get_lang('Registered in courses'); ?>
    <?php foreach ( $this->courseList as $course ): ?>
    <dd><?php echo $course['administrativeNumber'] . ' - ' . $course['title'] . ' - ' . $course['titulars']; ?></dd>
    <?php endforeach; ?>
</dl>
<?php if ( claro_debug_mode () ): ?>
<h3><?php echo get_lang('EPC service response'); ?></h3>
<pre>
<?php echo var_export($this->serviceInfo,true); ?>
</pre>
<?php endif; ?>

