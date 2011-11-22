<script type="text/javascript">
    $(document).ready(function(){
        $(".ICSURVEW_question").hide();
        $("#question1").show();
        $(".ICSURVEW_prev").click(function(){
            var question=$(this).attr("id").substr(9);
            $("#question"+question).hide();
            question--;
            $("#question"+question).show();
        });
        $(".ICSURVEW_next").click(function(){
            var question=$(this).attr("id").substr(9);
            $("#question"+question).hide();
            question++;
            $("#question"+question).show();
        });
    });
</script>
<form id="stage1"
      method="post"
      action="<?php echo  htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) );?>" >
<?php foreach( $this->answer->getQuestionList() as $questionId => $question ) : ?>
<div id="question<?php echo $questionId; ?>"
     class="ICSURVEW_question">
    <h3><?php echo get_lang( 'Question' ) . ' ' . $questionId . '/' . count( (array)$this->answer->getQuestionList() ) . ': ' . utf8_decode( $question->question ); ?></h3>
    <table align="center">
        <thead>
            <tr>
                <td></td>
    <?php foreach( $question->options as $optionId => $option ) : ?>
                <td><?php echo utf8_decode( $option ); ?></td>
    <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
    <?php $color = 1; ?>
    <?php foreach( $this->answer->getCourseList() as $courseId => $course ) : ?>
    <?php $color = -$color; ?>
            <tr class="ICSURVEW_<?php echo $color > 0 ? 'dark' : 'light'; ?>">
            <td>
                <?php echo $courseId; ?><br />
                <span class="ICSURVEW_courseTitle"><?php echo $course[ 'title' ]; ?></span><br />
                <span class="ICSURVEW_courseManager"><?php echo get_lang( 'Manager' ) . ' : ' . $course[ 'manager' ]; ?></span>
            </td>
        <?php foreach( $question->options as $optionId => $option ) : ?>
            <td>
                <input type="radio" name="answer[<?php echo $courseId; ?>][<?php echo $questionId; ?>]" value="<?php echo $optionId; ?>"
                <?php if( $this->answer->get( $courseId , $questionId ) == $optionId ) echo 'checked="checked"'; ?>/>
            </td>
        <?php endforeach; ?>
            </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
    <div class="ICSURVEW_nav">
        <?php if( $questionId != 1 ) : ?>
        <input type="button"
               id="nav_prev_<?php echo $questionId; ?>"
               class="ICSURVEW_prev"
               value="<?php echo get_lang( 'Previous' ); ?>" />
        <?php endif; ?>
        <?php if( $questionId < count( (array)$this->answer->getQuestionList() ) ) : ?>
        <input type="button"
               id="nav_next_<?php echo $questionId; ?>"
               class="ICSURVEW_next"
               value="<?php echo get_lang( 'Next' ); ?>" />
        <?php else : ?>
        <input id="ICSURVEW_submit" type="submit" name="" value="<?php echo get_lang( 'Submit' ); ?>" />
        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>
    <a href="<?php echo  htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=later' ) );?>">
        <input type="button" name="cancel" value="<?php echo get_lang( 'Later' );?>" />
    </a>
</form>