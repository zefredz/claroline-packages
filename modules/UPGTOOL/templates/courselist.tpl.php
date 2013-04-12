<h4><?php echo $this->title; ?></h4>
<p><a class="claroCmd" href="<?php echo php_self(); ?>"><?php echo get_lang('Back'); ?></a></p>
<table class="claroTable" style="width:100%;">
    <thead>
        <tr>
            <th><?php echo get_lang('Course Id'); ?></th>
            <th><?php echo get_lang('Course Database'); ?></th>
            <th><?php echo get_lang('Course Status'); ?></th>
            <th><?php echo get_lang('Last step'); ?></th>
            <th><?php echo get_lang('Failed steps'); ?></th>
            <th><?php echo get_lang('Action'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php if ( count( $this->courseList ) ): ?>
        <?php foreach ( $this->courseList as $course ): ?>
        <tr>
            <td><?php echo $course['code']; ?></td>
            <td><?php echo $course['dbName']; ?></td>
            <td><?php echo $course['status']; ?></td>
            <td><?php echo $course['step']; ?></td>
            <td><?php echo $course['stepFailed']; ?></td>
        <?php if ( $course['status'] == "success" ): ?>
            <td><a href="<?php echo get_path('url').'/claroline/course/index.php?cid='.htmlspecialchars($course['code']); ?>"><?php echo get_Lang('Show course'); ?></a></td>
        <?php elseif ( $course['status'] == "pending" ): ?>
            <td><a href="<?php echo php_self() . '?cmd=upgradeCourse&cid='.htmlspecialchars($course['code']); ?>"><?php echo get_Lang('Upgrade course'); ?></a></td>
        <?php elseif ( $course['status'] == "partial" ): ?>
            <td><a href="<?php echo php_self() . '?cmd=retryFaildSteps&cid='.htmlspecialchars($course['code']); ?>"><?php echo get_Lang('Retry failed steps'); ?></a></td>
        <?php elseif ( $course['status'] == "started" ): ?>
            <td> - </td>
        <?php elseif ( $course['status'] == "error" ): ?>
            <td><a href="<?php echo php_self() . '?cmd=resetStatus&cid='.htmlspecialchars($course['code']); ?>"><?php echo get_Lang('Reset status'); ?></td>
        <?php endif; ?>
        </tr>
        <?php endforeach; ?> 
    <?php else: ?>
        <tr><td colspan="6"><?php echo get_lang('Empty'); ?></td></tr>
    <?php endif; ?>
    </tbody>
</table>
