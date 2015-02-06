<shownumcorrect/>
<?php foreach( $this->question->answerList as $answer ) : ?>
<subquestion <?php if( MOODLEEX_is_html( $answer[ 'proposition' ] )
                   ||  MOODLEEX_is_html( $answer[ 'answer' ] ) ) : ?>format="html"<?php endif; ?>>
  <text><?php MOODLEEX_bake( $answer[ 'proposition' ] ); ?></text>
  <answer>
    <text><?php MOODLEEX_bake( $answer[ 'answer' ] ); ?></text>
  </answer>
</subquestion>
<?php endforeach; ?>
