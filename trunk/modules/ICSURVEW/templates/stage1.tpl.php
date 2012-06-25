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
        var answerCount = <?php echo $this->answer->getAnswerCount(); ?>;
        
    <?php if ( ! get_conf( 'ICSURVEW_postpone_allowed' ) ) : ?>
        $("#ICSURVEW_buttons").hide();
    <?php endif; ?>
        $(":radio").click(function(){
            var checkCount = $(":radio:checked").length;
    <?php if ( get_conf( 'ICSURVEW_postpone_allowed' ) ) : ?>
            if(checkCount==answerCount){
                $("#ICSURVEW_submit").attr({value:"<?php echo get_lang( '_send' ); ?>"});
            }else{
                $("#ICSURVEW_submit").attr({value:"<?php echo get_lang( '_send_and_quit' ); ?>"});
            }
    <?php else : ?>
            if(checkCount==answerCount){
                $("#ICSURVEW_buttons").show()
            }
    <?php endif; ?>
        });
    });
</script>
<form id="stage1"
      method="post"
      action="<?php echo  htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) );?>" >
    <?php foreach( array_values( $this->answer->getQuestionList() ) as $questionNb => $question ) : ?>
    <div id="question<?php echo $questionNb+1; ?>"
         class="ICSURVEW_question">
        <h3><?php echo get_lang( 'Question' ) . ' ' . (string)($questionNb+1) . '/' . count( (array)$this->answer->getQuestionList() ) . ' : ' . utf8_decode( $question['question'] ); ?></h3>
        <table align="center">
            <thead>
                <tr>
                    <td></td>
        <?php foreach( $question['choice'] as $choiceId => $choice ) : ?>
                    <td><?php echo utf8_decode( $choice ); ?></td>
        <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
        <?php $color = 1; ?>
        <?php foreach( $this->answer->getCourseList() as $courseId => $course ) : ?>
        <?php $color = -$color; ?>
                <tr class="ICSURVEW_<?php echo $color > 0 ? 'dark' : 'light'; ?>">
                <td>
                    <?php echo $course[ 'code' ]; ?><br />
                    <span class="ICSURVEW_courseTitle"><?php echo $course[ 'title' ]; ?></span><br />
                    <span class="ICSURVEW_courseManager"><?php echo get_lang( 'Manager' ) . ' : ' . $course[ 'manager' ]; ?></span>
                </td>
            <?php foreach( $question['choice'] as $choiceId => $choice ) : ?>
                <td>
                    <input type="radio" name="answer[<?php echo $courseId; ?>][<?php echo $question['id']; ?>]" value="<?php echo $choiceId; ?>"
                    <?php if( $this->answer->get( $courseId , $question['id'] ) == $choiceId ) echo ' checked="checked"'; ?>/>
                </td>
            <?php endforeach; ?>
                </tr>
        <?php endforeach; ?>
            </tbody>
        </table>
        <div class="ICSURVEW_nav">
            <?php if( $questionNb != 0 ) : ?>
            <input type="button"
                   id="nav_prev_<?php echo $questionNb+1; ?>"
                   class="ICSURVEW_prev"
                   value="<?php echo get_lang( '_back' ); ?>" />
            <?php endif; ?>
            <?php if( $questionNb < count( (array)$this->answer->getQuestionList() ) - 1 ) : ?>
            <input type="button"
                   id="nav_next_<?php echo $questionNb+1; ?>"
                   class="ICSURVEW_next"
                   value="<?php echo get_lang( '_next' ); ?>" />
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
    <div id="ICSURVEW_buttons">
    <?php if( get_conf( 'ICSURVEW_postpone_allowed' ) ) : ?>
        <input id="ICSURVEW_submit"
               type="submit"
               name=""
               value="<?php echo get_lang( '_send_and_quit' ); ?>" />
        <?php echo claro_html_button( htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=later' ) ) , get_lang( '_escape' ) ); ?>
    <?php else : ?>
        <input id="ICSURVEW_submit"
               type="submit"
               name=""
               value="<?php echo get_lang( '_send' ); ?>" />
    <?php endif; ?>
    </div>
</form>