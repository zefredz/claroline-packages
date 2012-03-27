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
        <?php foreach( $this->answer->getCourseList() as $courseId => $course ) :
            // the line below uses a filter where question and choice id's must correspond to the database fields
            //foreach( $this->answer->getCourseList( array( array( 'question_id' => 3 , 'choice_id' => 7 ) , array( 'question_id' => 4 , 'choice_id' => '!8' ) ) ) as $courseId => $course ) : ?>
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
        <input type="submit" value="<?php echo get_lang( '_finish' ); ?>" />
    </div>
</form>