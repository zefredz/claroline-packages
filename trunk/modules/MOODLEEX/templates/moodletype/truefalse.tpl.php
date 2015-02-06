<answer fraction="<?php echo $this->question->answerList[ 'true' ][ 'fraction' ]; ?>">
    <text><?php echo get_lang( 'true' ); ?></text>
    <feedback>
        <text><?php echo MOODLEEX_bake( $this->question->answerList[ 'true' ][ 'feedback' ] ); ?></text>
    </feedback>
</answer>
<answer fraction="<?php echo $this->question->answerList[ 'false' ][ 'fraction' ]; ?>">
    <text><?php echo get_lang( 'false' ); ?></text>
    <feedback>
        <text><?php echo MOODLEEX_bake( $this->question->answerList[ 'false' ][ 'feedback' ] ); ?></text>
    </feedback>
</answer>
