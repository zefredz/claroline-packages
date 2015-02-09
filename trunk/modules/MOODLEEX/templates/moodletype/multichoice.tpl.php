<?php foreach( $this->question->answerList as $answer ) : ?>
<answer fraction="<?php echo $answer[ 'fraction' ]; ?>" <?php if( MOODLEEX_is_html( $answer[ 'content' ] ) ) : ?>format="html"<?php endif; ?>>
    <text><?php echo MOODLEEX_bake( $answer[ 'content' ] ); ?></text>
    <feedback>
        <text><?php echo MOODLEEX_bake( $answer[ 'feedback' ] ); ?></text>
    </feedback>
</answer>
<?php endforeach; ?>
