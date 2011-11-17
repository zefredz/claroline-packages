<p><?php echo get_lang( '_course_code_verification' ); ?></p>
<form id="Stage2"
      method="post"
      action="<?php echo  htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) );?>" >
    <div class="ICSURVEW_question">
        <table align="center">
            <thead>
                <tr>
                    <td><?php echo get_lang( 'Actual course code' ); ?></td>
                    <td><?php echo get_lang( 'New offical code' ); ?></td>
                </tr>
            </thead>
            <tbody>
        <?php $color = 1; ?>
        <?php foreach( array_keys( $this->answer->getCourseList() ) as $courseId ) : ?>
        <?php $color = -$color; ?>
            <tr class="ICSURVEW_<?php echo $color > 0 ? 'dark' : 'light'; ?>">
                <td><?php echo $courseId; ?></td>
                <td><input type="text" name="code[<?php echo $courseId; ?>]" value="<?php echo $courseId; ?>" />
            </tr>
            </tbody>
        <?php endforeach; ?>
        </table>
        <input type="submit" value="<?php echo get_lang( 'Done' ); ?>" />
    </div>
</form>