<?php if ( $this->type == 'course' ): ?>
<?php require dirname(__FILE__) . '/epc_coursequery_info.tpl.php'; ?>
<?php else: ?>
<?php require dirname(__FILE__) . '/epc_programquery_info.tpl.php'; ?>
<?php endif; ?>

