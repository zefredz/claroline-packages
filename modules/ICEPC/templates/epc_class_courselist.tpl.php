<table class="claroTable">
    <thead>
        <tr>
            <th><?php echo get_lang( 'Code' ); ?></th>
            <th><?php echo get_lang( 'Title' ); ?></th>
            <th><?php echo get_lang( 'Titular' ); ?></th>
            <th><?php echo get_lang( 'Unregister from course' ); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php if (count( $this->courseList ) ): ?>
    <?php foreach ( $this->courseList as $code => $course ): ?>
        <tr>
            <td><?php echo $course['administrativeNumber']; ?></td>
            <td><?php echo $course['title']; ?></td>
            <td><?php echo $course['titulars']; ?></td>
            <td><a class="checkCourseUnreg" href="<?php echo claro_htmlspecialchars($this->unregFromCourseBaseUrl.'&courseId='.$code); ?>"><?php echo get_lang('Delete'); ?></a></td>
        </tr>
    <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="4"><?php echo get_lang("No course yet"); ?></td></tr>
    <?php endif; ?>
    </tbody>
</table>
<!-- script type="text/javascript">
$(function(){
    $('.checkCourseUnreg').click(function(){
        return confirm("<?php echo get_lang( "You are going to unregister the class from this course, continue ?" ); ?>");
    });
});
</script -->
