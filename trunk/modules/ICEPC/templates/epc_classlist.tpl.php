<table class="claroTable">
    <thead>
        <tr>
            <th><?php echo get_lang( 'EPC list type' ); ?></th>
            <th><?php echo get_lang( 'Course or program code' ); ?></th>
            <th><?php echo get_lang( 'Academic year' ); ?></th>
            <th><?php echo get_lang( 'Number of students' ); ?></th>
            <th><?php echo get_lang( 'Last synced' ); ?></th>
            <th><?php echo get_lang( 'Update from EPC' ); ?></th>
            <th><?php echo get_lang( 'Delete from course' ); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php if (count( $this->epcClassList ) ): ?>
    <?php foreach ( $this->epcClassList as $epcClass ): ?>
        <?php if ( $epcClass['name'] ): ?>
        <?php $epcClassName = EpcClassName::parse($epcClass['name']); ?>
        <tr>
            <td><?php echo $epcClassName->getEpcClassType() == 'course' 
                ? get_lang('Course') 
                : get_lang('Program'); ?></td>
            <td><?php echo $epcClassName->getEpcCourseOrProgramCode(); ?></td>
            <td><?php echo $epcClassName->getEpcAcademicYear(); ?></td>
            <td>
            <?php if ( claro_is_platform_admin() ) : ?>
                <a href="<?php echo Url::Contextualize ( php_self () . '?cmd=dispUserList&amp;classId=' . $epcClass['id'] ); ?>"><?php echo class_get_number_of_users( $epcClass['id'] ); ?>              
                    (<?php echo class_get_number_of_users_in_course(  claro_get_current_course_id (), $epcClass['id']); ?> <?php echo get_lang('in course'); ?>)</a>
            <?php else: ?>
                <a href="<?php echo Url::Contextualize ( php_self () . '?cmd=dispUserList&amp;classId=' . $epcClass['id'] ); ?>"><?php echo class_get_number_of_users_in_course(  claro_get_current_course_id (), $epcClass['id']); ?></a>
            <?php endif; ?>
            </td>
            <td><?php echo $epcClass['last_sync'] ? $epcClass['last_sync'] : '-';?></td>
            <td style="text-align: center;"><a class="warnTakesTime" href="<?php echo Url::Contextualize ( php_self () . '?cmd=exSync&amp;classId=' . $epcClass['id'] ); ?>"><img src="<?php echo get_icon_url('refresh', 'ICEPC') ?>" alt="<?php echo get_lang('Update'); ?>" /></a></td>
            <td style="text-align: center;"><a class="checkClassDeletion" href="<?php echo Url::Contextualize ( php_self () . '?cmd=exUnreg&amp;classId=' . $epcClass['id'] ); ?>"><img src="<?php echo get_icon_url('delete'); ?>" alt="<?php echo get_lang('Delete'); ?>" /></a></td>
        </tr>
        <?php endif;?>
    <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="6"><?php echo get_lang("No EPC student list imported into this course yet"); ?></td></tr>
    <?php endif; ?>
    </tbody>
</table>
<!-- script type="text/javascript">
$(function(){
    $('.checkClassDeletion').click(function(){
        return confirm("<?php echo get_lang( "You are going to delete this class, do you want to continue ?" ); ?>");
    });
});
</script -->