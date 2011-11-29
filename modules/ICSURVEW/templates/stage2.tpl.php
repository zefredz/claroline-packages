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
        <?php foreach( $this->answer->getCourseList( array( array( 'question_id' => 1 , 'choice_id' => 1 ) , array( 'question_id' => 2 , 'choice_id' => '!3' ) ) ) as $courseId => $course ) : ?>
        <?php $color = -$color; ?>
            <tr class="ICSURVEW_<?php echo $color > 0 ? 'dark' : 'light'; ?>">
                <td><?php echo $course[ 'code' ]; ?></td>
                <td>
                    <input type="hidden" name="code[<?php echo $courseId; ?>]" value="<?php echo $course[ 'code' ]; ?>" />
                    <input type="text" name="newCode[<?php echo $courseId; ?>]" value="<?php echo $course[ 'code' ]; ?>" />
                </td>
            </tr>
            </tbody>
        <?php endforeach; ?>
        </table>
        <input type="submit" value="<?php echo get_lang( 'Done' ); ?>" />
    </div>
</form>