<p><?php echo get_lang( '_course_code_verification' ); ?></p>
<form id="Stage2"
      method="post"
      action="<?php echo  htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) );?>" >
    <table>
    <?php foreach( array_keys( $this->answer->getCourseList() ) as $courseId ) : ?>
        <tr>
            <td><?php echo $courseId; ?></td>
            <td><input type="text" name="code[<?php echo $courseId; ?>]" value="<?php echo $courseId; ?>" />
        </tr>
    <?php endforeach; ?>
    </table>
    <input type="submit" value="<?php echo get_lang( 'Done' ); ?>" />
</form>