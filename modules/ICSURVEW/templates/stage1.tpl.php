<form id="stage1"
      method="post"
      action="<?php echo  htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) );?>" >
<?php foreach( $this->answer->getQuestionList() as $questionId => $question ) : ?>
    <h3><?php echo utf8_decode( $question->question ); ?></h3>
    <table>
        <thead>
            <tr>
                <td></td>
    <?php foreach( $question->options as $optionId => $option ) : ?>
                <td><?php echo( $option ); ?></td>
    <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
    <?php foreach( array_keys( $this->answer->getCourseList() ) as $courseId ) : ?>
            <tr>
            <td><?php echo $courseId; ?></td>
        <?php foreach( $question->options as $optionId => $option ) : ?>
            <td>
                <input type="radio" name="answer[<?php echo $courseId; ?>][<?php echo $questionId; ?>]" value="<?php echo $optionId; ?>"
                <?php if( $this->answer->get( $courseId , $questionId ) == $optionId ) echo 'checked="checked"'; ?>/>
            </td>
        <?php endforeach; ?>
            </tr>
    <?php endforeach; ?>
        </tbody>
    <table>
<?php endforeach; ?>
    <input type="submit" name="" value="<?php echo get_lang( 'Submit' ); ?>" />
    <a href="<?php echo  htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=later' ) );?>">
        <input type="button" name="cancel" value="<?php echo get_lang( 'Later' );?>" />
    </a>
</form>